<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class ProductionOrdersController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getnewproductionordercode','getproductsforsalesorder');		
	}
	
	public function resumen() {
    $this->loadModel('Quotation');
    $this->loadModel('SalesOrder');
    
    $this->loadModel('User');
				
    $loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('userRoleId'));
    
    
		if ($this->request->is('post')) {
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
		$this->set(compact('startDate','endDate'));
		
		$conditions=[];
		switch ($loggedUserId){
			case ROLE_ADMIN:
			case ROLE_ASSISTANT:
				break;
			case ROLE_SALES_EXECUTIVE:	
				$quotationIds=$this->Quotation->find('list',[
					'fields'=>['Quotation.id'],
					'conditions'=>[
						'Quotation.user_id'=>$loggedUserId,
					],
				]);
				$salesOrderIds=$this->SalesOrder->find('list',[
					'fields'=>['SalesOrder.id'],
					'conditions'=>[
						'SalesOrder.quotation_id'=>$quotationIds,
					],
				]);
				$conditions[]=['ProductionOrder.sales_order_id'=>$salesOrderIds];
				break;
			case ROLE_DEPARTMENT_BOSS:
				$departmentBoss=$this->User->find('first',['conditions'=>['User.id'=>$loggedUserId]]);
				if (!empty($departmentBoss)){
					$conditions[]=['ProductionOrder.department_id'=>$departmentBoss['User']['department_id']];
				}
				break;
			case ROLE_OPERATOR:
				//OPERATORS SHOULD NEVER SEE THIS
				$salesOrderIds=[];
				$conditions['ProductionOrder.sales_order_id']=$salesOrderIds;
				break;
		}
		
		$productionOrderCount=	$this->ProductionOrder->find('count', [
			'fields'=>['ProductionOrder.id'],
			'conditions' => $conditions,
		]);
		
		$this->Paginator->settings = [
			'conditions' => $conditions,
			'contain'=>[
				'SalesOrder',
			],
			'limit'=>($productionOrderCount!=0?$productionOrderCount:1),
		];

		$productionOrders = $this->Paginator->paginate('ProductionOrder');
		
    //for ($po=0;$po<count($productionOrders);$po++){
		//	$percentageProcessed=$this->ProductionOrder->getPercentageProcessed($productionOrders[$po]['ProductionOrder']['id']);
		//	$productionOrders[$po]['ProductionOrder']['percentage_processed']=$percentageProcessed;
		//}
		
		$this->set(compact('productionOrders'));
		
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
    
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function guardarResumenOrdenesDeProduccion() {
		$exportData=$_SESSION['resumenOrdenesDeProduccion'];
		$this->set(compact('exportData'));
	}
/*
	public function view($id = null) {
		if (!$this->ProductionOrder->exists($id)) {
			throw new NotFoundException(__('Invalid production order'));
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
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			//if ($this->Session->check('currencyId')){
			//	$currencyId=$_SESSION['currencyId'];
			//}
			//if ($this->Session->check('userId')){
			//	$user_id=$_SESSION['userId'];
			///}
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		//$_SESSION['currencyId']=$currencyId;
		//$_SESSION['userId']=$user_id;
		$this->set(compact('startDate','endDate'));
		//$this->set(compact('currencyId','userId'));
		
		$conditions = array(
			'ProductionOrder.id'=> $id,
		);
		$productionOrder= $this->ProductionOrder->find('first', array(
			'conditions'=>$conditions,
			'contain'=>array(
				'ProductionOrderProduct'=>array(
					'Product',
					'ProductionOrderProductOperationLocation'=>array(
						'OperationLocation',
					),
					'ProductionOrderProductDepartment'=>array(
						'Department',
						'order'=>'ProductionOrderProductDepartment.rank ASC',
					),
				),
				'ProductionOrderRemark'=>array(
					'User',
				),
				'SalesOrder',
			),
		));
		
		$this->set(compact('productionOrder'));
		//pr($productionOrder);
		
		$filename="Orden_de_producción_".$productionOrder['ProductionOrder']['production_order_code'];
		$this->set(compact('filename'));
		
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}
*/
  public function detalle($id = null) {
		if (!$this->ProductionOrder->exists($id)) {
			throw new NotFoundException(__('Invalid production order'));
		}
		
    define('SHOW_ALL','1');
    define('SHOW_ONLY_CURRENT','2');
    
    $displayOptions=[
      SHOW_ALL=>'Todos departamentos y instrucciones',
      SHOW_ONLY_CURRENT=>'Únicamente departamentos y instrucciones actuales',
    ];
    $this->set(compact('displayOptions'));
    
    $displayOptionId=SHOW_ALL;
    if ($this->request->is('post')) {
      $displayOptionId=$this->request->data['Report']['display_option_id'];
    }
    $this->set(compact('displayOptionId'));
    
		$productionOrder= $this->ProductionOrder->find('first', [
			'conditions'=>['ProductionOrder.id'=> $id,],
			'contain'=>[
        'ProductionOrderDepartment'=>[
          'Department',
          'ProductionOrderDepartmentInstruction',
          'ProductionOrderDepartmentState'=>[
            'ProductionOrderState',
          ],
          'order'=>'ProductionOrderDepartment.rank ASC',
        ],
				'ProductionOrderProduct'=>[
					'Product',
					'ProductionOrderProductOperationLocation'=>[
						'OperationLocation',
					],
					'ProductionOrderProductDepartment'=>[
						'Department',
            'ProductionOrderProductDepartmentInstruction',
            'ProductionOrderProductDepartmentState'=>[
              'ProductionOrderState',
            ],
						'order'=>'ProductionOrderProductDepartment.rank ASC',
					],
				],
				'ProductionOrderRemark'=>[
					'User',
				],
				'SalesOrder',
			],
		]);
		$this->set(compact('productionOrder'));
		//pr($productionOrder);
    
    $this->loadModel('User');
    $users=$this->User->getUserNaturalNameList();
		$this->set(compact('users'));
    
		$filename="Orden_de_producción_".$productionOrder['ProductionOrder']['production_order_code'];
		$this->set(compact('filename'));
		
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

  public function detallePdf($id = null) {
		if (!$this->ProductionOrder->exists($id)) {
			throw new NotFoundException(__('Invalid production order'));
		}
		
    define('SHOW_ALL','1');
    define('SHOW_ONLY_CURRENT','2');
    
    $displayOptions=[
      SHOW_ALL=>'Todos departamentos y instrucciones',
      SHOW_ONLY_CURRENT=>'Únicamente departamentos y instrucciones actuales',
    ];
    $this->set(compact('displayOptions'));
    
    $displayOptionId=SHOW_ALL;
    if ($this->request->is('post')) {
      $displayOptionId=$this->request->data['Report']['display_option_id'];
    }
    $this->set(compact('displayOptionId'));
    
		$productionOrder= $this->ProductionOrder->find('first', [
			'conditions'=>['ProductionOrder.id'=> $id,],
			'contain'=>[
        'ProductionOrderDepartment'=>[
          'Department',
          'ProductionOrderDepartmentInstruction',
          'ProductionOrderDepartmentState'=>[
            'ProductionOrderState',
          ],
          'order'=>'ProductionOrderDepartment.rank ASC',
        ],
				'ProductionOrderProduct'=>[
					'Product',
					'ProductionOrderProductOperationLocation'=>[
						'OperationLocation',
					],
					'ProductionOrderProductDepartment'=>[
						'Department',
            'ProductionOrderProductDepartmentInstruction',
            'ProductionOrderProductDepartmentState'=>[
              'ProductionOrderState',
            ],
						'order'=>'ProductionOrderProductDepartment.rank ASC',
					],
				],
				'ProductionOrderRemark'=>[
					'User',
				],
				'SalesOrder',
			],
		]);
		$this->set(compact('productionOrder'));
		//pr($productionOrder);
    
    $this->loadModel('User');
    $users=$this->User->getUserNaturalNameList();
		$this->set(compact('users'));
    
		$filename="Orden_de_producción_".$productionOrder['ProductionOrder']['production_order_code'];
		$this->set(compact('filename'));
		
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function viewPdf($id = null) {
		if (!$this->ProductionOrder->exists($id)) {
			throw new NotFoundException(__('Invalid production order'));
		}
		$conditions = array(
			'ProductionOrder.id'=> $id,
		);
		$productionOrder= $this->ProductionOrder->find('first', array(
			'conditions'=>$conditions,
			'contain'=>array(
				'ProductionOrderProduct'=>array(
					'Product',
					'ProductionOrderProductOperationLocation'=>array(
						'OperationLocation',
					),
					'ProductionOrderProductDepartment'=>array(
						'Department',
						'order'=>'ProductionOrderProductDepartment.rank ASC',
					),
				),
				'ProductionOrderRemark'=>array(
					'User',
				),
				'SalesOrder',
			),
		));
		
		$this->set(compact('productionOrder'));
		//pr($productionOrder);
		
		$filename="Orden_de_producción_".$productionOrder['ProductionOrder']['production_order_code'];
		$this->set(compact('filename'));
		
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

  public function crear($salesOrderId=0) {
		$this->loadModel('SalesOrder');
    $this->loadModel('SalesOrderProduct');
    
		$this->loadModel('ProductionOrderProduct');
    $this->loadModel('Product');
    $this->loadModel('Department');

    $this->loadModel('ProductionOrderDepartment');
		$this->loadModel('ProductionOrderDepartmentState');
    $this->loadModel('ProductionOrderDepartmentInstruction');
    
    $this->loadModel('ProductionOrderProductDepartment');
    $this->loadModel('ProductionOrderProductDepartmentState');
    $this->loadModel('ProductionOrderProductDepartmentInstruction');
                
    $this->loadModel('ProductionOrderState');
    
		$this->loadModel('OperationLocation');
		$this->loadModel('ProductionOrderProductOperationLocation');
		
    $this->loadModel('ProductionOrderRemark');
    
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('userRoleId'));
    
		$productsForSalesOrder=[];
		if ($salesOrderId>0){
			$productConditions=[
				'SalesOrderProduct.sales_order_id'=>$salesOrderId,
				'SalesOrderProduct.bool_no_production'=>false,
			];
			
			$productsForSalesOrder=$this->SalesOrderProduct->find('all',[
				'fields'=>[
					'SalesOrderProduct.id',
					'SalesOrderProduct.product_id',
					'SalesOrderProduct.product_description',
					'SalesOrderProduct.product_quantity',
				],
				'conditions'=>$productConditions,
        'recursive'=>-1,
			]);
		}
		$requestDepartments=[];
		$requestProducts=[];
    if ($this->request->is('post')) {
			$salesOrderId=$this->request->data['ProductionOrder']['sales_order_id'];
			
      for ($i=0;$i<count($this->request->data['ProductionOrder']['Department']);$i++){
				if ($this->request->data['ProductionOrder']['Department'][$i]['department_id'] > 0){
          $department=$this->request->data['ProductionOrder']['Department'][$i];
					$requestDepartments[]=$department;
				}
			}
      
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id'] > 0 && $this->request->data['ProductionOrderProduct'][$i]['product_quantity'] > 0){
          $productionOrderProduct=$this->request->data['ProductionOrderProduct'][$i];
          
					$requestProducts[]['ProductionOrderProduct']=$this->request->data['ProductionOrderProduct'][$i];
				}
			}
			
			$productionOrderDateArray=$this->request->data['ProductionOrder']['production_order_date'];
			//pr($productionOrderDateArray);
			$productionOrderDateString=$productionOrderDateArray['year'].'-'.$productionOrderDateArray['month'].'-'.$productionOrderDateArray['day'];
			$productionOrderDate=date( "Y-m-d", strtotime($productionOrderDateString));
			
			if ($productionOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de producción no puede estar en el futuro!  No se guardó la orden de producción.'), 'default',array('class' => 'error-message'));
			}
			else {			
        //pr($this->request->data);
      	$datasource=$this->ProductionOrder->getDataSource();
				$datasource->begin();
				try {
					if ($this->request->data['ProductionOrder']['bool_annulled']){
						$this->ProductionOrder->create();
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando la orden de producción";
							//pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						} 
						$production_order_id=$this->ProductionOrder->id;
						
						$productionOrderRemarkArray=[];
						$productionOrderRemarkArray['ProductionOrderRemark']['user_id']=$this->Auth->User('id');
						$productionOrderRemarkArray['ProductionOrderRemark']['production_order_id']=$production_order_id;
						$productionOrderRemarkArray['ProductionOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$productionOrderRemarkArray['ProductionOrderRemark']['remark_text']="Se anuló la orden de producción";
						$productionOrderRemarkArray['ProductionOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->ProductionOrderRemark->create();
						if (!$this->ProductionOrderRemark->save($productionOrderRemarkArray)) {
							echo "Problema guardando las remarcas para la orden de producción";
							pr($this->validateErrors($this->ProductionOrderRemark));
							throw new Exception();
						}
					}
					else {
						$this->ProductionOrder->create();
						
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando la orden de producción";
							pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						}
						$productionOrderId=$this->ProductionOrder->id;
						if (!empty($this->request->data['Document']['url_doc'][0]['tmp_name'])){
							$docOK=$this->uploadFiles('productionorderdocuments/'.$production_order_id,$this->request->data['Document']['url_doc']);
							//echo "doc OK<br/>";
							//pr($docOK);
							if (array_key_exists('urls',$docOK)){
								$this->request->data['ProductionOrder']['url_doc']=$docOK['urls'][0];
							}
						}
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando el documento de diseño para la orden de producción";
							pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						}
						
            $departmentRank=1;
            foreach ($this->request->data['ProductionOrder']['Department'] as $departmentData){
              if ($departmentData['department_id'] > 0){
                $departmentArray=[
                  'user_id'=>$loggedUserId,
                  'production_order_id'=>$productionOrderId,
                  'department_id'=>$departmentData['department_id'],
                  'department_datetime'=>date('Y-m-d H:i:s'),
                  'rank'=>$departmentRank,
                ]; 
                $this->ProductionOrderDepartment->create();
                if (!$this->ProductionOrderDepartment->save($departmentArray)) {
                  echo "Problema guardando el departamento para la orden de producción";
                  pr($this->validateErrors($this->ProductionOrderDepartment));
                  throw new Exception();
                }
                $departmentRank++;
                $productionOrderDepartmentId=$this->ProductionOrderDepartment->id;
                
                $departmentStateArray=[
                  'user_id'=>$loggedUserId,
                  'production_order_id'=>$productionOrderId,
                  'production_order_department_id'=>$productionOrderDepartmentId,
                  'department_id'=>$departmentData['department_id'],
                  'production_order_state_id'=>$departmentData['ProductionOrderDepartmentState'][0]['production_order_state_id'],
                  'state_datetime'=>date('Y-m-d H:i:s'),
                ];
                $this->ProductionOrderDepartmentState->create();
                if (!$this->ProductionOrderDepartmentState->save($departmentStateArray)) {
                  echo "Problema guardando el estado del departamento de la orden de producción";
                  pr($this->validateErrors($this->ProductionOrderDepartmentState));
                  throw new Exception();
                }
                
                if (!empty($departmentData['ProductionOrderDepartmentInstruction'][0]['instruction_text'])){
                  $departmentInstructionArray=[
                    'user_id'=>$loggedUserId,
                    'production_order_id'=>$productionOrderId,
                    'production_order_department_id'=>$productionOrderDepartmentId,
                    'department_id'=>$departmentData['department_id'],
                    'instruction_datetime'=>date('Y-m-d H:i:s'),
                    'instruction_text'=>$departmentData['ProductionOrderDepartmentInstruction'][0]['instruction_text'],
                  ];
                  $this->ProductionOrderDepartmentInstruction->create();
                  if (!$this->ProductionOrderDepartmentInstruction->save($departmentInstructionArray)) {
                    echo "Problema guardando el estado las instrucciones del departamento de la orden de producción";
                    pr($this->validateErrors($this->ProductionOrderDepartmentInstruction));
                    throw new Exception();
                  }
                }
              }
            }
            
						foreach ($this->request->data['ProductionOrderProduct'] as $productionOrderProduct){
							if ($productionOrderProduct['product_id']>0){
								//pr($productionOrderProduct);
								$productArray=[
                  'production_order_id'=>$productionOrderId,
                  'product_id'=>$productionOrderProduct['product_id'],
                  'product_description'=>$productionOrderProduct['product_description'],
                  'product_quantity'=>$productionOrderProduct['product_quantity'],
                  'sales_order_product_id'=>$productionOrderProduct['sales_order_product_id'],
                  'current_department_id'=>$productionOrderProduct['Department'][0]['department_id'],
                ];
								$this->ProductionOrderProduct->create();
								if (!$this->ProductionOrderProduct->save($productArray)) {
									echo "Problema guardando los productos de la orden de producción";
									pr($this->validateErrors($this->ProductionOrderProduct));
									throw new Exception();
								}
								
								$productionOrderProductId=$this->ProductionOrderProduct->id;
								
								$salesOrderProductArray=[
                  'id'=>$productionOrderProduct['sales_order_product_id'],
                  'sales_order_product_status_id'=>PRODUCT_STATUS_AWAITING_PRODUCTION,
                ];
								$this->SalesOrderProduct->id=$productionOrderProduct['sales_order_product_id'];
								if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
									echo "Problema cambiando el estado de los productos de la orden de venta";
									pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
								
								foreach ($productionOrderProduct['operation_location_id'] as $locationId){
									if ($locationId>0){
										$this->ProductionOrderProductOperationLocation->create();
										$locationArray=[
                      'production_order_product_id'=>$productionOrderProductId,
                      'operation_location_id'=>$locationId,
                    ];
										if (!$this->ProductionOrderProductOperationLocation->save($locationArray)) {
											echo "Problema guardando los lugares de las operaciones del producto";
											pr($this->validateErrors($this->ProductionOrderProductOperationLocation));
											throw new Exception();
										}
									}
								}
								
								$productDepartmentRank=1;
                $boolFirstDepartment=true;
                foreach ($productionOrderProduct['Department'] as $productDepartmentData){
                  if ($productDepartmentData['department_id'] > 0){
                    $departmentArray=[
                      'user_id'=>$loggedUserId,
                      'production_order_id'=>$productionOrderId,
                      'production_order_product_id'=>$productionOrderProductId,
                      'department_id'=>$productDepartmentData['department_id'],
                      'department_datetime'=>date('Y-m-d H:i:s'),
                      'rank'=>$productDepartmentRank,
                    ]; 
                    $this->ProductionOrderProductDepartment->create();
                    if (!$this->ProductionOrderProductDepartment->save($departmentArray)) {
                      echo "Problema guardando el departamento para el producto de la orden de producción";
                      pr($this->validateErrors($this->ProductionOrderProductDepartment));
                      throw new Exception();
                    }
                    $productionOrderProductDepartmentId=$this->ProductionOrderProductDepartment->id;
                    if ($boolFirstDepartment){
                      $boolFirstDepartment=false;
                      $productArray=[
                        'id'=>$productionOrderProductId,
                        'current_production_order_product_department_id'=>$productionOrderProductDepartmentId,
                      ];
                      $this->ProductionOrderProduct->id=$productionOrderProductId;
                      if (!$this->ProductionOrderProduct->save($productArray)) {
                        echo "Problema insertando el departamento actual del producto de la orden de producción";
                        pr($this->validateErrors($this->ProductionOrderProduct));
                        throw new Exception();
                      }
                    }
                    
                    $productDepartmentRank++;
                    $productionOrderProductDepartmentId=$this->ProductionOrderProductDepartment->id;
                    
                    $departmentStateArray=[
                      'user_id'=>$loggedUserId,
                      'production_order_id'=>$productionOrderId,
                      'production_order_product_id'=>$productionOrderProductId,
                      'production_order_product_department_id'=>$productionOrderProductDepartmentId,
                      'product_id'=>$productionOrderProduct['product_id'],
                      'department_id'=>$productDepartmentData['department_id'],
                      'production_order_state_id'=>$productDepartmentData['state_id'],
                      'state_datetime'=>date('Y-m-d H:i:s'),
                    ];
                    $this->ProductionOrderProductDepartmentState->create();
                    if (!$this->ProductionOrderProductDepartmentState->save($departmentStateArray)) {
                      echo "Problema guardando el estado del departamento del producto de la orden de producción";
                      pr($this->validateErrors($this->ProductionOrderProductDepartmentState));
                      throw new Exception();
                    }
                    
                    if (!empty($productDepartmentData['Instruction'])){
                      //pr($productDepartmentData['Instruction']);
                      foreach ($productDepartmentData['Instruction'] as $productDepartmentInstructionData){
                        if (!empty($productDepartmentInstructionData['instruction_text'])){
                          $departmentInstructionArray=[
                            'user_id'=>$loggedUserId,
                            'production_order_id'=>$productionOrderId,
                            'production_order_product_id'=>$productionOrderProductId,
                            'production_order_product_department_id'=>$productionOrderProductDepartmentId,
                            'product_id'=>$productionOrderProduct['product_id'],
                            'department_id'=>$productDepartmentData['department_id'],
                            'instruction_datetime'=>date('Y-m-d H:i:s'),
                            'instruction_text'=>$productDepartmentInstructionData['instruction_text'],
                          ];
                          $this->ProductionOrderProductDepartmentInstruction->create();
                          //pr($departmentInstructionArray);
                          if (!$this->ProductionOrderProductDepartmentInstruction->save($departmentInstructionArray)) {
                            echo "Problema guardando el estado las instrucciones del departamento del producto de la orden de producción";
                            pr($this->validateErrors($this->ProductionOrderProductDepartmentInstruction));
                            throw new Exception();
                          }  
                        }
                      }    
                    }
                  }
                }
							}
						}
													
						$productionOrderRemark=$this->request->data['ProductionOrderRemark'];
						//pr($quotationRemark);
						$remarkArray=[
              'user_id'=>$productionOrderRemark['user_id'],
              'production_order_id'=>$productionOrderId,
              'remark_datetime'=>date('Y-m-d H:i:s'),
              'remark_text'=>(empty($this->request->data['ProductionOrderRemark']['remark_text'])?"Orden de Producción creada":$productionOrderRemark['remark_text']),
            ];
						$this->ProductionOrderRemark->create();
						if (!$this->ProductionOrderRemark->save($remarkArray)) {
							echo "Problema guardando las remarcas para la orden de producción";
							pr($this->validateErrors($this->ProductionOrderRemark));
							throw new Exception();
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->ProductionOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se creó la orden de producción ".$this->request->data['ProductionOrder']['production_order_code']);
					$this->Session->setFlash(__('The production order has been saved.'), 'default',['class' => 'success']);
					return $this->redirect(['action' => 'detalle',$productionOrderId]);
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('The production order could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				}
        
			}
		}
		else {
      foreach ($productsForSalesOrder as $salesOrderProduct){
        //pr($salesOrderProduct);
        $productionOrderProduct=[
          'ProductionOrderProduct'=>$salesOrderProduct['SalesOrderProduct'],
        ];
        $productionOrderProduct['ProductionOrderProduct']['sales_order_product_id']=$salesOrderProduct['SalesOrderProduct']['id'];
        $productionOrderProduct['ProductionOrderProduct']['operation_location_id']=0;
        $productionOrderProduct['ProductionOrderProduct']['Department']=[];
        $requestProducts[]=$productionOrderProduct;
      }
    }
    $this->set(compact('requestProducts'));
    $this->set(compact('requestDepartments'));
		$this->set(compact('salesOrderId'));
		//pr($productsForSalesOrder);
		$this->set(compact('productsForSalesOrder'));
		
    // exclude sales orders that already have production orders
		$excludedSalesOrderIds=$this->ProductionOrder->find('list',[
			'fields'=>['ProductionOrder.sales_order_id'],
			'conditions'=>[
				'ProductionOrder.bool_annulled'=>false,
			],
		]);
		$salesOrders = $this->ProductionOrder->SalesOrder->find('list',[
			'conditions'=>[
				'bool_annulled'=>false,
				'bool_authorized'=>true,
				'bool_completely_delivered'=>false,
				'SalesOrder.id !='=>$excludedSalesOrderIds,
			],
      'order'=>'SalesOrder.sales_order_code',
		]);
		$this->set(compact('salesOrders'));
		
		$departments=$this->Department->getDepartmentList();
		$operationLocations=$this->OperationLocation->getOperationLocationList();
		$products=$this->Product->getProductList();
    $this->set(compact('departments','operationLocations','products'));
		
    $productionOrderStates=$this->ProductionOrderState->getProductionOrderStateList();
    $this->set(compact('productionOrderStates'));
    
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($loggedUserId,$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($loggedUserId,$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
	}

  public function editar($id = null) {
		if (!$this->ProductionOrder->exists($id)) {
			throw new NotFoundException(__('Invalid production order'));
		}
		
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('Product');
		$this->loadModel('OperationLocation');
		$this->loadModel('Department');
		$this->loadModel('ProductionOrderRemark');
		$this->loadModel('ProductionOrderProduct');
		$this->loadModel('ProductionOrderProductOperationLocation');
		$this->loadModel('ProductionOrderProductDepartment');
		
		$this->Product->recursive=-1;
		
		$requestProducts=[];
		
		if ($this->request->is(array('post', 'put'))) {
			$salesOrderId=$this->request->data['ProductionOrder']['sales_order_id'];
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id']>0&&$this->request->data['ProductionOrderProduct'][$i]['product_quantity']>0){
					pr($this->request->data['ProductionOrderProduct'][$i]);
					
					$locationValues=[];
					if (!empty($this->request->data['ProductionOrderProduct'][$i]['operation_location_id'])){
						for ($loc=0;$loc<count($this->request->data['ProductionOrderProduct'][$i]['operation_location_id']);$loc++){
							$locationValues[]=$this->request->data['ProductionOrderProduct'][$i]['operation_location_id'][$loc];
						}
					}
					$this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation']['locationValues']=$locationValues;
					
					$departmentsInRequest=[];
					foreach ($this->request->data['ProductionOrderProduct'][$i]['departments'] as $department){
						if (!empty($department['department_id'])){
							$departmentsInRequest[]=array(
								'department_id'=>$department['department_id'],
								'rank'=>count($departmentsInRequest)-1,
							);
						}
					}
					$this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductDepartment']=$departmentsInRequest;
					$requestProducts[]['ProductionOrderProduct']=$this->request->data['ProductionOrderProduct'][$i];
				}
			}
			
			$productionOrderDateArray=$this->request->data['ProductionOrder']['production_order_date'];
			//pr($productionOrderDateArray);
			$productionOrderDateString=$productionOrderDateArray['year'].'-'.$productionOrderDateArray['month'].'-'.$productionOrderDateArray['day'];
			$productionOrderDate=date( "Y-m-d", strtotime($productionOrderDateString));
			
			$boolDepartmentsOK=true;
			$warning="";
			
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id']>0&&$this->request->data['ProductionOrderProduct'][$i]['product_quantity']>0){
					$boolDepartmentPresentForProduct=false;
					foreach ($this->request->data['ProductionOrderProduct'][$i]['departments'] as $department){
						if ($department['department_id']){
							$boolDepartmentPresentForProduct=true;
						}
					}
					if (!$boolDepartmentPresentForProduct){
						$boolDepartmentsOK=false;
						$relatedProduct=$this->Product->find('first',array('conditions'=>array('Product.id'=>$this->request->data['ProductionOrderProduct'][$i]['product_id'])));
						$warning.="El producto  ".$relatedProduct['Product']['name']." con cantidad ".$this->request->data['ProductionOrderProduct'][$i]['product_quantity']." no tiene asociado un departamento.  Por favor corregir. <br/>";
					}
				}
			}
			//echo "bool departments ok is ".$boolDepartmentsOK."<br/>";
			/*
			$boolRemarkOK=true;
			$warning="";
			if (!$bool_first_production_order){
				if (empty($this->request->data['ProductionOrderRemark']['remark_text'])){
					$boolRemarkOK=false;
					$warning="Esto es una orden de producción registrado manualmente.  Se debe registrar una remarca para esta orden de producción.  La orden de producción no se guardó.";
				}
			}
			*/
			if ($productionOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de producción no puede estar en el futuro!  No se guardó la orden de producción.'), 'default',array('class' => 'error-message'));
			}
			//elseif (!$boolRemarkOK){
			//	$this->Session->setFlash($warning, 'default',array('class' => 'error-message'));
			//}
			elseif (!$boolDepartmentsOK){
				$this->Session->setFlash($warning, 'default',array('class' => 'error-message'));
			}
			else {			
				$datasource=$this->Product->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$previousProductionOrderProducts=$this->ProductionOrderProduct->find('all',array(
						'fields'=>array('ProductionOrderProduct.id','ProductionOrderProduct.sales_order_product_id'),
						'conditions'=>array(
							'ProductionOrderProduct.production_order_id'=>$id,
						),
						'contain'=>array(
							'ProductionOrderProductOperationLocation',
							'ProductionOrderProductDepartment',
						),
					));
					//pr($previousProductionOrderProducts);
					if (!empty($previousProductionOrderProducts)){
						foreach ($previousProductionOrderProducts as $previousProductionOrderProduct){
							if (!empty($previousProductionOrderProduct['ProductionOrderProductOperationLocation'])){
								foreach ($previousProductionOrderProduct['ProductionOrderProductOperationLocation'] as $location){
									$this->ProductionOrderProductOperationLocation->id=$location['id'];
									$this->ProductionOrderProductOperationLocation->delete($location['id']);
								}
							}
							if (!empty($previousProductionOrderProduct['ProductionOrderProductDepartment'])){
								foreach ($previousProductionOrderProduct['ProductionOrderProductDepartment'] as $department){
									$this->ProductionOrderProductDepartment->id=$department['id'];
									$this->ProductionOrderProductDepartment->delete($department['id']);
								}
							}								
							
							$this->SalesOrderProduct->id=$previousProductionOrderProduct['ProductionOrderProduct']['sales_order_product_id'];
							$salesOrderProductArray=[];
							$salesOrderProductArray['SalesOrderProduct']['id']=$previousProductionOrderProduct['ProductionOrderProduct']['sales_order_product_id'];
							$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AUTHORIZED;
							$this->SalesOrderProduct->id=$previousProductionOrderProduct['ProductionOrderProduct']['sales_order_product_id'];
							if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
								echo "Problema cambiando el estado de los productos de la orden de venta";
								pr($this->validateErrors($this->SalesOrderProduct));
								throw new Exception();
							}
							
							$this->ProductionOrderProduct->id=$previousProductionOrderProduct['ProductionOrderProduct']['id'];
							$this->ProductionOrderProduct->delete($previousProductionOrderProduct['ProductionOrderProduct']['id']);
						}
					}
					
					if ($this->request->data['ProductionOrder']['bool_annulled']){
						$this->ProductionOrder->id=$id;
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando la orden de producción";
							//pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						} 
						$productionOrderId=$this->ProductionOrder->id;
						
						$productionOrderRemarkArray=[];
						$productionOrderRemarkArray['ProductionOrderRemark']['user_id']=$this->Auth->User('id');
						$productionOrderRemarkArray['ProductionOrderRemark']['purchase_order_id']=$productionOrderId;
						$productionOrderRemarkArray['ProductionOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$productionOrderRemarkArray['ProductionOrderRemark']['remark_text']="Se anuló la orden de producción";
						$productionOrderRemarkArray['ProductionOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->ProductionOrderRemark->create();
						if (!$this->ProductionOrderRemark->save($productionOrderRemarkArray)) {
							echo "Problema guardando las remarcas para la orden de producción";
							pr($this->validateErrors($this->ProductionOrderRemark));
							throw new Exception();
						}
					}
					else {
						$this->ProductionOrder->id=$id;
						
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando la orden de producción";
							pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						}
						$productionOrderId=$this->ProductionOrder->id;
						if (!empty($this->request->data['Document']['url_doc'][0]['tmp_name'])){
							$docOK=$this->uploadFiles('productionorderdocuments/'.$productionOrderId,$this->request->data['Document']['url_doc']);
							//echo "image OK<br/>";
							//pr($imageOK);
							if (array_key_exists('urls',$docOK)){
								$this->request->data['ProductionOrder']['url_doc']=$docOK['urls'][0];
							}
						}
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando el documento de diseño para la orden de producción";
							pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						}
						
						foreach ($this->request->data['ProductionOrderProduct'] as $productionOrderProduct){
							if ($productionOrderProduct['product_id']>0){
								//pr($quotationProduct);
								$productArray=[];
								$productArray['ProductionOrderProduct']['production_order_id']=$productionOrderId;
								$productArray['ProductionOrderProduct']['product_id']=$productionOrderProduct['product_id'];
								$productArray['ProductionOrderProduct']['product_description']=$productionOrderProduct['product_description'];
								$productArray['ProductionOrderProduct']['product_instruction']=$productionOrderProduct['product_instruction'];
								$productArray['ProductionOrderProduct']['product_quantity']=$productionOrderProduct['product_quantity'];
								$productArray['ProductionOrderProduct']['sales_order_product_id']=$productionOrderProduct['sales_order_product_id'];
								$this->ProductionOrderProduct->create();
								if (!$this->ProductionOrderProduct->save($productArray)) {
									echo "Problema guardando los productos de la orden de producción";
									pr($this->validateErrors($this->ProductionOrderProduct));
									throw new Exception();
								}
								
								$productionOrderProductId=$this->ProductionOrderProduct->id;
								
								$salesOrderProductArray=[];
								$salesOrderProductArray['SalesOrderProduct']['id']=$productionOrderProduct['sales_order_product_id'];
								$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PURCHASE;
								$this->SalesOrderProduct->id=$productionOrderProduct['sales_order_product_id'];
								if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
									echo "Problema cambiando el estado de los productos de la orden de venta";
									pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
								
								foreach ($productionOrderProduct['operation_location_id'] as $locationId){
									if ($locationId>0){
										$this->ProductionOrderProductOperationLocation->create();
										$locationArray=[];
										$locationArray['ProductionOrderProductOperationLocation']['production_order_product_id']=$productionOrderProductId;
										$locationArray['ProductionOrderProductOperationLocation']['operation_location_id']=$locationId;
										if (!$this->ProductionOrderProductOperationLocation->save($locationArray)) {
											echo "Problema guardando los lugares de las operaciones del producto";
											pr($this->validateErrors($this->ProductionOrderProductOperationLocation));
											throw new Exception();
										}
									}
								}
								
								$rank=0;
								foreach ($productionOrderProduct['departments'] as $department){
									pr($department);
									if ($department['department_id']>0){
										$this->ProductionOrderProductDepartment->create();
										$departmentArray=[];
										$departmentArray['ProductionOrderProductDepartment']['production_order_product_id']=$productionOrderProductId;
										$departmentArray['ProductionOrderProductDepartment']['department_id']=$department['department_id'];
										$departmentArray['ProductionOrderProductDepartment']['rank']=$rank;
										if (!$this->ProductionOrderProductDepartment->save($departmentArray)) {
											echo "Problema guardando los departamentos para el producto";
											pr($this->validateErrors($this->ProductionOrderProductDepartment));
											throw new Exception();
										}
										$rank++;
									}
								}
							}
						}
							
						if (!empty($this->request->data['ProductionOrderRemark']['remark_text'])){
							$productionOrderRemark=$this->request->data['ProductionOrderRemark'];
							//pr($quotationRemark);
							$remarkArray=[];
							$remarkArray['ProductionOrderRemark']['user_id']=$productionOrderRemark['user_id'];
							$remarkArray['ProductionOrderRemark']['production_order_id']=$productionOrderId;
							$remarkArray['ProductionOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
							$remarkArray['ProductionOrderRemark']['remark_text']=$productionOrderRemark['remark_text'];
							$this->ProductionOrderRemark->create();
							if (!$this->ProductionOrderRemark->save($remarkArray)) {
								echo "Problema guardando las remarcas para la orden de producción";
								pr($this->validateErrors($this->ProductionOrderRemark));
								throw new Exception();
							}
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->ProductionOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se creó la orden de producción ".$this->request->data['ProductionOrder']['production_order_code']);
					$this->Session->setFlash(__('The production order has been saved.'), 'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('The production order could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				}
			}
		} 
		else {
			$options = array(
				'conditions' => array(
					'ProductionOrder.id' => $id,
				),
				'contain'=>array(
					'ProductionOrderProduct'=>array(
						'Product',
						'ProductionOrderProductOperationLocation',
						'ProductionOrderProductDepartment'=>array(
							'order'=>'ProductionOrderProductDepartment.rank',
						),
					),
					'SalesOrder'=>array(
						'ProductionOrder',
						'SalesOrderProduct'=>array(
							'ProductionOrderProduct',
						),
					),
				),
			);
			$this->request->data = $this->ProductionOrder->find('first', $options);
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id']>0 && $this->request->data['ProductionOrderProduct'][$i]['product_quantity']>0){
					$locationValues=[];
					if (!empty($this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation'])){
						for ($loc=0;$loc<count($this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation']);$loc++){
							$locationValues[]=$this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation'][$loc]['operation_location_id'];
						}
					}
					$this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation']['locationValues']=$locationValues;
					$requestProducts[]['ProductionOrderProduct']=$this->request->data['ProductionOrderProduct'][$i];
				}
			}
		}
		
		$this->set(compact('requestProducts'));
		//pr($requestProducts);
		
		if (!empty($this->request->data['SalesOrder']['ProductionOrder'])){
			$bool_first_production_order=false;
		}
		else {
			$bool_first_production_order=true;
		}
		$this->set(compact('bool_first_production_order'));
		
		$salesOrders = $this->ProductionOrder->SalesOrder->find('list',array(
			'conditions'=>array(
				'bool_annulled'=>false,
				'bool_authorized'=>true,
			),
		));
		$departments = $this->Department->find('list');
		$this->set(compact('salesOrders', 'departments'));
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$operationLocations=$this->OperationLocation->find('list');
		$this->set(compact('operationLocations'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}
	
	public function add($salesOrderId=0) {
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('Product');
		$this->loadModel('OperationLocation');
		$this->loadModel('ProductionOrderRemark');
		$this->loadModel('ProductionOrderProduct');
		$this->loadModel('ProductionOrderProductDepartment');
		$this->loadModel('ProductionOrderProductOperationLocation');
		$this->loadModel('Department');
		
		$this->Product->recursive=-1;
		
		$productsForSalesOrder=[];
		if ($salesOrderId>0){
			$productConditions=array(
				'SalesOrderProduct.sales_order_id'=>$salesOrderId,
				'SalesOrderProduct.bool_no_production'=>false,
			);
			
			$productsForSalesOrder=$this->SalesOrderProduct->find('all',array(
				'fields'=>array(
					'SalesOrderProduct.id',
					'SalesOrderProduct.product_id',
					'SalesOrderProduct.product_description',
					'SalesOrderProduct.product_quantity',
				),
				'conditions'=>$productConditions,
			));
		}
		
		$requestProducts=[];
		if ($this->request->is('post')) {
			$salesOrderId=$this->request->data['ProductionOrder']['sales_order_id'];
			
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id']>0&&$this->request->data['ProductionOrderProduct'][$i]['product_quantity']>0){
					$requestProducts[]['ProductionOrderProduct']=$this->request->data['ProductionOrderProduct'][$i];
				}
			}
			
			$productionOrderDateArray=$this->request->data['ProductionOrder']['production_order_date'];
			//pr($productionOrderDateArray);
			$productionOrderDateString=$productionOrderDateArray['year'].'-'.$productionOrderDateArray['month'].'-'.$productionOrderDateArray['day'];
			$productionOrderDate=date( "Y-m-d", strtotime($productionOrderDateString));
			
			$boolRemarkOK=true;
			$boolDepartmentsOK=true;
			
			$warning="";
			
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id']>0&&$this->request->data['ProductionOrderProduct'][$i]['product_quantity']>0){
					$boolDepartmentPresentForProduct=false;
					foreach ($this->request->data['ProductionOrderProduct'][$i]['departments'] as $department){
						if ($department['department_id']){
							$boolDepartmentPresentForProduct=true;
						}
					}
					if (!$boolDepartmentPresentForProduct){
						$boolDepartmentsOK=false;
						$relatedProduct=$this->Product->find('first',array('conditions'=>array('Product.id'=>$this->request->data['ProductionOrderProduct'][$i]['product_id'])));
						$warning.="El producto  ".$relatedProduct['Product']['name']." con cantidad ".$this->request->data['ProductionOrderProduct'][$i]['product_quantity']." no tiene asociado un departamento.  Por favor corregir. <br/>";
					}
				}
			}
			echo "bool departments ok is ".$boolDepartmentsOK."<br/>";
			
			if ($productionOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de producción no puede estar en el futuro!  No se guardó la orden de producción.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolRemarkOK){
				$this->Session->setFlash($warning, 'default',array('class' => 'error-message'));
			}
			elseif (!$boolDepartmentsOK){
				$this->Session->setFlash($warning, 'default',array('class' => 'error-message'));
			}
			else {			
				$datasource=$this->ProductionOrder->getDataSource();
				$datasource->begin();
				try {
					if ($this->request->data['ProductionOrder']['bool_annulled']){
						$this->ProductionOrder->create();
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando la orden de producción";
							//pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						} 
						$productionOrderId=$this->ProductionOrder->id;
						
						$productionOrderRemarkArray=[];
						$productionOrderRemarkArray['ProductionOrderRemark']['user_id']=$this->Auth->User('id');
						$productionOrderRemarkArray['ProductionOrderRemark']['production_order_id']=$productionOrderId;
						$productionOrderRemarkArray['ProductionOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$productionOrderRemarkArray['ProductionOrderRemark']['remark_text']="Se anuló la orden de producción";
						$productionOrderRemarkArray['ProductionOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->ProductionOrderRemark->create();
						if (!$this->ProductionOrderRemark->save($productionOrderRemarkArray)) {
							echo "Problema guardando las remarcas para la orden de producción";
							pr($this->validateErrors($this->ProductionOrderRemark));
							throw new Exception();
						}
					}
					else {
						$this->ProductionOrder->create();
						
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando la orden de producción";
							pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						}
						$productionOrderId=$this->ProductionOrder->id;
						if (!empty($this->request->data['Document']['url_doc'][0]['tmp_name'])){
							$docOK=$this->uploadFiles('productionorderdocuments/'.$productionOrderId,$this->request->data['Document']['url_doc']);
							//echo "doc OK<br/>";
							//pr($docOK);
							if (array_key_exists('urls',$docOK)){
								$this->request->data['ProductionOrder']['url_doc']=$docOK['urls'][0];
							}
						}
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando el documento de diseño para la orden de producción";
							pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						}
						
						foreach ($this->request->data['ProductionOrderProduct'] as $productionOrderProduct){
							if ($productionOrderProduct['product_id']>0){
								//pr($productionOrderProduct);
								$productArray=[];
								$productArray['ProductionOrderProduct']['production_order_id']=$productionOrderId;
								$productArray['ProductionOrderProduct']['product_id']=$productionOrderProduct['product_id'];
								$productArray['ProductionOrderProduct']['product_description']=$productionOrderProduct['product_description'];
								$productArray['ProductionOrderProduct']['product_instruction']=$productionOrderProduct['product_instruction'];
								$productArray['ProductionOrderProduct']['product_quantity']=$productionOrderProduct['product_quantity'];
								$productArray['ProductionOrderProduct']['sales_order_product_id']=$productionOrderProduct['sales_order_product_id'];
								$this->ProductionOrderProduct->create();
								if (!$this->ProductionOrderProduct->save($productArray)) {
									echo "Problema guardando los productos de la orden de producción";
									pr($this->validateErrors($this->ProductionOrderProduct));
									throw new Exception();
								}
								
								$productionOrderProductId=$this->ProductionOrderProduct->id;
								
								$salesOrderProductArray=[];
								$salesOrderProductArray['SalesOrderProduct']['id']=$productionOrderProduct['sales_order_product_id'];
								$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PURCHASE;
								$this->SalesOrderProduct->id=$productionOrderProduct['sales_order_product_id'];
								if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
									echo "Problema cambiando el estado de los productos de la orden de venta";
									pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
								
								foreach ($productionOrderProduct['operation_location_id'] as $locationId){
									if ($locationId>0){
										$this->ProductionOrderProductOperationLocation->create();
										$locationArray=[];
										$locationArray['ProductionOrderProductOperationLocation']['production_order_product_id']=$productionOrderProductId;
										$locationArray['ProductionOrderProductOperationLocation']['operation_location_id']=$locationId;
										if (!$this->ProductionOrderProductOperationLocation->save($locationArray)) {
											echo "Problema guardando los lugares de las operaciones del producto";
											pr($this->validateErrors($this->ProductionOrderProductOperationLocation));
											throw new Exception();
										}
									}
								}
								
								$rank=0;
								foreach ($productionOrderProduct['departments'] as $department){
									//pr($department);
									if ($department['department_id']>0){
										$this->ProductionOrderProductDepartment->create();
										$departmentArray=[];
										$departmentArray['ProductionOrderProductDepartment']['production_order_product_id']=$productionOrderProductId;
										$departmentArray['ProductionOrderProductDepartment']['department_id']=$department['department_id'];
										$departmentArray['ProductionOrderProductDepartment']['rank']=$rank;
										if (!$this->ProductionOrderProductDepartment->save($departmentArray)) {
											echo "Problema guardando los departamentos para el producto";
											pr($this->validateErrors($this->ProductionOrderProductDepartment));
											throw new Exception();
										}
										$rank++;
									}
								}
							}
						}
													
						$productionOrderRemark=$this->request->data['ProductionOrderRemark'];
						//pr($quotationRemark);
						$remarkArray=[];
						$remarkArray['ProductionOrderRemark']['user_id']=$productionOrderRemark['user_id'];
						$remarkArray['ProductionOrderRemark']['production_order_id']=$productionOrderId;
						$remarkArray['ProductionOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
						if (!empty($this->request->data['ProductionOrderRemark']['remark_text'])){
							$remarkArray['ProductionOrderRemark']['remark_text']=$productionOrderRemark['remark_text'];
						}
						else {
							$remarkArray['ProductionOrderRemark']['remark_text']="Orden de Producción creada";
						}
						$this->ProductionOrderRemark->create();
						if (!$this->ProductionOrderRemark->save($remarkArray)) {
							echo "Problema guardando las remarcas para la orden de producción";
							pr($this->validateErrors($this->ProductionOrderRemark));
							throw new Exception();
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->ProductionOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se creó la orden de producción ".$this->request->data['ProductionOrder']['production_order_code']);
					$this->Session->setFlash(__('The production order has been saved.'), 'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('The production order could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		$this->set(compact('requestProducts'));
		$this->set(compact('sales_order_id','department_id','bool_first_production_order'));
		//pr($productsForSalesOrder);
		$this->set(compact('productsForSalesOrder'));
		
		$excludedSalesOrderIds=$this->ProductionOrder->find('list',array(
			'fields'=>array('ProductionOrder.sales_order_id'),
			'conditions'=>array(
				'ProductionOrder.bool_annulled'=>false,
			),
		));
		
		$salesOrders = $this->ProductionOrder->SalesOrder->find('list',array(
			'conditions'=>array(
				'bool_annulled'=>false,
				'bool_authorized'=>true,
				'bool_completely_delivered'=>false,
				'SalesOrder.id !='=>$excludedSalesOrderIds,
			),
      'order'=>'SalesOrder.sales_order_code',
		));
		$this->set(compact('salesOrders'));
		
		$products=$this->Product->find('list',array('order'=>'Product.name'));
		$this->set(compact('products'));
		
		$operationLocations=$this->OperationLocation->find('list',array('order'=>'OperationLocation.name'));
		$this->set(compact('operationLocations'));
		
		$departments=$this->Department->find('list',array('order'=>'Department.name'));
		$this->set(compact('departments'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
	}

  public function edit($id = null) {
		if (!$this->ProductionOrder->exists($id)) {
			throw new NotFoundException(__('Invalid production order'));
		}
		
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('Product');
		$this->loadModel('OperationLocation');
		$this->loadModel('Department');
		$this->loadModel('ProductionOrderRemark');
		$this->loadModel('ProductionOrderProduct');
		$this->loadModel('ProductionOrderProductOperationLocation');
		$this->loadModel('ProductionOrderProductDepartment');
		
		$this->Product->recursive=-1;
		
		$requestProducts=[];
		
		if ($this->request->is(array('post', 'put'))) {
			$salesOrderId=$this->request->data['ProductionOrder']['sales_order_id'];
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id']>0&&$this->request->data['ProductionOrderProduct'][$i]['product_quantity']>0){
					pr($this->request->data['ProductionOrderProduct'][$i]);
					
					$locationValues=[];
					if (!empty($this->request->data['ProductionOrderProduct'][$i]['operation_location_id'])){
						for ($loc=0;$loc<count($this->request->data['ProductionOrderProduct'][$i]['operation_location_id']);$loc++){
							$locationValues[]=$this->request->data['ProductionOrderProduct'][$i]['operation_location_id'][$loc];
						}
					}
					$this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation']['locationValues']=$locationValues;
					
					$departmentsInRequest=[];
					foreach ($this->request->data['ProductionOrderProduct'][$i]['departments'] as $department){
						if (!empty($department['department_id'])){
							$departmentsInRequest[]=array(
								'department_id'=>$department['department_id'],
								'rank'=>count($departmentsInRequest)-1,
							);
						}
					}
					$this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductDepartment']=$departmentsInRequest;
					$requestProducts[]['ProductionOrderProduct']=$this->request->data['ProductionOrderProduct'][$i];
				}
			}
			
			$productionOrderDateArray=$this->request->data['ProductionOrder']['production_order_date'];
			//pr($productionOrderDateArray);
			$productionOrderDateString=$productionOrderDateArray['year'].'-'.$productionOrderDateArray['month'].'-'.$productionOrderDateArray['day'];
			$productionOrderDate=date( "Y-m-d", strtotime($productionOrderDateString));
			
			$boolDepartmentsOK=true;
			$warning="";
			
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id']>0&&$this->request->data['ProductionOrderProduct'][$i]['product_quantity']>0){
					$boolDepartmentPresentForProduct=false;
					foreach ($this->request->data['ProductionOrderProduct'][$i]['departments'] as $department){
						if ($department['department_id']){
							$boolDepartmentPresentForProduct=true;
						}
					}
					if (!$boolDepartmentPresentForProduct){
						$boolDepartmentsOK=false;
						$relatedProduct=$this->Product->find('first',array('conditions'=>array('Product.id'=>$this->request->data['ProductionOrderProduct'][$i]['product_id'])));
						$warning.="El producto  ".$relatedProduct['Product']['name']." con cantidad ".$this->request->data['ProductionOrderProduct'][$i]['product_quantity']." no tiene asociado un departamento.  Por favor corregir. <br/>";
					}
				}
			}
			//echo "bool departments ok is ".$boolDepartmentsOK."<br/>";
			/*
			$boolRemarkOK=true;
			$warning="";
			if (!$bool_first_production_order){
				if (empty($this->request->data['ProductionOrderRemark']['remark_text'])){
					$boolRemarkOK=false;
					$warning="Esto es una orden de producción registrado manualmente.  Se debe registrar una remarca para esta orden de producción.  La orden de producción no se guardó.";
				}
			}
			*/
			if ($productionOrderDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de orden de producción no puede estar en el futuro!  No se guardó la orden de producción.'), 'default',array('class' => 'error-message'));
			}
			//elseif (!$boolRemarkOK){
			//	$this->Session->setFlash($warning, 'default',array('class' => 'error-message'));
			//}
			elseif (!$boolDepartmentsOK){
				$this->Session->setFlash($warning, 'default',array('class' => 'error-message'));
			}
			else {			
				$datasource=$this->Product->getDataSource();
				$datasource->begin();
				try {
					//pr($this->request->data);
					$previousProductionOrderProducts=$this->ProductionOrderProduct->find('all',array(
						'fields'=>array('ProductionOrderProduct.id','ProductionOrderProduct.sales_order_product_id'),
						'conditions'=>array(
							'ProductionOrderProduct.production_order_id'=>$id,
						),
						'contain'=>array(
							'ProductionOrderProductOperationLocation',
							'ProductionOrderProductDepartment',
						),
					));
					//pr($previousProductionOrderProducts);
					if (!empty($previousProductionOrderProducts)){
						foreach ($previousProductionOrderProducts as $previousProductionOrderProduct){
							if (!empty($previousProductionOrderProduct['ProductionOrderProductOperationLocation'])){
								foreach ($previousProductionOrderProduct['ProductionOrderProductOperationLocation'] as $location){
									$this->ProductionOrderProductOperationLocation->id=$location['id'];
									$this->ProductionOrderProductOperationLocation->delete($location['id']);
								}
							}
							if (!empty($previousProductionOrderProduct['ProductionOrderProductDepartment'])){
								foreach ($previousProductionOrderProduct['ProductionOrderProductDepartment'] as $department){
									$this->ProductionOrderProductDepartment->id=$department['id'];
									$this->ProductionOrderProductDepartment->delete($department['id']);
								}
							}								
							
							$this->SalesOrderProduct->id=$previousProductionOrderProduct['ProductionOrderProduct']['sales_order_product_id'];
							$salesOrderProductArray=[];
							$salesOrderProductArray['SalesOrderProduct']['id']=$previousProductionOrderProduct['ProductionOrderProduct']['sales_order_product_id'];
							$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AUTHORIZED;
							$this->SalesOrderProduct->id=$previousProductionOrderProduct['ProductionOrderProduct']['sales_order_product_id'];
							if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
								echo "Problema cambiando el estado de los productos de la orden de venta";
								pr($this->validateErrors($this->SalesOrderProduct));
								throw new Exception();
							}
							
							$this->ProductionOrderProduct->id=$previousProductionOrderProduct['ProductionOrderProduct']['id'];
							$this->ProductionOrderProduct->delete($previousProductionOrderProduct['ProductionOrderProduct']['id']);
						}
					}
					
					if ($this->request->data['ProductionOrder']['bool_annulled']){
						$this->ProductionOrder->id=$id;
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando la orden de producción";
							//pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						} 
						$productionOrderId=$this->ProductionOrder->id;
						
						$productionOrderRemarkArray=[];
						$productionOrderRemarkArray['ProductionOrderRemark']['user_id']=$this->Auth->User('id');
						$productionOrderRemarkArray['ProductionOrderRemark']['purchase_order_id']=$productionOrderId;
						$productionOrderRemarkArray['ProductionOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$productionOrderRemarkArray['ProductionOrderRemark']['remark_text']="Se anuló la orden de producción";
						$productionOrderRemarkArray['ProductionOrderRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->ProductionOrderRemark->create();
						if (!$this->ProductionOrderRemark->save($productionOrderRemarkArray)) {
							echo "Problema guardando las remarcas para la orden de producción";
							pr($this->validateErrors($this->ProductionOrderRemark));
							throw new Exception();
						}
					}
					else {
						$this->ProductionOrder->id=$id;
						
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando la orden de producción";
							pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						}
						$productionOrderId=$this->ProductionOrder->id;
						if (!empty($this->request->data['Document']['url_doc'][0]['tmp_name'])){
							$docOK=$this->uploadFiles('productionorderdocuments/'.$productionOrderId,$this->request->data['Document']['url_doc']);
							//echo "image OK<br/>";
							//pr($imageOK);
							if (array_key_exists('urls',$docOK)){
								$this->request->data['ProductionOrder']['url_doc']=$docOK['urls'][0];
							}
						}
						if (!$this->ProductionOrder->save($this->request->data)) {
							echo "Problema guardando el documento de diseño para la orden de producción";
							pr($this->validateErrors($this->ProductionOrder));
							throw new Exception();
						}
						
						foreach ($this->request->data['ProductionOrderProduct'] as $productionOrderProduct){
							if ($productionOrderProduct['product_id']>0){
								//pr($quotationProduct);
								$productArray=[];
								$productArray['ProductionOrderProduct']['production_order_id']=$productionOrderId;
								$productArray['ProductionOrderProduct']['product_id']=$productionOrderProduct['product_id'];
								$productArray['ProductionOrderProduct']['product_description']=$productionOrderProduct['product_description'];
								$productArray['ProductionOrderProduct']['product_instruction']=$productionOrderProduct['product_instruction'];
								$productArray['ProductionOrderProduct']['product_quantity']=$productionOrderProduct['product_quantity'];
								$productArray['ProductionOrderProduct']['sales_order_product_id']=$productionOrderProduct['sales_order_product_id'];
								$this->ProductionOrderProduct->create();
								if (!$this->ProductionOrderProduct->save($productArray)) {
									echo "Problema guardando los productos de la orden de producción";
									pr($this->validateErrors($this->ProductionOrderProduct));
									throw new Exception();
								}
								
								$productionOrderProductId=$this->ProductionOrderProduct->id;
								
								$salesOrderProductArray=[];
								$salesOrderProductArray['SalesOrderProduct']['id']=$productionOrderProduct['sales_order_product_id'];
								$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PURCHASE;
								$this->SalesOrderProduct->id=$productionOrderProduct['sales_order_product_id'];
								if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
									echo "Problema cambiando el estado de los productos de la orden de venta";
									pr($this->validateErrors($this->SalesOrderProduct));
									throw new Exception();
								}
								
								foreach ($productionOrderProduct['operation_location_id'] as $locationId){
									if ($locationId>0){
										$this->ProductionOrderProductOperationLocation->create();
										$locationArray=[];
										$locationArray['ProductionOrderProductOperationLocation']['production_order_product_id']=$productionOrderProductId;
										$locationArray['ProductionOrderProductOperationLocation']['operation_location_id']=$locationId;
										if (!$this->ProductionOrderProductOperationLocation->save($locationArray)) {
											echo "Problema guardando los lugares de las operaciones del producto";
											pr($this->validateErrors($this->ProductionOrderProductOperationLocation));
											throw new Exception();
										}
									}
								}
								
								$rank=0;
								foreach ($productionOrderProduct['departments'] as $department){
									pr($department);
									if ($department['department_id']>0){
										$this->ProductionOrderProductDepartment->create();
										$departmentArray=[];
										$departmentArray['ProductionOrderProductDepartment']['production_order_product_id']=$productionOrderProductId;
										$departmentArray['ProductionOrderProductDepartment']['department_id']=$department['department_id'];
										$departmentArray['ProductionOrderProductDepartment']['rank']=$rank;
										if (!$this->ProductionOrderProductDepartment->save($departmentArray)) {
											echo "Problema guardando los departamentos para el producto";
											pr($this->validateErrors($this->ProductionOrderProductDepartment));
											throw new Exception();
										}
										$rank++;
									}
								}
							}
						}
							
						if (!empty($this->request->data['ProductionOrderRemark']['remark_text'])){
							$productionOrderRemark=$this->request->data['ProductionOrderRemark'];
							//pr($quotationRemark);
							$remarkArray=[];
							$remarkArray['ProductionOrderRemark']['user_id']=$productionOrderRemark['user_id'];
							$remarkArray['ProductionOrderRemark']['production_order_id']=$productionOrderId;
							$remarkArray['ProductionOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
							$remarkArray['ProductionOrderRemark']['remark_text']=$productionOrderRemark['remark_text'];
							$this->ProductionOrderRemark->create();
							if (!$this->ProductionOrderRemark->save($remarkArray)) {
								echo "Problema guardando las remarcas para la orden de producción";
								pr($this->validateErrors($this->ProductionOrderRemark));
								throw new Exception();
							}
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->ProductionOrder->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se creó la orden de producción ".$this->request->data['ProductionOrder']['production_order_code']);
					$this->Session->setFlash(__('The production order has been saved.'), 'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('The production order could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				}
			}
		} 
		else {
			$options = array(
				'conditions' => array(
					'ProductionOrder.id' => $id,
				),
				'contain'=>array(
					'ProductionOrderProduct'=>array(
						'Product',
						'ProductionOrderProductOperationLocation',
						'ProductionOrderProductDepartment'=>array(
							'order'=>'ProductionOrderProductDepartment.rank',
						),
					),
					'SalesOrder'=>array(
						'ProductionOrder',
						'SalesOrderProduct'=>array(
							'ProductionOrderProduct',
						),
					),
				),
			);
			$this->request->data = $this->ProductionOrder->find('first', $options);
			for ($i=0;$i<count($this->request->data['ProductionOrderProduct']);$i++){
				if ($this->request->data['ProductionOrderProduct'][$i]['product_id']>0 && $this->request->data['ProductionOrderProduct'][$i]['product_quantity']>0){
					$locationValues=[];
					if (!empty($this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation'])){
						for ($loc=0;$loc<count($this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation']);$loc++){
							$locationValues[]=$this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation'][$loc]['operation_location_id'];
						}
					}
					$this->request->data['ProductionOrderProduct'][$i]['ProductionOrderProductOperationLocation']['locationValues']=$locationValues;
					$requestProducts[]['ProductionOrderProduct']=$this->request->data['ProductionOrderProduct'][$i];
				}
			}
		}
		
		$this->set(compact('requestProducts'));
		//pr($requestProducts);
		
		if (!empty($this->request->data['SalesOrder']['ProductionOrder'])){
			$bool_first_production_order=false;
		}
		else {
			$bool_first_production_order=true;
		}
		$this->set(compact('bool_first_production_order'));
		
		$salesOrders = $this->ProductionOrder->SalesOrder->find('list',array(
			'conditions'=>array(
				'bool_annulled'=>false,
				'bool_authorized'=>true,
			),
		));
		$departments = $this->Department->find('list');
		$this->set(compact('salesOrders', 'departments'));
		
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$operationLocations=$this->OperationLocation->find('list');
		$this->set(compact('operationLocations'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionOrder->id = $id;
		if (!$this->ProductionOrder->exists()) {
			throw new NotFoundException(__('Invalid production order'));
		}
		
		$this->loadModel('ProductionOrderProductOperationLocation');
		$this->loadModel('ProductionOrderProductDepartment');
		$this->loadModel('SalesOrderProduct');
		
		//$this->request->allowMethod('post', 'delete');
		$productionOrder=$this->ProductionOrder->find('first',array(
			'conditions'=>array(
				'ProductionOrder.id'=>$id,
			),
			'contain'=>array(
				'ProductionOrderProduct'=>array(
					'fields'=>array(
						'ProductionOrderProduct.id',
						'ProductionOrderProduct.sales_order_product_id'
					),
					'PurchaseOrderProduct'=>array(
						'PurchaseOrder',
					),
					'ProductionOrderProductOperationLocation',
					'ProductionOrderProductDepartment',
				),
				'ProductionOrderRemark',
			),
		));
		
		$flashMessage="";
		$boolDeletionAllowed=true;
		if (!empty($productionOrder['PurchaseOrderProduct'])){
			foreach ($productionOrder['PurchaseOrderProduct'] as $productionOrderProduct){
				if (count($productionOrderProduct['PurchaseOrderProduct'])>0){
					$boolDeletionAllowed=false;
					$flashMessage.="Esta orden de producción tiene ordenes de compra correspondientes.  Para poder eliminar la orden de producción, primero hay que eliminar o modificar las ordenes de compra ";
					if (count($productionOrderProduct['PurchaseOrderProduct'])==1){
						$flashMessage.=$productionOrderProduct['PurchaseOrderProduct']['PurchaseOrder'][0]['purchase_order_code'].".";
					}
					else {
						for ($i=0;$i<count($productionOrderProduct['PurchaseOrderProduct']);$i++){
							$flashMessage.=$productionOrderProduct['PurchaseOrderProduct']['PurchaseOrder'][$i]['purchase_order_code'];
							if ($i==count($productionOrderProduct['PurchaseOrderProduct'])-1){
								$flashMessage.=".";
							}
							else {
								$flashMessage.=" y ";
							}
						}
					}
				}
			}
		}
		
		/*
		if (count($productionOrder['ProductionProcess'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Esta orden de compra tiene procesos de producción correspondientes.  Para poder eliminar la orden de compra, primero hay que eliminar o modificar l0s procesos de producción ";
			if (count($productionOrder['ProductionProcess'])==1){
				$flashMessage.=$productionOrder['ProductionProcess'][0]['production_process_code'].".";
			}
			else {
				for ($i=0;$i<count($productionOrder['ProductionProcess']);$i++){
					$flashMessage.=$productionOrder['ProductionProcess'][$i]['production_process_code'];
					if ($i==count($productionOrder['ProductionProcess'])-1){
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
			$flashMessage.=" No se eliminó la orden de producción.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			$datasource=$this->ProductionOrder->getDataSource();
			$datasource->begin();	
			try {
				//delete all products, remarks and other costs
				foreach ($productionOrder['ProductionOrderProduct'] as $productionOrderProduct){
					if (!empty($productionOrderProduct['ProductionOrderProductOperationLocation'])){
						foreach ($productionOrderProduct['ProductionOrderProductOperationLocation'] as $location){
							$this->ProductionOrderProductOperationLocation->id=$location['id'];
							$this->ProductionOrderProductOperationLocation->delete($location['id']);
						}
					}	
					if (!empty($productionOrderProduct['ProductionOrderProductDepartment'])){
						foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $department){
							$this->ProductionOrderProductDepartment->id=$department['id'];
							$this->ProductionOrderProductDepartment->delete($department['id']);
						}
					}
					
					
					$salesOrderProductArray=[];
					$salesOrderProductArray['SalesOrderProduct']['id']=$productionOrderProduct['sales_order_product_id'];
					$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AUTHORIZED;
					$this->SalesOrderProduct->id=$productionOrderProduct['sales_order_product_id'];
					if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
						echo "Problema cambiando el estado de los productos de la orden de venta";
						pr($this->validateErrors($this->SalesOrderProduct));
						throw new Exception();
					}
					
					if (!$this->ProductionOrder->ProductionOrderProduct->delete($productionOrderProduct['id'])) {
						echo "Problema al eliminar el producto de la orden de producción";
						pr($this->validateErrors($this->ProductionOrder->ProductionOrderProduct));
						throw new Exception();
					}
				}
				foreach ($productionOrder['ProductionOrderRemark'] as $productionOrderRemark){
					if (!$this->ProductionOrder->ProductionOrderRemark->delete($productionOrderRemark['id'])) {
						echo "Problema al eliminar la remarca de la orden de producción";
						pr($this->validateErrors($this->ProductionOrder->ProductionOrderRemark));
						throw new Exception();
					}
				}
				
				if (!$this->ProductionOrder->delete($id)) {
					echo "Problema al eliminar la orden de producción";
					pr($this->validateErrors($this->ProductionOrder));
					throw new Exception();
				}
						
				$datasource->commit();
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=[];
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$productionOrder['ProductionOrder']['id'];
				$deletionArray['Deletion']['reference']=$productionOrder['ProductionOrder']['production_order_code'];
				$deletionArray['Deletion']['type']='ProductionOrder';
				$this->Deletion->save($deletionArray);
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó la orden de producción número ".$productionOrder['ProductionOrder']['production_order_code']);
						
				$this->Session->setFlash(__('Se eliminó la orden de producción.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía eliminar la orden de producción.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}
	
	public function annul($id = null) {
		$this->ProductionOrder->id = $id;
		if (!$this->ProductionOrder->exists()) {
			throw new NotFoundException(__('Invalid production order'));
		}
		
		$this->loadModel('ProductionOrderProductOperationLocation');
		$this->loadModel('ProductionOrderProductDepartment');
		$this->loadModel('SalesOrderProduct');
		
		$productionOrder=$this->ProductionOrder->find('first',array(
			'conditions'=>array(
				'ProductionOrder.id'=>$id,
			),
			'contain'=>array(
				'ProductionOrderProduct'=>array(
					'PurchaseOrderProduct'=>array(
						'PurchaseOrder',
					),
					'ProductionOrderProductOperationLocation',
					'ProductionOrderProductDepartment',
				),
				'ProductionOrderRemark',
			),
		));
		$flashMessage="";
		$boolAnnulmentAllowed=true;
		foreach ($productionOrder['ProductionOrderProduct'] as $productionOrderProduct){
			if (count($productionOrderProduct['PurchaseOrderProduct'])>0){
				$boolAnnulmentAllowed=false;
				$flashMessage.="Esta orden de producción tiene ordenes de compra correspondientes.  Para poder anular la orden de producción, primero hay que eliminar o modificar las ordenes de compra ";
				if (count($productionOrderProduct['PurchaseOrderProduct'])==1){
					$flashMessage.=$productionOrderProduct['PurchaseOrderProduct']['PurchaseOrder'][0]['purchase_order_code'].".";
				}
				else {
					for ($i=0;$i<count($productionOrderProduct['PurchaseOrderProduct']);$i++){
						$flashMessage.=$productionOrderProduct['PurchaseOrderProduct']['PurchaseOrder'][$i]['purchase_order_code'];
						if ($i==count($productionOrderProduct['PurchaseOrderProduct'])-1){
							$flashMessage.=".";
						}
						else {
							$flashMessage.=" y ";
						}
					}
				}
			}
		}
		
		/*
		if (count($productionOrder['ProductionProcess'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Esta orden de compra tiene procesos de producción correspondientes.  Para poder eliminar la orden de compra, primero hay que eliminar o modificar l0s procesos de producción ";
			if (count($productionOrder['ProductionProcess'])==1){
				$flashMessage.=$productionOrder['ProductionProcess'][0]['production_process_code'].".";
			}
			else {
				for ($i=0;$i<count($productionOrder['ProductionProcess']);$i++){
					$flashMessage.=$productionOrder['ProductionProcess'][$i]['production_process_code'];
					if ($i==count($productionOrder['ProductionProcess'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		*/
		if (!$boolAnnulmentAllowed){
			$flashMessage.=" No se anuló la orden de producción.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			$datasource=$this->ProductionOrder->getDataSource();
			$datasource->begin();	
			try {
				//delete all products, remarks and other costs
				foreach ($productionOrder['ProductionOrderProduct'] as $productionOrderProduct){
					if (!empty($productionOrderProduct['ProductionOrderProductOperationLocation'])){
						foreach ($productionOrderProduct['ProductionOrderProductOperationLocation'] as $location){
							$this->ProductionOrderProductOperationLocation->id=$location['id'];
							$this->ProductionOrderProductOperationLocation->delete($location['id']);
						}
					}	
					if (!empty($productionOrderProduct['ProductionOrderProductDepartment'])){
						foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $department){
							$this->ProductionOrderProductDepartment->id=$department['id'];
							$this->ProductionOrderProductDepartment->delete($department['id']);
						}
					}
					
					$salesOrderProductArray=[];
					$salesOrderProductArray['SalesOrderProduct']['id']=$productionOrderProduct['sales_order_product_id'];
					$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AUTHORIZED;
					$this->SalesOrderProduct->id=$productionOrderProduct['sales_order_product_id'];
					if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
						echo "Problema cambiando el estado de los productos de la orden de venta";
						pr($this->validateErrors($this->SalesOrderProduct));
						throw new Exception();
					}
					
					if (!$this->ProductionOrder->ProductionOrderProduct->delete($productionOrderProduct['id'])) {
						echo "Problema al eliminar el producto de la orden de producción";
						pr($this->validateErrors($this->ProductionOrder->ProductionOrderProduct));
						throw new Exception();
					}
				}
				/*
				foreach ($productionOrder['ProductionOrderRemark'] as $productionOrderRemark){
					if (!$this->ProductionOrder->ProductionOrderRemark->delete($productionOrderRemark['id'])) {
						echo "Problema al eliminar la remarca de la orden de producción";
						pr($this->validateErrors($this->ProductionOrder->ProductionOrderRemark));
						throw new Exception();
					}
				}
				*/
				
				$this->ProductionOrder->id=$id;
				$productionOrderArray=[];
				$productionOrderArray['ProductionOrder']['id']=$id;
				$productionOrderArray['ProductionOrder']['bool_annulled']=true;
				if (!$this->ProductionOrder->save($productionOrderArray)) {
					echo "Problema al anular la orden de producción";
					pr($this->validateErrors($this->ProductionOrder));
					throw new Exception();
				}
								
				$this->loadModel('ProductionOrderRemark');
				$remarkArray=[];
				$remarkArray['ProductionOrderRemark']['user_id']=$this->Auth->User('id');
				$remarkArray['ProductionOrderRemark']['production_order_id']=$id;
				$remarkArray['ProductionOrderRemark']['remark_datetime']=date('Y-m-d H:i:s');
				$remarkArray['ProductionOrderRemark']['remark_text']="Se anuló la orden de producción número ".$productionOrder['ProductionOrder']['production_order_code'];
				$this->ProductionOrderRemark->create();
				if (!$this->ProductionOrderRemark->save($remarkArray)) {
					echo "Problema guardando las remarcas para la orden de producción";
					pr($this->validateErrors($this->ProductionOrderRemark));
					throw new Exception();
				}
				$datasource->commit();
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se anuló la orden de producción número ".$productionOrder['ProductionOrder']['production_order_code']);
						
				$this->Session->setFlash(__('Se anuló la orden de producción.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía anular la orden de producción.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}
	
	public function getnewproductionordercode(){
		$this->layout= "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$this->loadModel('SalesOrder');
		
		$salesOrderId=trim($_POST['sales_order_id']);
		if (!$salesOrderId){
			//throw new NotFoundException(__('Orden de Venta no presente'));
		}
		
		$newProductionOrderCode="";
		$selectedSalesOrder=$this->SalesOrder->find('first',array(
			'conditions'=>array('SalesOrder.id'=>$salesOrderId,),
			'contain'=>array(
				'ProductionOrder',
			)
		));
		$newProductionOrderCode=$selectedSalesOrder['SalesOrder']['sales_order_code'];
		return $newProductionOrderCode;
	}
	
	public function getproductsforsalesorder(){
		$this->layout = "ajax";
		
		$salesOrderId=trim($_POST['sales_order_id']);
		if (!$salesOrderId){
			throw new NotFoundException(__('Orden de Venta no presente'));
		}
		
		$this->loadModel('SalesOrderProduct');
		$productConditions=array(
			'SalesOrderProduct.sales_order_id'=>$salesOrderId,
			'SalesOrderProduct.bool_no_production'=>false,
		);
		
		// $this->InvoiceProduct->virtualFields['total_product_quantity']=0;
		$productsForSalesOrder=$this->SalesOrderProduct->find('all',array(
			'fields'=>array(
				'SalesOrderProduct.product_id','SalesOrderProduct.product_quantity','SalesOrderProduct.product_description',
			),
			'conditions'=>$productConditions,
		));
		//pr($productsForSalesOrder);
		$this->set(compact('productsForSalesOrder'));
		
		$this->loadModel('Product');
		$products=$this->Product->find('list');
		$this->set(compact('products'));
		
		$this->loadModel('OperationLocation');
		$operationLocations=$this->OperationLocation->find('list');
		$this->set(compact('operationLocations'));
		
		$this->loadModel('Department');
		$departments=$this->Department->find('list');
		$this->set(compact('departments'));
	}
}
