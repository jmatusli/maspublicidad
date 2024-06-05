<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
/**
 * Quotations Controller
 *
 * @property Quotation $Quotation
 * @property PaginatorComponent $Paginator
 */
class QuotationsController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('getquotationproducts','getquotationinfo','getnewquotationcode','getquotationcurrencyid','getquotationsforclient','generarOrdenDeVenta');		
	}

	public function index() {
		$this->Quotation->recursive = -1;
		
		$currencyId=CURRENCY_USD;
		$rejectedDisplay=0;
		$salesOrderDisplay=0;
		$userId=$this->Auth->User('id');
		
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$userId=$this->request->data['Report']['user_id'];
			$currencyId=$this->request->data['Report']['currency_id'];
			
			$rejectedDisplay=$this->request->data['Report']['rejected_display'];
			$salesOrderDisplay=$this->request->data['Report']['sales_order_display'];
		}		
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
			if ($this->Session->check('userId')){
				$userId=$_SESSION['userId'];
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
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('currencyId','userId'));
		$this->set(compact('rejectedDisplay','salesOrderDisplay'));
		
		// 20151109 user selection added
		$conditions=array(
			'Quotation.quotation_date >='=>$startDate,
			'Quotation.quotation_date <'=>$endDatePlusOne,
		);
		$userrole = $this->Auth->User('role_id');
		if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { 
			$conditions[]=array('Quotation.user_id'=>$this->Auth->User('id'));
		}
		else {
			if ($userId>0){
				$conditions[]=array('Quotation.user_id'=>$userId);
			}
		}
		switch ($rejectedDisplay){
			case 0:
				break;
			case 1:
				$conditions=array('Quotation.bool_rejected'=>true);
				break;
			case 2:
				$conditions=array('Quotation.bool_rejected'=>false);
				break;	
		}
		
		
		$quotationCount=$this->Quotation->find('count', array(
			'fields'=>array('Quotation.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'fields'=>array(
				'Quotation.id',
				'Quotation.quotation_date',
				'Quotation.due_date',
				'Quotation.quotation_code',
				'Quotation.price_subtotal','Quotation.price_iva','Quotation.price_total',
				'Quotation.bool_iva',
			),
			'conditions' => $conditions,
			'contain'=>array(				
				'Client'=>array(
					'fields'=>array('Client.id','Client.name',),
				),
				'Contact'=>array(
					'fields'=>array('Contact.id','Contact.first_name','Contact.last_name'),
				),
				'Currency'=>array(
					'fields'=>array('Currency.id','Currency.abbreviation',),
				),
				'SalesOrder'=>array(
					'fields'=>'SalesOrder.id',
					'conditions'=>array(
						'SalesOrder.bool_annulled'=>false,
					),
				),
				'User'=>array(
					'fields'=>array(
						'User.id','User.username',
						'User.first_name','User.last_name',
					),
				),
			),
			'order'=>'Quotation.quotation_date DESC, Quotation.quotation_code DESC',
			'limit'=>($quotationCount!=0?$quotationCount:1),
		);
		$quotations = $this->Paginator->paginate('Quotation');
		
		//pr($quotations);
		if (!empty($quotations)){
			$this->loadModel('ExchangeRate');
			for ($i=0;$i<count($quotations);$i++){
				// set the exchange rate
				$quotationDate=$quotations[$i]['Quotation']['quotation_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
				$quotations[$i]['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				
				if (!empty($quotations[$i]['SalesOrder'])){
					$quotations[$i]['Quotation']['bool_sales_order_present']=true;
				}
				else {
					$quotations[$i]['Quotation']['bool_sales_order_present']=false;
				}
			}
		}
		
		$this->set(compact('quotations'));
		
		// pending quotations from previous months
		$this->loadModel('SalesOrder');
		$quotationIdsWithSalesOrder=$this->SalesOrder->find('list',array(
			'fields'=>array('SalesOrder.quotation_id'),
			'conditions'=>array(
				'SalesOrder.bool_annulled'=>false,
			),
		));
		
		$conditions=array(
			'Quotation.quotation_date <'=>$startDate,
			'Quotation.bool_rejected'=>false,
			'Quotation.id !='=>$quotationIdsWithSalesOrder,
		);
		$userrole = $this->Auth->User('role_id');
		if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { 
			$conditions[]=array('Quotation.user_id'=>$this->Auth->User('id'));
		}
		else {
			if ($userId>0){
				$conditions[]=array('Quotation.user_id'=>$userId);
			}
		}
		$pendingQuotationCount=$this->Quotation->find('count', array(
			'fields'=>array('Quotation.id'),
			'conditions' => $conditions,
		));
		//echo "pending quotation count is ".$pendingQuotationCount."<br/>";
		
		$this->Paginator->settings = array(
			'fields'=>array(
				'Quotation.id',
				'Quotation.quotation_date',
				'Quotation.due_date',
				'Quotation.quotation_code',
				'Quotation.price_subtotal','Quotation.price_iva','Quotation.price_total',
				'Quotation.bool_iva',
				
			),
			'conditions' => $conditions,
			'contain'=>array(				
				'Client'=>array(
					'fields'=>array('Client.id','Client.name',),
				),
				'Contact'=>array(
					'fields'=>array('Contact.id','Contact.first_name','Contact.last_name'),
				),
				'Currency'=>array(
					'fields'=>array('Currency.id','Currency.abbreviation',),
				),
				'SalesOrder'=>array(
					'fields'=>'SalesOrder.id',
					'conditions'=>array(
						'SalesOrder.bool_annulled'=>false,
					),
				),
				'User'=>array(
					'fields'=>array(
						'User.id','User.username',
						'User.first_name','User.last_name',
					),
				),
			),
			'order'=>'Quotation.quotation_date DESC, Quotation.quotation_code DESC',
			'limit'=>($pendingQuotationCount!=0?$pendingQuotationCount:1),
		);
		$pendingQuotations = $this->Paginator->paginate('Quotation');
		
		if (!empty($pendingQuotations)){
			$this->loadModel('ExchangeRate');
			for ($i=0;$i<count($pendingQuotations);$i++){
				// set the exchange rate
				$quotationDate=$pendingQuotations[$i]['Quotation']['quotation_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
				$pendingQuotations[$i]['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				
				if (!empty($pendingQuotations[$i]['SalesOrder'])){
					$pendingQuotations[$i]['Quotation']['bool_sales_order_present']=true;
				}
				else {
					$pendingQuotations[$i]['Quotation']['bool_sales_order_present']=false;
				}
			}
		}
		
		//pr($pendingQuotations);
		$this->set(compact('pendingQuotations'));
		
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
		
		$salesOrderOptions=array();
		$salesOrderOptions[0]="Mostrar todas cotizaciones (con y sin orden de venta)";
		$salesOrderOptions[1]="Mostrar solamente cotizaciones con ordenes de venta";
		$salesOrderOptions[2]="Mostrar solamente cotizaciones sin ordenes de venta";
		$this->set(compact('salesOrderOptions'));
		
		$rejectedOptions=array();
		$rejectedOptions[0]="Mostrar todas cotizaciones (caídas y vigentes)";
		$rejectedOptions[1]="Mostrar solamente cotizaciones caídas";
		$rejectedOptions[2]="Mostrar solamente cotizaciones vigentes";
		$this->set(compact('rejectedOptions'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
		$aco_name="SalesOrders/index";		
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
		
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->Quotation->exists($id)) {
			throw new NotFoundException(__('Invalid quotation'));
		}

		$options = array(
			'conditions' => array(
				'Quotation.' . $this->Quotation->primaryKey => $id,
			),
			'contain'=>array(
				'Client',
				'Contact',
				'Currency',
				'Invoice'=>array(
					//'Product',
				),
				'QuotationProduct'=>array(
					'Product',
				),
				'QuotationRemark'=>array(
					'User',
				),
				'SalesOrder'=>array(
					//'Product',
				),
				'User',
			),
		);
		$quotation=$this->Quotation->find('first', $options);
		$this->set(compact('quotation'));
		
		if (!empty($quotation['SalesOrder'])){
			//pr($quotation['SalesOrder']);
			if ($quotation['SalesOrder'][0]['bool_authorized']){
				$bool_edit_permission=false;
			}
		}
		$this->set(compact('bool_edit_permission'));
		
		$filename=$quotation['Client']['name'].'_'.$quotation['Quotation']['quotation_code'];
		$this->set(compact('filename'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
		$aco_name="SalesOrders/index";		
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
	}

	public function viewPdf($id = null) {
		if (!$this->Quotation->exists($id)) {
			throw new NotFoundException(__('Invalid quotation'));
		}

		$options = array(
			'conditions' => array(
				'Quotation.' . $this->Quotation->primaryKey => $id,
			),
			'contain'=>array(
				'Client',
				'Contact',
				'Currency',
				'User',
				'QuotationProduct'=>array(
					'Product',
				),
				'SalesOrder'=>array(
					//'Product',
				),
				'Invoice'=>array(
					//'Product',
				),
				
			),
		);
		
		$quotation=$this->Quotation->find('first', $options);
		$this->set(compact('quotation'));
		
		$dueDate= new DateTime($quotation['Quotation']['due_date']);
		$quotationDate= new DateTime($quotation['Quotation']['quotation_date']);
		$daysValid=$quotationDate->diff($dueDate);
		$validityQuotation=(int)$daysValid->format("%r%a");
		$this->set(compact('validityQuotation'));
		
		$filename=$quotation['Client']['name'].'_'.$quotation['Quotation']['quotation_code'];
		$this->set(compact('filename'));
	}
	
	public function add() {
		$this->loadModel('Product');
		$this->loadModel('QuotationProduct');
		$this->loadModel('QuotationRemark');
		
		if ($this->request->is('post')) {
			$quotationDateArray=$this->request->data['Quotation']['quotation_date'];
			//pr($quotationDateArray);
			$quotationDateString=$quotationDateArray['year'].'-'.$quotationDateArray['month'].'-'.$quotationDateArray['day'];
			$quotationDate=date( "Y-m-d", strtotime($quotationDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['QuotationProduct'] as $quotationProduct){
				if ($quotationProduct['product_id']>0){
					$multiplicationDifference=abs($quotationProduct['product_total_price']-$quotationProduct['product_quantity']*$quotationProduct['product_unit_price']);
					//pr($quotationProduct);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			if ($quotationDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de cotización no puede estar en el futuro!  No se guardó la cotización.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			else {				
				$this->loadModel('Client');
				$this->loadModel('Contact');
				$datasource=$this->Quotation->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$client_id=0;
					if (!empty($this->request->data['Quotation']['client_id'])){
						$client_id=$this->request->data['Quotation']['client_id'];
					}
					if ($this->request->data['Client']['bool_new']){
						$this->Client->create();
						if (!$this->Client->save($this->request->data)) {
							echo "Problema guardando el cliente";
							pr($this->validateErrors($this->Client));
							throw new Exception();
						}
						$client_id=$this->Client->id;
						$this->request->data['Quotation']['client_id']=$client_id;
					}
					$contact_id=0;
					if (!empty($this->request->data['Quotation']['contact_id'])){
						$contact_id=$this->request->data['Quotation']['contact_id'];
					}
					if ($this->request->data['Contact']['bool_new']&&!empty($this->request->data['Contact']['first_name'])&&!empty($this->request->data['Contact']['last_name'])){
						$this->Contact->create();
						$this->request->data['Contact']['client_id']=$client_id;
						if (!$this->Contact->save($this->request->data)) {
							echo "Problema guardando el contacto";
							pr($this->validateErrors($this->Contact));
							throw new Exception();
						}
						$contact_id=$this->Contact->id;
						$this->request->data['Quotation']['contact_id']=$contact_id;
					}
					$this->Quotation->create();
					//pr($this->request->data);
					if (!$this->Quotation->save($this->request->data)) {
						echo "Problema guardando la cotización";
						pr($this->validateErrors($this->Quotation));
						throw new Exception();
					}
					$quotation_id=$this->Quotation->id;
					
					foreach ($this->request->data['QuotationProduct'] as $quotationProduct){
						if ($quotationProduct['product_id']>0){
							//pr($quotationProduct);
							$productArray=array();
							$productArray['QuotationProduct']['quotation_id']=$quotation_id;
							$productArray['QuotationProduct']['product_id']=$quotationProduct['product_id'];
							$productArray['QuotationProduct']['product_description']=$quotationProduct['product_description'];
							$productArray['QuotationProduct']['delivery_time']=$quotationProduct['delivery_time'];
							$productArray['QuotationProduct']['product_quantity']=$quotationProduct['product_quantity'];
							$productArray['QuotationProduct']['product_unit_price']=$quotationProduct['product_unit_price'];
							$productArray['QuotationProduct']['product_total_price']=$quotationProduct['product_total_price'];
							$productArray['QuotationProduct']['currency_id']=$this->request->data['Quotation']['currency_id'];
							$this->QuotationProduct->create();
							if (!$this->QuotationProduct->save($productArray)) {
								echo "Problema guardando los productos de la cotización";
								pr($this->validateErrors($this->QuotationProduct));
								throw new Exception();
							}
						}
					}
					
					if (!empty($this->request->data['QuotationRemark']['remark_text'])){
						$quotationRemark=$this->request->data['QuotationRemark'];
						//pr($quotationRemark);
						$quotationRemarkArray=array();
						$quotationRemarkArray['QuotationRemark']['user_id']=$quotationRemark['user_id'];
						$quotationRemarkArray['QuotationRemark']['quotation_id']=$quotation_id;
						$quotationRemarkArray['QuotationRemark']['remark_datetime']=$quotationRemark['remark_datetime'];
						$quotationRemarkArray['QuotationRemark']['remark_text']=$quotationRemark['remark_text'];
						$this->QuotationRemark->create();
						if (!$this->QuotationRemark->save($quotationRemarkArray)) {
							echo "Problema guardando los productos de la cotización";
							pr($this->validateErrors($this->QuotationRemark));
							throw new Exception();
						}
					}
					
					$datasource->commit();
					$this->recordUserAction($this->Quotation->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la cotización número ".$this->request->data['Quotation']['quotation_code']);
					
					$this->Session->setFlash(__('The quotation has been saved.'),'default',array('class' => 'success'));				
					return $this->redirect(array('action' => 'index'));
				}
				catch(Exception $e){
					$datasource->rollback();
					// pr($e);
					$this->Session->setFlash(__('No se podía guardar la cotización..'), 'default',array('class' => 'error-message'));
				}
			}
		}
		
		$users = $this->Quotation->User->find('list',array(
			'conditions'=>array(
				'User.id'=>$this->Auth->User('id'),
			),
		));
		$clients = $this->Quotation->Client->find('list',array('order'=>'Client.name'));
		$contacts = $this->Quotation->Contact->find('list',array('order'=>'Contact.first_name'));
		$currencies = $this->Quotation->Currency->find('list');
		$this->set(compact('users', 'clients', 'contacts', 'currencies'));
		
		$products=$this->Product->find('list',array(
			'order'=>'Product.name',
		));
		$this->set(compact('products'));
		
		$this->loadModel('Provider');
		$this->Provider->recursive=-1;
		$providers = $this->Provider->find('all',array(
			'fields'=>array('Provider.id','Provider.name'),
			'conditions'=>array(
				'Provider.bool_active'=>true,
			),
			'order'=>'Provider.name',
		));
		$departments = $this->Product->Department->find('list');
		$this->set(compact('departments', 'providers'));
		
		$quotationDate=date( "Y-m-d");
		$quotationExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
		$exchangeRateQuotation=$quotationExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateQuotation'));
	}

	public function edit($id = null) {
		$this->loadModel('Product');
		$this->loadModel('QuotationProduct');
		$this->loadModel('QuotationRemark');
		
		if (!$this->Quotation->exists($id)) {
			throw new NotFoundException(__('Invalid quotation'));
		}
		if ($this->request->is(array('post', 'put'))) {
			$quotationDateArray=$this->request->data['Quotation']['quotation_date'];
			//pr($quotationDateArray);
			$quotationDateString=$quotationDateArray['year'].'-'.$quotationDateArray['month'].'-'.$quotationDateArray['day'];
			$quotationDate=date( "Y-m-d", strtotime($quotationDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['QuotationProduct'] as $quotationProduct){
				if ($quotationProduct['product_id']>0){
					$multiplicationDifference=abs($quotationProduct['product_total_price']-$quotationProduct['product_quantity']*$quotationProduct['product_unit_price']);
					//pr($quotationProduct);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			
			if ($quotationDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de cotización no puede estar en el futuro!  No se guardó la cotización.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			else {		
				$datasource=$this->Quotation->getDataSource();
				$datasource->begin();
				try {				
					//pr($this->request->data);
					$this->QuotationProduct->recursive=-1;
					$previousQuotationProducts=$this->QuotationProduct->find('all',array(
						'fields'=>array('QuotationProduct.id'),
						'conditions'=>array(
							'QuotationProduct.quotation_id'=>$id,
						),
					));
					if (!empty($previousQuotationProducts)){
						foreach ($previousQuotationProducts as $previousQuotationProduct){
							$this->QuotationProduct->id=$previousQuotationProduct['QuotationProduct']['id'];
							$this->QuotationProduct->delete($previousQuotationProduct['QuotationProduct']['id']);
						}
					}
					
					if (!$this->Quotation->save($this->request->data)) {
						echo "Problema guardando la cotización";
						pr($this->validateErrors($this->Quotation));
						throw new Exception();
					}
					$quotation_id=$this->Quotation->id;
					
					foreach ($this->request->data['QuotationProduct'] as $quotationProduct){
						if ($quotationProduct['product_id']>0){
							//pr($quotationProduct);
							$productArray=array();
							$productArray['QuotationProduct']['quotation_id']=$quotation_id;
							$productArray['QuotationProduct']['product_id']=$quotationProduct['product_id'];
							$productArray['QuotationProduct']['product_description']=$quotationProduct['product_description'];
							$productArray['QuotationProduct']['delivery_time']=$quotationProduct['delivery_time'];
							$productArray['QuotationProduct']['product_quantity']=$quotationProduct['product_quantity'];
							$productArray['QuotationProduct']['product_unit_price']=$quotationProduct['product_unit_price'];
							$productArray['QuotationProduct']['product_total_price']=$quotationProduct['product_total_price'];
							$productArray['QuotationProduct']['currency_id']=$this->request->data['Quotation']['currency_id'];
							$this->QuotationProduct->create();
							if (!$this->QuotationProduct->save($productArray)) {
								echo "Problema guardando los productos de la cotización";
								pr($this->validateErrors($this->QuotationProduct));
								throw new Exception();
							}
						}
					}
					
					if (!empty($this->request->data['QuotationRemark']['remark_text'])){
						$quotationRemark=$this->request->data['QuotationRemark'];
						//pr($quotationRemark);
						$quotationRemarkArray=array();
						$quotationRemarkArray['QuotationRemark']['user_id']=$quotationRemark['user_id'];
						$quotationRemarkArray['QuotationRemark']['quotation_id']=$quotation_id;
						$quotationRemarkArray['QuotationRemark']['remark_datetime']=$quotationRemark['remark_datetime'];
						$quotationRemarkArray['QuotationRemark']['remark_text']=$quotationRemark['remark_text'];
						$this->QuotationRemark->create();
						if (!$this->QuotationRemark->save($quotationRemarkArray)) {
							echo "Problema guardando los productos de la cotización";
							pr($this->validateErrors($this->QuotationRemark));
							throw new Exception();
						}
					}
					
					$datasource->commit();
					$this->recordUserAction($this->Quotation->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la cotización número ".$this->request->data['Quotation']['quotation_code']);
					
					$this->Session->setFlash(__('The quotation has been saved.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					//pr($e);
					$this->Session->setFlash(__('No se podía guardar la cotización..'), 'default',array('class' => 'error-message'));
				}
			}
		} 
		else {
			$options = array(
				'conditions' => array(
					'Quotation.id' => $id,
				),
				'contain'=>array(
					'QuotationProduct',
				),
			);
			$this->request->data = $this->Quotation->find('first', $options);
		}
		
		$this->loadModel('QuotationRemark');
		$this->QuotationRemark->recursive=-1;
		$quotationRemarks=$this->QuotationRemark->find('all',array(
			'conditions'=>array(
				'QuotationRemark.quotation_id'=>$id,
			),
			'contain'=>array(
				'User'
			)
		));
		$this->set(compact('quotationRemarks'));
		
		$userrole = $this->Auth->User('role_id');
		$boolChangeDueDate=false;
		if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { 
			$users = $this->Quotation->User->find('list',array(
				'conditions'=>array(
					'User.id'=>$this->Auth->User('id'),
				),
			));
		}
		else {
			$users = $this->Quotation->User->find('list');
			$boolChangeDueDate=true;
		}
		$this->set(compact('boolChangeDueDate'));
		$clients = $this->Quotation->Client->find('list',array('order'=>'Client.name'));
		$contacts = $this->Quotation->Contact->find('list',array('order'=>'Contact.first_name'));
		$currencies = $this->Quotation->Currency->find('list');
		$this->set(compact('users', 'clients', 'contacts', 'currencies'));
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		//$quotationDate=$this->request->data['Quotation']['quotation_date'];
		//$quotationDateTime= new DateTime($quotationDate);
		$quotationDateAsString=$this->Quotation->deconstruct('quotation_date',$this->request->data['Quotation']['quotation_date']);
		
		$quotationExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDateAsString);
		$exchangeRateQuotation=$quotationExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateQuotation'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Quotation->id = $id;
		if (!$this->Quotation->exists()) {
			throw new NotFoundException(__('Invalid quotation'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$this->Quotation->recursive=-1;
		$quotation=$this->Quotation->find('first',array(
			'conditions'=>array(
				'Quotation.id'=>$id,
			),
			'contain'=>array(
				'QuotationProduct',
				'SalesOrder'=>array(
					'conditions'=>array(
						'SalesOrder.bool_annulled'=>false,
					),
				),
			)
		));
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (count($quotation['SalesOrder'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Esta cotización tiene ordenes de venta correspondientes.  Para poder eliminar la cotización, primero hay que eliminar o modificar las ordenes de venta ";
			if (count($quotation['SalesOrder'])==1){
				$flashMessage.=$quotation['SalesOrder'][0]['sales_order_code'].".";
			}
			else {
				for ($i=0;$i<count($quotation['SalesOrder']);$i++){
					$flashMessage.=$quotation['SalesOrder'][$i]['sales_order_code'];
					if ($i==count($quotation['SalesOrder'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó la cotización.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			try {
				$datasource=$this->Quotation->getDataSource();
				$datasource->begin();	
				//delete all stockMovements, stockItems and stockItemLogs
				foreach ($quotation['QuotationProduct'] as $quotationProduct){
					if (!$this->Quotation->QuotationProduct->delete($quotationProduct['id'])) {
						echo "Problema al eliminar el producto de la cotización";
						pr($this->validateErrors($this->Quotation->QuotationProduct));
						throw new Exception();
					}
				}
				
				if (!$this->Quotation->delete($id)) {
					echo "Problema al eliminar la cotización";
					pr($this->validateErrors($this->Quotation));
					throw new Exception();
				}
						
				$datasource->commit();
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó la cotización número ".$quotation['Quotation']['quotation_code']);
						
				$this->Session->setFlash(__('Se eliminó la cotización.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía eliminar la cotización.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
		
		
		if ($this->Quotation->delete()) {
			$this->Session->setFlash(__('The quotation has been deleted.'));
		} else {
			$this->Session->setFlash(__('The quotation could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function getquotationproducts() {
		$this->layout = "ajax";
		
		$quotationid=trim($_POST['quotationid']);
		$editpermissiondenied=trim($_POST['editpermissiondenied']);
		$this->set(compact('editpermissiondenied'));
		
		if (!$quotationid){
			throw new NotFoundException(__('Cotizacion no presente'));
		}
		
		$this->loadModel('QuotationProduct');
		$this->loadModel('Product');
		$this->loadModel('SalesOrderProductStatus');
		$quotationProductConditions=array(
			'QuotationProduct.quotation_id'=>$quotationid,
		);
		
		
		// $this->InvoiceProduct->virtualFields['total_product_quantity']=0;
		$productsForQuotation=$this->QuotationProduct->find('all',array(
			'fields'=>array(
				'QuotationProduct.product_id','QuotationProduct.product_quantity','QuotationProduct.product_description',
				'QuotationProduct.product_unit_price','QuotationProduct.product_total_price','QuotationProduct.currency_id',
			),
			'conditions'=>$quotationProductConditions,
		));
		//pr($productsForQuotation);
		$this->set(compact('productsForQuotation'));
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		$salesOrderProductStatuses=$this->SalesOrderProductStatus->find('list');
		$this->set(compact('salesOrderProductStatuses'));
	}
	
	public function getquotationinfo() {
		$this->layout = "ajax";
		
		$quotationid=trim($_POST['quotationid']);
		if (!$quotationid){
			throw new NotFoundException(__('Cotizacion no presente'));
		}
		
		// $this->InvoiceProduct->virtualFields['total_product_quantity']=0;
		$quotation=$this->Quotation->find('first',array(
			'fields'=>array(
				'Quotation.quotation_code','Quotation.quotation_date',
			),
			'conditions'=>array(
				'Quotation.id'=>$quotationid,
			),
			'contain'=>array(
				'Client'=>array(
					'fields'=>array('Client.id','Client.name'),
				),
				'User'=>array(
					'fields'=>array('User.id','User.username'),
				),
			),
			
		));
		//pr($quotation);
		$this->set(compact('quotation'));
	}

	public function getnewquotationcode(){
		$this->layout= "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$this->loadModel('User');
		$userid=trim($_POST['userid']);
		$quotationdateday=trim($_POST['quotationdateday']);
		$quotationdatemonth=trim($_POST['quotationdatemonth']);
		$quotationdateyear=trim($_POST['quotationdateyear']);
		if (!$userid){
			throw new NotFoundException(__('Usuario no presente'));
		}
		$selectedUser=$this->User->find('first',array(
			'conditions'=>array('User.id'=>$userid,),
		));
		$userAbbreviation=$selectedUser['User']['abbreviation'];
		
		$quotationDateString=$quotationdateyear.'-'.$quotationdatemonth.'-'.$quotationdateday;
		$quotationDate=date( "Y-m-d", strtotime($quotationDateString));
		$firstDayString=$quotationdateyear.'-'.$quotationdatemonth.'-01';
		$dateFirstDayMonth=date('Y-m-d', strtotime($firstDayString));
		// 20160411 COMMENTED OUT
		/*
		$quotationCount=$this->Quotation->find('count',array(
			'conditions'=>array(
				'Quotation.user_id'=>$userid,
				'Quotation.quotation_date >='=>$dateFirstDayMonth,
				'Quotation.quotation_date <='=>$quotationDate,
			),
		));
		*/
		$dateFirstDayNextMonth=date('Y-m-d',strtotime($firstDayString."+1 months"));
		$quotationCount=$this->Quotation->find('count',array(
			'conditions'=>array(
				'Quotation.quotation_code LIKE'=>$userAbbreviation."%",
				'Quotation.quotation_date >='=>$dateFirstDayMonth,
				'Quotation.quotation_date <'=>$dateFirstDayNextMonth,
			),
		));
		$fullQuotationCount=$this->Quotation->find('count',array(
			'conditions'=>array(
				'Quotation.quotation_code LIKE'=>$userAbbreviation."%",
			),
		));
		
		$newQuotationCode=$userAbbreviation."_".($fullQuotationCount+1)."_".($quotationCount+1)."_".$quotationdateday.$quotationdatemonth.substr($quotationdateyear,2,2);
		return $newQuotationCode;
	}
	
	public function getquotationcurrencyid(){
		$this->layout= "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$this->loadModel('User');
		$quotationid=trim($_POST['quotationid']);
		if (!$quotationid){
			throw new NotFoundException(__('Cotización no presente'));
		}
		
		$quotation=$this->Quotation->find('first',array('conditions'=>array('Quotation.id'=>$quotationid)));
		if (!empty($quotation)){
			return $quotation['Quotation']['currency_id'];
		}
		return CURRENCY_CS;
	}
	
	public function getquotationsforclient() {
		$this->layout = "ajax";
		
		$clientid=trim($_POST['clientid']);
		$userid=trim($_POST['userid']);
		if (!$clientid){
			throw new NotFoundException(__('Cliente no presente'));
		}
		
		$this->loadModel('Client');
		$quotationConditions=array(
			'Quotation.client_id'=>$clientid,
		);
		if ($userid>0){
			$quotationConditions[]=array('Quotation.user_id'=>$userid,);
		}
		
		// $this->InvoiceProduct->virtualFields['total_product_quantity']=0;
		$quotationsForClient=$this->Quotation->find('all',array(
			'fields'=>array(
				'Quotation.id','Quotation.quotation_code',
			),
			'conditions'=>$quotationConditions,
		));
		//pr($quotationsForClient);
		$this->set(compact('quotationsForClient'));
	}

	public function verReporteCotizacionesPorEjecutivo(){
		$user_id=0;
		$currencyId=CURRENCY_USD;
		$userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole'));
		if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { 
			$user_id=$this->Auth->User('id');
		}
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			//pr($startDateArray);
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$currencyId=$this->request->data['Report']['currency_id'];
			
			$user_id=$this->request->data['Report']['user_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
			if (!empty($_SESSION['userId'])){
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
		
		$this->set(compact('startDate','endDate','currencyId','user_id'));
		$this->loadModel('User');
		$userConditions=array();
		$this->User->recursive=-1;
		if ($user_id>0){
			$userConditions['User.id']=$user_id;
		}
		
		$selectedUsers=$this->User->find('all',array(
			'fields'=>array('User.id','User.username'),
			'conditions'=>$userConditions,
		));
		//pr($selectedUsers);
		$this->Quotation->recursive=-1;
		for ($u=0;$u<count($selectedUsers);$u++){
			$quotations=$this->Quotation->find('all',array(
				'conditions'=>array(
					'Quotation.quotation_date >='=>$startDate,
					'Quotation.quotation_date <'=>$endDatePlusOne,
					'Quotation.user_id'=>$selectedUsers[$u]['User']['id'],
				),
				'contain'=>array(
					'Client',
					'Contact',
					'Currency',
					'SalesOrder'=>array(
						'fields'=>'SalesOrder.id',
						'conditions'=>array(
							'SalesOrder.bool_annulled'=>false,
						),
					),
				),
				'order'=>'Quotation.quotation_date,Quotation.quotation_code',
			));
			if (!empty($quotations)){
				$this->loadModel('ExchangeRate');
				for ($q=0;$q<count($quotations);$q++){
					$quotationDate=$quotations[$q]['Quotation']['quotation_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
					$quotations[$q]['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					
					$quotations[$q]['Quotation']['dropped']=$this->Quotation->getDroppedPercentageForQuotation($quotations[$q]['Quotation']['id']);
					$quotations[$q]['Quotation']['sold']=$this->Quotation->getSoldPercentageForQuotation($quotations[$q]['Quotation']['id']);
					
					if (!empty($quotations[$q]['SalesOrder'])){
						$quotations[$q]['Quotation']['bool_sales_order_present']=true;
					}
					else {
						$quotations[$q]['Quotation']['bool_sales_order_present']=false;
					}
					
				}
			}
			$selectedUsers[$u]['Quotations']=$quotations;
		}
		
		$users=$this->User->find('list',array(
			'conditions'=>array(
				//'User.role_id'=>ROLE_EXECUTIVE,
			)
		));
		
		$this->set(compact('users','selectedUsers'));	
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}

	public function guardarReporteCotizacionesPorEjecutivo() {
		$exportData=$_SESSION['reporteCotizacionesPorEjecutivo'];
		$this->set(compact('exportData'));
	}

	public function verReporteCotizacionesPorDepartamento(){
		$currencyId=CURRENCY_USD;
		$department_id=0;
		$product_id=0;
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$currencyId=$this->request->data['Report']['currency_id'];
			$department_id=$this->request->data['Report']['department_id'];
			$product_id=$this->request->data['Report']['product_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
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
		
		$this->set(compact('startDate','endDate','currencyId','department_id','product_id'));
		
		$this->loadModel('Product');
		$this->loadModel('QuotationProduct');
		$this->loadModel('Department');
		
		$departmentConditions=array();
		$this->Department->recursive=-1;
		if ($department_id>0){
			$departmentConditions['Department.id']=$department_id;
		}
		$selectedDepartments=$this->Department->find('all',array(
			'fields'=>array('Department.id','Department.name'),
			'conditions'=>$departmentConditions,
		));
		//pr($selectedDepartments);
		
		$productConditions=array();
		$this->Product->recursive=-1;
		if ($product_id>0){
			$productConditions['Product.id']=$product_id;
		}
		$selectedProducts=$this->Product->find('all',array(
			'fields'=>array('Product.id','Product.name'),
			'conditions'=>$productConditions,
		));
		//pr($selectedProducts);
		$this->set(compact('selectedProducts'));	
		$selectedProductList=$this->Product->find('list',array(
			'fields'=>array('Product.id'),
			'conditions'=>$productConditions,
		));
		//pr($selectedProductList);
		
		$conditions=array(
			'Quotation.quotation_date >='=>$startDate,
			'Quotation.quotation_date <'=>$endDatePlusOne,
		);
		$userrole = $this->Auth->User('role_id');
		if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { 
			$conditions[]=array('Quotation.user_id'=>$this->Auth->User('id'));
		}
		
		$this->Quotation->recursive=-1;
		$quotationsInPeriod=$this->Quotation->find('list',array(
			'fields'=>'Quotation.id',
			'conditions'=>$conditions,
		));
		for ($pc=0;$pc<count($selectedDepartments);$pc++){
			// get the product ids in this department and the selectedProductList
			$productsInDepartment=$this->Product->find('list',array(
				'fields'=>'Product.id',
				'conditions'=>array(
					'Product.department_id'=>$selectedDepartments[$pc]['Department']['id'],
					'Product.id'=>$selectedProductList,
				)
			));
			//pr($productsInDepartment);
			// get the products of the selecteddepartment and the selectedProductList present in quotations for the selected period
			$quotationProducts=$this->QuotationProduct->find('all',array(
				'fields'=>array(
					'QuotationProduct.id','QuotationProduct.product_quantity',
					'QuotationProduct.currency_id','QuotationProduct.product_total_price',
				),
				'conditions'=>array(
					'QuotationProduct.product_id'=>$productsInDepartment,
					'QuotationProduct.quotation_id'=>$quotationsInPeriod,
					
				),
				'contain'=>array(
					'Currency'=>array(
						'fields'=>array('Currency.id','Currency.abbreviation')
					),
					'Product'=>array(
						'fields'=>array('Product.id','Product.name')
					),
					'Quotation'=>array(
						'fields'=>array(
							'Quotation.id','Quotation.quotation_code','Quotation.quotation_date',
						),
						'Client',
						'Currency',
						'User',
						'SalesOrder'=>array(
							'fields'=>array('SalesOrder.id'),
							'conditions'=>array(
								'SalesOrder.bool_annulled'=>false,
							),
						),
					),
				),
				'order'=>'Quotation.quotation_date,Quotation.quotation_code',
			));
			//pr($quotationProducts);
			if (!empty($quotationProducts)){
				$this->loadModel('ExchangeRate');
				for ($qp=0;$qp<count($quotationProducts);$qp++){
					$quotationDate=$quotationProducts[$qp]['Quotation']['quotation_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
					$quotationProducts[$qp]['QuotationProduct']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					
					$quotationProducts[$qp]['QuotationProduct']['dropped']=$this->QuotationProduct->getDroppedPercentageForQuotationProduct($quotationProducts[$qp]['QuotationProduct']['id']);
					$quotationProducts[$qp]['QuotationProduct']['sold']=$this->QuotationProduct->getSoldPercentageForQuotationProduct($quotationProducts[$qp]['QuotationProduct']['id']);
					
					if (!empty($quotationProducts[$qp]['Quotation']['SalesOrder'])){
						$quotationProducts[$qp]['Quotation']['bool_sales_order_present']=true;
					}
					else {
						$quotationProducts[$qp]['Quotation']['bool_sales_order_present']=false;
					}
				}
			}
			$selectedDepartments[$pc]['QuotationProducts']=$quotationProducts;
		}
		//pr($selectedDepartments);
		$this->set(compact('selectedDepartments'));
		
		$departments=$this->Department->find('list');
		$this->set(compact('departments'));	
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));	
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}
	
	public function guardarReporteCotizacionesPorDepartamento() {
		$exportData=$_SESSION['reporteCotizacionesPorDepartamento'];
		$this->set(compact('exportData'));
	}

	public function verReporteCotizacionesPorCliente(){
		$currencyId=CURRENCY_USD;
		$client_id=0;
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			//pr($startDateArray);
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$currencyId=$this->request->data['Report']['currency_id'];
			$client_id=$this->request->data['Report']['client_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('userId')){
				$currencyId=$_SESSION['currencyId'];
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
		
		$this->set(compact('startDate','endDate','currencyId','client_id'));
		$this->loadModel('Client');
		$clientConditions=array();
		$this->Client->recursive=-1;
		
		if ($client_id>0){
			$clientConditions['Client.id']=$client_id;
		}
		
		$selectedClients=$this->Client->find('all',array(
			'fields'=>array('Client.id','Client.name'),
			'conditions'=>$clientConditions,
		));
		//pr($selectedUsers);
		$this->Quotation->recursive=-1;
		for ($c=0;$c<count($selectedClients);$c++){
			$conditions=array(
				'Quotation.quotation_date >='=>$startDate,
				'Quotation.quotation_date <'=>$endDatePlusOne,
				'Quotation.client_id'=>$selectedClients[$c]['Client']['id'],
			);
			$userrole = $this->Auth->User('role_id');
			if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { 
				$conditions[]=array('Quotation.user_id'=>$this->Auth->User('id'));
			}
			$quotations=$this->Quotation->find('all',array(
				'conditions'=>$conditions,
				'contain'=>array(
					'Contact',
					'Currency',
					'User',
					'SalesOrder'=>array(
						'fields'=>'SalesOrder.id',
						'conditions'=>array(
							'SalesOrder.bool_annulled'=>false,
						),
					),
				),
				'order'=>'Quotation.quotation_date,Quotation.quotation_code',
			));
			if ($selectedClients[$c]['Client']['id']==36){
				//pr($quotations);
			}
			if (!empty($quotations)){
				$this->loadModel('ExchangeRate');
				for ($q=0;$q<count($quotations);$q++){
					$quotationDate=$quotations[$q]['Quotation']['quotation_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
					$quotations[$q]['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					
					$quotations[$q]['Quotation']['dropped']=$this->Quotation->getDroppedPercentageForQuotation($quotations[$q]['Quotation']['id']);
					$quotations[$q]['Quotation']['sold']=$this->Quotation->getSoldPercentageForQuotation($quotations[$q]['Quotation']['id']);
					
					if (!empty($quotations[$q]['SalesOrder'])){
						$quotations[$q]['Quotation']['bool_sales_order_present']=true;
					}
					else {
						$quotations[$q]['Quotation']['bool_sales_order_present']=false;
					}
				}
			}
			$selectedClients[$c]['Quotations']=$quotations;
		}
		
		$clients=$this->Client->find('list',array(
			'conditions'=>array(
				//'User.role_id'=>ROLE_EXECUTIVE,
			)
		));
		
		$this->set(compact('clients','selectedClients'));	
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}

	public function guardarReporteCotizacionesPorCliente() {
		$exportData=$_SESSION['reporteCotizacionesPorCliente'];
		$this->set(compact('exportData'));
	}

	public function generarOrdenDeVenta($quotationid){
		$this->Quotation->id = $quotationid;
		if (!$this->Quotation->exists()) {
			throw new NotFoundException(__('Invalid quotation'));
		}
		
		$quotation=$this->Quotation->find('first',array(
			'conditions'=>array(
				'Quotation.id'=>$quotationid,
			),
			'contain'=>array(
				'QuotationProduct',
				'SalesOrder',
			)
		));
		
		$boolGenerationPossible=true;
		$flashMessage="";
		if (count($quotation['SalesOrder'])>0){
			$boolGenerationPossible=false;
			$flashMessage.="Esta cotización ya tiene ordenes de venta correspondientes con números";
			if (count($quotation['SalesOrder'])==1){
				$flashMessage.=$quotation['SalesOrder'][0]['sales_order_code'].".";
			}
			else {
				for ($i=0;$i<count($quotation['SalesOrder']);$i++){
					$flashMessage.=$quotation['SalesOrder'][$i]['sales_order_code'];
					if ($i==count($quotation['SalesOrder'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (!$boolGenerationPossible){
			$flashMessage.=" La generación automática de ordenes de venta ya no está posible para esta cotización.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			$this->loadModel('SalesOrder');
			try {
				$datasource=$this->SalesOrder->getDataSource();
				$datasource->begin();
				$salesOrderArray=array();
				$salesOrderArray['SalesOrder']['quotation_id']=$quotationid;	
				$salesOrderArray['SalesOrder']['sales_order_date']=date('Y-m-d');
				$salesOrderArray['SalesOrder']['sales_order_code']=$quotation['Quotation']['quotation_code'];
				$salesOrderArray['SalesOrder']['bool_annulled']=false;
				$salesOrderArray['SalesOrder']['price_subtotal']=$quotation['Quotation']['price_subtotal'];
				$salesOrderArray['SalesOrder']['currency_id']=$quotation['Quotation']['currency_id'];
				$this->SalesOrder->create();
				if (!$this->SalesOrder->save($salesOrderArray)) {
					echo "Problema al guardar la orden de venta";
					//pr($this->validateErrors($this->SalesOrder));
					throw new Exception();
				}
				$salesOrderId=$this->SalesOrder->id;
				
				foreach ($quotation['QuotationProduct'] as $quotationProduct){
					$salesOrderProductArray=array();
					$salesOrderProductArray['SalesOrderProduct']['sales_order_id']=$salesOrderId;	
					$salesOrderProductArray['SalesOrderProduct']['product_id']=$quotationProduct['product_id'];
					$salesOrderProductArray['SalesOrderProduct']['product_description']=$quotationProduct['product_description'];
					$salesOrderProductArray['SalesOrderProduct']['product_unit_price']=$quotationProduct['product_unit_price'];
					$salesOrderProductArray['SalesOrderProduct']['product_quantity']=$quotationProduct['product_quantity'];
					$salesOrderProductArray['SalesOrderProduct']['product_total_price']=$quotationProduct['product_total_price'];
					$salesOrderProductArray['SalesOrderProduct']['currency_id']=$quotationProduct['currency_id'];
					$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_REGISTERED;
					$this->SalesOrder->SalesOrderProduct->create();
					if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
						echo "Problema al guardar los productos para la orden de venta";
						//pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
						throw new Exception();
					}
				}
				
				$datasource->commit();
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se generó automáticamente la orden de venta número ".$quotation['Quotation']['quotation_code']);
				$this->Session->setFlash(__('Se generó la orden de venta.'),'default',array('class' => 'success'));				
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía generar la cotización.'), 'default',array('class' => 'error-message'));
			}
			return $this->redirect(array('action' => 'index'));
		}
		
	}

}
