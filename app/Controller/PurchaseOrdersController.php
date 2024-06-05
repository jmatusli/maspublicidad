<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class PurchaseOrdersController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getpurchaseorderinfo','getpurchaseorderproducts');		
	}

	public function resumenComprasPendientes() {
		$this->loadModel('ProductionOrder');
		$this->loadModel('ProductionOrderProduct');
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('PurchaseOrderProduct');
		$this->loadModel('PurchaseOrderOtherCost');
		$this->loadModel('PurchaseOrderRemark');
		
		$qualifiedSalesOrderProductIds=$this->SalesOrderProduct->find('list',array(
			'fields'=>array('SalesOrderProduct.id'),
			'conditions'=>array(
				'SalesOrderProduct.sales_order_product_status_id >= '=>PRODUCT_STATUS_AWAITING_PURCHASE,
				'SalesOrderProduct.sales_order_product_status_id < '=>PRODUCT_STATUS_AWAITING_PRODUCTION,
			),
		));
		
		$qualifiedProductionOrderIds=$this->ProductionOrder->find('list',array(
			'fields'=>array('ProductionOrder.id'),
			'conditions'=>array(
				'ProductionOrder.bool_annulled'=>false,
			),
		));
		
		$pendingProductionOrderProducts=$this->ProductionOrderProduct->find('all',array(
			'fields'=>array(
				'ProductionOrderProduct.id',
				'ProductionOrderProduct.product_id',
				'ProductionOrderProduct.product_description',
				'ProductionOrderProduct.product_quantity',
				'ProductionOrderProduct.sales_order_product_id',
			),
			'conditions'=>array(
				'ProductionOrderProduct.sales_order_product_id'=>$qualifiedSalesOrderProductIds,
				'ProductionOrderProduct.production_order_id'=>$qualifiedProductionOrderIds,
			),
			'contain'=>array(
				'ProductionOrder'=>array(
					'fields'=>array('ProductionOrder.id','ProductionOrder.production_order_code'),
				),
				'ProductionOrderProductDepartment'=>array(
					'Department',
				),
				'Product'=>array(
					'fields'=>array('Product.id','Product.name','Product.code'),
					'ProductCategory'=>array(
						'fields'=>array('ProductCategory.id','ProductCategory.name'),
					),
				),
				'PurchaseOrderProduct'=>array(
					'fields'=>array(
						'PurchaseOrderProduct.id',
						'PurchaseOrderProduct.product_id',
						'PurchaseOrderProduct.product_description',
						'PurchaseOrderProduct.product_quantity',
						'PurchaseOrderProduct.product_unit_cost',
						'PurchaseOrderProduct.product_total_cost',
						'PurchaseOrderProduct.bool_received',
						'PurchaseOrderProduct.sales_order_product_id',
					),
					'PurchaseOrder'=>array(
						'fields'=>array('PurchaseOrder.id','PurchaseOrder.purchase_order_code'),
						'Currency',
					),
				),
				'SalesOrderProduct'=>array(
					'SalesOrderProductStatus'=>array(
						'fields'=>array('SalesOrderProductStatus.id','SalesOrderProductStatus.status'),
					),
				),
			),
		));
		$this->set(compact('pendingProductionOrderProducts'));
		//pr($pendingProductionOrderProducts);
	
		$productionOrders=array();
		if (!empty($pendingProductionOrderProducts)){
			foreach ($pendingProductionOrderProducts  as $productionOrderProduct){
				//pr($productionOrderProduct);
				$productionOrders[$productionOrderProduct['ProductionOrder']['id']]=$productionOrderProduct['ProductionOrder']['production_order_code'];
			}
		}
		$this->set(compact('productionOrders'));
	
		$this->PurchaseOrder->recursive = -1;
		$requestOtherCosts=array();
		$currencyId=CURRENCY_CS;
		
		if ($this->request->is('post')) {
			$currencyId=$this->request->data['PurchaseOrder']['currency_id'];
			if (!empty($this->request->data['PurchaseOrderOtherCost'])){	
				foreach ($this->request->data['PurchaseOrderOtherCost'] as $otherCost){
					//pr($otherCost);
					if ($otherCost['task_quantity']>0 && $otherCost['task_unit_cost']>0){
						$requestOtherCosts[]['PurchaseOrderOtherCost']=$otherCost;
					}
				}
			}
			$purchaseOrderDateArray=$this->request->data['PurchaseOrder']['purchase_order_date'];
			//pr($quotationDateArray);
			$purchaseOrderDateString=$purchaseOrderDateArray['year'].'-'.$purchaseOrderDateArray['month'].'-'.$purchaseOrderDateArray['day'];
			$purchaseOrderDate=date( "Y-m-d", strtotime($purchaseOrderDateString));
			
			$boolMultiplicationOK=true;
			if (!empty($this->request->data['PurchaseOrderProduct'])){
				foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
					if ($purchaseOrderProduct['product_id']>0 && $purchaseOrderProduct['product_quantity']>0){
						$multiplicationDifference=abs($purchaseOrderProduct['product_total_cost']-$purchaseOrderProduct['product_quantity']*$purchaseOrderProduct['product_unit_cost']);
						//pr($purchaseOrderProduct);
						if ($multiplicationDifference>=0.01){
							$boolMultiplicationOK=false;
						};
					}
				}
			}
			if ($purchaseOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de compra no puede estar en el futuro!  No se guardó la orden de compra.'), 'default',array('class' => 'error-message'));
			}
			elseif (empty($this->request->data['PurchaseOrder']['provider_id'])){
				$this->Session->setFlash(__('Se debe seleccionar el proveedor.  No se guardó la orden de compra.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			else {				
				$datasource=$this->PurchaseOrder->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$this->PurchaseOrder->create();
					$this->request->data['PurchaseOrder']['bool_received']=false;
					if (!$this->PurchaseOrder->save($this->request->data)) {
						echo "Problema guardando la orden de compra";
						pr($this->validateErrors($this->PurchaseOrder));
						throw new Exception();
					}
					$purchase_order_id=$this->PurchaseOrder->id;
					
					foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
						if ($purchaseOrderProduct['product_id']>0 && $purchaseOrderProduct['product_quantity']>0){
							// UPDATE THE SALES ORDER PRODUCT 
							$relatedSalesOrderProduct=$this->SalesOrderProduct->find('first',array(
								'conditions'=>array(
									'SalesOrderProduct.id'=>$purchaseOrderProduct['sales_order_product_id'],
								),
							));
							$salesOrderProductQuantity=$relatedSalesOrderProduct['SalesOrderProduct']['product_quantity'];
							if ($purchaseOrderProduct['product_quantity']==$salesOrderProductQuantity){
								// product awaiting reception delivered
								$this->SalesOrderProduct->id=$purchaseOrderProduct['sales_order_product_id'];
								$salesOrderProductArray=array();
								$salesOrderProductArray['SalesOrderProduct']['id']=$purchaseOrderProduct['sales_order_product_id'];
								$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_RECEPTION;
								if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
									echo "Problema cambiando el estado de los productos de la orden de venta";
									pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
							}
							else {
								//pr($purchaseOrderProduct);
                /*
								if (!$this->SalesOrderProduct->splitSalesOrderProduct($purchaseOrderProduct['sales_order_product_id'], PRODUCT_STATUS_AWAITING_RECEPTION,$purchaseOrderProduct['product_quantity'])){
									echo "Problema con la separación de los productos de la orden de venta";
									throw new Exception();
								}
                */
								//echo "alrighty the sales order product has been successfully split!!<br/>";
							}										
						
							//pr($quotationProduct);
							$productArray=array();
							$productArray['PurchaseOrderProduct']['purchase_order_id']=$purchase_order_id;
							$productArray['PurchaseOrderProduct']['product_id']=$purchaseOrderProduct['product_id'];
							$productArray['PurchaseOrderProduct']['production_order_product_id']=$purchaseOrderProduct['production_order_product_id'];
							$productArray['PurchaseOrderProduct']['product_description']=$purchaseOrderProduct['product_description'];
							$productArray['PurchaseOrderProduct']['product_quantity']=$purchaseOrderProduct['product_quantity'];
							$productArray['PurchaseOrderProduct']['product_unit_cost']=$purchaseOrderProduct['product_unit_cost'];
							$productArray['PurchaseOrderProduct']['product_total_cost']=$purchaseOrderProduct['product_total_cost'];
							$productArray['PurchaseOrderProduct']['currency_id']=$this->request->data['PurchaseOrder']['currency_id'];
							$productArray['PurchaseOrderProduct']['production_order_id']=$purchaseOrderProduct['production_order_id'];
							$productArray['PurchaseOrderProduct']['department_id']=$purchaseOrderProduct['department_id'];
							$productArray['PurchaseOrderProduct']['sales_order_product_id']=$purchaseOrderProduct['sales_order_product_id'];
							$this->PurchaseOrderProduct->create();
							if (!$this->PurchaseOrderProduct->save($productArray)) {
								echo "Problema guardando los productos de la orden de compra";
								pr($this->validateErrors($this->PurchaseOrderProduct));
								throw new Exception();
							}
						}
					}
					
					foreach ($this->request->data['PurchaseOrderOtherCost'] as $purchaseOrderOtherCost){
						if ($purchaseOrderOtherCost['task_quantity']>0 && $purchaseOrderOtherCost['task_unit_cost']>0){
							//pr($quotationProduct);
							$costArray=array();
							$costArray['PurchaseOrderOtherCost']['purchase_order_id']=$purchase_order_id;
							$costArray['PurchaseOrderOtherCost']['task_description']=$purchaseOrderOtherCost['task_description'];
							$costArray['PurchaseOrderOtherCost']['department_id']=$purchaseOrderOtherCost['department_id'];
							$costArray['PurchaseOrderOtherCost']['task_quantity']=$purchaseOrderOtherCost['task_quantity'];
							$costArray['PurchaseOrderOtherCost']['task_unit_cost']=$purchaseOrderOtherCost['task_unit_cost'];
							$costArray['PurchaseOrderOtherCost']['task_total_cost']=$purchaseOrderOtherCost['task_total_cost'];
							$costArray['PurchaseOrderOtherCost']['currency_id']=$this->request->data['PurchaseOrder']['currency_id'];
							$this->PurchaseOrderOtherCost->create();
							if (!$this->PurchaseOrderOtherCost->save($costArray)) {
								echo "Problema guardando los otros costos de la orden de compra";
								pr($this->validateErrors($this->PurchaseOrderOtherCost));
								throw new Exception();
							}
						}
					}
						
					$purchaseOrderRemarkArray=array();
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['user_id']=$this->Auth->User('id');
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['purchase_order_id']=$purchase_order_id;
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_text']="Se creó la orden de compra";
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
					$this->PurchaseOrderRemark->create();
					if (!$this->PurchaseOrderRemark->save($purchaseOrderRemarkArray)) {
						echo "Problema guardando las remarcas para la orden de compra";
						pr($this->validateErrors($this->PurchaseOrderRemark));
						throw new Exception();
					}
					
					$datasource->commit();
					$this->recordUserAction($this->PurchaseOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de compra número ".$this->request->data['PurchaseOrder']['purchase_order_code']);
					
					$this->Session->setFlash(__('Se guardó la orden de compra.'),'default',array('class' => 'success'));				
					return $this->redirect(array('action' => 'view',$purchase_order_id));
				}
				catch(Exception $e){
					$datasource->rollback();
					// pr($e);
					$this->Session->setFlash(__('No se podía guardar la orden de compra.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		$this->set(compact('requestOtherCosts'));
		$this->set(compact('currencyId'));
		
		$conditions=array(
			'PurchaseOrder.bool_received'=>false,
		);
		
		$purchaseOrderCount=	$this->PurchaseOrder->find('count', array(
			'fields'=>array('PurchaseOrder.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(				
				'Currency',
				'PaymentMode',
				'Provider',
				'User',
				
			),
			'limit'=>($purchaseOrderCount!=0?$purchaseOrderCount:1),
		);

		$purchaseOrders = $this->Paginator->paginate('PurchaseOrder');
		if (!empty($purchaseOrders)){
			$this->loadModel('ExchangeRate');
			for ($i=0;$i<count($purchaseOrders);$i++){
				// set the exchange rate
				$purchaseOrderDate=$purchaseOrders[$i]['PurchaseOrder']['purchase_order_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($purchaseOrderDate);
				$purchaseOrders[$i]['PurchaseOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
			}
		}
		$this->set(compact('purchaseOrders'));
		
		$providers = $this->PurchaseOrder->Provider->find('list',array('order'=>'Provider.name'));
		$users = $this->PurchaseOrder->User->find('list',array('order'=>'User.username'));
		$displayCurrencies=$currencies = $this->PurchaseOrder->Currency->find('list');
		$paymentModes=$this->PurchaseOrder->PaymentMode->find('list');
		$this->set(compact('providers','users','currencies','displayCurrencies','paymentModes'));
		
		$this->loadModel('Department');
		$departments=$this->Department->find('list');
		$this->set(compact('departments'));
		
		$this->loadModel('Product');
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
	}

	public function index() {
		$this->PurchaseOrder->recursive = -1;
		
		$currencyId=CURRENCY_USD;
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			//$userId=$this->request->data['Report']['user_id'];
			$currencyId=$this->request->data['Report']['currency_id'];
		}
		
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
			//if ($this->Session->check('userId')){
			//	$userId=$_SESSION['userId'];
			//}
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$_SESSION['currencyId']=$currencyId;
		//$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('currencyId'));
		
		$conditions=array(
			'PurchaseOrder.purchase_order_date >='=>$startDate,
			'PurchaseOrder.purchase_order_date <'=>$endDatePlusOne,
		);
		
		$purchaseOrderCount=	$this->PurchaseOrder->find('count', array(
			'fields'=>array('PurchaseOrder.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(				
				'Currency',
				'PaymentMode',
				'Provider',
				'User',
				
			),
			'limit'=>($purchaseOrderCount!=0?$purchaseOrderCount:1),
		);

		$purchaseOrders = $this->Paginator->paginate('PurchaseOrder');
		if (!empty($purchaseOrders)){
			$this->loadModel('ExchangeRate');
			for ($i=0;$i<count($purchaseOrders);$i++){
				// set the exchange rate
				$purchaseOrderDate=$purchaseOrders[$i]['PurchaseOrder']['purchase_order_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($purchaseOrderDate);
				$purchaseOrders[$i]['PurchaseOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
			}
		}
		$this->set(compact('purchaseOrders'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
	}
	
	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->PurchaseOrder->exists($id)) {
			throw new NotFoundException(__('Invalid purchase order'));
		}
		
		$options = array(
			'conditions' => array(
				'PurchaseOrder.id' => $id,
			),
			'contain'=>array(
				'Currency',
				'PaymentMode',
				'Provider',
				'PurchaseOrderProduct'=>array(
					'Product',
				),
				'PurchaseOrderOtherCost'=>array(
					'Department',
				),
				'PurchaseOrderRemark'=>array(
					'ActionType',
					'User',
				),
				'User',
			),
		);
		$purchaseOrder=$this->PurchaseOrder->find('first', $options);
		
		$purchaseOrderDate=$purchaseOrder['PurchaseOrder']['purchase_order_date'];
		$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($purchaseOrderDate);
		$purchaseorder['PurchaseOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
		
		$this->set(compact('purchaseOrder'));
		//pr($purchaseOrder);
		
		$filename="Orden_de_compras_".$purchaseOrder['PurchaseOrder']['purchase_order_code'];
		$this->set(compact('filename'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
	}

	public function viewPdf($id = null) {
		if (!$this->PurchaseOrder->exists($id)) {
			throw new NotFoundException(__('Invalid purchase order'));
		}
		
		$options = array(
			'conditions' => array(
				'PurchaseOrder.id' => $id,
			),
			'contain'=>array(
				'Currency',
				'PaymentMode',
				'Provider',
				'PurchaseOrderProduct'=>array(
					'Product',
				),
				'PurchaseOrderOtherCost'=>array(
					'Department',
				),
				'PurchaseOrderRemark'=>array(
					'ActionType',
					'User',
				),
				'User',
			),
		);
		$purchaseOrder=$this->PurchaseOrder->find('first', $options);
		
		$purchaseOrderDate=$purchaseOrder['PurchaseOrder']['purchase_order_date'];
		$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($purchaseOrderDate);
		$purchaseorder['PurchaseOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
		
		$this->set(compact('purchaseOrder'));
		
		$filename="Orden_de_compras_".$purchaseOrder['PurchaseOrder']['purchase_order_code'];
		$this->set(compact('filename'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
	}

	public function add() {
		$this->loadModel('ProductionOrder');
		$this->loadModel('SalesOrderProduct');
		
		$productionOrders=$this->ProductionOrder->find('list',array(
			'order'=>'ProductionOrder.production_order_code',
		));
		$this->set(compact('productionOrders'));

		$requestProducts=array();
		$requestOtherCosts=array();
		
		$currencyId=CURRENCY_CS;
		
		if ($this->request->is('post')) {
			$currencyId=$this->request->data['PurchaseOrder']['currency_id'];
			$boolProductionOrderOK = true;
			$boolDepartmentOK = true;
			$flashMessage="";
			foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
				if ($purchaseOrderProduct['product_id']>0 && $purchaseOrderProduct['product_quantity']>0 && $purchaseOrderProduct['product_unit_cost']>0){
					$requestProducts[]['PurchaseOrderProduct']=$purchaseOrderProduct;
					if (!$this->request->data['PurchaseOrder']['bool_annulled']){
						if (empty($purchaseOrderProduct['production_order_id'])){
							$boolProductionOrderOK=false;
							$flashMessage.="Para producto ".$purchaseOrderProduct['product_description']." no se indicó la orden de producción.  ";
						}
						if (empty($purchaseOrderProduct['department_id'])){
							$boolDepartmentOK=false;
							$flashMessage.="Para producto ".$purchaseOrderProduct['product_description']." no se indicó el departamento.  ";
						}
					}					
				}
			}
			if (!empty($this->request->data['PurchaseOrderOtherCost'])){	
				foreach ($this->request->data['PurchaseOrderOtherCost'] as $otherCost){
					//pr($otherCost);
					if ($otherCost['task_quantity']>0 && $otherCost['task_unit_cost']>0){
						$requestOtherCosts[]['PurchaseOrderOtherCost']=$otherCost;
					}
				}
			}
			$purchaseOrderDateArray=$this->request->data['PurchaseOrder']['purchase_order_date'];
			//pr($quotationDateArray);
			$purchaseOrderDateString=$purchaseOrderDateArray['year'].'-'.$purchaseOrderDateArray['month'].'-'.$purchaseOrderDateArray['day'];
			$purchaseOrderDate=date( "Y-m-d", strtotime($purchaseOrderDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
				if ($purchaseOrderProduct['product_id']>0 && $purchaseOrderProduct['product_quantity']>0){
					$multiplicationDifference=abs($purchaseOrderProduct['product_total_cost']-$purchaseOrderProduct['product_quantity']*$purchaseOrderProduct['product_unit_cost']);
					//pr($purchaseOrderProduct);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			if ($purchaseOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de compra no puede estar en el futuro!  No se guardó la orden de compra.'), 'default',array('class' => 'error-message'));
			}
			elseif (empty($this->request->data['PurchaseOrder']['provider_id'])){
				$this->Session->setFlash(__('Se debe seleccionar el proveedor.  No se guardó la orden de compra.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolProductionOrderOK){
				$this->Session->setFlash($flashMessage."  No se guardó la orden de compra.", 'default',array('class' => 'error-message'));
			}
			elseif (!$boolDepartmentOK){
				$this->Session->setFlash($flashMessage."  No se guardó la orden de compra.", 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  No se guardó la orden de compra.'), 'default',array('class' => 'error-message'));
			}
			else {				
				$datasource=$this->PurchaseOrder->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$this->loadModel('PurchaseOrderProduct');
					$this->loadModel('PurchaseOrderOtherCost');
					$this->loadModel('PurchaseOrderRemark');
					
					$this->PurchaseOrder->create();
					//pr($this->request->data);
					if (!$this->PurchaseOrder->save($this->request->data)) {
						echo "Problema guardando la orden de compra";
						pr($this->validateErrors($this->PurchaseOrder));
						throw new Exception();
					}
					$purchase_order_id=$this->PurchaseOrder->id;
					
					foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
						if ($purchaseOrderProduct['product_id']>0 && $purchaseOrderProduct['product_quantity']>0){
							// UPDATE THE SALES ORDER PRODUCT 
							/*
							$relatedSalesOrderProduct=$this->SalesOrderProduct->find('first',array(
								'conditions'=>array(
									'SalesOrderProduct.id'=>$purchaseOrderProduct['sales_order_product_id'],
								),
							));
							
							$salesOrderProductQuantity=$relatedSalesOrderProduct['SalesOrderProduct']['product_quantity'];
							if ($purchaseOrderProduct['product_quantity']==$salesOrderProductQuantity){
								// product awaiting reception delivered
								$this->SalesOrderProduct->id=$purchaseOrderProduct['sales_order_product_id'];
								$salesOrderProductArray=array();
								$salesOrderProductArray['SalesOrderProduct']['id']=$purchaseOrderProduct['sales_order_product_id'];
								$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_RECEPTION;
								if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
									echo "Problema cambiando el estado de los productos de la orden de venta";
									pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
							}
							else {
								if (!$this->SalesOrderProduct->splitSalesOrderProduct($purchaseOrderProduct['sales_order_product_id'], PRODUCT_STATUS_AWAITING_RECEPTION,$purchaseOrderProduct['product_quantity'])){
									echo "Problema con la separación de los productos de la orden de venta";
									throw new Exception();
								}
								//echo "alrighty the sales order product has been successfully split!!<br/>";
							}
							*/
							//pr($purchaseOrderProduct);
							$productArray=array();
							$productArray['PurchaseOrderProduct']['purchase_order_id']=$purchase_order_id;
							$productArray['PurchaseOrderProduct']['product_id']=$purchaseOrderProduct['product_id'];
							$productArray['PurchaseOrderProduct']['production_order_product_id']=0;
							$productArray['PurchaseOrderProduct']['product_description']=$purchaseOrderProduct['product_description'];
							$productArray['PurchaseOrderProduct']['product_quantity']=$purchaseOrderProduct['product_quantity'];
							$productArray['PurchaseOrderProduct']['product_unit_cost']=$purchaseOrderProduct['product_unit_cost'];
							$productArray['PurchaseOrderProduct']['product_total_cost']=$purchaseOrderProduct['product_total_cost'];
							$productArray['PurchaseOrderProduct']['currency_id']=$this->request->data['PurchaseOrder']['currency_id'];
							$this->PurchaseOrderProduct->create();
							if (!$this->PurchaseOrderProduct->save($productArray)) {
								echo "Problema guardando los productos de la orden de compra";
								pr($this->validateErrors($this->PurchaseOrderProduct));
								throw new Exception();
							}
						}
					}
					
					foreach ($this->request->data['PurchaseOrderOtherCost'] as $purchaseOrderOtherCost){
						if ($purchaseOrderOtherCost['task_quantity']>0 && $purchaseOrderOtherCost['task_unit_cost']>0){
							//pr($quotationProduct);
							$costArray=array();
							$costArray['PurchaseOrderOtherCost']['purchase_order_id']=$purchase_order_id;
							$costArray['PurchaseOrderOtherCost']['task_description']=$purchaseOrderOtherCost['task_description'];
							$costArray['PurchaseOrderOtherCost']['department_id']=$purchaseOrderOtherCost['department_id'];
							$costArray['PurchaseOrderOtherCost']['task_quantity']=$purchaseOrderOtherCost['task_quantity'];
							$costArray['PurchaseOrderOtherCost']['task_unit_cost']=$purchaseOrderOtherCost['task_unit_cost'];
							$costArray['PurchaseOrderOtherCost']['task_total_cost']=$purchaseOrderOtherCost['task_total_cost'];
							$costArray['PurchaseOrderOtherCost']['currency_id']=$this->request->data['PurchaseOrder']['currency_id'];
							$this->PurchaseOrderOtherCost->create();
							if (!$this->PurchaseOrderOtherCost->save($costArray)) {
								echo "Problema guardando los otros costos de la orden de compra";
								pr($this->validateErrors($this->PurchaseOrderOtherCost));
								throw new Exception();
							}
						}
					}
						
					$purchaseOrderRemarkArray=array();
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['user_id']=$this->Auth->User('id');
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['purchase_order_id']=$purchase_order_id;
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_text']="Se creó la orden de compra";
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
					$this->PurchaseOrderRemark->create();
					if (!$this->PurchaseOrderRemark->save($purchaseOrderRemarkArray)) {
						echo "Problema guardando las remarcas para la orden de compra";
						pr($this->validateErrors($this->PurchaseOrderRemark));
						throw new Exception();
					}
					
					$datasource->commit();
					$this->recordUserAction($this->PurchaseOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de compra número ".$this->request->data['PurchaseOrder']['purchase_order_code']);
					
					$this->Session->setFlash(__('Se guardó la orden de compra.'),'default',array('class' => 'success'));				
					return $this->redirect(array('action' => 'view',$purchase_order_id));
				}
				catch(Exception $e){
					$datasource->rollback();
					// pr($e);
					$this->Session->setFlash(__('No se podía guardar la orden de compra.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		$this->set(compact('requestProducts'));
		$this->set(compact('requestOtherCosts'));
		$this->set(compact('currencyId'));
		
		$providers = $this->PurchaseOrder->Provider->find('list',array('order'=>'Provider.name'));
		$users = $this->PurchaseOrder->User->find('list',array('order'=>'User.username'));
		$currencies = $this->PurchaseOrder->Currency->find('list');
		$paymentModes = $this->PurchaseOrder->PaymentMode->find('list');
		$this->set(compact('providers', 'users', 'currencies', 'paymentModes'));
		
		$this->loadModel('Department');
		$departments=$this->Department->find('list');
		$this->set(compact('departments'));
		
		$this->loadModel('Product');
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->PurchaseOrder->exists($id)) {
			throw new NotFoundException(__('Invalid purchase order'));
		}
		$this->loadModel('ProductionOrder');
		$this->loadModel('SalesOrderProduct');
		
		$productionOrders=$this->ProductionOrder->find('list',array(
			'order'=>'ProductionOrder.production_order_code',
		));
		$this->set(compact('productionOrders'));

		$requestProducts=array();
		$requestOtherCosts=array();
		
		$currencyId=CURRENCY_CS;
		
		if ($this->request->is(array('post', 'put'))) {
			$currencyId=$this->request->data['PurchaseOrder']['currency_id'];
			$boolProductionOrderOK = true;
			$boolDepartmentOK = true;
			$flashMessage="";
			foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
				if ($purchaseOrderProduct['product_id']>0 && $purchaseOrderProduct['product_quantity']>0 && $purchaseOrderProduct['product_unit_cost']>0){
					$requestProducts[]['PurchaseOrderProduct']=$purchaseOrderProduct;
					if (!$this->request->data['PurchaseOrder']['bool_annulled']){
						if (empty($purchaseOrderProduct['production_order_id'])){
							$boolProductionOrderOK=false;
							$flashMessage.="Para producto ".$purchaseOrderProduct['product_description']." no se indicó la orden de producción.  ";
						}
						if (empty($purchaseOrderProduct['department_id'])){
							$boolDepartmentOK=false;
							$flashMessage.="Para producto ".$purchaseOrderProduct['product_description']." no se indicó el departamento.  ";
						}
					}					
				}
			}
			if (!empty($this->request->data['PurchaseOrderOtherCost'])){	
				foreach ($this->request->data['PurchaseOrderOtherCost'] as $otherCost){
					//pr($otherCost);
					if ($otherCost['task_quantity']>0 && $otherCost['task_unit_cost']>0){
						$requestOtherCosts[]['PurchaseOrderOtherCost']=$otherCost;
					}
				}
			}
			
			$purchaseOrderDateArray=$this->request->data['PurchaseOrder']['purchase_order_date'];
			//pr($quotationDateArray);
			$purchaseOrderDateString=$purchaseOrderDateArray['year'].'-'.$purchaseOrderDateArray['month'].'-'.$purchaseOrderDateArray['day'];
			$purchaseOrderDate=date( "Y-m-d", strtotime($purchaseOrderDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
				if ($purchaseOrderProduct['product_id']>0 && $purchaseOrderProduct['product_quantity']>0){
					$multiplicationDifference=abs($purchaseOrderProduct['product_total_cost']-$purchaseOrderProduct['product_quantity']*$purchaseOrderProduct['product_unit_cost']);
					//pr($purchaseOrderProduct);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			if ($purchaseOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de compra no puede estar en el futuro!  No se guardó la orden de compra.'), 'default',array('class' => 'error-message'));
			}
			elseif (empty($this->request->data['PurchaseOrder']['provider_id'])){
				$this->Session->setFlash(__('Se debe seleccionar el proveedor.  No se guardó la orden de compra.'), 'default',array('class' => 'error-message'));
			}
			elseif (empty($this->request->data['PurchaseOrder']['bool_received'])){
				$this->Session->setFlash(__('Ya se recibió esta orden de compra.  No se puede editar.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolProductionOrderOK){
				$this->Session->setFlash($flashMessage."  No se guardó la orden de compra.", 'default',array('class' => 'error-message'));
			}
			elseif (!$boolDepartmentOK){
				$this->Session->setFlash($flashMessage."  No se guardó la orden de compra.", 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  No se guardó la orden de compra.'), 'default',array('class' => 'error-message'));
			}
			else {				
				$datasource=$this->PurchaseOrder->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$this->loadModel('PurchaseOrderProduct');
					$this->loadModel('PurchaseOrderOtherCost');
					$this->loadModel('PurchaseOrderRemark');
					
					$previousPurchaseOrderProducts=$this->PurchaseOrderProduct->find('all',array(
						'fields'=>array(
							'PurchaseOrderProduct.id',
							'PurchaseOrderProduct.sales_order_product_id',
						),
						'conditions'=>array(
							'PurchaseOrderProduct.purchase_order_id'=>$id,
						),
					));
					if (!empty($previousPurchaseOrderProducts)){
						foreach ($previousPurchaseOrderProducts as $previousPurchaseOrderProduct){
							//pr($previousPurchaseOrderProduct);
							$salesOrderProductArray=array();
							$salesOrderProductArray['SalesOrderProduct']['id']=$previousPurchaseOrderProduct['PurchaseOrderProduct']['sales_order_product_id'];
							$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PURCHASE;
							$this->SalesOrderProduct->id=$previousPurchaseOrderProduct['PurchaseOrderProduct']['sales_order_product_id'];
							if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
								echo "Problema cambiando el estado de los productos de la orden de venta";
								pr($this->validateErrors($this->SalesOrderProduct));
								throw new Exception();
							}
							
							$this->PurchaseOrderProduct->id=$previousPurchaseOrderProduct['PurchaseOrderProduct']['id'];
							$this->PurchaseOrderProduct->delete($previousPurchaseOrderProduct['PurchaseOrderProduct']['id']);
						}
					}
					$previousPurchaseOrderOtherCosts=$this->PurchaseOrderOtherCost->find('all',array(
						'fields'=>array('PurchaseOrderOtherCost.id'),
						'conditions'=>array(
							'PurchaseOrderOtherCost.purchase_order_id'=>$id,
						),
					));
					if (!empty($previousPurchaseOrderOtherCosts)){
						foreach ($previousPurchaseOrderOtherCosts as $previousPurchaseOrderOtherCost){
							$this->PurchaseOrderOtherCost->id=$previousPurchaseOrderOtherCost['PurchaseOrderOtherCost']['id'];
							$this->PurchaseOrderOtherCost->delete($previousPurchaseOrderOtherCost['PurchaseOrderOtherCost']['id']);
						}
					}
					
					if ($this->request->data['PurchaseOrder']['bool_annulled']){
						$this->request->data['PurchaseOrder']['cost_subtotal']=0;
						$this->request->data['PurchaseOrder']['cost_iva']=0;
						$this->request->data['PurchaseOrder']['cost_total']=0;
						$this->PurchaseOrder->id=$id;
						if (!$this->PurchaseOrder->save($this->request->data)) {
							echo "Problema guardando la orden de compra";
							//pr($this->validateErrors($this->PurchaseOrder));
							throw new Exception();
						} 
						$purchase_order_id=$this->PurchaseOrder->id;
						
						$purchaseOrderRemarkArray=array();
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['user_id']=$this->Auth->User('id');
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['purchase_order_id']=$purchase_order_id;
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_text']="Se anuló la orden de compra";
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->PurchaseOrderRemark->create();
						if (!$this->PurchaseOrderRemark->save($purchaseOrderRemarkArray)) {
							echo "Problema guardando las remarcas para la orden de compra";
							pr($this->validateErrors($this->PurchaseOrderRemark));
							throw new Exception();
						}
					}
					else {			
						//pr($this->request->data);
						if (!$this->PurchaseOrder->save($this->request->data)) {
							echo "Problema guardando la orden de compra";
							pr($this->validateErrors($this->PurchaseOrder));
							throw new Exception();
						}
						$purchase_order_id=$this->PurchaseOrder->id;
						
						foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
							if ($purchaseOrderProduct['product_id']>0 && $purchaseOrderProduct['product_quantity']>0){
								// UPDATE THE SALES ORDER PRODUCT 
								$relatedSalesOrderProduct=$this->SalesOrderProduct->find('first',array(
									'conditions'=>array(
										'SalesOrderProduct.id'=>$purchaseOrderProduct['sales_order_product_id'],
									),
								));
								$salesOrderProductQuantity=$relatedSalesOrderProduct['SalesOrderProduct']['product_quantity'];
								if ($purchaseOrderProduct['product_quantity']==$salesOrderProductQuantity){
									// product awaiting reception delivered
									$this->SalesOrderProduct->id=$purchaseOrderProduct['sales_order_product_id'];
									$salesOrderProductArray=array();
									$salesOrderProductArray['SalesOrderProduct']['id']=$purchaseOrderProduct['sales_order_product_id'];
									$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_RECEPTION;
									if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
										echo "Problema cambiando el estado de los productos de la orden de venta";
										pr($this->validateErrors($this->SalesOrderProduct));
										throw new Exception();
									}
								}
								else {
                  /*
									if (!$this->SalesOrderProduct->splitSalesOrderProduct($purchaseOrderProduct['sales_order_product_id'], PRODUCT_STATUS_AWAITING_RECEPTION,$purchaseOrderProduct['product_quantity'])){
										echo "Problema con la separación de los productos de la orden de venta";
										throw new Exception();
									}
                  */
									//echo "alrighty the sales order product has been successfully split!!<br/>";
								}
							
								//pr($purchaseOrderProduct);
								$productArray=array();
								
								$productArray['PurchaseOrderProduct']['purchase_order_id']=$purchase_order_id;
								$productArray['PurchaseOrderProduct']['product_id']=$purchaseOrderProduct['product_id'];
								$productArray['PurchaseOrderProduct']['production_order_product_id']=$purchaseOrderProduct['production_order_product_id'];
								$productArray['PurchaseOrderProduct']['product_description']=$purchaseOrderProduct['product_description'];
								$productArray['PurchaseOrderProduct']['product_quantity']=$purchaseOrderProduct['product_quantity'];
								$productArray['PurchaseOrderProduct']['product_unit_cost']=$purchaseOrderProduct['product_unit_cost'];
								$productArray['PurchaseOrderProduct']['product_total_cost']=$purchaseOrderProduct['product_total_cost'];
								$productArray['PurchaseOrderProduct']['currency_id']=$this->request->data['PurchaseOrder']['currency_id'];
								$productArray['PurchaseOrderProduct']['bool_received']=0;
								$productArray['PurchaseOrderProduct']['date_received']=0;
								$productArray['PurchaseOrderProduct']['production_order_id']=$purchaseOrderProduct['production_order_id'];
								$productArray['PurchaseOrderProduct']['department_id']=$purchaseOrderProduct['department_id'];
								
								$this->PurchaseOrderProduct->create();
								if (!$this->PurchaseOrderProduct->save($productArray)) {
									echo "Problema guardando los productos de la orden de compra";
									pr($this->validateErrors($this->PurchaseOrderProduct));
									throw new Exception();
								}
							}
						}
						
						foreach ($this->request->data['PurchaseOrderOtherCost'] as $purchaseOrderOtherCost){
							if ($purchaseOrderOtherCost['task_quantity']>0 && $purchaseOrderOtherCost['task_unit_cost']>0){
								//pr($quotationProduct);
								$costArray=array();
								$costArray['PurchaseOrderOtherCost']['purchase_order_id']=$purchase_order_id;
								$costArray['PurchaseOrderOtherCost']['task_description']=$purchaseOrderOtherCost['task_description'];
								$costArray['PurchaseOrderOtherCost']['department_id']=$purchaseOrderOtherCost['department_id'];
								$costArray['PurchaseOrderOtherCost']['task_quantity']=$purchaseOrderOtherCost['task_quantity'];
								$costArray['PurchaseOrderOtherCost']['task_unit_cost']=$purchaseOrderOtherCost['task_unit_cost'];
								$costArray['PurchaseOrderOtherCost']['task_total_cost']=$purchaseOrderOtherCost['task_total_cost'];
								$costArray['PurchaseOrderOtherCost']['currency_id']=$this->request->data['PurchaseOrder']['currency_id'];
								$this->PurchaseOrderOtherCost->create();
								if (!$this->PurchaseOrderOtherCost->save($costArray)) {
									echo "Problema guardando los otros costos de la orden de compra";
									pr($this->validateErrors($this->PurchaseOrderOtherCost));
									throw new Exception();
								}
							}
						}
							
						$purchaseOrderRemarkArray=array();
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['user_id']=$this->Auth->User('id');
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['purchase_order_id']=$purchase_order_id;
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_text']="Se creó la orden de compra";
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->PurchaseOrderRemark->create();
						if (!$this->PurchaseOrderRemark->save($purchaseOrderRemarkArray)) {
							echo "Problema guardando las remarcas para la orden de compra";
							pr($this->validateErrors($this->PurchaseOrderRemark));
							throw new Exception();
						}
					}	
					$datasource->commit();
					$this->recordUserAction($this->PurchaseOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de compra número ".$this->request->data['PurchaseOrder']['purchase_order_code']);
					
					$this->Session->setFlash(__('Se guardó la orden de compra.'),'default',array('class' => 'success'));				
					return $this->redirect(array('action' => 'view',$purchase_order_id));					
				}
				catch(Exception $e){
					$datasource->rollback();
					// pr($e);
					$this->Session->setFlash(__('No se podía guardar la orden de compra.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		else {
			$options = array(
				'conditions' => array(
					'PurchaseOrder.id' => $id,
				),
				'contain'=>array(
					'PurchaseOrderProduct'=>array(
						'Product',
						'ProductionOrderProduct',
					),
					'PurchaseOrderOtherCost',
					'PurchaseOrderRemark',
				),
			);
			$this->request->data = $this->PurchaseOrder->find('first', $options);
			for ($i=0;$i<count($this->request->data['PurchaseOrderProduct']);$i++){
				if ($this->request->data['PurchaseOrderProduct'][$i]['product_id']>0 && $this->request->data['PurchaseOrderProduct'][$i]['product_quantity']>0 && $this->request->data['PurchaseOrderProduct'][$i]['product_unit_cost']>0){
					$requestProducts[]['PurchaseOrderProduct']=$this->request->data['PurchaseOrderProduct'][$i];
				}
			}
			for ($i=0;$i<count($this->request->data['PurchaseOrderOtherCost']);$i++){
				if ($this->request->data['PurchaseOrderOtherCost'][$i]['task_quantity']>0 && $this->request->data['PurchaseOrderOtherCost'][$i]['task_unit_cost']>0){
					$requestOtherCosts[]['PurchaseOrderOtherCost']=$this->request->data['PurchaseOrderOtherCost'][$i];
				}
			}	
		}
			
		$this->set(compact('requestProducts'));
		$this->set(compact('requestOtherCosts'));
		$this->set(compact('currencyId'));
		
		$providers = $this->PurchaseOrder->Provider->find('list',array('order'=>'Provider.name'));
		$users = $this->PurchaseOrder->User->find('list',array('order'=>'User.username'));
		$currencies = $this->PurchaseOrder->Currency->find('list');
		$paymentModes = $this->PurchaseOrder->PaymentMode->find('list');
		$this->set(compact('providers', 'users', 'currencies', 'paymentModes'));
		
		$this->loadModel('Department');
		$departments=$this->Department->find('list');
		$this->set(compact('departments'));
		
		$this->loadModel('Product');
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->PurchaseOrder->id = $id;
		if (!$this->PurchaseOrder->exists()) {
			throw new NotFoundException(__('Invalid purchase order'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$this->loadModel('SalesOrderProduct');
		
		$purchaseOrder=$this->PurchaseOrder->find('first',array(
			'conditions'=>array(
				'PurchaseOrder.id'=>$id,
			),
			'contain'=>array(
				'PurchaseOrderProduct'=>array(
					'fields'=>array(
						'PurchaseOrderProduct.id',
						'PurchaseOrderProduct.sales_order_product_id'
					),
					'Product'=>array(
						'fields'=>array('Product.id','Product.name'),
					),
					'SalesOrderProduct'=>array(
						'SalesOrderProductStatus',
						'ProductionProcessProduct'=>array(
							'ProductionProcess'=>array(
								'fields'=>array('ProductionProcess.id','ProductionProcess.production_process_code'),
							),
						),
					),
				),
				'PurchaseOrderOtherCost',
				'PurchaseOrderRemark',
			),
		));
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (count($purchaseOrder['PurchaseOrderProduct'])>0){
			foreach ($purchaseOrder['PurchaseOrderProduct'] as $purchaseOrderProduct){
				if (!empty($purchaseOrderProduct['SalesOrderProduct'])){
					if ($purchaseOrderProduct['SalesOrderProduct']['SalesOrderProductStatus']['id']>=PRODUCT_STATUS_READY_FOR_DELIVERY){
						$boolDeletionAllowed=false;
						$flashMessage.="Esta orden de compra tiene procesos de producción correspondientes.  Para poder eliminar la orden de compra, primero hay que eliminar o modificar los procesos de producción ";
					}
				}
			}
			if (!$boolDeletionAllowed){
				foreach ($purchaseOrder['PurchaseOrderProduct'] as $purchaseOrderProduct){
					if (!empty($purchaseOrderProduct['SalesOrderProduct'])){
						if ($purchaseOrderProduct['SalesOrderProduct']['SalesOrderProductStatus']['id']>=PRODUCT_STATUS_READY_FOR_DELIVERY){
							foreach ($purchaseOrder['SalesOrderProduct']['ProductionProcessProduct'] as $processProduct){
								$flashMessage.=$processProduct['ProductionProcess']['production_process_code'].",";
							}
						}
					}
				}
			}
		}
		
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó la orden de compra.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			$datasource=$this->PurchaseOrder->getDataSource();
			$datasource->begin();	
			try {
				//delete all products, remarks and other costs
				foreach ($purchaseOrder['PurchaseOrderProduct'] as $purchaseOrderProduct){
					$salesOrderProductArray=array();
					$salesOrderProductArray['SalesOrderProduct']['id']=$purchaseOrderProduct['sales_order_product_id'];
					$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PURCHASE;
					$this->SalesOrderProduct->id=$purchaseOrderProduct['sales_order_product_id'];
					if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
						echo "Problema cambiando el estado de los productos de la orden de venta";
						pr($this->validateErrors($this->SalesOrderProduct));
						throw new Exception();
					}
					
					if (!$this->PurchaseOrder->PurchaseOrderProduct->delete($purchaseOrderProduct['id'])) {
						echo "Problema al eliminar el producto de la orden de compra";
						pr($this->validateErrors($this->PurchaseOrder->PurchaseOrderProduct));
						throw new Exception();
					}
				}
				foreach ($purchaseOrder['PurchaseOrderRemark'] as $purchaseOrderRemark){
					if (!$this->PurchaseOrder->PurchaseOrderRemark->delete($purchaseOrderRemark['id'])) {
						echo "Problema al eliminar la remarca de la orden de compra";
						pr($this->validateErrors($this->PurchaseOrder->PurchaseOrderRemark));
						throw new Exception();
					}
				}
				foreach ($purchaseOrder['PurchaseOrderOtherCost'] as $purchaseOrderOtherCost){
					if (!$this->PurchaseOrder->PurchaseOrderOtherCost->delete($purchaseOrderOtherCost['id'])) {
						echo "Problema al eliminar el costo adicional de la orden de compra";
						pr($this->validateErrors($this->PurchaseOrder->PurchaseOrderOtherCost));
						throw new Exception();
					}
				}
				
				if (!$this->PurchaseOrder->delete($id)) {
					echo "Problema al eliminar la orden de compra";
					pr($this->validateErrors($this->PurchaseOrder));
					throw new Exception();
				}
						
				$datasource->commit();
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$purchaseOrder['PurchaseOrder']['id'];
				$deletionArray['Deletion']['reference']=$purchaseOrder['PurchaseOrder']['purchase_order_code'];
				$deletionArray['Deletion']['type']='PurchaseOrder';
				$this->Deletion->save($deletionArray);
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó la orden de compra número ".$purchaseOrder['PurchaseOrder']['purchase_order_code']);
						
				$this->Session->setFlash(__('Se eliminó la orden de compra.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía eliminar la orden de compra.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}

	public function annul($id = null) {
		$this->PurchaseOrder->id = $id;
		if (!$this->PurchaseOrder->exists()) {
			throw new NotFoundException(__('Orden de Compra inválida'));
		}
		$this->request->allowMethod('post', 'delete');
		$this->loadModel('SalesOrderProduct');
		
		$datasource=$this->PurchaseOrder->getDataSource();
		$datasource->begin();
		try {
			//pr($this->request->data);
			
			$this->loadModel('PurchaseOrderProduct');
			$this->PurchaseOrderProduct->recursive=-1;
			$previousPurchaseOrderProducts=$this->PurchaseOrderProduct->find('all',array(
				'fields'=>array(
					'PurchaseOrderProduct.id',
					'PurchaseOrderProduct.sales_order_product_id',
				),
				'conditions'=>array(
					'PurchaseOrderProduct.purchase_order_id'=>$id,
				),
			));
			if (!empty($previousPurchaseOrderProducts)){
				foreach ($previousPurchaseOrderProducts as $previousPurchaseOrderProduct){
					$salesOrderProductArray=array();
					$salesOrderProductArray['SalesOrderProduct']['id']=$previousPurchaseOrderProduct['PurchaseOrderProduct']['sales_order_product_id'];
					$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PURCHASE;
					$this->SalesOrderProduct->id=$previousPurchaseOrderProduct['PurchaseOrderProduct']['sales_order_product_id'];
					if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
						echo "Problema cambiando el estado de los productos de la orden de venta";
						pr($this->validateErrors($this->SalesOrderProduct));
						throw new Exception();
					}
				
					$this->PurchaseOrderProduct->id=$previousPurchaseOrderProduct['PurchaseOrderProduct']['id'];
					if (!$this->PurchaseOrderProduct->delete($previousPurchaseOrderProduct['PurchaseOrderProduct']['id'])){
						echo "Problema al eliminar los productos de la orden de compra";
						pr($this->validateErrors($this->PurchaseOrderProduct));
						throw new Exception();
					}
				}
			}
			$this->loadModel('PurchaseOrderOtherCost');
			$this->PurchaseOrderOtherCost->recursive=-1;
			$previousPurchaseOrderOtherCosts=$this->PurchaseOrderOtherCost->find('all',array(
				'fields'=>array('PurchaseOrderOtherCost.id'),
				'conditions'=>array(
					'PurchaseOrderOtherCost.purchase_order_id'=>$id,
				),
			));
			if (!empty($previousPurchaseOrderOtherCosts)){
				foreach ($previousPurchaseOrderOtherCosts as $previousPurchaseOrderOtherCost){
					$this->PurchaseOrderOtherCost->id=$previousPurchaseOrderOtherCost['PurchaseOrderOtherCost']['id'];
					if (!$this->PurchaseOrderOtherCost->delete($previousPurchaseOrderOtherCost['PurchaseOrderOtherCost']['id'])){
						echo "Problema al eliminar los otros costos de la orden de compra";
						pr($this->validateErrors($this->PurchaseOrderOtherCost));
						throw new Exception();
					}
				}
			}
		
			$this->PurchaseOrder->id=$id;
			$purchaseOrderArray=array();
			$purchaseOrderArray['PurchaseOrder']['id']=$id;
			$purchaseOrderArray['PurchaseOrder']['bool_annulled']=true;
			$purchaseOrderArray['PurchaseOrder']['cost_subtotal']=0;
			$purchaseOrderArray['PurchaseOrder']['cost_iva']=0;
			$purchaseOrderArray['PurchaseOrder']['cost_total']=0;
			if (!$this->PurchaseOrder->save($purchaseOrderArray)) {
				echo "Problema al anular la orden de compra";
				pr($this->validateErrors($this->PurchaseOrder));
				throw new Exception();
			}
						
			$datasource->commit();
			$this->Session->setFlash(__('La orden de compra se anuló.'),'default',array('class' => 'success'));
		}
		catch(Exception $e){
			$this->Session->setFlash(__('La orden de compra no se podía anular.'), 'default',array('class' => 'error-message'));
		}
		
		return $this->redirect(array('action' => 'index'));
	}

	public function recibirOrdenDeCompra($id = 0) {
		$this->loadModel('PurchaseOrderProduct');
		$this->loadModel('PurchaseOrderRemark');
		$this->loadModel('ProductionOrder');
		$this->loadModel('SalesOrderProduct');
		
		$purchaseOrderId=0;
		if ($id>0){
			if (!$this->PurchaseOrder->exists($id)) {
				throw new NotFoundException(__('Invalid purchase order'));
			}
			$purchaseOrderId=$id;
		}
		if ($this->request->is(array('post', 'put'))) {
			//pr($this->request->data);
			//$purchaseOrderId=$this->request->data['PurchaseOrder']['id'];
			$purchaseOrderId=$this->request->data['Report']['purchase_order_id'];
		}
		$this->set(compact('purchaseOrderId'));

		$productionOrders=$this->ProductionOrder->find('list',array(
			'order'=>'ProductionOrder.production_order_code',
		));
		$this->set(compact('productionOrders'));
		
		$requestProducts=array();
		if ($this->request->is(array('post', 'put'))) {
			foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){
				if ($purchaseOrderProduct['id']>0 ){
					$requestProducts[]['PurchaseOrderProduct']=$purchaseOrderProduct;
				}
			}
			
			if ($purchaseOrderId>0){
				$datasource=$this->PurchaseOrder->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$purchase_order_id=$purchaseOrderId;
					foreach ($this->request->data['PurchaseOrderProduct'] as $purchaseOrderProduct){						
						//pr($purchaseOrderProduct);
						$productArray=array();
						$productArray['PurchaseOrderProduct']['bool_received']=$purchaseOrderProduct['bool_received'];
						$productArray['PurchaseOrderProduct']['date_received']=$purchaseOrderProduct['date_received'];
						$this->PurchaseOrderProduct->id=$purchaseOrderProduct['id'];
						if (!$this->PurchaseOrderProduct->save($productArray)) {
							echo "Problema guardando los productos de la orden de compra";
							pr($this->validateErrors($this->PurchaseOrderProduct));
							throw new Exception();
						}
						 if ($purchaseOrderProduct['bool_received']){
							$salesOrderProductArray=array();
							$salesOrderProductArray['SalesOrderProduct']['id']=$purchaseOrderProduct['sales_order_product_id'];
							$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PRODUCTION;
							$this->SalesOrderProduct->id=$purchaseOrderProduct['sales_order_product_id'];
							if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
								echo "Problema cambiando el estado de los productos de la orden de venta";
								pr($this->validateErrors($this->SalesOrderProduct));
								throw new Exception();
							}
						}
					}
					
					$purchaseOrderArray=array();
					$purchaseOrderArray['PurchaseOrder']['bool_received']=$this->request->data['PurchaseOrder']['bool_received'];
					$this->PurchaseOrder->id=$purchaseOrderId;
					if (!$this->PurchaseOrder->save($purchaseOrderArray)) {
						echo "Problema guardando la orden de compra";
						pr($this->validateErrors($this->PurchaseOrder));
						throw new Exception();
					}
					
					$purchaseOrderRemarkArray=array();
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['user_id']=$this->Auth->User('id');
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['purchase_order_id']=$purchaseOrderId;
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
					if ($purchaseOrderArray['PurchaseOrder']['bool_received']){
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_text']="Se recibió la orden de compra";
					}
					else {
						$purchaseOrderRemarkArray['PurchaseOrderRemark']['remark_text']="Se ha modificado el estado de recibido de los productos";
					}
					$purchaseOrderRemarkArray['PurchaseOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
					$this->PurchaseOrderRemark->create();
					if (!$this->PurchaseOrderRemark->save($purchaseOrderRemarkArray)) {
						echo "Problema guardando las remarcas para la orden de compra";
						pr($this->validateErrors($this->PurchaseOrderRemark));
						throw new Exception();
					}
					
					$datasource->commit();
					$this->recordUserAction($this->PurchaseOrder->id,null,null);
					$purchaseOrder=$this->PurchaseOrder->find('first',array('conditions'=>array('PurchaseOrder.id'=>$purchaseOrderId)));
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de compra número ".$purchaseOrder['PurchaseOrder']['purchase_order_code']);
					
					$this->Session->setFlash(__('Se guardó la orden de compra.'),'default',array('class' => 'success'));				
					return $this->redirect(array('action' => 'view',$purchaseOrderId));					
				}
				catch(Exception $e){
					$datasource->rollback();
					// pr($e);
					$this->Session->setFlash(__('No se podía guardar la orden de compra.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		else {
			if ($purchaseOrderId){
				$this->request->data = $this->PurchaseOrder->find('first', array(
					'conditions' => array(
						'PurchaseOrder.id' => $purchaseOrderId,
					),
					'contain'=>array(
						'PurchaseOrderProduct'=>array(
							'Product',
							'ProductionOrderProduct',
						),
						//'PurchaseOrderOtherCost',
						'PurchaseOrderRemark',
					),
				));
				for ($i=0;$i<count($this->request->data['PurchaseOrderProduct']);$i++){
					$requestProducts[]['PurchaseOrderProduct']=$this->request->data['PurchaseOrderProduct'][$i];
				}
			}
		}
			
		$this->set(compact('requestProducts'));
		//$this->set(compact('requestOtherCosts'));
		$this->set(compact('currencyId'));
		
		$providers = $this->PurchaseOrder->Provider->find('list',array('order'=>'Provider.name'));
		$users = $this->PurchaseOrder->User->find('list',array('order'=>'User.username'));
		$currencies = $this->PurchaseOrder->Currency->find('list');
		$paymentModes = $this->PurchaseOrder->PaymentMode->find('list');
		$this->set(compact('providers', 'users', 'currencies', 'paymentModes'));
		
		$this->loadModel('Department');
		$departments=$this->Department->find('list');
		$this->set(compact('departments'));
		
		$this->loadModel('Product');
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$purchaseOrders=$this->PurchaseOrder->find('list',array(
			'conditions'=>array(
				'OR'=>array(
					array(
						'PurchaseOrder.bool_received'=>false,
						'PurchaseOrder.bool_annulled'=>false,
					),
					array(
						'PurchaseOrder.id'=>$purchaseOrderId,
					),
				),
			),
			'order'=>'PurchaseOrder.purchase_order_code',
		));
		$this->set(compact('purchaseOrders'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
	}

	public function getpurchaseorderinfo() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$purchaseOrderId=trim($_POST['purchaseorderid']);
		
		$this->PurchaseOrder->recursive=-1;
		$purchaseOrder=$this->PurchaseOrder->find('first',array(
			'conditions'=>array(
				'PurchaseOrder.id'=> $purchaseOrderId,
			),
			'contain'=>array(
				'Currency',
				'PaymentMode',
				'Provider',
				'User',
			),
		));
		return json_encode($purchaseOrder);
	}
	
	public function getpurchaseorderproducts() {
		//$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$purchaseOrderId=trim($_POST['purchaseorderid']);
		
		$purchaseOrder=$this->PurchaseOrder->find('first',array(
			'conditions'=>array(
				'PurchaseOrder.id'=> $purchaseOrderId,
			),
		));
		$currencyId=$purchaseOrder['PurchaseOrder']['currency_id'];
		$this->set(compact('currencyId'));
		
		$this->loadModel('PurchaseOrderProduct');
		$this->PurchaseOrderProduct->recursive=-1;
		$purchaseOrderProducts=$this->PurchaseOrderProduct->find('all',array(
			'conditions'=>array(
				'PurchaseOrderProduct.purchase_order_id'=> $purchaseOrderId,
			),
			'contain'=>array(
				'Product',
				'ProductionOrder',
				'Department',
				'ProductionOrderProduct'=>array(
					'ProductionOrder',
				),
			),
		));
		$this->set(compact('purchaseOrderProducts'));
	}
}
