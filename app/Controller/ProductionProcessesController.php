<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class ProductionProcessesController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getnewproductionprocesscode');		
	}

	public function index() {
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('SalesOrder');
		$this->loadModel('Quotation');
	
		$this->ProductionProcess->recursive = -1;
		
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
			
			//$user_id=$this->request->data['Report']['user_id'];
			//$currencyId=$this->request->data['Report']['currency_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
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
		$this->set(compact('startDate','endDate'));
		$this->set(compact('currencyId','userId'));
		
		$conditions=array();
		switch ($this->Auth->User('role_id')){
			case ROLE_ADMIN:
			case ROLE_ASSISTANT:
				break;
			case ROLE_SALES_EXECUTIVE:	
				$this->loadModel('Quotation');
				$this->loadModel('SalesOrder');
				$quotationIds=$this->Quotation->find('list',array(
					'fields'=>array('Quotation.id'),
					'conditions'=>array(
						'Quotation.user_id'=>$this->Auth->User('id'),
					),
				));
				$salesOrderIds=$this->SalesOrder->find('list',array(
					'fields'=>array('SalesOrder.id'),
					'conditions'=>array(
						'SalesOrder.quotation_id'=>$quotationIds,
					),
				));
				$salesOrderProductIds=$this->SalesOrderProduct->find('list',array(
					'fields'=>array('SalesOrderProduct.id'),
					'conditions'=>array(
						'SalesOrderProduct.sales_order_id'=>$salesOrderIds,
					),
				));
				$productionProcessIds=$this->ProductionProcessProduct->find('list',array(
					'fields'=>array('ProductionProcessProduct.production_process_id'),
					'conditions'=>array(
						'ProductionProcessProduct.sales_order_product_id'=>$salesOrderProductIds,
					),
				));
				$conditions[]=array('ProductionProcess.id'=>$productionProcessIds);
				break;
			case ROLE_DEPARTMENT_BOSS:
				$this->loadModel('User');
				$departmentBoss=$this->User->find('first',array('conditions'=>array('User.id'=>$this->Auth->User('id'))));
				if (!empty($departmentBoss)){
					$conditions[]=array('ProductionProcess.department_id'=>$departmentBoss['User']['department_id']);
				}
				break;
			case ROLE_OPERATOR:
				//OPERATORS SHOULD NEVER SEE THIS
				$productionProcessIds=array();
				$conditions[]=array('ProductionProcess.id'=>$productionProcessIds);
				break;
		}
		
		$productionProcessCount=$this->ProductionProcess->find('count', array(
			'fields'=>array('ProductionProcess.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(	
				'Department',
			),
			'limit'=>($productionProcessCount!=0?$productionProcessCount:1),
		);

		$productionProcesses = $this->Paginator->paginate('ProductionProcess');
		$this->set(compact('productionProcesses'));
		
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->ProductionProcess->exists($id)) {
			throw new NotFoundException(__('Invalid production process'));
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
			//}
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
		
		$conditions=array('ProductionProcess.id' => $id);
		$productionProcess= $this->ProductionProcess->find('first', array(
			'conditions'=>$conditions,
			'contain'=>array(
				'Department',
				'ProductionProcessProduct'=>array(
					'Product',
					'Operator',
					'Machine',
					'ProductionProcessProductOperationLocation'=>array(
						'OperationLocation',
					),
				),
				'ProductionProcessRemark'=>array(
					'ActionType',
					'User',
				),
			),
		));
		$this->set(compact('productionProcess'));
		//pr($productionProcess);
		
		$filename="Proceso_de_producción_".$productionProcess['ProductionProcess']['production_process_code'];
		$this->set(compact('filename'));
		
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function viewPdf($id = null) {
		if (!$this->ProductionProcess->exists($id)) {
			throw new NotFoundException(__('Invalid production process'));
		}
		
		$conditions=array('ProductionProcess.id' => $id);
		$productionProcess= $this->ProductionProcess->find('first', array(
			'conditions'=>$conditions,
			'contain'=>array(
				'Department',
				'ProductionProcessProduct'=>array(
					'Product',
					'Operator',
					'Machine',
					'ProductionProcessProductOperationLocation'=>array(
						'OperationLocation',
					),
				),
				'ProductionProcessRemark'=>array(
					'ActionType',
					'User',
				),
			),
		));
		$this->set(compact('productionProcess'));
		//pr($productionProcess);
		
		$filename="Proceso_de_producción_".$productionProcess['ProductionProcess']['production_process_code'];
		$this->set(compact('filename'));
		
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function add($department_id=0) {
		$this->loadModel('PurchaseOrderProduct');
		$this->loadModel('Product');
		$this->loadModel('OperationLocation');
		$this->loadModel('ProductionProcessRemark');
		$this->loadModel('ProductionProcessProduct');
		$this->loadModel('ProductionProcessProductOperationLocation');
		$this->loadModel('Machine');
		$this->loadModel('User');
		$this->loadModel('SalesOrderProduct');
		
		$departmentBoss=array();
		if ($this->Auth->User('role_id')==ROLE_DEPARTMENT_BOSS){
			$departmentBoss=$this->User->find('first',array('conditions'=>array('User.id'=>$this->Auth->User('id'))));						
		}
		if (!empty($departmentBoss)){
			$department_id=$departmentBoss['User']['department_id'];
		}
		
		$requestProducts=array();
		if ($this->request->is('post')) {
			$department_id=$this->request->data['ProductionProcess']['department_id'];
			foreach ($this->request->data['ProductionProcessProduct'] as $productionProcessProduct){
				if ($productionProcessProduct['product_id']>0&&$productionProcessProduct['product_quantity']>0){
					$requestProducts[]['ProductionProcessProduct']=$productionProcessProduct;
				}
			}
			
			$productionProcessDateArray=$this->request->data['ProductionProcess']['production_process_date'];
			//pr($productionProcessDateArray);
			$productionProcessDateString=$productionProcessDateArray['year'].'-'.$productionProcessDateArray['month'].'-'.$productionProcessDateArray['day'];
			$productionProcessDate=date( "Y-m-d", strtotime($productionProcessDateString));
			
			if ($productionProcessDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de proceso de producción no puede estar en el futuro!  No se guardó el proceso de producción.'), 'default',array('class' => 'error-message'));
			}
			else {			
				$datasource=$this->ProductionProcess->getDataSource();
				$datasource->begin();
				try {
					if ($this->request->data['ProductionProcess']['bool_annulled']){
						$this->ProductionProcess->create();
						if (!$this->ProductionProcess->save($this->request->data)) {
							echo "Problema guardando el proceso de producción";
							//pr($this->validateErrors($this->ProductionProcess));
							throw new Exception();
						} 
						$production_process_id=$this->ProductionProcess->id;
						
						$productionProcessRemarkArray=array();
						$productionProcessRemarkArray['ProductionProcessRemark']['user_id']=$this->Auth->User('id');
						$productionProcessRemarkArray['ProductionProcessRemark']['production_process_id']=$production_process_id;
						$productionProcessRemarkArray['ProductionProcessRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$productionProcessRemarkArray['ProductionProcessRemark']['remark_text']="Se anuló el proceso de producción";
						$productionProcessRemarkArray['ProductionProcessRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->ProductionProcessRemark->create();
						if (!$this->ProductionProcessRemark->save($productionProcessRemarkArray)) {
							echo "Problema guardando las remarcas para el proceso de producción";
							pr($this->validateErrors($this->ProductionProcessRemark));
							throw new Exception();
						}
					}
					else {
						$this->ProductionProcess->create();
						
						if (!$this->ProductionProcess->save($this->request->data)) {
							echo "Problema guardando el proceso de producción";
							pr($this->validateErrors($this->ProductionProcess));
							throw new Exception();
						}
						$production_process_id=$this->ProductionProcess->id;
						//pr($this->request->data);
						foreach ($this->request->data['ProductionProcessProduct'] as $productionProcessProduct){
							if ($productionProcessProduct['product_id']>0 && $productionProcessProduct['product_quantity']>0){
								// UPDATE THE SALES ORDER PRODUCT 
								//pr($productionProcessProduct);
								$relatedSalesOrderProduct=$this->SalesOrderProduct->find('first',array(
									'conditions'=>array(
										'SalesOrderProduct.id'=>$productionProcessProduct['sales_order_product_id'],
									),
									'contain'=>array(
										'SalesOrder',
									),
								));
								$salesOrderProductQuantity=$relatedSalesOrderProduct['SalesOrderProduct']['product_quantity'];
								if ($productionProcessProduct['product_quantity']==$salesOrderProductQuantity){
									$boolOtherDepartmentsPending=false;
									$salesOrderProduct=$this->SalesOrderProduct->find('first',array(
										'conditions'=>array(
											'SalesOrderProduct.id'=>$productionProcessProduct['sales_order_product_id'],
										),
										'contain'=>array(
											'ProductionOrderProduct'=>array(
												'ProductionOrderProductDepartment',
											),
											'ProductionProcessProduct'=>array(
												'ProductionProcess',
											),
										),
									));
									$productionOrderProductQuantity=0;
									foreach ($salesOrderProduct['ProductionOrderProduct'] as $productionOrderProduct){
										$productionOrderProductQuantity+=$productionOrderProduct['product_quantity'];
										foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $productDepartment){
											$departmentId=$productDepartment['department_id'];
											$boolProductionProcessProductPresent=false;
											if (!empty($salesOrderProduct['ProductionProcessProduct'])){
												foreach ($salesOrderProduct['ProductionProcessProduct'] as $saleProductionProcessProduct){
													if ($saleProductionProcessProduct['ProductionProcess']['department_id']==$departmentId){
														if ($saleProductionProcessProduct['product_quantity']>=$productionOrderProductQuantity){
															$boolProductionProcessProductPresent=true;
														}
													}
												}
											}
											if (!$boolProductionProcessProductPresent){
												$boolOtherDepartmentsPending=true;
											}
										}
									}
									// 20160930 A CHECK IS NEEDED TO VERIFY THAT ALL OPERATIONS FOR THE PRODUCT HAVE BEEN EXECUTED
									// PENDING !!!
									// TAKE INTO ACCOUNT THAT OPERATIONS CAN BE DONE ON SEPARATE MACHINES AS WELL, DISTRIBUTED OVER THIS AND OTHER PRODUCTION PROCESSES
									if (!$boolOtherDepartmentsPending){
										$this->SalesOrderProduct->id=$productionProcessProduct['sales_order_product_id'];
										$salesOrderProductArray=array();
										$salesOrderProductArray['SalesOrderProduct']['id']=$productionProcessProduct['sales_order_product_id'];
										$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_READY_FOR_DELIVERY;
										if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
											echo "Problema cambiando el estado de los productos de la orden de venta";
											pr($this->validateErrors($this->SalesOrderProduct));
											throw new Exception();
										}
									}
								}								
								else {
									// 20160930 A CHECK IS NEEDED TO VERIFY THAT THERE ARE NO OTHER DEPARTMENTS PENDING FOR THIS SAME PRODUCT
									// THIS IS A DIRECT CONSEQUENCE OF THE SEPARATION OF PRODUCTION ORDER PRODUCTS PER DEPARTMENT, AND SHOULD BE CHARGED FOR
									// PENDING !!!
									
									// 20160930 A CHECK IS NEEDED TO VERIFY THAT ALL OPERATIONS FOR THE PRODUCT HAVE BEEN EXECUTED
									// PENDING !!!
									// TAKE INTO ACCOUNT THAT OPERATIONS CAN BE DONE ON SEPARATE MACHINES AS WELL, DISTRIBUTED OVER THIS AND OTHER PRODUCTION PROCESSES
									/*
									if (!$this->SalesOrderProduct->splitSalesOrderProduct($productionProcessProduct['sales_order_product_id'], PRODUCT_STATUS_READY_FOR_DELIVERY,$productionProcessProduct['product_quantity'])){
										echo "Problema con la separación de los productos de la orden de venta";
										throw new Exception();
									}
                  */
									//echo "alrighty the sales order product has been successfully split!!<br/>";
								}
								//pr($quotationProduct);
								$productArray=array();
								$productArray['ProductionProcessProduct']['production_process_id']=$production_process_id;
								$productArray['ProductionProcessProduct']['product_id']=$productionProcessProduct['product_id'];
								$productArray['ProductionProcessProduct']['product_description']=$productionProcessProduct['product_description'];
								$productArray['ProductionProcessProduct']['product_quantity']=$productionProcessProduct['product_quantity'];
								$productArray['ProductionProcessProduct']['operator_id']=$productionProcessProduct['operator_id'];
								$productArray['ProductionProcessProduct']['machine_id']=$productionProcessProduct['machine_id'];
								$productArray['ProductionProcessProduct']['sales_order_id']=$relatedSalesOrderProduct['SalesOrder']['id'];
								$productArray['ProductionProcessProduct']['sales_order_product_id']=$productionProcessProduct['sales_order_product_id'];
								$this->ProductionProcessProduct->create();
								if (!$this->ProductionProcessProduct->save($productArray)) {
									echo "Problema guardando los productos del proceso de producción";
									pr($this->validateErrors($this->ProductionProcessProduct));
									throw new Exception();
								}
								
								$production_process_product_id=$this->ProductionProcessProduct->id;
								//pr($productionProcessProduct);
								foreach ($productionProcessProduct['operation_location_id'] as $locationId){
									$this->ProductionProcessProductOperationLocation->create();
									$locationArray=array();
									$locationArray['ProductionProcessProductOperationLocation']['production_process_product_id']=$production_process_product_id;
									$locationArray['ProductionProcessProductOperationLocation']['operation_location_id']=$locationId;
									if (!$this->ProductionProcessProductOperationLocation->save($locationArray)) {
										echo "Problema guardando los lugares de las operaciones del producto";
										pr($this->validateErrors($this->ProductionProcessProductOperationLocation));
										throw new Exception();
									}
								}
							}
						}
													
						$productionProcessRemark=$this->request->data['ProductionProcessRemark'];
						//pr($productionProcessRemark);
						$remarkArray=array();
						$remarkArray['ProductionProcessRemark']['user_id']=$productionProcessRemark['user_id'];
						$remarkArray['ProductionProcessRemark']['production_process_id']=$production_process_id;
						$remarkArray['ProductionProcessRemark']['remark_datetime']=date('Y-m-d H:i:s');
						if (!empty($this->request->data['ProductionProcessRemark']['remark_text'])){
							$remarkArray['ProductionProcessRemark']['remark_text']=$productionProcessRemark['remark_text'];
						}
						else {
							$remarkArray['ProductionProcessRemark']['remark_text']="Proceso de Producción creada";
						}
						$remarkArray['ProductionProcessRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->ProductionProcessRemark->create();
						if (!$this->ProductionProcessRemark->save($remarkArray)) {
							echo "Problema guardando las remarcas para el proceso de producción";
							pr($this->validateErrors($this->ProductionProcessRemark));
							throw new Exception();
						}
						
					}
					$datasource->commit();
					$this->recordUserAction($this->ProductionProcess->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se creó el proceso de producción ".$this->request->data['ProductionProcess']['production_process_code']);
					$this->Session->setFlash(__('The production process has been saved.'), 'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('The production process could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				}
			}
		}
		$this->set(compact('department_id'));
		$this->set(compact('requestProducts'));

		$departmentConditions=array();
		if (!empty($departmentBoss)){
			$departmentConditions=array(
				'Department.id'=>$departmentBoss['User']['department_id'],
			);
		}
		$departments = $this->ProductionProcess->Department->find('list',array(
			'conditions'=>$departmentConditions,
			'order'=>'Department.name',
		));
		$this->set(compact('departments'));
		
		$operationLocations=$this->OperationLocation->find('list',array('order'=>'OperationLocation.name'));
		$this->set(compact('operationLocations'));
		
		$operators=$this->User->find('list',array(
			'conditions'=>array(
				'User.role_id'=>ROLE_OPERATOR,
			),
			'order'=>'User.last_name',
		));
		$this->set(compact('operators'));
		
		
		$machines=$this->Machine->find('list',array(
			'conditions'=>array(
				'Machine.bool_active'=>true,
			),
			'order'=>'Machine.name',
		));
		$this->set(compact('machines'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
	}

	public function edit($id = null) {
		if (!$this->ProductionProcess->exists($id)) {
			throw new NotFoundException(__('Invalid production process'));
		}
		$production_process_id=$id;
		$this->set(compact('production_process_id'));
		
		$this->loadModel('PurchaseOrderProduct');
		$this->loadModel('Product');
		$this->loadModel('OperationLocation');
		$this->loadModel('ProductionProcessRemark');
		$this->loadModel('ProductionProcessProduct');
		$this->loadModel('ProductionProcessProductOperationLocation');
		$this->loadModel('Machine');
		$this->loadModel('User');
		$this->loadModel('SalesOrderProduct');
		
		$this->loadModel('Department');
		$this->loadModel('ProductionOrder');
		$this->loadModel('ProductionOrderProduct');
		
		$departmentBoss=array();
		if ($this->Auth->User('role_id')==ROLE_DEPARTMENT_BOSS){
			$departmentBoss=$this->User->find('first',array('conditions'=>array('User.id'=>$this->Auth->User('id'))));						
		}
		if (!empty($departmentBoss)){
			$department_id=$departmentBoss['User']['department_id'];
		}

		$requestProducts=array();
		$productionProcessRemarks=array();
		if ($this->request->is(array('post', 'put'))) {
			$department_id=$this->request->data['ProductionProcess']['department_id'];
			foreach ($this->request->data['ProductionProcessProduct'] as $productionProcessProduct){
				if ($productionProcessProduct['product_id']>0&&$productionProcessProduct['product_quantity']>0){
					$requestProducts[]['ProductionProcessProduct']=$productionProcessProduct;
				}
			}
			
			$productionProcessDateArray=$this->request->data['ProductionProcess']['production_process_date'];
			//pr($productionProcessDateArray);
			$productionProcessDateString=$productionProcessDateArray['year'].'-'.$productionProcessDateArray['month'].'-'.$productionProcessDateArray['day'];
			$productionProcessDate=date( "Y-m-d", strtotime($productionProcessDateString));
			
			if ($productionProcessDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de proceso de producción no puede estar en el futuro!  No se guardó el proceso de producción.'), 'default',array('class' => 'error-message'));
			}
			else {			
				$datasource=$this->ProductionProcess->getDataSource();
				$datasource->begin();
				try {
					$previousProductionProcessProducts=$this->ProductionProcessProduct->find('all',array(
						'fields'=>array(
							'ProductionProcessProduct.id',
							'ProductionProcessProduct.sales_order_product_id',
						),
						'conditions'=>array(
							'ProductionProcessProduct.production_process_id'=>$id,
						),
						'contain'=>array(
							'ProductionProcessProductOperationLocation',
						),
					));
					if (!empty($previousProductionProcessProducts)){
						foreach ($previousProductionProcessProducts as $previousProductionProcessProduct){
							if (!empty($previousProductionProcessProduct['ProductionProcessProductOperationLocation'])){
								foreach ($previousProductionProcessProduct['ProductionProcessProductOperationLocation'] as $previousProductOperationLocation){
									$this->ProductionProcessProductOperationLocation->id=$previousProductOperationLocation['id'];
									$this->ProductionProcessProductOperationLocation->delete($previousProductOperationLocation['id']);
								}
							}
							//pr($previousProductionProcessProduct);
							$salesOrderProductArray=array();
							$salesOrderProductArray['SalesOrderProduct']['id']=$previousProductionProcessProduct['ProductionProcessProduct']['sales_order_product_id'];
							$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PRODUCTION;
							$this->SalesOrderProduct->id=$previousProductionProcessProduct['ProductionProcessProduct']['sales_order_product_id'];
							if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
								echo "Problema cambiando el estado de los productos de la orden de venta";
								pr($this->validateErrors($this->SalesOrderProduct));
								throw new Exception();
							}
							
							$this->ProductionProcessProduct->id=$previousProductionProcessProduct['ProductionProcessProduct']['id'];
							$this->ProductionProcessProduct->delete($previousProductionProcessProduct['ProductionProcessProduct']['id']);
						}
					}
				
					if ($this->request->data['ProductionProcess']['bool_annulled']){
						$this->ProductionProcess->id=$id;
						if (!$this->ProductionProcess->save($this->request->data)) {
							echo "Problema guardando el proceso de producción";
							//pr($this->validateErrors($this->ProductionProcess));
							throw new Exception();
						} 
						$production_process_id=$this->ProductionProcess->id;
						
						$productionProcessRemarkArray=array();
						$productionProcessRemarkArray['ProductionProcessRemark']['user_id']=$this->Auth->User('id');
						$productionProcessRemarkArray['ProductionProcessRemark']['production_process_id']=$production_process_id;
						$productionProcessRemarkArray['ProductionProcessRemark']['remark_datetime']=date('Y-m-d H:i:s');
						$productionProcessRemarkArray['ProductionProcessRemark']['remark_text']="Se anuló el proceso de producción";
						$productionProcessRemarkArray['ProductionProcessRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->ProductionProcessRemark->create();
						if (!$this->ProductionProcessRemark->save($productionProcessRemarkArray)) {
							echo "Problema guardando las remarcas para el proceso de producción";
							pr($this->validateErrors($this->ProductionProcessRemark));
							throw new Exception();
						}
					}
					else {
						$this->ProductionProcess->id=$id;
						
						if (!$this->ProductionProcess->save($this->request->data)) {
							echo "Problema guardando el proceso de producción";
							pr($this->validateErrors($this->ProductionProcess));
							throw new Exception();
						}
						$production_process_id=$this->ProductionProcess->id;
						
						foreach ($this->request->data['ProductionProcessProduct'] as $productionProcessProduct){
							if ($productionProcessProduct['product_id']>0 && $productionProcessProduct['product_quantity']>0){
								// UPDATE THE SALES ORDER PRODUCT 
								$relatedSalesOrderProduct=$this->SalesOrderProduct->find('first',array(
									'conditions'=>array(
										'SalesOrderProduct.id'=>$productionProcessProduct['sales_order_product_id'],
									),
									'contain'=>array(
										'SalesOrder',
									),
								));
								$salesOrderProductQuantity=$relatedSalesOrderProduct['SalesOrderProduct']['product_quantity'];
								if ($productionProcessProduct['product_quantity']==$salesOrderProductQuantity){
									$boolOtherDepartmentsPending=false;
									$salesOrderProduct=$this->SalesOrderProduct->find('first',array(
										'conditions'=>array(
											'SalesOrderProduct.id'=>$productionProcessProduct['sales_order_product_id'],
										),
										'contain'=>array(
											'ProductionOrderProduct'=>array(
												'ProductionOrderProductDepartment',
											),
											'ProductionProcessProduct'=>array(
												'ProductionProcess',
											),
										),
									));
									$productionOrderProductQuantity=0;
									foreach ($salesOrderProduct['ProductionOrderProduct'] as $productionOrderProduct){
										$productionOrderProductQuantity+=$productionOrderProduct['product_quantity'];
										foreach ($productionOrderProduct['ProductionOrderProductDepartment'] as $productDepartment){
											$departmentId=$productDepartment['department_id'];
											$boolProductionProcessProductPresent=false;
											if (!empty($salesOrderProduct['ProductionProcessProduct'])){
												foreach ($salesOrderProduct['ProductionProcessProduct'] as $saleProductionProcessProduct){
													if ($saleProductionProcessProduct['ProductionProcess']['department_id']==$departmentId){
														if ($saleProductionProcessProduct['product_quantity']>=$productionOrderProductQuantity){
															$boolProductionProcessProductPresent=true;
														}
													}
												}
											}
											if (!$boolProductionProcessProductPresent){
												$boolOtherDepartmentsPending=true;
											}
										}
									}
									if (!$boolOtherDepartmentsPending){
										$this->SalesOrderProduct->id=$productionProcessProduct['sales_order_product_id'];
										$salesOrderProductArray=array();
										$salesOrderProductArray['SalesOrderProduct']['id']=$productionProcessProduct['sales_order_product_id'];
										$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_READY_FOR_DELIVERY;
										if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
											echo "Problema cambiando el estado de los productos de la orden de venta";
											pr($this->validateErrors($this->SalesOrderProduct));
											throw new Exception();
										}
									}
								}
								else {
                  /*
									if (!$this->SalesOrderProduct->splitSalesOrderProduct($productionProcessProduct['sales_order_product_id'], PRODUCT_STATUS_READY_FOR_DELIVERY,$productionProcessProduct['product_quantity'])){
										echo "Problema con la separación de los productos de la orden de venta";
										throw new Exception();
									}
                  */
									//echo "alrighty the sales order product has been successfully split!!<br/>";
								}
								//pr($quotationProduct);
								$productArray=array();
								$productArray['ProductionProcessProduct']['production_process_id']=$production_process_id;
								$productArray['ProductionProcessProduct']['product_id']=$productionProcessProduct['product_id'];
								$productArray['ProductionProcessProduct']['product_description']=$productionProcessProduct['product_description'];
								$productArray['ProductionProcessProduct']['product_quantity']=$productionProcessProduct['product_quantity'];
								$productArray['ProductionProcessProduct']['operator_id']=$productionProcessProduct['operator_id'];
								$productArray['ProductionProcessProduct']['machine_id']=$productionProcessProduct['machine_id'];
								$productArray['ProductionProcessProduct']['sales_order_id']=$relatedSalesOrderProduct['SalesOrder']['id'];
								$productArray['ProductionProcessProduct']['sales_order_product_id']=$productionProcessProduct['sales_order_product_id'];
								$this->ProductionProcessProduct->create();
								if (!$this->ProductionProcessProduct->save($productArray)) {
									echo "Problema guardando los productos del proceso de producción";
									pr($this->validateErrors($this->ProductionProcessProduct));
									throw new Exception();
								}
								
								$production_process_product_id=$this->ProductionProcessProduct->id;
								
								foreach ($productionProcessProduct['operation_location_id'] as $locationId){
									$this->ProductionProcessProductOperationLocation->create();
									$locationArray=array();
									$locationArray['ProductionProcessProductOperationLocation']['production_process_product_id']=$production_process_product_id;
									$locationArray['ProductionProcessProductOperationLocation']['operation_location_id']=$locationId;
									if (!$this->ProductionProcessProductOperationLocation->save($locationArray)) {
										echo "Problema guardando los lugares de las operaciones del producto";
										pr($this->validateErrors($this->ProductionProcessProductOperationLocation));
										throw new Exception();
									}
								}
							}
						}
													
						$productionProcessRemark=$this->request->data['ProductionProcessRemark'];
						//pr($productionProcessRemark);
						$remarkArray=array();
						$remarkArray['ProductionProcessRemark']['user_id']=$productionProcessRemark['user_id'];
						$remarkArray['ProductionProcessRemark']['production_process_id']=$production_process_id;
						$remarkArray['ProductionProcessRemark']['remark_datetime']=date('Y-m-d H:i:s');
						if (!empty($this->request->data['ProductionProcessRemark']['remark_text'])){
							$remarkArray['ProductionProcessRemark']['remark_text']=$productionProcessRemark['remark_text'];
						}
						else {
							$remarkArray['ProductionProcessRemark']['remark_text']="Proceso de Producción creada";
						}
						$remarkArray['ProductionProcessRemark']['action_type_id']=ACTION_TYPE_OTHER;
						$this->ProductionProcessRemark->create();
						if (!$this->ProductionProcessRemark->save($remarkArray)) {
							echo "Problema guardando las remarcas para el proceso de producción";
							pr($this->validateErrors($this->ProductionProcessRemark));
							throw new Exception();
						}
					}
					$datasource->commit();
					$this->recordUserAction($this->ProductionProcess->id,null,null);
					$this->recordUserActivity($this->Session->read('User.username'),"Se creó el proceso de producción ".$this->request->data['ProductionProcess']['production_process_code']);
					$this->Session->setFlash(__('The production process has been saved.'), 'default',array('class' => 'success'));
					return $this->redirect(array('action' => 'index'));
				} 
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('The production process could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				}
			}
		} 
		else {
			$this->request->data = $this->ProductionProcess->find('first', array(
				'conditions' => array(
					'ProductionProcess.id' => $id,
				),
				'contain'=>array(
					'ProductionProcessProduct'=>array(
						'SalesOrderProduct'=>array(
							'ProductionOrderProduct'=>array(
								'ProductionOrder'
							),
						),
						'ProductionProcessProductOperationLocation',
					),
					'ProductionProcessRemark'=>array(
						'User',
					),
				),
			));
			foreach ($this->request->data['ProductionProcessProduct'] as $product){
				//pr($product);
				
				if (!empty($product['SalesOrderProduct'])){
					if (!empty($product['SalesOrderProduct']['ProductionOrderProduct'])){
						$product['production_order_id']=$product['SalesOrderProduct']['ProductionOrderProduct'][0]['ProductionOrder']['id'];
						$product['production_order_code']=$product['SalesOrderProduct']['ProductionOrderProduct'][0]['ProductionOrder']['production_order_code'];
					}
				}
				$locationIds=array();
				if (!empty($product['ProductionProcessProductOperationLocation'])){
					foreach($product['ProductionProcessProductOperationLocation'] as $locationId)
					$locationIds[]=$locationId['operation_location_id'];
				}
				$product['locationIds']=$locationIds;
				//pr($product);
				$requestProducts[]['ProductionProcessProduct']=$product;
				
			}
			foreach ($this->request->data['ProductionProcessRemark'] as $remark){
				$productionProcessRemarks[]['ProductionProcessRemark']=$remark;
			}
		}
		$this->set(compact('requestProducts'));
		$this->set(compact('productionProcessRemarks'));
		
		$salesOrderProductsInProductionProcess=$this->ProductionProcessProduct->find('list',array(
			'fields'=>array('ProductionProcessProduct.sales_order_product_id'),
			'conditions'=>array('ProductionProcessProduct.production_process_id'=>$id),
		));
		//pr($salesOrderProductsInProductionProcess);
		$this->SalesOrderProduct->Recursive=-1;
		$fullSalesOrderProducts=$this->SalesOrderProduct->find('all',array(
			'fields'=>array('SalesOrderProduct.id'),
			'conditions'=>array(
				'OR'=>array(
					array('SalesOrderProduct.sales_order_product_status_id'=>PRODUCT_STATUS_AWAITING_PRODUCTION),
					array('SalesOrderProduct.id'=>$salesOrderProductsInProductionProcess,),
				),
			),
			'contain'=>array(
				'Product'=>array(
					'fields'=>array('Product.name'),
				),
			),
		));
		//pr($fullSalesOrderProducts);
		$salesOrderProducts=array();
		foreach ($fullSalesOrderProducts as $product){
			$salesOrderProducts[$product['SalesOrderProduct']['id']]=$product['Product']['name'];
		}
		//pr($salesOrderProducts);
		$this->set(compact('salesOrderProducts'));
		$departmentConditions=array();
		if (!empty($departmentBoss)){
			$departmentConditions=array(
				'Department.id'=>$departmentBoss['User']['department_id'],
			);
		}
		$departments = $this->ProductionProcess->Department->find('list',array(
			'conditions'=>$departmentConditions,
			'order'=>'Department.name',
		));
		$this->set(compact('departments'));
		
		$operationLocations=$this->OperationLocation->find('list',array('order'=>'OperationLocation.name'));
		$this->set(compact('operationLocations'));
		
		$operators=$this->User->find('list',array(
			'conditions'=>array(
				'User.role_id'=>ROLE_OPERATOR,
			),
			'order'=>'User.last_name',
		));
		$this->set(compact('operators'));
		
		$machines=$this->Machine->find('list',array(
			'conditions'=>array(
				'Machine.bool_active'=>true,
			),
			'order'=>'Machine.name',
		));
		$this->set(compact('machines'));
		
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
		
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
		$this->ProductionProcess->id = $id;
		if (!$this->ProductionProcess->exists()) {
			throw new NotFoundException(__('Invalid production process'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('ProductionProcessProductOperationLocation');
		
		$productionProcess=$this->ProductionProcess->find('first',array(
			'conditions'=>array(
				'ProductionProcess.id'=>$id,
			),
			'contain'=>array(
				'ProductionProcessProduct'=>array(
					'ProductionProcessProductOperationLocation',
					'Product',
					'SalesOrderProduct'=>array(
						'InvoiceProduct'=>array(
							'Invoice',
						),
					),
				),
				'ProductionProcessRemark',
			)
		));
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (!empty($productionProcess['ProductionProcessProduct'])){
			foreach ($productionProcess['ProductionProcessProduct'] as $productionProcessProduct){
				if (!empty($productionProcessProduct['product_quantity'])){
					if (!empty($productionProcessProduct['SalesOrderProduct'])){
						$salesOrderProduct=$productionProcessProduct['SalesOrderProduct'];
						if (!empty($salesOrderProduct['InvoiceProduct'])){
							$boolDeletionAllowed=false;
							$flashMessage.="El producto ".$productionProcess['ProductionProcessProduct']['Product']['name']." ya se facturó en factura # ".$salesOrderProduct['InvoiceProduct'][0]['Invoice']['invoice_code']."!  Para poder eliminar el proceso de producción, primero hay que eliminar o modificar la factura.";
							//if (count($productionProcess['ProductionProcess'])==1){
							//	$flashMessage.=$productionProcess['ProductionProcess'][0]['production_process_code'].".";
							//}
							//else {
							//	for ($i=0;$i<count($productionProcess['ProductionProcess']);$i++){
							//		$flashMessage.=$productionProcess['ProductionProcess'][$i]['production_process_code'];
							//		if ($i==count($productionProcess['ProductionProcess'])-1){
							//			$flashMessage.=".";
							//		}
							//		else {
							//			$flashMessage.=" y ";
							//		}
							//	}
							//}
						}
					}
				}
			}
		}
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó el proceso de producción.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			$datasource=$this->ProductionProcess->getDataSource();
			$datasource->begin();	
			try {
				//delete all products, remarks and other costs
				foreach ($productionProcess['ProductionProcessProduct'] as $productionProcessProduct){
					if (!empty($productionProcessProduct['ProductionProcessProductOperationLocation'])){
						foreach ($productionProcessProduct['ProductionProcessProductOperationLocation'] as $productOperationLocation){
							$this->ProductionProcessProductOperationLocation->id=$productOperationLocation['id'];
							$this->ProductionProcessProductOperationLocation->delete($productOperationLocation['id']);
						}
					}
					if (!$this->ProductionProcess->ProductionProcessProduct->delete($productionProcessProduct['id'])) {
						echo "Problema al eliminar el producto del proceso de producción";
						pr($this->validateErrors($this->ProductionProcess->ProductionProcessProduct));
						throw new Exception();
					}
					$salesOrderProductArray=array();
					$salesOrderProductArray['SalesOrderProduct']['id']=$productionProcessProduct['sales_order_product_id'];
					$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PRODUCTION;
					$this->SalesOrderProduct->id=$productionProcessProduct['sales_order_product_id'];
					if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
						echo "Problema cambiando el estado de los productos de la orden de venta";
						pr($this->validateErrors($this->SalesOrderProduct));
						throw new Exception();
					}
				}
				
				foreach ($productionProcess['ProductionProcessRemark'] as $productionProcessRemark){
					if (!$this->ProductionProcess->ProductionProcessRemark->delete($productionProcessRemark['id'])) {
						echo "Problema al eliminar la remarca del proceso de producción";
						pr($this->validateErrors($this->ProductionProcess->ProductionProcessRemark));
						throw new Exception();
					}
				}
				
				if (!$this->ProductionProcess->delete($id)) {
					echo "Problema al eliminar el proceso de producción";
					pr($this->validateErrors($this->ProductionProcess));
					throw new Exception();
				}
						
				$datasource->commit();
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$productionProcess['ProductionProcess']['id'];
				$deletionArray['Deletion']['reference']=$productionProcess['ProductionProcess']['production_process_code'];
				$deletionArray['Deletion']['type']='ProductionProcess';
				$this->Deletion->save($deletionArray);
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó el proceso de producción número ".$productionProcess['ProductionProcess']['production_process_code']);
						
				$this->Session->setFlash(__('Se eliminó el proceso de producción.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía eliminar el proceso de producción.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}

	public function annul($id = null) {
		$this->ProductionProcess->id = $id;
		if (!$this->ProductionProcess->exists()) {
			throw new NotFoundException(__('Proceso de Producción inválida'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$this->loadModel('SalesOrderProduct');
		$this->loadModel('ProductionProcessProductOperationLocation');
		
		$datasource=$this->ProductionProcess->getDataSource();
		$datasource->begin();
		try {
			//pr($this->request->data);
			
			$this->loadModel('ProductionProcessProduct');
			$this->ProductionProcessProduct->recursive=-1;
			$previousProductionProcessProducts=$this->ProductionProcessProduct->find('all',array(
				'fields'=>array('ProductionProcessProduct.id'),
				'conditions'=>array(
					'ProductionProcessProduct.production_process_id'=>$id,
				),
				'contain'=>array(
					'ProductionProcessProductOperationLocation',
				),
			));
			if (!empty($previousProductionProcessProducts)){
				foreach ($previousProductionProcessProducts as $previousProductionProcessProduct){
					if (!empty($previousProductionProcessProduct['ProductionProcessProductOperationLocation'])){
						foreach ($previousProductionProcessProduct['ProductionProcessProductOperationLocation'] as $productOperationLocation){
							$this->ProductionProcessProductOperationLocation->id=$productOperationLocation['id'];
							$this->ProductionProcessProductOperationLocation->delete($productOperationLocation['id']);
						}
					}
					
					$this->ProductionProcessProduct->id=$previousProductionProcessProduct['ProductionProcessProduct']['id'];
					if (!$this->ProductionProcessProduct->delete($previousProductionProcessProduct['ProductionProcessProduct']['id'])){
						echo "Problema al eliminar los productos del proceso de producción";
						pr($this->validateErrors($this->ProductionProcessProduct));
						throw new Exception();
					}
					$salesOrderProductArray=array();
					$salesOrderProductArray['SalesOrderProduct']['id']=$previousProductionProcessProduct['ProductionProcessProduct']['sales_order_product_id'];
					$salesOrderProductArray['SalesOrderProduct']['sales_order_product_status_id']=PRODUCT_STATUS_AWAITING_PRODUCTION;
					$this->SalesOrderProduct->id=$previousProductionProcessProduct['ProductionProcessProduct']['sales_order_product_id'];
					if (!$this->SalesOrderProduct->save($salesOrderProductArray)) {
						echo "Problema cambiando el estado de los productos de la orden de venta";
						pr($this->validateErrors($this->SalesOrderProduct));
						throw new Exception();
					}
				}
			}
			
			$this->ProductionProcess->id=$id;
			$productionProcessArray=array();
			$productionProcessArray['ProductionProcess']['id']=$id;
			$productionProcessArray['ProductionProcess']['bool_annulled']=true;
			if (!$this->ProductionProcess->save($productionProcessArray)) {
				echo "Problema al anular el proceso de producción";
				pr($this->validateErrors($this->ProductionProcess));
				throw new Exception();
			}
						
			$datasource->commit();
			$this->Session->setFlash(__('El proceso de producción se anuló.'),'default',array('class' => 'success'));
		}
		catch(Exception $e){
			$this->Session->setFlash(__('El proceso de producción no se podía anular.'), 'default',array('class' => 'error-message'));
		}
		
		return $this->redirect(array('action' => 'index'));
	}

	public function getnewproductionprocesscode(){
		$this->layout= "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$department_id=trim($_POST['department_id']);
		
		$newProductionProcessCode="";
		
		$this->loadModel('Department');
		$this->Department->recursive=-1;
		$selectedDepartment=$this->Department->find('first',array(
			'conditions'=>array('Department.id'=>$department_id,),
		));
		//pr($selectedDepartment);
		$departmentAbbreviation=$selectedDepartment['Department']['abbreviation'];
		
		$this->ProductionProcess->recursive=-1;
		$lastProductionProcess=$this->ProductionProcess->find('first',array(
			'conditions'=>array(
				'ProductionProcess.department_id'=>$department_id,
			),
			'order'=>'ProductionProcess.production_process_code DESC',
		));
		//pr($lastProductionProcess);
		
		if (!empty($lastProductionProcess)){
			$newProductionProcessCode=$departmentAbbreviation."_".str_pad(substr($lastProductionProcess['ProductionProcess']['production_process_code'],strlen($departmentAbbreviation)+1)+1, 4, "0", STR_PAD_LEFT);
		}
		else {
			$newProductionProcessCode=$departmentAbbreviation."_0001";
		}
		
		return $newProductionProcessCode;
	}
}
