<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

define('MAX_CONTACTS',25);

class ClientsController extends AppController {

	public $components = array('Paginator');
	public $helpers = array('PhpExcel');
  
  public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('getclientlist','getclientlistforclientname','getclientinfo','saveclient');		
	}

	public function index() {
		$this->loadModel('ClientUser');
		$this->loadModel('Quotation');
		$this->loadModel('Invoice');
    
    $this->loadModel('Currency');
		$this->loadModel('ExchangeRate');
		
    $this->loadModel('User');
    $this->loadModel('UserPageRight');
		
		$loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    //echo 'loggedUserId is '.$loggedUserId.'<br/>';
    //echo 'userRoleId is '.$userRoleId.'<br/>';
    
    $canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Quotations','verReporteGestionDeVentas');
    $this->set(compact('canSeeAllUsers'));
    
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Quotations','verReporteGestionDeVentas');
    $this->set(compact('canSeeAllSalesExecutives'));
    //echo 'can see all sales executives:'.$canSeeAllSalesExecutives.'<br/>';
	
    if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives){
			$userId=0;
		}
    else {
      $userId=$loggedUserId;
    }
		
    define('AGGREGATES_NONE','0');
		define('AGGREGATES_INVOICES_QUOTATIONS','1');
		define('AGGREGATES_QUOTATIONS_INVOICES','2');
    $aggregateOptions=[
			AGGREGATES_NONE=>'No mostrar acumulados, ordenar por nombre cliente',
			AGGREGATES_INVOICES_QUOTATIONS=>'Mostrar facturas y cotizaciones, ordenado por ventas, cotizaciones y cliente',
			AGGREGATES_QUOTATIONS_INVOICES=>'Mostrar cotizaciones y facturas, ordenado por cotizaciones, ventas y cliente',
		];
		$this->set(compact('aggregateOptions'));
    
    
		define('SHOW_CLIENT_SALES_YES','0');
		define('SHOW_CLIENT_SALES_OR_NOT','1');
    define('SHOW_CLIENT_SALES_NO','2');
    $salesDisplayOptions=[
			SHOW_CLIENT_SALES_YES=>'Solo mostrar clientes con ventas',
			SHOW_CLIENT_SALES_OR_NOT=>'Mostrar todos clientes',
			SHOW_CLIENT_SALES_NO=>'Solo mostrar clientes sin ventas',
		];
		$this->set(compact('salesDisplayOptions'));
    
		
    define('SHOW_CLIENT_ACTIVE_YES','0');
		define('SHOW_CLIENT_ACTIVE_ALL','1');
		define('SHOW_CLIENT_ACTIVE_NO','2');
    $activeDisplayOptions=[
			'0'=>'Mostrar solamente clientes activos',
			'1'=>'Mostrar clientes activos y no activos',
			'2'=>'Mostrar clientes desactivados',
		];
		$this->set(compact('activeDisplayOptions'));
		
		define('SHOW_CLIENT_VIP_ALL','0');
		define('SHOW_CLIENT_VIP_YES','1');
		define('SHOW_CLIENT_VIP_NO','2');
    $vipDisplayOptions=[
			'0'=>'Mostrar todos clientes',
			'1'=>'Mostrar solamente clientes VIP',
			'2'=>'Mostrar clientes no VIP',
		];
		$this->set(compact('vipDisplayOptions'));
    
		$currencyId=CURRENCY_USD;
		
    $salesDisplayOptionId=SHOW_CLIENT_SALES_YES;
		$activeDisplayOptionId=SHOW_CLIENT_ACTIVE_YES;
		$vipDisplayOptionId=SHOW_CLIENT_VIP_ALL;
		$searchTerm="";
		
    if ($userRoleId==ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives) { 
			$aggregateOptionId=AGGREGATES_INVOICES_QUOTATIONS;
		}
    else {
      $aggregateOptionId=AGGREGATES_NONE;
    }
    
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$currencyId=$this->request->data['Report']['currency_id'];
			$userId=$this->request->data['Report']['user_id'];
			
      $aggregateOptionId=$this->request->data['Report']['aggregate_option_id'];
      $salesDisplayOptionId=$this->request->data['Report']['sales_display_option_id'];
			$activeDisplayOptionId=$this->request->data['Report']['active_display_option_id'];
			$vipDisplayOptionId=$this->request->data['Report']['vip_display_option_id'];
      
			$searchTerm=$this->request->data['Report']['searchterm'];
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
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('currencyId','userId'));
		$this->set(compact('aggregateOptionId','salesDisplayOptionId','activeDisplayOptionId','vipDisplayOptionId','aggregateOptionId'));
		$this->set(compact('searchTerm'));
		
		$clientConditions=[];
    $clientUserConditions=[];
    
		if ($userId > 0) { 
      // in this case the user_id is set to the logged in user explicitly
      // the clients are limited to those that have at least at one time been associated with the user
    	$clientUserIds=$this->ClientUser->find('list',[
				'fields'=>['ClientUser.client_id'],
				'conditions'=>['ClientUser.user_id'=>$userId,],
			]);
		
			$clientConditions['Client.id']=$clientUserIds;
      $clientUserConditions['ClientUser.user_id']=$userId;
		}
    elseif ($userRoleId != ROLE_ADMIN && !$canSeeAllUsers && $canSeeAllSalesExecutives) {
      $vendorUserIds=array_keys($users=$this->User->getActiveSalesUsersOnlyList());
      
      $clientUserIds=$this->ClientUser->find('list',[
				'fields'=>['ClientUser.client_id'],
				'conditions'=>['ClientUser.user_id'=>$vendorUserIds,],
			]);
		
			//$clientConditions['Client.id']=$vendorUserIds;
      //$clientUserConditions['ClientUser.user_id']=$vendorUserIds;
    }
		
		if ($activeDisplayOptionId!=SHOW_CLIENT_ACTIVE_ALL){
			if ($activeDisplayOptionId==SHOW_CLIENT_ACTIVE_YES){
				$clientConditions['Client.bool_active']=true;
			}
			else {
				$clientConditions['Client.bool_active']=false;
			}
		}
		if ($vipDisplayOptionId!=SHOW_CLIENT_VIP_ALL){
			if ($vipDisplayOptionId==SHOW_CLIENT_VIP_YES){
				$clientConditions['Client.bool_vip']=true;
			}
			else {
				$clientConditions['Client.bool_vip']=false;
			}
		}
		if (!empty($searchTerm)){
			$clientConditions['Client.name LIKE']= '%'.$searchTerm.'%';
		}
		//pr($clientConditions);
		$clientCount=	$this->Client->find('count', [
			'fields'=>['Client.id'],
			'conditions' => $clientConditions,
		]);
    //pr($clientCount);
		
		$this->Paginator->settings = [
			'conditions' => $clientConditions,
			'contain'=>[				
				'ClientUser'=>[
          'conditions' => $clientUserConditions,  
					'User',
					'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
          'limit'=>1,
				],
        'Quotation'=>[
					'Currency',
					'conditions'=>[
						'Quotation.quotation_date >='=>$startDate,
						'Quotation.quotation_date <'=>$endDatePlusOne,
					],
				],
				'Invoice'=>[
					'Currency',
					'conditions'=>[
						'Invoice.invoice_date >='=>$startDate,
						'Invoice.invoice_date <'=>$endDatePlusOne,
					],
				],
			],
			'order'=>'Client.name ASC',
			'limit'=>($clientCount!=0?$clientCount:1),
		];
		// to paginate, just remove the limit
		
		$allClients = $this->Paginator->paginate('Client');
    //pr($allClients);
    $clients=[
      'Registered'=>[],
      'Generic'=>[],
    ];	
		//pr($clients);
		for ($c=0;$c<count($allClients);$c++){
			$quotationTotal=0;
			$invoiceTotal=0;
      //20171213 IF THE USER ID IS EMPTY, THAT IS WHEN IT IS AN ADMIN OR AN ASSISTANT
      //20171213 OR IF THE USER ID IS ASSIGNED TO THE SELECTED USER AT THIS TIME
      if (empty($userId)||$allClients[$c]['ClientUser'][0]['bool_assigned']){
        $thisClient=$allClients[$c];
        
        if ($thisClient['Client']['bool_generic']){
          $genericClientData=[
            $thisClient['Client']['name']=>[
              'name'=>'',
              'ruc'=>'',
              'address'=>'',
              'phone'=>'',
              'cell'=>'',
              'invoice_total'=>0,
              'invoice_quantity'=>0,
            ],
          ];
          if (!empty($thisClient['Quotation']) || in_array($salesDisplayOptionId,[SHOW_CLIENT_SALES_OR_NOT,SHOW_CLIENT_SALES_NO])){
            for ($q=0;$q<count($thisClient['Quotation']);$q++){
              // set the exchange rate
              $quotationDate=$thisClient['Quotation'][$q]['quotation_date'];
              $thisClient['Quotation'][$q]['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($quotationDate);
              if ($currencyId==CURRENCY_CS){
                if ($thisClient['Quotation'][$q]['Currency']['id']==CURRENCY_CS){
                  $quotationTotal+=$thisClient['Quotation'][$q]['price_subtotal'];
                }
                elseif ($thisClient['Quotation'][$q]['Currency']['id']==CURRENCY_USD){
                  //added calculation of totals in CS$
                  $quotationTotal+=round($thisClient['Quotation'][$q]['price_subtotal']*$thisClient['Quotation'][$q]['exchange_rate'],2);
                }
              }
              elseif ($currencyId==CURRENCY_USD){
                if ($thisClient['Quotation'][$q]['Currency']['id']==CURRENCY_USD){
                  $quotationTotal+=$thisClient['Quotation'][$q]['price_subtotal'];
                }
                elseif ($thisClient['Quotation'][$q]['Currency']['id']==CURRENCY_CS){
                  //added calculation of totals in USD
                  $quotationTotal+=round($thisClient['Quotation'][$q]['price_subtotal']/$thisClient['Quotation'][$q]['exchange_rate'],2);
                }
              }
            }
          }
          if (!empty($thisClient['Invoice']) || in_array($salesDisplayOptionId,[SHOW_CLIENT_SALES_OR_NOT,SHOW_CLIENT_SALES_NO])){
            
            for ($i=0;$i<count($thisClient['Invoice']);$i++){
              $clientName=$thisClient['Invoice'][$i]['client_name']." (".$thisClient['Client']['name'].")";
              if (empty($clientName)){
                $clientName=$thisClient['Client']['name'];
              }
              if (!array_key_exists($clientName,$genericClientData)){
                $genericClientData[$clientName]=[
                  'name'=>$clientName,
                  'ruc'=>$thisClient['Invoice'][$i]['client_ruc'],
                  'address'=>$thisClient['Invoice'][$i]['client_address'],
                  'phone'=>$thisClient['Invoice'][$i]['client_phone'],
                  'cell'=>$thisClient['Invoice'][$i]['client_cell'],
                  'invoice_total'=>0,
                  'invoice_quantity'=>0,
                ];
              }
              
              // set the exchange rate
              $invoiceDate=$thisClient['Invoice'][$i]['invoice_date'];
              $genericClientData[$clientName]['invoice_quantity']+=1;
              $thisClient['Invoice'][$i]['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
              if ($currencyId == CURRENCY_CS){
                if ($thisClient['Invoice'][$i]['Currency']['id']==CURRENCY_CS){
                  $genericClientData[$clientName]['invoice_total']+=$thisClient['Invoice'][$i]['price_subtotal'];
                }
                elseif ($thisClient['Invoice'][$i]['Currency']['id']==CURRENCY_USD){
                  //added calculation of totals in CS$
                  $genericClientData[$clientName]['invoice_total']+=round($thisClient['Invoice'][$i]['price_subtotal']*$thisClient['Invoice'][$i]['exchange_rate'],2);
                }
              }
              elseif ($currencyId == CURRENCY_USD){
                if ($thisClient['Invoice'][$i]['Currency']['id']==CURRENCY_USD){
                  $genericClientData[$clientName]['invoice_total']+=$thisClient['Invoice'][$i]['price_subtotal'];
                }
                elseif ($thisClient['Invoice'][$i]['Currency']['id']==CURRENCY_CS){
                  //added calculation of totals in USD
                  $genericClientData[$clientName]['invoice_total']+=round($thisClient['Invoice'][$i]['price_subtotal']/$thisClient['Invoice'][$i]['exchange_rate'],2);
                }
              }
            }
          }  
          $thisClient['Client']['quotation_total']=$quotationTotal;
          $thisClient['Client']['generic_client_data']=$genericClientData;
          $clients['Generic'][]=$thisClient;    
        }
        else {
          if (!empty($thisClient['Quotation']) || in_array($salesDisplayOptionId,[SHOW_CLIENT_SALES_OR_NOT,SHOW_CLIENT_SALES_NO])){
            for ($q=0;$q<count($thisClient['Quotation']);$q++){
              // set the exchange rate
              $quotationDate=$thisClient['Quotation'][$q]['quotation_date'];
              $exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
              $thisClient['Quotation'][$q]['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
              if ($currencyId==CURRENCY_CS){
                if ($thisClient['Quotation'][$q]['Currency']['id']==CURRENCY_CS){
                  $quotationTotal+=$thisClient['Quotation'][$q]['price_subtotal'];
                }
                elseif ($thisClient['Quotation'][$q]['Currency']['id']==CURRENCY_USD){
                  //added calculation of totals in CS$
                  $quotationTotal+=round($thisClient['Quotation'][$q]['price_subtotal']*$thisClient['Quotation'][$q]['exchange_rate'],2);
                }
              }
              elseif ($currencyId==CURRENCY_USD){
                if ($thisClient['Quotation'][$q]['Currency']['id']==CURRENCY_USD){
                  $quotationTotal+=$thisClient['Quotation'][$q]['price_subtotal'];
                }
                elseif ($thisClient['Quotation'][$q]['Currency']['id']==CURRENCY_CS){
                  //added calculation of totals in USD
                  $quotationTotal+=round($thisClient['Quotation'][$q]['price_subtotal']/$thisClient['Quotation'][$q]['exchange_rate'],2);
                }
              }
            }
          }
          if (!empty($thisClient['Invoice']) || in_array($salesDisplayOptionId,[SHOW_CLIENT_SALES_OR_NOT,SHOW_CLIENT_SALES_NO])){
            for ($i=0;$i<count($thisClient['Invoice']);$i++){
              // set the exchange rate
              $invoiceDate=$thisClient['Invoice'][$i]['invoice_date'];
              $thisClient['Invoice'][$i]['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
              if ($currencyId==CURRENCY_CS){
                if ($thisClient['Invoice'][$i]['Currency']['id']==CURRENCY_CS){
                  $invoiceTotal+=$thisClient['Invoice'][$i]['price_subtotal'];
                }
                elseif ($thisClient['Invoice'][$i]['Currency']['id']==CURRENCY_USD){
                  //added calculation of totals in CS$
                  $invoiceTotal+=round($thisClient['Invoice'][$i]['price_subtotal']*$thisClient['Invoice'][$i]['exchange_rate'],2);
                }
              }
              elseif ($currencyId==CURRENCY_USD){
                if ($thisClient['Invoice'][$i]['Currency']['id']==CURRENCY_USD){
                  $invoiceTotal+=$thisClient['Invoice'][$i]['price_subtotal'];
                }
                elseif ($thisClient['Invoice'][$i]['Currency']['id']==CURRENCY_CS){
                  //added calculation of totals in USD
                  $invoiceTotal+=round($thisClient['Invoice'][$i]['price_subtotal']/$thisClient['Invoice'][$i]['exchange_rate'],2);
                }
              }
            }
          }  
          $thisClient['Client']['quotation_total']=$quotationTotal;
          $thisClient['Client']['invoice_total']=$invoiceTotal;
          $clients['Registered'][]=$thisClient;    
        }  
      }
		}
    //pr($clients['Generic']);
    switch ($aggregateOptionId){
      case AGGREGATES_NONE:
        usort($clients['Registered'],[$this,'sortByCompanyName']);
        usort($clients['Generic'],[$this,'sortByCompanyName']);
        break;
      case AGGREGATES_INVOICES_QUOTATIONS:
        usort($clients['Registered'],array($this,'sortByInvoiceTotalQuotationTotalCompanyName'));
        //usort($clients['Generic'],[$this,'sortByInvoiceTotalQuotationTotalCompanyName']);  
        
        break;
      case AGGREGATES_QUOTATIONS_INVOICES:
        usort($clients['Registered'],array($this,'sortByQuotationTotalInvoiceTotalCompanyName'));
        //usort($clients['Generic'],array($this,'sortByQuotationTotalInvoiceTotalCompanyName'));
        break;
    }
    
		$this->set(compact('clients'));
		//pr($clients);
		
		
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
		
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$aco_name="Contacts/index";		
		$bool_contact_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_contact_index_permission'));
		$aco_name="Contacts/add";		
		$bool_contact_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_contact_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
	}
  
  public function sortByCompanyName($a,$b ){ 
		if( $a['Client']['name'] == $b['Client']['name'] ){ 
			return 0 ; 
		} 
		return ($a['Client']['name'] < $b['Client']['name']) ? -1 : 1;
	}
	public function sortByQuotationTotalInvoiceTotalCompanyName($a,$b ){ 
		if( $a['Client']['quotation_total'] == $b['Client']['quotation_total'] ){ 			
      //return 0 ;       
      if( $a['Client']['invoice_total'] == $b['Client']['invoice_total'] ){ 
        if( $a['Client']['name'] == $b['Client']['name'] ){ 
          return 0 ; 
        }
        else {
          return ($a['Client']['name'] < $b['Client']['name']) ? -1 : 1;
        }
      }
      else {
        return ($a['Client']['invoice_total'] < $b['Client']['invoice_total']) ? 1 : -1;
      }  
		} 
		return ($a['Client']['quotation_total'] < $b['Client']['quotation_total']) ? 1 : -1;
	}
	public function sortByInvoiceTotalQuotationTotalCompanyName($a,$b ){ 
		if($a['Client']['invoice_total'] == $b['Client']['invoice_total'] ){ 
			//return 0 ; 
      if( $a['Client']['quotation_total'] == $b['Client']['quotation_total'] ){ 
        if( $a['Client']['name'] == $b['Client']['name'] ){ 
          return 0 ; 
        }
        else {
          return ($a['Client']['name'] < $b['Client']['name']) ? -1 : 1;
        }
      }
      else {
        return ($a['Client']['quotation_total'] < $b['Client']['quotation_total']) ? 1 : -1;
      }
		} 
		return ($a['Client']['invoice_total'] < $b['Client']['invoice_total']) ? 1 : -1;
	}
  
	public function guardarResumen() {
		$exportData=$_SESSION['resumenClientes'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->Client->exists($id)) {
			throw new NotFoundException(__('Invalid client'));
		}
		
		$options = array(
			'conditions' => array(
				'Client.id' => $id
			),
			'contain'=>array(
				'CreatingUser',
				'ClientUser'=>array(
          'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
					'User',
				),
				'Contact',
				'Invoice'=>array(
					'InvoiceSalesOrder'=>array(
						'SalesOrder'=>array(
							'Quotation'
						),
						'User',
					),
				),
				'Quotation'=>array(
					'Contact',
					'User',
					'SalesOrder',
				),
			),
		);
    $client=$this->Client->find('first', $options);
		$this->set(compact('client'));
    
    $userIdList=[];
    foreach ($client['ClientUser'] as $clientUser){
      if (!in_array($clientUser['user_id'],$userIdList)){
        $userIdList[]=$clientUser['user_id'];
      }
    }
    $this->loadModel('Users');
    $uniqueUsers=$this->User->find('all',array(
      'conditions'=>array(
        'User.id'=>$userIdList,
      ),
      'contain'=>array(					
        'ClientUser'=>array(
          'conditions'=>array(
            'ClientUser.client_id'=>$id,
          ),
          'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
        )
  		),
      'order'=>'User.username'
    ));
    $this->set(compact('uniqueUsers'));
		
		$aco_name="Contacts/index";		
		$bool_contact_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_contact_index_permission'));
		$aco_name="Contacts/add";		
		$bool_contact_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_contact_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
		
		$aco_name="Users/edit";		
		$bool_user_edit_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_user_edit_permission'));
	}

	public function add() {
    $userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole'));
  
    //$userid = $this->Auth->User('id');
		//$this->set(compact('userid'));
    
		$this->loadModel('User');
		$this->User->recursive=-1;
		$users = $this->User->find('all',array(
			'fields'=>array('User.id','User.username','User.first_name','User.last_name'),
      'conditions'=>array(
        'User.bool_active'=>true,
      ),
		));
		$this->set(compact('users'));
	
		if ($this->request->is('post')) {
			$this->loadModel('Contact');
			$this->Contact->recursive=-1;
			$boolContactsOK=true;
			$flashMessage="";
      
      $existingClientsWithThisName=$this->Client->find('list',[
        'conditions'=>[
          'Client.name'=>$this->request->data['Client']['name'],
        ],
      ]);
      
			foreach ($this->request->data['Contact'] as $contact){
				/*
				if (!empty($contact['first_name'])&&!empty($contact['last_name'])){
					$existingContacts=$this->Contact->find('all',array(
						'fields'=>array('Contact.id'),
						'conditions'=>array(
							'Contact.first_name'=>$contact['first_name'],
							'Contact.last_name'=>$contact['last_name'],
						),
					));
					if (count($existingContacts)>0){
						$flashMessage="Ya existe un contacto con nombre ".$contact['first_name']." y apellido ".$contact['last_name'].".  No se guardó el cliente.";
						$boolContactsOK=false;
					}
				}
				*/
			}
			if (!$boolContactsOK){
				$this->Session->setFlash($flashMessage, 'default',['class' => 'error-message']);
			}
      elseif (!empty($existingClientsWithThisName)){
        $this->Session->setFlash('Ya existe un cliente con el nombre '.$this->request->data['Client']['name'].'.  No se guardó el cliente.', 'default',['class' => 'error-message']);
      }
			else {
				$datasource=$this->Client->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$this->Client->create();
					$this->request->data['Client']['creating_user_id']=$this->Auth->User('id');
					if (!$this->Client->save($this->request->data)) {
						echo "Problema guardando el cliente";
						pr($this->validateErrors($this->Client));
						throw new Exception();
					}
					$client_id=$this->Client->id;
          
					if (!empty($this->request->data['User'])){
						$currentDateTime=new DateTime();
						for ($u=0;$u<count($this->request->data['User']);$u++){
							$clientUserArray=array();
							$this->Client->ClientUser->create();
							$clientUserArray['ClientUser']['client_id']=$client_id;
							$clientUserArray['ClientUser']['user_id']=$users[$u]['User']['id'];
							$clientUserArray['ClientUser']['assignment_datetime']=$currentDateTime->format('Y-m-d H:i:s');
							$clientUserArray['ClientUser']['bool_assigned']=$this->request->data['User'][$u]['id'];
							if (!$this->Client->ClientUser->save($clientUserArray)){
								echo "Problema guardando el vendedor para el cliente";
								pr($this->validateErrors($this->Client->ClientUser));
								throw new Exception();
							}							
						}
					}
          
					if (!empty($this->request->data['Contact'])){
						foreach ($this->request->data['Contact'] as $contact){
							if (!empty($contact['first_name'])&&!empty($contact['last_name'])){
								//pr($quotationProduct);
								$contactArray=array();
								$contactArray['Contact']['first_name']=$contact['first_name'];
								$contactArray['Contact']['last_name']=$contact['last_name'];
								$contactArray['Contact']['phone']=$contact['phone'];
								$contactArray['Contact']['cell']=$contact['cell'];
								$contactArray['Contact']['email']=$contact['email'];
								$contactArray['Contact']['department']=$contact['department'];
								$contactArray['Contact']['bool_active']=true;
								$contactArray['Contact']['client_id']=$client_id;
								$this->Contact->create();
								if (!$this->Contact->save($contactArray)) {
									echo "Problema guardando los contactos del cliente";
									pr($this->validateErrors($this->Contact));
									throw new Exception();
								}
							}
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->Client->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró el cliente ".$this->request->data['Client']['name']);
					
					$this->Session->setFlash(__('The client has been saved.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('The client could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		
		$aco_name="Contacts/index";		
		$bool_contact_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_contact_index_permission'));
		$aco_name="Contacts/add";		
		$bool_contact_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_contact_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
		$aco_name="Invoices/index";		
		$bool_invoice_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_index_permission'));
		$aco_name="Invoices/add";		
		$bool_invoice_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_add_permission'));
	}

	public function edit($id = null) {
    $userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole'));
  
		if (!$this->Client->exists($id)) {
			throw new NotFoundException(__('Invalid client'));
		}
		$this->loadModel('ClientUser');
		$this->loadModel('User');
		$this->User->recursive=-1;
		$users = $this->User->find('all',array(
			'fields'=>array('User.id','User.username','User.first_name','User.last_name'),
      'conditions'=>array(
        'User.bool_active'=>true,
      ),
			'contain'=>array(
				'ClientUser'=>array(
					'conditions'=>array(
						'ClientUser.client_id'=>$id,
					),
					'order'=>'ClientUser.id DESC',
				),
			),
			'order'=>'User.first_name,User.last_name',
		));
		$this->set(compact('users'));
		//pr($users);
		
		$this->Client->Contact->recursive=-1;
		$existingContacts=$this->Client->Contact->find('all',array(
			'conditions'=>array(
				'Contact.client_id'=>$id,
			),
		));
		$this->set(compact('existingContacts'));
		if ($this->request->is(['post', 'put'])) {
			$this->loadModel('Contact');
			
			$existingContactIds=$this->Contact->find('list',array(
				'fields'=>'Contact.id',
				'conditions'=>array(
					'Contact.client_id'=>$id,
				),
			));
			//pr($existingContactIds);
			
      $existingClientsWithThisName=$this->Client->find('list',[
        'conditions'=>[
          'Client.name'=>$this->request->data['Client']['name'],
          'Client.id !='=>$id,
        ],
      ]);
      
      
			$boolContactsOK=true;
			$flashMessage="";
			if (!empty($this->request->data['Contact'])){
				foreach ($this->request->data['Contact'] as $contact){
					if (!empty($contact['first_name'])&&!empty($contact['last_name'])){
						$existingContactsInDatabase=$this->Contact->find('list',array(
							'fields'=>array('Contact.id'),
							'conditions'=>array(
								'Contact.first_name'=>$contact['first_name'],
								'Contact.last_name'=>$contact['last_name'],
								'Contact.client_id'=>$id,
								'Contact.id !='=>$existingContactIds,
							),
						));
						//echo "existing contacts for contact ".$contact['first_name']." y apellido ".$contact['last_name']."<br/>";
						//pr($existingContactsInDatabase);
						if (count($existingContactsInDatabase)>0){
							$flashMessage="Ya existe un contacto con nombre ".$contact['first_name']." y apellido ".$contact['last_name'].".  No se guardó el cliente.";
							$boolContactsOK=false;
						}
					}
				}
			}
			if (!$boolContactsOK){
				$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			}
      elseif (!empty($existingClientsWithThisName)){
        $this->Session->setFlash('Ya existe un cliente con el nombre '.$this->request->data['Client']['name'].'.  No se guardó el cliente.', 'default',['class' => 'error-message']);
      }
			else {
				$datasource=$this->Client->getDataSource();
				$datasource->begin();
				try {
					$this->Client->ClientUser->recursive=-1;
					//20170517 YA NO SE ELIMINAN LOS CLIENT USER PORQUE SE GUARDA EL HISTORIAL
					//$previousClientUsers=$this->Client->ClientUser->find('all',array(
					//	'fields'=>array('ClientUser.id'),
					//	'conditions'=>array(
					//		'ClientUser.client_id'=>$id,
					//	),
					//));
					//if (!empty($previousClientUsers)){
					//	foreach ($previousClientUsers as $previousClientUser){
					//		$this->Client->ClientUser->id=$previousClientUser['ClientUser']['id'];
					//		$this->Client->ClientUser->delete($previousClientUser['ClientUser']['id']);
					//	}
					//}
				
					//pr($this->request->data);
					$this->Client->id=$id;
					if (!$this->Client->save($this->request->data)) {
						echo "Problema guardando el cliente";
						pr($this->validateErrors($this->Client));
						throw new Exception();
					}
					$client_id=$this->Client->id;
					if (!empty($this->request->data['User'])){
						$currentDateTime=new DateTime();					
						for ($u=0;$u<count($this->request->data['User']);$u++){
							//pr($this->request->data['User'][$u]);
							$clientUserArray=array();
							$this->Client->ClientUser->create();
							
							$clientUserArray['ClientUser']['client_id']=$client_id;
							$clientUserArray['ClientUser']['user_id']=$users[$u]['User']['id'];
							$clientUserArray['ClientUser']['assignment_datetime']=$currentDateTime->format('Y-m-d H:i:s');
							$clientUserArray['ClientUser']['bool_assigned']=$this->request->data['User'][$u]['id'];
							if (!$this->Client->ClientUser->save($clientUserArray)){
								echo "Problema guardando el vendedor para el cliente";
								pr($this->validateErrors($this->Client->ClientUser));
								throw new Exception();
							}
						}
					}
					
					if (!empty($this->request->data['Contact'])){
						$i=0;
						foreach ($this->request->data['Contact'] as $contact){
							if ($i<count($existingContacts)){
								if (!empty($contact['first_name'])&&!empty($contact['last_name'])){
									
									$contactArray=array();
									$contactArray['Contact']['id']=$existingContacts[$i]['Contact']['id'];
									$contactArray['Contact']['first_name']=$contact['first_name'];
									$contactArray['Contact']['last_name']=$contact['last_name'];
									$contactArray['Contact']['phone']=$contact['phone'];
									$contactArray['Contact']['cell']=$contact['cell'];
									$contactArray['Contact']['email']=$contact['email'];
									$contactArray['Contact']['department']=$contact['department'];
									$contactArray['Contact']['bool_active']=true;
									$contactArray['Contact']['client_id']=$client_id;
									if (!$this->Contact->save($contactArray)) {
										echo "Problema guardando los contactos del cliente";
										pr($this->validateErrors($this->Contact));
										throw new Exception();
									}
								}
							}
							else { 
								if (!empty($contact['first_name'])&&!empty($contact['last_name'])){
									$contactArray=array();
									$contactArray['Contact']['first_name']=$contact['first_name'];
									$contactArray['Contact']['last_name']=$contact['last_name'];
									$contactArray['Contact']['phone']=$contact['phone'];
									$contactArray['Contact']['cell']=$contact['cell'];
									$contactArray['Contact']['email']=$contact['email'];
									$contactArray['Contact']['department']=$contact['department'];
									$contactArray['Contact']['bool_active']=true;
									$contactArray['Contact']['client_id']=$client_id;
									$this->Contact->create();
									if (!$this->Contact->save($contactArray)) {
										echo "Problema guardando los contactos del cliente";
										pr($this->validateErrors($this->Contact));
										throw new Exception();
									}
								}
							}
							$i++;
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->Client->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se editó el cliente ".$this->request->data['Client']['name']);
					
					$this->Session->setFlash(__('Se guardó el cliente.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					//pr($e);
					$this->Session->setFlash(__('No se podía guardar el cliente.'), 'default',array('class' => 'error-message'));
				}
			}
		} 
		else {
			$options = array('conditions' => array('Client.id'=> $id));
			$this->request->data = $this->Client->find('first', $options);
		}
		
		$this->loadModel('User');
    $this->User->recursive=-1;  
    $allUsers=$this->User->find('all',[
      'fields'=>['User.id','User.id','User.username','User.first_name','User.last_name'],
      'order'=>'User.first_name ASC,User.last_name ASC,User.username ASC',
    ]);
    
    $usersAssociatedWithClient=[];
    foreach ($allUsers as $user){
      if ($this->ClientUser->checkAssociationClientWithUser($id,$user['User']['id'])){
        $usersAssociatedWithClient[]=$user;
      }
    }
    $this->set(compact('usersAssociatedWithClient'));
    //pr($users);
		$aco_name="Contacts/index";		
		$bool_contact_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_contact_index_permission'));
		$aco_name="Contacts/add";		
		$bool_contact_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_contact_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
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
		$this->Client->id = $id;
		if (!$this->Client->exists()) {
			throw new NotFoundException(__('Invalid client'));
		}
		$client=$this->Client->find('first',array(
			'conditions'=>array(
				'Client.id'=>$id,
			),
			'contain'=>array(
				'Quotation',
				'Invoice',
				'Remission',
				
				'ClientUser',
				'Contact',
				'VipClientObjective',
			),
		));
		$this->request->allowMethod('post', 'delete');
		
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (count($client['Quotation'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Este cliente tiene cotizaciones correspondientes.  Para poder eliminar el cliente, primero hay que eliminar o modificar las cotizaciones ";
			if (count($client['Quotation'])==1){
				$flashMessage.=$client['Quotation'][0]['quotation_code'].".";
			}
			else {
				for ($i=0;$i<count($client['Quotation']);$i++){
					$flashMessage.=$client['Quotation'][$i]['quotation_code'];
					if ($i==count($client['Quotation'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (count($client['Invoice'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Este cliente tiene facturas correspondientes.  Para poder eliminar el cliente, primero hay que eliminar o modificar las facturas ";
			if (count($client['Invoice'])==1){
				$flashMessage.=$client['Invoice'][0]['invoice_code'].".";
			}
			else {
				for ($i=0;$i<count($client['Invoice']);$i++){
					$flashMessage.=$client['Invoice'][$i]['invoice_code'];
					if ($i==count($client['Invoice'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (count($client['Remission'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Este cliente tiene remisiones correspondientes.  Para poder eliminar el cliente, primero hay que eliminar o modificar las remisiones ";
			if (count($client['Remission'])==1){
				$flashMessage.=$client['Remission'][0]['remission_code'].".";
			}
			else {
				for ($i=0;$i<count($client['Remission']);$i++){
					$flashMessage.=$client['Remission'][$i]['remission_code'];
					if ($i==count($client['Remission'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó el cliente.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			$datasource=$this->Client->getDataSource();
			$datasource->begin();	
			try {
				//delete all vip client objectives, contacts
				foreach ($client['ClientUser'] as $clientUser){
					if (!$this->Client->ClientUser->delete($clientUser['id'])) {
						echo "Problema al eliminar la asociación con usuarios para el cliente";
						pr($this->validateErrors($this->Client->ClientUser));
						throw new Exception();
					}
				}
				foreach ($client['Contact'] as $contact){
					if (!$this->Client->Contact->delete($contact['id'])) {
						echo "Problema al eliminar el contacto del cliente";
						pr($this->validateErrors($this->Client->Contact));
						throw new Exception();
					}
				}
				foreach ($client['VipClientObjective'] as $vipClientObjective){
					if (!$this->Client->VipClientObjective->delete($vipClientObjective['id'])) {
						echo "Problema al eliminar el objetivo VIP para el cliente";
						pr($this->validateErrors($this->Client->VipClientObjective));
						throw new Exception();
					}
				}
				
				if (!$this->Client->delete($id)) {
					echo "Problema al eliminar el cliente";
					pr($this->validateErrors($this->Client));
					throw new Exception();
				}
						
				$datasource->commit();
					
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$client['Client']['id'];
				$deletionArray['Deletion']['reference']=$client['Client']['name'];
				$deletionArray['Deletion']['type']='Client';
				$this->Deletion->save($deletionArray);
				
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó el cliente  ".$client['Client']['name']);
						
				$this->Session->setFlash(__('Se eliminó el cliente, sus contactos, asociaciones con vendedores y objetivos VIP.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía eliminar el cliente.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function saveclient() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$clientid=trim($_POST['clientid']);
		$boolnewclient=($_POST['boolnewclient']=="true");
		
		$clientname=trim($_POST['clientname']);
		$clientruc=trim($_POST['clientruc']);
		$clientaddress=trim($_POST['clientaddress']);
		$clientphone=trim($_POST['clientphone']);
		$clientcell=trim($_POST['clientcell']);
		//20170821 ADDED CHECK ON EXISTING CLIENTS
    $boolClientOK=true;
    if ($boolnewclient){
      $this->Client->recursive=-1;
      $existingClientsWithThisName=$this->Client->find('all',array(
        'fields'=>array('Client.id'),
        'conditions'=>array(
          'Client.name'=>$clientname,
        ),
      ));
      if (!empty($existingClientsWithThisName)){
        $boolClientOK=false;
      }
    }
    if (!$boolClientOK){
      return "Ya existe un cliente con el mismo nombre, seleccione de la lista";
    }
    else {
      $datasource=$this->Client->getDataSource();
      $datasource->begin();
      try {
        $currentDateTime=new DateTime();
        //pr($this->request->data);
        $clientArray=array();
        $clientArray['Client']['name']=$clientname;
        $clientArray['Client']['ruc']=$clientruc;
        $clientArray['Client']['address']=$clientaddress;
        $clientArray['Client']['phone']=$clientphone;
        $clientArray['Client']['cell']=$clientcell;
        $clientArray['Client']['bool_active']=true;
        $clientArray['Client']['creating_user_id']=$this->Auth->User('id');
        if ($boolnewclient){
          $this->Client->create();
        }
        else {
          $this->Client->id=$clientid;
          //pr($clientid);
          if (!$this->Client->exists($clientid)) {
            throw new Exception(__('Cliente inválido'));
          }				
        }
        if (!$this->Client->save($clientArray)) {
          echo "Problema guardando el cliente";
          pr($this->validateErrors($this->Client));
          throw new Exception();
        }
        $client_id=$this->Client->id;

        $this->loadModel('ClientUser');
        $this->ClientUser->create();
        $clientUserData=array();
        $clientUserData['ClientUser']['client_id']=$client_id;
        $clientUserData['ClientUser']['user_id']=$this->Auth->User('id');
        $clientUserData['ClientUser']['assignment_datetime']=$currentDateTime->format('Y-m-d H:i:s');
        $clientUserData['ClientUser']['bool_assigned']=true;
        if (!$this->ClientUser->save($clientUserData)) {
          echo "Problema guardando la asociación entre cliente y usuario";
          pr($this->validateErrors($this->ClientUser));
          throw new Exception();
        }
        
        $datasource->commit();
      
        $this->recordUserAction($this->Client->id,"add",null);
        $this->recordUserActivity($this->Session->read('User.username'),"Se registró el cliente ".$clientname);
        
        $this->Session->setFlash(__('The client has been saved.'),'default',array('class' => 'success'));
        //return $this->redirect(array('action' => 'index'));
        return true;
      } 
      catch(Exception $e){
        $datasource->rollback();
        //pr($e);
        $this->Session->setFlash(__('The client could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
        return false;
      }
    }
  }
	
	public function getclientlist() {
		$this->layout = "ajax";
		
		$this->Client->recursive=-1;
		$clients=$this->Client->find('all',array(
			'fields'=>array('Client.id','Client.name','Client.bool_active'),
			'order'=>'Client.name',
		));
		//pr($clients);
		$this->set(compact('clients'));
	}
	
	public function getclientlistforclientname() {
		$this->layout = "ajax";
		
		$clientval=trim($_POST['clientval']);
		
		$this->Client->recursive=-1;
		$clients=$this->Client->find('all',array(
			'fields'=>array('Client.id','Client.name','Client.bool_active'),
			'conditions'=>array(
				'Client.name LIKE'=> "%$clientval%",
			),
			'order'=>'Client.name',
		));
		//pr($clients);
		$this->set(compact('clients'));
	}
	
	public function getclientinfo() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$clientid=trim($_POST['clientid']);
		
		$this->Client->recursive=-1;
		$client=$this->Client->find('first',array(
			'fields'=>array('Client.id','Client.name','Client.ruc','Client.address','Client.phone','Client.cell'),
			'conditions'=>array(
				'Client.id'=> $clientid,
			),
			'contain'=>array(
				'CreatingUser'=>array(
					'fields'=>'username',
				),
				'Quotation'=>array(
					'fields'=>array(
						'Quotation.quotation_code',
					),	
					'order'=>'Quotation.quotation_date DESC',
					'limit'=>5,
				),
			),
		));
		return json_encode($client);
	}

	public function asociarClientesUsuarios($selectedClientId=0,$selectedUserId=0){
		$this->loadModel('Client');
		$this->loadModel('ClientUser');
		$this->loadModel('User');
		
		$this->Client->recursive=-1;
		$this->ClientUser->recursive=-1;
		$this->User->recursive=-1;
		
		$this->request->allowMethod('get','post', 'put');
		
		$selectedUserId=0;
		if ($this->request->is('post')) {
      //echo "posting the data";
			//pr($this->request->data);
      //pr($this->request->data['ClientUser']);
			$selectedUserId=$this->request->data['ClientUser']['user_id'];
			$selectedClientId=$this->request->data['ClientUser']['client_id'];
			
      
			if (!empty($this->request->data['refresh'])){
        //$this->redirect(array('action' => 'asociarClientesUsuarios',$selectedClientId,$selectedUserId 'page' => 1));
      }
      else {
				$currentDateTime=new DateTime();
				$datasource=$this->ClientUser->getDataSource();
				$datasource->begin();
				try {
					foreach ($this->request->data['Client'] as $clientId=>$clientValue){
						//pr($clientValue);
						if ($clientValue['bool_changed']){
							foreach ($clientValue['User'] as $userId=>$userValue){
								$clientUserArray=array();
								$clientUserArray['ClientUser']['client_id']=$clientId;
								$clientUserArray['ClientUser']['user_id']=$userId;
								$clientUserArray['ClientUser']['assignment_datetime']=$currentDateTime->format('Y-m-d H:i:s');
								$clientUserArray['ClientUser']['bool_assigned']=$userValue['bool_assigned'];
								//pr($clientUserArray);
								$this->ClientUser->create();
								if (!$this->ClientUser->save($clientUserArray)){
									echo "Problema creando la asociación entre cliente y vendedor";
									pr($this->validateErrors($this->ClientUser));
									throw new Exception();
								}								
							}
						}					
					}
					$datasource->commit();
					
					$this->recordUserAction(null,'asociarClientesUsuarios','clients');
					$this->recordUserActivity($this->Session->read('User.username'),"Se asignaron clientes a usuarios");
					$this->Session->setFlash(__('Se asignaron los clientes a los usuarios.'),'default',array('class' => 'success'));
					//return $this->redirect(array('action' => 'asociarClientesUsuarios'));
					return $this->redirect($this->referer());
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('No se podían asignar los clientes a los usuarios.'), 'default',array('class' => 'error-message'));
					$this->recordUserActivity($this->Session->read('User.username')," intentó asignar clientes sin éxito");
				}
			}
		}
		
    $this->set(compact('selectedUserId'));
    $this->set(compact('selectedClientId'));
    
		$userConditions=[
      'User.bool_active'=>true,
      'User.bool_test'=>false,
    ];
		if (!empty($selectedUserId)){
			$userConditions['User.id']=$selectedUserId;
		}
		$selectedUsers=$this->User->find('list',[
			'fields'=>[
				'User.id',
				'User.username',
			],
			'conditions'=>$userConditions,
			'order'=>'User.username',			
		]);
		$this->set(compact('selectedUsers'));
    //echo "selected users<br/>";
		//pr($selectedUsers);
		
		$clientConditions=['Client.bool_active'=>true];
		if (!empty($selectedClientId)){
			$clientConditions['Client.id']=$selectedClientId;
		}
		/*
		$selectedClients=$this->Client->find('all',array(
			'fields'=>array(
				'Client.id',
				'Client.name',
			),
			'conditions'=>$clientConditions,
			'contain'=>array(
				'ClientUser'=>array(
					'fields'=>array(
						'ClientUser.id',
						'ClientUser.user_id',
						'ClientUser.bool_assigned',
						'ClientUser.assignment_datetime',
					),
					'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
				),
			),
			'order'=>'Client.name',
		));
		*/
		$this->Paginator->settings=[
			'fields'=>[
				'Client.id',
				'Client.name',
			],
			'conditions'=>$clientConditions,
			'contain'=>[
				'ClientUser'=>[
					'fields'=>[
						'ClientUser.id',
						'ClientUser.user_id',
						'ClientUser.bool_assigned',
						'ClientUser.assignment_datetime',
					],
					'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
				],
			],
			'order'=>'Client.name',
			'limit'=>150,
		];
		$selectedClients=$this->Paginator->paginate();
		
		//pr($selectedClients);
		for ($c=0;$c<count($selectedClients);$c++){
			$userArray=array();
			if (!empty($selectedClients[$c]['ClientUser'])){
				foreach ($selectedUsers as $userId=>$userValue){
					$userArray[$userId]=0;
					foreach ($selectedClients[$c]['ClientUser'] as $clientUser){
						if ($clientUser['user_id']==$userId){
							$userArray[$userId]=$clientUser['bool_assigned'];
							break;
						}
					}
				}
			}
			$selectedClients[$c]['Users']=$userArray;
		}
		$this->set(compact('selectedClients'));
    //echo "selected clients<br/>";
		//pr($selectedClients);
		
		$users=$this->User->find('list',array(
			'fields'=>array(
				'User.id',
				'User.username',
			),
			'order'=>'User.username',			
		));
		$this->set(compact('users'));
		
		$clients=$this->Client->find('list',array(
			'fields'=>array(
				'Client.id',
				'Client.name',
			),
			'conditions'=>array(
				'Client.bool_active'=>true,
			),
			'order'=>'Client.name',
		));
		$this->set(compact('clients'));
	}
	
	public function guardarAsociacionesClientesUsuarios() {
		$exportData=$_SESSION['resumenAsociaciones'];
		$this->set(compact('exportData'));
	}

	public function reasignarClientes() {
		$this->loadModel('User');
		$this->loadModel('ClientUser');
		$this->loadModel('Client');
		$this->Client->recursive=-1;
		
		$originUserId=0;
		$boolKeepOrigin=true;
		$destinyUserArray=0;
		$clientsAssociatedWithUser=array();
		if ($this->request->is(array('post', 'put'))) {
			//pr($this->request->data);
			$originUserId=$this->request->data['Reassign']['origin_user_id'];
			$boolKeepOrigin=$this->request->data['Reassign']['bool_keep_origin'];
      echo "bool keep origin is ".$boolKeepOrigin."<br/>";
			$destinyUserId=$this->request->data['Reassign']['destiny_user_id'];
			if (!empty($this->request->data['Reassign']['origin_user_id'])){
        // 20181113 REMOVED THIS SIMPLISTIC CODE AS IT ASSUMED THERE WAS ONLY ONE ASSOCIATION  
				//$clientIdsAssociatedWithUser=$this->ClientUser->find('list',[
				//	'fields'=>['ClientUser.client_id'],
				//	'conditions'=>['ClientUser.user_id'=>$originUserId,],
				//]);
				//pr($clientIdsAssociatedWithUser);
        //$clientsAssociatedWithUser=$this->Client->find('all',[
				//	'conditions'=>['Client.id'=>$clientIdsAssociatedWithUser,],
				//	'order'=>'Client.name',
				//]);
        
        $selectedClients=$this->Client->find('all',[
          'contain'=>[
            'ClientUser'=>[
              'fields'=>[
                'ClientUser.id',
                'ClientUser.user_id',
                'ClientUser.bool_assigned',
                'ClientUser.assignment_datetime',
              ],
              'conditions'=>['ClientUser.user_id'=>$originUserId,],
              'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
            ],
          ],
          'order'=>'Client.name',
        ]);
        $clientsAssociatedWithUser=[];
        //pr($selectedClients);
        for ($c=0;$c<count($selectedClients);$c++){
          if (!empty($selectedClients[$c]['ClientUser'])){
            //pr($selectedClients[$c]['ClientUser']);
            //foreach ($selectedClients[$c]['ClientUser'] as $clientUser){  
              if ($selectedClients[$c]['ClientUser'][0]['bool_assigned']){
                $clientsAssociatedWithUser[]=$selectedClients[$c];
              }  
            //}
          }
        }
        //pr($clientsAssociatedWithUser);
			}
			if (empty($this->request->data['showclients'])){
				$currentDateTime=new DateTime();
				$datasource=$this->ClientUser->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$this->ClientUser->recursive=-1;
					foreach ($this->request->data['Reassign']['Client'] as $clientId=>$clientValue){						
						if ($clientValue['selector']){
              //pr($clientValue);
							if (!$boolKeepOrigin){
								//20170510 REMOVED DELETIOM OF EXISTING ONES, NEEDS TO BE KEPT FOR HISTORIAL
								//$unassignClientUserId=$this->ClientUser->find('first',array(
								//	'fields'=>'ClientUser.id',
								//	'conditions'=>array(
								//		'ClientUser.client_id'=>$clientId,
								//		'ClientUser.user_id'=>$originUserId,	
								//	),
								//));
								//if (!empty($unassignClientUserId)){
								//	if (!$this->ClientUser->delete($unassignClientUserId['ClientUser']['id'])){
								//		echo "Problema removiendo la asociación entre el usuario de origen y el cliente";
								//		pr($this->validateErrors($this->ClientUser));
								//		throw new Exception();
								//	}
								//}
								$clientUserArray=[];
								$clientUserArray['ClientUser']['client_id']=$clientId;
								$clientUserArray['ClientUser']['user_id']=$originUserId;
								$clientUserArray['ClientUser']['assignment_datetime']=$currentDateTime->format('Y-m-d H:i:s');
								$clientUserArray['ClientUser']['bool_assigned']=false;
								
								$this->ClientUser->create();
                //echo "removing the client from the previous user<br/>";
                //pr($clientUserArray);
								if (!$this->ClientUser->save($clientUserArray)){
									echo "Problema creando la asociación entre cliente y vendedor";
									pr($this->validateErrors($this->ClientUser));
									throw new Exception();
								}
							}
							foreach ($clientValue['target_user_id'] as $targetUserId){
								//20170510 REMOVED LOOKUP OF EXISTING ONES, COMPLETELY IRRELEVANT
								//$clientUserId=$this->ClientUser->find('first',array(
								//	'fields'=>'ClientUser.id',
								//	'conditions'=>array(
								//		'ClientUser.client_id'=>$clientId,
								//		'ClientUser.user_id'=>$targetUserId,	
								//	),
								//));
								
								//if (empty($clientUserId)){
									$clientUserArray=array();
									$clientUserArray['ClientUser']['client_id']=$clientId;
									$clientUserArray['ClientUser']['user_id']=$targetUserId;
									$clientUserArray['ClientUser']['assignment_datetime']=$currentDateTime->format('Y-m-d H:i:s');
									$clientUserArray['ClientUser']['bool_assigned']=true;
									
									$this->ClientUser->create();
                  //echo "adding the client to the target user<br/>";
                  //pr($clientUserArray);
									if (!$this->ClientUser->save($clientUserArray)){
										echo "Problema creando la asociación entre cliente y vendedor";
										pr($this->validateErrors($this->ClientUser));
										throw new Exception();
									}
								//}
							}
						}
					}
					$datasource->commit();
					
					$this->recordUserAction(null,'reassignClients','clients');
					$this->recordUserActivity($this->Session->read('User.username'),"Se reasignaron clientes");
					$this->Session->setFlash(__('Se reasignaron los clientes.'),'default',array('class' => 'success'));
					
					//return $this->redirect(array('action' => 'reasignarClientes'));
					
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('No se podían reasignar los clientes.'), 'default',array('class' => 'error-message'));
					$this->recordUserActivity($this->Session->read('User.username')," intentó reasignar clientes sin éxito");
				}
			}
		}
		$this->set(compact('originUserId'));
		$this->set(compact('boolKeepOrigin'));
		$this->set(compact('destinyUserArray'));
		$this->set(compact('clientsAssociatedWithUser'));
		
		$targetUsers=$originUsers = $destinyUsers= $users= $this->User->find('list',array(
			'order'=>'User.username ASC',
		));
		$this->set(compact('originUsers','destinyUsers','targetUsers','users'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Users/index";		
		$bool_user_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_user_index_permission'));
		
		//pr($clientsAssociatedWithUser);
		//$clientsCreatedByUser=$this->Client->find('all',array(
		//	'conditions'=>array(
		//		'Client.creating_user_id'=>$id,
		//	),
		//	'order'=>'Client.created',
		//));
		//$this->set(compact('clientsCreatedByUser'));
		//$departments = $this->User->Department->find('list',array('order'=>'Department.name ASC'));
		//$companies = $this->User->Company->find('list',array('order'=>'Company.name ASC'));
		/*
		$this->loadModel('ClientUser');
		$this->loadModel('User');
		$this->User->recursive=-1;
		$users = $this->User->find('all',array(
			'fields'=>array('User.id','User.username','User.first_name','User.last_name'),
			'contain'=>array(
				'ClientUser'=>array(
					'conditions'=>array(
						'ClientUser.client_id'=>$id,
					)
				),
			),
			'order'=>'User.first_name,User.last_name',
		));
		$this->set(compact('users'));
		//pr($users);
		
		$this->Client->Contact->recursive=-1;
		$existingContacts=$this->Client->Contact->find('all',array(
			'conditions'=>array(
				'Contact.client_id'=>$id,
			),
		));
		$this->set(compact('existingContacts'));
		if ($this->request->is(array('post', 'put'))) {
			$this->loadModel('Contact');
			
			$existingContactIds=$this->Contact->find('list',array(
				'fields'=>'Contact.id',
				'conditions'=>array(
					'Contact.client_id'=>$id,
				),
			));
			//pr($existingContactIds);
			
			$boolContactsOK=true;
			$flashMessage="";
			foreach ($this->request->data['Contact'] as $contact){
				if (!empty($contact['first_name'])&&!empty($contact['last_name'])){
					$existingContactsInDatabase=$this->Contact->find('list',array(
						'fields'=>array('Contact.id'),
						'conditions'=>array(
							'Contact.first_name'=>$contact['first_name'],
							'Contact.last_name'=>$contact['last_name'],
							'Contact.client_id'=>$id,
							'Contact.id !='=>$existingContactIds,
						),
					));
					//echo "existing contacts for contact ".$contact['first_name']." y apellido ".$contact['last_name']."<br/>";
					//pr($existingContactsInDatabase);
					if (count($existingContactsInDatabase)>0){
						$flashMessage="Ya existe un contacto con nombre ".$contact['first_name']." y apellido ".$contact['last_name'].".  No se guardó el cliente.";
						$boolContactsOK=false;
					}
				}
			}
			
			if (!$boolContactsOK){
				$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			}
			else {
				$datasource=$this->Client->getDataSource();
				$datasource->begin();
				try {
					$this->Client->ClientUser->recursive=-1;
					$previousClientUsers=$this->Client->ClientUser->find('all',array(
						'fields'=>array('ClientUser.id'),
						'conditions'=>array(
							'ClientUser.client_id'=>$id,
						),
					));
					if (!empty($previousClientUsers)){
						foreach ($previousClientUsers as $previousClientUser){
							$this->Client->ClientUser->id=$previousClientUser['ClientUser']['id'];
							$this->Client->ClientUser->delete($previousClientUser['ClientUser']['id']);
						}
					}
				
					//pr($this->request->data);
					$this->Client->id=$id;
					if (!$this->Client->save($this->request->data)) {
						echo "Problema guardando el cliente";
						pr($this->validateErrors($this->Client));
						throw new Exception();
					}
					$client_id=$this->Client->id;
					
					for ($u=0;$u<count($this->request->data['User']);$u++){
						//pr($this->request->data['User'][$u]);
						if ($this->request->data['User'][$u]['id']){
							$clientUserArray=array();
							$this->Client->ClientUser->create();
							
							$clientUserArray['ClientUser']['client_id']=$client_id;
							$clientUserArray['ClientUser']['user_id']=$users[$u]['User']['id'];
							if (!$this->Client->ClientUser->save($clientUserArray)){
								echo "Problema guardando el vendedor para el cliente";
								pr($this->validateErrors($this->Client->ClientUser));
								throw new Exception();
							}
						}
					}
					
					$i=0;
					foreach ($this->request->data['Contact'] as $contact){
						if ($i<count($existingContacts)){
							if (!empty($contact['first_name'])&&!empty($contact['last_name'])){
								
								$contactArray=array();
								$contactArray['Contact']['id']=$existingContacts[$i]['Contact']['id'];
								$contactArray['Contact']['first_name']=$contact['first_name'];
								$contactArray['Contact']['last_name']=$contact['last_name'];
								$contactArray['Contact']['phone']=$contact['phone'];
								$contactArray['Contact']['cell']=$contact['cell'];
								$contactArray['Contact']['email']=$contact['email'];
								$contactArray['Contact']['department']=$contact['department'];
								$contactArray['Contact']['bool_active']=true;
								$contactArray['Contact']['client_id']=$client_id;
								if (!$this->Contact->save($contactArray)) {
									echo "Problema guardando los contactos del cliente";
									pr($this->validateErrors($this->Contact));
									throw new Exception();
								}
							}
						}
						else { 
							if (!empty($contact['first_name'])&&!empty($contact['last_name'])){
								$contactArray=array();
								$contactArray['Contact']['first_name']=$contact['first_name'];
								$contactArray['Contact']['last_name']=$contact['last_name'];
								$contactArray['Contact']['phone']=$contact['phone'];
								$contactArray['Contact']['cell']=$contact['cell'];
								$contactArray['Contact']['email']=$contact['email'];
								$contactArray['Contact']['department']=$contact['department'];
								$contactArray['Contact']['bool_active']=true;
								$contactArray['Contact']['client_id']=$client_id;
								$this->Contact->create();
								if (!$this->Contact->save($contactArray)) {
									echo "Problema guardando los contactos del cliente";
									pr($this->validateErrors($this->Contact));
									throw new Exception();
								}
							}
						}
						$i++;
					}
					$datasource->commit();
					$this->recordUserAction($this->Client->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró el cliente ".$this->request->data['Client']['name']);
					
					$this->Session->setFlash(__('Se guardó el cliente.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					//pr($e);
					$this->Session->setFlash(__('No se podía guardar el cliente.'), 'default',array('class' => 'error-message'));
				}
			}
		} 
		else {
			$options = array('conditions' => array('Client.id'=> $id));
			$this->request->data = $this->Client->find('first', $options);
		}
		
		$userIdsAssociatedWithClient=$this->ClientUser->find('list',array(
			'fields'=>array('ClientUser.user_id'),
			'conditions'=>array(
				'ClientUser.client_id'=>$id,
			),
		));
		$usersAssociatedWithClient=$this->User->find('all',array(
			'conditions'=>array(
				'User.id'=>$userIdsAssociatedWithClient,
			),
			'order'=>'User.first_name ASC,User.last_name ASC',
		));
		//$this->set(compact('usersAssociatedWithClient'));
		//pr($users);
		*/
	}

}
