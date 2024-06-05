<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class SalesOrdersController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('getsalesordersforclient','getsalesorderproducts','getsalesorderinfo','getsalesorderinfonofinance');		
	}
	
	public function index() {
    $this->loadModel('Quotation');
    $this->loadModel('ExchangeRate');
    $this->loadModel('ProductionOrder');
    
    $this->loadModel('User');
    $this->loadModel('UserPageRight');
    
		$this->SalesOrder->recursive = -1;
    $this->User->recursive = -1;
    
    $user_id=$loggedUserId=$this->Auth->User('id');
    $this->set(compact('loggedUserId'));
    $userRoleId = $userrole = $this->Auth->User('role_id');
    $this->set(compact('userrole'));
    $this->set(compact('userRoleId'));
    
    $canSeeExecutiveTables=$this->UserPageRight->hasUserPageRight('VER_RESUMEN_EJECUTIVO',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeExecutiveTables'));
    
    $canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllUsers'));
    //echo 'can see all users:'.$canSeeAllUsers.'<br/>';
    
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllSalesExecutives'));
    //echo 'can see all sales executives:'.$canSeeAllSalesExecutives.'<br/>';
    
		$currencyId=CURRENCY_USD;
		$invoiceDisplay=0;
		$authorizedDisplay=0;
		
		if ($this->request->is('post')) {
			if (!empty($this->request->data['authorize_all'])){
				//pr($this->request->data);
				foreach ($this->request->data['Report']['selector'] as $salesOrderId=>$checked){
					if ($checked){
						$salesOrder=$this->SalesOrder->find('first',array(
							'conditions'=>array(
								'SalesOrder.id'=>$salesOrderId,
							),
						));
						if (!$salesOrder['SalesOrder']['bool_authorized']){
							$datasource=$this->SalesOrder->getDataSource();
							$datasource->begin();
							try {
								//pr($this->request->data);
								$this->SalesOrder->id=$salesOrder['SalesOrder']['id'];
								$salesOrderArray['SalesOrder']['id']=$salesOrder['SalesOrder']['id'];
								$salesOrderArray['SalesOrder']['bool_authorized']=true;
								$salesOrderArray['SalesOrder']['authorizing_user_id']=$this->Auth->User('id');
								if (!$this->SalesOrder->save($salesOrderArray)) {
									echo "Problema al autorizar la orden de venta";
									pr($this->validateErrors($this->SalesOrder));
									throw new Exception();
								}
								
								$this->loadModel('SalesOrderProduct');
								$this->SalesOrderProduct->recursive=-1;
								$salesOrderProducts=$this->SalesOrderProduct->find('all',array(
									'fields'=>array('SalesOrderProduct.id','SalesOrderProduct.bool_no_production'),
									'conditions'=>array(
										'SalesOrderProduct.sales_order_id'=>$salesOrder['SalesOrder']['id'],
									)
								));
								if (!empty($salesOrderProducts)){
									foreach ($salesOrderProducts as $salesOrderProduct){
										$this->SalesOrderProduct->id=$salesOrderProduct['SalesOrderProduct']['id'];
										$salesOrderProductArray=array();
										if ($salesOrderProduct['SalesOrderProduct']['bool_no_production']){
											$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_READY_FOR_DELIVERY;
										}
										else {
											$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AUTHORIZED;
										}
										if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
											echo "Problema al cambiar el estado de los productos de la orden de venta a autorizado";
											pr($this->validateErrors($this->SalesOrderProduct));
											throw new Exception();
										}
									}
								}
											
								$datasource->commit();
								$flashMessage="La orden de venta ".$salesOrder['SalesOrder']['sales_order_code']." se ha autorizada.";
								$this->Session->setFlash($flashMessage,'default',array('class' => 'success'));
							}
							catch(Exception $e){
								$this->Session->setFlash(__('La orden de venta no se podía autorizar.'), 'default',array('class' => 'error-message'));
							}
						}	
					}
				}
			}
		
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$user_id=$this->request->data['Report']['user_id'];
			$currencyId=$this->request->data['Report']['currency_id'];
			
			$invoiceDisplay=$this->request->data['Report']['invoice_display'];
			$authorizedDisplay=$this->request->data['Report']['authorized_display'];
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
			if ($this->Session->check('invoiceDisplay')){
				$invoiceDisplay=$_SESSION['invoiceDisplay'];
			}
			if ($this->Session->check('authorizedDisplay')){
				$authorizedDisplay=$_SESSION['authorizedDisplay'];
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
		$_SESSION['invoiceDisplay']=$invoiceDisplay;
		$_SESSION['authorizedDisplay']=$authorizedDisplay;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('user_id','currencyId'));
		$this->set(compact('invoiceDisplay'));
		$this->set(compact('authorizedDisplay'));
		
		$userConditions=[];
		if ($userRoleId != ROLE_ADMIN && !$canSeeAllUsers && !$canSeeAllSalesExecutives  && $user_id > 0) { 
			$userConditions=['User.id'=>$user_id];
		}
		$userPeriod=$this->User->find('list',[
			'conditions'=>$userConditions,
			'order'=>'User.username'
		]);
		
    foreach ($userPeriod as $key=>$value){
        $userPeriod[$key]=[
          'annulled'=>0,
          'pending'=>0,
          'authorized'=>0,
          'total'=>0,
          'pending_before'=>0,
          'pending_after'=>0,
        ];
    }
    //pr($userPeriod);
    
    $userPeriodCS=$userPeriodUSD=$userPeriod;
    
    
    $conditions=[];
		if (($userRoleId != ROLE_ADMIN && !$canSeeAllUsers && !$canSeeAllSalesExecutives) || $user_id != 0) { 
			$this->Quotation->recursive=-1;
			$quotationList=$this->Quotation->find('list',[
				'fields'=>'Quotation.id',
				'conditions'=>[
					'Quotation.user_id'=>$user_id,	
				],
			]);
			$conditions['SalesOrder.quotation_id']=$quotationList;
		}
    
    $contain=[
      'Currency',
      'Quotation'=>[
        'Client',
        'User',
      ],
      'InvoiceSalesOrder'=>[
        'Invoice'=>[
          'fields'=>['Invoice.id','Invoice.invoice_code'],
          'conditions'=>[
            'Invoice.bool_annulled'=>false,
          ],
          'InvoiceProduct'=>[
            'fields'=>['InvoiceProduct.id','InvoiceProduct.product_total_price'],
          ],
        ],
      ],
      'SalesOrderProduct'=>[
        'fields'=>[
          'SalesOrderProduct.id','SalesOrderProduct.product_total_price',
          'SalesOrderProduct.bool_no_production',
          'SalesOrderProduct.sales_order_product_status_id',
        ],
        'Product'=>[
          'fields'=>['Product.product_category_id'],
        ],
        'SalesOrderProductStatus',
      ],
    ];
    
    $authorizationPendingForPeriodConditions=$conditions;
    $authorizationPendingForPeriodConditions['SalesOrder.bool_authorized']=false;
    $authorizationPendingForPeriodConditions['SalesOrder.bool_annulled']=false;
    $authorizationPendingForPeriodConditions['SalesOrder.sales_order_date >=']=$startDate;
		$authorizationPendingForPeriodConditions['SalesOrder.sales_order_date <']=$endDatePlusOne;
    
    //pr($authorizationPendingConditions);
    $salesOrderCount=$this->SalesOrder->find('count', [
			'fields'=>['SalesOrder.id'],
			'conditions' => $authorizationPendingForPeriodConditions,
		]);
    $this->Paginator->settings = [
			'conditions' => $authorizationPendingForPeriodConditions,
			'contain'=>$contain,
			'order'=>'SalesOrder.sales_order_date DESC,SalesOrder.sales_order_code DESC',
			'limit'=>($salesOrderCount!=0?$salesOrderCount:1),
		];
    $salesOrders['authorization_pending_period'] = $this->Paginator->paginate('SalesOrder');
    if (!empty($salesOrders['authorization_pending_period'])){
      $pendingSalesOrders=$salesOrders['authorization_pending_period'];
			for ($i=0;$i<count($salesOrders['authorization_pending_period']);$i++){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($pendingSalesOrders[$i]['SalesOrder']['sales_order_date']);
				$salesOrders['authorization_pending_period'][$i]['SalesOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				// get the status
				$status=0;
				if (!empty($pendingSalesOrders[$i]['SalesOrderProduct'])){
					$totalPriceProducts=0;
					$totalPriceSoldProducts=0;
					foreach ($pendingSalesOrders[$i]['SalesOrderProduct'] as $salesOrderProduct){
						$totalPriceProducts+=$salesOrderProduct['product_total_price'];
						if ($salesOrderProduct['sales_order_product_status_id']==PRODUCT_STATUS_DELIVERED){
							$totalPriceSoldProducts+=$salesOrderProduct['product_total_price'];
						}
					}
					$status=100*$totalPriceSoldProducts/$totalPriceProducts;
					if ($pendingSalesOrders[$i]['Currency']['id']==CURRENCY_CS){
            $userPeriodCS[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending']+=$pendingSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodUSD[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending']+=round($pendingSalesOrders[$i]['SalesOrder']['price_subtotal']/$salesOrders['authorization_pending_period'][$i]['SalesOrder']['exchange_rate'],2);
          }
          elseif ($pendingSalesOrders[$i]['Currency']['id']==CURRENCY_USD){
            $userPeriodUSD[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending']+=$pendingSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodCS[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending']+=round($pendingSalesOrders[$i]['SalesOrder']['price_subtotal']*$salesOrders['authorization_pending_period'][$i]['SalesOrder']['exchange_rate'],2);
          }
				}
				$salesOrders['authorization_pending_period'][$i]['SalesOrder']['status']=$status;
			}
		}
    
    $authorizationPendingBeforePeriodConditions=$conditions;
    $authorizationPendingBeforePeriodConditions['SalesOrder.bool_authorized']=false;
    $authorizationPendingBeforePeriodConditions['SalesOrder.bool_annulled']=false;
    $authorizationPendingBeforePeriodConditions['SalesOrder.sales_order_date <']=$startDate;
		
    //pr($authorizationPendingBeforePeriodConditions);
    $salesOrderCount=$this->SalesOrder->find('count', [
			'fields'=>['SalesOrder.id'],
			'conditions' => $authorizationPendingBeforePeriodConditions,
		]);
    $this->Paginator->settings = [
			'conditions' => $authorizationPendingBeforePeriodConditions,
			'contain'=>$contain,
			'order'=>'SalesOrder.sales_order_date DESC,SalesOrder.sales_order_code DESC',
			'limit'=>($salesOrderCount!=0?$salesOrderCount:1),
		];
    $salesOrders['authorization_pending_before_period'] = $this->Paginator->paginate('SalesOrder');
    if (!empty($salesOrders['authorization_pending_before_period'])){
      $pendingSalesOrders=$salesOrders['authorization_pending_before_period'];
			for ($i=0;$i<count($salesOrders['authorization_pending_before_period']);$i++){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($pendingSalesOrders[$i]['SalesOrder']['sales_order_date']);
				$salesOrders['authorization_pending_before_period'][$i]['SalesOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				// get the status
				$status=0;
				if (!empty($pendingSalesOrders[$i]['SalesOrderProduct'])){
					$totalPriceProducts=0;
					$totalPriceSoldProducts=0;
					foreach ($pendingSalesOrders[$i]['SalesOrderProduct'] as $salesOrderProduct){
						$totalPriceProducts+=$salesOrderProduct['product_total_price'];
						if ($salesOrderProduct['sales_order_product_status_id']==PRODUCT_STATUS_DELIVERED){
							$totalPriceSoldProducts+=$salesOrderProduct['product_total_price'];
						}
					}
					$status=100*$totalPriceSoldProducts/$totalPriceProducts;
					if ($pendingSalesOrders[$i]['Currency']['id']==CURRENCY_CS){
            $userPeriodCS[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending_before']+=$pendingSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodUSD[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending_before']+=round($pendingSalesOrders[$i]['SalesOrder']['price_subtotal']/$salesOrders['authorization_pending_before_period'][$i]['SalesOrder']['exchange_rate'],2);
          }
          elseif ($pendingSalesOrders[$i]['Currency']['id']==CURRENCY_USD){
            $userPeriodUSD[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending_before']+=$pendingSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodCS[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending_before']+=round($pendingSalesOrders[$i]['SalesOrder']['price_subtotal']*$salesOrders['authorization_pending_before_period'][$i]['SalesOrder']['exchange_rate'],2);
          }
				}
				$salesOrders['authorization_pending_before_period'][$i]['SalesOrder']['status']=$status;
			}
		}
		
    $authorizationPendingAfterPeriodConditions=$conditions;
    $authorizationPendingAfterPeriodConditions['SalesOrder.bool_authorized']=false;
    $authorizationPendingAfterPeriodConditions['SalesOrder.bool_annulled']=false;
    $authorizationPendingAfterPeriodConditions['SalesOrder.sales_order_date >=']=$endDatePlusOne;
    
    //pr($authorizationPendingBeforePeriodConditions);
    $salesOrderCount=$this->SalesOrder->find('count', [
			'fields'=>['SalesOrder.id'],
			'conditions' => $authorizationPendingAfterPeriodConditions,
		]);
    $this->Paginator->settings = [
			'conditions' => $authorizationPendingAfterPeriodConditions,
			'contain'=>$contain,
			'order'=>'SalesOrder.sales_order_date DESC,SalesOrder.sales_order_code DESC',
			'limit'=>($salesOrderCount!=0?$salesOrderCount:1),
		];
    $salesOrders['authorization_pending_after_period'] = $this->Paginator->paginate('SalesOrder');
    if (!empty($salesOrders['authorization_pending_after_period'])){
      $pendingSalesOrders=$salesOrders['authorization_pending_after_period'];
			for ($i=0;$i<count($salesOrders['authorization_pending_after_period']);$i++){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($pendingSalesOrders[$i]['SalesOrder']['sales_order_date']);
				$salesOrders['authorization_pending_after_period'][$i]['SalesOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				// get the status
				$status=0;
				if (!empty($pendingSalesOrders[$i]['SalesOrderProduct'])){
					$totalPriceProducts=0;
					$totalPriceSoldProducts=0;
					foreach ($pendingSalesOrders[$i]['SalesOrderProduct'] as $salesOrderProduct){
						$totalPriceProducts+=$salesOrderProduct['product_total_price'];
						if ($salesOrderProduct['sales_order_product_status_id']==PRODUCT_STATUS_DELIVERED){
							$totalPriceSoldProducts+=$salesOrderProduct['product_total_price'];
						}
					}
					$status=100*$totalPriceSoldProducts/$totalPriceProducts;
					if ($pendingSalesOrders[$i]['Currency']['id']==CURRENCY_CS){
            $userPeriodCS[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending_after']+=$pendingSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodUSD[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending_after']+=round($pendingSalesOrders[$i]['SalesOrder']['price_subtotal']/$salesOrders['authorization_pending_after_period'][$i]['SalesOrder']['exchange_rate'],2);
          }
          elseif ($pendingSalesOrders[$i]['Currency']['id']==CURRENCY_USD){
            $userPeriodUSD[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending_after']+=$pendingSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodCS[$pendingSalesOrders[$i]['Quotation']['User']['id']]['pending_after']+=round($pendingSalesOrders[$i]['SalesOrder']['price_subtotal']*$salesOrders['authorization_pending_after_period'][$i]['SalesOrder']['exchange_rate'],2);
          }
				}
				$salesOrders['authorization_pending_after_period'][$i]['SalesOrder']['status']=$status;
			}
		}
    
    $authorizedConditions=$conditions;
    $authorizedConditions['SalesOrder.bool_authorized']=true;
    $authorizationPendingConditions['SalesOrder.bool_annulled']=false;
    $authorizedConditions['SalesOrder.sales_order_date >=']=$startDate;
		$authorizedConditions['SalesOrder.sales_order_date <']=$endDatePlusOne;
    //pr($authorizedConditions);
		$salesOrderCount=$this->SalesOrder->find('count', [
			'fields'=>['SalesOrder.id'],
			'conditions' => $authorizedConditions,
		]);
		$this->Paginator->settings = [
			'conditions' => $authorizedConditions,
			'contain'=>$contain,
			'order'=>'SalesOrder.sales_order_date DESC,SalesOrder.sales_order_code DESC',
			'limit'=>($salesOrderCount!=0?$salesOrderCount:1),
		];
		$salesOrders['authorized'] = $this->Paginator->paginate('SalesOrder');
		if (!empty($salesOrders['authorized'])){
      $authorizedSalesOrders=$salesOrders['authorized'];
			for ($i=0;$i<count($salesOrders['authorized']);$i++){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($authorizedSalesOrders[$i]['SalesOrder']['sales_order_date']);
				$salesOrders['authorized'][$i]['SalesOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				// get the status
				$status=0;
				if (!empty($authorizedSalesOrders[$i]['SalesOrderProduct'])){
					$totalPriceProducts=0;
					$totalPriceSoldProducts=0;
					foreach ($authorizedSalesOrders[$i]['SalesOrderProduct'] as $salesOrderProduct){
						$totalPriceProducts+=$salesOrderProduct['product_total_price'];
						if ($salesOrderProduct['sales_order_product_status_id']==PRODUCT_STATUS_DELIVERED){
							$totalPriceSoldProducts+=$salesOrderProduct['product_total_price'];
						}
					}
					$status=100*$totalPriceSoldProducts/$totalPriceProducts;
					if (!empty($status)){
						//echo "status is ".$status."<br/>";
					}
          if ($authorizedSalesOrders[$i]['Currency']['id']==CURRENCY_CS){
            $userPeriodCS[$authorizedSalesOrders[$i]['Quotation']['User']['id']]['authorized']+=$authorizedSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodUSD[$authorizedSalesOrders[$i]['Quotation']['User']['id']]['authorized']+=round($authorizedSalesOrders[$i]['SalesOrder']['price_subtotal']/$salesOrders['authorized'][$i]['SalesOrder']['exchange_rate'],2);
          }
          elseif ($authorizedSalesOrders[$i]['Currency']['id']==CURRENCY_USD){
            $userPeriodUSD[$authorizedSalesOrders[$i]['Quotation']['User']['id']]['authorized']+=$authorizedSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodCS[$authorizedSalesOrders[$i]['Quotation']['User']['id']]['authorized']+=round($authorizedSalesOrders[$i]['SalesOrder']['price_subtotal']*$salesOrders['authorized'][$i]['SalesOrder']['exchange_rate'],2);
          }
				}
				$salesOrders['authorized'][$i]['SalesOrder']['status']=$status;
			}
		}
		
    $annulledConditions=$conditions;
    $annulledConditions['SalesOrder.bool_annulled']=true;
    $annulledConditions['SalesOrder.sales_order_date >=']=$startDate;
		$annulledConditions['SalesOrder.sales_order_date <']=$endDatePlusOne;
    //pr($authorizedConditions);
		$salesOrderCount=$this->SalesOrder->find('count', [
			'fields'=>['SalesOrder.id'],
			'conditions' => $annulledConditions,
		]);
		$this->Paginator->settings = [
			'conditions' => $annulledConditions,
			'contain'=>$contain,
			'order'=>'SalesOrder.sales_order_date DESC,SalesOrder.sales_order_code DESC',
			'limit'=>($salesOrderCount!=0?$salesOrderCount:1),
		];
		$salesOrders['annulled'] = $this->Paginator->paginate('SalesOrder');
		if (!empty($salesOrders['annulled'])){
      $annulledSalesOrders=$salesOrders['annulled'];
			for ($i=0;$i<count($salesOrders['annulled']);$i++){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($annulledSalesOrders[$i]['SalesOrder']['sales_order_date']);
				$salesOrders['annulled'][$i]['SalesOrder']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				// get the status
				$status=0;
				if (!empty($annulledSalesOrders[$i]['SalesOrderProduct'])){
					$totalPriceProducts=0;
					$totalPriceSoldProducts=0;
					foreach ($annulledSalesOrders[$i]['SalesOrderProduct'] as $salesOrderProduct){
						$totalPriceProducts+=$salesOrderProduct['product_total_price'];
						if ($salesOrderProduct['sales_order_product_status_id']==PRODUCT_STATUS_DELIVERED){
							$totalPriceSoldProducts+=$salesOrderProduct['product_total_price'];
						}
					}
					$status=100*$totalPriceSoldProducts/$totalPriceProducts;
					if (!empty($status)){
						//echo "status is ".$status."<br/>";
					}
          if ($annulledSalesOrders[$i]['Currency']['id']==CURRENCY_CS){
            $userPeriodCS[$annulledSalesOrders[$i]['Quotation']['User']['id']]['annulled']+=$annulledSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodUSD[$annulledSalesOrders[$i]['Quotation']['User']['id']]['annulled']+=round($annulledSalesOrders[$i]['SalesOrder']['price_subtotal']/$salesOrders['annulled'][$i]['SalesOrder']['exchange_rate'],2);
          }
          elseif ($annulledSalesOrders[$i]['Currency']['id']==CURRENCY_USD){
            $userPeriodUSD[$annulledSalesOrders[$i]['Quotation']['User']['id']]['annulled']+=$annulledSalesOrders[$i]['SalesOrder']['price_subtotal'];
            $userPeriodCS[$annulledSalesOrders[$i]['Quotation']['User']['id']]['annulled']+=round($annulledSalesOrders[$i]['SalesOrder']['price_subtotal']*$salesOrders['annulled'][$i]['SalesOrder']['exchange_rate'],2);
          }
				}
				$salesOrders['annulled'][$i]['SalesOrder']['status']=$status;
			}
		}
		
    
    $this->set(compact('salesOrders'));
    $this->set(compact('userPeriodCS','userPeriodUSD'));
		
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
		
		$invoiceOptions=[];
		$invoiceOptions[0]="Mostrar todas ordenes de venta (completamente entregadas o no)";
		$invoiceOptions[1]="Mostrar solamente ordenes de venta completamente entregadas";
		$invoiceOptions[2]="Mostrar solamente ordenes de venta pendientes";
		$this->set(compact('invoiceOptions'));
		
		$authorizedOptions=[];
		$authorizedOptions[0]="Mostrar todas ordenes de venta (autorizadas o no)";
		$authorizedOptions[1]="Mostrar solamente ordenes de venta autorizadas";
		$authorizedOptions[2]="Mostrar solamente ordenes de venta no autorizadas";
		$this->set(compact('authorizedOptions'));
		
		$aco_name="SalesOrders/autorizar";		
		$bool_autorizar_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_autorizar_permission'));
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
		$options = array(
			'conditions' => array(
				'SalesOrder.id' => $id,
			),
			'contain'=>array(
				'AuthorizingUser', 
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
				'InvoiceSalesOrder'=>array(
					'Invoice'=>array(
						'Currency',
						'InvoiceProduct'=>array(
							'Product',
						),
					),
				),
				'ProductionOrder'=>array(
					'ProductionOrderProduct'=>array(
						'PurchaseOrderProduct',
						'ProductionOrderProductDepartment'=>array(
							'Department',
						),
					),
				),
				'ProductionProcessProduct',
			),
		);
		$this->SalesOrder->recursive=-1;
		$salesOrder=$this->SalesOrder->find('first', $options);
		$this->loadModel('SalesOrderProductStatus');
		// REMOVE THE OVERRIDE FOR THE SALES ORDER PRODUCT STATUS
		/*
		for ($sop=0;$sop<count($salesOrder['SalesOrderProduct']);$sop++){
			$status=PRODUCT_STATUS_REGISTERED;
			if (!empty($salesOrder['ProductionOrder'])){
				//$status=PRODUCT_STATUS_AWAITING_PURCHASE;
				//pr($salesOrder['ProductionOrder']);
				foreach ($salesOrder['ProductionOrder'][0]['ProductionOrderProduct'] as $productionOrderProduct){
					if ($productionOrderProduct['product_id']==$salesOrder['SalesOrderProduct'][$sop]['product_id']){
						$status=PRODUCT_STATUS_AWAITING_PURCHASE;
						if (!empty($productionOrderProduct['PurchaseOrderProduct'])){
							if (!$productionOrderProduct['PurchaseOrderProduct']['bool_received']){
								$status=PRODUCT_STATUS_AWAITING_RECEPTION;
							}
							else {
								$status=PRODUCT_STATUS_AWAITING_PRODUCTION;
							}
						}
					}
				}
			}
			if (!empty($salesOrder['ProductionProcessProduct'])){
				foreach ($salesOrder['ProductionProcessProduct'] as $productionProcessProduct){
					if ($productionProcessProduct['product_id']==$salesOrder['SalesOrderProduct'][$sop]['product_id']){
						$status=PRODUCT_STATUS_READY_FOR_DELIVERY;
					}
				}
			}
			if (!empty($salesOrder['Invoice'])){
				foreach ($salesOrder['Invoice'] as $invoice){
					foreach ($invoice['InvoiceProduct'] as $invoiceProduct){
						if ($invoiceProduct['product_id']==$salesOrder['SalesOrderProduct'][$sop]['product_id']){
							$status=PRODUCT_STATUS_DELIVERED;
						}
					}
				}
			}
			$relatedSalesOrderProductStatus=$this->SalesOrderProductStatus->find('first',array(
				'conditions'=>array(
					'SalesOrderProductStatus.id'=>$status,
				),
			));
			$salesOrder['SalesOrderProduct'][$sop]['SalesOrderProductStatus']=$relatedSalesOrderProductStatus['SalesOrderProductStatus'];
		}
		*/
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
		
		$aco_name="Invoices/edit";		
		$bool_invoice_edit_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_edit_permission'));
		$aco_name="ProductionOrders/edit";		
		$bool_productionorder_edit_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_productionorder_edit_permission'));
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
				'AuthorizingUser', 
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
		$this->loadModel('SalesOrderRemark');
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('SalesOrderProductStatus');
		
		if ($this->request->is('post')) {
			$salesOrderDateArray=$this->request->data['SalesOrder']['sales_order_date'];
			//pr($entryDateArray);
			$salesOrderDateString=$salesOrderDateArray['year'].'-'.$salesOrderDateArray['month'].'-'.$salesOrderDateArray['day'];
			$salesOrderDate=date( "Y-m-d", strtotime($salesOrderDateString));
			
			$boolMultiplicationOK=true;
			if (!empty($this->request->data['SalesOrderProduct'])){
				foreach ($this->request->data['SalesOrderProduct'] as $salesOrderProduct){
					if ($salesOrderProduct['product_id']>0){
						$multiplicationDifference=abs($salesOrderProduct['product_total_price']-$salesOrderProduct['product_quantity']*$salesOrderProduct['product_unit_price']);
						if ($multiplicationDifference>=0.01){
							$boolMultiplicationOK=false;
						};
					}
				}
			}
			
			$relatedQuotation=$this->Quotation->find('first',array('conditions'=>array('Quotation.id'=>$this->request->data['SalesOrder']['quotation_id'])));
			$boolTotalOK=true;
			if (empty($this->request->data['SalesOrder']['price_total'])){
				$boolTotalOK=false;
			}
			elseif ($this->request->data['SalesOrder']['price_total']!=$relatedQuotation['Quotation']['price_total']){
				$boolTotalOK=false;
			}
			
			if ($salesOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de venta no puede estar en el futuro!  No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolTotalOK){
				$this->Session->setFlash(__('Occurrió un problema al cargar los productos; los totales de la orden de venta y de la cotización deben estar iguales.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
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
							$productArray=[];
							$productArray['SalesOrderProduct']['sales_order_id']=$sales_order_id;
							$productArray['SalesOrderProduct']['product_id']=$salesOrderProduct['product_id'];
							$productArray['SalesOrderProduct']['product_description']=$salesOrderProduct['product_description'];
							$productArray['SalesOrderProduct']['product_quantity']=$salesOrderProduct['product_quantity'];
							$productArray['SalesOrderProduct']['product_unit_price']=$salesOrderProduct['product_unit_price'];
							$productArray['SalesOrderProduct']['product_total_price']=$salesOrderProduct['product_total_price'];
							$productArray['SalesOrderProduct']['bool_iva']=$salesOrderProduct['bool_iva'];
							$productArray['SalesOrderProduct']['currency_id']=$this->request->data['SalesOrder']['currency_id'];
							$productArray['SalesOrderProduct']['bool_no_production']=$salesOrderProduct['bool_no_production'];
							$productArray['SalesOrderProduct']['sales_order_product_status_id']=$salesOrderProduct['sales_order_product_status_id'];
							
							$this->SalesOrderProduct->create();
							if (!$this->SalesOrderProduct->save($productArray)) {
								echo "Problema guardando los productos de la orden de venta";
								//pr($this->validateErrors($this->SalesOrderProduct));
								throw new Exception();
							}
							
							$sales_order_product_id=$this->SalesOrderProduct->id;
						}
					}
					
					if (!empty($this->request->data['SalesOrderRemark']['remark_text'])){
						$salesOrderRemark=$this->request->data['SalesOrderRemark'];
						//pr($quotationRemark);
						$salesOrderRemarkArray=[];
						$salesOrderRemarkArray['SalesOrderRemark']['user_id']=$salesOrderRemark['user_id'];
						$salesOrderRemarkArray['SalesOrderRemark']['sales_order_id']=$sales_order_id;
						$salesOrderRemarkArray['SalesOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$salesOrderRemarkArray['SalesOrderRemark']['remark_text']=$salesOrderRemark['remark_text'];
						$salesOrderRemarkArray['SalesOrderRemark']['working_days_before_reminder']=$salesOrderRemark['working_days_before_reminder'];
						$salesOrderRemarkArray['SalesOrderRemark']['reminder_date']=$salesOrderRemark['reminder_date'];
						$salesOrderRemarkArray['SalesOrderRemark']['action_type_id']=$salesOrderRemark['action_type_id'];
						$this->SalesOrderRemark->create();
						if (!$this->SalesOrderRemark->save($salesOrderRemarkArray)) {
							echo "Problema guardando las remarcas para la orden de venta";
							pr($this->validateErrors($this->SalesOrderRemark));
							throw new Exception();
						}
					}
					
					$datasource->commit();
					$this->recordUserAction($this->SalesOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de venta número ".$this->request->data['SalesOrder']['sales_order_code']);
					
					$this->Session->setFlash(__('The sales order has been saved.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'view',$sales_order_id));
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
		// MODIFID 20160713 TO ALLOW ADMINISTRATORS TO CREATE SALES ORDERS FOR EXTERIOR QUOTATIONS
		$quotationConditions=array('Quotation.id !='=>$quotationsInSalesOrders);
		$userrole = $this->Auth->User('role_id');
		if ($userrole!=ROLE_ADMIN && $userrole!=ROLE_ASSISTANT) { 
			$quotationConditions[]=array('Quotation.user_id'=>$user_id);
		}
		
		$quotations = $this->SalesOrder->Quotation->find('list',array(
			'conditions'=>$quotationConditions,
			'order'=>'Quotation.quotation_code',
		));
		$this->set(compact('quotations'));
		
		$salesOrderProductStatuses = $this->SalesOrderProductStatus->find('list');
		$currencies = $this->SalesOrder->Currency->find('list');
		$this->set(compact('salesOrderProductStatuses','currencies'));
		
		$this->loadModel('Client');
		$clients = $this->Client->find('list',['order'=>'Client.name']);
		$this->set(compact('clients'));
		
		$salesOrderDate=date( "Y-m-d");
		$salesOrderExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($salesOrderDate);
		$exchangeRateSalesOrder=$salesOrderExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateSalesOrder'));
		
		$this->loadModel('ActionType');
		$actionTypes=$this->ActionType->find('list',array('order'=>'ActionType.list_order ASC'));
		$this->set(compact('actionTypes'));
		
		$aco_name="SalesOrders/autorizar";		
		$bool_autorizar_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_autorizar_permission'));
		
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
		
		$role_id = $this->Auth->User('role_id');
		$this->set(compact('role_id'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
	}

	public function edit($id = null) {
		if (!$this->SalesOrder->exists($id)) {
			throw new NotFoundException(__('Invalid sales order'));
		}
		
		$this->loadModel('Quotation');
		$this->loadModel('SalesOrderRemark');
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('SalesOrderProductStatus');
		$this->loadModel('Product');
		
		$requestProducts=[];
		if ($this->request->is(array('post', 'put'))) {
			$salesOrderDateArray=$this->request->data['SalesOrder']['sales_order_date'];
			//pr($entryDateArray);
			$salesOrderDateString=$salesOrderDateArray['year'].'-'.$salesOrderDateArray['month'].'-'.$salesOrderDateArray['day'];
			$salesOrderDate=date( "Y-m-d", strtotime($salesOrderDateString));
			
			$boolMultiplicationOK=true;
			if (!$this->request->data['SalesOrder']['bool_annulled']){
				foreach ($this->request->data['SalesOrderProduct'] as $salesOrderProduct){
					//pr($salesOrderProduct);
					if ($salesOrderProduct['product_id']>0){
						$requestProducts[]['SalesOrderProduct']=$salesOrderProduct;
						$multiplicationDifference=abs($salesOrderProduct['product_total_price']-$salesOrderProduct['product_quantity']*$salesOrderProduct['product_unit_price']);
						if ($multiplicationDifference>=0.01){
							$boolMultiplicationOK=false;
						};
					}
				}
			}
			//pr($requestProducts);
			
			$relatedQuotation=$this->Quotation->find('first',array(
				'conditions'=>array(
					'Quotation.id'=>$this->request->data['SalesOrder']['quotation_id'],
				),
			));
			$boolTotalOK=true;
			if (empty($this->request->data['SalesOrder']['price_total'])){
				$boolTotalOK=false;
			}
			elseif ($this->request->data['SalesOrder']['price_total']!=$relatedQuotation['Quotation']['price_total']){
				$boolTotalOK=false;
			}
			
			if ($salesOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de venta no puede estar en el futuro!  No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolMultiplicationOK){
				$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolTotalOK){
				$this->Session->setFlash(__('Occurrió un problema al cargar los productos; los totales de la orden de venta ('.$this->request->data['SalesOrder']['price_total'].') y de la cotización ( '.$relatedQuotation['Quotation']['price_total'].') deben estar iguales.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
			}
			elseif ($this->request->data['SalesOrder']['bool_annulled']&&empty($this->request->data['SalesOrderRemark']['remark_text'])){
				$this->Session->setFlash(__('Se debe grabar una remarca al anular una orden de venta.  No se guardó la orden de venta.'), 'default',array('class' => 'error-message'));
			}
			else {
				$datasource=$this->SalesOrder->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$this->SalesOrderProduct->recursive=-1;
					$previousSalesOrderProducts=$this->SalesOrderProduct->find('all',array(
						'fields'=>array('SalesOrderProduct.id'),
						'conditions'=>array(
							'SalesOrderProduct.sales_order_id'=>$id,
						),
						'contain'=>array(
						)
					));
					if (!empty($previousSalesOrderProducts)){
						foreach ($previousSalesOrderProducts as $previousSalesOrderProduct){
							//pr($previousSalesOrderProduct);
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
								$productArray=[];
								$productArray['SalesOrderProduct']['sales_order_id']=$sales_order_id;
								$productArray['SalesOrderProduct']['product_id']=$salesOrderProduct['product_id'];
								$productArray['SalesOrderProduct']['product_description']=$salesOrderProduct['product_description'];
								$productArray['SalesOrderProduct']['product_quantity']=$salesOrderProduct['product_quantity'];
								$productArray['SalesOrderProduct']['sales_order_product_status_id']=$salesOrderProduct['sales_order_product_status_id'];
								$productArray['SalesOrderProduct']['product_unit_price']=$salesOrderProduct['product_unit_price'];
								$productArray['SalesOrderProduct']['product_total_price']=$salesOrderProduct['product_total_price'];
								$productArray['SalesOrderProduct']['bool_iva']=$salesOrderProduct['bool_iva'];
								$productArray['SalesOrderProduct']['currency_id']=$this->request->data['SalesOrder']['currency_id'];
								
								$this->SalesOrderProduct->create();
								if (!$this->SalesOrderProduct->save($productArray)) {
									echo "Problema guardando los productos de la orden de venta";
									//pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
								
								$sales_order_product_id=$this->SalesOrderProduct->id;
							}
						}
						
						if (!empty($this->request->data['SalesOrderRemark']['remark_text'])){
							$salesOrderRemark=$this->request->data['SalesOrderRemark'];
							//pr($quotationRemark);
							$salesOrderRemarkArray=[];
							$salesOrderRemarkArray['SalesOrderRemark']['user_id']=$salesOrderRemark['user_id'];
							$salesOrderRemarkArray['SalesOrderRemark']['sales_order_id']=$sales_order_id;
							$salesOrderRemarkArray['SalesOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
							$salesOrderRemarkArray['SalesOrderRemark']['remark_text']=$salesOrderRemark['remark_text'];
							$salesOrderRemarkArray['SalesOrderRemark']['working_days_before_reminder']=$salesOrderRemark['working_days_before_reminder'];
							$salesOrderRemarkArray['SalesOrderRemark']['reminder_date']=$salesOrderRemark['reminder_date'];
							$salesOrderRemarkArray['SalesOrderRemark']['action_type_id']=$salesOrderRemark['action_type_id'];
							$this->SalesOrderRemark->create();
							if (!$this->SalesOrderRemark->save($salesOrderRemarkArray)) {
								echo "Problema guardando las remarcas para la orden de venta";
								pr($this->validateErrors($this->SalesOrderRemark));
								throw new Exception();
							}
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->SalesOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se registró la orden de venta número ".$this->request->data['SalesOrder']['sales_order_code']);
					
					$this->Session->setFlash(__('The sales order has been saved.'),'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'view',$sales_order_id));
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
					'SalesOrderProduct'=>array(
						'Product'=>array(
						),
					),
					'ProductionOrder',
				),
			);
			$this->request->data = $this->SalesOrder->find('first', $options);
			for ($sop=0;$sop<count($this->request->data['SalesOrderProduct']);$sop++){
				$this->request->data['SalesOrderProduct'][$sop]['bool_no_iva']=$this->request->data['SalesOrderProduct'][$sop]['Product']['bool_no_iva'];
				$requestProducts[]['SalesOrderProduct']=$this->request->data['SalesOrderProduct'][$sop];
				
			}
		}
		
		$this->set(compact('requestProducts'));
		
		$this->loadModel('SalesOrderRemark');
		$this->SalesOrderRemark->recursive=-1;
		$salesOrderRemarks=$this->SalesOrderRemark->find('all',array(
			'conditions'=>array(
				'SalesOrderRemark.sales_order_id'=>$id,
			),
			'contain'=>array(
				'User',
			)
		));
		$this->set(compact('salesOrderRemarks'));
		
		$relatedQuotation=$this->Quotation->read(null,$this->request->data['SalesOrder']['quotation_id']);
		$this->set(compact('relatedQuotation'));	
		
		$user_id=$this->Auth->User('id');
		$this->set(compact('user_id'));
		$role_id = $this->Auth->User('role_id');
		$this->set(compact('role_id'));
		
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
		
		$products=$this->Product->find('list',array('order'=>'Product.name'));
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
		
		$this->loadModel('ActionType');
		$actionTypes=$this->ActionType->find('list',array('order'=>'ActionType.list_order ASC'));
		$this->set(compact('actionTypes'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$aco_name="SalesOrders/autorizar";		
		$bool_autorizar_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_autorizar_permission'));
		
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
				$datasource->begin();
				try {
					//pr($this->request->data);
					
					
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
								$productArray=[];
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
					return $this->redirect(array('action' => 'view',$this->SalesOrder->id));
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

	public function autorizar($id = null) {
		if (!$this->SalesOrder->exists($id)) {
			throw new NotFoundException(__('Invalid sales order'));
		}
		//$this->request->allowMethod('post', 'put');
		
		$this->SalesOrder->recursive=-1;
		$salesOrder=$this->SalesOrder->find('first',array(
			'conditions'=>array(
				'SalesOrder.id'=>$id,
			),
		));
		
		if (!$salesOrder['SalesOrder']['bool_authorized']){
			$datasource=$this->SalesOrder->getDataSource();
			$datasource->begin();
			try {
				//pr($this->request->data);
				$this->SalesOrder->id=$id;
				$salesOrderArray['SalesOrder']['id']=$id;
				$salesOrderArray['SalesOrder']['bool_authorized']=true;
				$salesOrderArray['SalesOrder']['authorizing_user_id']=$this->Auth->User('id');
				if (!$this->SalesOrder->save($salesOrderArray)) {
					echo "Problema al autorizar la orden de venta";
					pr($this->validateErrors($this->SalesOrder));
					throw new Exception();
				}
				
				$this->loadModel('SalesOrderProduct');
				$this->SalesOrderProduct->recursive=-1;
				$salesOrderProducts=$this->SalesOrderProduct->find('all',array(
					'fields'=>array('SalesOrderProduct.id','SalesOrderProduct.bool_no_production'),
					'conditions'=>array(
						'SalesOrderProduct.sales_order_id'=>$id,
					)
				));
				if (!empty($salesOrderProducts)){
					foreach ($salesOrderProducts as $salesOrderProduct){
						$this->SalesOrderProduct->id=$salesOrderProduct['SalesOrderProduct']['id'];
						$salesOrderProductArray=[];
						if ($salesOrderProduct['SalesOrderProduct']['bool_no_production']){
							$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_READY_FOR_DELIVERY;
						}
						else {
							$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AUTHORIZED;
						}
						if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
							echo "Problema al cambiar el estado de los productos de la orden de venta a autorizado";
							pr($this->validateErrors($this->SalesOrderProduct));
							throw new Exception();
						}
					}
				}
							
				$datasource->commit();
				$flashMessage="La orden de venta ".$salesOrder['SalesOrder']['sales_order_code']." se ha autorizada.";
				$this->Session->setFlash($flashMessage,'default',array('class' => 'success'));
			}
			catch(Exception $e){
				$this->Session->setFlash(__('La orden de venta no se podía autorizar.'), 'default',array('class' => 'error-message'));
			}
		}
		return $this->redirect(array('action' => 'view',$id));
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
				'InvoiceSalesOrder'=>array(
					'Invoice'=>array(
						'conditions'=>array(
							'Invoice.bool_annulled'=>false,
						),
					),
				),
				'SalesOrderProduct',
			)
		));

		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (!empty($salesOrder['InvoiceSalesOrder'])){
			$boolDeletionAllowed=false;
			$flashMessage.="Esta orden de venta tiene facturas correspondientes.  Para poder eliminar la orden de venta, primero hay que eliminar o modificar las facturas ";
			
			if (count($salesOrder['InvoiceSalesOrder'])==1){
				$flashMessage.=$salesOrder['InvoiceSalesOrder'][0]['Invoice']['invoice_code'].".";
			}
			else {
				for ($i=0;$i<count($salesOrder['InvoiceSalesOrder']);$i++){
					$flashMessage.=$salesOrder['InvoiceSalesOrder'][$i]['Invoice']['invoice_code'];
					if ($i==count($salesOrder['InvoiceSalesOrder'])-1){
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
			$datasource=$this->SalesOrder->getDataSource();
			$datasource->begin();	
			try {
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
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=[];
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$salesOrder['SalesOrder']['id'];
				$deletionArray['Deletion']['reference']=$salesOrder['SalesOrder']['sales_order_code'];
				$deletionArray['Deletion']['type']='SalesOrder';
				$this->Deletion->save($deletionArray);
						
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
		$datasource->begin();
		try {
			//pr($this->request->data);
			
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
			$salesOrderArray=[];
			$salesOrderArray['SalesOrder']['id']=$id;
			$salesOrderArray['SalesOrder']['bool_annulled']=true;
			$salesOrderArray['SalesOrder']['price_subtotal']=0;
			$salesOrderArray['SalesOrder']['price_iva']=0;
			$salesOrderArray['SalesOrder']['price_total']=0;
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
		
		$clientId=trim($_POST['clientid']);
		$boolSkipProductChecks=trim($_POST['boolskipproductchecks']);
		$invoiceId=trim($_POST['invoiceid']);
		//$userid=trim($_POST['userid']);
		
		//pr($clientId);
		$this->loadModel('Client');
		$this->loadModel('Quotation');
		$quotationConditions=[];
		if ($clientId>0){
			$quotationConditions=[
				'Quotation.client_id'=>$clientId,
			];
		}
		//if ($userid>0){
		//	$quotationConditions[]=array('Quotation.user_id'=>$userid);
		//}
		$quotationsForClient=$this->Quotation->find('list',[
			'fields'=>'Quotation.id',
			'conditions'=>$quotationConditions,
		]);
		//pr($quotationsForClient);
		
		$this->SalesOrder->recursive=-1;
		
		// 20160807 WITH ADDED CHECKS ON WHETHER TO CHECK ON PRODUCT STATUS OR NOT
		if ($boolSkipProductChecks){
			// in case bool_skip_production_checks is true=>just make a direct check on bool_completely_delivered and that's it
			$salesOrders=$this->SalesOrder->find('all',[
				'fields'=>['SalesOrder.id','SalesOrder.sales_order_code',],
				'conditions'=>[				
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.quotation_id'=>$quotationsForClient,
          'SalesOrder.bool_authorized'=>true,
				],
				'order'=>'LENGTH(SalesOrder.sales_order_code) ASC, SalesOrder.sales_order_code ASC',
			]);
			//pr($salesOrders);
		}
		else {
			$salesOrderIdsBasedOnProducts=$this->SalesOrder->SalesOrderProduct->find('list',array(
				'fields'=>array('SalesOrderProduct.sales_order_id'),
				'conditions'=>array(
					'SalesOrderProduct.sales_order_product_status_id'=>array(PRODUCT_STATUS_READY_FOR_DELIVERY),
				),
			));
			$salesOrders=$this->SalesOrder->find('all',array(
				'fields'=>array('SalesOrder.id','SalesOrder.sales_order_code',),
				'conditions'=>array(				
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.id'=>$salesOrderIdsBasedOnProducts,
					'SalesOrder.quotation_id'=>$quotationsForClient,
				),
				'order'=>'LENGTH(SalesOrder.sales_order_code) ASC, SalesOrder.sales_order_code ASC',
			));
		}
		//COMMENTED OUT 20160807 TO ACCOMODATE FOR SKIP PRODUCT CHECK AND ALSO THE MULTIPLE
		/*
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
		*/
		//pr($quotationsForClient);
		$salesOrdersForClient=$salesOrders;
		$this->set(compact('salesOrdersForClient'));
	}

	public function getsalesorderproducts() {
		$this->layout = "ajax";
		
		$selectedSalesOrders=$_POST['selectedSalesOrders'];
		
		$currencyId=trim($_POST['currencyid']);
		$exchangeRate=trim($_POST['exchangerate']);
		$invoiceId=trim($_POST['invoiceid']);
		$boolIVA=trim($_POST['boolIVA']);
		$this->set(compact('currencyId','exchangeRate','boolIVA'));
		
		$boolSkipProductChecks=trim($_POST['boolskipproductchecks']);
		
		if (empty($selectedSalesOrders)){
			throw new NotFoundException(__('Orden de Venta no presente'));
		}
		
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('Product');
		$salesOrderIds=$this->SalesOrder->find('list',array(
			'fields'=>array('SalesOrder.id'),
			'conditions'=>array(
				'SalesOrder.id'=>$selectedSalesOrders,
			),
		));
		
		// pr($salesOrders);
		$productConditions=array(
			'SalesOrderProduct.sales_order_id'=>$salesOrderIds,
		);
		if (!$boolSkipProductChecks){
			if ($invoiceId>0){
				$productConditions[]=array('SalesOrderProduct.sales_order_product_status_id >= '=>PRODUCT_STATUS_READY_FOR_DELIVERY);
			}
			else {
				$productConditions[]=array('SalesOrderProduct.sales_order_product_status_id'=>PRODUCT_STATUS_READY_FOR_DELIVERY);
			}
		}
		//pr($productConditions);
		$productsForSalesOrder=$this->SalesOrderProduct->find('all',array(
			'fields'=>array(
				'SalesOrderProduct.id',
				'SalesOrderProduct.product_id','SalesOrderProduct.product_description',
				'SalesOrderProduct.product_quantity',
				'SalesOrderProduct.product_unit_price','SalesOrderProduct.product_total_price',
				'SalesOrderProduct.currency_id','SalesOrderProduct.bool_iva',
				'SalesOrderProduct.sales_order_product_status_id','SalesOrderProduct.bool_no_production',
			),
			'contain'=>array(
				'Product'=>array('fields'=>array('Product.bool_no_iva')),
			),
			'conditions'=>$productConditions,
			'order'=>'SalesOrderProduct.sales_order_id',
		));
		//pr($productsForSalesOrder);
		
		$this->set(compact('productsForSalesOrder'));
		
		$products=$this->Product->find('list',array('order'=>'Product.name'));
		$this->set(compact('products'));
	}
	
	public function getsalesorderinfo() {
		$this->layout = "ajax";
		
		$selectedSalesOrders=$_POST['selectedSalesOrders'];
		if (empty($selectedSalesOrders)){
			throw new NotFoundException(__('Orden de Venta no presente'));
		}
		
		$salesOrders=$this->SalesOrder->find('all',array(
			'fields'=>array(
				'SalesOrder.sales_order_code','SalesOrder.sales_order_date','SalesOrder.quotation_id',
			),
			'conditions'=>array(
				'SalesOrder.id'=>$selectedSalesOrders,
			),
			'contain'=>array(
				'Quotation'=>array(
					'fields'=>array('Quotation.id','Quotation.quotation_code'),
					'Contact'=>array(
						'fields'=>array('Contact.id','Contact.first_name','Contact.last_name','Contact.phone','Contact.email'),
					),
					'Client'=>array(
						'fields'=>array('Client.id','Client.name'),
					),
					'User'=>array(
						'fields'=>array('User.id','User.username'),
					),
				),
			),
		));
		//pr($salesOrder);
		$this->set(compact('salesOrders'));
	}
	
	public function getsalesorderinfonofinance() {
		$this->layout = "ajax";
		
		$sales_order_id=trim($_POST['sales_order_id']);
		if (!$sales_order_id){
			throw new NotFoundException(__('Orden de Venta no presente'));
		}
		
		// $this->InvoiceProduct->virtualFields['total_product_quantity']=0;
		$salesOrder=$this->SalesOrder->find('first',array(
			'fields'=>array(
				'SalesOrder.sales_order_code','SalesOrder.sales_order_date','SalesOrder.quotation_id',
			),
			'conditions'=>array(
				'SalesOrder.id'=>$sales_order_id,
			),
			'contain'=>array(
				'Quotation'=>array(
					'fields'=>array('Quotation.id','Quotation.quotation_code'),
					'Contact'=>array(
						'fields'=>array('Contact.id','Contact.first_name','Contact.last_name','Contact.phone','Contact.email'),
					),
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
		$salesOrderProductStatusConditions=[];
		$this->SalesOrderProductStatus->recursive=-1;
		
		if ($sales_order_product_status_id>0){
			$salesOrderProductStatusConditions['SalesOrderProductStatus.id']=$sales_order_product_status_id;
		}
		
		$selectedStatuses=[];
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
				'AuthorizingUser', 
				'Currency',
				'Quotation'=>array(
					'Client',
					'Contact',
					'User',
				),
				'SalesOrderProduct'=>array(
					'fields'=>array(
						'SalesOrderProduct.id','SalesOrderProduct.sales_order_product_status_id',
					),
					'order'=>'SalesOrderProduct.sales_order_product_status_id DESC',
					'Product',
					'SalesOrderProductStatus',
					'Currency',
				),
				/*
				'InvoiceSalesOrder'=>array(
					'Invoice'=>array(
						'InvoiceProduct',
					),
				),
				*/
				'ProductionOrder'=>array(
					'ProductionOrderProduct'=>array(
						'PurchaseOrderProduct',
					),
				),
				'ProductionProcessProduct',
			),
		));
		if (!empty($salesOrders)){
			for ($so=0;$so<count($salesOrders);$so++){	
				$orderStatus=PRODUCT_STATUS_DELIVERED;
				for ($sop=0;$sop<count($salesOrders[$so]['SalesOrderProduct']);$sop++){
					$status=$salesOrders[$so]['SalesOrderProduct'][$sop]['sales_order_product_status_id'];
					/*
					if (!empty($salesOrder['ProductionOrder'])){
						//pr($salesOrder['ProductionOrder']);
						foreach ($salesOrders[$so]['ProductionOrder'][0]['ProductionOrderProduct'] as $productionOrderProduct){
							if ($productionOrderProduct['product_id']==$salesOrders[$so]['SalesOrderProduct'][$sop]['product_id']){
								$status=PRODUCT_STATUS_AWAITING_PURCHASE;
								if (!empty($productionOrderProduct['PurchaseOrderProduct'])){
									if (!$productionOrderProduct['PurchaseOrderProduct']['bool_received']){
										$status=PRODUCT_STATUS_AWAITING_RECEPTION;
									}
									else {
										$status=PRODUCT_STATUS_AWAITING_PRODUCTION;
									}
								}
							}
						}
					}
					if (!empty($salesOrders[$so]['ProductionProcessProduct'])){
						foreach ($salesOrders[$so]['ProductionProcessProduct'] as $productionProcessProduct){
							if ($productionProcessProduct['product_id']==$salesOrders[$so]['SalesOrderProduct'][$sop]['product_id']){
								$status=PRODUCT_STATUS_READY_FOR_DELIVERY;
							}
						}
					}
					if (!empty($salesOrders[$so]['Invoice'])){
						foreach ($salesOrders[$so]['Invoice'] as $invoice){
							foreach ($invoice['InvoiceProduct'] as $invoiceProduct){
								if ($invoiceProduct['product_id']==$salesOrders[$so]['SalesOrderProduct'][$sop]['product_id']){
									$status=PRODUCT_STATUS_DELIVERED;
								}
							}
						}
					}
					*/
					if ($status<$orderStatus){
						$orderStatus=$status;
					}
				}
				$salesOrders[$so]['SalesOrder']['order_status']=$orderStatus;
			}
		}
		//pr($salesOrders);
		for ($s=0;$s<count($selectedStatuses);$s++){
			$salesOrdersOfStatus=[];
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
		
		$annulledSalesOrders=[];
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
