<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class InvoicesController extends AppController {
	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getNewInvoiceCode');		
	}
  
  private function getFontSizeBasedOnInvoiceProducts($invoiceProducts){
    
    // notice that the actual number of characters per line is variable
    // the minimumCharacters provide a safe estimate for the number of lines
    /*
    $minimumCharactersPerPtFontSize=[
      "7.5"=>66,
      "9"=>58,
      "10"=>52,
      "11"=>46,
      12"=>46,
    ];
    */
    $averageCharactersPerPtFontSize=[
      "7.5"=>66,
      "9"=>63,
      "10"=>55,
      "11"=>52,
      "12"=>50,
    ];
    /*
    $maximumCharactersPerPtFontSize=[
      "7.5"=>66,
      "9"=>69,
      "10"=>61,
      "11"=>62,
      "12"=>53,
    ];
    */
    
    $maxTotalLinesPerPtFontSize=[
      "7.5"=>23,
      "9"=>21,
      "10"=>20,
      "11"=>19,
      "12"=>18,
    ];
    
    $totalLinesPerPtFontSize=[
      "7.5"=>0,
      "9"=>0,
      "10"=>0,
      "11"=>0,
      "12"=>0,
    ];
    // essentially pt font size * lineheight factor 1.42857
    $lineHeightsPerPtFontSize=[
      "7.5"=>12,
      "9"=>15,
      "10"=>16,
      "11"=>17,
      "12"=>18,
    ];
    $totalHeightPerPtFontSize=[
      "7.5"=>0,
      "9"=>0,
      "10"=>0,
      "11"=>0,
      "12"=>0,
    ];
    
    foreach ($invoiceProducts as $invoiceProduct){
      $productDescription=$invoiceProduct['product_description'];
      //pr($productDescription);
      $productDescriptionSplitByLineBreaks=explode(PHP_EOL,$productDescription);
      //pr($productDescriptionSplitByLineBreaks);
      $totalLinesForProduct=[
        "7.5"=>0,
        "9"=>0,
        "10"=>0,
        "11"=>0,
        "12"=>0,
      ];
      foreach ($productDescriptionSplitByLineBreaks as $productDescriptionSegment){
        foreach (array_keys($totalLinesPerPtFontSize) as $ptFontSize){
          $totalLinesPerPtFontSize[$ptFontSize]+=ceil(strlen($productDescriptionSegment)/$averageCharactersPerPtFontSize[$ptFontSize]);
          $totalLinesForProduct[$ptFontSize]+=ceil(strlen($productDescriptionSegment)/$averageCharactersPerPtFontSize[$ptFontSize]);
        }
      }
      foreach (array_keys($totalLinesPerPtFontSize) as $ptFontSize){
        $totalHeightPerPtFontSize[$ptFontSize]+=5+$lineHeightsPerPtFontSize[$ptFontSize]*$totalLinesForProduct[$ptFontSize];
      }  
    }
    //pr($totalLinesPerPtFontSize);
    //pr($totalHeightPerPtFontSize[$ptFontSize]);
    foreach (array_reverse(array_keys($totalLinesPerPtFontSize)) as $ptFontSize){
      //echo 'checked font size is '.$ptFontSize.'<br/>';
      /*
      if ($totalLinesPerPtFontSize[$ptFontSize] <= $maxTotalLinesPerPtFontSize[$ptFontSize]){
        echo 'total lines '.$totalLinesPerPtFontSize[$ptFontSize].' is less than or equal to '.$maxTotalLinesPerPtFontSize[$ptFontSize].' and returned font size is '.$ptFontSize.'<br/>';
        return $ptFontSize;
      }
      else {
        echo 'total lines '.$totalLinesPerPtFontSize[$ptFontSize].' is greater than '.$maxTotalLinesPerPtFontSize[$ptFontSize].' and returned font size is '.$ptFontSize.'<br/>';
      }
      */
      //echo 'total height '.$totalHeightPerPtFontSize[$ptFontSize].'<br/>';
      if ($totalHeightPerPtFontSize[$ptFontSize] <= 418){
        return $ptFontSize;
      }
    }
    return "7.5";
  }

  public function getNewInvoiceCode(){
		$this->layout= "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
	//	$userId=trim($_POST['userid']);
 	//	$invoiceDateDay=trim($_POST['invoicedateday']);
	//	$invoiceDateMonth=trim($_POST['invoicedatemonth']);
	//	$invoiceDateYear=trim($_POST['invoicedateyear']);
    
	//	if (!$userId){
	//		throw new NotFoundException(__('Usuario no presente'));
	//	}
  //  $this->loadModel('User');
	//	$userAbbreviation=$this->User->getUserAbbreviationById($userId);
		
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
		
		return $this->Invoice->getInvoiceCode();
	}
/*	
	public function index() {
		$this->loadModel('InvoiceSalesOrder');
    
    $this->loadModel('Currency');
    $this->loadModel('ExchangeRate');
		
    $this->Invoice->recursive = -1;
    
    $this->loadModel('User');
    $this->loadModel('UserPageRight');
		
		$loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    
		$canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSeeAllUsers'));
    
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSeeAllSalesExecutives'));
		
		if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives){
			$userId=0;
		}
    else {
      $userId=$loggedUserId;
    }
		$currencyId=CURRENCY_USD;
		
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
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
			//if ($this->Session->check('userId')){
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
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate','userId','currencyId'));
		
		$conditions=[
			'Invoice.invoice_date >='=>$startDate,
			'Invoice.invoice_date <'=>$endDatePlusOne,
		];
		
		//echo "user_id is ".$user_id."<br/>";
		if ($userId > 0) { 
			$invoiceIdsForUser=$this->InvoiceSalesOrder->find('list',[
				'fields'=>['InvoiceSalesOrder.invoice_id'],
				'conditions'=>[
					'InvoiceSalesOrder.user_id'=>$userId,
				],
			]);
			$conditions['Invoice.id']=$invoiceIdsForUser;
		}
    elseif ($userRoleId != ROLE_ADMIN && !$canSeeAllUsers && $canSeeAllSalesExecutives){
      $vendorUserIds=array_keys($users=$this->User->getActiveSalesUsersOnlyList());
      $invoiceIdsForUser=$this->InvoiceSalesOrder->find('list',[
				'fields'=>['InvoiceSalesOrder.invoice_id'],
				'conditions'=>[
					'InvoiceSalesOrder.user_id'=>$vendorUserIds,
				],
			]);
      $conditions['Invoice.id']=$invoiceIdsForUser;
    }
		
		$invoiceCount=	$this->Invoice->find('count', [
			'fields'=>['Invoice.id'],
			'conditions' => $conditions,
		]);
		$this->Paginator->settings = [
			'conditions' => $conditions,
			'contain'=>[				
				'Client',
				'Currency',
				'InvoiceSalesOrder'=>[
					'SalesOrder'=>[
						'Quotation'
					],
					'User',
				],
			],
			'order'=>['Invoice.invoice_date'=>'DESC','Invoice.invoice_code'=>'DESC'],
			'limit'=>($invoiceCount!=0?$invoiceCount:1),
		];

		$invoices = $this->Paginator->paginate('Invoice');
		if (!empty($invoices)){
			for ($i=0;$i<count($invoices);$i++){
				$invoiceDate=$invoices[$i]['Invoice']['invoice_date'];
				$invoices[$i]['Invoice']['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
			}
		}
		$this->set(compact('invoices'));
		
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
		
    
		$invoiceIdsForPeriod=$this->Invoice->find('list',[
			'fields'=>['Invoice.id'],
			'conditions'=>$conditions,
		]);
		$invoicesPerUser=$this->User->find('all',[
			'contain'=>[
				'InvoiceSalesOrder'=>[
					'conditions'=>[
						'InvoiceSalesOrder.invoice_id'=>$invoiceIdsForPeriod,
					],
					'Invoice',
				],
			],
		]);
    $this->set(compact('invoicesPerUser'));
    //pr($invoicesPerUser);
    
		$invoiceTotalsPerUser=[];
		foreach ($invoicesPerUser as $userInvoice){
			$userArray=[];
			$userArray['User']=$userInvoice['User'];
			$invoiceTotal=0;
      $processedInvoices=[];
			foreach ($userInvoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
        if (!in_array($invoiceSalesOrder['Invoice']['id'],$processedInvoices)){
          $processedInvoices[]=$invoiceSalesOrder['Invoice']['id'];
          $invoiceTotal+=$invoiceSalesOrder['Invoice']['price_subtotal'];  
        }
			}
			$userArray['invoice_total']=$invoiceTotal;
			$invoiceTotalsPerUser[]=$userArray;
		}
		$this->set(compact('invoiceTotalsPerUser'));
		
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
*/
	public function resumen() {
		$this->loadModel('InvoiceSalesOrder');
    
    $this->loadModel('Currency');
    $this->loadModel('ExchangeRate');
		
    $this->Invoice->recursive = -1;
    
    $this->loadModel('User');
    $this->loadModel('UserPageRight');
		
		$loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    
		$canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSeeAllUsers'));
    
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSeeAllSalesExecutives'));
		
		if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives){
			$userId=0;
		}
    else {
      $userId=$loggedUserId;
    }
		$currencyId=CURRENCY_USD;
		
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
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
			//if ($this->Session->check('userId')){
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
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate','userId','currencyId'));
		
		$conditions=[
			'Invoice.invoice_date >='=>$startDate,
			'Invoice.invoice_date <'=>$endDatePlusOne,
		];
		
		//echo "user_id is ".$user_id."<br/>";
		if ($userId > 0) { 
			$invoiceIdsForUser=$this->InvoiceSalesOrder->find('list',[
				'fields'=>['InvoiceSalesOrder.invoice_id'],
				'conditions'=>[
					'InvoiceSalesOrder.user_id'=>$userId,
				],
			]);
			$conditions['Invoice.id']=$invoiceIdsForUser;
		}
    elseif ($userRoleId != ROLE_ADMIN && !$canSeeAllUsers && $canSeeAllSalesExecutives){
      $vendorUserIds=array_keys($users=$this->User->getActiveSalesUsersOnlyList());
      $invoiceIdsForUser=$this->InvoiceSalesOrder->find('list',[
				'fields'=>['InvoiceSalesOrder.invoice_id'],
				'conditions'=>[
					'InvoiceSalesOrder.user_id'=>$vendorUserIds,
				],
			]);
      $conditions['Invoice.id']=$invoiceIdsForUser;
    }
		
		$invoiceCount=	$this->Invoice->find('count', [
			'fields'=>['Invoice.id'],
			'conditions' => $conditions,
		]);
		$this->Paginator->settings = [
			'conditions' => $conditions,
			'contain'=>[				
				'Client',
				'Currency',
				'InvoiceSalesOrder'=>[
					'SalesOrder'=>[
						'Quotation'
					],
					'User',
				],
			],
			'order'=>['Invoice.invoice_date'=>'DESC','Invoice.invoice_code'=>'DESC'],
			'limit'=>($invoiceCount!=0?$invoiceCount:1),
		];

		$invoices = $this->Paginator->paginate('Invoice');
		if (!empty($invoices)){
			for ($i=0;$i<count($invoices);$i++){
				$invoiceDate=$invoices[$i]['Invoice']['invoice_date'];
				$invoices[$i]['Invoice']['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
			}
		}
		$this->set(compact('invoices'));
		
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
		
    
		$invoiceIdsForPeriod=$this->Invoice->find('list',[
			'fields'=>['Invoice.id'],
			'conditions'=>$conditions,
		]);
		$invoicesPerUser=$this->User->find('all',[
			'contain'=>[
				'InvoiceSalesOrder'=>[
					'conditions'=>[
						'InvoiceSalesOrder.invoice_id'=>$invoiceIdsForPeriod,
					],
					'Invoice',
				],
			],
		]);
    $this->set(compact('invoicesPerUser'));
    //pr($invoicesPerUser);
    
		$invoiceTotalsPerUser=[];
		foreach ($invoicesPerUser as $userInvoice){
			$userArray=[];
			$userArray['User']=$userInvoice['User'];
			$invoiceTotal=0;
      $processedInvoices=[];
			foreach ($userInvoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
        if (!in_array($invoiceSalesOrder['Invoice']['id'],$processedInvoices)){
          $processedInvoices[]=$invoiceSalesOrder['Invoice']['id'];
          if ($invoiceSalesOrder['Invoice']['currency_id'] == $currencyId){
            $invoiceTotal+=round($invoiceSalesOrder['Invoice']['price_subtotal'],2);  
          }
          else {
            if ($currencyId == CURRENCY_USD){
              $invoiceExchangeRate=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceSalesOrder['Invoice']['invoice_date']);
              //invoice is in C$
              $invoiceTotal+=round($invoiceSalesOrder['Invoice']['price_subtotal']/$invoiceExchangeRate,2); 
            }
            else {
              $invoiceExchangeRate=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceSalesOrder['Invoice']['invoice_date']);
              //invoice is in US$
              $invoiceTotal+=round($invoiceSalesOrder['Invoice']['price_subtotal']*$invoiceExchangeRate,2); 
            }
          }
        }
			}
			$userArray['invoice_total']=$invoiceTotal;
			$invoiceTotalsPerUser[]=$userArray;
		}
		$this->set(compact('invoiceTotalsPerUser'));
		
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
/*
	public function view($id = null) {
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid invoice'));
		}

		$options = [
			'conditions' => ['Invoice.id' => $id],
			'contain'=>[
				'Client',
				'Currency',
				'InvoiceSalesOrder'=>[
					'SalesOrder'=>[
						'Quotation',
					],
					'User',
				],
				'InvoiceProduct'=>[
					'Product',
				],
				'VendorCommissionPayment',
			],
			'order'=>'Invoice.invoice_date DESC, Invoice.invoice_code DESC'
		];
		$invoice=$this->Invoice->find('first', $options);
		$this->set(compact('invoice'));
		
		$filename='Factura_'.$invoice['Invoice']['invoice_code'];
		$this->set(compact('filename'));
		
    $aco_name="Invoices/editarReferencia";		
		$bool_invoice_editarReferencia_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_editarReferencia_permission'));
		
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
				'InvoiceSalesOrder'=>array(
					'SalesOrder'=>array(
						'Quotation'=>array(
							'Contact',
						),
					),
					'User',
				),				
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
*/
  public function detalle($id = null) {
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid invoice'));
		}

		$options = [
			'conditions' => ['Invoice.id' => $id],
			'contain'=>[
				'Client',
				'Currency',
				'InvoiceSalesOrder'=>[
					'SalesOrder'=>[
						'Quotation',
					],
					'User',
				],
				'InvoiceProduct'=>[
					'Product',
				],
				'VendorCommissionPayment',
			],
			'order'=>'Invoice.invoice_date DESC, Invoice.invoice_code DESC'
		];
		$invoice=$this->Invoice->find('first', $options);
		$this->set(compact('invoice'));
		
		$filename='Factura_'.$invoice['Invoice']['invoice_code'];
		$this->set(compact('filename'));
		
    $aco_name="Invoices/editarReferencia";		
		$bool_invoice_editarReferencia_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_invoice_editarReferencia_permission'));
		
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
	
	public function detallePdf($id = null) {
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid invoice'));
		}
		
		$options = array(
			'conditions' => array('Invoice.' . $this->Invoice->primaryKey => $id),
			'contain'=>array(
				'Client',
				'Currency',
				'InvoiceSalesOrder'=>array(
					'SalesOrder'=>array(
						'Quotation'=>array(
							'Contact',
						),
					),
					'User',
				),				
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

  public function imprimirVenta($id = null) {
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid sale'));
		}
    
    $this->loadModel('ExchangeRate');
    
    define('INVOICE_ARTICLES_MAX',20);
   
    $options = [
			'conditions' => ['Invoice.id' => $id],
			'contain'=>[
				'Client',
				'Currency',
				'InvoiceSalesOrder'=>[
					'SalesOrder'=>[
						'Quotation',
					],
					'User',
				],
				'InvoiceProduct'=>[
          'Currency',
					'Product',
				],
				'VendorCommissionPayment',
			],
			'order'=>'Invoice.invoice_date DESC, Invoice.invoice_code DESC'
		];
		$invoice=$this->Invoice->find('first', $options);
    
    $invoiceProductFontSize=$this->getFontSizeBasedOnInvoiceProducts($invoice['InvoiceProduct']);
    $this->set(compact('invoiceProductFontSize'));
    //echo 'invoiceProductFontSize is '.$invoiceProductFontSize.'<br/>';
    
    if (!empty($invoice)){
			$exchangeRateCurrent=$this->ExchangeRate->getApplicableExchangeRateValue(date('Y-m-d'));
			$invoicePriceTotal=$invoice['Invoice']['price_total'];
			if ($invoice['Invoice']['currency_id']==CURRENCY_USD){
        $invoice['Invoice']['price_total_cs']=$invoicePriceTotal*$exchangeRateCurrent;
        $invoice['Invoice']['price_total_usd']=$invoicePriceTotal;
			}
      else {
        $invoice['Invoice']['price_total_cs']=$invoicePriceTotal;
        $invoice['Invoice']['price_total_usd']=$invoicePriceTotal/$exchangeRateCurrent;
      }
    }
		
		$this->set(compact('invoice'));
	}
	
  public function crear() {
    $this->loadModel('ActionType');
    $this->loadModel('Client');
    $this->loadModel('Currency');
    $this->loadModel('ExchangeRate');
		
		$this->loadModel('Product');
    $this->loadModel('InvoiceProduct');
		$this->loadModel('InvoiceSalesOrder');
		$this->loadModel('InvoiceRemark');
		$this->loadModel('SalesOrder');
		$this->loadModel('SalesOrderProduct');
    $this->loadModel('Quotation');
		
    $this->loadModel('User');
    $this->loadModel('UserPageRight');
		
    $loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));

    $canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllUsers'));
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllSalesExecutives'));
    
    $canSavePartialInvoice=$this->UserPageRight->hasUserPageRight('GRABAR_FACTURA_PARCIAL',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSavePartialInvoice'));
    
		$productChecks=[
      '0'=>"Solo remitir productos con estado Listo para Entrega",
      '1'=>"Remitir todos productos de ordenes de venta autorizadas no entregadas",
    ];
		$this->set(compact('productChecks'));
		$boolSkipProductChecks=1;
    
    $productsForInvoice=[];
		$selectedSalesOrderArray=[];
    
		if ($this->request->is('post')) {
      $currencyId=$this->request->data['Invoice']['currency_id']; 
			$boolSkipProductChecks=$this->request->data['Invoice']['bool_skip_product_checks'];
			$selectedSalesOrderArray=$this->request->data['Invoice']['sales_order_id'];
			
			if (empty($this->request->data['refresh'])){
				$invoiceDateArray=$this->request->data['Invoice']['invoice_date'];
				//pr($quotationDateArray);
				$invoiceDateString=$invoiceDateArray['year'].'-'.$invoiceDateArray['month'].'-'.$invoiceDateArray['day'];
				$invoiceDate=date( "Y-m-d", strtotime($invoiceDateString));
				
				$boolMultiplicationOK=true;
				if (!$this->request->data['Invoice']['bool_annulled']){
					if (!empty($this->request->data['InvoiceProduct'])){
						foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
              $invoiceProduct['Product']['bool_no_iva']=$invoiceProduct['bool_no_iva'];
              $productsForInvoice[]=$invoiceProduct;
							
              if ($invoiceProduct['product_id']>0){
								$multiplicationDifference=abs($invoiceProduct['product_total_price']-$invoiceProduct['product_quantity']*$invoiceProduct['product_unit_price']);
								if ($multiplicationDifference>=0.01){
									$boolMultiplicationOK=false;
								};
							}
						}
					}
				}
				
				if ($invoiceDateString>date('Y-m-d')){
					$this->Session->setFlash(__('La fecha de factura no puede estar en el futuro!  No se guardó la factura.'), 'default',array('class' => 'error-message'));
				}
				elseif (!$boolMultiplicationOK){
					$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
				}
				elseif ($this->request->data['Invoice']['bool_annulled'] && empty($this->request->data['InvoiceRemark']['remark_text'])){
					$this->Session->setFlash(__('Al anular la factura se debe dejar una remarca.  No se guardó la factura.'), 'default',['class' => 'error-message']);
				}
        elseif (empty($this->request->data['Invoice']['client_name'])){
          $this->Session->setFlash('Se debe registrar el nombre para el cliente.  No se guardó la factura.', 'default',['class' => 'error-message']);  
        }
        elseif (empty($this->request->data['Invoice']['client_phone']) && empty($this->request->data['Invoice']['client_cell']) && empty($this->request->data['Invoice']['client_email'])){
          $this->Session->setFlash('Se debe registrar o un número de teléfono o un email para el cliente.  No se guardó la factura.', 'default',['class' => 'error-message']);  
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
							$invoiceId=$this->Invoice->id;
						}
						else {
							// STEP I THE INVOICE
							$this->Invoice->create();
							if (!$this->Invoice->save($this->request->data)) {
								echo "Problema guardando la factura";
								pr($this->validateErrors($this->Invoice));
								throw new Exception();
							}
							$invoiceId=$this->Invoice->id;
							
							if (!empty($this->request->data['InvoiceProduct'])){
								foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
									if ($invoiceProduct['product_id'] > 0 && $invoiceProduct['product_quantity'] > 0){									
										//pr($invoiceProduct);
										// STEP II THE INVOICE PRODUCT
										$productArray=[
                      'InvoiceProduct'=>[
                        'invoice_id'=>$invoiceId,
                        'product_id'=>$invoiceProduct['product_id'],
                        'product_description'=>$invoiceProduct['product_description'],
                        'product_quantity'=>$invoiceProduct['product_quantity'],
                        'product_unit_price'=>$invoiceProduct['product_unit_price'],
                        'product_total_price'=>$invoiceProduct['product_total_price'],
                        'bool_iva'=>$invoiceProduct['bool_iva'],
                        'currency_id'=>$this->request->data['Invoice']['currency_id'],
                        'sales_order_product_id'=>$invoiceProduct['sales_order_product_id'],
                      ],
                    ];                      
										$this->InvoiceProduct->create();
										if (!$this->InvoiceProduct->save($productArray)) {
											echo "Problema guardando los productos de la factura";
											pr($this->validateErrors($this->InvoiceProduct));
											throw new Exception();
										}
										
										// STEP III THE SALES ORDER PRODUCT
										if ($invoiceProduct['product_quantity'] == $invoiceProduct['product_quantity_pending']){
											// product completely delivered
											$this->SalesOrderProduct->id=$invoiceProduct['sales_order_product_id'];
											$salesOrderProductArray=[
                        'SalesOrderProduct'=>[
                          'id'=>$invoiceProduct['sales_order_product_id'],
                          'sales_order_product_status_id'=>PRODUCT_STATUS_DELIVERED,
                        ],
                      ];
											if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
												echo "Problema cambiando el estado de los productos de la orden de venta";
												pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
												throw new Exception();
											}
										}
                    // 20211002 REMOVE DIVISION OF SALESORDERPRODUCT
										//else {
										//	if (!$this->SalesOrderProduct->splitSalesOrderProduct($invoiceProduct['sales_order_product_id'], PRODUCT_STATUS_DELIVERED,$invoiceProduct['product_quantity'])){
										//		echo "Problema con la entrega parcial de los productos de la orden de venta";
										//		throw new Exception();
										//	}
										//	//echo "alrighty the sales order product has been successfully split!!<br/>";
										//}
									}
								}								
							}
							$salesOrderIds=$this->request->data['Invoice']['sales_order_id'];
							//pr($salesOrderIds);
							if (!empty($salesOrderIds)){
								foreach ($salesOrderIds as $key=>$salesOrderId){
									$relatedSalesOrder=$this->SalesOrder->find('first',[
										'conditions'=>['SalesOrder.id'=>$salesOrderId],
										'contain'=>[
											'Quotation',
											'SalesOrderProduct',
										],
									]);
									if (!empty($relatedSalesOrder)){
										$invoiceSalesOrderArray=[
                      'InvoiceSalesOrder'=>[
                        'invoice_id'=>$invoiceId,
                        'sales_order_id'=>$salesOrderId,
                        'user_id'=>$relatedSalesOrder['Quotation']['user_id'],
                      ],
                    ];
										$this->InvoiceSalesOrder->create();
										if (!$this->InvoiceSalesOrder->save($invoiceSalesOrderArray)) {
											echo "Problema guardando la relación entre facturas y ordenes de venta";
											pr($this->validateErrors($this->InvoiceSalesOrder));
											throw new Exception();
										}									
										
										$boolCompletelyDelivered=true;
										foreach ($relatedSalesOrder['SalesOrderProduct']as $salesOrderProduct){
											//pr($salesOrderProduct);
											if ($salesOrderProduct['product_quantity'] > 0 && $salesOrderProduct['sales_order_product_status_id']<PRODUCT_STATUS_DELIVERED){
												$boolCompletelyDelivered=false;
											}
										}
										
										$salesOrderArray=[
                      'SalesOrder'=>[
                        'id'=>$salesOrderId,
                        'bool_completely_delivered'=>$boolCompletelyDelivered,
                      ],
                    ];
										$this->SalesOrder->id=$salesOrderId;
										if (!$this->SalesOrder->save($salesOrderArray)) {
											echo "Problema guardando el estado de entregado de la factura";
											pr($this->validateErrors($this->SalesOrder));
											throw new Exception();
										}
									}
								}								
							}
							if (!empty($this->request->data['InvoiceRemark']['remark_text'])){
								$invoiceRemark=$this->request->data['InvoiceRemark'];
								//pr($invoiceRemark);
								$invoiceRemarkArray=[
                  'InvoiceRemark'=>[
                    'user_id'=>$invoiceRemark['user_id'],
                    'invoice_id'=>$invoiceId,
                    'remark_datetime'=>date('Y-m-d H:i:s'),
                    'remark_text'=>$invoiceRemark['remark_text'],
                  ],
                ];
								//$invoiceRemarkArray['InvoiceRemark']['working_days_before_reminder']=$invoiceRemark['working_days_before_reminder'];
								//$invoiceRemarkArray['InvoiceRemark']['reminder_date']=$invoiceRemark['reminder_date'];
								//$invoiceRemarkArray['InvoiceRemark']['action_type_id']=$invoiceRemark['action_type_id'];
								$this->InvoiceRemark->create();
								if (!$this->InvoiceRemark->save($invoiceRemarkArray)) {
									echo "Problema guardando las remarcas para la factura";
									pr($this->validateErrors($this->InvoiceRemark));
									throw new Exception();
								}
							}
						}
						
						$datasource->commit();
						//echo "everything nicely committed<br/>";
						$this->recordUserAction($this->Invoice->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se registró la factura número ".$this->request->data['Invoice']['invoice_code']);
						
						$this->Session->setFlash(__('The invoice has been saved.'),'default',['class' => 'success']);
						if (!empty($this->request->data['saveandnext'])){
							return $this->redirect(['action' => 'crear']);
						}
						else{
							return $this->redirect(['action' => 'detalle',$invoiceId]);
						}
					} 
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('The invoice could not be saved. Please, try again.'), 'default',['class' => 'error-message']);
					}
				}
			}
		}
    $this->set(compact('currencyId'));	
    $this->set(compact('selectedSalesOrderArray'));	
		$this->set(compact('productsForInvoice'));	  

    
		if ($boolSkipProductChecks){
			// in case bool_skip_production_checks is true=>just make a direct check on bool_completely_delivered and that's it
			$salesOrders=$this->SalesOrder->find('list',[
				'conditions'=>[
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
				],
				'order'=>'SalesOrder.sales_order_code',
			]);
			$quotationIds=$this->SalesOrder->find('list',[
				'fields'=>['SalesOrder.quotation_id'],
				'conditions'=>[
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
				],
			]);
		}
		else {
			$salesOrderIdsBasedOnProducts=$this->SalesOrder->SalesOrderProduct->find('list',[
				'fields'=>['SalesOrderProduct.sales_order_id'],
				'conditions'=>[
					'SalesOrderProduct.sales_order_product_status_id'=>[PRODUCT_STATUS_READY_FOR_DELIVERY],
				],
			]);
			$salesOrders=$this->SalesOrder->find('list',[
				'conditions'=>[				
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
					'SalesOrder.id'=>$salesOrderIdsBasedOnProducts,
				],
				'order'=>'SalesOrder.sales_order_code',
			]);
			$quotationIds=$this->SalesOrder->find('list',[
				'fields'=>['SalesOrder.quotation_id'],
				'conditions'=>[
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
					'SalesOrder.id'=>$salesOrderIdsBasedOnProducts,
				],
			]);
		}
		$this->set(compact('boolSkipProductChecks'));
    
    $productIdsListForInvoice=[];
    foreach($productsForInvoice as $invoiceProduct){
      //pr($invoiceProduct);
      if (!in_array($invoiceProduct['product_id'],$productIdsListForInvoice)){
        $productIdsListForInvoice[]=$invoiceProduct['product_id'];
      }
    }
    $products=$this->Product->find('list',[
      'conditions'=>[
        'OR'=>[
          ['Product.bool_active'=>true],
          ['Product.id'=>$productIdsListForInvoice]
        ],
      ],
      'order'=>'Product.name'
    ]);
    $this->set(compact('products'));	
    
		$clientIds=$this->Quotation->getClientIdsForQuotationIds($quotationIds);
    //pr($clientIds);
		$clients=$this->Client->getClientListForClientIds($clientIds);
    $currencies = $this->Currency->find('list');
		$this->set(compact('clients', 'users', 'currencies'));
    
    // WE DO NOT WANT TO SEE THE USER TO SEE SALES ORDERS THAT ARE NOT HIS OR HERS
		$salesOrders=[];
    $boolInitialLoad=true;
    if ($this->request->is('post')){
      $boolInitialLoad=false;
      $salesOrderIds=$this->request->data['Invoice']['sales_order_id'];
      $salesOrders=$this->SalesOrder->find('list',[
				'conditions'=>[				
					'SalesOrder.id'=>$salesOrderIds,
				],
				'order'=>'SalesOrder.sales_order_code',
			]);
    }
    $this->set(compact('salesOrders', 'boolInitialLoad'));
		//pr($salesOrders);
    
		$invoiceDate=date( "Y-m-d");
		$exchangeRateInvoice=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
		$this->set(compact('exchangeRateInvoice'));
		
		$actionTypes=$this->ActionType->find('list');
		$this->set(compact('actionTypes'));
		
		$users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));
    $vendorUsers = $this->User->find('list',[
			'conditions'=>[
				'User.id'=>$loggedUserId,
        'User.bool_active'=>true
			],
		]);
    $this->set(compact('vendorUsers'));
    //pr($salesOrders);
    $clientsBySalesOrder=$this->SalesOrder->getClientsBySalesOrder(array_keys($salesOrders));
    $this->set(compact('clientsBySalesOrder'));
    //pr($clientsBySalesOrder);
    
    $clientDataByClient=$this->Client->getClientDataByClient();
		$this->set(compact('clientDataByClient'));
    //pr($clientDataByClient);
    
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

	public function editar($id = null) {
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid invoice'));
		}
		$invoiceid=$id;
		$this->set(compact('invoiceid'));
		
    $this->loadModel('ActionType');
    $this->loadModel('Client');
    $this->loadModel('Currency');
    $this->loadModel('ExchangeRate');
		
		$this->loadModel('Product');
		$this->loadModel('InvoiceProduct');
		$this->loadModel('InvoiceSalesOrder');
		$this->loadModel('InvoiceRemark');
		$this->loadModel('SalesOrder');
		$this->loadModel('SalesOrderProduct');
    $this->loadModel('Quotation');
    
		$this->loadModel('User');
    $this->loadModel('UserPageRight');
		
    $loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    
    $canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllUsers'));
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllSalesExecutives'));
    
    $canSavePartialInvoice=$this->UserPageRight->hasUserPageRight('GRABAR_FACTURA_PARCIAL',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSavePartialInvoice'));
    
    $productChecks=[
      '0'=>"Solo remitir productos con estado Listo para Entrega",
      '1'=>"Remitir todos productos de ordenes de venta autorizadas no entregadas",
    ];
		$this->set(compact('productChecks'));
		$boolSkipProductChecks=1;
    
    $productsForInvoice=[];
		$selectedSalesOrderArray=[];
    
    $currencyId=CURRENCY_USD;
    if ($this->request->is(['post', 'put'])) {
      $currencyId=$this->request->data['Invoice']['currency_id'];
      $boolSkipProductChecks=$this->request->data['Invoice']['bool_skip_product_checks'];
			$selectedSalesOrderArray=$this->request->data['Invoice']['sales_order_id'];
			
			if (empty($this->request->data['refresh'])){				
				if (!$this->request->data['Invoice']['bool_annulled']){
					if (!empty($this->request->data['InvoiceProduct'])){
						foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
							//if ($invoiceProduct['product_quantity']>0){
								//pr($invoiceProduct);
                $invoiceProduct['Product']['bool_no_iva']=$invoiceProduct['bool_no_iva'];
								$productsForInvoice[]=$invoiceProduct;
							//}
						}
					}
				}
				//pr($productsForInvoice);
			
				$invoiceDateArray=$this->request->data['Invoice']['invoice_date'];
				//pr($quotationDateArray);
				$invoiceDateString=$invoiceDateArray['year'].'-'.$invoiceDateArray['month'].'-'.$invoiceDateArray['day'];
				$invoiceDate=date( "Y-m-d", strtotime($invoiceDateString));
				
				//$lastPaymentDateArray=$this->request->data['Invoice']['last_payment_date'];
				//$lastPaymentDateString=$lastPaymentDateArray['year'].'-'.$lastPaymentDateArray['month'].'-'.$lastPaymentDateArray['day'];
				//$lastPaymentDate=date( "Y-m-d", strtotime($lastPaymentDateString));
				
				$boolMultiplicationOK=true;
				if (!$this->request->data['Invoice']['bool_annulled']){
					foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
						if ($invoiceProduct['product_id']>0){
							$multiplicationDifference=abs($invoiceProduct['product_total_price']-$invoiceProduct['product_quantity']*$invoiceProduct['product_unit_price']);
							if ($multiplicationDifference>=0.01){
								$boolMultiplicationOK=false;
							};
						}
					}
				}
		
				if ($invoiceDateString>date('Y-m-d')){
					$this->Session->setFlash(__('La fecha de factura no puede estar en el futuro!  No se guardó la factura.'), 'default',array('class' => 'error-message'));
				}
				elseif (!$boolMultiplicationOK){
					$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
				}
				elseif ($this->request->data['Invoice']['bool_annulled']&&empty($this->request->data['InvoiceRemark']['remark_text'])){
					$this->Session->setFlash(__('Al anular la factura se debe dejar una remarca.  No se guardó la factura.'), 'default',array('class' => 'error-message'));
				}
        elseif (empty($this->request->data['Invoice']['client_name'])){
          $this->Session->setFlash('Se debe registrar el nombre para el cliente.  No se guardó la factura.', 'default',['class' => 'error-message']);  
        }
        elseif (empty($this->request->data['Invoice']['client_phone']) && empty($this->request->data['Invoice']['client_cell']) && empty($this->request->data['Invoice']['client_email'])){
          $this->Session->setFlash('Se debe registrar o un número de teléfono o un email para el cliente.  No se guardó la factura.', 'default',['class' => 'error-message']);  
        }
				else {	
					$datasource=$this->Invoice->getDataSource();
					$datasource->begin();
					try {
						// FIRST REMOVE THE OLD DATA
						//pr($this->request->data);
						$this->InvoiceProduct->recursive=-1;
						$previousInvoiceProducts=$this->InvoiceProduct->find('all',[
							'fields'=>[
								'InvoiceProduct.id',
								'InvoiceProduct.product_id',
								'InvoiceProduct.sales_order_product_id'
							],
							'conditions'=>[
								'InvoiceProduct.invoice_id'=>$id,
							],
						]);
						if (!empty($previousInvoiceProducts)){
							foreach ($previousInvoiceProducts as $previousInvoiceProduct){
								$this->InvoiceProduct->id=$previousInvoiceProduct['InvoiceProduct']['id'];
								$this->InvoiceProduct->delete($previousInvoiceProduct['InvoiceProduct']['id']);
								/*
								$this->SalesOrderProduct->recursive=-1;
								$salesOrderProduct=$this->SalesOrderProduct->find('first',array(
									'conditions'=>array(
										'SalesOrderProduct.id'=>$previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'],
									),
								));
								*/
								$calculatedSalesOrderProductStatus=$this->SalesOrderProduct->calculateSalesOrderProductStatus($previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'],$id);
								//echo "calculated status is ".$calculatedSalesOrderProductStatus."<br/>";
								$salesOrderProductArray=[
                  'SalesOrderProduct'=>[
                    'id'=>$previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'],
                    'sales_order_product_status_id'=>$calculatedSalesOrderProductStatus,
                  ],
                ];					
								$this->SalesOrderProduct->id=$previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'];
								if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
									echo "Problema cambiando el estado de los productos de la orden de venta";
									pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
									throw new Exception();
								}
							}
						}
						
						$this->InvoiceSalesOrder->recursive=-1;
						$previousInvoiceSalesOrders=$this->InvoiceSalesOrder->find('all',[
							'fields'=>[
								'InvoiceSalesOrder.id',
								'InvoiceSalesOrder.sales_order_id',
							],
							'conditions'=>[
								'InvoiceSalesOrder.invoice_id'=>$id,
							],
						]);
						if (!empty($previousInvoiceSalesOrders)){
							foreach ($previousInvoiceSalesOrders as $previousInvoiceSalesOrder){
								$this->SalesOrder->id=$previousInvoiceSalesOrder['InvoiceSalesOrder']['sales_order_id'];
								$salesOrderArray=[
                  'SalesOrder'=>[
                    'id'=>$previousInvoiceSalesOrder['InvoiceSalesOrder']['sales_order_id'],
                    'bool_completely_delivered'=>false,
                  ],
                ];
								if (!$this->SalesOrder->save($salesOrderArray)){
									echo "Problema cambiando el estado de la orden de venta";
									pr($this->validateErrors($this->SalesOrder));
									throw new Exception();
								}
								$this->InvoiceSalesOrder->id=$previousInvoiceSalesOrder['InvoiceSalesOrder']['id'];
								$this->InvoiceSalesOrder->delete($previousInvoiceSalesOrder['InvoiceSalesOrder']['id']);
							}
						}
						
						// SECOND CHECK FOR ANNULLED AND HANDLE CORRESPONDING CASE
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
							$invoiceId=$this->Invoice->id;
						}
						else {
							// STEP I THE INVOICE
							$this->Invoice->create();
							if (!$this->Invoice->save($this->request->data)) {
								echo "Problema guardando la factura";
								pr($this->validateErrors($this->Invoice));
								throw new Exception();
							}
							$invoiceId=$this->Invoice->id;
							
							if (!empty($this->request->data['InvoiceProduct'])){
								foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
									if ($invoiceProduct['product_id']>0 && $invoiceProduct['product_quantity']>0){	
										//pr($invoiceProduct);
										
										$productArray=[
                      'InvoiceProduct'=>[
                        'invoice_id'=>$invoiceId,
                        'product_id'=>$invoiceProduct['product_id'],
                        'product_description'=>$invoiceProduct['product_description'],
                        'product_quantity'=>$invoiceProduct['product_quantity'],
                        'product_unit_price'=>$invoiceProduct['product_unit_price'],
                        'product_total_price'=>$invoiceProduct['product_total_price'],
                        'bool_iva'=>$invoiceProduct['bool_iva'],
                        'currency_id'=>$this->request->data['Invoice']['currency_id'],
                        'sales_order_product_id'=>$invoiceProduct['sales_order_product_id'],
                      ],
                    ];      
                    $this->InvoiceProduct->create();
                    if (!$this->InvoiceProduct->save($productArray)) {
                      echo "Problema guardando los productos de la factura";
                      pr($this->validateErrors($this->InvoiceProduct));
                      throw new Exception();
                    }
										
										// STEP III THE SALES ORDER PRODUCT
                    if ($invoiceProduct['product_quantity']==$invoiceProduct['product_quantity_pending']){
                      // product completely delivered
                      $this->SalesOrderProduct->id=$invoiceProduct['sales_order_product_id'];
											$salesOrderProductArray=[
                        'SalesOrderProduct'=>[
                          'id'=>$invoiceProduct['sales_order_product_id'],
                          'sales_order_product_status_id'=>PRODUCT_STATUS_DELIVERED,
                        ],
                      ];
                      if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
                        echo "Problema cambiando el estado de los productos de la orden de venta";
                        pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
                        throw new Exception();
                      }
                    }
                    // 20211002 REMOVE DIVISION OF SALESORDERPRODUCT
                    //else {
                    //	if (!$this->SalesOrderProduct->splitSalesOrderProduct($invoiceProduct['sales_order_product_id'], PRODUCT_STATUS_DELIVERED,$invoiceProduct['product_quantity'])){
                    //		echo "Problema con la entrega parcial de los productos de la orden de venta";
                    //		throw new Exception();
                    //	}
                    //}
									}
								}								
							}
							$salesOrders=$this->request->data['Invoice']['sales_order_id'];
							//pr($salesOrders);
							if (!empty($salesOrders)){
								foreach ($salesOrders as $key=>$salesOrderId){
									$relatedSalesOrder=$this->SalesOrder->find('first',[
										'conditions'=>['SalesOrder.id'=>$salesOrderId],
										'contain'=>[
											'Quotation',
											'SalesOrderProduct',
										],
									]);
									if (!empty($relatedSalesOrder)){
										$invoiceSalesOrderArray=[
                      'InvoiceSalesOrder'=>[
                        'invoice_id'=>$invoiceId,
                        'sales_order_id'=>$salesOrderId,
                        'user_id'=>$relatedSalesOrder['Quotation']['user_id'],
                      ],
                    ];
										$this->InvoiceSalesOrder->create();
										if (!$this->InvoiceSalesOrder->save($invoiceSalesOrderArray)) {
											echo "Problema guardando la relación entre facturas y ordenes de venta";
											pr($this->validateErrors($this->InvoiceSalesOrder));
											throw new Exception();
										}									
										
										$boolCompletelyDelivered=true;
										foreach ($relatedSalesOrder['SalesOrderProduct']as $salesOrderProduct){
											//pr($salesOrderProduct);
											if ($salesOrderProduct['product_quantity'] > 0 && $salesOrderProduct['sales_order_product_status_id']<PRODUCT_STATUS_DELIVERED){
												$boolCompletelyDelivered=false;
											}
										}
										
										
										$salesOrderArray=[
                      'SalesOrder'=>[
                        'id'=>$salesOrderId,
                        'bool_completely_delivered'=>$boolCompletelyDelivered,
                      ],
                    ];
										$this->SalesOrder->id=$salesOrderId;
										if (!$this->SalesOrder->save($salesOrderArray)) {
											echo "Problema guardando el estado de entregado de la factura";
											pr($this->validateErrors($this->SalesOrder));
											throw new Exception();
										}	
									}
								}								
							}
							if (!empty($this->request->data['InvoiceRemark']['remark_text'])){
								$invoiceRemark=$this->request->data['InvoiceRemark'];
								//pr($invoiceRemark);
								$invoiceRemarkArray=[
                  'InvoiceRemark'=>[
                    'user_id'=>$invoiceRemark['user_id'],
                    'invoice_id'=>$invoiceId,
                    'remark_datetime'=>date('Y-m-d H:i:s'),
                    'remark_text'=>$invoiceRemark['remark_text'],
                  ],
                ];
								//$invoiceRemarkArray['InvoiceRemark']['working_days_before_reminder']=$invoiceRemark['working_days_before_reminder'];
								//$invoiceRemarkArray['InvoiceRemark']['reminder_date']=$invoiceRemark['reminder_date'];
								//$invoiceRemarkArray['InvoiceRemark']['action_type_id']=$invoiceRemark['action_type_id'];
								$this->InvoiceRemark->create();
								if (!$this->InvoiceRemark->save($invoiceRemarkArray)) {
									echo "Problema guardando las remarcas para la factura";
									pr($this->validateErrors($this->InvoiceRemark));
									throw new Exception();
								}
							}
						}
						
						$datasource->commit();
						$this->recordUserAction($this->Invoice->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se editó la factura número ".$this->request->data['Invoice']['invoice_code']);
						
						$this->Session->setFlash(__('La factura se editó exitósamente.'),'default',['class' => 'success']);
						if (!empty($this->request->data['saveandnext'])){
							return $this->redirect(['action' => 'crear']);
						}
						else{
							return $this->redirect(['action' => 'detalle',$invoiceId]);
						}
					} 
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('No se podía guardar la factura.'), 'default',array('class' => 'error-message'));
					}
				}
			}
		}
		else {
			$options = [
				'conditions' => [
					'Invoice.id'=> $id,
				],
				'contain'=>[
					'Client'=>[
						'Quotation'=>[
							'fields'=>'Quotation.id',
						],
					],
					'InvoiceProduct'=>[
						'Product',
					],
					'InvoiceSalesOrder'=>[
						'SalesOrder'=>[
							'SalesOrderProduct',
						],
					],
				],
			];
			$this->request->data =$invoice= $this->Invoice->find('first', $options);
      //pr($invoice);
			$currencyId=$this->request->data['Invoice']['currency_id'];
			// $productsForInvoice=$this->request->data['InvoiceProduct'];
			$productsForInvoice=[];
			foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
				$boolProductInArray=false;
				if (!empty($productsForInvoice)){
					for ($pfi=0;$pfi<count($productsForInvoice);$pfi++){
						if (!$boolProductInArray){
							if (($productsForInvoice[$pfi]['product_id']==$invoiceProduct['product_id'])&&($productsForInvoice[$pfi]['product_description']==$invoiceProduct['product_description'])){
								//echo "summing the quantity<br/>";
								$productsForInvoice[$pfi]['product_quantity']+=$invoiceProduct['product_quantity'];
								//pr($productsForInvoice[$pfi]);
								$boolProductInArray=true;
							}
						}
					}
				}
				if (!$boolProductInArray){
					$productsForInvoice[]=$invoiceProduct;
					//echo "product for invoice added<br/>";
				}
			}
			//pr($productsForInvoice);
			
      $currentInvoiceId=$id; 
      
			foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
				$selectedSalesOrderArray[]=$invoiceSalesOrder['sales_order_id'];
			}
      
      $productsForSalesOrders=[];
      if (!empty($selectedSalesOrderArray)){
        $salesOrderIds  =$selectedSalesOrderArray;
        //pr($selectedSalesOrderArray);
        if ($boolSkipProductChecks){
          $salesOrderProductConditionsBasedOnProductStatus=[
            'SalesOrderProduct.sales_order_id'=>$salesOrderIds,
            'SalesOrderProduct.sales_order_product_status_id <'=>PRODUCT_STATUS_DELIVERED,
          ];
        }
        else {
          $salesOrderProductConditionsBasedOnProductStatus=[
            'SalesOrderProduct.sales_order_id'=>$salesOrderIds,
            'SalesOrderProduct.sales_order_product_status_id >='=>PRODUCT_STATUS_READY_FOR_DELIVERY,
          ];
        }
        
        $salesOrderProductIdsInInvoice=$this->InvoiceProduct->getSalesOrderProductIdsForInvoiceId($id);
        $salesOrderProductConditionsFromCurrentInvoice=[
          'SalesOrderProduct.id'=>$salesOrderProductIdsInInvoice,
        ];
        $salesOrderProductConditions=[
          'OR'=>[
            $salesOrderProductConditionsBasedOnProductStatus,
            $salesOrderProductConditionsFromCurrentInvoice,
          ],
        ];
       
        $productsForSalesOrders=$this->SalesOrderProduct->find('all',[
          'fields'=>[
            'SalesOrderProduct.id',
            'SalesOrderProduct.product_id','SalesOrderProduct.product_description',
            'SalesOrderProduct.product_quantity',
            'SalesOrderProduct.product_unit_price','SalesOrderProduct.product_total_price',
            'SalesOrderProduct.currency_id','SalesOrderProduct.bool_iva',
            'SalesOrderProduct.sales_order_product_status_id','SalesOrderProduct.bool_no_production',
          ],
          'conditions'=>$salesOrderProductConditions,
          'contain'=>[
            'Product'=>[
              'fields'=>[
                'Product.id',
                'Product.bool_no_iva',
              ]
            ],
            'SalesOrder'=>[
              'InvoiceSalesOrder'=>[
                'Invoice'=>[
                  'InvoiceProduct'
                ],
              ],
            ],
          ],
          'order'=>'SalesOrderProduct.sales_order_id',
        ]);
        //pr($productsForSalesOrders);
        if (!empty($productsForSalesOrders)){
          for($sop=0;$sop<count($productsForSalesOrders);$sop++){
            if (empty($productsForSalesOrders[$sop]['SalesOrder']['InvoiceSalesOrder'])){
              $productsForSalesOrders[$sop]['SalesOrderProduct']['product_quantity_pending']=$productsForSalesOrders[$sop]['SalesOrderProduct']['product_quantity'];
            }
            else {
              $quantityAlreadyDelivered=0;
              $productQuantity=0;
              foreach($productsForSalesOrders[$sop]['SalesOrder']['InvoiceSalesOrder'] as $invoiceSalesOrder){
                foreach ($invoiceSalesOrder['Invoice']['InvoiceProduct'] as $invoiceProduct){
                  //pr($invoiceProduct);
                  //pr($productsForSalesOrders[$sop]['SalesOrderProduct']);
                  if ($invoiceProduct['sales_order_product_id'] == $productsForSalesOrders[$sop]['SalesOrderProduct']['id']){
                    if ($invoiceProduct['invoice_id'] != $currentInvoiceId){
                      $quantityAlreadyDelivered+=$invoiceProduct['product_quantity'];
                      //echo 'quantityAlreadyDelivered is '.$quantityAlreadyDelivered.'<br/>';
                    }
                    else {
                      // if the product is in the current invoice, show the current invoice amount  
                      $productQuantity+=$invoiceProduct['product_quantity'];
                    }
                  }
                }
              }
              if ($productQuantity == 0){
                $productQuantity=$productsForSalesOrders[$sop]['SalesOrderProduct']['product_quantity']-$quantityAlreadyDelivered;
              }
              $productsForSalesOrders[$sop]['SalesOrderProduct']['product_quantity_pending']=$productsForSalesOrders[$sop]['SalesOrderProduct']['product_quantity']-$quantityAlreadyDelivered;
              $productsForSalesOrders[$sop]['SalesOrderProduct']['product_quantity']=$productQuantity;
            }
          }
        }
      }
      $this->set(compact('productsForSalesOrders'));	
      //pr($productsForSalesOrders);
      $extraProductsForInvoice=[];
      if (!empty($productsForSalesOrders)){
        for ($sop=0;$sop<count($productsForSalesOrders);$sop++){
          $boolProductInInvoice=false;
          for ($ip=0;$ip<count($productsForInvoice);$ip++){
            if ($productsForSalesOrders[$sop]['SalesOrderProduct']['id'] == $productsForInvoice[$ip]['sales_order_product_id']){
              $boolProductInInvoice=true;
              $productsForInvoice[$ip]['product_quantity_pending']=$productsForSalesOrders[$sop]['SalesOrderProduct']['product_quantity_pending'];
            }
          }
          if (!$boolProductInInvoice){
            $productsForSalesOrders[$sop]['SalesOrderProduct']['product_quantity']=0;  
            $extraProductsForInvoice[]=$productsForSalesOrders[$sop]['SalesOrderProduct'];
          }
       }
      }
      $productsForInvoice += $extraProductsForInvoice;
		}
		$this->set(compact('currencyId'));	
    $this->set(compact('selectedSalesOrderArray'));	
		//pr($selectedSalesOrderArray);
    $this->set(compact('productsForInvoice'));	
    //pr($productsForInvoice);
		
		
    // FIRST GET THE CLIENT LIST
		$boolSkipProductChecks=$this->request->data['Invoice']['bool_skip_product_checks'];
		$quotationIds=[];
		if ($boolSkipProductChecks){
			// in case bool_skip_production_checks is true=>just make a direct check on bool_completely_delivered and that's it
			$quotationIds=$this->SalesOrder->find('list',[
				'fields'=>['SalesOrder.quotation_id'],
				'conditions'=>[
					'OR'=>[
						[
							'SalesOrder.bool_completely_delivered'=>false,
							'SalesOrder.bool_authorized'=>true,
						],
						[
							'SalesOrder.id'=>$selectedSalesOrderArray,
						],
					],
				],
			]);
		}
		else {
			$salesOrderIdsBasedOnProducts=$this->SalesOrder->SalesOrderProduct->find('list',[
				'fields'=>['SalesOrderProduct.sales_order_id'],
				'conditions'=>[
					'SalesOrderProduct.sales_order_product_status_id'=>[PRODUCT_STATUS_READY_FOR_DELIVERY],
				],
			]);
			$quotationIds=$this->SalesOrder->find('list',[
				'fields'=>['SalesOrder.quotation_id'],
				'conditions'=>[
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
					'OR'=>[
						[
							'SalesOrder.id'=>$salesOrderIdsBasedOnProducts,
						],
						[
							'SalesOrder.id'=>$selectedSalesOrderArray,
						],
					],	
				],
			]); 
		}

    
		//pr($quotationIds);
		$clientIds=$this->Quotation->getClientIdsForQuotationIds($quotationIds);;
		$clients=$this->Invoice->Client->getClientListForClientIds($clientIds);
		$this->set(compact('clients'));
		
		// SECOND GET THE SALES ORDERS FOR THE SELECTED CLIENT
		//20160811 IT IS NOT ENOUGH TO JUST GET THE SALES ORDERS; ONE MUST ALSO GET THE SALESORDERS THAT WERE COMPLETED WITHIN THE INVOICE
		$quotationIdsForClient=$this->Quotation->find('list',[
			'fields'=>'Quotation.id',
			'conditions'=>[
				'Quotation.client_id'=>$this->request->data['Invoice']['client_id'],
			],
		]);
		$salesOrderConditions=[
			'OR'=>[
				[
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
					'SalesOrder.quotation_id'=>$quotationIdsForClient,
				],
				[
					'SalesOrder.id'=>$selectedSalesOrderArray,
				],
			],
		];
		$this->SalesOrder->recursive=-1;
		if (!$this->request->data['Invoice']['bool_skip_product_checks']){
			$salesOrderIdsBasedOnProducts=$this->SalesOrder->SalesOrderProduct->find('list',[
				'fields'=>['SalesOrderProduct.sales_order_id'],
				'conditions'=>[
					'SalesOrderProduct.sales_order_product_status_id >='=>PRODUCT_STATUS_READY_FOR_DELIVERY,
				],
			]);
			$salesOrderConditions['SalesOrder.id']=$salesOrderIdsBasedOnProducts;
		}		
		$salesOrders=$this->SalesOrder->find('list',[
			'fields'=>['SalesOrder.id','SalesOrder.sales_order_code',],
			'conditions'=>$salesOrderConditions,
			'order'=>'SalesOrder.sales_order_code ASC',
		]);
		$this->set(compact('salesOrders'));	
			
    $productIdsListForInvoice=[];
    foreach($productsForInvoice as $invoiceProduct){
      //pr($invoiceProduct);
      if (!in_array($invoiceProduct['product_id'],$productIdsListForInvoice)){
        $productIdsListForInvoice[]=$invoiceProduct['product_id'];
      }
    }
    $products=$this->Product->find('list',[
      'conditions'=>[
        'OR'=>[
          ['Product.bool_active'=>true],
          ['Product.id'=>$productIdsListForInvoice]
        ],
      ],
      'order'=>'Product.name'
    ]);
    $this->set(compact('products'));	
		
		$this->set(compact('boolSkipProductChecks'));
		
    
		//$users = $this->Invoice->User->find('list');
		$currencies = $this->Invoice->Currency->find('list');
		$this->set(compact('salesOrders', 'clients', 'users', 'currencies'));
		
		//$products=$this->Product->find('list',array('order'=>'Product.name'));
		//$this->set(compact('products'));
		
		$invoiceDateAsString=$this->Invoice->deconstruct('invoice_date',$this->request->data['Invoice']['invoice_date']);
		$exchangeRateInvoice=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDateAsString);
		$this->set(compact('exchangeRateInvoice'));
		
		$actionTypes=$this->ActionType->find('list');
		$this->set(compact('actionTypes'));
		
		$users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));
    
    $clientDataByClient=$this->Client->getClientDataByClient();
		$this->set(compact('clientDataByClient'));
    //pr($clientDataByClient);
    
    $genericClientIds=$this->Client->getGenericClientIds();
    $this->set(compact('genericClientIds'));
    
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
/*  
  public function add() {
    
		$this->loadModel('ActionType');
		
		$this->loadModel('InvoiceProduct');
		$this->loadModel('InvoiceSalesOrder');
		$this->loadModel('InvoiceRemark');
		$this->loadModel('Quotation');
		$this->loadModel('SalesOrder');
		$this->loadModel('SalesOrderProduct');
		
    $loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));

    $this->loadModel('User');
    $this->loadModel('UserPageRight');
		
    $canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllUsers'));
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllSalesExecutives'));
    
    $canSavePartialInvoice=$this->UserPageRight->hasUserPageRight('GRABAR_FACTURA_PARCIAL',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSavePartialInvoice'));
    
		$boolSkipProductChecks=1;
		
		if ($this->request->is('post')) {
			$boolSkipProductChecks=$this->request->data['Invoice']['bool_skip_product_checks'];
			
			if (empty($this->request->data['refresh'])){
				$invoiceDateArray=$this->request->data['Invoice']['invoice_date'];
				//pr($quotationDateArray);
				$invoiceDateString=$invoiceDateArray['year'].'-'.$invoiceDateArray['month'].'-'.$invoiceDateArray['day'];
				$invoiceDate=date( "Y-m-d", strtotime($invoiceDateString));
				
				$boolMultiplicationOK=true;
				if (!$this->request->data['Invoice']['bool_annulled']){
					if (!empty($this->request->data['InvoiceProduct'])){
						foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
							if ($invoiceProduct['product_id']>0){
								$multiplicationDifference=abs($invoiceProduct['product_total_price']-$invoiceProduct['product_quantity']*$invoiceProduct['product_unit_price']);
								if ($multiplicationDifference>=0.01){
									$boolMultiplicationOK=false;
								};
							}
						}
					}
				}
				
				if ($invoiceDateString>date('Y-m-d')){
					$this->Session->setFlash(__('La fecha de factura no puede estar en el futuro!  No se guardó la factura.'), 'default',array('class' => 'error-message'));
				}
				elseif (!$boolMultiplicationOK){
					$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
				}
				elseif ($this->request->data['Invoice']['bool_annulled']&&empty($this->request->data['InvoiceRemark']['remark_text'])){
					$this->Session->setFlash(__('Al anular la factura se debe dejar una remarca.  No se guardó la factura.'), 'default',array('class' => 'error-message'));
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
							$invoice_id=$this->Invoice->id;
						}
						else {
							// STEP I THE INVOICE
							$this->Invoice->create();
							if (!$this->Invoice->save($this->request->data)) {
								echo "Problema guardando la factura";
								pr($this->validateErrors($this->Invoice));
								throw new Exception();
							}
							$invoice_id=$this->Invoice->id;
							
							if (!empty($this->request->data['InvoiceProduct'])){
								foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
									if ($invoiceProduct['product_id']>0 && $invoiceProduct['product_quantity']>0){									
										//pr($invoiceProduct);
										// STEP II THE INVOICE PRODUCT
										$productArray=[];
										$productArray['InvoiceProduct']['invoice_id']=$invoice_id;
										$productArray['InvoiceProduct']['product_id']=$invoiceProduct['product_id'];
										$productArray['InvoiceProduct']['product_description']=$invoiceProduct['product_description'];
										$productArray['InvoiceProduct']['product_quantity']=$invoiceProduct['product_quantity'];
										$productArray['InvoiceProduct']['product_unit_price']=$invoiceProduct['product_unit_price'];
										$productArray['InvoiceProduct']['product_total_price']=$invoiceProduct['product_total_price'];
										$productArray['InvoiceProduct']['bool_iva']=$invoiceProduct['bool_iva'];
										$productArray['InvoiceProduct']['currency_id']=$this->request->data['Invoice']['currency_id'];
										$productArray['InvoiceProduct']['sales_order_product_id']=$invoiceProduct['sales_order_product_id'];
										$this->InvoiceProduct->create();
										if (!$this->InvoiceProduct->save($productArray)) {
											echo "Problema guardando los productos de la factura";
											pr($this->validateErrors($this->InvoiceProduct));
											throw new Exception();
										}
										
										// STEP III THE SALES ORDER PRODUCT
										if ($invoiceProduct['product_quantity']==$invoiceProduct['product_quantity_pending']){
											// product completely delivered
											$this->SalesOrderProduct->id=$invoiceProduct['sales_order_product_id'];
											$salesOrderProductArray=[];
											$salesOrderProductArray['SalesOrderProduct']['id']=$invoiceProduct['sales_order_product_id'];
											$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_DELIVERED;
											if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
												echo "Problema cambiando el estado de los productos de la orden de venta";
												pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
												throw new Exception();
											}
										}
										else {
											if (!$this->SalesOrderProduct->splitSalesOrderProduct($invoiceProduct['sales_order_product_id'], PRODUCT_STATUS_DELIVERED,$invoiceProduct['product_quantity'])){
												echo "Problema con la entrega parcial de los productos de la orden de venta";
												throw new Exception();
											}
											//echo "alrighty the sales order product has been successfully split!!<br/>";
										}
									}
								}								
							}
							//echo "now moving on with the sales orders... <br/>";
							// STEP IV THE INVOICE SALES ORDERS
							// REMARK 20160808
							// NOTICE THAT IF A SALESORDER GETS SELECTED WITHOUT ITS PRODUCT, THIS WOULD LEAD TO AN ASSOCIATION WITHOUT CORRESPONDING PRODUCTS
							// THIS MAY BE REFINED BY REMOVING THE SALESORDER IF NO PRODUCTS ARE PRESENT - ADDITIONAL CHECK
							$salesOrders=$this->request->data['Invoice']['sales_order_id'];
							//pr($salesOrders);
							if (!empty($salesOrders)){
								foreach ($salesOrders as $key=>$sales_order_id){
									$this->SalesOrder->recursive=-1;
									$relatedSalesOrder=$this->SalesOrder->find('first',array(
										'conditions'=>array('SalesOrder.id'=>$sales_order_id),
										'contain'=>array(
											'Quotation',
											'SalesOrderProduct',
										),
									));
									if (!empty($relatedSalesOrder)){
										$invoiceSalesOrderArray=[];
										$invoiceSalesOrderArray['InvoiceSalesOrder']['invoice_id']=$invoice_id;
										$invoiceSalesOrderArray['InvoiceSalesOrder']['sales_order_id']=$sales_order_id;
										$invoiceSalesOrderArray['InvoiceSalesOrder']['user_id']=$relatedSalesOrder['Quotation']['user_id'];
										$this->InvoiceSalesOrder->create();
										if (!$this->InvoiceSalesOrder->save($invoiceSalesOrderArray)) {
											echo "Problema guardando la relación entre facturas y ordenes de venta";
											pr($this->validateErrors($this->InvoiceSalesOrder));
											throw new Exception();
										}									
										
										$boolCompletelyDelivered=true;
										foreach ($relatedSalesOrder['SalesOrderProduct']as $salesOrderProduct){
											//pr($salesOrderProduct);
											if ($salesOrderProduct['product_quantity'] > 0 && $salesOrderProduct['sales_order_product_status_id']<PRODUCT_STATUS_DELIVERED){
												$boolCompletelyDelivered=false;
											}
										}
										
										$salesOrderArray=[];
										$this->SalesOrder->id=$sales_order_id;
										$salesOrderArray['SalesOrder']['id']=$sales_order_id;
										$salesOrderArray['SalesOrder']['bool_completely_delivered']=$boolCompletelyDelivered;
										if (!$this->SalesOrder->save($salesOrderArray)) {
											echo "Problema guardando el estado de entregado de la factura";
											pr($this->validateErrors($this->SalesOrder));
											throw new Exception();
										}
									}
								}								
							}
							if (!empty($this->request->data['InvoiceRemark']['remark_text'])){
								$invoiceRemark=$this->request->data['InvoiceRemark'];
								//pr($invoiceRemark);
								$invoiceRemarkArray=[];
								$invoiceRemarkArray['InvoiceRemark']['user_id']=$invoiceRemark['user_id'];
								$invoiceRemarkArray['InvoiceRemark']['invoice_id']=$invoice_id;
								$invoiceRemarkArray['InvoiceRemark']['remark_datetime']=date('Y-m-d H:i:s');
								$invoiceRemarkArray['InvoiceRemark']['remark_text']=$invoiceRemark['remark_text'];
								//$invoiceRemarkArray['InvoiceRemark']['working_days_before_reminder']=$invoiceRemark['working_days_before_reminder'];
								//$invoiceRemarkArray['InvoiceRemark']['reminder_date']=$invoiceRemark['reminder_date'];
								//$invoiceRemarkArray['InvoiceRemark']['action_type_id']=$invoiceRemark['action_type_id'];
								$this->InvoiceRemark->create();
								if (!$this->InvoiceRemark->save($invoiceRemarkArray)) {
									echo "Problema guardando las remarcas para la factura";
									pr($this->validateErrors($this->InvoiceRemark));
									throw new Exception();
								}
							}
						}
						
						$datasource->commit();
						//echo "everything nicely committed<br/>";
						$this->recordUserAction($this->Invoice->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se registró la factura número ".$this->request->data['Invoice']['invoice_code']);
						
						$this->Session->setFlash(__('The invoice has been saved.'),'default',array('class' => 'success'));
						if (!empty($this->request->data['saveandnext'])){
							return $this->redirect(array('action' => 'add'));
						}
						else{
							return $this->redirect(array('action' => 'view',$invoice_id));
						}
					} 
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('The invoice could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
					}
				}
			}
		}
		
		if ($boolSkipProductChecks){
			// in case bool_skip_production_checks is true=>just make a direct check on bool_completely_delivered and that's it
			$salesOrders=$this->SalesOrder->find('list',array(
				'conditions'=>array(				
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
				),
				'order'=>'SalesOrder.sales_order_code',
			));
			$quotationIds=$this->SalesOrder->find('list',array(
				'fields'=>array('SalesOrder.quotation_id'),
				'conditions'=>array(
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
				),
			));
		}
		else {
			$salesOrderIdsBasedOnProducts=$this->SalesOrder->SalesOrderProduct->find('list',array(
				'fields'=>array('SalesOrderProduct.sales_order_id'),
				'conditions'=>array(
					'SalesOrderProduct.sales_order_product_status_id'=>array(PRODUCT_STATUS_READY_FOR_DELIVERY),
				),
			));
			$salesOrders=$this->SalesOrder->find('list',array(
				'conditions'=>array(				
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
					'SalesOrder.id'=>$salesOrderIdsBasedOnProducts,
				),
				'order'=>'SalesOrder.sales_order_code',
			));
			$quotationIds=$this->SalesOrder->find('list',array(
				'fields'=>array('SalesOrder.quotation_id'),
				'conditions'=>array(
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
					'SalesOrder.id'=>$salesOrderIdsBasedOnProducts,
				),
			));
		}
		$this->set(compact('boolSkipProductChecks'));
		
		// 20160808 OVERRIDE SALES ORDERS AS THEY SHOULD NOT SHOW WHEN NO CLIENT IS SELECTED
		$salesOrders=[];
		
		$productChecks=[];
		$productChecks[]="Solo remitir productos con estado Listo para Entrega";
		$productChecks[]="Remitir todos productos de ordenes de venta autorizadas no entregadas";
		$this->set(compact('productChecks'));
		
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
		
		
		$currencies = $this->Invoice->Currency->find('list');
		$this->set(compact('salesOrders', 'clients', 'users', 'currencies'));
		
		$invoiceDate=date( "Y-m-d");
		$invoiceExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
		$exchangeRateInvoice=$invoiceExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateInvoice'));
		
		$actionTypes=$this->ActionType->find('list');
		$this->set(compact('actionTypes'));
		
		$users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));
    $vendorUsers = $this->User->find('list',[
			'conditions'=>[
				'User.id'=>$this->Auth->User('id'),
        'User.bool_active'=>true
			],
		]);
    $this->set(compact('vendorUsers'));
		
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
		$invoiceid=$id;
		$this->set(compact('invoiceid'));
		
		$this->loadModel('Product');
		$this->loadModel('InvoiceProduct');
		$this->loadModel('InvoiceSalesOrder');
		$this->loadModel('InvoiceRemark');
		$this->loadModel('Quotation');
		$this->loadModel('SalesOrder');
		$this->loadModel('SalesOrderProduct');
		
		$loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    
    $this->loadModel('User');
    $this->loadModel('UserPageRight');
		
    $canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllUsers'));
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Quotations','index');
    $this->set(compact('canSeeAllSalesExecutives'));
    
    $canSavePartialInvoice=$this->UserPageRight->hasUserPageRight('GRABAR_FACTURA_PARCIAL',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSavePartialInvoice'));
    
    $productsForInvoice=[];
		$selectedSalesOrderArray=[];
		if ($this->request->is(array('post', 'put'))) {
			$boolSkipProductChecks=$this->request->data['Invoice']['bool_skip_product_checks'];
			$selectedSalesOrderArray=$this->request->data['Invoice']['sales_order_id'];
			
			if (empty($this->request->data['refresh'])){				
				if (!$this->request->data['Invoice']['bool_annulled']){
					if (!empty($this->request->data['InvoiceProduct'])){
						foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
							//if ($invoiceProduct['product_quantity']>0){
								//pr($invoiceProduct);
								$productsForInvoice[]=$invoiceProduct;
							//}
						}
					}
				}
				//pr($productsForInvoice);
			
				$invoiceDateArray=$this->request->data['Invoice']['invoice_date'];
				//pr($quotationDateArray);
				$invoiceDateString=$invoiceDateArray['year'].'-'.$invoiceDateArray['month'].'-'.$invoiceDateArray['day'];
				$invoiceDate=date( "Y-m-d", strtotime($invoiceDateString));
				
				//$lastPaymentDateArray=$this->request->data['Invoice']['last_payment_date'];
				//$lastPaymentDateString=$lastPaymentDateArray['year'].'-'.$lastPaymentDateArray['month'].'-'.$lastPaymentDateArray['day'];
				//$lastPaymentDate=date( "Y-m-d", strtotime($lastPaymentDateString));
				
				$boolMultiplicationOK=true;
				if (!$this->request->data['Invoice']['bool_annulled']){
					foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
						if ($invoiceProduct['product_id']>0){
							$multiplicationDifference=abs($invoiceProduct['product_total_price']-$invoiceProduct['product_quantity']*$invoiceProduct['product_unit_price']);
							if ($multiplicationDifference>=0.01){
								$boolMultiplicationOK=false;
							};
						}
					}
				}
		
				if ($invoiceDateString>date('Y-m-d')){
					$this->Session->setFlash(__('La fecha de factura no puede estar en el futuro!  No se guardó la factura.'), 'default',array('class' => 'error-message'));
				}
				elseif (!$boolMultiplicationOK){
					$this->Session->setFlash(__('Occurrió un problema al multiplicar el precio unitario con la cantidad.  Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
				}
				elseif ($this->request->data['Invoice']['bool_annulled']&&empty($this->request->data['InvoiceRemark']['remark_text'])){
					$this->Session->setFlash(__('Al anular la factura se debe dejar una remarca.  No se guardó la factura.'), 'default',array('class' => 'error-message'));
				}
				else {	
					$datasource=$this->Invoice->getDataSource();
					$datasource->begin();
					try {
						// FIRST REMOVE THE OLD DATA
						//pr($this->request->data);
						$this->InvoiceProduct->recursive=-1;
						$previousInvoiceProducts=$this->InvoiceProduct->find('all',array(
							'fields'=>array(
								'InvoiceProduct.id',
								'InvoiceProduct.product_id',
								'InvoiceProduct.sales_order_product_id'
							),
							'conditions'=>array(
								'InvoiceProduct.invoice_id'=>$id,
							),
						));
						if (!empty($previousInvoiceProducts)){
							foreach ($previousInvoiceProducts as $previousInvoiceProduct){
								$this->InvoiceProduct->id=$previousInvoiceProduct['InvoiceProduct']['id'];
								$this->InvoiceProduct->delete($previousInvoiceProduct['InvoiceProduct']['id']);
								//
								//$this->SalesOrderProduct->recursive=-1;
								//$salesOrderProduct=$this->SalesOrderProduct->find('first',array(
								//	'conditions'=>array(
								//		'SalesOrderProduct.id'=>$previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'],
								//	),
								//));
								
								$calculatedSalesOrderProductStatus=$this->SalesOrderProduct->calculateSalesOrderProductStatus($previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'],$id);
								//echo "calculated status is ".$calculatedSalesOrderProductStatus."<br/>";
								$salesOrderProductArray=[];
								$this->SalesOrderProduct->id=$previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'];
								$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=$calculatedSalesOrderProductStatus;										
								if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
									echo "Problema cambiando el estado de los productos de la orden de venta";
									pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
									throw new Exception();
								}
							}
						}
						
						$this->InvoiceSalesOrder->recursive=-1;
						$previousInvoiceSalesOrders=$this->InvoiceSalesOrder->find('all',array(
							'fields'=>array(
								'InvoiceSalesOrder.id',
								'InvoiceSalesOrder.sales_order_id',
							),
							'conditions'=>array(
								'InvoiceSalesOrder.invoice_id'=>$id,
							),
						));
						if (!empty($previousInvoiceSalesOrders)){
							foreach ($previousInvoiceSalesOrders as $previousInvoiceSalesOrder){
								$this->SalesOrder->id=$previousInvoiceSalesOrder['InvoiceSalesOrder']['sales_order_id'];
								$salesOrderArray=[];
								$salesOrderArray['SalesOrder']['id']=$previousInvoiceSalesOrder['InvoiceSalesOrder']['sales_order_id'];
								$salesOrderArray['SalesOrder']['bool_completely_delivered']=false;
								if (!$this->SalesOrder->save($salesOrderArray)){
									echo "Problema cambiando el estado de la orden de venta";
									pr($this->validateErrors($this->SalesOrder));
									throw new Exception();
								}
								$this->InvoiceSalesOrder->id=$previousInvoiceSalesOrder['InvoiceSalesOrder']['id'];
								$this->InvoiceSalesOrder->delete($previousInvoiceSalesOrder['InvoiceSalesOrder']['id']);
							}
						}
						
						// SECOND CHECK FOR ANNULLED AND HANDLE CORRESPONDING CASE
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
							$invoice_id=$this->Invoice->id;
						}
						else {
							// STEP I THE INVOICE
							$this->Invoice->create();
							if (!$this->Invoice->save($this->request->data)) {
								echo "Problema guardando la factura";
								pr($this->validateErrors($this->Invoice));
								throw new Exception();
							}
							$invoice_id=$this->Invoice->id;
							
							if (!empty($this->request->data['InvoiceProduct'])){
								foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
									if ($invoiceProduct['product_id']>0 && $invoiceProduct['product_quantity']>0){	
										//pr($invoiceProduct);
										
										//20160812 SALES ORDER PRODUCTS CAN ACTUALLY BE AN ARRAY
										// THE DEFAULT SALES ORDER PRODUCT ID IS THE ONE FROM THE INVOICE ADD ONE
										// BUT SALES ORDER PRODUCTS ARE SUMMED TOGETHER TO GET THE PENDING QUANTITY
										// AND THIS SHOULD NOW BE UNRAVELLED
										$this->SalesOrderProduct->recursive=-1;
										$salesOrderProduct=$this->SalesOrderProduct->find('first',array(
											'conditions'=>array(
												'SalesOrderProduct.id'=>$invoiceProduct['sales_order_product_id'],
											),
										));
										$salesOrderProductQuantity=0;
										if (!empty($salesOrderProduct)){
											$salesOrderProductQuantity=$salesOrderProduct['SalesOrderProduct']['product_quantity'];
										}
										//echo "sales order product quantity for salesorderprduct ".$invoiceProduct['sales_order_product_id']." is ".$salesOrderProductQuantity."<br/>";
										
										if ($invoiceProduct['product_quantity']<=$salesOrderProductQuantity){
											// STEP IIA THE INVOICE PRODUCT IN THE CASE THERE IS ONLY ONE SALES ORDER PRODUCT
											// 20160812 OKIDOKI, THE IDENTIFIED SALES ORDER PRODUCT IS INDEED THE ONE AND WE PROCEED WITH THIS SALES ORDER PRODUCT ALONE
											$productArray=[];
											$productArray['InvoiceProduct']['invoice_id']=$invoice_id;
											$productArray['InvoiceProduct']['product_id']=$invoiceProduct['product_id'];
											$productArray['InvoiceProduct']['product_description']=$invoiceProduct['product_description'];
											$productArray['InvoiceProduct']['product_quantity']=$invoiceProduct['product_quantity'];
											$productArray['InvoiceProduct']['product_unit_price']=$invoiceProduct['product_unit_price'];
											$productArray['InvoiceProduct']['product_total_price']=$invoiceProduct['product_total_price'];
											$productArray['InvoiceProduct']['bool_iva']=$invoiceProduct['bool_iva'];
											$productArray['InvoiceProduct']['currency_id']=$this->request->data['Invoice']['currency_id'];
											$productArray['InvoiceProduct']['sales_order_product_id']=$invoiceProduct['sales_order_product_id'];
											$this->InvoiceProduct->create();
											if (!$this->InvoiceProduct->save($productArray)) {
												echo "Problema guardando los productos de la factura";
												pr($this->validateErrors($this->InvoiceProduct));
												throw new Exception();
											}
										
											// STEP IIIA THE SALES ORDER PRODUCT IN THE CASE THIS IS THE ONLY SALES ORDER PRODUCT
											if ($invoiceProduct['product_quantity']==$invoiceProduct['product_quantity_pending']){
												// product completely delivered
												$this->SalesOrderProduct->id=$invoiceProduct['sales_order_product_id'];
												
												$salesOrderProductArray=[];
												$salesOrderProductArray['SalesOrderProduct']['id']=$invoiceProduct['sales_order_product_id'];
												$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_DELIVERED;
												if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
													echo "Problema cambiando el estado de los productos de la orden de venta";
													pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
													throw new Exception();
												}
											}
											else {
												if (!$this->SalesOrderProduct->splitSalesOrderProduct($invoiceProduct['sales_order_product_id'], PRODUCT_STATUS_DELIVERED,$invoiceProduct['product_quantity'])){
													echo "Problema con la entrega parcial de los productos de la orden de venta";
													throw new Exception();
												}
												//echo "alrighty the sales order product has been successfully split!!<br/>";
											}
										}
										else {
											// STEP IIB THE INVOICE PRODUCT IN THE CASE THERE ARE SEVERAL SALES ORDER PRODUCTS NEED TO REACH THE QUANTITY
											
											$totalQuantityPending=$invoiceProduct['product_quantity'];
											// STEP IIB1 FIRST REGISTER THE PRODUCT CORRESPONDING WITH THE SALES ORDER PRODUCT FOR ITS FULL QUANTITY
											$productArray=[];
											$productArray['InvoiceProduct']['invoice_id']=$invoice_id;
											$productArray['InvoiceProduct']['product_id']=$invoiceProduct['product_id'];
											$productArray['InvoiceProduct']['product_description']=$invoiceProduct['product_description'];
											$productArray['InvoiceProduct']['product_quantity']=$salesOrderProductQuantity;
											$productArray['InvoiceProduct']['product_unit_price']=$invoiceProduct['product_unit_price'];
											$productArray['InvoiceProduct']['product_total_price']=$salesOrderProductQuantity*$invoiceProduct['product_unit_price'];
											$productArray['InvoiceProduct']['bool_iva']=$invoiceProduct['bool_iva'];
											$productArray['InvoiceProduct']['currency_id']=$this->request->data['Invoice']['currency_id'];
											$productArray['InvoiceProduct']['sales_order_product_id']=$invoiceProduct['sales_order_product_id'];
											$this->InvoiceProduct->create();
											if (!$this->InvoiceProduct->save($productArray)) {
												echo "Problema guardando los productos de la factura";
												pr($this->validateErrors($this->InvoiceProduct));
												throw new Exception();
											}
										
											// STEP IIIB1 THE SALES ORDER PRODUCT THAT WAS PRESELECTED SHOULD CHANGE STATUS TO DELIVERED
											$this->SalesOrderProduct->id=$invoiceProduct['sales_order_product_id'];
											$salesOrderProductArray=[];
											$salesOrderProductArray['SalesOrderProduct']['id']=$invoiceProduct['sales_order_product_id'];
											$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_DELIVERED;
											if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
												echo "Problema cambiando el estado de los productos de la orden de venta";
												pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
												throw new Exception();
											}
												
											$totalQuantityPending-=$salesOrderProductQuantity;
											
											$otherSalesOrderProductsAvailable=$this->SalesOrderProduct->find('all',array(
												'fields'=>array(
													'SalesOrderProduct.id',
													'SalesOrderProduct.product_quantity',
												),
												'conditions'=>array(
													'SalesOrderProduct.product_id'=>$invoiceProduct['product_id'],
													'SalesOrderProduct.product_description'=>$invoiceProduct['product_description'],
													'SalesOrderProduct.id !='=>$invoiceProduct['sales_order_product_id'],
												),
												'order'=>'SalesOrderProduct.sales_order_product_status_id DESC',
											));
										
											foreach ($otherSalesOrderProductsAvailable as $otherSalesOrderProductAvailable){
												if ($totalQuantityPending>0){	
													$salesOrderProductQuantity=$otherSalesOrderProductAvailable['SalesOrderProduct']['product_quantity'];
													if ($totalQuantityPending >=  $salesOrderProductQuantity){
														// STEP IIB2 FIRST REGISTER THE PRODUCT CORRESPONDING WITH THE SALES ORDER PRODUCT FOR ITS FULL QUANTITY
														$productArray=[];
														$productArray['InvoiceProduct']['invoice_id']=$invoice_id;
														$productArray['InvoiceProduct']['product_id']=$invoiceProduct['product_id'];
														$productArray['InvoiceProduct']['product_description']=$invoiceProduct['product_description'];
														$productArray['InvoiceProduct']['product_quantity']=$salesOrderProductQuantity;
														$productArray['InvoiceProduct']['product_unit_price']=$invoiceProduct['product_unit_price'];
														$productArray['InvoiceProduct']['product_total_price']=$salesOrderProductQuantity*$invoiceProduct['product_unit_price'];
														$productArray['InvoiceProduct']['bool_iva']=$invoiceProduct['bool_iva'];
														$productArray['InvoiceProduct']['currency_id']=$this->request->data['Invoice']['currency_id'];
														$productArray['InvoiceProduct']['sales_order_product_id']=$otherSalesOrderProductAvailable['SalesOrderProduct']['id'];
														$this->InvoiceProduct->create();
														if (!$this->InvoiceProduct->save($productArray)) {
															echo "Problema guardando los productos de la factura";
															pr($this->validateErrors($this->InvoiceProduct));
															throw new Exception();
														}
													
														// STEP IIIB2 THE SALES ORDER PRODUCT THAT WAS PRESELECTED SHOULD CHANGE STATUS TO DELIVERED
														$this->SalesOrderProduct->id=$otherSalesOrderProductAvailable['SalesOrderProduct']['id'];
														$salesOrderProductArray=[];
														$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_DELIVERED;
														if (!$this->SalesOrder->SalesOrderProduct->save($salesOrderProductArray)) {
															echo "Problema cambiando el estado de los productos de la orden de venta";
															pr($this->validateErrors($this->SalesOrder->SalesOrderProduct));
															throw new Exception();
														}
														$totalQuantityPending-=$salesOrderProductQuantity;
													}
													else {
														// STEP IIB3 FINALLY REGISTER THE PRODUCT CORRESPONDING WITH THE REMAINING total quantity pending
														$productArray=[];
														$productArray['InvoiceProduct']['invoice_id']=$invoice_id;
														$productArray['InvoiceProduct']['product_id']=$invoiceProduct['product_id'];
														$productArray['InvoiceProduct']['product_description']=$invoiceProduct['product_description'];
														$productArray['InvoiceProduct']['product_quantity']=$totalQuantityPending;
														$productArray['InvoiceProduct']['product_unit_price']=$invoiceProduct['product_unit_price'];
														$productArray['InvoiceProduct']['product_total_price']=$totalQuantityPending*$invoiceProduct['product_unit_price'];
														$productArray['InvoiceProduct']['bool_iva']=$invoiceProduct['bool_iva'];
														$productArray['InvoiceProduct']['currency_id']=$this->request->data['Invoice']['currency_id'];
														$productArray['InvoiceProduct']['sales_order_product_id']=$otherSalesOrderProductAvailable['SalesOrderProduct']['id'];
														$this->InvoiceProduct->create();
														if (!$this->InvoiceProduct->save($productArray)) {
															echo "Problema guardando los productos de la factura";
															pr($this->validateErrors($this->InvoiceProduct));
															throw new Exception();
														}
														
														if (!$this->SalesOrderProduct->splitSalesOrderProduct($otherSalesOrderProductAvailable['SalesOrderProduct']['id'], PRODUCT_STATUS_DELIVERED,$invoiceProduct['product_quantity'])){
															echo "Problema con la entrega parcial de los productos de la orden de venta";
															throw new Exception();
														}													
													}
												}
											}
										}
									}
								}								
							}
							//echo "now moving on with the sales orders... <br/>";
							// STEP IV THE INVOICE SALES ORDERS
							// REMARK 20160808
							// NOTICE THAT IF A SALESORDER GETS SELECTED WITHOUT ITS PRODUCT, THIS WOULD LEAD TO AN ASSOCIATION WITHOUT CORRESPONDING PRODUCTS
							// THIS MAY BE REFINED BY REMOVING THE SALESORDER IF NO PRODUCTS ARE PRESENT - ADDITIONAL CHECK
							$salesOrders=$this->request->data['Invoice']['sales_order_id'];
							//pr($salesOrders);
							if (!empty($salesOrders)){
								foreach ($salesOrders as $key=>$sales_order_id){
									$relatedSalesOrder=$this->SalesOrder->find('first',[
										'conditions'=>['SalesOrder.id'=>$sales_order_id],
										'contain'=>[
											'Quotation',
											'SalesOrderProduct',
										],
									]);
									if (!empty($relatedSalesOrder)){
										$invoiceSalesOrderArray=[];
										$invoiceSalesOrderArray['InvoiceSalesOrder']['invoice_id']=$invoice_id;
										$invoiceSalesOrderArray['InvoiceSalesOrder']['sales_order_id']=$sales_order_id;
										$invoiceSalesOrderArray['InvoiceSalesOrder']['user_id']=$relatedSalesOrder['Quotation']['user_id'];
										$this->InvoiceSalesOrder->create();
										if (!$this->InvoiceSalesOrder->save($invoiceSalesOrderArray)) {
											echo "Problema guardando la relación entre facturas y ordenes de venta";
											pr($this->validateErrors($this->InvoiceSalesOrder));
											throw new Exception();
										}									
										
										$boolCompletelyDelivered=true;
										foreach ($relatedSalesOrder['SalesOrderProduct']as $salesOrderProduct){
											//pr($salesOrderProduct);
											if ($salesOrderProduct['product_quantity'] > 0 && $salesOrderProduct['sales_order_product_status_id']<PRODUCT_STATUS_DELIVERED){
												$boolCompletelyDelivered=false;
											}
										}
										
										$salesOrderArray=[];
										$this->SalesOrder->id=$sales_order_id;
										$salesOrderArray['SalesOrder']['id']=$sales_order_id;
										$salesOrderArray['SalesOrder']['bool_completely_delivered']=$boolCompletelyDelivered;
										if (!$this->SalesOrder->save($salesOrderArray)) {
											echo "Problema guardando el estado de entregado de la factura";
											pr($this->validateErrors($this->SalesOrder));
											throw new Exception();
										}	
									}
								}								
							}
							if (!empty($this->request->data['InvoiceRemark']['remark_text'])){
								$invoiceRemark=$this->request->data['InvoiceRemark'];
								//pr($invoiceRemark);
								$invoiceRemarkArray=[];
								$invoiceRemarkArray['InvoiceRemark']['user_id']=$invoiceRemark['user_id'];
								$invoiceRemarkArray['InvoiceRemark']['invoice_id']=$invoice_id;
								$invoiceRemarkArray['InvoiceRemark']['remark_datetime']=date('Y-m-d H:i:s');
								$invoiceRemarkArray['InvoiceRemark']['remark_text']=$invoiceRemark['remark_text'];
								//$invoiceRemarkArray['InvoiceRemark']['working_days_before_reminder']=$invoiceRemark['working_days_before_reminder'];
								//$invoiceRemarkArray['InvoiceRemark']['reminder_date']=$invoiceRemark['reminder_date'];
								//$invoiceRemarkArray['InvoiceRemark']['action_type_id']=$invoiceRemark['action_type_id'];
								$this->InvoiceRemark->create();
								if (!$this->InvoiceRemark->save($invoiceRemarkArray)) {
									echo "Problema guardando las remarcas para la factura";
									pr($this->validateErrors($this->InvoiceRemark));
									throw new Exception();
								}
							}
						}
						
						$datasource->commit();
						$this->recordUserAction($this->Invoice->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se editó la factura número ".$this->request->data['Invoice']['invoice_code']);
						
						$this->Session->setFlash(__('La factura se editó exitósamente.'),'default',array('class' => 'success'));
						if (!empty($this->request->data['saveandnext'])){
							return $this->redirect(array('action' => 'add'));
						}
						else{
							return $this->redirect(array('action' => 'view',$invoice_id));
						}
					} 
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('No se podía guardar la factura.'), 'default',array('class' => 'error-message'));
					}
				}
			}
		}
		else {
			$options = array(
				'conditions' => array(
					'Invoice.id'=> $id,
				),
				'contain'=>array(
					'Client'=>array(
						'Quotation'=>array(
							'fields'=>'Quotation.id',
						),
					),
					'InvoiceProduct'=>array(
						'Product',
					),
					'InvoiceSalesOrder'=>array(
						'SalesOrder'=>array(
							'SalesOrderProduct',
						),
					),
				),
			);
			$this->request->data =$invoice= $this->Invoice->find('first', $options);
			
			// $productsForInvoice=$this->request->data['InvoiceProduct'];
			$productsForInvoice=[];
			foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
				$boolProductInArray=false;
				if (!empty($productsForInvoice)){
					//echo "invoice product<br/>";
					//pr($invoiceProduct);
					for ($pfi=0;$pfi<count($productsForInvoice);$pfi++){
						//echo "product for invoice<br/>";
						//pr($productsForInvoice[$pfi]);
						if (!$boolProductInArray){
							if (($productsForInvoice[$pfi]['product_id']==$invoiceProduct['product_id'])&&($productsForInvoice[$pfi]['product_description']==$invoiceProduct['product_description'])){
								//echo "summing the quantity<br/>";
								$productsForInvoice[$pfi]['product_quantity']+=$invoiceProduct['product_quantity'];
								//pr($productsForInvoice[$pfi]);
								$boolProductInArray=true;
							}
						}
					}
				}
				if (!$boolProductInArray){
					$productsForInvoice[]=$invoiceProduct;
					//echo "product for invoice added<br/>";
				}
			}
			//pr($productsForInvoice);
			
			foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
				$selectedSalesOrderArray[]=$invoiceSalesOrder['sales_order_id'];
			}
		}
		
		$this->set(compact('selectedSalesOrderArray'));	
		//pr($selectedSalesOrderArray);
		
		// FIRST GET THE CLIENT LIST
		$boolSkipProductChecks=$this->request->data['Invoice']['bool_skip_product_checks'];
		$quotationIds=[];
		if ($boolSkipProductChecks){
			// in case bool_skip_production_checks is true=>just make a direct check on bool_completely_delivered and that's it
			$quotationIds=$this->SalesOrder->find('list',array(
				'fields'=>array('SalesOrder.quotation_id'),
				'conditions'=>array(
					'OR'=>array(
						array(
							'SalesOrder.bool_completely_delivered'=>false,
							'SalesOrder.bool_authorized'=>true,
						),
						array(
							'SalesOrder.id'=>$selectedSalesOrderArray,
						),
					),
				),
			));
		}
		else {
			$salesOrderIdsBasedOnProducts=$this->SalesOrder->SalesOrderProduct->find('list',array(
				'fields'=>array('SalesOrderProduct.sales_order_id'),
				'conditions'=>array(
					'SalesOrderProduct.sales_order_product_status_id'=>array(PRODUCT_STATUS_READY_FOR_DELIVERY),
				),
			));
			$quotationIds=$this->SalesOrder->find('list',array(
				'fields'=>array('SalesOrder.quotation_id'),
				'conditions'=>array(
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
					'OR'=>array(
						array(
							'SalesOrder.id'=>$salesOrderIdsBasedOnProducts,
						),
						array(
							'SalesOrder.id'=>$selectedSalesOrderArray,
						),
					),	
				),
			));
		}
		//pr($quotationIds);
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
		$this->set(compact('clients'));
		
		// SECOND GET THE SALES ORDERS FOR THE SELECTED CLIENT
		//20160811 IT IS NOT ENOUGH TO JUST GET THE SALES ORDERS; ONE MUST ALSO GET THE SALESORDERS THAT WERE COMPLETED WITHIN THE INVOICE
		$quotationIdsForClient=$this->Quotation->find('list',array(
			'fields'=>'Quotation.id',
			'conditions'=>array(
				'Quotation.client_id'=>$this->request->data['Invoice']['client_id'],
			),
		));
		$salesOrderConditions=array(
			'OR'=>array(
				array(
					'SalesOrder.bool_completely_delivered'=>false,
					'SalesOrder.bool_authorized'=>true,
					'SalesOrder.quotation_id'=>$quotationIdsForClient,
				),
				array(
					'SalesOrder.id'=>$selectedSalesOrderArray,
				),
			),
		);
		$this->SalesOrder->recursive=-1;
		if (!$this->request->data['Invoice']['bool_skip_product_checks']){
			$salesOrderIdsBasedOnProducts=$this->SalesOrder->SalesOrderProduct->find('list',array(
				'fields'=>array('SalesOrderProduct.sales_order_id'),
				'conditions'=>array(
					'SalesOrderProduct.sales_order_product_status_id >='=>array(PRODUCT_STATUS_READY_FOR_DELIVERY),
				),
			));
			$salesOrderConditions[]=array('SalesOrder.id'=>$salesOrderIdsBasedOnProducts);
		}		
		$salesOrders=$this->SalesOrder->find('list',array(
			'fields'=>array('SalesOrder.id','SalesOrder.sales_order_code',),
			'conditions'=>$salesOrderConditions,
			'order'=>'SalesOrder.sales_order_code ASC',
		));
		$this->set(compact('salesOrders'));	
		
		// THIRD GET THE PRODUCTS FOR THE SALES ORDER TO GET THE TOTALS
		$salesOrderProducts=[];
		if (!empty($selectedSalesOrderArray)){
			//pr($selectedSalesOrderArray);
			$this->SalesOrderProduct->recursive=-1;
			$salesOrderProductConditions=array(
				'SalesOrderProduct.sales_order_id'=>$selectedSalesOrderArray,
			);
			if (!$this->request->data['Invoice']['bool_skip_product_checks']){
				$salesOrderProductConditions[]=array(
					'SalesOrderProduct.sales_order_product_status_id >='=>array(PRODUCT_STATUS_READY_FOR_DELIVERY),
				);
			}
			$this->SalesOrderProduct->virtualFields['total_quantity']=0;
			$salesOrderProducts=$this->SalesOrderProduct->find('all',array(
				'fields'=>array(
					'SalesOrderProduct.product_id',
					'SalesOrderProduct.product_description',
					'SUM(SalesOrderProduct.product_quantity) AS SalesOrderProduct__total_quantity',
					'SalesOrderProduct.product_unit_price',
					'SalesOrderProduct.currency_id',
					'SalesOrderProduct.bool_iva',
				),
				'conditions'=>$salesOrderProductConditions,
				'group'=>array(
					'SalesOrderProduct.product_id',
					'SalesOrderProduct.product_description',
					'SalesOrderProduct.sales_order_id',
				),
			));
			for ($sop=0;$sop<count($salesOrderProducts);$sop++){
				$firstSalesOrderProductOrderedByStatus=$this->SalesOrderProduct->find('first',array(
					'fields'=>array('SalesOrderProduct.id'),
					'conditions'=>array(
						'SalesOrderProduct.product_id'=>$salesOrderProducts[$sop]['SalesOrderProduct']['product_id'],
						'SalesOrderProduct.product_description'=>$salesOrderProducts[$sop]['SalesOrderProduct']['product_description'],
					),
					'order'=>'SalesOrderProduct.sales_order_product_status_id DESC',
				));
				if (!empty($firstSalesOrderProductOrderedByStatus)){
					$salesOrderProducts[$sop]['SalesOrderProduct']['id']=$firstSalesOrderProductOrderedByStatus['SalesOrderProduct']['id'];
				}
			}
		}
		$this->set(compact('salesOrderProducts'));	
		//pr($salesOrderProducts);
		
		// FOURTH SET THE PENDING TOTAL QUANTITY
		if (!empty($salesOrderProducts)){
			//echo "salesorder product count is ".count($salesOrderProducts)."<br/>";
			//pr($productsForInvoice);
			foreach ($salesOrderProducts as $salesOrderProduct){
				//pr($salesOrderProduct);
				//echo "invoice product count is ".count($productsForInvoice)."<br/>";
				$boolProductHandled=false;
				for ($ip=0;$ip<count($productsForInvoice);$ip++){
					//pr ($productsForInvoice[$ip]);
					if (($salesOrderProduct['SalesOrderProduct']['product_id']==$productsForInvoice[$ip]['product_id'])&&($salesOrderProduct['SalesOrderProduct']['product_description']==$productsForInvoice[$ip]['product_description'])){
						//echo "salesorderproduct<br/>";
						//pr($salesOrderProduct);
						//echo "invoiceproduct<br/>";
						//pr ($productsForInvoice[$ip]);
						//echo "handling the product for ".$salesOrderProduct['SalesOrderProduct']['id']."<br/>";
						$boolProductHandled=true;
						$productsForInvoice[$ip]['product_quantity_pending']=$salesOrderProduct['SalesOrderProduct']['total_quantity'];
						//pr ($productsForInvoice[$ip]);
					}
				}
				if (!$boolProductHandled){
					$newProductForInvoice=[];
					//$newProductForInvoice['id']=0;
					//$newProductForInvoice['invoice_id']=0;
					$newProductForInvoice['product_id']=$salesOrderProduct['SalesOrderProduct']['product_id'];
					$newProductForInvoice['product_description']=$salesOrderProduct['SalesOrderProduct']['product_description'];
					if ($salesOrderProduct['SalesOrderProduct']['product_unit_price']==$this->request->data['Invoice']['currency_id']){
						$newProductForInvoice['product_unit_price']=$salesOrderProduct['SalesOrderProduct']['product_unit_price'];
					}
					else {
						if ($this->request->data['Invoice']['currency_id']==CURRENCY_CS){
							$newProductForInvoice['product_unit_price']=round($salesOrderProduct['SalesOrderProduct']['product_unit_price']*$this->request->data['Invoice']['exchange_rate'],2);
						}
						else {
							$newProductForInvoice['product_unit_price']=round($salesOrderProduct['SalesOrderProduct']['product_unit_price']/$this->request->data['Invoice']['exchange_rate'],2);
						}
					}
					$newProductForInvoice['product_quantity_pending']=$salesOrderProduct['SalesOrderProduct']['total_quantity'];
					$newProductForInvoice['product_quantity']=0;
					$newProductForInvoice['product_total_price']=0;
					
					$newProductForInvoice['currency_id']=$this->request->data['Invoice']['currency_id'];
					$newProductForInvoice['bool_iva']=$salesOrderProduct['SalesOrderProduct']['bool_iva'];
					$newProductForInvoice['sales_order_product_id']=$salesOrderProduct['SalesOrderProduct']['id'];
			
					$relatedProduct=$this->Product->find('first',array(
						'conditions'=>array(
							'Product.id'=>$salesOrderProduct['SalesOrderProduct']['product_id'],
						),
					));
					$newProductForInvoice['Product']=$relatedProduct['Product'];
					$productsForInvoice[]=$newProductForInvoice;
				}
			
				//pr($productsForInvoice);
			}
		}
		//echo "invoice product count is ".count($productsForInvoice)."<br/>";
		$this->set(compact('productsForInvoice'));	
		//pr($productsForInvoice);
		
		
    $productIdsListForInvoice=[];
    foreach($productsForInvoice as $invoiceProduct){
      //pr($invoiceProduct);
      if (!in_array($invoiceProduct['product_id'],$productIdsListForInvoice)){
        $productIdsListForInvoice[]=$invoiceProduct['product_id'];
      }
    }
    $products=$this->Product->find('list',[
      'conditions'=>[
        'OR'=>[
          ['Product.bool_active'=>true],
          ['Product.id'=>$productIdsListForInvoice]
        ],
      ],
      'order'=>'Product.name'
    ]);
    $this->set(compact('products'));	
		
		$this->set(compact('boolSkipProductChecks'));
		
		$productChecks=[];
		$productChecks[]="Solo remitir productos con estado Listo para Entrega";
		$productChecks[]="Remitir todos productos de ordenes de venta autorizadas no entregadas";
		$this->set(compact('productChecks'));
		
		//$users = $this->Invoice->User->find('list');
		$currencies = $this->Invoice->Currency->find('list');
		$this->set(compact('salesOrders', 'clients', 'users', 'currencies'));
		
		//$products=$this->Product->find('list',array('order'=>'Product.name'));
		//$this->set(compact('products'));
		
		$invoiceDateAsString=$this->Invoice->deconstruct('invoice_date',$this->request->data['Invoice']['invoice_date']);
		$invoiceExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDateAsString);
		$exchangeRateInvoice=$invoiceExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateInvoice'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$this->loadModel('ActionType');
		$actionTypes=$this->ActionType->find('list');
		$this->set(compact('actionTypes'));
		
		$this->loadModel('User');
		$users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));
		
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
*/  
  public function editarReferencia($id = null) {
		if (!$this->Invoice->exists($id)) {
			throw new NotFoundException(__('Invalid invoice'));
		}
		
		$this->loadModel('Product');
		$this->loadModel('InvoiceProduct');
		$this->loadModel('InvoiceSalesOrder');
		$this->loadModel('InvoiceRemark');
		$this->loadModel('Quotation');
		$this->loadModel('SalesOrder');
		$this->loadModel('SalesOrderProduct');
    
    $this->loadModel('ActionType');
		
		$this->loadModel('User');
		
		$loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    
    
    if ($this->request->is(['post', 'put'])) {
			$invoiceDateArray=$this->request->data['Invoice']['invoice_date'];
      $invoiceDateString=$invoiceDateArray['year'].'-'.$invoiceDateArray['month'].'-'.$invoiceDateArray['day'];
      $invoiceDate=date( "Y-m-d", strtotime($invoiceDateString));
      
      $datasource=$this->Invoice->getDataSource();
      $datasource->begin();
      try {
        $this->Invoice->id=$id;
        if (!$this->Invoice->save($this->request->data['Invoice'])) {
          echo "Problema editando la referencia de la factura";
          pr($this->validateErrors($this->Invoice));
          throw new Exception();
        }          
        $datasource->commit();
        $this->recordUserAction($this->Invoice->id,null,null);
        $this->recordUserActivity($this->Session->read('User.username'),"Se editó la referencia de factura número ".$this->request->data['Invoice']['invoice_code']);
        
        $this->Session->setFlash(__('La referencia de la factura se editó exitósamente.'),'default',['class' => 'success']);
        return $this->redirect(['action' => 'detalle',$id]);
        
      } 
      catch(Exception $e){
        $datasource->rollback();
        pr($e);
        $this->Session->setFlash('No se podía editar la referencia de la factura.', 'default',['class' => 'error-message']);
      }
      
		}
		else {
			$options = [
				'conditions' => [
					'Invoice.id'=> $id,
				],
				'contain'=>[
					'Client'=>[
						'Quotation'=>[
							'fields'=>'Quotation.id',
						],
					],
					'InvoiceProduct'=>[
						'Product',
					],
					'InvoiceSalesOrder'=>[
						'SalesOrder'=>[
							'SalesOrderProduct',
						],
					],
				],
			];
			$this->request->data =$invoice= $this->Invoice->find('first', $options);
			
			// $productsForInvoice=$this->request->data['InvoiceProduct'];
			$productsForInvoice=[];
			foreach ($this->request->data['InvoiceProduct'] as $invoiceProduct){
				$boolProductInArray=false;
				if (!empty($productsForInvoice)){
					for ($pfi=0;$pfi<count($productsForInvoice);$pfi++){
						if (!$boolProductInArray){
							if (($productsForInvoice[$pfi]['product_id']==$invoiceProduct['product_id'])&&($productsForInvoice[$pfi]['product_description']==$invoiceProduct['product_description'])){
								$productsForInvoice[$pfi]['product_quantity']+=$invoiceProduct['product_quantity'];
								$boolProductInArray=true;
							}
						}
					}
				}
				if (!$boolProductInArray){
					$productsForInvoice[]=$invoiceProduct;
				}
			}
			//pr($productsForInvoice);
			
			foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
				$selectedSalesOrderArray[]=$invoiceSalesOrder['sales_order_id'];
			}
		}
		
    $this->set(compact('productsForInvoice'));	
		$this->set(compact('selectedSalesOrderArray'));	
    //pr($selectedSalesOrderArray);
    $clients=$this->Invoice->Client->find('list',[
			'conditions'=>[
				'Client.id'=>$this->request->data['Invoice']['client_id'],
			],
			'order'=>'Client.name',
		]);
		$this->set(compact('clients'));
		
		$salesOrderConditions=[
      'SalesOrder.id'=>$selectedSalesOrderArray,
    ];
		$salesOrders=$this->SalesOrder->find('list',[
			'fields'=>['SalesOrder.id','SalesOrder.sales_order_code'],
			'conditions'=>$salesOrderConditions,
			'order'=>'SalesOrder.sales_order_code ASC',
		]);
		
    $productIdsListForInvoice=[];
    foreach($productsForInvoice as $invoiceProduct){
      //pr($invoiceProduct);
      if (!in_array($invoiceProduct['product_id'],$productIdsListForInvoice)){
        $productIdsListForInvoice[]=$invoiceProduct['product_id'];
      }
    }
    $products=$this->Product->find('list',[
      'conditions'=>[
        'OR'=>[
          ['Product.bool_active'=>true],
          ['Product.id'=>$productIdsListForInvoice]
        ],
      ],
      'order'=>'Product.name'
    ]);
    $this->set(compact('products'));	
		
		
		$currencies = $this->Invoice->Currency->find('list');
		$this->set(compact('salesOrders', 'clients', 'users', 'currencies'));
		
		$invoiceDateAsString=$this->Invoice->deconstruct('invoice_date',$this->request->data['Invoice']['invoice_date']);
		$exchangeRateInvoice=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDateAsString);
		$this->set(compact('exchangeRateInvoice'));
		
		$actionTypes=$this->ActionType->find('list');
		$this->set(compact('actionTypes'));
		
    $users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));
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
		
		$this->loadModel('InvoiceProduct');
		$this->loadModel('InvoiceSalesOrder');
		$this->loadModel('SalesOrder');
		$this->loadModel('SalesOrderProduct');
		$this->request->allowMethod('post', 'delete');
		
		$this->Invoice->recursive=-1;
		$invoice=$this->Invoice->find('first',array(
			'conditions'=>array(
				'Invoice.id'=>$id,
			),
			'contain'=>array(
				'InvoiceProduct',
				'InvoiceSalesOrder'=>array(
					'SalesOrder',
				),
			)
		));

		$flashMessage="";
		$boolDeletionAllowed=true;
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó la factura.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'detalle',$id));
		}
		else {
			$datasource=$this->Invoice->getDataSource();
			$datasource->begin();	
			try {
				if (!empty($invoice['InvoiceSalesOrder'])){
					foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
					
						$this->SalesOrder->id=$invoiceSalesOrder['SalesOrder']['id'];
						$salesOrderArray=[];
						$salesOrderArray['SalesOrder']['bool_completely_delivered']=false;
						if (!$this->SalesOrder->save($salesOrderArray)){
							echo "Problema cambiando el estado de la orden de venta";
							pr($this->validateErrors($this->SalesOrder));
							throw new Exception();
						}
					}
				}
				
				$this->InvoiceProduct->recursive=-1;
				$previousInvoiceProducts=$this->InvoiceProduct->find('all',array(
					'fields'=>array(
						'InvoiceProduct.id',
						'InvoiceProduct.product_id',
						'InvoiceProduct.sales_order_product_id'
					),
					'conditions'=>array(
						'InvoiceProduct.invoice_id'=>$id,
					),
				));
				if (!empty($previousInvoiceProducts)){
					foreach ($previousInvoiceProducts as $previousInvoiceProduct){
						$this->InvoiceProduct->id=$previousInvoiceProduct['InvoiceProduct']['id'];
						$this->InvoiceProduct->delete($previousInvoiceProduct['InvoiceProduct']['id']);
						
						$calculatedSalesOrderProductStatus=$this->SalesOrderProduct->calculateSalesOrderProductStatus($previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'],$id);
						//echo "calculated status is ".$calculatedSalesOrderProductStatus."<br/>";
						$salesOrderProductArray=[];
						$this->SalesOrderProduct->id=$previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'];
						$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=$calculatedSalesOrderProductStatus;										
						if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
							echo "Problema cambiando el estado de los productos de la orden de venta";
							pr($this->validateErrors($this->SalesOrderProduct));
							throw new Exception();
						}
					}
				}
						
				$this->InvoiceSalesOrder->recursive=-1;
				$previousInvoiceSalesOrders=$this->InvoiceSalesOrder->find('all',array(
					'fields'=>array(
						'InvoiceSalesOrder.id',
					),
					'conditions'=>array(
						'InvoiceSalesOrder.invoice_id'=>$id,
					),
				));
				if (!empty($previousInvoiceSalesOrders)){
					foreach ($previousInvoiceSalesOrders as $previousInvoiceSalesOrder){
						$this->InvoiceSalesOrder->id=$previousInvoiceSalesOrder['InvoiceSalesOrder']['id'];
						$this->InvoiceSalesOrder->delete($previousInvoiceSalesOrder['InvoiceSalesOrder']['id']);
					}
				}
				
				if (!$this->Invoice->delete($id)) {
					echo "Problema al eliminar la factura";
					//pr($this->validateErrors($this->Invoice));
					throw new Exception();
				}
						
				$datasource->commit();
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=[];
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$invoice['Invoice']['id'];
				$deletionArray['Deletion']['reference']=$invoice['Invoice']['invoice_code'];
				$deletionArray['Deletion']['type']='Invoice';
				$this->Deletion->save($deletionArray);
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó la factura número ".$invoice['Invoice']['invoice_code']);
						
				$this->Session->setFlash(__('Se eliminó la factura.'),'default',['class' => 'success']);				
				return $this->redirect(array('action' => 'resumen'));
			}
			catch(Exception $e){
				$datasource->rollback();
				//pr($e);
				$this->Session->setFlash(__('No se podía eliminar la factura.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'detalle',$id));
			}
		}
		if ($this->Invoice->delete()) {
			$this->Session->setFlash(__('The invoice has been deleted.'));
		} else {
			$this->Session->setFlash(__('The invoice could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'resumen'));
	}
	
	public function annul($id = null) {
		$this->loadModel('InvoiceProduct');
		$this->loadModel('InvoiceSalesOrder');
		$this->loadModel('SalesOrder');
		$this->loadModel('SalesOrderProduct');
			
		$this->Invoice->id = $id;
		if (!$this->Invoice->exists()) {
			throw new NotFoundException(__('Factura inválida'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$datasource=$this->Invoice->getDataSource();
		$datasource->begin();
		try {
			//pr($this->request->data);
			$this->InvoiceProduct->recursive=-1;
			$previousInvoiceProducts=$this->InvoiceProduct->find('all',array(
				'fields'=>array(
					'InvoiceProduct.id',
					'InvoiceProduct.product_id',
					'InvoiceProduct.sales_order_product_id'
				),
				'conditions'=>array(
					'InvoiceProduct.invoice_id'=>$id,
				),
			));
			if (!empty($previousInvoiceProducts)){
				foreach ($previousInvoiceProducts as $previousInvoiceProduct){
					$this->InvoiceProduct->id=$previousInvoiceProduct['InvoiceProduct']['id'];
					$this->InvoiceProduct->delete($previousInvoiceProduct['InvoiceProduct']['id']);
					
					$calculatedSalesOrderProductStatus=$this->SalesOrderProduct->calculateSalesOrderProductStatus($previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'],$id);
					//echo "calculated status is ".$calculatedSalesOrderProductStatus."<br/>";
					$salesOrderProductArray=[];
					$this->SalesOrderProduct->id=$previousInvoiceProduct['InvoiceProduct']['sales_order_product_id'];
					$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=$calculatedSalesOrderProductStatus;										
					if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
						echo "Problema cambiando el estado de los productos de la orden de venta";
						pr($this->validateErrors($this->SalesOrderProduct));
						throw new Exception();
					}
				}
			}
						
			$this->InvoiceSalesOrder->recursive=-1;
			$previousInvoiceSalesOrders=$this->InvoiceSalesOrder->find('all',array(
				'fields'=>array(
					'InvoiceSalesOrder.id',
					'InvoiceSalesOrder.sales_order_id',
				),
				'conditions'=>array(
					'InvoiceSalesOrder.invoice_id'=>$id,
				),
			));
			if (!empty($previousInvoiceSalesOrders)){
				foreach ($previousInvoiceSalesOrders as $previousInvoiceSalesOrder){
					$this->SalesOrder->id=$previousInvoiceSalesOrder['InvoiceSalesOrder']['sales_order_id'];
					$salesOrderArray=[];
					$salesOrderArray['SalesOrder']['id']=$previousInvoiceSalesOrder['InvoiceSalesOrder']['sales_order_id'];
					$salesOrderArray['SalesOrder']['bool_completely_delivered']=false;
					if (!$this->SalesOrder->save($salesOrderArray)){
						echo "Problema cambiando el estado de la orden de venta";
						pr($this->validateErrors($this->SalesOrder));
						throw new Exception();
					}

					$this->InvoiceSalesOrder->id=$previousInvoiceSalesOrder['InvoiceSalesOrder']['id'];
					$this->InvoiceSalesOrder->delete($previousInvoiceSalesOrder['InvoiceSalesOrder']['id']);
				}
			}
			
			$this->Invoice->recursive=-1;
			$invoice=$this->Invoice->find('first',array(
				'conditions'=>array(
					'Invoice.id'=>$id,
				),
				'contain'=>array(
					'InvoiceProduct',
					'InvoiceSalesOrder'=>array(
						'SalesOrder',
					),
				)
			));
			if (!empty($invoice['InvoiceSalesOrder'])){
				foreach ($invoice['InvoiceSalesOrder'] as $invoiceSalesOrder){
				
					$this->SalesOrder->id=$invoiceSalesOrder['SalesOrder']['id'];
					$salesOrderArray=[];
					$salesOrderArray['SalesOrder']['bool_completely_delivered']=false;
					if (!$this->SalesOrder->save($salesOrderArray)){
						echo "Problema cambiando el estado de la orden de venta";
						pr($this->validateErrors($this->SalesOrder));
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
			$this->Session->setFlash(__('La orden de venta se anuló.'),'default',['class' => 'success']);
		}
		catch(Exception $e){
			$this->Session->setFlash(__('La orden de venta no se podía anular.'), 'default',array('class' => 'error-message'));
		}
		
		return $this->redirect(array('action' => 'resumen'));
	}
	
	public function verReporteFacturasPorEjecutivo(){
		$this->loadModel('InvoiceSalesOrder');
    
    $this->loadModel('Currency');
    $this->loadModel('ExchangeRate');
		
    $this->loadModel('User');
    $this->loadModel('UserPageRight');
		
		$loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    
		$canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSeeAllUsers'));
    
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSeeAllSalesExecutives'));
		
		if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives){
			$userId=0;
		}
    else {
      $userId=$loggedUserId;
    }
    $currencyId=CURRENCY_USD;
			
		if ($this->request->is('post')) {
      //pr($this->request->data);
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
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			//echo "retrieving values from session<br/>";
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
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('userId','currencyId'));
		
		$userConditions=[];
		if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers){ 
      $selectedUsers=$this->User->getActiveVendorAllUsers($userId);
    }
    else {
      $selectedUsers=$this->User->getActiveVendorAllVendors($userId);
    }
		//pr($selectedUsers);
		$this->Invoice->recursive=-1;
		for ($u=0;$u<count($selectedUsers);$u++){
			$this->InvoiceSalesOrder->recursive=-1;
			$invoiceIds=$this->InvoiceSalesOrder->find('list',[
				'fields'=>['InvoiceSalesOrder.invoice_id'],
				'conditions'=>[
					'InvoiceSalesOrder.user_id'=>$selectedUsers[$u]['User']['id']
				],
			]);
			
			$invoices=$this->Invoice->find('all',[
				'conditions'=>[
					'Invoice.invoice_date >='=>$startDate,
					'Invoice.invoice_date <'=>$endDatePlusOne,
					'Invoice.id'=>$invoiceIds,
				],
				'contain'=>[
					'Client',
					//'Contact',
					'Currency',
				],
			]);
			
			if (!empty($invoices)){
				for ($i=0;$i<count($invoices);$i++){
					
          $invoices[$i]['Invoice']['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoices[$i]['Invoice']['invoice_date']);
				}
			}
			$selectedUsers[$u]['Invoices']=$invoices;
			
		}
		
		if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers){
      $users=$this->User->getActiveVendorAllUserList();
    }
    elseif ($canSeeAllSalesExecutives) {
      $users=$this->User->getActiveSalesUsersOnlyList();
    }
    else {
      $users=$this->User->getUserList($loggedUserId);
    }
		$this->set(compact('users','selectedUsers'));	
		
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));	
		
	}

	public function guardarReporteFacturasPorEjecutivo() {
		$exportData=$_SESSION['reporteFacturasPorEjecutivo'];
		$this->set(compact('exportData'));
	}

  public function reporteVentasPorCliente(){
    $this->loadModel('Client');
    $this->loadModel('ExchangeRate');
    
    $this->loadModel('User');
    $this->loadModel('Currency');
    
    $this->Invoice->recursive=-1;
    $this->Client->recursive=-1;
    
    $this->User->recursive=-1;
    
    $userId=0;
		$currencyId=CURRENCY_CS;
    
    define('BOOL_AND',0);
    define('BOOL_OR',1);
    $boolOrConditions=[
      BOOL_AND=>'Condiciones inclusivas',
      BOOL_OR=>'Condiciones exclusivas',
    ];
    $this->set(compact('boolOrConditions'));
    $cutoffAmount=100000;
    $frequencyNumber=6;
    $boolOrConditionId=0;
    
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
      
      $cutoffAmount=$this->request->data['Report']['cutoff_amount'];
      $frequencyNumber=$this->request->data['Report']['frequency_number'];
      $boolOrConditionId=$this->request->data['Report']['bool_or_condition_id'];
		}
		else if (!empty($_SESSION['startDateYear']) && !empty($_SESSION['endDateYear'])){
			$startDate=$_SESSION['startDateYear'];
			$endDate=$_SESSION['endDateYear'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
		}
		else{
			//$startDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-1 month" ) );
			$startDateThisYear = date("Y-01-01");
      $startDate= date( "Y-m-d", strtotime( date("Y-01-01")."-1 years" ) );
			$endDateThisYear=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDateThisYearPlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
      $endDatePlusOne=date("Y-m-d",strtotime(date("Y-01-01")));
			$endDate= date( "Y-m-d", strtotime( date("Y-01-01")."-1 days" ) );
		}
		$_SESSION['startDateYear']=$startDate;
		$_SESSION['endDateYear']=$endDate;
		
		$_SESSION['currencyId']=$currencyId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('userId','currencyId'));
    $this->set(compact('cutoffAmount'));
    $this->set(compact('frequencyNumber'));
    $this->set(compact('boolOrConditionId'));
		
		$userConditions=[];
		if ($userId>0){
			$userConditions['User.id']=$userId;
		}
		$selectedUsers=$this->User->find('all',array(
			'fields'=>['User.id','User.username'],
			'conditions'=>$userConditions,
		));
		
		$monthArray=$this->Invoice->getMonthArray($startDate,$endDate);
		//pr($monthArray);
    $clients=$this->Client->find('all',[
      'fields'=>['Client.id,Client.name,Client.bool_vip'],
      'order'=>'name ASC'
    ]);
    //pr($clients);
    $this->set(compact('clients'));
    $clientList=[];
    foreach ($clients as $client){
      $clientList[$client['Client']['id']]=[
        'bool_vip'=>$client['Client']['bool_vip'],
        'name'=>$client['Client']['name'],
      ];
    }
    //pr($clientList);
    $this->set(compact('clientList'));
    $salesArray=[];
		$totalSale=0;
    
    $salesArray[0]['totalUSD']=0;
    $salesArray[0]['totalCS']=0;
    $salesArray[0]['client_id']=0;
    
    //initialize sales array
     //first parameter is for clients, 0 means all
    for ($p=0;$p<count($monthArray);$p++){
      $salesArray[0]['sales'][$p]['totalUSD']=0;
      $salesArray[0]['sales'][$p]['totalCS']=0;
    }
    for ($c=0;$c<count($clients);$c++){      
      $salesArray[$c+1]['totalUSD']=0;
      $salesArray[$c+1]['totalCS']=0;
      $salesArray[$c+1]['frequency']=0;
      $salesArray[$c+1]['client_id']=$clients[$c]['Client']['id'];
      for ($p=0;$p<count($monthArray);$p++){
        $salesArray[$c]['sales'][$p]['period']=$monthArray[$p]['period'];
        $salesArray[$c]['sales'][$p]['totalUSD']=0;
        $salesArray[$c]['sales'][$p]['totalCS']=0;
        
      }
    }  
    //pr($salesArray);
    
    $invoiceConditions=[];      
    if ($userId>0){
      $this->loadModel('InvoiceSalesOrder');
      $this->InvoiceSalesOrder->recursive=-1;
      $invoiceIds=$this->InvoiceSalesOrder->find('list',[
        'fields'=>['InvoiceSalesOrder.invoice_id'],
        'conditions'=>['InvoiceSalesOrder.user_id'=>$userId]
      ]);
      $invoiceConditions['Invoice.id']=$invoiceIds;
    }
    
    for ($c=0;$c<count($clients);$c++){
      $clientId=$clients[$c]['Client']['id'];
      $invoiceConditions['Invoice.client_id']=$clients[$c]['Client']['id'];
      $totalForClient=0;
      
      for ($p=0;$p<count($monthArray);$p++){
        $period=$monthArray[$p]['period'];
        
        $invoiceConditions['Invoice.invoice_date >=']=$monthArray[$p]['sale_start_date'];
        $invoiceConditions['Invoice.invoice_date <']=$monthArray[$p]['sale_end_date_plus_one'];
      
        $invoices=$this->Invoice->find('all',[
          'fields'=>['Invoice.price_subtotal,Invoice.invoice_date,Invoice.currency_id'],
          'conditions'=>$invoiceConditions,
        ]);
        $subtotalCS=0;
        $subtotalUSD=0;
        if (!empty($invoices)){
          $salesArray[$c+1]['frequency']++;
          
          for ($i=0;$i<count($invoices);$i++){
            $invoiceDate=$invoices[$i]['Invoice']['invoice_date'];
            $exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
            $invoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
            if ($invoices[$i]['Invoice']['currency_id']==CURRENCY_CS){
              $subtotalCS+=$invoices[$i]['Invoice']['price_subtotal'];
              $subtotalUSD+=round($invoices[$i]['Invoice']['price_subtotal']/$invoices[$i]['Invoice']['exchange_rate'],2);
            }
            elseif ($invoices[$i]['Invoice']['currency_id']==CURRENCY_USD){
              $subtotalUSD+=$invoices[$i]['Invoice']['price_subtotal'];
              $subtotalCS+=round($invoices[$i]['Invoice']['price_subtotal']*$invoices[$i]['Invoice']['exchange_rate'],2);
            }
          }
          
          $salesArray[$c+1]['sales'][$p]['period']=$period;
          $salesArray[$c+1]['sales'][$p]['totalUSD']=$subtotalUSD;
          $salesArray[$c+1]['sales'][$p]['totalCS']=$subtotalCS;
          $salesArray[$c+1]['totalUSD']+=$subtotalUSD;
          $salesArray[$c+1]['totalCS']+=$subtotalCS;
          
          $salesArray[0]['sales'][$p]['totalUSD']+=$subtotalUSD;
          $salesArray[0]['sales'][$p]['totalCS']+=$subtotalCS;
          $salesArray[0]['totalUSD']+=$subtotalUSD;
          $salesArray[0]['totalCS']+=$subtotalCS;
        }  
      }
    }
		//pr($salesArray);
		usort($salesArray,array($this,'sortByTotalForClient'));
		//pr($salesArray);
		
		$this->set(compact('clients','monthArray','salesArray'));
    
    $users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));	
		
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));	
  }
  
  public function sortByTotalForClient($a,$b ){ 
	  if( $a['totalCS'] == $b['totalCS'] ){ return 0 ; } 
	  return ($a['totalCS'] < $b['totalCS']) ? 1 : -1;
	} 
  
  public function guardarReporteVentasPorCliente(){
    $exportData=$_SESSION['reporteVentasPorCliente'];
		$this->set(compact('exportData'));
  }
  
  public function reporteVentasAnualesPorCliente(){
    $this->loadModel('Client');
    $this->loadModel('ExchangeRate');
    
    $this->loadModel('User');
    $this->loadModel('Currency');
    
    $this->Invoice->recursive=-1;
    $this->Client->recursive=-1;
    
    $this->User->recursive=-1;
    
    $userId=0;
		$currencyId=CURRENCY_CS;
    
    $startDate= date( "Y-m-d", strtotime( date("2016-01-01") ) );
    $endDatePlusOne=date("Y-m-d",strtotime(date("Y-01-01")."+1 years"));
    $endDate= date( "Y-m-d", strtotime( date("Y-12-31")."-1 days" ) );
    
    define('BOOL_AND',0);
    define('BOOL_OR',1);
    $boolOrConditions=[
      BOOL_AND=>'Condiciones inclusivas',
      BOOL_OR=>'Condiciones exclusivas',
    ];
    $this->set(compact('boolOrConditions'));
    $cutoffAmount=100000;
    $frequencyNumber=6;
    $boolOrConditionId=0;
    
		if ($this->request->is('post')) {
			$userId=$this->request->data['Report']['user_id'];
			$currencyId=$this->request->data['Report']['currency_id'];
      
      $cutoffAmount=$this->request->data['Report']['cutoff_amount'];
      $frequencyNumber=$this->request->data['Report']['frequency_number'];
      $boolOrConditionId=$this->request->data['Report']['bool_or_condition_id'];
		}
		else if (!empty($_SESSION['startDateYear']) && !empty($_SESSION['endDateYear'])){
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
		}
		
    
		$_SESSION['startDateYear']=$startDate;
		$_SESSION['endDateYear']=$endDate;
		
		$_SESSION['currencyId']=$currencyId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('userId','currencyId'));
    $this->set(compact('cutoffAmount'));
    $this->set(compact('frequencyNumber'));
    $this->set(compact('boolOrConditionId'));
		
		$userConditions=[];
		if ($userId>0){
			$userConditions['User.id']=$userId;
		}
		$selectedUsers=$this->User->find('all',array(
			'fields'=>['User.id','User.username'],
			'conditions'=>$userConditions,
		));
		//$monthArray=$this->Invoice->getMonthArray($startDate,$endDate);
		$yearArray=$this->Invoice->getYearArray(2016,date('Y'));
		//pr($yearArray);
    $clients=$this->Client->find('all',[
      'fields'=>['Client.id,Client.name,Client.bool_vip'],
      'order'=>'name ASC'
    ]);
    //pr($clients);
    $this->set(compact('clients'));
    $clientList=[];
    foreach ($clients as $client){
      $clientList[$client['Client']['id']]=[
        'bool_vip'=>$client['Client']['bool_vip'],
        'name'=>$client['Client']['name'],
      ];
    }
    //pr($clientList);
    $this->set(compact('clientList'));
    $salesArray=[];
		$totalSale=0;
    
    $salesArray[0]['totalUSD']=0;
    $salesArray[0]['totalCS']=0;
    $salesArray[0]['client_id']=0;
    
    //initialize sales array
     //first parameter is for clients, 0 means all
    for ($p=0;$p<count($yearArray);$p++){
      $salesArray[0]['sales'][$p]['totalUSD']=0;
      $salesArray[0]['sales'][$p]['totalCS']=0;
    }
    for ($c=0;$c<count($clients);$c++){      
      $salesArray[$c+1]['totalUSD']=0;
      $salesArray[$c+1]['totalCS']=0;
      $salesArray[$c+1]['frequency']=0;
      $salesArray[$c+1]['client_id']=$clients[$c]['Client']['id'];
      for ($p=0;$p<count($yearArray);$p++){
        $salesArray[$c]['sales'][$p]['period']=$yearArray[$p]['period'];
        $salesArray[$c]['sales'][$p]['totalUSD']=0;
        $salesArray[$c]['sales'][$p]['totalCS']=0;
        
      }
    }  
    //pr($salesArray);
    
    $invoiceConditions=[];      
    if ($userId>0){
      $this->loadModel('InvoiceSalesOrder');
      $this->InvoiceSalesOrder->recursive=-1;
      $invoiceIds=$this->InvoiceSalesOrder->find('list',[
        'fields'=>['InvoiceSalesOrder.invoice_id'],
        'conditions'=>['InvoiceSalesOrder.user_id'=>$userId]
      ]);
      $invoiceConditions['Invoice.id']=$invoiceIds;
    }
    
    for ($c=0;$c<count($clients);$c++){
      $clientId=$clients[$c]['Client']['id'];
      $invoiceConditions['Invoice.client_id']=$clients[$c]['Client']['id'];
      $totalForClient=0;
      
      for ($p=0;$p<count($yearArray);$p++){
        $period=$yearArray[$p]['period'];
        
        $invoiceConditions['Invoice.invoice_date >=']=$yearArray[$p]['sale_start_date'];
        $invoiceConditions['Invoice.invoice_date <']=$yearArray[$p]['sale_end_date_plus_one'];
      
        $invoices=$this->Invoice->find('all',[
          'fields'=>['Invoice.price_subtotal,Invoice.invoice_date,Invoice.currency_id'],
          'conditions'=>$invoiceConditions,
        ]);
        $subtotalCS=0;
        $subtotalUSD=0;
        if (!empty($invoices)){
          $salesArray[$c+1]['frequency']++;
          
          for ($i=0;$i<count($invoices);$i++){
            $invoiceDate=$invoices[$i]['Invoice']['invoice_date'];
            $exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
            $invoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
            if ($invoices[$i]['Invoice']['currency_id']==CURRENCY_CS){
              $subtotalCS+=$invoices[$i]['Invoice']['price_subtotal'];
              $subtotalUSD+=round($invoices[$i]['Invoice']['price_subtotal']/$invoices[$i]['Invoice']['exchange_rate'],2);
            }
            elseif ($invoices[$i]['Invoice']['currency_id']==CURRENCY_USD){
              $subtotalUSD+=$invoices[$i]['Invoice']['price_subtotal'];
              $subtotalCS+=round($invoices[$i]['Invoice']['price_subtotal']*$invoices[$i]['Invoice']['exchange_rate'],2);
            }
          }
          
          $salesArray[$c+1]['sales'][$p]['period']=$period;
          $salesArray[$c+1]['sales'][$p]['totalUSD']=$subtotalUSD;
          $salesArray[$c+1]['sales'][$p]['totalCS']=$subtotalCS;
          $salesArray[$c+1]['totalUSD']+=$subtotalUSD;
          $salesArray[$c+1]['totalCS']+=$subtotalCS;
          
          $salesArray[0]['sales'][$p]['totalUSD']+=$subtotalUSD;
          $salesArray[0]['sales'][$p]['totalCS']+=$subtotalCS;
          $salesArray[0]['totalUSD']+=$subtotalUSD;
          $salesArray[0]['totalCS']+=$subtotalCS;
        }  
      }
    }
		//pr($salesArray);
		usort($salesArray,array($this,'sortByTotalForClient'));
		//pr($salesArray);
		
		$this->set(compact('clients','yearArray','salesArray'));
    
    $users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));	
		
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));	
  }
  
  public function guardarReporteVentasAnualesPorCliente(){
    $exportData=$_SESSION['reporteVentasAnualesPorCliente'];
		$this->set(compact('exportData'));
  }
  
	public function cuentasPorCobrar() {
		$this->loadModel('ExchangeRate');
		$this->loadModel('InvoiceSalesOrder');
		
		$this->Invoice->recursive = -1;
		$currencyId=CURRENCY_USD;
		
		$userRoleId = $this->Auth->User('role_id');
		
		if ($this->request->is('post')) {
			if (!empty($this->request->data['Invoice'])){
				//pr($this->request->data);
				for ($i=0;$i<count($this->request->data['Invoice']);$i++){
					$submitButtonName="Invoice_".$i."_submit";
					if (!empty($this->request->data[$submitButtonName])){
						if (empty($this->request->data['Invoice'][$i]['cash_receipt_code'])){
							$this->Session->setFlash(__('El recibo de caja debe estar presente para establecer una factura como pagado.  No se guardó la factura como pagada'), 'default',array('class' => 'error-message'));
						}
						else {
							$invoice=$this->Invoice->read(null,$this->request->data['Invoice'][$i]['id']);
							$this->Invoice->id=$this->request->data['Invoice'][$i]['id'];
							$invoiceDataArray['Invoice']['id']=$this->request->data['Invoice'][$i]['id'];
							$invoiceDataArray['Invoice']['cash_receipt_code']=$this->request->data['Invoice'][$i]['cash_receipt_code'];
							$invoiceDataArray['Invoice']['last_payment_date']=$this->request->data['Invoice'][$i]['last_payment_date'];
							$invoiceDataArray['Invoice']['bool_paid']=true;
							//pr($invoiceDataArray);

							if (!$this->Invoice->save($invoiceDataArray)) {
								$this->Session->setFlash(__('No se podía guardar la factura como pagada.  Intente de nuevo.'), 'default',array('class' => 'error-message'));
								pr($this->validateErrors($this->Invoice));
							}
							else {
								$this->Session->setFlash(__('Se guardó la factura '.$invoice['Invoice']['invoice_code'].' como pagada.'), 'default',['class' => 'success']);
/*
								//return $this->redirect(array('action' => 'cuentasPorCobrar'));
								//return $this->redirect($this->request->here);
								//return $this->redirect(array('controller'=>'invoices','action' => 'cuentasPorCobrar'));
								//return $this->redirect(Router::url(null, true));
								//return $this->redirect(Router::url( $this->request->here, true ));
								//return $this->redirect($_SERVER["REQUEST_URI"]);
*/
							}

						}
					}
				}

			}
		
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
			
			if (!empty($this->request->data['Report'])){
				$currencyId=$this->request->data['Report']['currency_id'];
			}
		}
		
		$_SESSION['currencyId']=$currencyId;
		
		//$this->set(compact('startDate','endDate','user_id'));
		$this->set(compact('currencyId'));
		$conditions=array(
			'Invoice.bool_annulled'=>false,
			'Invoice.bool_paid'=>false,
		);
		//pr($this->Auth->User);
		//echo "user id is ".$this->Auth->User['id']."<br/>";
		//echo "user id in session is ".$_SESSION['userId']."<br/>";
		//echo "userrole is ".$userRoleId."<br/>";
		
		if ($userRoleId!=ROLE_ADMIN&&$userRoleId!=ROLE_ASSISTANT) { 
			$invoiceIdsForUser=$this->InvoiceSalesOrder->find('list',array(
				'fields'=>array('InvoiceSalesOrder.invoice_id'),
				'conditions'=>array(
					'InvoiceSalesOrder.user_id'=>$_SESSION['userId'],
				),
			));
			//pr($invoiceIdsForUser);
			$conditions[]=array('Invoice.id'=>$invoiceIdsForUser);
		}
		//pr($conditions);
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

		$invoices = $this->Paginator->paginate('Invoice');
		//pr($invoices);
		if (!empty($invoices)){
			for ($i=0;$i<count($invoices);$i++){
				$invoiceDate=$invoices[$i]['Invoice']['invoice_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
				$invoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
			}
		}
		$this->set(compact('invoices'));
		//pr($invoices);
		$this->loadModel('Client');
		$clients=$this->Client->find('all',array(
			'contain'=>array(
				'Invoice'=>array(
					'fields'=>array(
						'Invoice.invoice_date',
						'Invoice.currency_id',
						'Invoice.price_subtotal',
						'Invoice.price_total',
						//'Invoice.amount_paid',
					),
					'conditions' => $conditions,					
				),
			),
		));
		//pr($clients);
		$clientesPorCobrar=[];
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
							//$clientesPorCobrar[$clientId]['saldo']+=($invoice['price_total']-$invoice['amount_paid']);
							$clientesPorCobrar[$clientId]['saldo']+=$invoice['price_total'];
						}
						else {
							//$clientesPorCobrar[$clientId]['saldo']+=round(($invoice['price_total']-$invoice['amount_paid'])/$rate,2);
							$clientesPorCobrar[$clientId]['saldo']+=round($invoice['price_total']/$rate,2);
						}
					}
					else {
						if ($invoice['currency_id']==CURRENCY_CS){
							//$clientesPorCobrar[$clientId]['saldo']+=($invoice['price_total']-$invoice['amount_paid']);
							$clientesPorCobrar[$clientId]['saldo']+=$invoice['price_total'];
						}
						else {
							//$clientesPorCobrar[$clientId]['saldo']+=round(($invoice['price_total']-$invoice['amount_paid'])*$rate,2);
							$clientesPorCobrar[$clientId]['saldo']+=round($invoice['price_total']*$rate,2);
						}
					}
				}
			}
		}
		//pr($clientesPorCobrar);
		usort($clientesPorCobrar,array($this,'sortBySaldo'));
		//pr($clientesPorCobrar);
		$this->set(compact('clientesPorCobrar'));
		
		$qualifiedInvoiceIds=$this->Invoice->find('list',array(
			'fields'=>array('Invoice.id'),
			'conditions' => $conditions,					
		));
		
		$this->loadModel('User');
		$vendors=$this->User->find('all',array(
			'contain'=>array(
				'InvoiceSalesOrder'=>array(
					'conditions'=>array(
						'InvoiceSalesOrder.invoice_id'=>$qualifiedInvoiceIds,
					),
					'Invoice'=>array(
						'fields'=>array(
							'Invoice.invoice_date',
							'Invoice.currency_id',
							'Invoice.price_subtotal',
							'Invoice.price_total',
						),
					),
				),
			),
		));
		//pr($vendors);
		
		$vendedoresPorCobrar=[];
		foreach ($vendors as $vendor){
			if (!empty($vendor['InvoiceSalesOrder'])){
				//pr($vendor['InvoiceSalesOrder']);
				$vendorId=$vendor['User']['id'];
				$vendedoresPorCobrar[$vendorId]['id']=$vendor['User']['id'];
				$vendedoresPorCobrar[$vendorId]['name']=$vendor['User']['username'];
				$vendedoresPorCobrar[$vendorId]['saldo']=0;
				foreach ($vendor['InvoiceSalesOrder'] as $invoiceSalesOrder){
					$invoice=$invoiceSalesOrder['Invoice'];
					$invoiceDate=$invoice['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					$rate=$exchangeRate['ExchangeRate']['rate'];
					if ($currencyId==CURRENCY_USD){
						if ($invoice['currency_id']==CURRENCY_USD){
							$vendedoresPorCobrar[$vendorId]['saldo']+=$invoice['price_total'];
						}
						else {
							$vendedoresPorCobrar[$vendorId]['saldo']+=round($invoice['price_total']/$rate,2);
						}
					}
					else {
						if ($invoice['currency_id']==CURRENCY_CS){
							$vendedoresPorCobrar[$vendorId]['saldo']+=$invoice['price_total'];
						}
						else {
							$vendedoresPorCobrar[$vendorId]['saldo']+=round($invoice['price_total']*$rate,2);
						}
					}
				}
			}
		}
		usort($vendedoresPorCobrar,array($this,'sortBySaldo'));
		
		$this->set(compact('vendedoresPorCobrar'));
		
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

	public function resumenRecibos() {
		$this->loadModel('ExchangeRate');
		$this->loadModel('InvoiceSalesOrder');
		
		$this->Invoice->recursive = -1;
		
		$currencyId=CURRENCY_USD;
		$userId=$this->Auth->User('id');
		
		$userRoleId = $this->Auth->User('role_id');
		
		if ($this->request->is('post')) {
			$currencyId=$this->request->data['Report']['currency_id'];
			$userId=$this->request->data['Report']['user_id'];
			
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
			//if ($this->Session->check('userId')){
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
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('currencyId'));
		$this->set(compact('userId'));
		
		$receiptConditions=array(
			'Invoice.bool_annulled'=>false,
			'Invoice.bool_paid'=>true,
			'Invoice.last_payment_date >='=>$startDate,
			'Invoice.last_payment_date <'=>$endDatePlusOne,
		);
		
		if ($userRoleId!=ROLE_ADMIN&&$userRoleId!=ROLE_ASSISTANT) { 
			$invoiceIdsForUser=$this->InvoiceSalesOrder->find('list',array(
				'fields'=>array('InvoiceSalesOrder.invoice_id'),
				'conditions'=>array(
					'InvoiceSalesOrder.user_id'=>$_SESSION['userId'],
				),
			));
			//pr($invoiceIdsForUser);
			$receiptConditions[]=array('Invoice.id'=>$invoiceIdsForUser);
		}
		else {
			if (!empty($userId)){
				$invoiceIdsForUser=$this->InvoiceSalesOrder->find('list',array(
					'fields'=>array('InvoiceSalesOrder.invoice_id'),
					'conditions'=>array(
						'InvoiceSalesOrder.user_id'=>$_SESSION['userId'],
					),
				));
				$receiptConditions[]=array('Invoice.id'=>$invoiceIdsForUser);
			}
		}
		//pr($conditions);
		$receiptCount=	$this->Invoice->find('count', array(
			'fields'=>array('Invoice.id'),
			'conditions' => $receiptConditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $receiptConditions,
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
			'order'=>['Invoice.last_payment_date'=>'DESC','Invoice.cash_receipt_code'=>'DESC','Invoice.invoice_code'=>'DESC'],
			'limit'=>($receiptCount!=0?$receiptCount:1),
		);

		$receipts = $this->Paginator->paginate('Invoice');
		//pr($receipts);
		if (!empty($receipts)){
			for ($i=0;$i<count($receipts);$i++){
				$invoiceDate=$receipts[$i]['Invoice']['invoice_date'];
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
				$receipts[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
			}
		}
		$this->set(compact('receipts'));
		//pr($receipts);
		
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$this->loadModel('User');
		$users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));
	}
	
	public function guardarResumenRecibos() {
		$exportData=$_SESSION['resumenRecibos'];
		$this->set(compact('exportData'));
	}
	
	public function comisionesPorVendedor() {
    $this->loadModel('Currency');
		$this->loadModel('ExchangeRate');
    
		$this->loadModel('SalesObjective');
		$this->loadModel('ExchangeRate');
		$this->loadModel('InvoiceSalesOrder');
    
		$this->Invoice->recursive = -1;
		
    $this->loadModel('User');
		
		$userRoleId = $this->Auth->User('role_id');
		$this->set(compact('userRoleId'));
		
		//echo "user id in session is ".$_SESSION['userId']."<br/>";
		$userId=$this->Auth->User('id');
		//echo "user id in session is ".$userId."<br/>";
		$currencyId=CURRENCY_USD;
		
		$startDate = date("Y-m-01");
		$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
		$boolSetFromPostData=false;
		if ($this->request->is('post')){
			//pr($this->request->data);
			if(!empty($this->request->data['Report'])) {
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
		
		$userConditions=[
			'User.role_id !='=>ROLE_DEPARTMENT_BOSS,
			'User.role_id !='=>ROLE_OPERATOR,
		];
		//if ($userId>0) { 
		//	$userConditions[]=array('User.id'=>$userId);
		//}
		$this->loadModel('User');
		$this->User->recursive=-1;
		$selectedUsers=$this->User->find('all',[
			'conditions'=>$userConditions,
			'order'=>'User.username',
		]);
		
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
						foreach ($this->request->data['User'][$selectedUser['User']['id']]['Invoice'] as $invoice){
							$this->Invoice->id=$invoice['invoiceid'];
							$invoiceData=[];
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
					
						$this->Session->setFlash('Se guardaron las comisiones para las facturas', 'default',['class' => 'success']);
						return $this->redirect($this->referer());
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);					
						$this->Session->setFlash('No se podían guardar las comisiones', 'default',['class' => 'error-message']);
					}
				}
			}
		}
		//pr($selectedUsers);
		for ($u=0;$u<count($selectedUsers);$u++){
			$this->InvoiceSalesOrder->recursive=-1;
			$invoiceIds=$this->InvoiceSalesOrder->find('list',[
				'fields'=>['InvoiceSalesOrder.invoice_id'],
				'conditions'=>[
					'InvoiceSalesOrder.user_id'=>$selectedUsers[$u]['User']['id']
				],
			]);
			$invoiceConditions=[
				'Invoice.invoice_date >='=>$startDate,
				'Invoice.invoice_date <'=>$endDatePlusOne,
				'Invoice.bool_annulled'=>false,
				'Invoice.id'=>$invoiceIds,
			];
			
			$invoiceCount=	$this->Invoice->find('count', [
				'fields'=>['Invoice.id'],
				'conditions' => $invoiceConditions,
			]);
			
			$this->Paginator->settings = [
				'fields'=>[
					'Invoice.id',
					'Invoice.invoice_code',
					'Invoice.invoice_date',
					'Invoice.price_subtotal',
					'Invoice.bool_credit',
					//'Invoice.amount_paid',
					'Invoice.bool_paid',
					'Invoice.percentage_commission',
					'Invoice.amount_commission',
				],
				'conditions' => $invoiceConditions,
				'contain'=>[				
					'Client'=>[
						'fields'=>[
							'Client.id','Client.name',
						],
					],
					'Currency',
					'InvoiceSalesOrder'=>[	
						'SalesOrder'=>[
							'fields'=>[
								'SalesOrder.id','SalesOrder.sales_order_code',
							],
							'Quotation'=>[
								//'Contact',
							],
						],
					],
				],
				'order'=>['Invoice.invoice_date'=>'ASC'],
				'limit'=>($invoiceCount!=0?$invoiceCount:1),
			];
			$invoices = $this->Paginator->paginate('Invoice');
			
			$cashInvoices=[];
			$creditInvoices=[];
			
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
			$subTotalCS=0;
			$commissionCS=0;
			$subTotalUSD=0;
			$commissionUSD=0;
			if (!empty($creditInvoices)){
				//echo "processing credit invoices<br/>";
				for ($i=0;$i<count($creditInvoices);$i++){
					//pr($creditInvoices[$i]);
					$invoiceDate=$creditInvoices[$i]['Invoice']['invoice_date'];
					$creditInvoices[$i]['Invoice']['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
					
					if ($creditInvoices[$i]['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$creditInvoices[$i]['Invoice']['price_subtotal'];
						$commissionCS+=$creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($creditInvoices[$i]['Invoice']['price_subtotal']/$creditInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['percentage_commission']/$creditInvoices[$i]['Invoice']['exchange_rate'],2);
					}
					elseif ($creditInvoices[$i]['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$creditInvoices[$i]['Invoice']['price_subtotal'];
						$commissionUSD+=$creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionCS+=round($creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['percentage_commission']*$creditInvoices[$i]['Invoice']['exchange_rate'],2);
					}
				}
				
			}
			$selectedUsers[$u]['creditInvoices']=$creditInvoices;
			$selectedUsers[$u]['credit_subtotal_CS']=$subTotalCS;
			$selectedUsers[$u]['credit_commission_CS']=$commissionCS;
			$selectedUsers[$u]['credit_subtotal_USD']=$subTotalUSD;
			$selectedUsers[$u]['credit_commission_USD']=$commissionUSD;
			
			$subTotalCS=0;
			$commissionCS=0;
			$subTotalUSD=0;
			$commissionUSD=0;
			if (!empty($cashInvoices)){
				//echo "processing cash invoices<br/>";
				for ($i=0;$i<count($cashInvoices);$i++){
					//pr($cashInvoices[$i]);
					$invoiceDate=$cashInvoices[$i]['Invoice']['invoice_date'];
					$cashInvoices[$i]['Invoice']['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
					
					if ($cashInvoices[$i]['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$cashInvoices[$i]['Invoice']['price_subtotal'];
						$commissionCS+=$cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($cashInvoices[$i]['Invoice']['price_subtotal']/$cashInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['percentage_commission']/$cashInvoices[$i]['Invoice']['exchange_rate'],2);
					}
					elseif ($cashInvoices[$i]['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$cashInvoices[$i]['Invoice']['price_subtotal'];
						$commissionUSD+=$cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionCS+=round($cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['percentage_commission']*$cashInvoices[$i]['Invoice']['exchange_rate'],2);
					}
				}
			}
			$selectedUsers[$u]['cashInvoices']=$cashInvoices;
			$selectedUsers[$u]['cash_subtotal_CS']=$subTotalCS;
			$selectedUsers[$u]['cash_commission_CS']=$commissionCS;
			$selectedUsers[$u]['cash_subtotal_USD']=$subTotalUSD;
			$selectedUsers[$u]['cash_commission_USD']=$commissionUSD;
			//pr($selectedUsers[$u]);
			
			// RECOVERED INVOICES
			$recoveredInvoiceConditions=[
				'Invoice.bool_annulled'=>false,
				'Invoice.id'=>$invoiceIds,
				'Invoice.bool_paid'=>true,
				'Invoice.invoice_date <'=>$startDate,
				'Invoice.last_payment_date >='=>$startDate,
				'Invoice.last_payment_date <'=>$endDatePlusOne,
				
				//'OR'=>[
					//['Invoice.bool_paid'=>false],
					//['Invoice.amount_paid <='=>'Invoice.price_total'],
				//)
			];
			$invoiceCount=	$this->Invoice->find('count', [
				'fields'=>['Invoice.id'],
				'conditions' => $recoveredInvoiceConditions,
			]);
			
			$this->Paginator->settings = [
				'conditions' => $recoveredInvoiceConditions,
				'contain'=>[				
					'Client'=>[
						'fields'=>[
							'Client.id','Client.name'
						],
					],
					'Currency'=>[
						'fields'=>[
							'Currency.id','Currency.abbreviation'
						],
					],
					'InvoiceSalesOrder'=>[					
						'SalesOrder'=>[					
							'fields'=>[
								'SalesOrder.id','SalesOrder.sales_order_code',
							],
							'Quotation'=>[
								'fields'=>[
									'Quotation.id','Quotation.quotation_code',
								],
								'Contact'=>[
									'fields'=>[
										'Contact.id','Contact.first_name','Contact.last_name',
									],
								],
							],
						],
						'User'=>[					
							'fields'=>[
								'User.id','User.username',
							],
						],
					],					
				],
				'order'=>['Invoice.invoice_date'=>'ASC'],
				'limit'=>($invoiceCount!=0?$invoiceCount:1),
			];
			$subTotalCS=0;
			$commissionCS=0;
			$subTotalUSD=0;
			$commissionUSD=0;
			$recoveredInvoices = $this->Paginator->paginate('Invoice');
			if (!empty($recoveredInvoices)){
        for ($i=0;$i<count($recoveredInvoices);$i++){
          //echo 'i is '.$i.'<br/>';
					$invoiceDate=$recoveredInvoices[$i]['Invoice']['invoice_date'];
					$recoveredInvoices[$i]['Invoice']['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
				
          if ($recoveredInvoices[$i]['Currency']['id']==CURRENCY_CS){
            $subTotalCS+=$recoveredInvoices[$i]['Invoice']['price_subtotal'];
            $commissionCS+=$recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['percentage_commission'];
            //added calculation of totals in US$
            $subTotalUSD+=round($recoveredInvoices[$i]['Invoice']['price_subtotal']/$recoveredInvoices[$i]['Invoice']['exchange_rate'],2);
            $commissionUSD+=round($recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['percentage_commission']/$recoveredInvoices[$i]['Invoice']['exchange_rate'],2);
          }
          elseif ($recoveredInvoices[$i]['Currency']['id']==CURRENCY_USD){
            $subTotalUSD+=$recoveredInvoices[$i]['Invoice']['price_subtotal'];
            $commissionUSD+=$recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['percentage_commission'];
            //added calculation of totals in CS$
            $subTotalCS+=round($recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['exchange_rate'],2);
            $commissionCS+=round($recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['percentage_commission']*$recoveredInvoices[$i]['Invoice']['exchange_rate'],2);
          }
        }
			}
			$selectedUsers[$u]['recoveredInvoices']=$recoveredInvoices;
			$selectedUsers[$u]['recovered_subtotal_CS']=$subTotalCS;
			$selectedUsers[$u]['recovered_commission_CS']=$commissionCS;
			$selectedUsers[$u]['recovered_subtotal_USD']=$subTotalUSD;
			$selectedUsers[$u]['recovered_commission_USD']=$commissionUSD;
			
			$salesObjective=$this->SalesObjective->getApplicableSalesObjective($selectedUsers[$u]['User']['id'],date('Y-m-d'));
			if (!empty($salesObjective)){
				$selectedUsers[$u]['SalesObjective']=$salesObjective['SalesObjective'];
			}
			else {
				$selectedUsers[$u]['SalesObjective']=[];
			}
			
			$pendingInvoiceConditions=[
				'Invoice.bool_annulled'=>false,
				'Invoice.id'=>$invoiceIds,
				'Invoice.bool_paid'=>false,
				'Invoice.invoice_date <'=>$startDate,
				
				//'OR'=>[
					//['Invoice.bool_paid'=>false],
					//['Invoice.amount_paid <='=>'Invoice.price_total'],
				//)
			];
			//pr($pendingInvoiceConditions);
			$invoiceCount=	$this->Invoice->find('count', [
				'fields'=>['Invoice.id'],
				'conditions' => $pendingInvoiceConditions,
			]);
			
			$this->Paginator->settings = [
				'conditions' => $pendingInvoiceConditions,
				
				'contain'=>[				
					'Client'=>[
						'fields'=>[
							'Client.id','Client.name'
						],
					],
					'Currency'=>[
						'fields'=>[
							'Currency.id','Currency.abbreviation'
						],
					],
					'InvoiceSalesOrder'=>[						
						'SalesOrder'=>[					
							'fields'=>[
								'SalesOrder.id','SalesOrder.sales_order_code',
							],
							'Quotation'=>[
								'fields'=>[
									'Quotation.id','Quotation.quotation_code',
								],
								'Contact'=>[
									'fields'=>[
										'Contact.id','Contact.first_name','Contact.last_name',
									],
								],
							],
						],
						'User'=>[					
							'fields'=>[
								'User.id','User.username',
							],
						],
					],					
				],
				'order'=>['Invoice.invoice_date'=>'ASC'],
				'limit'=>($invoiceCount!=0?$invoiceCount:1),
			];
			$subTotalCS=0;
			$commissionCS=0;
			$subTotalUSD=0;
			$commissionUSD=0;
			$pendingInvoices = $this->Paginator->paginate('Invoice');
			if (!empty($pendingInvoices)){
				for ($i=0;$i<count($pendingInvoices);$i++){
					$invoiceDate=$pendingInvoices[$i]['Invoice']['invoice_date'];
					$pendingInvoices[$i]['Invoice']['exchange_rate']=$this->ExchangeRate->getApplicableExchangeRateValue($invoiceDate);
				
					if ($pendingInvoices[$i]['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$pendingInvoices[$i]['Invoice']['price_subtotal'];
						$commissionCS+=$pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($pendingInvoices[$i]['Invoice']['price_subtotal']/$pendingInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['percentage_commission']/$pendingInvoices[$i]['Invoice']['exchange_rate'],2);
					}
					elseif ($pendingInvoices[$i]['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$pendingInvoices[$i]['Invoice']['price_subtotal'];
						$commissionUSD+=$pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionCS+=round($pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['percentage_commission']*$pendingInvoices[$i]['Invoice']['exchange_rate'],2);
					}
				}
				
				
			}
			$selectedUsers[$u]['pendingInvoices']=$pendingInvoices;
			$selectedUsers[$u]['pending_subtotal_CS']=$subTotalCS;
			$selectedUsers[$u]['pending_commission_CS']=$commissionCS;
			$selectedUsers[$u]['pending_subtotal_USD']=$subTotalUSD;
			$selectedUsers[$u]['pending_commission_USD']=$commissionUSD;
		}
		$this->set(compact('selectedUsers'));
		//pr($selectedUsers);
		
    $users=$this->User->find('list',[
      'conditions'=>['bool_active'=>true],
			'order'=>'User.username ASC',
		]);
		$this->set(compact('users'));
		
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$filename=date('Ymd')." Comisiones por vendedor";
		$this->set(compact('filename'));
	}

	public function guardarComisionesPorVendedor() {
		$exportData=$_SESSION['comisionesPorVendedor'];
		$this->set(compact('exportData'));
	}

	public function verPdfComisionesPorVendedor($userId,$currencyId,$startDate,$endDate) {
		$this->loadModel('SalesObjective');
		$this->loadModel('ExchangeRate');
		$this->loadModel('InvoiceSalesOrder');
			
		$this->Invoice->recursive = -1;
		
		$userRoleId = $this->Auth->User('role_id');
		$this->set(compact('userRoleId'));
		
		
		//$startDate=date("Y-m-d",strtotime($startDate));
		//$endDate=date("Y-m-d",strtotime($endDate));
		$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
		$this->set(compact('startDate','endDate','userId','currencyId'));
		
		$exchangeRateStartDate=$this->ExchangeRate->getApplicableExchangeRate($startDate);
		$this->set(compact('exchangeRateStartDate'));
		
		$userConditions=array(
			'User.role_id !='=>ROLE_DEPARTMENT_BOSS,
			'User.role_id !='=>ROLE_OPERATOR,
		);
		//if ($userId>0) { 
		//	$userConditions[]=array('User.id'=>$userId);
		//}
		$this->loadModel('User');
		$this->User->recursive=-1;
		$selectedUsers=$this->User->find('all',array(
			'conditions'=>$userConditions,
			'order'=>'User.username',
		));
		
		if (!empty($selectedUsers)){
			for ($u=0;$u<count($selectedUsers);$u++){
				$selectedUsers[$u]['historical_performance']=$this->SalesObjective->getHistoricalPerformance($selectedUsers[$u]['User']['id']);
			}
		}
		//pr($selectedUsers);
		//pr($startDate);
		
		for ($u=0;$u<count($selectedUsers);$u++){
			$this->InvoiceSalesOrder->recursive=-1;
			$invoiceIds=$this->InvoiceSalesOrder->find('list',array(
				'fields'=>array('InvoiceSalesOrder.invoice_id'),
				'conditions'=>array(
					'InvoiceSalesOrder.user_id'=>$selectedUsers[$u]['User']['id']
				),
			));
			$invoiceConditions=array(
				'Invoice.invoice_date >='=>$startDate,
				'Invoice.invoice_date <'=>$endDatePlusOne,
				'Invoice.bool_annulled'=>false,
				'Invoice.id'=>$invoiceIds,
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
					//'Invoice.amount_paid',
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
					'InvoiceSalesOrder'=>array(	
						'SalesOrder'=>array(
							'fields'=>array(
								'SalesOrder.id','SalesOrder.sales_order_code',
							),
							'Quotation'=>array(
								//'Contact',
							),
						),
					),
				),
				'order'=>array('Invoice.invoice_date'=>'ASC'),
				'limit'=>($invoiceCount!=0?$invoiceCount:1),
			);
			$invoices = $this->Paginator->paginate('Invoice');
			
			$cashInvoices=[];
			$creditInvoices=[];
			
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
			$subTotalCS=0;
			$commissionCS=0;
			$subTotalUSD=0;
			$commissionUSD=0;
			if (!empty($creditInvoices)){
				//echo "processing credit invoices<br/>";
				for ($i=0;$i<count($creditInvoices);$i++){
					//pr($creditInvoices[$i]);
					$invoiceDate=$creditInvoices[$i]['Invoice']['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					//pr($exchangeRate);
					$creditInvoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					
					if ($creditInvoices[$i]['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$creditInvoices[$i]['Invoice']['price_subtotal'];
						$commissionCS+=$creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($creditInvoices[$i]['Invoice']['price_subtotal']/$creditInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['percentage_commission']/$creditInvoices[$i]['Invoice']['exchange_rate'],2);
					}
					elseif ($creditInvoices[$i]['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$creditInvoices[$i]['Invoice']['price_subtotal'];
						$commissionUSD+=$creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionCS+=round($creditInvoices[$i]['Invoice']['price_subtotal']*$creditInvoices[$i]['Invoice']['percentage_commission']*$creditInvoices[$i]['Invoice']['exchange_rate'],2);
					}
				}
				
			}
			$selectedUsers[$u]['creditInvoices']=$creditInvoices;
			$selectedUsers[$u]['credit_subtotal_CS']=$subTotalCS;
			$selectedUsers[$u]['credit_commission_CS']=$commissionCS;
			$selectedUsers[$u]['credit_subtotal_USD']=$subTotalUSD;
			$selectedUsers[$u]['credit_commission_USD']=$commissionUSD;
			
			$subTotalCS=0;
			$commissionCS=0;
			$subTotalUSD=0;
			$commissionUSD=0;
			if (!empty($cashInvoices)){
				//echo "processing cash invoices<br/>";
				for ($i=0;$i<count($cashInvoices);$i++){
					//pr($cashInvoices[$i]);
					$invoiceDate=$cashInvoices[$i]['Invoice']['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					//pr($exchangeRate);
					$cashInvoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
					
					if ($cashInvoices[$i]['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$cashInvoices[$i]['Invoice']['price_subtotal'];
						$commissionCS+=$cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($cashInvoices[$i]['Invoice']['price_subtotal']/$cashInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['percentage_commission']/$cashInvoices[$i]['Invoice']['exchange_rate'],2);
					}
					elseif ($cashInvoices[$i]['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$cashInvoices[$i]['Invoice']['price_subtotal'];
						$commissionUSD+=$cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionCS+=round($cashInvoices[$i]['Invoice']['price_subtotal']*$cashInvoices[$i]['Invoice']['percentage_commission']*$cashInvoices[$i]['Invoice']['exchange_rate'],2);
					}
				}
			}
			$selectedUsers[$u]['cashInvoices']=$cashInvoices;
			$selectedUsers[$u]['cash_subtotal_CS']=$subTotalCS;
			$selectedUsers[$u]['cash_commission_CS']=$commissionCS;
			$selectedUsers[$u]['cash_subtotal_USD']=$subTotalUSD;
			$selectedUsers[$u]['cash_commission_USD']=$commissionUSD;
			//pr($selectedUsers[$u]);
			
			// RECOVERED INVOICES
			$recoveredInvoiceConditions=array(
				'Invoice.bool_annulled'=>false,
				'Invoice.id'=>$invoiceIds,
				'Invoice.bool_paid'=>true,
				'Invoice.invoice_date <'=>$startDate,
				'Invoice.last_payment_date >='=>$startDate,
				'Invoice.last_payment_date <'=>$endDatePlusOne,
				
				//'OR'=>array(
					//array('Invoice.bool_paid'=>false),
					//array('Invoice.amount_paid <='=>'Invoice.price_total'),
				//)
			);
			$invoiceCount=	$this->Invoice->find('count', array(
				'fields'=>array('Invoice.id'),
				'conditions' => $recoveredInvoiceConditions,
			));
			
			$this->Paginator->settings = array(
				'conditions' => $recoveredInvoiceConditions,
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
			$subTotalCS=0;
			$commissionCS=0;
			$subTotalUSD=0;
			$commissionUSD=0;
			$recoveredInvoices = $this->Paginator->paginate('Invoice');
			if (!empty($recoveredInvoices)){
				for ($i=0;$i<count($recoveredInvoices);$i++){
					$invoiceDate=$recoveredInvoices[$i]['Invoice']['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					$recoveredInvoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				}
				
				if ($recoveredInvoices[$i]['Currency']['id']==CURRENCY_CS){
					$subTotalCS+=$recoveredInvoices[$i]['Invoice']['price_subtotal'];
					$commissionCS+=$recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['percentage_commission'];
					//added calculation of totals in US$
					$subTotalUSD+=round($recoveredInvoices[$i]['Invoice']['price_subtotal']/$recoveredInvoices[$i]['Invoice']['exchange_rate'],2);
					$commissionUSD+=round($recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['percentage_commission']/$recoveredInvoices[$i]['Invoice']['exchange_rate'],2);
				}
				elseif ($recoveredInvoices[$i]['Currency']['id']==CURRENCY_USD){
					$subTotalUSD+=$recoveredInvoices[$i]['Invoice']['price_subtotal'];
					$commissionUSD+=$recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['percentage_commission'];
					//added calculation of totals in CS$
					$subTotalCS+=round($recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['exchange_rate'],2);
					$commissionCS+=round($recoveredInvoices[$i]['Invoice']['price_subtotal']*$recoveredInvoices[$i]['Invoice']['percentage_commission']*$recoveredInvoices[$i]['Invoice']['exchange_rate'],2);
				}
			}
			$selectedUsers[$u]['recoveredInvoices']=$recoveredInvoices;
			$selectedUsers[$u]['recovered_subtotal_CS']=$subTotalCS;
			$selectedUsers[$u]['recovered_commission_CS']=$commissionCS;
			$selectedUsers[$u]['recovered_subtotal_USD']=$subTotalUSD;
			$selectedUsers[$u]['recovered_commission_USD']=$commissionUSD;
			
			$salesObjective=$this->SalesObjective->getApplicableSalesObjective($selectedUsers[$u]['User']['id'],date('Y-m-d'));
			if (!empty($salesObjective)){
				$selectedUsers[$u]['SalesObjective']=$salesObjective['SalesObjective'];
			}
			else {
				$selectedUsers[$u]['SalesObjective']=[];
			}
			

			$pendingInvoiceConditions=array(
				'Invoice.bool_annulled'=>false,
				'Invoice.id'=>$invoiceIds,
				'Invoice.bool_paid'=>false,
				'Invoice.invoice_date <'=>$startDate,
				
				//'OR'=>array(
					//array('Invoice.bool_paid'=>false),
					//array('Invoice.amount_paid <='=>'Invoice.price_total'),
				//)
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
			$subTotalCS=0;
			$commissionCS=0;
			$subTotalUSD=0;
			$commissionUSD=0;
			$pendingInvoices = $this->Paginator->paginate('Invoice');
			if (!empty($pendingInvoices)){
				for ($i=0;$i<count($pendingInvoices);$i++){
					$invoiceDate=$pendingInvoices[$i]['Invoice']['invoice_date'];
					$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($invoiceDate);
					$pendingInvoices[$i]['Invoice']['exchange_rate']=$exchangeRate['ExchangeRate']['rate'];
				
					if ($pendingInvoices[$i]['Currency']['id']==CURRENCY_CS){
						$subTotalCS+=$pendingInvoices[$i]['Invoice']['price_subtotal'];
						$commissionCS+=$pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in US$
						$subTotalUSD+=round($pendingInvoices[$i]['Invoice']['price_subtotal']/$pendingInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionUSD+=round($pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['percentage_commission']/$pendingInvoices[$i]['Invoice']['exchange_rate'],2);
					}
					elseif ($pendingInvoices[$i]['Currency']['id']==CURRENCY_USD){
						$subTotalUSD+=$pendingInvoices[$i]['Invoice']['price_subtotal'];
						$commissionUSD+=$pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['percentage_commission'];
						//added calculation of totals in CS$
						$subTotalCS+=round($pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['exchange_rate'],2);
						$commissionCS+=round($pendingInvoices[$i]['Invoice']['price_subtotal']*$pendingInvoices[$i]['Invoice']['percentage_commission']*$pendingInvoices[$i]['Invoice']['exchange_rate'],2);
					}
				}
				
				
			}
			$selectedUsers[$u]['pendingInvoices']=$pendingInvoices;
			$selectedUsers[$u]['pending_subtotal_CS']=$subTotalCS;
			$selectedUsers[$u]['pending_commission_CS']=$commissionCS;
			$selectedUsers[$u]['pending_subtotal_USD']=$subTotalUSD;
			$selectedUsers[$u]['pending_commission_USD']=$commissionUSD;
		}
		$this->set(compact('selectedUsers'));
		//pr($selectedUsers);
		
		$this->loadModel('User');
		$users=$this->User->find('list',array(
			'order'=>'User.username'
		));
		$this->set(compact('users'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$filename=date('Ymd')." Comisiones por vendedor";
		$this->set(compact('filename'));
	}

  public function verReporteTranscursoFacturas(){
    $this->loadModel('Quotation');
    $this->loadModel('SalesOrder');
    $this->loadModel('InvoiceSalesOrder');
    
    $this->loadModel('Currency');
    $this->loadModel('ExchangeRate');
    
    $this->Invoice->recursive=-1;
    $this->InvoiceSalesOrder->recursive=-1;
    
    $this->loadModel('User');
		$this->loadModel('UserPageRight');
		
    $this->User->recursive=-1;
    
		$loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    
		$canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSeeAllUsers'));
    
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Invoices','All');
    $this->set(compact('canSeeAllSalesExecutives'));
		
		if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives){
			$userId=0;
		}
    else {
      $userId=$loggedUserId;
    }
		
    define('DATE_INVOICE',1);
    define('DATE_QUOTATION',2);
    $dateSelectionOptions=[
      DATE_INVOICE=>'Fecha Factura',
      DATE_QUOTATION=>'Fecha Cotización',
    ];
    $this->set(compact('dateSelectionOptions'));
    
    $dateSelectionOptionId=DATE_INVOICE;
		$currencyId=CURRENCY_USD;
			
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			//pr($startDateArray);
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
      $dateSelectionOptionId=$this->request->data['Report']['date_selection_option_id'];
			$userId=$this->request->data['Report']['user_id'];
			$currencyId=$this->request->data['Report']['currency_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
    
		$this->set(compact('startDate','endDate'));
		$this->set(compact('dateSelectionOptionId','userId','currencyId'));
		
    if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers){ 
      $selectedUsers=$this->User->getActiveVendorAllUsers($userId);
    }
    else {
      $selectedUsers=$this->User->getActiveVendorAllVendors($userId);
    }
		//pr($selectedUsers);
    
    
    for ($u=0;$u<count($selectedUsers);$u++){
      //echo "date selection option id is ".$dateSelectionOptionId."<br/>";
      if ($dateSelectionOptionId == DATE_INVOICE){
        $invoiceSalesOrderConditions=['InvoiceSalesOrder.user_id'=>$selectedUsers[$u]['User']['id']];
        $invoiceIds=$this->InvoiceSalesOrder->find('list',[
          'fields'=>['InvoiceSalesOrder.invoice_id'],
          'conditions'=>$invoiceSalesOrderConditions,
        ]);
        $invoiceConditions=[
          'Invoice.invoice_date >='=>$startDate,
          'Invoice.invoice_date <'=>$endDatePlusOne,
          'Invoice.id'=>$invoiceIds,
        ];
      }
      elseif ($dateSelectionOptionId == DATE_QUOTATION){
        $quotationConditions=[
          'Quotation.quotation_date >='=>$startDate,
          'Quotation.quotation_date <'=>$endDatePlusOne,
          'Quotation.user_id'=>$selectedUsers[$u]['User']['id'],
          'Quotation.bool_rejected'=>false,
        ];
        //echo 'quotation conditions';
        //pr($quotationConditions);
        $quotationIds=$this->Quotation->find('list',[
          'fields'=>['Quotation.id'],
          'conditions'=>$quotationConditions,
        ]);
        //echo "quotations";
        //pr($quotationIds);
        $salesOrderConditions=['SalesOrder.quotation_id'=>$quotationIds];
        $salesOrderIds=$this->SalesOrder->find('list',[
          'fields'=>['SalesOrder.id'],
          'conditions'=>$salesOrderConditions,
        ]);
        //echo "sales orders";
        //pr($salesOrderIds);
        $invoiceSalesOrderConditions=['InvoiceSalesOrder.sales_order_id'=>$salesOrderIds];
        $invoiceIds=$this->InvoiceSalesOrder->find('list',[
          'fields'=>['InvoiceSalesOrder.invoice_id'],
          'conditions'=>$invoiceSalesOrderConditions,
        ]);
        //echo "invoices";
        //pr($invoiceIds);
        $invoiceConditions=[
          'Invoice.id'=>$invoiceIds,
        ];
      }    
      $invoices=$this->Invoice->find('all',[
        'fields'=>['Invoice.id','Invoice.invoice_date','Invoice.invoice_code','Invoice.price_subtotal','Invoice.currency_id',],
        'conditions'=>$invoiceConditions,
        'contain'=>[
          'Client'=>[
            'fields'=>['Client.id','Client.name',],
          ],
          'Currency'=>[
            'fields'=>['Currency.id','Currency.abbreviation',],
          ],
          'InvoiceSalesOrder'=>[
            'SalesOrder'=>[
              'fields'=>['SalesOrder.id','SalesOrder.sales_order_date','SalesOrder.sales_order_code',],
              'Quotation'=>[
                'fields'=>['Quotation.id','Quotation.quotation_date','Quotation.quotation_code',],
              ],
            ],
          ],
        ],
      ]);
      
      if (!empty($invoices)){
        for ($i=0;$i<count($invoices);$i++){
          $invoices[$i]['Invoice']['subtotal_selected_currency']=$invoices[$i]['Invoice']['price_subtotal'];
          if ($invoices[$i]['Invoice']['currency_id'] != $currencyId){
            $invoices[$i]['Invoice']['subtotal_selected_currency']=$this->ExchangeRate->convertAmount($invoices[$i]['Invoice']['currency_id'],$currencyId,$invoices[$i]['Invoice']['invoice_date'],$invoices[$i]['Invoice']['price_subtotal']);
          }
        }
      }
      $selectedUsers[$u]['Invoices']=$invoices;
    }
		$this->set(compact('selectedUsers'));			
		
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
		
	}

	public function guardarReporteTranscursoFacturas() {
		$exportData=$_SESSION['reporteTranscursoFacturas'];
		$this->set(compact('exportData'));
	}

}
