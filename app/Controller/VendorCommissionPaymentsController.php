<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class VendorCommissionPaymentsController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getnewinvoicecode');		
	}
	public function index() {
		$this->VendorCommissionPayment->recursive = -1;
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
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			
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
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('userId'));
		
		$userRoleId = $this->Auth->User('role_id');
		$this->set(compact('userRoleId'));
		
		$userConditions=array();
		if ($userId>0) { 
			$userConditions[]=array('User.id'=>$userId);
		}
		$this->loadModel('User');
		$this->User->recursive=-1;
		$selectedUsers=$this->User->find('all',array(
			'conditions'=>$userConditions,
		));
		
		for ($u=0;$u<count($selectedUsers);$u++){
			$conditions=array(
				'VendorCommissionPayment.payment_date >='=>$startDate,
				'VendorCommissionPayment.payment_date <'=>$endDatePlusOne,
				'VendorCommissionPayment.user_id'=>$selectedUsers[$u]['User']['id'],
			);
			$vendorCommissionPaymentCount=	$this->VendorCommissionPayment->find('count', array(
				'fields'=>array('VendorCommissionPayment.id'),
				'conditions' => $conditions,
			));
			
			$this->Paginator->settings = array(
				'conditions' => $conditions,
				'contain'=>array(				
					'Invoice',
					'User',
				),
				'limit'=>($vendorCommissionPaymentCount!=0?$vendorCommissionPaymentCount:1),
			);

			$vendorCommissionPayments = $this->Paginator->paginate('VendorCommissionPayment');
			$selectedUsers[$u]['vendorCommissionPayments']=$vendorCommissionPayments;
		}
		//pr($selectedUsers);
		$this->set(compact('selectedUsers'));
		
		$this->loadModel('User');
		$users=$this->User->find('list',array(
			'order'=>'User.username'
		));
		$this->set(compact('users'));
		
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
		if (!$this->VendorCommissionPayment->exists($id)) {
			throw new NotFoundException(__('Invalid vendor commission payment'));
		}
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
		$options = array('conditions' => array('VendorCommissionPayment.' . $this->VendorCommissionPayment->primaryKey => $id));
		$this->set('vendorCommissionPayment', $this->VendorCommissionPayment->find('first', $options));
		
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->VendorCommissionPayment->create();
			if ($this->VendorCommissionPayment->save($this->request->data)) {
				$this->Session->setFlash(__('The vendor commission payment has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The vendor commission payment could not be saved. Please, try again.'),'default',array('class' => 'error-message'));
			}
		}
		$users = $this->VendorCommissionPayment->User->find('list');
		$invoices = $this->VendorCommissionPayment->Invoice->find('list');
		$this->set(compact('users', 'invoices'));
		
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->VendorCommissionPayment->exists($id)) {
			throw new NotFoundException(__('Invalid vendor commission payment'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->VendorCommissionPayment->save($this->request->data)) {
				$this->Session->setFlash(__('The vendor commission payment has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The vendor commission payment could not be saved. Please, try again.'),'default',array('class' => 'error-message'));
			}
		} else {
			$options = array('conditions' => array('VendorCommissionPayment.' . $this->VendorCommissionPayment->primaryKey => $id));
			$this->request->data = $this->VendorCommissionPayment->find('first', $options);
		}
		$users = $this->VendorCommissionPayment->User->find('list');
		$invoices = $this->VendorCommissionPayment->Invoice->find('list');
		$this->set(compact('users', 'invoices'));
		
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->VendorCommissionPayment->id = $id;
		if (!$this->VendorCommissionPayment->exists()) {
			throw new NotFoundException(__('Invalid vendor commission payment'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$vendorCommissionPayment=$this->VendorCommissionPayment->find('first',array(
			'conditions'=>array(
				'VendorCommissionPayment.id'=>$id,
			),
		));
		
		if ($this->VendorCommissionPayment->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$vendorCommissionPayment['VendorCommissionPayment']['id'];
			$deletionArray['Deletion']['reference']=$vendorCommissionPayment['VendorCommissionPayment']['username'];
			$deletionArray['Deletion']['type']='VendorCommissionPayment';
			$this->Deletion->save($deletionArray);
		
			$this->Session->setFlash(__('The vendor commission payment has been deleted.'));
		} 
		else {
			$this->Session->setFlash(__('The vendor commission payment could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function resumenPagosPendientes() {
		$this->loadModel('Invoice');
		$this->loadModel('InvoiceSalesOrder');
		$this->Invoice->recursive = -1;
		$this->VendorCommissionPayment->recursive = -1;
		$userId=$this->Auth->User('id');
		
		$userRoleId = $this->Auth->User('role_id');
		$this->set(compact('userRoleId'));
		
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
				
				$userId=$this->request->data['Report']['user_id'];
			}
		}
		if (!$boolSetFromPostData){
			if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
				$startDate=$_SESSION['startDate'];
				$endDate=$_SESSION['endDate'];
				$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
				
				if ($userRoleId==ROLE_ADMIN||$userRoleId==ROLE_ASSISTANT){
					$userId=$_SESSION['userId'];
				}
			}
			else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		}	
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('userId'));
		
		$monthName="";
		switch (date('m',strtotime($startDate))){
			case "01":
				$monthName.="ENERO ";
				break;
			case "02":
				$monthName.="FEBRERO ";
				break;
			case "03":
				$monthName.="MARZO ";
				break;
			case "04":
				$monthName.="ABRIL ";
				break;
			case "05":
				$monthName.="MAYO ";
				break;
			case "06":
				$monthName.="JUNIO ";
				break;
			case "07":
				$monthName.="JULIO ";
				break;
			case "08":
				$monthName.="AGOSTO ";
				break;
			case "09":
				$monthName.="SEPTIEMBRE ";
				break;
			case "10":
				$monthName.="OCTUBRE ";
				break;
			case "11":
				$monthName.="NOVIEMBRE ";
				break;
			case "12":
				$monthName.="DICIEMBRE ";
				break;
			default:
				//pr($startDate);
				//echo "month is ".date('m',strtotime($startDate))."<br/>";
				//$monthName="ENERO 2016";
		}
		$monthName.=date('Y',strtotime($startDate));
		$this->set(compact('monthName'));
		
		$userConditions=array();
		if ($userId>0) { 
			$userConditions[]=array('User.id'=>$userId);
		}
		$this->loadModel('User');
		$this->User->recursive=-1;
		$selectedUsers=$this->User->find('all',array(
			'conditions'=>$userConditions,
		));
		
		if ($this->request->is('post')) {
			//pr($this->request->data);
			foreach ($selectedUsers as $selectedUser){
				$submitButtonName="User_".$selectedUser['User']['id']."_save";
				if (!empty($this->request->data[$submitButtonName])){
					//pr($this->request->data['User'][$selectedUser['User']['id']]);
					
					$datasource=$this->Invoice->getDataSource();
					$datasource->begin();
					try {
						foreach ($this->request->data['User'][$selectedUser['User']['id']]['Invoice'] as $invoice){
							if ($invoice['commission_paid']>0){
								$this->VendorCommissionPayment->create();
								$vendorCommissionPaymentData=array();
								$vendorCommissionPaymentData['VendorCommissionPayment']['user_id']=$selectedUser['User']['id'];
								$vendorCommissionPaymentData['VendorCommissionPayment']['invoice_id']=$invoice['invoiceid'];
								$vendorCommissionPaymentData['VendorCommissionPayment']['payment_date']=$this->request->data['User'][$selectedUser['User']['id']]['payment_date'];
								$vendorCommissionPaymentData['VendorCommissionPayment']['commission_paid']=$invoice['commission_paid'];
								if (!$this->VendorCommissionPayment->save($vendorCommissionPaymentData)) {
									echo "problema al guardar el pago al vendedor";
									pr($this->validateErrors($this->VendorCommissionPayment));
									throw new Exception();
								}
							}
						}
						$datasource->commit();
						echo "bueno, llegamos al final<br/>";
						$this->Session->setFlash(__('Se guardaron los pagos al vendedor'), 'default',array('class' => 'success'));
						//return $this->redirect(array('action' => 'resumenPagosPendientes'));
						return $this->redirect($this->referer());
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);					
						$this->Session->setFlash(__('No se podían guardar los pagos'), 'default',array('class' => 'error-message'));
					}
				}
			}
		}
		
		for ($u=0;$u<count($selectedUsers);$u++){
			$this->InvoiceSalesOrder->recursive=-1;
			$invoiceIds=$this->InvoiceSalesOrder->find('list',array(
				'fields'=>array('InvoiceSalesOrder.invoice_id'),
				'conditions'=>array(
					'InvoiceSalesOrder.user_id'=>$selectedUsers[$u]['User']['id']
				),
			));
			$conditions=array(
				'Invoice.invoice_date >='=>$startDate,
				'Invoice.invoice_date <'=>$endDatePlusOne,
				'Invoice.id'=>$invoiceIds,
				'Invoice.bool_credit'=>false,
			);
			$invoiceCount=	$this->Invoice->find('count', array(
				'fields'=>array('Invoice.id'),
				'conditions' => $conditions,
			));
			
			$this->Paginator->settings = array(
				'conditions' => $conditions,
				'contain'=>array(				
					'VendorCommissionPayment',
				),
				'order'=>array('Invoice.invoice_date'=>'ASC'),
				'limit'=>($invoiceCount!=0?$invoiceCount:1),
			);

			$cashInvoicesForPeriod = $this->Paginator->paginate('Invoice');
			//pr($invoices);
			
			for ($i=0;$i<count($cashInvoicesForPeriod);$i++){
				//pr($cashInvoicesForPeriod[$i]);
				$amountCommission=$cashInvoicesForPeriod[$i]['Invoice']['amount_commission'];
				$already_paid=0;
				$payment_date="2014-0-01";
				if (!empty($cashInvoicesForPeriod[$i]['VendorCommissionPayment'])){
					foreach($cashInvoicesForPeriod[$i]['VendorCommissionPayment'] as $payment){
						$already_paid+=$payment['commission_paid'];
						if (date("Y-m-d",strtotime($payment['payment_date']))>date("Y-m-d",strtotime($payment_date))){
							$payment_date=$payment['payment_date'];
						}
					}
				}
				if ($already_paid<$amountCommission){
					$remaining_saldo=$amountCommission-$already_paid;
				}
				else {
					$remaining_saldo=0;
				}
				$cashInvoicesForPeriod[$i]['Invoice']['already_paid']=$already_paid;
				$cashInvoicesForPeriod[$i]['Invoice']['payment_date']=$payment_date;
				$cashInvoicesForPeriod[$i]['Invoice']['remaining_saldo']=$remaining_saldo;
				
				// INCLUDE ALL INVOICES, EVEN IF THEY ARE CANCELED
				//if ($remaining_saldo>0){
				//	$cashInvoices[]=$invoice;
				//}
			}
			$selectedUsers[$u]['cashInvoices']=$cashInvoicesForPeriod;
			
			$conditions=array(
				'Invoice.invoice_date <'=>$startDate,
				'Invoice.id'=>$invoiceIds,
				'Invoice.bool_credit'=>true,
				'Invoice.bool_paid'=>true,
				'Invoice.last_payment_date >='=>$startDate,
				'Invoice.last_payment_date <'=>$endDatePlusOne,
			);
			$invoiceCount=	$this->Invoice->find('count', array(
				'fields'=>array('Invoice.id'),
				'conditions' => $conditions,
			));
			
			$this->Paginator->settings = array(
				'conditions' => $conditions,
				'contain'=>array(				
					'VendorCommissionPayment',
				),
				'order'=>array('Invoice.invoice_date'=>'ASC'),
				'limit'=>($invoiceCount!=0?$invoiceCount:1),
			);

			$paidInvoicesFromPreviousPeriod = $this->Paginator->paginate('Invoice');
			//pr($paidInvoicesFromPreviousPeriod);
			for ($i=0;$i<count($paidInvoicesFromPreviousPeriod);$i++){
				//pr($paidInvoicesFromPreviousPeriod[$i]);
				$amountCommission=$paidInvoicesFromPreviousPeriod[$i]['Invoice']['amount_commission'];
				$already_paid=0;
				$payment_date="2014-01-01";
				if (!empty($paidInvoicesFromPreviousPeriod[$i]['VendorCommissionPayment'])){
					foreach($paidInvoicesFromPreviousPeriod[$i]['VendorCommissionPayment'] as $payment){
						$already_paid+=$payment['commission_paid'];
						if (date("Y-m-d",strtotime($payment['payment_date']))>date("Y-m-d",strtotime($payment_date))){
							$payment_date=$payment['payment_date'];
						}
					}
				}
				if ($already_paid<$amountCommission){
					$remaining_saldo=$amountCommission-$already_paid;
				}
				else {
					$remaining_saldo=0;
				}
				$paidInvoicesFromPreviousPeriod[$i]['Invoice']['already_paid']=$already_paid;
				$paidInvoicesFromPreviousPeriod[$i]['Invoice']['payment_date']=$payment_date;
				$paidInvoicesFromPreviousPeriod[$i]['Invoice']['remaining_saldo']=$remaining_saldo;
			}
			$selectedUsers[$u]['paidInvoicesPreviousPeriod']=$paidInvoicesFromPreviousPeriod;
			
			$pendingInvoiceConditions=array(
				'Invoice.bool_annulled'=>false,
				'Invoice.id'=>$invoiceIds,
				'Invoice.bool_paid'=>false,
				'Invoice.invoice_date <'=>$startDate,
			);
			//pr($pendingInvoiceConditions);
			$invoiceCount=	$this->Invoice->find('count', array(
				'fields'=>array('Invoice.id'),
				'conditions' => $pendingInvoiceConditions,
			));
			
			$this->Paginator->settings = array(
				'conditions' => $pendingInvoiceConditions,
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
					'InvoiceSalesOrder'=>array(						
						'SalesOrder'=>array(					
							'fields'=>array(
								'SalesOrder.id','SalesOrder.sales_order_code',
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
						),
						'User'=>array(					
							'fields'=>array(
								'User.id','User.username',
							),
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
		
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
	}
	
	public function guardarResumenPagosPendientes() {
		$exportData=$_SESSION['resumenPagosPendientes'];
		$this->set(compact('exportData'));
	}
	

}
