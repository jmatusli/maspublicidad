<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
/**
 * SalesOrders Controller
 *
 * @property SalesOrder $SalesOrder
 * @property PaginatorComponent $Paginator
 */
class SalesOrdersController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('getsalesordersforclient','getsalesorderproducts','getsalesorderinfo');		
	}
	public function index() {
		$this->SalesOrder->recursive = -1;
		
		//echo "user id in session is ".$_SESSION['userId']."<br/>";
		$user_id=$this->Auth->User('id');
		$currencyId=CURRENCY_USD;
		
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$user_id=$this->request->data['Report']['user_id'];
			$currencyId=$this->request->data['Report']['currency_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			//echo "retrieving values from session<br/>";
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
			if ($this->Session->check('userId')){
				$user_id=$_SESSION['userId'];
			}
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$_SESSION['currencyId']=$currencyId;
		$_SESSION['userId']=$user_id;
		
		$this->set(compact('startDate','endDate','user_id','currencyId'));
		
		$conditions=array(
			'SalesOrder.sales_order_date >='=>$startDate,
			'SalesOrder.sales_order_date <'=>$endDatePlusOne,
			
		);
		$userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole'));
		if (($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT)||$user_id!=0) { 
			$this->loadModel('Quotation');
			$this->Quotation->recursive=-1;
			$quotationList=$this->Quotation->find('list',array(
				'fields'=>'Quotation.id',
				'conditions'=>array(
					'Quotation.user_id'=>$user_id,	
				),
			));
			$conditions[]=array('SalesOrder.quotation_id'=>$quotationList);
		}
		
		$salesOrderCount=	$this->SalesOrder->find('count', array(
			'fields'=>array('SalesOrder.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(
				'Currency',
				'Quotation'=>array(
					'Client',
					'User',
				),
				'Invoice'=>array(
					'fields'=>array('Invoice.id','Invoice.invoice_code'),
					'conditions'=>array(
						'Invoice.bool_annulled'=>false,
					),
				),
			),
			'limit'=>($salesOrderCount!=0?$salesOrderCount:1),
		);

		$salesOrders = $this->Paginator->paginate('SalesOrder');
		if (!empty($salesOrders)){
			$this->loadModel('ExchangeRate');
			for ($i=0;$i<count($salesOrders);$i++){
				// get the exchange rate
				$salesOrderDate=$salesOrders[$i]['SalesOrder']['sales_order_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($salesOrderDate);
				$salesOrders[$i]['SalesOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				// get the status
				$salesOrderProducts=$this->SalesOrder->SalesOrderProduct->find('all',array(
					'conditions'=>array('SalesOrderProduct.sales_order_id'=>$salesOrders[$i]['SalesOrder']['id']),
				));
				$status=0;
				// start making the weighted average
				if (!empty($salesOrderProducts)){
					foreach ($salesOrderProducts as $salesOrderProduct){
						$status=50*($salesOrderProduct['SalesOrderProduct']['sales_order_product_status_id']-1)*$salesOrderProduct['SalesOrderProduct']['product_total_price'];
					}
					if (!empty($salesOrders[$i]['SalesOrder']['price_subtotal'])){
						$status=$status/$salesOrders[$i]['SalesOrder']['price_subtotal'];
					}
				}
				$salesOrders[$i]['SalesOrder']['status']=$status;
				
				if (!empty($salesOrders[$i]['Invoice'])){
					$salesOrders[$i]['SalesOrder']['bool_invoice_present']=true;
				}
				else {
					$salesOrders[$i]['SalesOrder']['bool_invoice_present']=false;
				}
			}
		}
		$this->set(compact('salesOrders'));
		
		$this->loadModel('User');
		$userConditions=array();
		if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT){
			$userConditions=array('User.id'=>$user_id);
		}
		$users=$this->User->find('list',array(
			'conditions'=>$userConditions,
			'order'=>'User.username'
		));
		$this->set(compact('users'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$aco_name="SalesOrders/cambiarEstado";		
		$bool_cambiarestado_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_cambiarestado_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
		
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->SalesOrder->exists($id)) {
			throw new NotFoundException(__('Invalid sales order'));
		}
		/*
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
		}
		if (!isset($startDate)){
			$startDate = date("Y-m-01");
		}
		if (!isset($endDate)){
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		$this->set(compact('startDate','endDate'));
		*/
		$options = array(
			'conditions' => array(
				'SalesOrder.' . $this->SalesOrder->primaryKey => $id,
				
			),
			'contain'=>array(
				'Currency',
				'Quotation'=>array(
					'Client',
					'Contact',
					'User',
				),
				'SalesOrderProduct'=>array(
					'Product',
					'SalesOrderProductStatus',
					'Currency',
				),
				'Invoice'=>array(
					//'Product',
				),
			),
		);
		$this->SalesOrder->recursive=-1;
		$salesOrder=$this->SalesOrder->find('first', $options);
		$this->set(compact('salesOrder'));
		
		$filename='Orden de Venta_'.$salesOrder['SalesOrder']['sales_order_code'];
		$this->set(compact('filename'));
		
		$aco_name="SalesOrders/cambiarEstado";		
		$bool_cambiarestado_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_cambiarestado_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}
	
	public function viewPdf($id = null) {
		if (!$this->SalesOrder->exists($id)) {
			throw new NotFoundException(__('Orden de Venta no válida'));
		}

		$options = array(
			'conditions' => array(
				'SalesOrder.' . $this->SalesOrder->primaryKey => $id,
				
			),
			'contain'=>array(
				'Currency',
				'Quotation'=>array(
					'Client',
					'Contact',
					'User',
				),
				'SalesOrderProduct'=>array(
					'Product',
					'SalesOrderProductStatus',
					'Currency',
				),
			),
		);
		$salesOrder=$this->SalesOrder->find('first', $options);
		$this->set(compact('salesOrder'));
		
		$dueDate= new DateTime($salesOrder['Quotation']['due_date']);
		$quotationDate= new DateTime($salesOrder['Quotation']['quotation_date']);
		$daysValid=$quotationDate->diff($dueDate);
		$validityQuotation=(int)$daysValid->format("%r%a");
		$this->set(compact('validityQuotation'));
		
		$filename='Orden de Venta_'.$salesOrder['SalesOrder']['sales_order_code'];
		$this->set(compact('filename'));
	}

	public function add($quotation_id=0) {
		$this->set(compact('quotation_id'));
		
		$this->loadModel('Quotation');
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('SalesOrderProductStatus');
		
		if ($this->request->is('post')) {
			$salesOrderDateArray=$this->request->data['SalesOrder']['sales_order_date'];
			//pr($entryDateArray);
			$salesOrderDateString=$salesOrderDateArray['year'].'-'.$salesOrderDateArray['month'].'-'.$salesOrderDateArray['day'];
			$salesOrderDate=date( "Y-m-d", strtotime($salesOrderDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['SalesOrderProduct'] as $salesOrderProduct){
				if ($salesOrderProduct['product_id']>0){
					$multiplicationDifference=abs($salesOrderProduct['product_total_price']-$salesOrderProduct['product_quantity']*$salesOrderProduct['product_unit_price']);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			
			if ($salesOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de venta no puede estar en el futuro!  No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			else {
				$datasource=$this->SalesOrder->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
										
					$this->SalesOrder->create();
					if (!$this->SalesOrder->save($this->request->data)) {
						echo "Problema guardando la orden de venta";
						//pr($this->validateErrors($this->SalesOrder));
						throw new Exception();
					} 
					$sales_order_id=$this->SalesOrder->id;
					
					foreach ($this->request->data['SalesOrderProduct'] as $salesOrderProduct){
						if ($salesOrderProduct['product_id']>0&&$salesOrderProduct['product_quantity']>0){
							//pr($salesOrderProduct);
							$productArray=array();
							$productArray['SalesOrderProduct']['sales_order_id']=$sales_order_id;
							$productArray['SalesOrderProduct']['product_id']=$salesOrderProduct['product_id'];
							$productArray['SalesOrderProduct']['product_description']=$salesOrderProduct['product_description'];
							$productArray['SalesOrderProduct']['product_quantity']=$salesOrderProduct['product_quantity'];
							$productArray['SalesOrderProduct']['sales_order_product_status_id']=$salesOrderProduct['sales_order_product_status_id'];
							$productArray['SalesOrderProduct']['product_unit_price']=$salesOrderProduct['product_unit_price'];
							$productArray['SalesOrderProduct']['product_total_price']=$salesOrderProduct['product_total_price'];
							$productArray['SalesOrderProduct']['currency_id']=$this->request->data['SalesOrder']['currency_id'];
							
							$this->SalesOrderProduct->create();
							if (!$this->SalesOrderProduct->save($productArray)) {
								echo "Problema guardando los productos de la orden de venta";
								//pr($this->validateErrors($this->SalesOrderProduct));
								throw new Exception();
							}
						}
					}
					
					$datasource->commit();
					$this->recordUserAction($this->SalesOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de venta número ".$this->request->data['SalesOrder']['sales_order_code']);
					
					$this->Session->setFlash(__('The sales order has been saved.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				}
				catch(Exception $e){
					$datasource->rollback();
					$this->Session->setFlash(__('No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
				}
			}
		}
			
		$user_id=$this->Auth->User('id');
		$this->set(compact('user_id'));
		
		$quotationsInSalesOrders=$this->SalesOrder->find('list',array(
			'fields'=>'SalesOrder.quotation_id',
			'order'=>'SalesOrder.quotation_id',
		));
		//pr($quotationsInSalesOrders);
		
		$quotations = $this->SalesOrder->Quotation->find('list',array(
			'conditions'=>array(
				'Quotation.user_id'=>$user_id,
				'Quotation.id !='=>$quotationsInSalesOrders,
			),
			'order'=>'Quotation.quotation_code',
		));
		$this->set(compact('quotations'));
		
		$salesOrderProductStatuses = $this->SalesOrderProductStatus->find('list');
		$currencies = $this->SalesOrder->Currency->find('list');
		$this->set(compact('salesOrderProductStatuses','currencies'));
		
		
		$this->loadModel('Client');
		$clients = $this->Client->find('list',array('order'=>'Client.name'));
		$this->set(compact('clients'));
		
		$salesOrderDate=date( "Y-m-d");
		$salesOrderExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($salesOrderDate);
		$exchangeRateSalesOrder=$salesOrderExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateSalesOrder'));
		
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
		
		$role_id = $this->Auth->User('role_id');
		$this->set(compact('role_id'));
	}

	public function edit($id = null) {
		if (!$this->SalesOrder->exists($id)) {
			throw new NotFoundException(__('Invalid sales order'));
		}
		
		$this->loadModel('Quotation');
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('SalesOrderProductStatus');
		$this->loadModel('Product');
		
		if ($this->request->is(array('post', 'put'))) {
			$salesOrderDateArray=$this->request->data['SalesOrder']['sales_order_date'];
			//pr($entryDateArray);
			$salesOrderDateString=$salesOrderDateArray['year'].'-'.$salesOrderDateArray['month'].'-'.$salesOrderDateArray['day'];
			$salesOrderDate=date( "Y-m-d", strtotime($salesOrderDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['SalesOrderProduct'] as $salesOrderProduct){
				if ($salesOrderProduct['product_id']>0){
					$multiplicationDifference=abs($salesOrderProduct['product_total_price']-$salesOrderProduct['product_quantity']*$salesOrderProduct['product_unit_price']);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			
			if ($salesOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de venta no puede estar en el futuro!  No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			
			else {
				$datasource=$this->SalesOrder->getDataSource();
				try {
					//pr($this->request->data);
					$datasource->begin();
					
					$this->SalesOrderProduct->recursive=-1;
					$previousSalesOrderProducts=$this->SalesOrderProduct->find('all',array(
						'fields'=>array('SalesOrderProduct.id'),
						'conditions'=>array(
							'SalesOrderProduct.sales_order_id'=>$id,
						),
					));
					if (!empty($previousSalesOrderProducts)){
						foreach ($previousSalesOrderProducts as $previousSalesOrderProduct){
							$this->SalesOrderProduct->id=$previousSalesOrderProduct['SalesOrderProduct']['id'];
							$this->SalesOrderProduct->delete($previousSalesOrderProduct['SalesOrderProduct']['id']);
						}
					}
					
					if ($this->request->data['SalesOrder']['bool_annulled']){
						$this->request->data['SalesOrder']['price_subtotal']=0;
						$this->request->data['SalesOrder']['price_iva']=0;
						$this->request->data['SalesOrder']['price_total']=0;
						$this->SalesOrder->id=$id;
						if (!$this->SalesOrder->save($this->request->data)) {
							echo "Problema guardando la orden de venta";
							//pr($this->validateErrors($this->SalesOrder));
							throw new Exception();
						} 
						$sales_order_id=$this->SalesOrder->id;
					}
					else {				
						if (!$this->SalesOrder->save($this->request->data)) {
							echo "Problema guardando la orden de venta";
							//pr($this->validateErrors($this->SalesOrder));
							throw new Exception();
						} 
						$sales_order_id=$this->SalesOrder->id;
						
						foreach ($this->request->data['SalesOrderProduct'] as $salesOrderProduct){
							if ($salesOrderProduct['product_id']>0){
								//pr($salesOrderProduct);
								$productArray=array();
								$productArray['SalesOrderProduct']['sales_order_id']=$sales_order_id;
								$productArray['SalesOrderProduct']['product_id']=$salesOrderProduct['product_id'];
								$productArray['SalesOrderProduct']['product_description']=$salesOrderProduct['product_description'];
								$productArray['SalesOrderProduct']['product_quantity']=$salesOrderProduct['product_quantity'];
								$productArray['SalesOrderProduct']['sales_order_product_status_id']=$salesOrderProduct['sales_order_product_status_id'];
								$productArray['SalesOrderProduct']['product_unit_price']=$salesOrderProduct['product_unit_price'];
								$productArray['SalesOrderProduct']['product_total_price']=$salesOrderProduct['product_total_price'];
								$productArray['SalesOrderProduct']['currency_id']=$this->request->data['SalesOrder']['currency_id'];
								
								$this->SalesOrderProduct->create();
								if (!$this->SalesOrderProduct->save($productArray)) {
									echo "Problema guardando los productos de la orden de venta";
									//pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
							}
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->SalesOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de venta número ".$this->request->data['SalesOrder']['sales_order_code']);
					
					$this->Session->setFlash(__('The sales order has been saved.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				}
				catch(Exception $e){
					$datasource->rollback();
					$this->Session->setFlash(__('No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
				}			
				
			}
		} 
		else {
			$options = array(
				'conditions' => array(
					'SalesOrder.id' => $id,
				),
				'contain'=>array(
					'SalesOrderProduct',
				),
			);
			$this->request->data = $this->SalesOrder->find('first', $options);
			
		}
		$relatedQuotation=$this->Quotation->read(null,$this->request->data['SalesOrder']['quotation_id']);
		$this->set(compact('relatedQuotation'));	
		
		$user_id=$this->Auth->User('id');
		$this->set(compact('user_id'));
		$role_id = $this->Auth->User('role_id');
		$this->set(compact('role_id'));
		
		/*
		$relatedClientId=0;
		
		if (!empty($this->request->data['SalesOrder']['client_id'])){
			$relatedClientId=$this->request->data['SalesOrder']['client_id'];
		}
		else {
			$relatedClientId=$relatedQuotation['Quotation']['client_id'];
		}
		
		$quotationConditions=array(
			'Quotation.client_id'=>$relatedClientId,
		);
		if ($role_id!=ROLE_ADMIN){
			$quotationConditions[]=array('Quotation.user_id'=>$user_id);
		}
		
		$quotations = $this->SalesOrder->Quotation->find('list',array(
			'conditions'=>$quotationConditions,
		));
		*/
		
		
		$quotationsInSalesOrders=$this->SalesOrder->find('list',array(
			'fields'=>'SalesOrder.quotation_id',
			'conditions'=>array(
				'SalesOrder.id !='=>$id,
			),
			'order'=>'SalesOrder.quotation_id',
		));
		//pr($quotationsInSalesOrders);
		
		$quotationConditions=array(
			'Quotation.id !='=>$quotationsInSalesOrders,
		);
		if ($role_id!=ROLE_ADMIN){
			$quotationConditions[]=array('Quotation.user_id'=>$user_id);
		}
		$quotations = $this->SalesOrder->Quotation->find('list',array(
			'conditions'=>$quotationConditions,
			'order'=>'Quotation.quotation_code',
		));
		$this->set(compact('quotations'));
		
		$currencies = $this->SalesOrder->Currency->find('list');
		$this->set(compact('quotations','currencies'));
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$salesOrderProductStatuses=$this->SalesOrderProductStatus->find('list');
		$this->set(compact('salesOrderProductStatuses'));
		
		$this->loadModel('Client');
		$clients = $this->Client->find('list',array('order'=>'Client.name'));
		$this->set(compact('clients'));
		
		$salesOrderDateAsString=$this->SalesOrder->deconstruct('sales_order_date',$this->request->data['SalesOrder']['sales_order_date']);
		$salesOrderExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($salesOrderDateAsString);
		$exchangeRateSalesOrder=$salesOrderExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateSalesOrder'));
		
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}

	public function cambiarEstado($id = null) {
		if (!$this->SalesOrder->exists($id)) {
			throw new NotFoundException(__('Invalid sales order'));
		}
		
		$this->loadModel('Quotation');
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('SalesOrderProductStatus');
		$this->loadModel('Product');
		
		if ($this->request->is(array('post', 'put'))) {
			$salesOrderDateArray=$this->request->data['SalesOrder']['sales_order_date'];
			//pr($entryDateArray);
			$salesOrderDateString=$salesOrderDateArray['year'].'-'.$salesOrderDateArray['month'].'-'.$salesOrderDateArray['day'];
			$salesOrderDate=date( "Y-m-d", strtotime($salesOrderDateString));
			if ($salesOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de venta no puede estar en el futuro!  No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
			}
			else {
				$datasource=$this->SalesOrder->getDataSource();
				try {
					//pr($this->request->data);
					$datasource->begin();
					
					$this->SalesOrderProduct->recursive=-1;
					$previousSalesOrderProducts=$this->SalesOrderProduct->find('all',array(
						'fields'=>array('SalesOrderProduct.id'),
						'conditions'=>array(
							'SalesOrderProduct.sales_order_id'=>$id,
						),
					));
					if (!empty($previousSalesOrderProducts)){
						foreach ($previousSalesOrderProducts as $previousSalesOrderProduct){
							$this->SalesOrderProduct->id=$previousSalesOrderProduct['SalesOrderProduct']['id'];
							$this->SalesOrderProduct->delete($previousSalesOrderProduct['SalesOrderProduct']['id']);
						}
					}
					
					if ($this->request->data['SalesOrder']['bool_annulled']){
						$this->request->data['SalesOrder']['price_subtotal']=0;
						$this->request->data['SalesOrder']['price_iva']=0;
						$this->request->data['SalesOrder']['price_total']=0;
						$this->SalesOrder->id=$id;
						if (!$this->SalesOrder->save($this->request->data)) {
							echo "Problema guardando la orden de venta";
							//pr($this->validateErrors($this->SalesOrder));
							throw new Exception();
						} 
						$sales_order_id=$this->SalesOrder->id;
					}
					else {				
						if (!$this->SalesOrder->save($this->request->data)) {
							echo "Problema guardando la orden de venta";
							//pr($this->validateErrors($this->SalesOrder));
							throw new Exception();
						} 
						$sales_order_id=$this->SalesOrder->id;
						
						foreach ($this->request->data['SalesOrderProduct'] as $salesOrderProduct){
							if ($salesOrderProduct['product_id']>0){
								//pr($salesOrderProduct);
								$productArray=array();
								$productArray['SalesOrderProduct']['sales_order_id']=$sales_order_id;
								$productArray['SalesOrderProduct']['product_id']=$salesOrderProduct['product_id'];
								$productArray['SalesOrderProduct']['product_description']=$salesOrderProduct['product_description'];
								$productArray['SalesOrderProduct']['product_quantity']=$salesOrderProduct['product_quantity'];
								$productArray['SalesOrderProduct']['sales_order_product_status_id']=$salesOrderProduct['sales_order_product_status_id'];
								$productArray['SalesOrderProduct']['product_unit_price']=$salesOrderProduct['product_unit_price'];
								$productArray['SalesOrderProduct']['product_total_price']=$salesOrderProduct['product_total_price'];
								$productArray['SalesOrderProduct']['currency_id']=$this->request->data['SalesOrder']['currency_id'];
								
								$this->SalesOrderProduct->create();
								if (!$this->SalesOrderProduct->save($productArray)) {
									echo "Problema guardando los productos de la orden de venta";
									//pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
							}
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->SalesOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de venta número ".$this->request->data['SalesOrder']['sales_order_code']);
					
					$this->Session->setFlash(__('The sales order has been saved.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				}
				catch(Exception $e){
					$datasource->rollback();
					$this->Session->setFlash(__('No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
				}			
				
			}
		} 
		else {
			$options = array(
				'conditions' => array(
					'SalesOrder.id' => $id,
				),
				'contain'=>array(
					'SalesOrderProduct',
				),
			);
			$this->request->data = $this->SalesOrder->find('first', $options);
			
		}
		$relatedQuotation=$this->Quotation->read(null,$this->request->data['SalesOrder']['quotation_id']);
		$this->set(compact('relatedQuotation'));	
		
		$user_id=$this->Auth->User('id');
		$this->set(compact('user_id'));
		$role_id = $this->Auth->User('role_id');
		$this->set(compact('role_id'));
		
		/*
		$relatedClientId=0;
		
		if (!empty($this->request->data['SalesOrder']['client_id'])){
			$relatedClientId=$this->request->data['SalesOrder']['client_id'];
		}
		else {
			$relatedClientId=$relatedQuotation['Quotation']['client_id'];
		}
		
		$quotationConditions=array(
			'Quotation.client_id'=>$relatedClientId,
		);
		if ($role_id!=ROLE_ADMIN){
			$quotationConditions[]=array('Quotation.user_id'=>$user_id);
		}
		
		$quotations = $this->SalesOrder->Quotation->find('list',array(
			'conditions'=>$quotationConditions,
		));
		*/
		
		
		$quotationsInSalesOrders=$this->SalesOrder->find('list',array(
			'fields'=>'SalesOrder.quotation_id',
			'conditions'=>array(
				'SalesOrder.id !='=>$id,
			),
			'order'=>'SalesOrder.quotation_id',
		));
		//pr($quotationsInSalesOrders);
		
		$quotationConditions=array(
			'Quotation.id !='=>$quotationsInSalesOrders,
		);
		
		$quotations = $this->SalesOrder->Quotation->find('list',array(
			'conditions'=>$quotationConditions,
			'order'=>'Quotation.quotation_code',
		));
		$this->set(compact('quotations'));
		
		$currencies = $this->SalesOrder->Currency->find('list');
		$this->set(compact('quotations','currencies'));
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		

		$salesOrderProductStatuses=$this->SalesOrderProductStatus->find('list');
		$this->set(compact('salesOrderProductStatuses'));
		
		
		$this->loadModel('Client');
		$clients = $this->Client->find('list',array('order'=>'Client.name'));
		$this->set(compact('clients'));
		
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->SalesOrder->id = $id;
		if (!$this->SalesOrder->exists()) {
			throw new NotFoundException(__('Invalid sales order'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$this->SalesOrder->recursive=-1;
		$salesOrder=$this->SalesOrder->find('first',array(
			'conditions'=>array(
				'SalesOrder.id'=>$id,
			),
			'contain'=>array(
				'Invoice'=>array(
					'conditions'=>array(
						'Invoice.bool_annulled'=>false,
					),
				),
				'SalesOrderProduct',
			)
		));

		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (count($salesOrder['Invoice'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Esta orden de venta tiene facturas correspondientes.  Para poder eliminar la orden de venta, primero hay que eliminar o modificar las facturas ";
			if (count($salesOrder['Invoice'])==1){
				$flashMessage.=$salesOrder['Invoice'][0]['invoice_code'].".";
			}
			else {
				for ($i=0;$i<count($salesOrder['Invoice']);$i++){
					$flashMessage.=$salesOrder['Invoice'][$i]['invoice_code'];
					if ($i==count($salesOrder['Invoice'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó la orden de venta.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			try {
				$datasource=$this->SalesOrder->getDataSource();
				$datasource->begin();	
				//delete all stockMovements, stockItems and stockItemLogs
				foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
					if (!$this->SalesOrder->SalesOrderProduct->delete($salesOrderProduct['id'])) {
						echo "Problema al eliminar el producto de la orden de venta";
						//pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
						throw new Exception();
					}
				}
				
				if (!$this->SalesOrder->delete($id)) {
					echo "Problema al eliminar la orden de venta";
					//pr($this->validateErrors($this->SalesOrder));
					throw new Exception();
				}
						
				$datasource->commit();
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó la orden de venta número ".$salesOrder['SalesOrder']['sales_order_code']);
						
				$this->Session->setFlash(__('Se eliminó la orden de venta.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				//pr($e);
				$this->Session->setFlash(__('No se podía eliminar la orden de venta.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}

	public function annul($id = null) {
		$this->SalesOrder->id = $id;
		if (!$this->SalesOrder->exists()) {
			throw new NotFoundException(__('Orden de Venta inválida'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$datasource=$this->SalesOrder->getDataSource();
		try {
			//pr($this->request->data);
			$datasource->begin();
			
			$this->loadModel('SalesOrderProduct');
			$this->SalesOrderProduct->recursive=-1;
			$previousSalesOrderProducts=$this->SalesOrderProduct->find('all',array(
				'fields'=>array('SalesOrderProduct.id'),
				'conditions'=>array(
					'SalesOrderProduct.sales_order_id'=>$id,
				),
			));
			if (!empty($previousSalesOrderProducts)){
				foreach ($previousSalesOrderProducts as $previousSalesOrderProduct){
					$this->SalesOrderProduct->id=$previousSalesOrderProduct['SalesOrderProduct']['id'];
					if (!$this->SalesOrderProduct->delete($previousSalesOrderProduct['SalesOrderProduct']['id'])){
						echo "Problema al eliminar los productos de la orden de venta";
						pr($this->validateErrors($this->SalesOrderProduct));
						throw new Exception();
					}
				}
			}
		
			$this->SalesOrder->id=$id;
			$salesOrderArray['SalesOrder']['id']=$id;
			$salesOrderArray['SalesOrder']['bool_annulled']=true;
			$salesOrderArray['SalesOrder']['price_subtotal']=0;
			if (!$this->SalesOrder->save($salesOrderArray)) {
				echo "Problema al anular la orden de venta";
				pr($this->validateErrors($this->SalesOrder));
				throw new Exception();
			}
						
			$datasource->commit();
			$this->Session->setFlash(__('La orden de venta se anuló.'),'default',array('class' => 'success'));
		}
		catch(Exception $e){
			$this->Session->setFlash(__('La orden de venta no se podía anular.'), 'default',array('class' => 'error-message'));
		}
		
		return $this->redirect(array('action' => 'index'));
	}

	public function getsalesordersforclient() {
		$this->layout = "ajax";
		
		$clientid=trim($_POST['clientid']);
		$userid=trim($_POST['userid']);
		if (!$clientid){
			throw new NotFoundException(__('Cliente no presente'));
		}
		
		$this->loadModel('Client');
		$this->loadModel('Quotation');
		
		$quotationConditions=array(
			'Quotation.client_id'=>$clientid,
		);
		//if ($userid>0){
		//	$quotationConditions[]=array('Quotation.user_id'=>$userid);
		//}
		$quotationsForClient=$this->Quotation->find('list',array(
			'fields'=>'Quotation.id',
			'conditions'=>$quotationConditions,
		));
		
		// ADDED 20160415 TO ONLY SHOW SALES ORDERS WITH PRODUCTS READY FOR DELIVERY
		$salesOrderIds=$this->SalesOrder->SalesOrderProduct->find('list',array(
			'fields'=>array('SalesOrderProduct.sales_order_id'),
			// MODIFIED 20160523 TO SHOW ALL SALES ORDERS WHETHER THEY HAVE PRODUCTS READY FOR DELIVERY OR NOT
			//'conditions'=>array(
			//	'SalesOrderProduct.sales_order_product_status_id'=>array(PRODUCT_STATUS_READY_FOR_DELIVERY),
			//),
		));
		
		// $this->InvoiceProduct->virtualFields['total_product_quantity']=0;
		$salesOrdersForClient=$this->SalesOrder->find('all',array(
			'fields'=>array(
				'SalesOrder.id','SalesOrder.sales_order_code',
			),
			'conditions'=>array(
				'SalesOrder.quotation_id'=>$quotationsForClient,
				'SalesOrder.bool_annulled'=>false,
				'SalesOrder.id'=>$salesOrderIds,
			),
			'order'=>'SalesOrder.sales_order_code',
		));
		//pr($quotationsForClient);
		$this->set(compact('salesOrdersForClient'));
	}

	public function getsalesorderproducts() {
		$this->layout = "ajax";
		
		$salesorderid=trim($_POST['salesorderid']);
		$boolIVA=trim($_POST['boolIVA']);
		$this->set(compact('boolIVA'));
		$editpermissiondenied=trim($_POST['editpermissiondenied']);
		$this->set(compact('editpermissiondenied'));
		
		if (!$salesorderid){
			throw new NotFoundException(__('Orden de Venta no presente'));
		}
		
		$this->loadModel('Quotation');
		$this->loadModel('QuotationProduct');
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('Product');
		
		$salesOrder=$this->SalesOrder->read(null,$salesorderid);
		//$quotation=$this->Quotation->read(null,$salesOrder['SalesOrder']['quotation_id']);
		$currency_id=$salesOrder['SalesOrder']['currency_id'];
		$this->set(compact('currency_id'));
		
		// $this->InvoiceProduct->virtualFields['total_product_quantity']=0;

		// MODIFIED 20160415 TO ONLY SHOW PRODUCTS READY FOR DELIVERY
		$productsForSalesOrder=$this->SalesOrderProduct->find('all',array(
			'fields'=>array(
				'SalesOrderProduct.product_id','SalesOrderProduct.product_description',
				'SalesOrderProduct.product_quantity',
				'SalesOrderProduct.product_unit_price','SalesOrderProduct.product_total_price',
				'SalesOrderProduct.currency_id',
			),
			'conditions'=>array(
				'SalesOrderProduct.sales_order_id'=>$salesorderid,
				// MODIFIED 20160524 TO SHOW ALL PRODUCTS
				//'SalesOrderProduct.sales_order_product_status_id'=>array(PRODUCT_STATUS_READY_FOR_DELIVERY)
			),
		));
		//pr($productsForSalesOrder);
		$this->set(compact('productsForSalesOrder'));
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));
	}
	
	public function getsalesorderinfo() {
		$this->layout = "ajax";
		
		$salesorderid=trim($_POST['salesorderid']);
		if (!$salesorderid){
			throw new NotFoundException(__('Orden de Venta no presente'));
		}
		
		// $this->InvoiceProduct->virtualFields['total_product_quantity']=0;
		$salesOrder=$this->SalesOrder->find('first',array(
			'fields'=>array(
				'SalesOrder.sales_order_code','SalesOrder.sales_order_date','SalesOrder.quotation_id',
			),
			'conditions'=>array(
				'SalesOrder.id'=>$salesorderid,
			),
			'contain'=>array(
				'Quotation'=>array(
					'fields'=>array('Quotation.id','Quotation.quotation_code'),
					'Client'=>array(
						'fields'=>array('Client.id','Client.name'),
					),
					'User'=>array(
						'fields'=>array('User.id','User.username'),
					),
				),
			),
		));
		if (!empty($salesOrder)){
			$this->loadModel('Quotation');
			$quotation=$this->Quotation->find('first',array(
				'conditions'=>array(
					'Quotation.id'=>$salesOrder['SalesOrder']['quotation_id'],
				),
			));
			$this->set(compact('quotation'));
		}
		//pr($salesOrder);
		$this->set(compact('salesOrder'));
	}

	public function getsalesordercurrencyid(){
		$this->layout= "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$this->loadModel('User');
		$salesorderid=trim($_POST['salesorderid']);
		if (!$salesorderid){
			throw new NotFoundException(__('Orden de venta no presente'));
		}
		
		$salesOrder=$this->SalesOrder->find('first',array('conditions'=>array('SalesOrder.id'=>$salesorderid)));
		if (!empty($salesOrder)){
			return $salesOrder['SalesOrder']['currency_id'];
		}
		return CURRENCY_CS;
	}
	
	public function verReporteOrdenesDeVentaPorEstado(){
		$sales_order_product_status_id=0;
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			//pr($startDateArray);
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$sales_order_product_status_id=$this->request->data['Report']['sales_order_product_status_id'];
		}
		if (!isset($startDate)){
			$startDate=date("Y-m-d", strtotime(date("Y-m-01")));
		}
		if (!isset($endDate)){
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$this->set(compact('startDate','endDate','sales_order_product_status_id'));
		
		$this->loadModel('SalesOrderProductStatus');
		$salesOrderProductStatusConditions=array();
		$this->SalesOrderProductStatus->recursive=-1;
		
		if ($sales_order_product_status_id>0){
			$salesOrderProductStatusConditions['SalesOrderProductStatus.id']=$sales_order_product_status_id;
		}
		
		$selectedStatuses=array();
		if ($sales_order_product_status_id!=-1){
			$selectedStatuses=$this->SalesOrderProductStatus->find('all',array(
				'fields'=>array('SalesOrderProductStatus.id','SalesOrderProductStatus.status'),
				'conditions'=>$salesOrderProductStatusConditions,
			));
		}
		//pr($selectedUsers);
		$this->SalesOrder->recursive=-1;
		$salesOrders=$this->SalesOrder->find('all',array(
			'conditions'=>array(
				'SalesOrder.sales_order_date >='=>$startDate,
				'SalesOrder.sales_order_date <'=>$endDatePlusOne,
				'SalesOrder.bool_annulled'=>false,
			),
			'contain'=>array(
				'SalesOrderProduct'=>array(
					'fields'=>array(
						'SalesOrderProduct.id','SalesOrderProduct.sales_order_product_status_id'
					),
					'order'=>'SalesOrderProduct.sales_order_product_status_id DESC',
				),
				'Quotation'=>array(
					'Client',
					'Contact',
					'Currency',
				),
			),
		));
		if (!empty($salesOrders)){
			for ($so=0;$so<count($salesOrders);$so++){
				// UPDATED 20160415 WITH NEW PRODUCT STATUSES
				/*	
				$productStatus=PRODUCT_STATUS_READY_FOR_DELIVERY;
				foreach ($salesOrders[$so]['SalesOrderProduct'] as $product){
					switch ($product['sales_order_product_status_id']){
						case PRODUCT_STATUS_IN_PRODUCTION:
							$productStatus=PRODUCT_STATUS_IN_PRODUCTION;
							break;
						case PRODUCT_STATUS_REGISTERED:
							$productStatus=PRODUCT_STATUS_REGISTERED;
							break;
					}
				}
				*/
				$productStatus=PRODUCT_STATUS_COMPLETELY_DELIVERED;
				foreach ($salesOrders[$so]['SalesOrderProduct'] as $product){
					switch ($product['sales_order_product_status_id']){
						case PRODUCT_STATUS_READY_FOR_DELIVERY:
							$productStatus=PRODUCT_STATUS_READY_FOR_DELIVERY;
							break;
						case PRODUCT_STATUS_IN_PRODUCTION:
							$productStatus=PRODUCT_STATUS_IN_PRODUCTION;
							break;
						case PRODUCT_STATUS_REGISTERED:
							$productStatus=PRODUCT_STATUS_REGISTERED;
							break;
					}
				}
				$salesOrders[$so]['SalesOrder']['order_status']=$productStatus;
			}
		}
		//pr($salesOrders);
		for ($s=0;$s<count($selectedStatuses);$s++){
			$salesOrdersOfStatus=array();
			foreach ($salesOrders as $salesOrder){
				if ($selectedStatuses[$s]['SalesOrderProductStatus']['id']==$salesOrder['SalesOrder']['order_status']){
					$salesOrdersOfStatus[]=$salesOrder;
				}
			}
			$selectedStatuses[$s]['SalesOrders']=$salesOrdersOfStatus;			
		}
		//pr($selectedStatuses);
		
		$salesOrderProductStatuses=$this->SalesOrderProductStatus->find('list');
		$salesOrderProductStatuses=array('-1'=>'Anulada')+$salesOrderProductStatuses;
		$this->set(compact('salesOrderProductStatuses','selectedStatuses'));	
		
		$annulledSalesOrders=array();
		if ($sales_order_product_status_id<=0){
			$annulledSalesOrders=$this->SalesOrder->find('all',array(
				'conditions'=>array(
					'SalesOrder.sales_order_date >='=>$startDate,
					'SalesOrder.sales_order_date <'=>$endDatePlusOne,
					'SalesOrder.bool_annulled'=>true,
				),
				'contain'=>array(
					'Quotation'=>array(
						'Client',
						'Contact',
						'Currency',
					),
				),
			));
		}
		$this->set(compact('annulledSalesOrders'));
	}
	
	public function guardarReporteOrdenesDeVentaPorEstado() {
		$exportData=$_SESSION['reporteCotizacionesPorEjecutivo'];
		$this->set(compact('exportData'));
	}
}
