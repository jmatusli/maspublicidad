<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
/**
 * Invoices Controller
 *
 * @property Invoice $Invoice
 * @property PaginatorComponent $Paginator
 */
class InvoicesController extends AppController {


	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getnewinvoicecode');		
	}
	public function index() {
		$this->Invoice->recursive = -1;
		
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
		
		$this->set(compact('startDate','endDate','user_id','currencyId'));
		
		$conditions=array(
			'Invoice.invoice_date >='=>$startDate,
			'Invoice.invoice_date <'=>$endDatePlusOne,
		);
		$userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole'));
		if (($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT)||$user_id!=0) { 
			$conditions[]=array('Invoice.user_id'=>$user_id);
		}
		
		$invoiceCount=	$this->Invoice->find('count', array(
			'fields'=>array('Invoice.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(				
				'Client',
				'Currency',
				'Quotation',
				'User',
			),
			'order'=>array('Invoice.invoice_date'=>'DESC'),
			'limit'=>($invoiceCount!=0?$invoiceCount:1),
		);

		$invoices = $this->Paginator->paginate('Invoice');
		if (!empty($invoices)){
			$this->loadModel('ExchangeRate');
			for ($i=0;$i<count($invoices);$i++){
				$invoiceDate=$invoices[$i]['Invoice']['invoice_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
				$invoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
			}
		}
		$this->set(compact('invoices'));
		
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
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid invoice'));
		}

		$options = array(
			'conditions' => array('Invoice.' . $this->Invoice->primaryKey => $id),
			'contain'=>array(
				'Client',
				'Currency',
				'Quotation',
				'User',
				'InvoiceProduct'=>array(
					'Product',
				),
				'VendorCommissionPayment',
			),
			'order'=>'Invoice.invoice_date DESC, Invoice.invoice_code DESC'
		);
		$invoice=$this->Invoice->find('first', $options);
		$this->set(compact('invoice'));
		
		$filename='Factura_'.$invoice['Invoice']['invoice_code'];
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
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}
	
	public function viewPdf($id = null) {
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid invoice'));
		}
		
		$options = array(
			'conditions' => array('Invoice.' . $this->Invoice->primaryKey => $id),
			'contain'=>array(
				'Client',
				'Currency',
				'Quotation'=>array(
					'Contact',
				),
				'User',
				'InvoiceProduct'=>array(
					'Product',
				),
			),
		);
		$invoice=$this->Invoice->find('first', $options);
		$this->set(compact('invoice'));
		
		$filename='Factura_'.$invoice['Invoice']['invoice_code'];
		$this->set(compact('filename'));
	}

	public function add() {
		$this->loadModel('InvoiceProduct');
		$this->loadModel('SalesOrder');
		$this->loadModel('Quotation');
		if ($this->request->is('post')) {
			$invoiceDateArray=$this->request->data['Invoice']['invoice_date'];
			//pr($quotationDateArray);
			$invoiceDateString=$invoiceDateArray['year'].'-'.$invoiceDateArray['month'].'-'.$invoiceDateArray['day'];
			$invoiceDate=date( "Y-m-d", strtotime($invoiceDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
				if ($invoiceProduct['product_id']>0){
					$multiplicationDifference=abs($invoiceProduct['product_total_price']-$invoiceProduct['product_quantity']*$invoiceProduct['product_unit_price']);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			
			if ($invoiceDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de factura no puede estar en el futuro!  No se guardó la factura.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			else {			
				$datasource=$this->Invoice->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					
					if ($this->request->data['Invoice']['bool_annulled']){
						$this->request->data['Invoice']['price_subtotal']=0;
						$this->request->data['Invoice']['price_iva']=0;
						$this->request->data['Invoice']['price_total']=0;
						$this->Invoice->create();
						if (!$this->Invoice->save($this->request->data)) {
							echo "Problema guardando la factura";
							pr($this->validateErrors($this->Invoice));
							throw new Exception();
						} 
						$sales_order_id=$this->SalesOrder->id;
					}
					else {
						$this->loadModel('SalesOrder');
						$this->loadModel('Quotation');
						$this->SalesOrder->recursive=-1;
						$salesOrder=$this->SalesOrder->find('first',array(
							'conditions'=>array(
								'SalesOrder.id'=>$this->request->data['Invoice']['sales_order_id'],
							),
							'contain'=>array(
								'SalesOrderProduct'=>array(
									'conditions'=>array(
										'product_quantity >'=>0,
									),
								),
							),
						));
						
						$quotation=$this->Quotation->find('first',array(
							'conditions'=>array(
								'Quotation.id'=>$salesOrder['SalesOrder']['quotation_id'],
							),
						));
						
						$this->request->data['Invoice']['client_id']=$quotation['Quotation']['client_id'];
						$this->request->data['Invoice']['user_id']=$quotation['Quotation']['user_id'];
						$this->request->data['Invoice']['quotation_id']=$quotation['Quotation']['id'];
						
						$this->Invoice->create();
						if (!$this->Invoice->save($this->request->data)) {
							echo "Problema guardando la factura";
							pr($this->validateErrors($this->Invoice));
							throw new Exception();
						}
						$invoice_id=$this->Invoice->id;
						
						foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
							if ($invoiceProduct['product_id']>0){
								if ($invoiceProduct['product_quantity']>0){
									//pr($invoiceProduct);
									$productArray=array();
									$productArray['InvoiceProduct']['invoice_id']=$invoice_id;
									$productArray['InvoiceProduct']['product_id']=$invoiceProduct['product_id'];
									$productArray['InvoiceProduct']['product_description']=$invoiceProduct['product_description'];
									$productArray['InvoiceProduct']['product_quantity']=$invoiceProduct['product_quantity'];
									$productArray['InvoiceProduct']['product_unit_price']=$invoiceProduct['product_unit_price'];
									$productArray['InvoiceProduct']['product_total_price']=$invoiceProduct['product_total_price'];
									$productArray['InvoiceProduct']['currency_id']=$this->request->data['Invoice']['currency_id'];
									$this->InvoiceProduct->create();
									if (!$this->InvoiceProduct->save($productArray)) {
										echo "Problema guardando los productos de la factura";
										pr($this->validateErrors($this->InvoiceProduct));
										throw new Exception();
									}
								
									$salesOrderProductArray=array();
									foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
										if ($salesOrderProduct['product_id']==$invoiceProduct['product_id']){
											$salesOrderProductArray['SalesOrderProduct']['id']=$salesOrderProduct['id'];
											if ($salesOrderProduct['product_quantity']<=$invoiceProduct['product_quantity']){
												$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_COMPLETELY_DELIVERED;
											}
											$this->SalesOrder->SalesOrderProduct->id=$salesOrderProduct['id'];
											if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
												echo "Problema cambiando el estado de los productos de la orden de venta";
												pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
												throw new Exception();
											}
										}
									}
								}
							}
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->Invoice->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la factura número ".$this->request->data['Invoice']['invoice_code']);
					
					$this->Session->setFlash(__('The invoice has been saved.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('The invoice could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				}
			}
		}

		$salesOrderIds=$this->SalesOrder->SalesOrderProduct->find('list',array(
			'fields'=>array('SalesOrderProduct.sales_order_id'),
			//'conditions'=>array(
			//	'SalesOrderProduct.sales_order_product_status_id'=>array(PRODUCT_STATUS_READY_FOR_DELIVERY),
			//),
		));
		$quotationIds=$this->SalesOrder->find('list',array(
			'fields'=>array('SalesOrder.quotation_id'),
			'conditions'=>array(
				'SalesOrder.id'=>$salesOrderIds,
			),
		));
		$clientIds=$this->Quotation->find('list',array(
			'fields'=>array('Quotation.client_id'),
			'conditions'=>array(
				'Quotation.id'=>$quotationIds,
			),
		));
		$clients=$this->Invoice->Client->find('list',array(
			'conditions'=>array(
				'Client.id'=>$clientIds,
			),
			'order'=>'Client.name',
		));
		$salesOrders=$this->SalesOrder->find('list',array(
			'order'=>'SalesOrder.sales_order_code',
		));
		
		$user_id=$this->Auth->User('id');
		$this->set(compact('user_id'));
		$role_id = $this->Auth->User('role_id');
		$this->set(compact('role_id'));
		
		$currencies = $this->Invoice->Currency->find('list');
		$this->set(compact('salesOrders', 'clients', 'users', 'currencies'));
		
		$invoiceDate=date( "Y-m-d");
		$invoiceExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
		$exchangeRateInvoice=$invoiceExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateInvoice'));
		
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
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid invoice'));
		}
		$this->loadModel('SalesOrder');
		$this->loadModel('InvoiceProduct');
		$this->loadModel('Product');
		$this->loadModel('Quotation');
		if ($this->request->is(array('post', 'put'))) {
			$invoiceDateArray=$this->request->data['Invoice']['invoice_date'];
			//pr($quotationDateArray);
			$invoiceDateString=$invoiceDateArray['year'].'-'.$invoiceDateArray['month'].'-'.$invoiceDateArray['day'];
			$invoiceDate=date( "Y-m-d", strtotime($invoiceDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
				if ($invoiceProduct['product_id']>0){
					$multiplicationDifference=abs($invoiceProduct['product_total_price']-$invoiceProduct['product_quantity']*$invoiceProduct['product_unit_price']);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			
			if ($invoiceDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de factura no puede estar en el futuro!  No se guardó la factura.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			else {	
				$datasource=$this->Invoice->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$this->InvoiceProduct->recursive=-1;
					$previousInvoiceProducts=$this->InvoiceProduct->find('all',array(
						'fields'=>array('InvoiceProduct.id','InvoiceProduct.product_id'),
						'conditions'=>array(
							'InvoiceProduct.invoice_id'=>$id,
						),
					));
					if (!empty($previousInvoiceProducts)){
						$salesOrder=$this->SalesOrder->find('first',array(
							'conditions'=>array(
								'SalesOrder.id'=>$this->request->data['Invoice']['sales_order_id'],
							),
							'contain'=>array(
								'SalesOrderProduct'=>array(
									'conditions'=>array(
										'product_quantity >'=>0,
									),
								),
							),
						));
						foreach ($previousInvoiceProducts as $previousInvoiceProduct){
							$this->InvoiceProduct->id=$previousInvoiceProduct['InvoiceProduct']['id'];
							$this->InvoiceProduct->delete($previousInvoiceProduct['InvoiceProduct']['id']);
							
							$salesOrderProductArray=array();
							foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
								if ($salesOrderProduct['product_id']==$previousInvoiceProduct['InvoiceProduct']['product_id']){
									$salesOrderProductArray['SalesOrderProduct']['id']=$salesOrderProduct['id'];
									$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_READY_FOR_DELIVERY;
									
									$this->SalesOrder->SalesOrderProduct->id=$salesOrderProduct['id'];
									if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
										echo "Problema cambiando el estado de los productos de la orden de venta";
										pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
										throw new Exception();
									}
								}
							}
						}
					}
					
					if ($this->request->data['Invoice']['bool_annulled']){
						$this->request->data['Invoice']['price_subtotal']=0;
						$this->request->data['Invoice']['price_iva']=0;
						$this->request->data['Invoice']['price_total']=0;
						$this->Invoice->create();
						if (!$this->Invoice->save($this->request->data)) {
							echo "Problema guardando la factura";
							pr($this->validateErrors($this->Invoice));
							throw new Exception();
						} 
						$sales_order_id=$this->SalesOrder->id;
					}
					else {
					
						$this->loadModel('SalesOrder');
						$this->loadModel('Quotation');
						$salesOrder=$this->SalesOrder->find('first',array(
							'conditions'=>array(
								'SalesOrder.id'=>$this->request->data['Invoice']['sales_order_id'],
							),
							'contain'=>array(
								'SalesOrderProduct'=>array(
									'conditions'=>array(
										'product_quantity >'=>0,
									),
								),
							),
						));
						
						$quotation=$this->Quotation->find('first',array(
							'conditions'=>array(
								'Quotation.id'=>$salesOrder['SalesOrder']['quotation_id'],
							),
						));
						
						$this->request->data['Invoice']['client_id']=$quotation['Quotation']['client_id'];
						$this->request->data['Invoice']['user_id']=$quotation['Quotation']['user_id'];
						$this->request->data['Invoice']['quotation_id']=$quotation['Quotation']['id'];
						
						$this->Invoice->id=$id;
						if (!$this->Invoice->save($this->request->data)) {
							echo "Problema guardando la factura";
							pr($this->validateErrors($this->Invoice));
							throw new Exception();
						}
						$invoice_id=$id;
						
						foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
							if ($invoiceProduct['product_id']>0){
								if ($invoiceProduct['product_quantity']>0){
									//pr($invoiceProduct);
									$productArray=array();
									$productArray['InvoiceProduct']['invoice_id']=$invoice_id;
									$productArray['InvoiceProduct']['product_id']=$invoiceProduct['product_id'];
									$productArray['InvoiceProduct']['product_description']=$invoiceProduct['product_description'];
									$productArray['InvoiceProduct']['product_quantity']=$invoiceProduct['product_quantity'];
									$productArray['InvoiceProduct']['product_unit_price']=$invoiceProduct['product_unit_price'];
									$productArray['InvoiceProduct']['product_total_price']=$invoiceProduct['product_total_price'];
									$productArray['InvoiceProduct']['currency_id']=$this->request->data['Invoice']['currency_id'];
									$this->InvoiceProduct->create();
									if (!$this->InvoiceProduct->save($productArray)) {
										echo "Problema guardando los productos de la factura";
										pr($this->validateErrors($this->InvoiceProduct));
										throw new Exception();
									}
									
									$salesOrderProductArray=array();
									foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
										if ($salesOrderProduct['product_id']==$invoiceProduct['product_id']){
											$salesOrderProductArray['SalesOrderProduct']['id']=$salesOrderProduct['id'];
											if ($salesOrderProduct['product_quantity']<=$invoiceProduct['product_quantity']){
												$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_COMPLETELY_DELIVERED;
											}
											$this->SalesOrder->SalesOrderProduct->id=$salesOrderProduct['id'];
											if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
												echo "Problema cambiando el estado de los productos de la orden de venta";
												pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
												throw new Exception();
											}
										}
									}
								}
							}
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->Invoice->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se editó la factura número ".$this->request->data['Invoice']['invoice_code']);
					
					$this->Session->setFlash(__('La factura se guardó.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('No se podía guardar la factura.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		else {
			$options = array(
				'conditions' => array(
					'Invoice.id'=> $id,
				),
				'contain'=>array(
					'InvoiceProduct',
				),
			);
			$this->request->data = $this->Invoice->find('first', $options);
			$relatedSalesOrder=$this->SalesOrder->find('first',array(
				'conditions'=>array(
					'SalesOrder.id'=>$this->request->data['Invoice']['sales_order_id'],
				),
				'contain'=>array(
					'Quotation'=>array(
						'Client',
					)
				),
			));
			$this->set(compact('relatedSalesOrder'));	
		}
		
		$user_id=$this->Auth->User('id');
		$this->set(compact('user_id'));
		$role_id = $this->Auth->User('role_id');
		$this->set(compact('role_id'));
		//pr($relatedSalesOrder);
		/*
		$salesOrderConditions=array(
			'Invoice.sales_order_id !='=>$relatedSalesOrder['SalesOrder']['id'],
		);
		$excludedSalesOrders=$this->Invoice->find('list',array(
			'fields'=>'Invoice.sales_order_id',
			'conditions'=>$salesOrderConditions,
		));
		pr($excludedSalesOrders);*/
		if (!empty($this->request->data['Invoice']['client_id'])){
			$relatedClientId=$this->request->data['Invoice']['client_id'];
		}
		else {
			$relatedClientId=$relatedSalesOrder['Quotation']['client_id'];
		}
		/*
		$quotationConditions=array(
			'Quotation.client_id'=>$relatedClientId,
		);
		if ($role_id!=ROLE_ADMIN){
			$quotationConditions[]=array('Quotation.user_id'=>$user_id);
		}
		$quotations = $this->SalesOrder->Quotation->find('list',array(
			'fields'=>'Quotation.id',
			'conditions'=>$quotationConditions,
		));
		//pr($quotations);
		$salesOrders = $this->SalesOrder->find('list',array(
			'fields'=>array('SalesOrder.id','SalesOrder.sales_order_code'),
			'conditions'=>array(
				'SalesOrder.quotation_id'=>$quotations,
				'SalesOrder.bool_annulled'=>false,
			),
		));
		$clients = $this->Invoice->Client->find('list',array('order'=>'Client.name'));
		*/
		
		$salesOrderIds=$this->SalesOrder->SalesOrderProduct->find('list',array(
			'fields'=>array('SalesOrderProduct.sales_order_id'),
			//'conditions'=>array(
			//	'SalesOrderProduct.sales_order_product_status_id'=>array(PRODUCT_STATUS_READY_FOR_DELIVERY),
			//),
		));
		$salesOrderIds[]=$this->request->data['Invoice']['sales_order_id'];
		//pr($salesOrderIds);
		
		$quotationIds=$this->SalesOrder->find('list',array(
			'fields'=>array('SalesOrder.quotation_id'),
			'conditions'=>array(
				'SalesOrder.id'=>$salesOrderIds,
			),
		));
		$clientIds=$this->Quotation->find('list',array(
			'fields'=>array('Quotation.client_id'),
			'conditions'=>array(
				'Quotation.id'=>$quotationIds,
			),
		));
		$clients=$this->Invoice->Client->find('list',array(
			'conditions'=>array(
				'Client.id'=>$clientIds,
			),
			'order'=>'Client.name',
		));
		
		$quotationIdsForSelectedClient=$this->Quotation->find('list',array(
			'fields'=>array('Quotation.id'),
			'conditions'=>array(
				'Quotation.client_id'=>$relatedClientId,
			),
		));
		
		$salesOrders=$this->SalesOrder->find('list',array(
			'conditions'=>array(
				'SalesOrder.id'=>$salesOrderIds,
				'SalesOrder.quotation_id'=>$quotationIdsForSelectedClient,
			),
			'order'=>'SalesOrder.sales_order_code',
		));
		
		//$users = $this->Invoice->User->find('list');
		$currencies = $this->Invoice->Currency->find('list');
		$this->set(compact('salesOrders', 'clients', 'users', 'currencies'));
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$invoiceDateAsString=$this->Invoice->deconstruct('invoice_date',$this->request->data['Invoice']['invoice_date']);
		$invoiceExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDateAsString);
		$exchangeRateInvoice=$invoiceExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateInvoice'));
		
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
		$this->Invoice->id = $id;
		if (!$this->Invoice->exists()) {
			throw new NotFoundException(__('Invalid invoice'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$this->Invoice->recursive=-1;
		$invoice=$this->Invoice->find('first',array(
			'conditions'=>array(
				'Invoice.id'=>$id,
			),
			'contain'=>array(
				'InvoiceProduct',
			)
		));

		$flashMessage="";
		$boolDeletionAllowed=true;
		/*
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
		*/
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó la factura.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			try {
				$datasource=$this->Invoice->getDataSource();
				$datasource->begin();	
				//delete all stockMovements, stockItems and stockItemLogs
				foreach ($invoice['InvoiceProduct'] as $invoiceProduct){
					if (!$this->Invoice->InvoiceProduct->delete($invoiceProduct['id'])) {
						echo "Problema al eliminar el producto de la factura";
						//pr($this->validateErrors($this->Invoice->InvoiceProduct));
						throw new Exception();
					}
				}
				
				if (!$this->Invoice->delete($id)) {
					echo "Problema al eliminar la factura";
					//pr($this->validateErrors($this->Invoice));
					throw new Exception();
				}
						
				$datasource->commit();
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó la factura número ".$invoice['Invoice']['invoice_code']);
						
				$this->Session->setFlash(__('Se eliminó la factura.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				//pr($e);
				$this->Session->setFlash(__('No se podía eliminar la factura.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
		if ($this->Invoice->delete()) {
			$this->Session->setFlash(__('The invoice has been deleted.'));
		} else {
			$this->Session->setFlash(__('The invoice could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function annul($id = null) {
		$this->Invoice->id = $id;
		if (!$this->Invoice->exists()) {
			throw new NotFoundException(__('Factura inválida'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$datasource=$this->Invoice->getDataSource();
		try {
			//pr($this->request->data);
			$datasource->begin();
			
			$this->loadModel('InvoiceProduct');
			$this->InvoiceProduct->recursive=-1;
			$previousInvoiceProducts=$this->InvoiceProduct->find('all',array(
				'fields'=>array('InvoiceProduct.id'),
				'conditions'=>array(
					'InvoiceProduct.invoice_id'=>$id,
				),
			));
			if (!empty($previousInvoiceProducts)){
				foreach ($previousInvoiceProducts as $previousInvoiceProduct){
					$this->InvoiceProduct->id=$previousInvoiceProduct['InvoiceProduct']['id'];
					if (!$this->InvoiceProduct->delete($previousInvoiceProduct['InvoiceProduct']['id'])){
						echo "Problema al eliminar los productos de la factura";
						pr($this->validateErrors($this->InvoiceProduct));
						throw new Exception();
					}
				}
			}
		
			$this->Invoice->id=$id;
			$invoiceArray['Invoice']['id']=$id;
			$invoiceArray['Invoice']['bool_annulled']=true;
			$invoiceArray['Invoice']['price_subtotal']=0;
			$invoiceArray['Invoice']['price_iva']=0;
			$invoiceArray['Invoice']['price_total']=0;
			if (!$this->Invoice->save($invoiceArray)) {
				echo "Problema al anular la factura";
				pr($this->validateErrors($this->Invoice));
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

	public function getnewinvoicecode(){
		$this->layout= "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$this->loadModel('User');
		$userid=trim($_POST['userid']);
		$invoicedateday=trim($_POST['invoicedateday']);
		$invoicedatemonth=trim($_POST['invoicedatemonth']);
		$invoicedateyear=trim($_POST['invoicedateyear']);
		if (!$userid){
			throw new NotFoundException(__('Usuario no presente'));
		}
		$selectedUser=$this->User->find('first',array(
			'conditions'=>array('User.id'=>$userid,),
		));
		$userAbbreviation=$selectedUser['User']['abbreviation'];
		
		/*
		$invoiceDateString=$invoicedateyear.'-'.$invoicedatemonth.'-'.$invoicedateday;
		$invoiceDate=date( "Y-m-d", strtotime($invoiceDateString));
		$firstDayString=$invoicedateyear.'-'.$invoicedatemonth.'-01';
		$dateFirstDayMonth=date('Y-m-d', strtotime($firstDayString));
		
		$dateFirstDayNextMonth=date('Y-m-d',strtotime($firstDayString."+1 months"));
		$invoiceCount=$this->Invoice->find('count',array(
			'conditions'=>array(
				//'Invoice.invoice_code LIKE'=>$userAbbreviation."%",
				'Invoice.invoice_date >='=>$dateFirstDayMonth,
				'Invoice.invoice_date <'=>$dateFirstDayNextMonth,
			),
		));
		$fullInvoiceCount=$this->Invoice->find('count',array(
			//'conditions'=>array(
				//'Invoice.invoice_code LIKE'=>$userAbbreviation."%",
			//),
		));
		
		//$newInvoiceCode="FAC_".$userAbbreviation."_".($fullInvoiceCount+1)."_".($invoiceCount+1)."_".$invoicedateday.$invoicedatemonth.substr($invoicedateyear,2,2);
		$newInvoiceCode="FAC_".($fullInvoiceCount+1)."_".($invoiceCount+1)."_".$invoicedateday.$invoicedatemonth.substr($invoicedateyear,2,2);
		*/
		$lastInvoiceCode=$this->Invoice->find('first',array(
			'order'=>array('Invoice.invoice_code DESC'),
		));
		if (!empty($lastInvoiceCode)){
			$newInvoiceCode=$lastInvoiceCode['Invoice']['invoice_code']+1;
		}
		else {
			$newInvoiceCode="2529";
		}
		return $newInvoiceCode;
		
	}
	
	
	public function verReporteFacturasPorEjecutivo(){
		$user_id=0;
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			//pr($startDateArray);
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$user_id=$this->request->data['Report']['user_id'];
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
		$this->set(compact('startDate','endDate','user_id'));
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
		$this->Invoice->recursive=-1;
		for ($u=0;$u<count($selectedUsers);$u++){
			$invoices=$this->Invoice->find('all',array(
				'conditions'=>array(
					'Invoice.invoice_date >='=>$startDate,
					'Invoice.invoice_date <'=>$endDatePlusOne,
					'Invoice.user_id'=>$selectedUsers[$u]['User']['id'],
				),
				'contain'=>array(
					'Client',
					//'Contact',
					'Currency',
				),
			));
			/*
			if (!empty($invoices)){
				for ($i=0;$i<count($invoices);$i++){
					$invoices[$i]['Invoice']['dropped']=$this->Invoice->getDroppedPercentageForInvoice($invoices[$i]['Invoice']['id']);
					$invoices[$i]['Invoice']['sold']=$this->Invoice->getSoldPercentageForInvoice($invoices[$i]['Invoice']['id']);
				}
			}
			*/
			$selectedUsers[$u]['Invoices']=$invoices;
			
		}
		
		$users=$this->User->find('list',array(
			'conditions'=>array(
				//'User.role_id'=>ROLE_EXECUTIVE,
			)
		));
		
		$this->set(compact('users','selectedUsers'));	
	}

	public function guardarReporteFacturasPorEjecutivo() {
		$exportData=$_SESSION['reporteFacturasPorEjecutivo'];
		$this->set(compact('exportData'));
	}

	public function cuentasPorCobrar() {
		$this->loadModel('ExchangeRate');
		
		$this->Invoice->recursive = -1;
		//echo "user id in session is ".$_SESSION['userId']."<br/>";
		//$userId=$this->Auth->User('id');
		$currencyId=CURRENCY_USD;
		
		if ($this->request->is('post')) {
			/*
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$userId=$this->request->data['Report']['user_id'];
			*/
			$currencyId=$this->request->data['Report']['currency_id'];
		}
		/*
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
		$_SESSION['userId']=$user_id;
		*/
		$_SESSION['currencyId']=$currencyId;
		
		//$this->set(compact('startDate','endDate','user_id'));
		$this->set(compact('currencyId'));
		/*
		$conditions=array(
			'Invoice.invoice_date >='=>$startDate,
			'Invoice.invoice_date <'=>$endDatePlusOne,
		);
		$userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole'));
		if (($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT)||$user_id!=0) { 
			$conditions[]=array('Invoice.user_id'=>$user_id);
		}
		*/
		$conditions=array(
			'Invoice.bool_annulled'=>false,
			'OR'=>array(
				array('Invoice.bool_paid'=>false),
				array('Invoice.amount_paid <='=>'Invoice.price_total'),
			)
		);
		$invoiceCount=	$this->Invoice->find('count', array(
			'fields'=>array('Invoice.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(				
				'Client'=>array(
					'fields'=>array(
						'Client.id','Client.name'
					),
				),
				'Currency'=>array(
					'fields'=>array(
						'Currency.id','Currency.abbreviation'
					),
				),
				'Quotation'=>array(
					'fields'=>array(
						'Quotation.id','Quotation.quotation_code',
					),
					'Contact'=>array(
						'fields'=>array(
							'Contact.id','Contact.first_name','Contact.last_name',
						),
					),
				),
				'SalesOrder'=>array(					
					'fields'=>array(
						'SalesOrder.id','SalesOrder.sales_order_code',
					),
				),
				'User'=>array(					
					'fields'=>array(
						'User.id','User.username',
					),
				),
			),
			'order'=>array('Invoice.invoice_date'=>'ASC'),
			'limit'=>($invoiceCount!=0?$invoiceCount:1),
		);

		$invoices = $this->Paginator->paginate('Invoice');
		if (!empty($invoices)){
			for ($i=0;$i<count($invoices);$i++){
				$invoiceDate=$invoices[$i]['Invoice']['invoice_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
				$invoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
			}
		}
		$this->set(compact('invoices'));
		
		$this->loadModel('Client');
		$clients=$this->Client->find('all',array(
			'contain'=>array(
				'Invoice'=>array(
					'fields'=>array(
						'Invoice.invoice_date',
						'Invoice.currency_id',
						'Invoice.price_subtotal',
						'Invoice.price_total',
						'Invoice.amount_paid',
					),
					'conditions' => $conditions,					
				),
			),
		));
		$clientesPorCobrar=array();
		foreach ($clients as $client){
			if (!empty($client['Invoice'])){
				$clientId=$client['Client']['id'];
				/*
				if (in_array($clientId,array_keys($clientesPorCobrar))){
					$clientesPorCobrar[$clientId]+=$client['Invoice']['price_subtotal'];
				}
				else {
					$clientesPorCobrar[$clientId]=$client['Invoice']['price_subtotal'];
				}
				*/
				$clientesPorCobrar[$clientId]['id']=$client['Client']['id'];
				$clientesPorCobrar[$clientId]['name']=$client['Client']['name'];
				$clientesPorCobrar[$clientId]['saldo']=0;
				foreach ($client['Invoice'] as $invoice){
					$invoiceDate=$invoice['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					$rate=$exchangeRate['ExchangeRate']['rate'];
					if ($currencyId==CURRENCY_USD){
						if ($invoice['currency_id']==CURRENCY_USD){
							$clientesPorCobrar[$clientId]['saldo']+=($invoice['price_total']-$invoice['amount_paid']);
						}
						else {
							$clientesPorCobrar[$clientId]['saldo']+=round(($invoice['price_total']-$invoice['amount_paid'])/$rate,2);
						}
					}
					else {
						if ($invoice['currency_id']==CURRENCY_CS){
							$clientesPorCobrar[$clientId]['saldo']+=($invoice['price_total']-$invoice['amount_paid']);
						}
						else {
							$clientesPorCobrar[$clientId]['saldo']+=round(($invoice['price_total']-$invoice['amount_paid'])*$rate,2);
						}
					}
				}
			}
		}
		//pr($clientesPorCobrar);
		usort($clientesPorCobrar,array($this,'sortBySaldo'));
		//pr($clientesPorCobrar);
		$this->set(compact('clientesPorCobrar'));
		
		$this->loadModel('User');
		$vendors=$this->User->find('all',array(
			'contain'=>array(
				'Invoice'=>array(
					'fields'=>array(
						'Invoice.invoice_date',
						'Invoice.currency_id',
						'Invoice.price_subtotal',
						'Invoice.price_total',
						'Invoice.amount_paid',
					),
					'conditions' => $conditions,					
				),
			),
		));
		$vendedoresPorCobrar=array();
		foreach ($vendors as $vendor){
			if (!empty($vendor['Invoice'])){
				$vendorId=$vendor['User']['id'];
				/*
				if (in_array($vendorId,array_keys($clientesPorCobrar))){
					$clientesPorCobrar[$vendorId]+=$vendor['Invoice']['price_subtotal'];
				}
				else {
					$clientesPorCobrar[$vendorId]=$vendor['Invoice']['price_subtotal'];
				}
				*/
				$vendedoresPorCobrar[$vendorId]['id']=$vendor['User']['id'];
				$vendedoresPorCobrar[$vendorId]['name']=$vendor['User']['username'];
				$vendedoresPorCobrar[$vendorId]['saldo']=0;
				foreach ($vendor['Invoice'] as $invoice){
					$invoiceDate=$invoice['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					$rate=$exchangeRate['ExchangeRate']['rate'];
					if ($currencyId==CURRENCY_USD){
						if ($invoice['currency_id']==CURRENCY_USD){
							$vendedoresPorCobrar[$vendorId]['saldo']+=($invoice['price_total']-$invoice['amount_paid']);
						}
						else {
							$vendedoresPorCobrar[$vendorId]['saldo']+=round(($invoice['price_total']-$invoice['amount_paid'])/$rate,2);
						}
					}
					else {
						if ($invoice['currency_id']==CURRENCY_CS){
							$vendedoresPorCobrar[$vendorId]['saldo']+=($invoice['price_total']-$invoice['amount_paid']);
						}
						else {
							$vendedoresPorCobrar[$vendorId]['saldo']+=round(($invoice['price_total']-$invoice['amount_paid'])*$rate,2);
						}
					}
				}
			}
		}
		//pr($vendedoresPorCobrar);
		usort($vendedoresPorCobrar,array($this,'sortBySaldo'));
		//pr($vendedoresPorCobrar);
		$this->set(compact('vendedoresPorCobrar'));
		
		/*
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
		*/
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}

	public function sortBySaldo($a,$b ){ 	
		return ($a['saldo'] < $b['saldo']) ? 1 : -1;
	} 
	
	public function guardarCuentasPorCobrar() {
		$exportData=$_SESSION['cuentasPorCobrar'];
		$this->set(compact('exportData'));
	}

	public function comisionesPorVendedor() {
		$this->loadModel('SalesObjective');
		$this->loadModel('ExchangeRate');
	
		$this->Invoice->recursive = -1;
		
		$userRoleId = $this->Auth->User('role_id');
		$this->set(compact('userRoleId'));
		
		//echo "user id in session is ".$_SESSION['userId']."<br/>";
		$userId=$this->Auth->User('id');
		$currencyId=CURRENCY_USD;
		
		$startDate = date("Y-m-01");
		$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
		$boolSetFromPostData=false;
		if ($this->request->is('post')){
			if(!empty($this->request->data['refresh'])) {
				$boolSetFromPostData=true;
				$startDateArray=$this->request->data['Report']['startdate'];
				$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
				$startDate=date( "Y-m-d", strtotime($startDateString));
			
				$endDateArray=$this->request->data['Report']['enddate'];
				$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
				$endDate=date("Y-m-d",strtotime($endDateString));
				$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
				
				if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT) { 
					$userId=$this->request->data['Report']['user_id'];
				}
				$currencyId=$this->request->data['Report']['currency_id'];
			}
		}
		if (!$boolSetFromPostData){
			if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
				//echo "setting data from session<br/>";
				$startDate=$_SESSION['startDate'];
				$endDate=$_SESSION['endDate'];
				$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
				if ($this->Session->check('currencyId')){
					$currencyId=$_SESSION['currencyId'];
				}
				if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT) { 
					$userId=$_SESSION['userId'];
				}
			}
			else {
				//echo "setting data defaults<br/>";
				$startDate = date("Y-m-01");
				$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
				$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
			}
		}
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$_SESSION['currencyId']=$currencyId;
		//echo "setting userId<br/>";
		$_SESSION['userId']=$userId;
		//echo "session userId is ".$_SESSION['userId']."!<br/>";
		$this->set(compact('startDate','endDate','userId','currencyId'));
		
		$exchangeRateStartDate=$this->ExchangeRate->getApplicableExchangeRate($startDate);
		$this->set(compact('exchangeRateStartDate'));
		
		$userConditions=array(
			//'User.bool_active'=>true,
		);
		if ($userId>0) { 
			$userConditions[]=array('User.id'=>$userId);
		}
		$this->loadModel('User');
		$this->User->recursive=-1;
		$selectedUsers=$this->User->find('all',array(
			'conditions'=>$userConditions,
		));
		
		if (!empty($selectedUsers)){
			for ($u=0;$u<count($selectedUsers);$u++){
				$selectedUsers[$u]['historical_performance']=$this->SalesObjective->getHistoricalPerformance($selectedUsers[$u]['User']['id']);
			}
		}
		//pr($selectedUsers);
		//pr($startDate);
		
		if ($this->request->is('post')) {
			//pr($this->request->data);
			foreach ($selectedUsers as $selectedUser){
				$submitButtonName="User_".$selectedUser['User']['id']."_save";
				if (!empty($this->request->data[$submitButtonName])){
					//pr($this->request->data);
					//pr($this->request->data['User'][$selectedUser['User']['id']]);
					$datasource=$this->Invoice->getDataSource();
					$datasource->begin();
					try {
						foreach ($this->request->data['User'][$selectedUser['User']['id']]['PendingInvoice'] as $invoice){
							$this->Invoice->id=$invoice['invoiceid'];
							$invoiceData=array();
							$invoiceData['Invoice']['id']=$invoice['invoiceid'];
							$invoiceData['Invoice']['percentage_commission']=$invoice['percentage_commission'];
							$invoiceData['Invoice']['amount_commission']=$invoice['amount_commission'];
							if (!$this->Invoice->save($invoiceData)) {
								echo "problema al guardar la factura pendiente ".$invoice['invoiceid'];
								pr($this->validateErrors($this->Invoice));
								throw new Exception();
							}
						}
						foreach ($this->request->data['User'][$selectedUser['User']['id']]['Invoice'] as $invoice){
							$this->Invoice->id=$invoice['invoiceid'];
							$invoiceData=array();
							$invoiceData['Invoice']['id']=$invoice['invoiceid'];
							$invoiceData['Invoice']['percentage_commission']=$invoice['percentage_commission'];
							$invoiceData['Invoice']['amount_commission']=$invoice['amount_commission'];
							if (!$this->Invoice->save($invoiceData)) {
								echo "problema al guardar la factura ".$invoice['invoiceid'];
								pr($this->validateErrors($this->Invoice));
								throw new Exception();
							}
						}
						$datasource->commit();
					
						$this->Session->setFlash(__('Se guardaron las comisiones para las facturas'), 'default',array('class' => 'success'));
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);					
						$this->Session->setFlash(__('No se podían guardar las comisiones'), 'default',array('class' => 'error-message'));
					}
				}
			}
		}
		
		for ($u=0;$u<count($selectedUsers);$u++){
			$invoiceConditions=array(
				'Invoice.invoice_date >='=>$startDate,
				'Invoice.invoice_date <'=>$endDatePlusOne,
				'Invoice.bool_annulled'=>false,
				'Invoice.user_id'=>$selectedUsers[$u]['User']['id'],
			);
			
			$invoiceCount=	$this->Invoice->find('count', array(
				'fields'=>array('Invoice.id'),
				'conditions' => $invoiceConditions,
			));
			
			$this->Paginator->settings = array(
				'fields'=>array(
					'Invoice.id',
					'Invoice.invoice_code',
					'Invoice.invoice_date',
					'Invoice.price_subtotal',
					'Invoice.bool_credit',
					'Invoice.amount_paid',
					'Invoice.bool_paid',
					'Invoice.percentage_commission',
					'Invoice.amount_commission',
				),
				'conditions' => $invoiceConditions,
				'contain'=>array(				
					'Client'=>array(
						'fields'=>array(
							'Client.id','Client.name',
						),
					),
					'Currency',
					'Quotation'=>array(
						//'Contact',
					),
					'SalesOrder'=>array(
						'fields'=>array(
							'SalesOrder.id','SalesOrder.sales_order_code',
						),
					),
				),
				'order'=>array('Invoice.invoice_date'=>'ASC'),
				'limit'=>($invoiceCount!=0?$invoiceCount:1),
			);
			$invoices = $this->Paginator->paginate('Invoice');
			
			$cashInvoices=array();
			$creditInvoices=array();
			//pr($invoices);
			if (!empty($invoices)){
				foreach ($invoices as $invoice){
					if ($invoice['Invoice']['bool_credit']){
						$creditInvoices[]=$invoice;
					}
					else {
						$cashInvoices[]=$invoice;
					}
				}
			}
			if (!empty($creditInvoices)){
				echo "processing credit invoices<br/>";
				for ($i=0;$i<count($creditInvoices);$i++){
					//pr($creditInvoices[$i]);
					$invoiceDate=$creditInvoices[$i]['Invoice']['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					//pr($exchangeRate);
					$creditInvoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				}
				//pr($creditInvoices);
			}
			$selectedUsers[$u]['creditInvoices']=$creditInvoices;
			if (!empty($cashInvoices)){
				//echo "processing cash invoices<br/>";
				for ($i=0;$i<count($cashInvoices);$i++){
					//pr($cashInvoices[$i]);
					$invoiceDate=$cashInvoices[$i]['Invoice']['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					//pr($exchangeRate);
					$cashInvoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				}
			}
			$selectedUsers[$u]['cashInvoices']=$cashInvoices;
			
			$salesObjective=$this->SalesObjective->getApplicableSalesObjective(date('Y-m-d'));
			if (!empty($salesObjective)){
				$selectedUsers[$u]['SalesObjective']=$salesObjective['SalesObjective'];
			}
			else {
				$selectedUsers[$u]['SalesObjective']=array();
			}
			
			$pendingInvoiceconditions=array(
				'Invoice.bool_annulled'=>false,
				'Invoice.user_id'=>$selectedUsers[$u]['User']['id'],
				'OR'=>array(
					array('Invoice.bool_paid'=>false),
					array('Invoice.amount_paid <='=>'Invoice.price_total'),
				)
			);
			$invoiceCount=	$this->Invoice->find('count', array(
				'fields'=>array('Invoice.id'),
				'conditions' => $pendingInvoiceconditions,
			));
			
			$this->Paginator->settings = array(
				'conditions' => $pendingInvoiceconditions,
				
				'contain'=>array(				
					'Client'=>array(
						'fields'=>array(
							'Client.id','Client.name'
						),
					),
					'Currency'=>array(
						'fields'=>array(
							'Currency.id','Currency.abbreviation'
						),
					),
					'Quotation'=>array(
						'fields'=>array(
							'Quotation.id','Quotation.quotation_code',
						),
						'Contact'=>array(
							'fields'=>array(
								'Contact.id','Contact.first_name','Contact.last_name',
							),
						),
					),
					'SalesOrder'=>array(					
						'fields'=>array(
							'SalesOrder.id','SalesOrder.sales_order_code',
						),
					),
					'User'=>array(					
						'fields'=>array(
							'User.id','User.username',
						),
					),
				),
				'order'=>array('Invoice.invoice_date'=>'ASC'),
				'limit'=>($invoiceCount!=0?$invoiceCount:1),
			);

			$pendingInvoices = $this->Paginator->paginate('Invoice');
			if (!empty($pendingInvoices)){
				for ($i=0;$i<count($pendingInvoices);$i++){
					$invoiceDate=$pendingInvoices[$i]['Invoice']['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					$pendingInvoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				}
			}
			$selectedUsers[$u]['pendingInvoices']=$pendingInvoices;
			
			
			
		}
		//pr($selectedUsers);
		$this->set(compact('selectedUsers'));
		
		$this->loadModel('User');
		$users=$this->User->find('list',array(
			'order'=>'User.username'
		));
		$this->set(compact('users'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}

	public function guardarComisionesPorVendedor() {
		$exportData=$_SESSION['comisionesPorVendedor'];
		$this->set(compact('exportData'));
	}


}
