<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');


class QuotationsController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getquotationproducts','getquotationinfo','getnewquotationcode','getquotationcurrencyid','getquotationiva','getquotationsforclient','generarOrdenDeVenta');		
	}

	public function index() {
		$this->loadModel('SalesOrder');
    $this->loadModel('User');
    $this->loadModel('UserPageRight');
    
    $this->Quotation->recursive = -1;
		$this->User->recursive = -1;
		
    $loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    
    $canSeeExecutiveTables=$this->UserPageRight->hasUserPageRight('VER_RESUMEN_EJECUTIVO',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeExecutiveTables'));
    
    $canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllUsers'));
    //echo 'can see all users:'.$canSeeAllUsers.'<br/>';
    
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllSalesExecutives'));
    //echo 'can see all sales executives:'.$canSeeAllSalesExecutives.'<br/>';
    
		$currencyId=CURRENCY_USD;
		$rejectedDisplay=0;
		$salesOrderDisplay=0;
		if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives){
      $userId=0;
    }
    else {
      $userId=$loggedUserId;
    }
		
    
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
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('currencyId','userId'));
		$this->set(compact('rejectedDisplay','salesOrderDisplay'));
		
		// 20151109 user selection added
		$conditions=[
			'Quotation.quotation_date >='=>$startDate,
			'Quotation.quotation_date <'=>$endDatePlusOne,
		];
		$userrole = $this->Auth->User('role_id');
		
    $userConditions=['User.bool_active'=>true];
		if ($userRoleId!=ROLE_ADMIN && !$canSeeAllUsers && !$canSeeAllSalesExecutives){
      // 20171214 it is not strictly necessary to restrict the user list as this is done in the page with a hidden form element
      // 2017 however, the condition is added back as it seems safer that way
			$userConditions[]=['User.id'=>$this->Auth->User('id')];
		}
		$userPeriod=$this->User->find('list',array(
      'conditions'=>$userConditions,
      'order'=>'User.username',
    ));
    foreach ($userPeriod as $key=>$value){
        $userPeriod[$key]=0;
    }
    //pr($userPeriod);
    
    $userPendingCS=$userPendingUSD=$userPeriodCS=$userPeriodUSD=$userPeriod;
    
		if ($userRoleId != ROLE_ADMIN && !$canSeeAllUsers && !$canSeeAllSalesExecutives) { 
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
				$conditions[]=array('Quotation.bool_rejected'=>true);
				break;
			case 2:
				$conditions[]=array('Quotation.bool_rejected'=>false);
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
				'Quotation.bool_rejected',
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
					'fields'=>array('SalesOrder.id','SalesOrder.sales_order_code'),
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
				'QuotationRemark'=>array(
					'order'=>'QuotationRemark.reminder_date DESC'
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
        if (array_key_exists($quotations[$i]['User']['id'],$userPeriod)){
          if ($quotations[$i]['Currency']['id']==CURRENCY_CS){          
            $userPeriodCS[$quotations[$i]['User']['id']]+=$quotations[$i]['Quotation']['price_subtotal'];
            $userPeriodUSD[$quotations[$i]['User']['id']]+=round($quotations[$i]['Quotation']['price_subtotal']/$quotations[$i]['Quotation']['exchange_rate'],2);
          }
          elseif ($quotations[$i]['Currency']['id']==CURRENCY_USD){
            $userPeriodUSD[$quotations[$i]['User']['id']]+=$quotations[$i]['Quotation']['price_subtotal'];
            $userPeriodCS[$quotations[$i]['User']['id']]+=round($quotations[$i]['Quotation']['price_subtotal']*$quotations[$i]['Quotation']['exchange_rate'],2);
          }    
        }
        
			}
		}
		
		$this->set(compact('quotations'));
		$this->set(compact('userPeriodCS','userPeriodUSD'));
		// pending quotations from previous months
		
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
		if ($userRoleId != ROLE_ADMIN && !$canSeeAllUsers && !$canSeeAllSalesExecutives) { 
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
				'Quotation.bool_rejected',
				
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
      //pr($pendingQuotations);
			for ($i=0;$i<count($pendingQuotations);$i++){
        //echo '$i is '.$i.'<br/>';
        //if ($i == 780){
        //  echo '$i is '.$i.'<br/>';
        //  pr($pendingQuotations[$i]);
        //}
				//if (empty($pendingQuotations[$i]['User']['id'])){
        //  echo '$i is '.$i.'<br/>';
        //  pr($pendingQuotations[$i]);
        //}
        
        // set the exchange rate
				$quotationDate=$pendingQuotations[$i]['Quotation']['quotation_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
				//pr($exchangeRate);
				$pendingQuotations[$i]['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				
				if (!empty($pendingQuotations[$i]['SalesOrder'])){
					$pendingQuotations[$i]['Quotation']['bool_sales_order_present']=true;
				}
				else {
					$pendingQuotations[$i]['Quotation']['bool_sales_order_present']=false;
				}
        
        if (array_key_exists($pendingQuotations[$i]['User']['id'],$userPeriod)){
          if ($pendingQuotations[$i]['Currency']['id'] == CURRENCY_CS){
            $userPendingCS[$pendingQuotations[$i]['User']['id']]+=$pendingQuotations[$i]['Quotation']['price_subtotal'];
            
            $userPendingUSD[$pendingQuotations[$i]['User']['id']]+=round($pendingQuotations[$i]['Quotation']['price_subtotal']/$pendingQuotations[$i]['Quotation']['exchange_rate'],2);
            
          }
          elseif ($pendingQuotations[$i]['Currency']['id']==CURRENCY_USD){
            $userPendingUSD[$pendingQuotations[$i]['User']['id']]+=$pendingQuotations[$i]['Quotation']['price_subtotal'];
            $userPendingCS[$pendingQuotations[$i]['User']['id']]+=round($pendingQuotations[$i]['Quotation']['price_subtotal']*$pendingQuotations[$i]['Quotation']['exchange_rate'],2);
          }
        }  
			}
		}
		//pr($pendingQuotations);
		$this->set(compact('pendingQuotations'));
    $this->set(compact('userPendingCS','userPendingUSD'));
		
    if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers){
      $users=$this->User->getActiveVendorAllUserList();
    }
    elseif ($canSeeAllSalesExecutives) {
      $users=$this->User->getActiveSalesUsersOnlyList();
    }
    else {
      $users=$this->User->getUserList($loggedUserId);
    }
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
				'QuotationImage',
				'QuotationProduct'=>array(
					'Product',
				),
				'QuotationRemark'=>array(
					'User',
				),
				'SalesOrder'=>array(
					'InvoiceSalesOrder'=>array(
						'Invoice'=>array(
							//'Product',
						),
					),
				),
				'User',
			),
		);
		$quotation=$this->Quotation->find('first', $options);
		
		$quotationDate=$quotation['Quotation']['quotation_date'];
		$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
		$quotation['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
		
		if (!empty($quotation['SalesOrder'])){
			$quotation['Quotation']['bool_sales_order_present']=true;
		}
		else {
			$quotation['Quotation']['bool_sales_order_present']=false;
		}
		$this->set(compact('quotation'));
		
		$bool_edit_forbidden_because_salesorder_authorized=false;
		if (!empty($quotation['SalesOrder'])){
			//pr($quotation['SalesOrder']);
			if ($quotation['SalesOrder'][0]['bool_authorized']){
				$bool_edit_permission=false;
				$bool_edit_forbidden_because_salesorder_authorized=true;
			}
		}
		$this->set(compact('bool_edit_permission'));
		$this->set(compact('bool_edit_forbidden_because_salesorder_authorized'));
		
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

		$options = [
			'conditions' => [
				'Quotation.id' => $id,
			],
			'contain'=>[
				'Client',
				'Contact',
				'Currency',
				'User',
        'QuotationImage',
				'QuotationProduct'=>['Product'],
				'SalesOrder'=>[
					'InvoiceSalesOrder'=>[
						'Invoice',
					],
					//'Product',
				],				
			],
		];
		
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
		define('NUM_IMAGES','5');
		
		$this->loadModel('Product');
		$this->loadModel('QuotationProduct');
		$this->loadModel('QuotationRemark');
		$this->loadModel('QuotationImage');
		
		$this->loadModel('Client');
		$this->loadModel('Contact');
		$this->loadModel('ClientUser');
		
		$requestProducts=array();
		if ($this->request->is('post')) {
			$quotationDateArray=$this->request->data['Quotation']['quotation_date'];
			//pr($quotationDateArray);
			$quotationDateString=$quotationDateArray['year'].'-'.$quotationDateArray['month'].'-'.$quotationDateArray['day'];
			$quotationDate=date( "Y-m-d", strtotime($quotationDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['QuotationProduct'] as $quotationProduct){
				if ($quotationProduct['product_id']>0){
					$requestProducts[]['QuotationProduct']=$quotationProduct;
					$multiplicationDifference=abs($quotationProduct['product_total_price']-$quotationProduct['product_quantity']*$quotationProduct['product_unit_price']);
					//pr($quotationProduct);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			$boolClientOK=true;
			$boolContactOK=true;
			if ($this->request->data['Client']['bool_new']){
				$this->Client->recursive=-1;
				$existingClientsWithThisName=$this->Client->find('all',array(
					'fields'=>array('Client.id'),
					'conditions'=>array(
						'Client.name'=>$this->request->data['Client']['name'],
					),
				));
				if (!empty($existingClientsWithThisName)){
					$boolClientOK=false;
				}
			}
			if ($this->request->data['Contact']['bool_new']&&!empty($this->request->data['Contact']['first_name'])&&!empty($this->request->data['Contact']['last_name'])){
				$conditions=array(
					'Contact.first_name'=>trim($this->request->data['Contact']['first_name']),
					'Contact.last_name'=>trim($this->request->data['Contact']['last_name']),
				);
				if (!$this->request->data['Client']['bool_new']){
					$conditions[]=array('Contact.client_id'=>$this->request->data['Quotation']['client_id']);
				}
				$existingContactsWithThisNameAndClient=$this->Contact->find('all',array(
					'fields'=>array('Contact.id'),
					'conditions'=>$conditions,
				));
				if (!empty($existingContactsWithThisNameAndClient)){
					$boolContactOK=false;
				}
			}
			
			if ($quotationDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de cotización no puede estar en el futuro!  No se guardó la cotización.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolClientOK){
				$this->Session->setFlash(__('Ya existe un cliente con este nombre, seleccionelo de la lista.  No se guardó la cotización.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolContactOK){
				$this->Session->setFlash(__('Ya existe un contacto con este nombre para este cliente, seleccionelo de la lista y asegure que la casilla nuevo contacto no está seleccionada.  No se guardó la cotización.'), 'default',array('class' => 'error-message'));
			}
			else {				
				$datasource=$this->Quotation->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$client_id=0;
					if (!empty($this->request->data['Quotation']['client_id'])){
						$client_id=$this->request->data['Quotation']['client_id'];
					}
					if ($this->request->data['Client']['bool_new']){
						$this->request->data['Client']['creating_user_id']=$this->Auth->User('id');
            
						$this->Client->create();
						if (!$this->Client->save($this->request->data)) {
							echo "Problema guardando el cliente";
							pr($this->validateErrors($this->Client));
							throw new Exception();
						}
						$client_id=$this->Client->id;
						$this->request->data['Quotation']['client_id']=$client_id;
						
						$this->ClientUser->create();
						$clientUserData=array();
						$clientUserData['ClientUser']['client_id']=$client_id;
						$clientUserData['ClientUser']['user_id']=$this->Auth->User('id');
						if (!$this->ClientUser->save($clientUserData)) {
							echo "Problema guardando la asociación entre cliente y usuario";
							pr($this->validateErrors($this->ClientUser));
							throw new Exception();
						}
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
					
					
					//pr($this->request->data['Document']['url_image'][0]);
					//20170512 CHECK ON SECOND FILE BECAUSE FIRST ONE REMAINS HIDDEN DUE TO JQUERY PROBLEMS
					if (!empty($this->request->data['Document']['url_image'][1]['tmp_name'])){
						$imageOK=$this->uploadFiles('quotations/'.$quotation_id,$this->request->data['Document']['url_image']);
						//echo "image OK<br/>";
						//pr($imageOK);
						if (array_key_exists('urls',$imageOK)){
							for ($img=0;$img<count($imageOK['urls']);$img++){
								$quotationImageArray=array();
								$quotationImageArray['QuotationImage']['quotation_id']=$quotation_id;
								$quotationImageArray['QuotationImage']['url_image']=$imageOK['urls'][$img];
								$this->QuotationImage->create();
								//pr($quotationImageArray);
								if (!$this->QuotationImage->save($quotationImageArray)) {
									echo "Problema guardando la imagen para la cotización";
									pr($this->validateErrors($this->QuotationImage));
									throw new Exception();
								}
							}
						}
					}
					
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
							$productArray['QuotationProduct']['bool_iva']=$quotationProduct['bool_iva'];
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
						$quotationRemarkArray['QuotationRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$quotationRemarkArray['QuotationRemark']['remark_text']=$quotationRemark['remark_text'];
						$quotationRemarkArray['QuotationRemark']['working_days_before_reminder']=$quotationRemark['working_days_before_reminder'];
						$quotationRemarkArray['QuotationRemark']['reminder_date']=$quotationRemark['reminder_date'];
						$quotationRemarkArray['QuotationRemark']['action_type_id']=$quotationRemark['action_type_id'];
						$this->QuotationRemark->create();
						if (!$this->QuotationRemark->save($quotationRemarkArray)) {
							echo "Problema guardando las remarcas para la cotización";
							pr($this->validateErrors($this->QuotationRemark));
							throw new Exception();
						}
					}
					
					$datasource->commit();
					$this->recordUserAction($this->Quotation->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la cotización número ".$this->request->data['Quotation']['quotation_code']);
					
					$this->Session->setFlash(__('The quotation has been saved.'),'default',array('class' => 'success'));				
					return $this->redirect(array('action' => 'view',$quotation_id));
				}
				catch(Exception $e){
					$datasource->rollback();
					// pr($e);
					$this->Session->setFlash(__('No se podía guardar la cotización.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		
		$this->set(compact('requestProducts'));
		
		$users = $this->Quotation->User->find('list',array(
			'conditions'=>array(
				'User.id'=>$this->Auth->User('id'),
        'User.bool_active'=>true
			),
		));
    //20141217 instead of not showing inactive clients and contacts, they are shown to make clear they are present, but they can not be selected because jquery disables them in the list on document ready
		//$clients = $this->Quotation->Client->find('list',['conditions'=>['Client.bool_active'=>true],'order'=>'Client.name']);
		//$contacts = $this->Quotation->Contact->find('list',['conditions'=>['Contact.bool_active'=>true],'order'=>'Contact.first_name']);
    
    $clientConditions=[];
    
    $userrole = $this->Auth->User('role_id');
    if ($userrole !=ROLE_ADMIN && $userrole != ROLE_ASSISTANT){
      //echo "looking up associated ids<br/>";
      //$associatedClientIds=$this->ClientUser->getAssociatedClientsForUser($this->Auth->User('id'));
      //pr($associatedClientIds);
      //$clientConditions['Client.id']=$associatedClientIds;
    }
    
    $clients = $this->Quotation->Client->find('list',['conditions'=>$clientConditions,'order'=>'Client.name']);
		$contacts = $this->Quotation->Contact->find('list',['order'=>'Contact.first_name']);
    $clientConditions['Client.bool_active']=false;
    $inactiveClients=$this->Quotation->Client->find('list',array(
			'fields'=>['Client.id'],
			'conditions'=>$clientConditions,
		));
    $inactiveClients=array_keys($inactiveClients);
    $this->set(compact('inactiveClients'));
    $inactiveContacts=$this->Quotation->Contact->find('list',array(
			'fields'=>array('Contact.id'),
			'conditions'=>array('Contact.bool_active'=>false),
		));
		$inactiveContacts=array_keys($inactiveContacts);
    $this->set(compact('inactiveContacts'));
    
		$currencies = $this->Quotation->Currency->find('list');
		$this->set(compact('users', 'clients', 'contacts', 'currencies'));
		
		$products=$this->Product->find('list',['conditions'=>['Product.bool_active'=>true],'order'=>'Product.name']);
		$this->set(compact('products'));
		
		$this->loadModel('Provider');
		$this->Provider->recursive=-1;
		$providers = $this->Provider->find('all',array(
			'fields'=>array('Provider.id','Provider.name'),
			'conditions'=>['Provider.bool_active'=>true],
			'order'=>'Provider.name',
		));
		$productCategories = $this->Product->ProductCategory->find('list',array('order'=>'ProductCategory.name'));
		$this->set(compact('productCategories', 'providers'));
		
		$quotationDate=date( "Y-m-d");
		$quotationExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
		$exchangeRateQuotation=$quotationExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateQuotation'));
		
		$this->loadModel('ActionType');
		$actionTypes=$this->ActionType->find('list',array('order'=>'ActionType.list_order ASC'));
		$this->set(compact('actionTypes'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
	}

	public function edit($id = null) {
    define('NUM_IMAGES','5');
    
		$this->loadModel('Product');
		$this->loadModel('QuotationProduct');
		$this->loadModel('QuotationRemark');
    $this->loadModel('QuotationImage');
		
		if (!$this->Quotation->exists($id)) {
			throw new NotFoundException(__('Invalid quotation'));
		}
		
		$requestProducts=array();
		if ($this->request->is(array('post', 'put'))) {
			$quotationDateArray=$this->request->data['Quotation']['quotation_date'];
			//pr($quotationDateArray);
			$quotationDateString=$quotationDateArray['year'].'-'.$quotationDateArray['month'].'-'.$quotationDateArray['day'];
			$quotationDate=date( "Y-m-d", strtotime($quotationDateString));
			
			$boolMultiplicationOK=true;
			foreach ($this->request->data['QuotationProduct'] as $quotationProduct){
				if ($quotationProduct['product_id']>0){
					$requestProducts[]['QuotationProduct']=$quotationProduct;
					$multiplicationDifference=abs($quotationProduct['product_total_price']-$quotationProduct['product_quantity']*$quotationProduct['product_unit_price']);
					//pr($quotationProduct);
					if ($multiplicationDifference>=0.01){
						$boolMultiplicationOK=false;
					};
				}
			}
			
			$boolRejectedOK=true;
			if ($this->request->data['Quotation']['bool_rejected']){
				if (!$this->request->data['Quotation']['rejected_reason_id']){
					$boolRejectedOK=false;
				}
			}
			
			if ($quotationDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de cotización no puede estar en el futuro!  No se guardó la cotización.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolRejectedOK){
				$this->Session->setFlash(__('Se debe indicar una razón de caída para cotizaciones caídas.  No se guardó la cotización.'), 'default',array('class' => 'error-message'));
			}
			else {		
				$datasource=$this->Quotation->getDataSource();
				$datasource->begin();
				try {
          if (!empty($this->request->data['QuotationImage'])){
            foreach ($this->request->data['QuotationImage'] as $quotationImage){
              if (!$quotationImage['bool_retain']){
                if (!$this->QuotationImage->delete($quotationImage['id'])){
                  echo "Problema eliminando la imagen de la cotización";
                  pr($this->validateErrors($this->QuotationImage));
                  throw new Exception();
                }
              }            
            }
          }          
        
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
							if (!$this->QuotationProduct->delete($previousQuotationProduct['QuotationProduct']['id'])){
                echo "Problema eliminando el producto anterior de la cotización";
                pr($this->validateErrors($this->QuotationProduct));
                throw new Exception();
              }
						}
					}
					
					if (!$this->Quotation->save($this->request->data)) {
						echo "Problema guardando la cotización";
						pr($this->validateErrors($this->Quotation));
						throw new Exception();
					}
					$quotation_id=$this->Quotation->id;
					
          if (!empty($this->request->data['Document']['url_image'][1]['tmp_name'])){
						$imageOK=$this->uploadFiles('quotations/'.$quotation_id,$this->request->data['Document']['url_image']);
						//echo "image OK<br/>";
						//pr($imageOK);
						if (array_key_exists('urls',$imageOK)){
							for ($img=0;$img<count($imageOK['urls']);$img++){
								$quotationImageArray=[];
								$quotationImageArray['QuotationImage']['quotation_id']=$quotation_id;
								$quotationImageArray['QuotationImage']['url_image']=$imageOK['urls'][$img];
								$this->QuotationImage->create();
								//pr($quotationImageArray);
								if (!$this->QuotationImage->save($quotationImageArray)) {
									echo "Problema guardando la imagen para la cotización";
									pr($this->validateErrors($this->QuotationImage));
									throw new Exception();
								}
							}
						}
					}
          
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
							$productArray['QuotationProduct']['bool_iva']=$quotationProduct['bool_iva'];
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
						//$quotationRemarkArray['QuotationRemark']['remark_datetime']=$quotationRemark['remark_datetime'];
						$quotationRemarkArray['QuotationRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$quotationRemarkArray['QuotationRemark']['remark_text']=$quotationRemark['remark_text'];
						$quotationRemarkArray['QuotationRemark']['working_days_before_reminder']=$quotationRemark['working_days_before_reminder'];
						$quotationRemarkArray['QuotationRemark']['reminder_date']=$quotationRemark['reminder_date'];
						$quotationRemarkArray['QuotationRemark']['action_type_id']=$quotationRemark['action_type_id'];
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
					return $this->redirect(array('action' => 'view',$id));
				} 
				catch(Exception $e){
					$datasource->rollback();
					//pr($e);
					$this->Session->setFlash(__('No se podía guardar la cotización.'), 'default',array('class' => 'error-message'));
				}
			}
		} 
		else {
			$options = array(
				'conditions' => array(
					'Quotation.id' => $id,
				),
				'contain'=>[
					'QuotationProduct'=>['Product'],
          'QuotationImage',
				],
			);
			$this->request->data = $this->Quotation->find('first', $options);
			for ($qp=0;$qp<count($this->request->data['QuotationProduct']);$qp++){
				$this->request->data['QuotationProduct'][$qp]['bool_no_iva']=$this->request->data['QuotationProduct'][$qp]['Product']['bool_no_iva'];
				$requestProducts[]['QuotationProduct']=$this->request->data['QuotationProduct'][$qp];
			}
		}
		
		$this->set(compact('requestProducts'));
		
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
          'User.bool_active'=>true
				),
			));
		}
		else {
			$users = $this->Quotation->User->find('list');
			$boolChangeDueDate=true;
		}
		$this->set(compact('boolChangeDueDate'));
    
		$clients = $this->Quotation->Client->find('list',[
      'conditions'=>[
        'OR'=>[
          ['Client.bool_active'=>true],
          ['Client.id'=>$this->request->data['Quotation']['client_id']]
        ],
      ],
      'order'=>'Client.name'
    ]);
		$contacts = $this->Quotation->Contact->find('list',[
      'conditions'=>[
        'OR'=>[
          ['Contact.bool_active'=>true],
          ['Contact.id'=>$this->request->data['Quotation']['client_id']]
        ],
      ],
      'order'=>'Contact.first_name'
    ]);
		$currencies = $this->Quotation->Currency->find('list');
		$this->set(compact('users', 'clients', 'contacts', 'currencies'));
		
		$rejectedOptions=array();
		$rejectedOptions[0]="Activa";
		$rejectedOptions[1]="Caída";
		$this->set(compact('rejectedOptions'));
		
		$this->loadModel('RejectedReason');
		$rejectedReasons=$this->RejectedReason->find('list',array(
			'order'=>'RejectedReason.list_order',
		));
		$this->set(compact('rejectedReasons'));
		
    $productIdsListForQuotation=[];
    foreach($requestProducts as $quotationProduct){
      if (!in_array($quotationProduct['QuotationProduct']['product_id'],$productIdsListForQuotation)){
        $productIdsListForQuotation[]=$quotationProduct['QuotationProduct']['product_id'];
      }
    }
    
    $products=$this->Product->find('list',[
      'conditions'=>[
        'OR'=>[
          ['Product.bool_active'=>true],
          ['Product.id'=>$productIdsListForQuotation]
        ],
      ],
      'order'=>'Product.name'
    ]);
		$this->set(compact('products'));
		
		//$quotationDate=$this->request->data['Quotation']['quotation_date'];
		//$quotationDateTime= new DateTime($quotationDate);
		$quotationDateAsString=$this->Quotation->deconstruct('quotation_date',$this->request->data['Quotation']['quotation_date']);
		
		$quotationExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDateAsString);
		$exchangeRateQuotation=$quotationExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateQuotation'));
		
		$this->loadModel('ActionType');
		$actionTypes=$this->ActionType->find('list',array('order'=>'ActionType.list_order ASC'));
		$this->set(compact('actionTypes'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
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
        'QuotationRemark',
        'QuotationImage',
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
			$datasource=$this->Quotation->getDataSource();
			$datasource->begin();	
			try {
				//delete all quotation products, quotation remarks and quotation images
				foreach ($quotation['QuotationProduct'] as $quotationProduct){
					if (!$this->Quotation->QuotationProduct->delete($quotationProduct['id'])) {
						echo "Problema al eliminar el producto de la cotización";
						pr($this->validateErrors($this->Quotation->QuotationProduct));
						throw new Exception();
					}
				}
				foreach ($quotation['QuotationRemark'] as $quotationRemark){
					if (!$this->Quotation->QuotationRemark->delete($quotationRemark['id'])) {
						echo "Problema al eliminar la remarca de la cotización";
						pr($this->validateErrors($this->Quotation->QuotationRemark));
						throw new Exception();
					}
				}
				foreach ($quotation['QuotationImage'] as $quotationImage){
					if (!$this->Quotation->QuotationImage->delete($quotationImage['id'])) {
						echo "Problema al eliminar la imagen de la cotización";
						pr($this->validateErrors($this->Quotation->QuotationImage));
						throw new Exception();
					}
				}
				if (!$this->Quotation->delete($id)) {
					echo "Problema al eliminar la cotización";
					pr($this->validateErrors($this->Quotation));
					throw new Exception();
				}
						
				$datasource->commit();
					
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$quotation['Quotation']['id'];
				$deletionArray['Deletion']['reference']=$quotation['Quotation']['quotation_code'];
				$deletionArray['Deletion']['type']='Quotation';
				$this->Deletion->save($deletionArray);
				
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
		
		$this->QuotationProduct->recursive=-1;
		$productsForQuotation=$this->QuotationProduct->find('all',array(
			'fields'=>array(
				'QuotationProduct.product_id','QuotationProduct.product_description',
				'QuotationProduct.product_quantity',
				'QuotationProduct.product_unit_price','QuotationProduct.product_total_price',
				'QuotationProduct.currency_id','QuotationProduct.bool_iva',
			),
			'conditions'=>$quotationProductConditions,
			'contain'=>array(
				'Product'=>array(
					'fields'=>array('Product.id','Product.bool_no_iva'),
					'ProductCategory'=>array(
						'fields'=>array('ProductCategory.id','ProductCategory.name'),
					),
				),
			)
		));
		//pr($productsForQuotation);
		$this->set(compact('productsForQuotation'));
		
		$products=$this->Product->find('list',array('order'=>'Product.name'));
		$this->set(compact('products'));
		
		$salesOrderProductStatuses=$this->SalesOrderProductStatus->find('list');
		$this->set(compact('salesOrderProductStatuses'));
		
		$this->loadModel('ProductCategory');
		$productCategories=$this->ProductCategory->find('list',array('order'=>'ProductCategory.name'));
		$this->set(compact('productCategories'));
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
		
		$this->Quotation->recursive=-1;
		$lastQuotationForMonth=$this->Quotation->find('first',array(
			'fields'=>array('Quotation.quotation_code'),
			'conditions'=>array(
				'Quotation.quotation_code LIKE'=>$userAbbreviation."%",
				'Quotation.quotation_date >='=>$dateFirstDayMonth,
				'Quotation.quotation_date <'=>$dateFirstDayNextMonth,
			),
			'order'=>'Quotation.quotation_date DESC, Quotation.id DESC',
		));
		
		if (!empty($lastQuotationForMonth)){
			//pr($lastQuotationForMonth);
			$positionFirstUnderscore=strlen($userAbbreviation);
			//pr ($positionFirstUnderscore);
			$positionSecondUnderscore=strpos($lastQuotationForMonth['Quotation']['quotation_code'],'_',$positionFirstUnderscore+1);
			//pr ($positionSecondUnderscore);
			$positionThirdUnderscore=strpos($lastQuotationForMonth['Quotation']['quotation_code'],'_',$positionSecondUnderscore+1);
			//pr ($positionThirdUnderscore);
			$lastQuotationNumberForMonth=substr($lastQuotationForMonth['Quotation']['quotation_code'],$positionSecondUnderscore+1,$positionThirdUnderscore-$positionSecondUnderscore-1);
			//pr ($lastQuotationNumberForMonth);
			$newQuotationCode=$userAbbreviation."_".($fullQuotationCount+1)."_".($lastQuotationNumberForMonth+1)."_".$quotationdateday.$quotationdatemonth.substr($quotationdateyear,2,2);
		}
		
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
	
	public function getquotationiva(){
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
			return $quotation['Quotation']['bool_iva'];
		}
		return true;
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
			'order'=>'Quotation.quotation_code',
		));
		//pr($quotationsForClient);
		$this->set(compact('quotationsForClient'));
	}

	public function verReporteGestionDeVentas(){
		$this->loadModel('ExchangeRate');
		$this->loadModel('Invoice');
		$this->loadModel('InvoiceSalesOrder');
		$this->loadModel('SalesOrder');
		$this->loadModel('User');
		
		$this->loadModel('RejectedReason');
		
		$this->loadModel('ActionType');
		$this->loadModel('Client');
		$this->loadModel('QuotationRemark');
		$this->loadModel('SalesOrderRemark');
		
		$user_id=0;
		$currencyId=CURRENCY_USD;
		$userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole'));
		if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT) { 
			$user_id=$this->Auth->User('id');
		}
		//echo "user id is ".$user_id."<br/>";
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
			//if (!empty($_SESSION['userId'])){
			//	$user_id=$_SESSION['userId'];
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
		$_SESSION['userId']=$user_id;
		//echo "user id is ".$user_id."<br/>";
		$this->set(compact('startDate','endDate'));
		$this->set(compact('currencyId','user_id'));
		
		$actionTypes=$this->ActionType->find('list');
		//pr($actionTypes);
		$this->set(compact('actionTypes'));
		
		$rejectedReasons=$this->RejectedReason->find('list');
		//pr($rejectedReasons);
		$this->set(compact('rejectedReasons'));
		
		//$clients=$this->Client->find('list',['conditions'=>['Client.bool_active']=>true],'order'=>'Client.name ASC']);
    $clients=$this->Client->find('list',[
      'conditions'=>['Client.bool_active'=>true],
      'order'=>'Client.name ASC'
    ]);
		//pr($clients);
		$this->set(compact('clients'));
		//pr($userConditions);
		$selectedUsers=$this->User->getAllActiveListUsers($user_id);
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
						'conditions'=>array(
							'SalesOrder.bool_annulled'=>false,
						),
						'InvoiceSalesOrder'=>array(
							'Invoice',
						),
					),
					'RejectedReason',
				),
				'order'=>'Quotation.quotation_date,Quotation.quotation_code',
			));
			//pr($quotations);
			$quotationTotals=array();
			
			$quotationTotals['value_quotations']=0;
			$quotationTotals['quantity_quotations']=0;
			
			$quotationTotals['value_rejected']=0;
			$quotationTotals['quantity_rejected']=0;
			$quotationTotals['value_pending']=0;
			$quotationTotals['quantity_pending']=0;
			
			$quotationTotals['value_sales_orders']=0;
			$quotationTotals['quantity_sales_orders']=0;
			$quotationTotals['value_invoices']=0;
			$quotationTotals['quantity_invoices']=0;
			
			$quotationRejections=array();
			
			foreach ($rejectedReasons as $id=>$name){
				$quotationRejections[$id]=0;
			}
			
			if (!empty($quotations)){
				for ($q=0;$q<count($quotations);$q++){
					$quotationDate=$quotations[$q]['Quotation']['quotation_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
					$quotations[$q]['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				
					$exchangeRateFactor=1;
					if ($currencyId!=$quotations[$q]['Quotation']['currency_id']){
						if ($currencyId==CURRENCY_CS){
							$exchangeRateFactor=$exchangeRate['ExchangeRate']['rate'];
						}
						else if ($currencyId==CURRENCY_USD){
							$exchangeRateFactor=1/$exchangeRate['ExchangeRate']['rate'];
						}
					}
					
					$quotationTotals['value_quotations']+=round($quotations[$q]['Quotation']['price_subtotal']*$exchangeRateFactor,2);
					$quotationTotals['quantity_quotations']+=1;
					
					if ($quotations[$q]['Quotation']['bool_rejected']){
						$quotationTotals['value_rejected']+=round($quotations[$q]['Quotation']['price_subtotal']*$exchangeRateFactor,2);
						$quotationTotals['quantity_rejected']+=1;
						if (!empty($quotations[$q]['Quotation']['rejected_reason_id'])){
							$quotationRejections[$quotations[$q]['Quotation']['rejected_reason_id']]+=1;
						}
						else {
							//pr($quotations[$q]['Quotation']);
						}
						
					}
					else {
						if (empty($quotations[$q]['SalesOrder'])){
							$quotationTotals['value_pending']+=round($quotations[$q]['Quotation']['price_subtotal']*$exchangeRateFactor,2);
							$quotationTotals['quantity_pending']+=1;
						}
						else {
							for ($qso=0;$qso<count($quotations[$q]['SalesOrder']);$qso++){
								$salesOrderDate=$quotations[$q]['SalesOrder'][$qso]['sales_order_date'];
								$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($salesOrderDate);
								$quotations[$q]['SalesOrder'][$qso]['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
								
								$exchangeRateFactor=1;
								if ($currencyId!=$quotations[$q]['SalesOrder'][$qso]['currency_id']){
									if ($currencyId==CURRENCY_CS){
										$exchangeRateFactor=$exchangeRate['ExchangeRate']['rate'];
									}
									else if ($currencyId==CURRENCY_USD){
										$exchangeRateFactor=1/$exchangeRate['ExchangeRate']['rate'];
									}
								}
								$quotationTotals['value_sales_orders']+=round($quotations[$q]['SalesOrder'][$qso]['price_subtotal']*$exchangeRateFactor,2);
								$quotationTotals['quantity_sales_orders']+=1;
								if (!empty($quotations[$q]['SalesOrder'][$qso]['InvoiceSalesOrder'])){
									for ($i=0;$i<count($quotations[$q]['SalesOrder'][$qso]['InvoiceSalesOrder']);$i++){
										//pr ($quotations[$q]['SalesOrder'][$qso]['InvoiceSalesOrder'][$i]);
										$invoiceDate=$quotations[$q]['SalesOrder'][$qso]['InvoiceSalesOrder'][$i]['Invoice']['invoice_date'];
										$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
										$quotations[$q]['SalesOrder'][$qso]['InvoiceSalesOrder'][$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
										
										$exchangeRateFactor=1;
										if ($currencyId!=$quotations[$q]['SalesOrder'][$qso]['InvoiceSalesOrder'][$i]['Invoice']['currency_id']){
											if ($currencyId==CURRENCY_CS){
												$exchangeRateFactor=$exchangeRate['ExchangeRate']['rate'];
											}
											else if ($currencyId==CURRENCY_USD){
												$exchangeRateFactor=1/$exchangeRate['ExchangeRate']['rate'];
											}
										}
										$quotationTotals['value_invoices']+=round($quotations[$q]['SalesOrder'][$qso]['InvoiceSalesOrder'][$i]['Invoice']['price_subtotal']*$exchangeRateFactor,2);
										$quotationTotals['quantity_invoices']+=1;
									}
								}
							}
						}
					}
				}
			}
			
			$this->QuotationRemark->recursive=-1;
			$relevantClients=array();
			foreach ($actionTypes as $id=>$name){
				$actionsForThisActionType=$this->QuotationRemark->find('all',array(
					'fields'=>array('QuotationRemark.id'),
					'conditions'=>array(
						'QuotationRemark.action_type_id'=>$id,
						'QuotationRemark.remark_datetime >='=>$startDate,
						'QuotationRemark.remark_datetime <'=>$endDatePlusOne,
						'QuotationRemark.user_id'=>$selectedUsers[$u]['User']['id'],
					),
					'contain'=>array(
						'Quotation'=>array(
							'fields'=>array('Quotation.client_id'),
						),
					),
				));
				if (!empty($actionsForThisActionType)){
					foreach ($actionsForThisActionType as $action){
						$relevantClients[]=$action['Quotation']['client_id'];
						//echo "for quotation remark id ".$action['QuotationRemark']['id']." the client id is ".$action['Quotation']['client_id']."<br/>";
					}
				}
				$actionsForThisActionType=$this->SalesOrderRemark->find('all',array(
					'fields'=>array('SalesOrderRemark.id'),
					'conditions'=>array(
						'SalesOrderRemark.action_type_id'=>$id,
						'SalesOrderRemark.remark_datetime >='=>$startDate,
						'SalesOrderRemark.remark_datetime <'=>$endDatePlusOne,
						'SalesOrderRemark.user_id'=>$selectedUsers[$u]['User']['id'],
					),
					'contain'=>array(
						'SalesOrder'=>array(
							'fields'=>array('SalesOrder.id'),
							'Quotation'=>array(
								'fields'=>array('Quotation.client_id'),
							),
						),
					),
				));
				if (!empty($actionsForThisActionType)){
					foreach ($actionsForThisActionType as $action){
						if (!empty($action['SalesOrder']['id'])){
							$relevantClients[]=$action['SalesOrder']['Quotation']['client_id'];
							//echo "for quotation remark id ".$action['SalesOrderRemark']['id']." the client id is ".$action['SalesOrder']['Quotation']['client_id']."<br/>";
						}
					}
				}
			}
			//pr($relevantClients);
			$relevantClients=array_unique($relevantClients);
			//pr($relevantClients);
			$clientActions=array();
			foreach ($relevantClients as $clientId){
				$quotationActionsForClient=array();
				$quotationIdsForClient=$this->Quotation->find('list',array(
					'fields'=>array('Quotation.id'),
					'conditions'=>array(
						'Quotation.client_id'=>$clientId,
					),
				));
				foreach ($actionTypes as $actionTypeId=> $actionTypeName){
					$quotationRemarksForClientAndActionType=$this->QuotationRemark->find('list',array(
						'conditions'=>array(
							'QuotationRemark.action_type_id'=>$actionTypeId,
							'QuotationRemark.remark_datetime >='=>$startDate,
							'QuotationRemark.remark_datetime <'=>$endDatePlusOne,
							'QuotationRemark.user_id'=>$selectedUsers[$u]['User']['id'],
							'QuotationRemark.quotation_id'=>$quotationIdsForClient,
						),
					));
					//echo "client id is ".$clientId." and action type id is ".$actionTypeId."<br/>";
					//pr($quotationRemarksForClientAndActionType);
					if (!empty($quotationRemarksForClientAndActionType)){
						$clientActions[$clientId]['quotationActionsForClients'][$actionTypeId]=count($quotationRemarksForClientAndActionType);
					}
					else {
						$clientActions[$clientId]['quotationActionsForClients'][$actionTypeId]=0;
					}
				}
				$salesOrderActionsForClient=array();
				$salesOrderIdsForClient=$this->SalesOrder->find('list',array(
					'fields'=>array('SalesOrder.id'),
					'conditions'=>array(
						'SalesOrder.quotation_id'=>$quotationIdsForClient,
					),
				));
				foreach ($actionTypes as $actionTypeId=> $actionTypeName){
					$salesOrderRemarksForClientAndActionType=$this->SalesOrderRemark->find('list',array(
						'conditions'=>array(
							'SalesOrderRemark.action_type_id'=>$actionTypeId,
							'SalesOrderRemark.remark_datetime >='=>$startDate,
							'SalesOrderRemark.remark_datetime <'=>$endDatePlusOne,
							'SalesOrderRemark.user_id'=>$selectedUsers[$u]['User']['id'],
							'SalesOrderRemark.sales_order_id'=>$salesOrderIdsForClient,
						),
					));
					//pr($salesOrderRemarksForClientAndActionType);
					if (!empty($salesOrderRemarksForClientAndActionType)){
						$clientActions[$clientId]['salesOrderActionsForClients'][$actionTypeId]=count($salesOrderRemarksForClientAndActionType);
					}
					else {
						$clientActions[$clientId]['salesOrderActionsForClients'][$actionTypeId]=0;
					}
				}
			}
			//pr($clientActions);
			
			$clientsCreatedForPeriod=$this->Client->find('all',array(
				'conditions'=>array(
					'Client.bool_active'=>true,
					'Client.created >='=>$startDate,
					'Client.created <'=>$endDatePlusOne,
					'Client.creating_user_id'=>$selectedUsers[$u]['User']['id'],
				),
			));
			
			$selectedUsers[$u]['Quotations']=$quotations;
			$selectedUsers[$u]['QuotationTotals']=$quotationTotals;
			$selectedUsers[$u]['QuotationRejections']=$quotationRejections;
			$selectedUsers[$u]['ClientActions']=$clientActions;
			$selectedUsers[$u]['CreatedClients']=$clientsCreatedForPeriod;
			
			$allQuotationIdsForUser=$this->Quotation->find('list',array(
				'fields'=>array('Quotation.id'),
				'conditions'=>array(
					'Quotation.quotation_date <'=>$endDatePlusOne,
					'Quotation.user_id'=>$selectedUsers[$u]['User']['id'],
				),
			));
			
			// now get all sales orders for the period, also for quotations outside the period
			$salesOrders=$this->SalesOrder->find('all',array(
				'conditions'=>array(
					'SalesOrder.sales_order_date >='=>$startDate,
					'SalesOrder.sales_order_date <'=>$endDatePlusOne,
					'SalesOrder.quotation_id'=>$allQuotationIdsForUser,
				),
				'contain'=>array(
					'Currency',
					'Quotation'=>array(
						'Client',
						'Contact',
					),
					'InvoiceSalesOrder'=>array(
						'Invoice',
					),
				),
				'order'=>'SalesOrder.sales_order_date,SalesOrder.sales_order_code',
			));
			$salesOrderTotals=array();
			$salesOrderTotals['value_sales_orders']=0;
			$salesOrderTotals['quantity_sales_orders']=0;
			
			$salesOrderTotals['value_annulled']=0;
			$salesOrderTotals['quantity_annulled']=0;
			$salesOrderTotals['value_pending']=0;
			$salesOrderTotals['quantity_pending']=0;
			
			$salesOrderTotals['value_invoices']=0;
			$salesOrderTotals['quantity_invoices']=0;
			
			if (!empty($salesOrders)){
				for ($so=0;$so<count($salesOrders);$so++){
					$salesOrderDate=$salesOrders[$so]['SalesOrder']['sales_order_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($salesOrderDate);
					$salesOrders[$so]['SalesOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					
					$exchangeRateFactor=1;
					if ($currencyId!=$salesOrders[$so]['SalesOrder']['currency_id']){
						if ($currencyId==CURRENCY_CS){
							$exchangeRateFactor=$exchangeRate['ExchangeRate']['rate'];
						}
						else if ($currencyId==CURRENCY_USD){
							$exchangeRateFactor=1/$exchangeRate['ExchangeRate']['rate'];
						}
					}
				
					$salesOrderTotals['value_sales_orders']+=round($salesOrders[$so]['SalesOrder']['price_subtotal']*$exchangeRateFactor,2);
					$salesOrderTotals['quantity_sales_orders']+=1;
					
					if ($salesOrders[$so]['SalesOrder']['bool_annulled']){
						$salesOrderTotals['value_annulled']+=round($salesOrders[$so]['SalesOrder']['price_subtotal']*$exchangeRateFactor,2);
						$salesOrderTotals['quantity_annulled']+=1;
					}
					else {
						if (!empty($salesOrders[$so]['InvoiceSalesOrder'])){
							foreach ($salesOrders[$so]['InvoiceSalesOrder'] as $invoiceSalesOrder){
								$exchangeRateFactorForInvoice=1;
								if ($invoiceSalesOrder['Invoice']['currency_id']!=$salesOrders[$so]['SalesOrder']['currency_id']){
									if ($salesOrders[$so]['SalesOrder']['currency_id']==CURRENCY_CS){
										if ($currencyId==CURRENCY_USD){
											$exchangeRateFactorForInvoice=1;
										}
										else if ($currencyId==CURRENCY_CS){
											$exchangeRateFactorForInvoice=$exchangeRate['ExchangeRate']['rate'];
										}
									}
									elseif ($salesOrders[$so]['SalesOrder']['currency_id']==CURRENCY_USD){
										if ($currencyId==CURRENCY_CS){
											$exchangeRateFactorForInvoice=1;
										}
										else if ($currencyId==CURRENCY_USD){
											$exchangeRateFactorForInvoice=1/$exchangeRate['ExchangeRate']['rate'];
										}
									}
								}
								$salesOrderTotals['value_invoices']+=round($invoiceSalesOrder['Invoice']['price_subtotal']*$exchangeRateFactorForInvoice,2);
								$salesOrderTotals['quantity_invoices']+=1;
							}							
						}
						else {
							$salesOrderTotals['value_pending']+=round($salesOrders[$so]['SalesOrder']['price_subtotal']*$exchangeRateFactor,2);
							$salesOrderTotals['quantity_pending']+=1;
						}
					}
				}
			}
			$selectedUsers[$u]['SalesOrders']=$salesOrders;
			$selectedUsers[$u]['SalesOrderTotals']=$salesOrderTotals;
			
			$allInvoiceIdsForUser=$this->InvoiceSalesOrder->find('list',array(
				'fields'=>array('InvoiceSalesOrder.invoice_id'),
				'conditions'=>array(
					'InvoiceSalesOrder.user_id'=>$selectedUsers[$u]['User']['id'],
				),
			));
			
			$invoices=$this->Invoice->find('all',array(
				'conditions'=>array(
					'Invoice.invoice_date >='=>$startDate,
					'Invoice.invoice_date <'=>$endDatePlusOne,
					'Invoice.id'=>$allInvoiceIdsForUser,
				),
				'contain'=>array(
					'Currency',
					'Client',
				),
				'order'=>'Invoice.invoice_date,Invoice.invoice_code',
			));
			$invoiceTotals=array();
			$invoiceTotals['value_invoices']=0;
			$invoiceTotals['quantity_invoices']=0;
			
			$invoiceTotals['value_paid']=0;
			$invoiceTotals['quantity_paid']=0;
			$invoiceTotals['value_payment_pending']=0;
			$invoiceTotals['quantity_payment_pending']=0;
			
			if (!empty($invoices)){
				for ($i=0;$i<count($invoices);$i++){
					$invoiceDate=$invoices[$i]['Invoice']['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					$invoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				
					$exchangeRateFactor=1;
					if ($currencyId!=$invoices[$i]['Invoice']['currency_id']){
						if ($currencyId==CURRENCY_CS){
							$exchangeRateFactor=$exchangeRate['ExchangeRate']['rate'];
						}
						else if ($currencyId==CURRENCY_USD){
							$exchangeRateFactor=1/$exchangeRate['ExchangeRate']['rate'];
						}
					}
				
					$invoiceTotals['value_invoices']+=round($invoices[$i]['Invoice']['price_subtotal']*$exchangeRateFactor,2);
					$invoiceTotals['quantity_invoices']+=1;
					
					if ($invoices[$i]['Invoice']['bool_paid']){
						$invoiceTotals['value_paid']+=round($invoices[$i]['Invoice']['price_subtotal']*$exchangeRateFactor,2);
						$invoiceTotals['quantity_paid']+=1;
					}
					else {
						$invoiceTotals['value_payment_pending']+=round($invoices[$i]['Invoice']['price_subtotal']*$exchangeRateFactor,2);
						$invoiceTotals['quantity_payment_pending']+=1;						
					}
				}
			}
			$selectedUsers[$u]['Invoices']=$invoices;
			$selectedUsers[$u]['InvoiceTotals']=$invoiceTotals;
		}
		
		$users=$this->User->find('list',[
      'conditions'=>[
        'bool_active'=>true,
        'bool_show_in_list'=>true,
      ],
      'order'=>'User.username ASC'
    ]);
		
		$this->set(compact('users','selectedUsers'));	
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}

	public function guardarReporteGestionDeVentas() {
		$exportData=$_SESSION['reporteGestionDeVentas'];
		$this->set(compact('exportData'));
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
			//if (!empty($_SESSION['userId'])){
			//	$user_id=$_SESSION['userId'];
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
		$_SESSION['userId']=$user_id;
		
		$this->set(compact('startDate','endDate','currencyId','user_id'));
		$this->loadModel('User');
		$userConditions=array();
		$this->User->recursive=-1;
		
		//if ($user_id>0){
		//	$userConditions['User.id']=$user_id;
		//}
		
		$selectedUsers=$this->User->getAllActiveListUsers($user_id);
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
			$totalPriceCS=0;
			$totalPriceUSD=0;
			if (!empty($quotations)){
				$this->loadModel('ExchangeRate');
				for ($q=0;$q<count($quotations);$q++){
					$quotationDate=$quotations[$q]['Quotation']['quotation_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
					$quotations[$q]['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					
					if (!empty($quotations[$q]['SalesOrder'])){
						$quotations[$q]['Quotation']['bool_sales_order_present']=true;
						$quotations[$q]['Quotation']['dropped']=0;
						$quotations[$q]['Quotation']['sold']=100;
					}
					else {
						$quotations[$q]['Quotation']['bool_sales_order_present']=false;
						if ($quotations[$q]['Quotation']['bool_rejected']){
							$quotations[$q]['Quotation']['dropped']=100;
							$quotations[$q]['Quotation']['sold']=0;
						}
						else {
							$quotations[$q]['Quotation']['dropped']=0;
							$quotations[$q]['Quotation']['sold']=0;
						}
					}
					if ($quotations[$q]['Quotation']['currency_id']==CURRENCY_CS){
						$totalPriceCS+=$quotations[$q]['Quotation']['price_subtotal'];
						//added calculation of totals in US$
						$totalPriceUSD+=round($quotations[$q]['Quotation']['price_subtotal']/$quotations[$q]['Quotation']['exchange_rate'],2);
					}
					else if ($quotations[$q]['Quotation']['currency_id']==CURRENCY_USD){
						$totalPriceUSD+=$quotations[$q]['Quotation']['price_subtotal'];
						//added calculation of totals in C$
						$totalPriceCS+=round($quotations[$q]['Quotation']['price_subtotal']*$quotations[$q]['Quotation']['exchange_rate'],2);
					}
				}
			}
			$selectedUsers[$u]['Quotations']=$quotations;
			$selectedUsers[$u]['price_subtotal_CS']=$totalPriceCS;
			$selectedUsers[$u]['price_subtotal_USD']=$totalPriceUSD;
		}
		
		$users=$this->User->getActiveUserList();
		
		$this->set(compact('users','selectedUsers'));	
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}

	public function guardarReporteCotizacionesPorEjecutivo() {
		$exportData=$_SESSION['reporteCotizacionesPorEjecutivo'];
		$this->set(compact('exportData'));
	}

	public function verReporteCotizacionesPorCategoria(){
    $currencyId=CURRENCY_USD;
		$product_category_id=0;
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
			$product_category_id=$this->request->data['Report']['product_category_id'];
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
		
		$this->set(compact('startDate','endDate','currencyId','product_category_id','product_id'));
		
		$this->loadModel('Product');
		$this->loadModel('QuotationProduct');
		$this->loadModel('ProductCategory');
		
		$productCategoryConditions=array();
		$this->ProductCategory->recursive=-1;
		//if ($product_category_id>0){
		//	$productCategoryConditions['ProductCategory.id']=$product_category_id;
		//}
		$selectedProductCategories=$this->ProductCategory->find('all',array(
			'fields'=>array('ProductCategory.id','ProductCategory.name'),
			'conditions'=>$productCategoryConditions,
			'order'=>'ProductCategory.name',
		));
		//pr($selectedProductCategories);
		
		$productConditions=['Product.bool_active'=>true];
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
		for ($pc=0;$pc<count($selectedProductCategories);$pc++){
			// get the product ids in this product category and the selectedProductList
			$productsInProductCategory=$this->Product->find('list',array(
				'fields'=>'Product.id',
				'conditions'=>array(
					'Product.product_category_id'=>$selectedProductCategories[$pc]['ProductCategory']['id'],
					'Product.id'=>$selectedProductList,
				)
			));
			//pr($productsInProductCategory);
			// get the products of the selectedproductcategory and the selectedProductList present in quotations for the selected period
			$quotationProducts=$this->QuotationProduct->find('all',array(
				'fields'=>array(
					'QuotationProduct.id','QuotationProduct.product_quantity',
					'QuotationProduct.currency_id','QuotationProduct.product_total_price',
				),
				'conditions'=>array(
					'QuotationProduct.product_id'=>$productsInProductCategory,
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
							'Quotation.id','Quotation.quotation_code','Quotation.quotation_date','Quotation.bool_rejected',
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
			$totalPriceCS=0;
			$totalPriceUSD=0;
			if (!empty($quotationProducts)){
				$this->loadModel('ExchangeRate');
				for ($qp=0;$qp<count($quotationProducts);$qp++){
					$quotationDate=$quotationProducts[$qp]['Quotation']['quotation_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
					$quotationProducts[$qp]['QuotationProduct']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					//$quotationProducts[$qp]['QuotationProduct']['dropped']=$this->QuotationProduct->getDroppedPercentageForQuotationProduct($quotationProducts[$qp]['QuotationProduct']['id']);
					//$quotationProducts[$qp]['QuotationProduct']['sold']=$this->QuotationProduct->getSoldPercentageForQuotationProduct($quotationProducts[$qp]['QuotationProduct']['id']);
					if (!empty($quotationProducts[$qp]['Quotation']['SalesOrder'])){
						$quotationProducts[$qp]['Quotation']['bool_sales_order_present']=true;
						$quotationProducts[$qp]['QuotationProduct']['dropped']=0;
						$quotationProducts[$qp]['QuotationProduct']['sold']=100;
					}
					else {
						$quotationProducts[$qp]['Quotation']['bool_sales_order_present']=false;
						if ($quotationProducts[$qp]['Quotation']['bool_rejected']){
							$quotationProducts[$qp]['QuotationProduct']['dropped']=100;
							$quotationProducts[$qp]['QuotationProduct']['sold']=0;
						}
						else {
							$quotationProducts[$qp]['QuotationProduct']['dropped']=0;
							$quotationProducts[$qp]['QuotationProduct']['sold']=0;
						}
					}
					if ($quotationProducts[$qp]['QuotationProduct']['currency_id']==CURRENCY_CS){
						$totalPriceCS+=$quotationProducts[$qp]['QuotationProduct']['product_total_price'];
						//added calculation of totals in US$
						$totalPriceUSD+=round($quotationProducts[$qp]['QuotationProduct']['product_total_price']/$quotationProducts[$qp]['QuotationProduct']['exchange_rate'],2);
					}
					else if ($quotationProducts[$qp]['QuotationProduct']['currency_id']==CURRENCY_USD){
						$totalPriceUSD+=$quotationProducts[$qp]['QuotationProduct']['product_total_price'];
						//added calculation of totals in C$
						$totalPriceCS+=round($quotationProducts[$qp]['QuotationProduct']['product_total_price']*$quotationProducts[$qp]['QuotationProduct']['exchange_rate'],2);
					}
				}
			}
			$selectedProductCategories[$pc]['QuotationProducts']=$quotationProducts;
			$selectedProductCategories[$pc]['total_price_CS']=$totalPriceCS;
			$selectedProductCategories[$pc]['total_price_USD']=$totalPriceUSD;
		}
		//pr($selectedProductCategories);
		$this->set(compact('selectedProductCategories'));
		
		$productCategories=$this->ProductCategory->find('list');
		$this->set(compact('productCategories'));	
		
		$products=$this->Product->find('list',[
      'conditions'=>['Product.bool_active'=>true],
      'order'=>'Product.name ASC'
    ]);
		$this->set(compact('products'));	
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}
	
	public function guardarReporteCotizacionesPorCategoria() {
		$exportData=$_SESSION['reporteCotizacionesPorCategoria'];
		$this->set(compact('exportData'));
	}

	public function verReporteCotizacionesPorCliente(){
    $userId=$this->Auth->User('id');
		$userrole = $this->Auth->User('role_id');
		if ($userrole==ROLE_ADMIN||$userrole==ROLE_ASSISTANT) { 
			$userId=0;
		}
		
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
			
      $userId=$this->request->data['Report']['user_id'];
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
		
		$this->set(compact('startDate','endDate','userId','currencyId','client_id'));
		
    $this->loadModel('Client');
		$clientConditions=array();
		$this->Client->recursive=-1;
		$clientConditions=array(
      'Client.bool_active'=>true
    );
		$clientCount=	$this->Client->find('count', array(
			'fields'=>array('Client.id'),
			'conditions' => $clientConditions,
		));
		
		$allClients=$this->Client->find('all',array(
			'conditions' => $clientConditions,
			'contain'=>array(				
				'ClientUser'=>array(
					'User',
					'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
          'limit'=>1,
				),
			),
			'order'=>'Client.name ASC',
			'limit'=>($clientCount!=0?$clientCount:1),
		));
    //pr($allClients);
    $clientIds=array();	
    for ($c=0;$c<count($allClients);$c++){
			if (empty($userId)||$allClients[$c]['ClientUser'][0]['bool_assigned']){
        $thisClient=$allClients[$c];
        $clientId=$thisClient['Client']['id'];
        $clientIds[]=$clientId;        
      }
		}
    $clients=$this->Client->find('list',array(
			'conditions'=>array(
				'Client.id'=>$clientIds,
			),
      'order'=>'Client.name',
		));
		
    
    $clientConditions=array();
    if ($client_id>0){
      $clientConditions[]=array('Client.id'=>$client_id);
    }
		$selectedClients=$this->Client->find('all',array(
			'fields'=>array('Client.id','Client.name'),
			'conditions'=>$clientConditions,
			'order'=>'Client.name',
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
			$totalPriceCS=0;
			$totalPriceUSD=0;
			if (!empty($quotations)){
				$this->loadModel('ExchangeRate');
				
				for ($q=0;$q<count($quotations);$q++){
					$quotationDate=$quotations[$q]['Quotation']['quotation_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
					$quotations[$q]['Quotation']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					
					//$quotations[$q]['Quotation']['dropped']=$this->Quotation->getDroppedPercentageForQuotation($quotations[$q]['Quotation']['id']);
					//$quotations[$q]['Quotation']['sold']=$this->Quotation->getSoldPercentageForQuotation($quotations[$q]['Quotation']['id']);
					
					if (!empty($quotations[$q]['SalesOrder'])){
						$quotations[$q]['Quotation']['bool_sales_order_present']=true;
						$quotations[$q]['Quotation']['dropped']=0;
						$quotations[$q]['Quotation']['sold']=100;
					}
					else {
						$quotations[$q]['Quotation']['bool_sales_order_present']=false;
						if ($quotations[$q]['Quotation']['bool_rejected']){
							$quotations[$q]['Quotation']['dropped']=100;
							$quotations[$q]['Quotation']['sold']=0;
						}
						else {
							$quotations[$q]['Quotation']['dropped']=0;
							$quotations[$q]['Quotation']['sold']=0;
						}
					}
					if ($quotations[$q]['Quotation']['currency_id']==CURRENCY_CS){
						$totalPriceCS+=$quotations[$q]['Quotation']['price_subtotal'];
						//added calculation of totals in US$
						$totalPriceUSD+=round($quotations[$q]['Quotation']['price_subtotal']/$quotations[$q]['Quotation']['exchange_rate'],2);
					}
					else if ($quotations[$q]['Quotation']['currency_id']==CURRENCY_USD){
						$totalPriceUSD+=$quotations[$q]['Quotation']['price_subtotal'];
						//added calculation of totals in C$
						$totalPriceCS+=round($quotations[$q]['Quotation']['price_subtotal']*$quotations[$q]['Quotation']['exchange_rate'],2);
					}
				}
			}
			$selectedClients[$c]['Quotations']=$quotations;
			$selectedClients[$c]['subtotal_price_CS']=$totalPriceCS;
			$selectedClients[$c]['subtotal_price_USD']=$totalPriceUSD;
		}
		
		$this->set(compact('clients','selectedClients'));	
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
    
    $userConditions=['User.bool_active'=>true];
    if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT){
			$userConditions['User.id']=$this->Auth->User('id');
		}
		$users=$this->User->find('list',array(
			'conditions'=>$userConditions,
			'order'=>'User.username'
		));
		$this->set(compact('users'));  
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
			$datasource=$this->SalesOrder->getDataSource();
			$datasource->begin();
			try {
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
