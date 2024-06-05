<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class ProductionOrderProductsController extends AppController {


	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');
	
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('');		
	}
  
  public function procesar($productionOrderProductId=0) {
    $this->set(compact('productionOrderProductId'));
    
    $this->loadModel('SalesOrder');
    $this->loadModel('SalesOrderProduct');
    
    $this->loadModel('ProductionOrder');
    $this->loadModel('ProductionOrderState');
    
		$this->loadModel('Product');
    $this->loadModel('Department');
    $this->loadModel('OperationLocation');
		$this->loadModel('ProductionOrderProductOperationLocation');
		
    $this->loadModel('ProductionOrderProductDepartment');
    $this->loadModel('ProductionOrderProductDepartmentState');
    $this->loadModel('ProductionOrderProductDepartmentInstruction');
    
    $this->loadModel('User');
		$loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('userRoleId'));
    
    if (!$this->ProductionOrderProduct->exists($productionOrderProductId)) {
			throw new NotFoundException(__('Invalid production order'));
		}
    
    $productionOrderProduct=$this->ProductionOrderProduct->getProductionOrderProductData($productionOrderProductId);
    $productionOrderId=$productionOrderProduct['ProductionOrder']['id'];		
    //pr($productionOrderProductStatus);
    if ($this->request->is('post')) {
      //pr($this->request->data);
    
      $datasource=$this->ProductionOrderProduct->getDataSource();
      $datasource->begin();
      try {
        $productDepartmentRank=  end($productionOrderProduct['ProductionOrderProductDepartment'])['rank'];
        
        foreach ($this->request->data['ProductionOrderProductDepartment'] as $departmentCounter=>$productDepartmentData){
          $productionOrderProductDepartmentId=$productDepartmentData['id'];
          // first check if the department is already existing or not
          if ($productionOrderProductDepartmentId > 0){
            //this is an exisitng department, check if department status has changed
            //echo 'posted state id for department is '.$productDepartmentData['state_id'].'<br/>';
            //echo 'existing state id for department is '.end($productionOrderProduct['ProductionOrderProductDepartment'][$departmentCounter]['ProductionOrderProductDepartmentState'])['production_order_state_id'].'<br/>';
            if ($productDepartmentData['state_id'] != end($productionOrderProduct['ProductionOrderProductDepartment'][$departmentCounter]['ProductionOrderProductDepartmentState'])['production_order_state_id']){
              //echo 'state change detected for department '.$productionOrderProductDepartmentId.'<br>';
              $departmentStateArray=[
                'user_id'=>$loggedUserId,
                'production_order_id'=>$productionOrderId,
                'production_order_product_id'=>$productionOrderProductId,
                'production_order_product_department_id'=>$productionOrderProductDepartmentId,
                'product_id'=>$productionOrderProduct['ProductionOrderProduct']['product_id'],
                'department_id'=>$productDepartmentData['department_id'],
                'production_order_state_id'=>$productDepartmentData['state_id'],
                'state_datetime'=>date('Y-m-d H:i:s'),
              ];
              //pr($departmentStateArray);
              $this->ProductionOrderProductDepartmentState->create();
              if (!$this->ProductionOrderProductDepartmentState->save($departmentStateArray)) {
                echo "Problema guardando el estado del departamento del producto de la orden de producción";
                pr($this->validateErrors($this->ProductionOrderProductDepartmentState));
                throw new Exception();
              }
            } 
            else {
              //echo 'no state change detected for department '.$productDepartmentData['id'].'<br>';
            }
          }
          /*
          else {
            // this is a new department
            $productDepartmentRank++;            
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
            } 
          }
        */ 
        
          if (!empty($productDepartmentData['ProductionOrderProductDepartmentInstruction'])){
            foreach ($productDepartmentData['ProductionOrderProductDepartmentInstruction'] as $productDepartmentInstructionData){
              // first check if the department is already existing or not
              if ($productDepartmentInstructionData['id'] > 0){
                // this is an existing instruction
                // do nothing
                //echo 'no new instruction<br/>';
              }
              else {
                if (!empty($productDepartmentInstructionData['instruction_text'])){
                  //echo 'new instruction<br/>';
                  $departmentInstructionArray=[
                    'user_id'=>$loggedUserId,
                    'production_order_id'=>$productionOrderId,
                    'production_order_product_id'=>$productionOrderProductId,
                    'production_order_product_department_id'=>$productionOrderProductDepartmentId,
                    'product_id'=>$productionOrderProduct['ProductionOrderProduct']['product_id'],
                    'department_id'=>$productDepartmentData['department_id'],
                    'instruction_datetime'=>date('Y-m-d H:i:s'),
                    'instruction_text'=>$productDepartmentInstructionData['instruction_text'],
                  ];
                  //pr($departmentInstructionArray);
                  $this->ProductionOrderProductDepartmentInstruction->create();
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
      
        $datasource->commit();
        $this->recordUserAction($productionOrderProductId,'procesar',null);
        $this->recordUserActivity($this->Session->read('User.username'),"Se procesó el producto de la orden de producción");
        $this->Session->setFlash(__('Se procesó el producto de la orden de producción.'), 'default',['class' => 'success']);
        return $this->redirect(['controller'=>'ProductionOrders','action' => 'detalle',$productionOrderId]);
      } 
      catch(Exception $e){
        $datasource->rollback();
        pr($e);
        $this->Session->setFlash(__('The production order could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
      }
		}
		
    $productionOrderProduct=$this->ProductionOrderProduct->getProductionOrderProductData($productionOrderProductId);
    $this->set(compact('productionOrderProduct','productionOrderProductStatus'));
    
    $departments=$this->Department->getDepartmentList();
		$operationLocations=$this->OperationLocation->getOperationLocationList();
		$products=$this->Product->getProductList();
    $this->set(compact('departments','operationLocations','products'));
		
    $productionOrderStates=$this->ProductionOrderState->getProductionOrderStateList();
    $this->set(compact('productionOrderStates'));

    $users=$this->User->getUserNaturalNameList();
    $this->set(compact('users'));

		$aco_name="SalesOrders/index";
		$bool_salesorder_index_permission=$this->hasPermission($loggedUserId,$aco_name);
		$this->set(compact('bool_salesorder_index_permission'));
		$aco_name="SalesOrders/add";		
		$bool_salesorder_add_permission=$this->hasPermission($loggedUserId,$aco_name);
		$this->set(compact('bool_salesorder_add_permission'));
	}
  

	public function verProduccionPendiente(){
		$this->loadModel('ProductionOrder');
		$this->loadModel('SalesOrderProduct');
		
		$this->ProductionOrderProduct->recursive = -1;
		
		$departmentId=0;
		if ($this->request->is('post')) {
			$departmentId=$this->request->data['Report']['department_id'];
		}
		$this->set(compact('departmentId'));
		
		$productionOrderConditions=array(
			'ProductionOrder.bool_annulled'=>false,
		);
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
				$productionOrderConditions[]=array('ProductionOrder.sales_order_id'=>$salesOrderIds);
				break;
			case ROLE_DEPARTMENT_BOSS:
				$this->loadModel('User');
				$departmentBoss=$this->User->find('first',array('conditions'=>array('User.id'=>$this->Auth->User('id'))));
				$departmentId=$departmentBoss['User']['department_id'];
				//if (!empty($departmentBoss)){
				//	$productionOrderConditions[]=array('ProductionOrder.department_id'=>$departmentBoss['User']['department_id']);
				//}
				break;
			case ROLE_OPERATOR:
				//OPERATORS SHOULD NEVER SEE THIS
				$salesOrderIds=array();
				$productionOrderConditions[]=array('ProductionOrder.sales_order_id'=>$salesOrderIds);
				break;
		}
		
		
		$productionOrderList=$this->ProductionOrder->find('list',array(
			'fields'=>array('ProductionOrder.id'),
			'conditions'=>$productionOrderConditions,
		));
		//pr($productionOrderList);
		
		$salesOrderProductIds=$this->SalesOrderProduct->find('list',array(
			'fields'=>array('SalesOrderProduct.id'),
			'conditions'=>array(
				'SalesOrderProduct.sales_order_product_status_id >='=>PRODUCT_STATUS_AUTHORIZED,
				'SalesOrderProduct.sales_order_product_status_id <'=>PRODUCT_STATUS_READY_FOR_DELIVERY,
			),
		));
		
		$conditions=array(
			'ProductionOrderProduct.production_order_id'=>$productionOrderList,
			'ProductionOrderProduct.product_quantity >'=>0,
			'ProductionOrderProduct.sales_order_product_id'=>$salesOrderProductIds,
		);
		
		if ($departmentId>0){
			$productionOrderProductIdsForDepartment=$this->ProductionOrderProductDepartment->find('list',array(
				'fields'=>array('ProductionOrderProductDepartment.production_order_product_id'),
				'conditions'=>array(
					'ProductionOrderProductDepartment.department_id'=>$departmentId,
				),				
			));
			//pr($productionOrderProductIdsForDepartment);
			$conditions[]=array('ProductionOrderProduct.id'=>$productionOrderProductIdsForDepartment);
		}
		
		
		$productionOrderProductCount=$this->ProductionOrderProduct->find('count', array(
			'fields'=>array('ProductionOrderProduct.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(			
				'Product'=>array(
					'ProductCategory',
				),
				'ProductionOrderProductOperationLocation',
				'ProductionOrderProductDepartment'=>array(
					'Department',
				),
				'ProductionOrder'=>array(
					'SalesOrder',
				),
				'PurchaseOrderProduct',
				'SalesOrderProduct'=>array(
					'SalesOrderProductStatus',
				),
			),
			'limit'=>($productionOrderProductCount!=0?$productionOrderProductCount:1),
		);
		$productionOrderProducts = $this->Paginator->paginate('ProductionOrderProduct');
		
		
		
		$this->set(compact('productionOrderProducts'));
		//pr($productionOrderProducts);
		
		$role_id=$this->Auth->User('role_id');
		$this->loadModel('Department');
		if ($role_id==ROLE_DEPARTMENT_BOSS){			
			$this->loadModel('User');
			$departmentBoss=$this->User->find('first',array('conditions'=>array('User.id'=>$this->Auth->User('id'))));
			$departments=$this->Department->find('list',array(
				'conditions'=>array(
					'Department.id'=>$departmentBoss['User']['department_id'],
				),
			));
		}
		else {
			$departments=$this->Department->find('list',array('order'=>'Department.name'));
		}
		$this->set(compact('departments'));
	}
	
	public function index() {
		$this->ProductionOrderProduct->recursive = -1;
		
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
		
		$productionOrderProductCount=	$this->ProductionOrderProduct->find('count', array(
			'fields'=>array('ProductionOrderProduct.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($productionOrderProductCount!=0?$productionOrderProductCount:1),
		);

		$productionOrderProducts = $this->Paginator->paginate('ProductionOrderProduct');
		$this->set(compact('productionOrderProducts'));
	}

	public function view($id = null) {
		if (!$this->ProductionOrderProduct->exists($id)) {
			throw new NotFoundException(__('Invalid production order product'));
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
		$options = array('conditions' => array('ProductionOrderProduct.' . $this->ProductionOrderProduct->primaryKey => $id));
		$this->set('productionOrderProduct', $this->ProductionOrderProduct->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->ProductionOrderProduct->create();
			if ($this->ProductionOrderProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The production order product has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production order product could not be saved. Please, try again.'));
			}
		}
		$productionOrders = $this->ProductionOrderProduct->ProductionOrder->find('list');
		$products = $this->ProductionOrderProduct->Product->find('list');
		$this->set(compact('productionOrders', 'products'));
	}

	public function edit($id = null) {
		if (!$this->ProductionOrderProduct->exists($id)) {
			throw new NotFoundException(__('Invalid production order product'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ProductionOrderProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The production order product has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production order product could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ProductionOrderProduct.' . $this->ProductionOrderProduct->primaryKey => $id));
			$this->request->data = $this->ProductionOrderProduct->find('first', $options);
		}
		$productionOrders = $this->ProductionOrderProduct->ProductionOrder->find('list');
		$products = $this->ProductionOrderProduct->Product->find('list');
		$this->set(compact('productionOrders', 'products'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionOrderProduct->id = $id;
		if (!$this->ProductionOrderProduct->exists()) {
			throw new NotFoundException(__('Invalid production order product'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductionOrderProduct->delete()) {
			$this->Session->setFlash(__('The production order product has been deleted.'));
		} else {
			$this->Session->setFlash(__('The production order product could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
