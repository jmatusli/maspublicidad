<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');


class UsersController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('login','logout');		
	}
	
	public function login() {
		if ($this->request->is('post')) {
			//pr($this->Auth->user);
			if ($this->Auth->login()) {
        $boolActive=$this->Auth->User('bool_active');
        //pr($this->Auth->User);
        //echo "bool active is ".$boolActive."<br/>";
        if ($boolActive){
          $this->recordUserActivity($this->data['User']['username'],"Login successful");
          $this->Session->write('User.username',$this->data['User']['username']);
          $this->Session->write('User.userid',$this->Auth->User('id'));
          $this->Session->write('User.role_id',$this->Auth->User('role_id'));
          $this->Session->write('User.id',$this->Auth->User('id'));
          
          //$userid = $this->Auth->User('id');
          //echo "user id ".$userid."!<br/>";
          $role = $this->Auth->User('role_id');
          //echo "role id ".$role."!<br/>";
          return $this->redirect(parent::userhome($role));
        }
			}
			$this->recordUserActivity($this->data['User']['username'],"Invalid username or password");
			$this->Session->setFlash(__('Invalid username or password, try again'));
		}
	}

	public function logout() {
		$this->recordUserActivity($this->Session->read('User.username'),"Logout");
		return $this->redirect($this->Auth->logout());
	}	
	
	public function index() {
		$this->User->recursive = 0;
		
		$companyId=0;
		if ($this->request->is('post')) {
			$companyId=$this->request->data['Report']['company_id'];
		}
		else if ($this->Session->check('companyId')){
			$companyId=$_SESSION['companyId'];
		}
		$_SESSION['companyId']=$companyId;
		$this->set(compact('companyId'));
		
		$conditions=array();
		if (!empty($companyId)) { 
			$conditions[]=array('User.company_id'=>$companyId);
		}
		
		$userCount=	$this->User->find('count', array(
			'fields'=>array('User.id'),
			'conditions' => $conditions,
		));
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(				
				'Role',
				'Department',
				'Company',
				'Employee',
				'Client',
				'ClientUser',
			),
			'order'=>array('User.family_name'=>'ASC','User.first_name'=>'ASC'),
			'limit'=>($userCount!=0?$userCount:1),
		);

		$users = $this->Paginator->paginate('User');
		$this->set(compact('users'));
		
		$this->loadModel('Company');
		$companies=$this->Company->find('list',array(
			'order'=>'Company.name ASC',
		));
		$this->set(compact('companies'));
		//pr($users);
	}

	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
    $this->loadModel('Client');
		$this->loadModel('Quotation');
		$this->loadModel('Invoice');
    $this->loadModel('InvoiceSalesOrder');
		$this->loadModel('ExchangeRate');
    
    $loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('userRoleId'));
		
    $unassociatedDisplayOptions=array(
			'0'=>'Mostrar solamente clientes asociados',
			'1'=>'Mostrar clientes asociados y no asociados',
		);
    $this->set(compact('unassociatedDisplayOptions'));
    $activeDisplayOptions=array(
			'0'=>'Mostrar solamente clientes activos',
			'1'=>'Mostrar clientes activos y no activos',
			'2'=>'Mostrar clientes desactivados',
		);
		$this->set(compact('activeDisplayOptions'));
		$vipDisplayOptions=array(
			'0'=>'Mostrar todos clientes',
			'1'=>'Mostrar solamente clientes VIP',
			'2'=>'Mostrar clientes no VIP',
		);
		$this->set(compact('vipDisplayOptions'));
    $aggregateOptions=array(
			'0'=>'No mostrar acumulados, ordenar por nombre cliente',
			'1'=>'Mostrar facturas y cotizaciones, ordenado por ventas, cotizaciones y cliente',
			'2'=>'Mostrar cotizaciones y facturas, ordenado por cotizaciones, ventas y cliente',
		);
		$this->set(compact('aggregateOptions'));
    $historyDisplayOptions=array(
			'0'=>'No mostrar el historial de asignaciones al cliente',
			'1'=>'Mostrar el historial de asignaciones al cliente',
		);
		$this->set(compact('historyDisplayOptions'));
		
    define('SHOW_CLIENT_UNASSOCIATED_NO','0');
    define('SHOW_CLIENT_UNASSOCIATED_YES','1');
    
		define('SHOW_CLIENT_ACTIVE_YES','0');
		define('SHOW_CLIENT_ACTIVE_ALL','1');
		define('SHOW_CLIENT_ACTIVE_NO','2');
		
		define('SHOW_CLIENT_VIP_ALL','0');
		define('SHOW_CLIENT_VIP_YES','1');
		define('SHOW_CLIENT_VIP_NO','2');
		
    define('AGGREGATES_NONE','0');
		define('AGGREGATES_INVOICES_QUOTATIONS','1');
		define('AGGREGATES_QUOTATIONS_INVOICES','2');
    
    define('HISTORY_NONE','0');
		define('HISTORY_FULL','1');
    
		$currencyId=CURRENCY_USD;
		
    $unassociatedDisplayOptionId=SHOW_CLIENT_UNASSOCIATED_NO;
    $activeDisplayOptionId=SHOW_CLIENT_ACTIVE_YES;
		$vipDisplayOptionId=SHOW_CLIENT_VIP_ALL;
    $aggregateOptionId=AGGREGATES_INVOICES_QUOTATIONS;
    $historyDisplayOptionId=HISTORY_NONE;
		$searchTerm="";
		
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$currencyId=$this->request->data['Report']['currency_id'];
      
      $unassociatedDisplayOptionId=$this->request->data['Report']['unassociated_display_option_id'];
      $activeDisplayOptionId=$this->request->data['Report']['active_display_option_id'];
			$vipDisplayOptionId=$this->request->data['Report']['vip_display_option_id'];
      $aggregateOptionId=$this->request->data['Report']['aggregate_option_id'];
      $historyDisplayOptionId=$this->request->data['Report']['history_display_option_id'];
			
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
		$this->set(compact('currencyId'));
    $this->set(compact('unassociatedDisplayOptionId'));
    $this->set(compact('activeDisplayOptionId','vipDisplayOptionId','aggregateOptionId'));
    $this->set(compact('historyDisplayOptionId'));
		$this->set(compact('searchTerm'));
    
		$invoiceIdsForPeriod=$this->Invoice->find('list',array(
			'fields'=>array('Invoice.id'),
			'conditions'=>array(
				'Invoice.invoice_date >='=>$startDate,
				'Invoice.invoice_date <'=>$endDatePlusOne,
			),
		));
    $invoiceIdsForUserAndPeriod=$this->InvoiceSalesOrder->find('list',array(
      'fields'=>array('InvoiceSalesOrder.invoice_id'),
			'conditions'=>array(
				'InvoiceSalesOrder.user_id'=>$id,
				'InvoiceSalesOrder.invoice_id'=>$invoiceIdsForPeriod,
			),
    ));
    
    $clientConditions=[];
    if ($activeDisplayOptionId!=SHOW_CLIENT_ACTIVE_ALL){
			if ($activeDisplayOptionId==SHOW_CLIENT_ACTIVE_YES){
				$clientConditions[]=array('Client.bool_active'=>true);
			}
			else {
				$clientConditions[]=array('Client.bool_active'=>false);
			}
		}
    if ($vipDisplayOptionId!=SHOW_CLIENT_VIP_ALL){
			if ($vipDisplayOptionId==SHOW_CLIENT_VIP_YES){
				$clientConditions[]=array('Client.bool_vip'=>true);
			}
			else {
				$clientConditions[]=array('Client.bool_vip'=>false);
			}
		}
    if (!empty($searchTerm)){
			$clientConditions[]=array('Client.name LIKE'=> '%'.$searchTerm.'%');
		}
    
    $clientIds=$this->Client->find('list',array(
      'fields'=>array('Client.id'),
      'conditions'=>$clientConditions,
    ));
    
		//pr($invoiceIdsForUserAndPeriod);
		$user= $this->User->find('first', array(
			'conditions' => array(
				'User.id' => $id,
			),
			'contain'=>array(
				'ClientUser'=>array(
          'conditions'=>array(
            'ClientUser.client_id'=>$clientIds,
          ),
          'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
        ),
				'Client'=>array(
          'conditions'=>array(
            'Client.id'=>$clientIds,
          ),
          'order'=>'Client.name',
        ),
				'Role',
				'Department',
				'Company',
				'Quotation'=>array(
					'Currency',
					'conditions'=>array(
						'Quotation.quotation_date >='=>$startDate,
						'Quotation.quotation_date <'=>$endDatePlusOne,
            'Quotation.user_id'=>$id,
            'Quotation.client_id'=>$clientIds,
					),
				),
				'InvoiceSalesOrder'=>array(
					'conditions'=>array(
						'InvoiceSalesOrder.invoice_id'=>$invoiceIdsForUserAndPeriod,
            'InvoiceSalesOrder.user_id'=>$id,
					),
					'Invoice'=>array(
            'conditions'=>array(
              'Invoice.client_id'=>$clientIds,
            ),
						'Currency',
					),
				),
				'UserLog'=>array(
					'conditions'=>array(
						'event LIKE '=>'%Log%',
						'created >='=>$startDate,
						'created <'=>$endDatePlusOne,
					),
					'order'=>'created DESC',
				),
			),
		));
		
    $quotationQuantity=0;
		$invoiceQuantity=0;
		$quotationTotal=0;
		$invoiceTotal=0;
		for ($q=0;$q<count($user['Quotation']);$q++){
			// set the exchange rate
      $quotationQuantity+=1;
			$quotationDate=$user['Quotation'][$q]['quotation_date'];
			$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
			$user['Quotation'][$q]['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
			if ($currencyId==CURRENCY_CS){
				if ($user['Quotation'][$q]['Currency']['id']==CURRENCY_CS){
					$quotationTotal+=$user['Quotation'][$q]['price_subtotal'];
				}
				elseif ($user['Quotation'][$q]['Currency']['id']==CURRENCY_USD){
					//added calculation of totals in CS$
					$quotationTotal+=round($user['Quotation'][$q]['price_subtotal']*$user['Quotation'][$q]['exchange_rate'],2);
				}
			}
			elseif ($currencyId==CURRENCY_USD){
				if ($user['Quotation'][$q]['Currency']['id']==CURRENCY_USD){
					$quotationTotal+=$user['Quotation'][$q]['price_subtotal'];
				}
				elseif ($user['Quotation'][$q]['Currency']['id']==CURRENCY_CS){
					//added calculation of totals in USD
					$quotationTotal+=round($user['Quotation'][$q]['price_subtotal']/$user['Quotation'][$q]['exchange_rate'],2);
				}
			}
		}
    $user['User']['quotation_quantity']=$quotationQuantity;
		$user['User']['quotation_total']=$quotationTotal;
    
		for ($i=0;$i<count($user['InvoiceSalesOrder']);$i++){
      if (!empty($user['InvoiceSalesOrder'][$i]['Invoice'])){
        $invoiceQuantity+=1;
        $invoiceDate=$user['InvoiceSalesOrder'][$i]['Invoice']['invoice_date'];
        //pr($user['InvoiceSalesOrder'][$i]['Invoice']);
        // set the exchange rate
        $exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
        $user['InvoiceSalesOrder'][$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
        if ($currencyId==CURRENCY_CS){
          if ($user['InvoiceSalesOrder'][$i]['Invoice']['Currency']['id']==CURRENCY_CS){
            $invoiceTotal+=$user['InvoiceSalesOrder'][$i]['Invoice']['price_subtotal'];
          }
          elseif ($invoice['Currency']['id']==CURRENCY_USD){
            //added calculation of totals in CS$
            $invoiceTotal+=round($user['InvoiceSalesOrder'][$i]['Invoice']['price_subtotal']*$user['InvoiceSalesOrder'][$i]['Invoice']['exchange_rate'],2);
          }
        }
        elseif ($currencyId==CURRENCY_USD){
          if ($user['InvoiceSalesOrder'][$i]['Invoice']['Currency']['id']==CURRENCY_USD){
            $invoiceTotal+=$user['InvoiceSalesOrder'][$i]['Invoice']['price_subtotal'];
          }
          elseif ($user['InvoiceSalesOrder'][$i]['Invoice']['Currency']['id']==CURRENCY_CS){
            //added calculation of totals in USD
            $invoiceTotal+=round($user['InvoiceSalesOrder'][$i]['Invoice']['price_subtotal']/$user['InvoiceSalesOrder'][$i]['Invoice']['exchange_rate'],2);
          }
        }
      }
		}
    $user['User']['invoice_quantity']=$invoiceQuantity;
		$user['User']['invoice_total']=$invoiceTotal;
    $this->set(compact('user'));
    
    $clientIdList=[];
    //20171213 the idea of an unassigned client list is attractive, but it is easier to make the distinction based upon the ClientUser contained in the array,which serves to show the historical listing in any case
    //$unassignedClientIdList=[];
    //for ($cu=0;$cu<count($user['ClientUser']);$cu++){
    //  if (!in_array($user['ClientUser'][$cu]['client_id'],$clientIdList)&&!in_array($user['ClientUser'][$cu]['client_id'],$unassignedClientIdList)){
    //    if ($user['ClientUser'][$cu]['bool_assigned']){
    //      $clientIdList[]=$user['ClientUser'][$cu]['client_id'];
    //    }
    //    else {
    //     $unassignedClientIdList[]=$user['ClientUser'][$cu]['client_id'];
    //    }
    //  }
    //}
    for ($cu=0;$cu<count($user['ClientUser']);$cu++){
      if (!in_array($user['ClientUser'][$cu]['client_id'],$clientIdList)){
        $clientIdList[]=$user['ClientUser'][$cu]['client_id'];
      }
    }
    //pr($clientIdList);
		$this->loadModel('Client');
    $uniqueClients=$this->Client->find('all',array(
      //'conditions'=>array(
      //  'OR'=>array(
      //    array('Client.id'=>$clientIdList),
      //    array('Client.id'=>$unassignedClientIdList),
      //  )
      //),
      'conditions'=>array(
        'Client.id'=>$clientIdList,
      ),
      'contain'=>array(					
        'Quotation'=>array(
          'conditions'=>array(
            'Quotation.quotation_date >='=>$startDate,
            'Quotation.quotation_date <'=>$endDatePlusOne,
            'Quotation.user_id'=>$id,
            'Quotation.client_id'=>$clientIds,
          ),
          'Currency',
        ),
        'Invoice'=>array(
          'conditions'=>array(
            'Invoice.id'=>$invoiceIdsForUserAndPeriod,
            'Invoice.client_id'=>$clientIds,
          ),
          'Currency',
        ),
        'ClientUser'=>array(
          'conditions'=>array(
            'ClientUser.user_id'=>$id,
          ),
          'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
        )
  		),
      'order'=>'Client.name'
    ));
    //pr($uniqueClients);
    
		for ($uc=0;$uc<count($uniqueClients);$uc++){
			$quotationTotal=0;
			$invoiceTotal=0;
			for ($q=0;$q<count($uniqueClients[$uc]['Quotation']);$q++){
				// set the exchange rate
				$quotationDate=$uniqueClients[$uc]['Quotation'][$q]['quotation_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
				$uniqueClients[$uc]['Quotation'][$q]['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				if ($currencyId==CURRENCY_CS){
					if ($uniqueClients[$uc]['Quotation'][$q]['Currency']['id']==CURRENCY_CS){
						$quotationTotal+=$uniqueClients[$uc]['Quotation'][$q]['price_subtotal'];
					}
					elseif ($uniqueClients[$uc]['Quotation'][$q]['Currency']['id']==CURRENCY_USD){
						//added calculation of totals in CS$
						$quotationTotal+=round($uniqueClients[$uc]['Quotation'][$q]['price_subtotal']*$uniqueClients[$uc]['Quotation'][$q]['exchange_rate'],2);
					}
				}
				elseif ($currencyId==CURRENCY_USD){
					if ($uniqueClients[$uc]['Quotation'][$q]['Currency']['id']==CURRENCY_USD){
						$quotationTotal+=$uniqueClients[$uc]['Quotation'][$q]['price_subtotal'];
					}
					elseif ($uniqueClients[$uc]['Quotation'][$q]['Currency']['id']==CURRENCY_CS){
						//added calculation of totals in USD
						$quotationTotal+=round($uniqueClients[$uc]['Quotation'][$q]['price_subtotal']/$uniqueClients[$uc]['Quotation'][$q]['exchange_rate'],2);
					}
				}
			}
			$uniqueClients[$uc]['Client']['quotation_total']=$quotationTotal;
			for ($i=0;$i<count($uniqueClients[$uc]['Invoice']);$i++){
        //pr($uniqueClients[$uc]['Invoice'][$i]);
				// set the exchange rate
				$invoiceDate=$uniqueClients[$uc]['Invoice'][$i]['invoice_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
				$uniqueClients[$uc]['Invoice'][$i]['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				if ($currencyId==CURRENCY_CS){
					if ($uniqueClients[$uc]['Invoice'][$i]['Currency']['id']==CURRENCY_CS){
						$invoiceTotal+=$uniqueClients[$uc]['Invoice'][$i]['Invoice']['price_subtotal'];
					}
					elseif ($invoice['Currency']['id']==CURRENCY_USD){
						//added calculation of totals in CS$
						$invoiceTotal+=round($uniqueClients[$uc]['Invoice'][$i]['price_subtotal']*$uniqueClients[$uc]['Invoice'][$i]['exchange_rate'],2);
					}
				}
				elseif ($currencyId==CURRENCY_USD){
					if ($uniqueClients[$uc]['Invoice'][$i]['Currency']['id']==CURRENCY_USD){
						$invoiceTotal+=$uniqueClients[$uc]['Invoice'][$i]['price_subtotal'];
					}
					elseif ($uniqueClients[$uc]['Invoice'][$i]['Currency']['id']==CURRENCY_CS){
						//added calculation of totals in USD
						$invoiceTotal+=round($uniqueClients[$uc]['Invoice'][$i]['price_subtotal']/$uniqueClients[$uc]['Invoice'][$i]['exchange_rate'],2);
					}
				}
			}
			$uniqueClients[$uc]['Client']['invoice_total']=$invoiceTotal;
		}
    switch ($aggregateOptionId){
      case AGGREGATES_NONE:
        //usort($uniqueClients,array($this,'sortByCompanyName'));
        break;
      case AGGREGATES_INVOICES_QUOTATIONS:
        usort($uniqueClients,array($this,'sortByInvoiceTotalQuotationTotalCompanyName'));
        break;
      case AGGREGATES_QUOTATIONS_INVOICES:
        usort($uniqueClients,array($this,'sortByQuotationTotalInvoiceTotalCompanyName'));
        break;
    }
		$this->set(compact('uniqueClients'));
		
		
		$aco_name="Clients/edit";		
		$bool_client_edit_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_edit_permission'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
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
		if( $a['Client']['invoice_total'] == $b['Client']['invoice_total'] ){ 
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
  
  public function guardarVistaUsuario($userName) {
		$exportData=$_SESSION['tablasDeVistaUsuario'];
    $this->set(compact('userName'));
		$this->set(compact('exportData'));
	}

	public function add() {
		$this->loadModel('Client');
		$this->Client->recursive=-1;
		$clients = $this->Client->find('all',array(
			'fields'=>array('Client.id','Client.name'),
			'conditions'=>array(
				//'Client.bool_vip'=>true,
				'Client.bool_active'=>true,
			),
			'order'=>'Client.name',
		));
		$this->set(compact('clients'));
		
		if ($this->request->is('post')) {
			$datasource=$this->User->getDataSource();
			$datasource->begin();
			try {
				//pr($this->request->data);
				$this->User->create();
				if (!$this->User->save($this->request->data)) {
					echo "Problema guardando el usuario";
					pr($this->validateErrors($this->User));
					throw new Exception();
				}
				$user_id=$this->User->id;
				if (!empty($this->request->data['Client'])){
					for ($c=0;$c<count($this->request->data['Client']);$c++){
						if ($this->request->data['Client'][$c]['id']){
							$clientUserArray=array();
							$this->User->ClientUser->create();
							$clientUserArray['ClientUser']['client_id']=$clients[$c]['Client']['id'];
							$clientUserArray['ClientUser']['user_id']=$user_id;
							if (!$this->User->ClientUser->save($clientUserArray)){
								echo "Problema guardando el cliente para el usuario";
								pr($this->validateErrors($this->User->ClientUser));
								throw new Exception();
							}
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->User->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se registró el usuario ".$this->request->data['User']['username']);
				$this->Session->setFlash(__('The user has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				$this->recordUserActivity($this->Session->read('User.username'),"Tried to add user unsuccessfully");
			}
		}
		
    $roles = $this->User->Role->find('list',['order'=>'list_order ASC']);
		$departments = $this->User->Department->find('list',['order'=>'name ASC']);
		$companies = $this->User->Company->find('list',['order'=>'name ASC']);
		$this->set(compact('roles','departments','companies'));
	}

	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->loadModel('ClientUser');
		$this->loadModel('Client');
		$this->Client->recursive=-1;
		$clients = $this->Client->find('all',array(
			'fields'=>array('Client.id','Client.name'),
			'conditions'=>array(
				//'Client.bool_vip'=>true,
				'Client.bool_active'=>true,
			),
			'contain'=>array(
				'ClientUser'=>array(
					'conditions'=>array(
						'ClientUser.user_id'=>$id,
					),
					'order'=>'ClientUser.id DESC',
				),
			),
			'order'=>'Client.name',
		));
		$this->set(compact('clients'));
		
		if ($this->request->is(array('post', 'put'))) {	
      if (empty($this->request->data['User']['pwd'])){
        $this->Session->setFlash(__('Por favor escribe una contraseña.  No se guardó el usuario.'), 'default',array('class' => 'error-message'));
      }
			else {
        $datasource=$this->User->getDataSource();
        $datasource->begin();
        try {
          //pr($this->request->data);
          //20170517 YA NO SE ELIMINAN LOS CLIENT USER PORQUE SE GUARDA EL HISTORIAL
          //$this->User->ClientUser->recursive=-1;
          //$previousClientUsers=$this->User->ClientUser->find('all',array(
          //	'fields'=>array('ClientUser.id'),
          //	'conditions'=>array(
          //		'ClientUser.user_id'=>$id,
          //	),
          //));
          //if (!empty($previousClientUsers)){
          //	foreach ($previousClientUsers as $previousClientUser){
          //		$this->User->ClientUser->id=$previousClientUser['ClientUser']['id'];
          //		$this->User->ClientUser->delete($previousClientUser['ClientUser']['id']);
          //	}
          //}
        
          $this->User->id=$id;
          if (!empty($this->request->data['User']['pwd'])){
            $this->request->data['User']['password']=$this->request->data['User']['pwd'];
          }
          //pr($this->request->data['User']);
          //unset($this->request->data['User']['role_id']);
          if (!$this->User->save($this->request->data['User'])) {
            echo "Problema guardando el usuario";
            pr($this->validateErrors($this->User));
            throw new Exception();
          }
          $user_id=$this->User->id;
          if ($this->request->data['User']['bool_active']){
            if (!empty($this->request->data['Client'])){
              $currentDateTime=new DateTime();
              for ($c=0;$c<count($this->request->data['Client']);$c++){						
                $clientUserArray=array();
                $this->User->ClientUser->create();
                $clientUserArray['ClientUser']['client_id']=$clients[$c]['Client']['id'];
                $clientUserArray['ClientUser']['user_id']=$user_id;
                $clientUserArray['ClientUser']['assignment_datetime']=$currentDateTime->format('Y-m-d H:i:s');
                $clientUserArray['ClientUser']['bool_assigned']=$this->request->data['Client'][$c]['id'];
                if (!$this->User->ClientUser->save($clientUserArray)){
                  echo "Problema guardando el cliente para el usuario";
                  pr($this->validateErrors($this->User->ClientUser));
                  throw new Exception();
                }
              }
            }
          }
          else {
            $this->User->ClientUser->recursive=-1;
            $previousClientUsers=$this->User->ClientUser->find('all',array(
            	'fields'=>array('ClientUser.id'),
            	'conditions'=>array(
            		'ClientUser.user_id'=>$id,
            	),
            ));
            if (!empty($previousClientUsers)){
            	foreach ($previousClientUsers as $previousClientUser){
            		$this->User->ClientUser->id=$previousClientUser['ClientUser']['id'];
            		$this->User->ClientUser->delete($previousClientUser['ClientUser']['id']);
            	}
            }
          }
          $datasource->commit();
          $this->recordUserAction($this->User->id,null,null);
          $this->recordUserActivity($this->Session->read('User.username'),"Se registró el usuario ".$this->request->data['User']['username']);
          $this->Session->setFlash(__('The user has been saved.'),'default',array('class' => 'success'));
          return $this->redirect(array('action' => 'index'));
        } 
        catch(Exception $e){
          $datasource->rollback();
          pr($e);
          $this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
          $this->recordUserActivity($this->Session->read('User.username'),"Tried to add user unsuccessfully");
        }
      }
		} 
		else {
			$options = array(
				'conditions' => array(
					'User.id'=> $id,
				),
			);
			$this->request->data = $this->User->find('first', $options);
      $this->request->data['User']['pwd'] = $this->request->data['User']['password'];
		}
    //pr($this->request->data);
		$clientIdsAssociatedWithUser=array();		
		foreach ($clients as $client){
			$boolAssociatedWithUser=$this->ClientUser->checkAssociationClientWithUser($client['Client']['id'],$id);
			if ($boolAssociatedWithUser){
				$clientIdsAssociatedWithUser[]=$client['Client']['id'];
			}
		}
		
		//pr($clientIdsAssociatedWithUser);
		$clientsAssociatedWithUser=$this->Client->find('all',array(
			'conditions'=>array(
				'Client.id'=>$clientIdsAssociatedWithUser,
			),
			'order'=>'Client.name',
		));
		$this->set(compact('clientsAssociatedWithUser'));
		//pr($clientsAssociatedWithUser);
		$clientsCreatedByUser=$this->Client->find('all',array(
			'conditions'=>array(
				'Client.creating_user_id'=>$id,
			),
			'order'=>'Client.created',
		));
		$this->set(compact('clientsCreatedByUser'));
		$roles = $this->User->Role->find('list',['order'=>'list_order ASC']);
		$departments = $this->User->Department->find('list',array('order'=>'Department.name ASC'));
		$companies = $this->User->Company->find('list',array('order'=>'Company.name ASC'));
		$this->set(compact('roles','departments','companies'));
	}

/*
	public function resumenUsuariosInventario() {
		$this->User->recursive = 0;
		$users=$this->Paginator->paginate();
		$this->set(compact('users'));
		//pr($users);
	}

	public function verUsuarioInventario($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}
	
	public function crearUsuarioInventario() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'),'default',array('class' => 'success'));
				$this->recordUserActivity($this->Session->read('User.username'),"Added new user ".$this->request->data['User']['username']);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				$this->recordUserActivity($this->Session->read('User.username'),"Tried to add user unsuccessfully");
			}
		}
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles'));
		
	}

	public function editarUsuarioInventario($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'),'default',array('class' => 'success'));
				$this->recordUserActivity($this->Session->read('User.username'),"Edited user ".$this->request->data['User']['username']);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->recordUserActivity($this->Session->read('User.username'),"Tried to edit user ".$this->request->data['User']['username']." without success");
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles'));
		
	}
*/
	
	public function delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		$user=$this->User->find('first',array(
			'conditions'=>array(
				'User.id'=>$id,
			),
			'contain'=>array(
				'Quotation',
				'InvoiceSalesOrder'=>array(
					'Invoice'
				),
				
				'ClientUser',
			),
		));
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (count($user['Quotation'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Este usuario tiene cotizaciones correspondientes.  Para poder eliminar el usuario, primero hay que eliminar o modificar las cotizaciones ";
			if (count($user['Quotation'])==1){
				$flashMessage.=$user['Quotation'][0]['quotation_code'].".";
			}
			else {
				for ($i=0;$i<count($user['Quotation']);$i++){
					$flashMessage.=$user['Quotation'][$i]['quotation_code'];
					if ($i==count($user['Quotation'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (count($user['InvoiceSalesOrder'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Este usuario tiene facturas correspondientes.  Para poder eliminar el usuario, primero hay que eliminar o modificar las facturas ";
			if (count($user['InvoiceSalesOrder'])==1){
				$flashMessage.=$user['InvoiceSalesOrder'][0]['Invoice']['invoice_code'].".";
			}
			else {
				for ($i=0;$i<count($user['InvoiceSalesOrder']);$i++){
					$flashMessage.=$user['InvoiceSalesOrder'][$i]['Invoice']['invoice_code'];
					if ($i==count($user['InvoiceSalesOrder'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó el usuario.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			$datasource=$this->User->getDataSource();
			$datasource->begin();	
			try {
				//delete all associations with clients
				foreach ($user['ClientUser'] as $clientUser){
					if (!$this->User->ClientUser->delete($clientUser['id'])) {
						echo "Problema al eliminar la asociación con clientes para el usuario";
						pr($this->validateErrors($this->User->ClientUser));
						throw new Exception();
					}
				}
				if (!$this->User->delete($id)) {
					echo "Problema al eliminar el usuario";
					pr($this->validateErrors($this->User));
					throw new Exception();
				}
						
				$datasource->commit();
					
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$user['User']['id'];
				$deletionArray['Deletion']['reference']=$user['User']['username'];
				$deletionArray['Deletion']['type']='User';
				$this->Deletion->save($deletionArray);
				
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó el usuario  ".$user['User']['username']." con id ".$user['User']['id']);
						
				$this->Session->setFlash(__('Se eliminó el usuario y sus asociaciones con clientes.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía eliminar el usuario.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function adminmanual() {
		$this->autoRender = false;
		//$this->response->file($file['path']);
		$this->response->file('webroot/files/MP_Admin_Manual.pdf', array('download' => false, 'name' => 'Mas Publicidad Admin Manual'));
		//Return response object to prevent controller from trying to render a view
		return $this->response;
	}
	
	public function vendormanual() {
		$this->autoRender = false;
		//$this->response->file($file['path']);
		$this->response->file('webroot/files/MP_Vendor_Manual.pdf', array('download' => false, 'name' => 'Mas Publicidad Vendor Manual'));
		//Return response object to prevent controller from trying to render a view
		return $this->response;
	}
	
	public function rolePermissions(){
		$this->loadModel('Role');
		
		$roles=$this->Role->find('all',[
      'conditions'=>[
        'Role.id !='=>ROLE_ADMIN,
      ],
    ]);
		$this->set(compact('roles'));
		
		$consideredControllerAliases=[
			'Quotations',
			'SalesOrders',
			'Invoices',
			
			'PaymentModes',
			'SalesObjectives',
		];
		
		$selectedControllers=$this->Acl->Aco->find('all',[
			'conditions'=>[
				'Aco.parent_id'=>'1',
				'Aco.alias'=>$consideredControllerAliases,
			],
		]);
		//pr($selectedControllers);
		
		$excludedActions=[
			'controllers',
			'recordUserActivity',
			'userhome',
			'get_date',
			'recordUserAction',
			'uploadFiles',
			'hasPermission',
			'recreateStockItemLogs',
			'normalizeChars',
			//20170526 REDUCTION
			'guardarResumen',
			//20170605 EXPANSION
			//'pdf',
			
			'getquotationproducts', // for quotations
			'getquotationinfo', // for quotations
			'getnewquotationcode', // for quotations
			'getquotationsforclient', // for quotations
			'getquotationcurrencyid', // for quotations
			'generarOrdenDeVenta', // for quotations
			'getquotationiva', // for quotations
			//20170526 REDUCTION
			'guardarReporteCotizacionesPorEjecutivo', // for quotations
			'guardarReporteCotizacionesPorCliente', // for quotations
			'guardarReporteCotizacionesPorCategoria', // for quotations
			'guardarReporteGestionDeVentas', // for quotations
			
			'getSalesOrderProducts', // for sales orders
			'getsalesorderinfo', // for sales orders
      'getSalesOrderInfo', // for sales orders
			'getsalesordersforclient', // for sales orders
      'getSalesOrdersForClient', // for sales orders
			'getsalesordercurrencyid', // for sales orders
			'getsalesorderinfonofinance', // for sales orders
			//20170526 REDUCTION
			'guardarReporteOrdenesDeVentaPorEstado', // for sales orders
			
			'getnewinvoicecode', // for invoices
			'sortBySaldo', // for invoices
			//20170526 REDUCTION
			'guardarReporteFacturasPorEjecutivo', // for invoices
			'guardarCuentasPorCobrar', // for invoices
			'guardarComisionesPorVendedor', // for invoices
			'guardarResumenRecibos', // for invoices
			'verPdfComisionesPorVendedor', // for invoices
      'sortByTotalForClient', // for invoices
		];
		
		for ($c=0;$c<count($selectedControllers);$c++){
			$selectedActions=[];
			$conditions=[
        'Aco.parent_id'=>$selectedControllers[$c]['Aco']['id'],
        'Aco.alias !='=>$excludedActions,
      ];
      $controllerName=$selectedControllers[$c]['Aco']['alias'];
      if ($controllerName=='StockItems'){
        $conditions[]=['Aco.alias !='=>['index','view','add','edit','delete',]];
      }
			$selectedActions=$this->Acl->Aco->find('all',[
				'conditions'=>$conditions,
			]);
			if (!empty($selectedActions)){
				for ($a=0;$a<count($selectedActions);$a++){
					$rolePermissions=[];
					for ($r=0;$r<count($roles);$r++){
						$aco_name=$selectedControllers[$c]['Aco']['alias']."/".$selectedActions[$a]['Aco']['alias'];
						//pr($aco_name);
						$hasPermission=$this->Acl->check(['Role'=>['id'=>$roles[$r]['Role']['id']]],$aco_name);
						//if ($selectedActions[$a]['Aco']['id']==15){
						//	echo "permission for ".$aco_name." is ".$hasPermission."<br/>";
						//}
						if ($hasPermission){
							$rolePermissions[$r]=$hasPermission;
						}
						else {
							$rolePermissions[$r]=0;
						}						
					}
					//if ($selectedActions[$a]['Aco']['id']==15){
					//	pr($rolePermissions);
					//}
					$selectedActions[$a]['rolePermissions']=$rolePermissions;
				}
			}
			//pr($selectedActions);
			
			$selectedControllers[$c]['actions']=$selectedActions;
		}
		$this->set(compact('selectedControllers'));
		//pr($selectedControllers);
		if ($this->request->is('post')) {
			//pr($this->request->data);
			$role = $this->User->Role;
			for ($r=0;$r<count($this->request->data['Role']);$r++){
				$thisRole=$roles[$r];
				//pr($role);
				$role_id=$thisRole['Role']['id'];
				
				$role->id=$role_id;
				
				for ($c=0;$c<count($this->request->data['Role'][$r]['Controller']);$c++){
					$controller=$selectedControllers[$c];
					//pr($controller);
					$controller_alias=$controller['Aco']['alias'];
					if ($controller['Aco']['id']==116){
						//pr($this->request->data['Role'][$r]['Controller'][$c]);
						//pr($controller);
					}
					for ($a=0;$a<count($this->request->data['Role'][$r]['Controller'][$c]['Action']);$a++){
						//pr($this->request->data['Role'][$r]['Controller'][$c]['Action'][$a]);
						
						$action=$selectedControllers[$c]['actions'][$a];
						//pr($action);
						$action_alias=$action['Aco']['alias'];
						//pr($action_alias);
						
						if ($this->request->data['Role'][$r]['Controller'][$c]['Action'][$a]){
							$this->Acl->allow($role, 'controllers/'.$controller_alias."/".$action_alias);
						}
						else {
							$this->Acl->deny($role, 'controllers/'.$controller_alias."/".$action_alias);
						}
						$this->Session->setFlash('Los permisos se guardaron.','default',['class' => 'success']);
						//$role->id = 5;
						//$this->Acl->allow($role, 'controllers');
						//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
						//$this->Acl->deny($role, 'controllers/StockMovementTypes');
						//$this->Acl->deny($role, 'controllers/Role');			
					}					
				}				
			}
			/*
			$this->Client->create();
			if ($this->Client->save($this->request->data)) {
				$this->Session->setFlash(__('The client has been saved.'),'default',['class' => 'success'));
				return $this->redirect(['action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The client could not be saved. Please, try again.'), 'default',['class' => 'error-message'));
			}
			*/
      for ($c=0;$c<count($selectedControllers);$c++){
        $selectedActions=[];
        $conditions=[
          'Aco.parent_id'=>$selectedControllers[$c]['Aco']['id'],
          'Aco.alias !='=>$excludedActions,
        ];
        $controllerName=$selectedControllers[$c]['Aco']['alias'];
        if ($controllerName=='StockItems'){
          $conditions[]=['Aco.alias !='=>['index','view','add','edit','delete',]];
        }
        $selectedActions=$this->Acl->Aco->find('all',[
          'conditions'=>$conditions,
        ]);
        if (!empty($selectedActions)){
          for ($a=0;$a<count($selectedActions);$a++){
            $rolePermissions=[];
            for ($r=0;$r<count($roles);$r++){
              $aco_name=$selectedControllers[$c]['Aco']['alias']."/".$selectedActions[$a]['Aco']['alias'];
              //pr($aco_name);
              $hasPermission=$this->Acl->check(['Role'=>['id'=>$roles[$r]['Role']['id']]],$aco_name);
              //if ($selectedActions[$a]['Aco']['id']==15){
              //	echo "permission for ".$aco_name." is ".$hasPermission."<br/>";
              //}
              if ($hasPermission){
                $rolePermissions[$r]=$hasPermission;
              }
              else {
                $rolePermissions[$r]=0;
              }						
            }
            //if ($selectedActions[$a]['Aco']['id']==15){
            //	pr($rolePermissions);
            //}
            $selectedActions[$a]['rolePermissions']=$rolePermissions;
          }
        }
        //pr($selectedActions);
        
        $selectedControllers[$c]['actions']=$selectedActions;
      }
      $this->set(compact('selectedControllers'));
		}
		
		
	}
	
  public function roleProductionPermissions(){
		$this->loadModel('Role');
		
		$roles=$this->Role->find('all',[
      'conditions'=>[
        'Role.id !='=>ROLE_ADMIN,
      ],
    ]);
		//pr($roles);
		$this->set(compact('roles'));
		
		$consideredControllerAliases=[
			'OperationLocations',
			'Entries',
      'StockItems',
			'Remissions',
			'InventoryProducts',
			'ProductLines',
			'Departments',	
			'InventoryProviders',
      'Machines',
		];
		
		$selectedControllers=$this->Acl->Aco->find('all',[
			'conditions'=>[
				'Aco.parent_id'=>'1',
				'Aco.alias'=>$consideredControllerAliases,
			],
		]);
		//pr($selectedControllers);
		
		$excludedActions=[
			'controllers',
			'recordUserActivity',
			'userhome',
			'get_date',
			'recordUserAction',
			'uploadFiles',
			'hasPermission',
			'recreateStockItemLogs',
			'normalizeChars',
			//20170526 REDUCTION
			'guardarResumen',
			//20170605 EXPANSION
			//'pdf',
			
      'getproductwidth', // for inventory products
      
      'getproductprice', // for products
			'getproductimage', // for products
			'saveproduct', // for products
			'getproductsfordepartment', // for products
			'getproductsfordepartmentlisted', // for products
			'getproductinfo', // for products
			'getproductsforproductcategory', // for products
			'getproductsforproductcategorylisted', // for products
			'getproductmeasuringunitid', // for products
			'getproductstatsfordate', // for products
			'getproductsforinventoryproductline', // for products
			'getproductsforinventoryproductlinelisted', // for products
			'getproductsforcategorylisted', // for products
      
      'cuadrarEstadosDeLote', // for stock items
      'recreateStockItemLogsForSquaring', // for stock items
      'recreateAllStockItemLogs', // for stock items
      'cuadrarPreciosBotellas', // for stock items
      'recreateAllBottleCosts', // for stock items
      'recreateProductionMovementPriceForSquaring', // for stock items
      'recreateStockItemPriceForSquaring', // for stock items
      'sortByInventoryProductLineNameProductName', // for stock items
		];
		
		for ($c=0;$c<count($selectedControllers);$c++){
			$selectedActions=[];
      $conditions=[
        'Aco.parent_id'=>$selectedControllers[$c]['Aco']['id'],
        'Aco.alias !='=>$excludedActions,
      ];
      $controllerName=$selectedControllers[$c]['Aco']['alias'];
      if ($controllerName=='StockItems'){
        $conditions[]=['Aco.alias !='=>['index','view','add','edit','delete',]];
      }
			$selectedActions=$this->Acl->Aco->find('all',[
				'conditions'=>$conditions,
			]);
			if (!empty($selectedActions)){
				for ($a=0;$a<count($selectedActions);$a++){
					$rolePermissions=[];
					for ($r=0;$r<count($roles);$r++){
						$aco_name=$selectedControllers[$c]['Aco']['alias']."/".$selectedActions[$a]['Aco']['alias'];
						//pr($aco_name);
						$hasPermission=$this->Acl->check(['Role'=>['id'=>$roles[$r]['Role']['id']]],$aco_name);
						//if ($selectedActions[$a]['Aco']['id']==15){
						//	echo "permission for ".$aco_name." is ".$hasPermission."<br/>";
						//}
						if ($hasPermission){
							$rolePermissions[$r]=$hasPermission;
						}
						else {
							$rolePermissions[$r]=0;
						}						
					}
					//if ($selectedActions[$a]['Aco']['id']==15){
					//	pr($rolePermissions);
					//}
					$selectedActions[$a]['rolePermissions']=$rolePermissions;
				}
			}
			//pr($selectedActions);
			
			$selectedControllers[$c]['actions']=$selectedActions;
		}
		$this->set(compact('selectedControllers'));
		//pr($selectedControllers);
		if ($this->request->is('post')) {
			//pr($this->request->data);
			$role = $this->User->Role;
			for ($r=0;$r<count($this->request->data['Role']);$r++){
				$thisRole=$roles[$r];
				//pr($role);
				$role_id=$thisRole['Role']['id'];
				
				$role->id=$role_id;
				
				for ($c=0;$c<count($this->request->data['Role'][$r]['Controller']);$c++){
					$controller=$selectedControllers[$c];
					//pr($controller);
					$controller_alias=$controller['Aco']['alias'];
					if ($controller['Aco']['id']==116){
						//pr($this->request->data['Role'][$r]['Controller'][$c]);
						//pr($controller);
					}
					for ($a=0;$a<count($this->request->data['Role'][$r]['Controller'][$c]['Action']);$a++){
						//pr($this->request->data['Role'][$r]['Controller'][$c]['Action'][$a]);
						
						$action=$selectedControllers[$c]['actions'][$a];
						//pr($action);
						$action_alias=$action['Aco']['alias'];
						//pr($action_alias);
						
						if ($this->request->data['Role'][$r]['Controller'][$c]['Action'][$a]){
							$this->Acl->allow($role, 'controllers/'.$controller_alias."/".$action_alias);
						}
						else {
							$this->Acl->deny($role, 'controllers/'.$controller_alias."/".$action_alias);
						}
						$this->Session->setFlash(__('Los permisos se guardaron.'),'default',['class' => 'success']);
						//$role->id = 5;
						//$this->Acl->allow($role, 'controllers');
						//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
						//$this->Acl->deny($role, 'controllers/StockMovementTypes');
						//$this->Acl->deny($role, 'controllers/Role');			
					}					
				}				
			}
			/*
			$this->Client->create();
			if ($this->Client->save($this->request->data)) {
				$this->Session->setFlash(__('The client has been saved.'),'default',['class' => 'success'));
				return $this->redirect(['action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The client could not be saved. Please, try again.'), 'default',['class' => 'error-message'));
			}
			*/
      for ($c=0;$c<count($selectedControllers);$c++){
        $selectedActions=[];
        $conditions=[
          'Aco.parent_id'=>$selectedControllers[$c]['Aco']['id'],
          'Aco.alias !='=>$excludedActions,
        ];
        $controllerName=$selectedControllers[$c]['Aco']['alias'];
        if ($controllerName=='StockItems'){
          $conditions[]=['Aco.alias !='=>['index','view','add','edit','delete',]];
        }
        $selectedActions=$this->Acl->Aco->find('all',[
          'conditions'=>$conditions,
        ]);
        if (!empty($selectedActions)){
          for ($a=0;$a<count($selectedActions);$a++){
            $rolePermissions=[];
            for ($r=0;$r<count($roles);$r++){
              $aco_name=$selectedControllers[$c]['Aco']['alias']."/".$selectedActions[$a]['Aco']['alias'];
              //pr($aco_name);
              $hasPermission=$this->Acl->check(['Role'=>['id'=>$roles[$r]['Role']['id']]],$aco_name);
              //if ($selectedActions[$a]['Aco']['id']==15){
              //	echo "permission for ".$aco_name." is ".$hasPermission."<br/>";
              //}
              if ($hasPermission){
                $rolePermissions[$r]=$hasPermission;
              }
              else {
                $rolePermissions[$r]=0;
              }						
            }
            //if ($selectedActions[$a]['Aco']['id']==15){
            //	pr($rolePermissions);
            //}
            $selectedActions[$a]['rolePermissions']=$rolePermissions;
          }
        }
        //pr($selectedActions);
        
        $selectedControllers[$c]['actions']=$selectedActions;
      }
      $this->set(compact('selectedControllers'));
		}
		
		
	}
	
  public function roleConfigPermissions(){
		$this->loadModel('Role');
		
		$roles=$this->Role->find('all',[
      'conditions'=>[
        'Role.id !='=>ROLE_ADMIN,
      ],
    ]);
		//pr($roles);
		$this->set(compact('roles'));
		
		$consideredControllerAliases=[
			'Products',
      
      'Providers',
      
      'Clients',
      'Contacts',
			
			'Users',
			
      'ExchangeRates',
      
			'Employees',
			'EmployeeHolidays',
			'HolidayTypes',
		];
		
		$selectedControllers=$this->Acl->Aco->find('all',[
			'conditions'=>[
				'Aco.parent_id'=>'1',
				'Aco.alias'=>$consideredControllerAliases,
			],
		]);
		//pr($selectedControllers);
		
		$excludedActions=[
			'controllers',
			'recordUserActivity',
			'userhome',
			'get_date',
			'recordUserAction',
			'uploadFiles',
			'hasPermission',
			'recreateStockItemLogs',
			'normalizeChars',
			//20170526 REDUCTION
			'guardarResumen',
			//20170605 EXPANSION
			//'pdf',
			
			
			'getproductprice', // for products
			'getproductimage', // for products
			'saveproduct', // for products
			'getproductsfordepartment', // for products
			'getproductsfordepartmentlisted', // for products
			'getproductinfo', // for products
			'getproductsforproductcategory', // for products
			'getproductsforproductcategorylisted', // for products
			'getproductmeasuringunitid', // for products
			'getproductstatsfordate', // for products
			'getproductsforinventoryproductline', // for products
			'getproductsforinventoryproductlinelisted', // for products
			'getproductsforcategorylisted', // for products
      
			'saveclient',// for clients
			'getclientlistforclientname',// for clients
			'getclientlist',// for clients
			'getclientinfo',// for clients
			'sortByQuotationTotal',// for clients
			'sortByInvoiceTotal',// for clients
      'sortByCompanyName',// for clients
			'sortByInvoiceTotalQuotationTotalCompanyName',// for clients
      'sortByQuotationTotalInvoiceTotalCompanyName',// for clients
			
			'getcontactcount',// for contacts
			'savecontact',// for contacts
			'getcontactlistforcontactname',
			'getcontactlist',// for contacts
			'getcontactinfo',// for contacts
			'sortByClientName',// for contacts
			
			'getexchangerate',//for exchange rates
			
			'login', // for users
			'logout', // for users
			'init_DB_permissions',
			'rolePermissions',
      'roleProductionPermissions',
      'roleConfigPermissions',
		];
		
		for ($c=0;$c<count($selectedControllers);$c++){
			$selectedActions=[];
			$conditions=[
        'Aco.parent_id'=>$selectedControllers[$c]['Aco']['id'],
        'Aco.alias !='=>$excludedActions,
      ];
      $controllerName=$selectedControllers[$c]['Aco']['alias'];
      if ($controllerName=='StockItems'){
        $conditions[]=['Aco.alias !='=>['index','view','add','edit','delete',]];
      }
			$selectedActions=$this->Acl->Aco->find('all',[
				'conditions'=>$conditions,
			]);
			if (!empty($selectedActions)){
				for ($a=0;$a<count($selectedActions);$a++){
					$rolePermissions=[];
					for ($r=0;$r<count($roles);$r++){
						$aco_name=$selectedControllers[$c]['Aco']['alias']."/".$selectedActions[$a]['Aco']['alias'];
						//pr($aco_name);
						$hasPermission=$this->Acl->check(['Role'=>['id'=>$roles[$r]['Role']['id']]],$aco_name);
						//if ($selectedActions[$a]['Aco']['id']==15){
						//	echo "permission for ".$aco_name." is ".$hasPermission."<br/>";
						//}
						if ($hasPermission){
							$rolePermissions[$r]=$hasPermission;
						}
						else {
							$rolePermissions[$r]=0;
						}						
					}
					//if ($selectedActions[$a]['Aco']['id']==15){
					//	pr($rolePermissions);
					//}
					$selectedActions[$a]['rolePermissions']=$rolePermissions;
				}
			}
			//pr($selectedActions);
			
			$selectedControllers[$c]['actions']=$selectedActions;
		}
		$this->set(compact('selectedControllers'));
		//pr($selectedControllers);
		if ($this->request->is('post')) {
			//pr($this->request->data);
			$role = $this->User->Role;
			for ($r=0;$r<count($this->request->data['Role']);$r++){
				$thisRole=$roles[$r];
				//pr($role);
				$role_id=$thisRole['Role']['id'];
				
				$role->id=$role_id;
				
				for ($c=0;$c<count($this->request->data['Role'][$r]['Controller']);$c++){
					$controller=$selectedControllers[$c];
					//pr($controller);
					$controller_alias=$controller['Aco']['alias'];
					if ($controller['Aco']['id']==116){
						//pr($this->request->data['Role'][$r]['Controller'][$c]);
						//pr($controller);
					}
					for ($a=0;$a<count($this->request->data['Role'][$r]['Controller'][$c]['Action']);$a++){
						//pr($this->request->data['Role'][$r]['Controller'][$c]['Action'][$a]);
						
						$action=$selectedControllers[$c]['actions'][$a];
						//pr($action);
						$action_alias=$action['Aco']['alias'];
						//pr($action_alias);
						
						if ($this->request->data['Role'][$r]['Controller'][$c]['Action'][$a]){
							$this->Acl->allow($role, 'controllers/'.$controller_alias."/".$action_alias);
						}
						else {
							$this->Acl->deny($role, 'controllers/'.$controller_alias."/".$action_alias);
						}
						$this->Session->setFlash('Los permisos de config se guardaron.','default',['class' => 'success']);
					}					
				}				
			}
      
      for ($c=0;$c<count($selectedControllers);$c++){
        $selectedActions=[];
        $conditions=[
          'Aco.parent_id'=>$selectedControllers[$c]['Aco']['id'],
          'Aco.alias !='=>$excludedActions,
        ];
        $controllerName=$selectedControllers[$c]['Aco']['alias'];
        if ($controllerName=='StockItems'){
          $conditions[]=['Aco.alias !='=>['index','view','add','edit','delete',]];
        }
        $selectedActions=$this->Acl->Aco->find('all',[
          'conditions'=>$conditions,
        ]);
        if (!empty($selectedActions)){
          for ($a=0;$a<count($selectedActions);$a++){
            $rolePermissions=[];
            for ($r=0;$r<count($roles);$r++){
              $aco_name=$selectedControllers[$c]['Aco']['alias']."/".$selectedActions[$a]['Aco']['alias'];
              //pr($aco_name);
              $hasPermission=$this->Acl->check(['Role'=>['id'=>$roles[$r]['Role']['id']]],$aco_name);
              //if ($selectedActions[$a]['Aco']['id']==15){
              //	echo "permission for ".$aco_name." is ".$hasPermission."<br/>";
              //}
              if ($hasPermission){
                $rolePermissions[$r]=$hasPermission;
              }
              else {
                $rolePermissions[$r]=0;
              }						
            }
            //if ($selectedActions[$a]['Aco']['id']==15){
            //	pr($rolePermissions);
            //}
            $selectedActions[$a]['rolePermissions']=$rolePermissions;
          }
        }
        //pr($selectedActions);
        
        $selectedControllers[$c]['actions']=$selectedActions;
      }
      $this->set(compact('selectedControllers'));
		}	
	}
	
  
  public function init_DB_permissions() {
	
		$role = $this->User->Role;

		// Allow admins to access everything
		$role->id = 5;
		$this->Acl->allow($role, 'controllers');
		//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
		//$this->Acl->deny($role, 'controllers/StockMovementTypes');
		//$this->Acl->deny($role, 'controllers/Role');
		
		// Allow assistants to access everything but leave out editing rights in the views and controllers
		$role->id = 6;
		$this->Acl->deny($role, 'controllers');
		//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
		//$this->Acl->deny($role, 'controllers/StockMovementTypes');
		//$this->Acl->deny($role, 'controllers/Role');
		
		// Allow assistants to access everything but leave out editing rights in the views and controllers
		$role->id = 7;
		$this->Acl->deny($role, 'controllers');
		//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
		//$this->Acl->deny($role, 'controllers/StockMovementTypes');
		//$this->Acl->deny($role, 'controllers/Role');
		
		// Allow inventory admins to access everything but leave out editing rights in the views and controllers
		$role->id = 8;
		$this->Acl->deny($role, 'controllers');
		
		// we add an exit to avoid an ugly "missing views" error message
		echo "all done";
		exit;
	
	}

}
