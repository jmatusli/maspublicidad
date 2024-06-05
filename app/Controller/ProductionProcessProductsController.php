<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class ProductionProcessProductsController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function verReporteProduccion(){
		$this->loadModel('ProductionOrder');
		$this->loadModel('ProductionProcess');
		
		$this->loadModel('Quotation');
		$this->loadModel('SalesOrder');
		$this->loadModel('SalesOrderProduct');
		
		$this->loadModel('Department');
		$this->loadModel('Machine');
		$this->loadModel('User');
		
		$role_id=$this->Auth->User('role_id');
		
		$departmentId=0;
		$machineId=0;
		$operatorId=0;
		
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$departmentId=$this->request->data['Report']['department_id'];
			
			$machineId=$this->request->data['Report']['machine_id'];
			$operatorId=$this->request->data['Report']['operator_id'];
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
		
		$salesOrderProductIds=array();
		switch ($this->Auth->User('role_id')){
			case ROLE_ADMIN:
			case ROLE_ASSISTANT:
				break;
			case ROLE_SALES_EXECUTIVE:
				break;
			case ROLE_DEPARTMENT_BOSS:
				$this->loadModel('User');
				$departmentBoss=$this->User->find('first',array('conditions'=>array('User.id'=>$this->Auth->User('id'))));
				$departmentId=$departmentBoss['User']['department_id'];
				break;
			case ROLE_OPERATOR:
				//OPERATORS SHOULD NEVER SEE THIS
				$operatorId=$this->Auth->User('id');
				break;
		}
		$this->set(compact('departmentId','machineId','operatorId'));
		
		$departmentConditions=array();
		if (!empty($departmentId)){
			$departmentConditions[]=array('Department.id'=>$departmentId);
		}
		$this->Department->recursive=-1;
		$selectedDepartments=$this->Department->find('all',array(
			'conditions'=>$departmentConditions,
			'order'=>'Department.name',
		));
		
		for($d=0;$d<count($selectedDepartments);$d++){
			$productionProcessConditions=array(
				'ProductionProcess.production_process_date >='=>$startDate,
				'ProductionProcess.production_process_date <'=>$endDatePlusOne,
				'ProductionProcess.bool_annulled'=>false,
				'ProductionProcess.department_id'=>$selectedDepartments[$d]['Department']['id'],
			);
			$productionProcessIds=$this->ProductionProcess->find('list',array('conditions'=>$productionProcessConditions));
			
			$productionProcessProductConditions=array('ProductionProcessProduct.production_process_id'=>$productionProcessIds);
			if (!empty($machineId)){
				$productionProcessProductConditions[]=array('ProductionProcessProduct.machine_id'=>$machineId);
			}
			if (!empty($operatorId)){
				$productionProcessProductConditions[]=array('ProductionProcessProduct.operator_id'=>$operatorId);
			}		
			
			if ($this->Auth->User('role_id')==ROLE_SALES_EXECUTIVE){	
				// for sales executives, only sales order products from sales orders from quotations of this sales executive should be retrieved
				// however, this would slow down the system after a while
				// therefore, there is a selection of sales order products corresponding with production for the period
				// this gives us sales order ids, which in turn give quotation ids, and on basis of this we work back
				$qualifiedSalesOrderProductIds=$this->ProductionProcessProduct->find('list',array(
					'fields'=>array('ProductionProcessProduct.sales_order_product_id'),
					'conditions'=>$productionProcessProductConditions,
				));
				$salesOrderIds=$this->SalesOrderProduct->find('list',array(
					'fields'=>array('SalesOrderProduct.sales_order_id'),
					'conditions'=>array(
						'SalesOrderProduct.id'=>$qualifiedSalesOrderProductIds,
					),
				));
				$quotationIds=$this->SalesOrder->find('list',array(
					'fields'=>array('SalesOrder.quotation_id'),
					'conditions'=>array(
						'SalesOrder.id'=>$salesOrderIds,
					),
				));
				$quotationIdsForSalesExecutive=$this->Quotation->find('list',array(
					'fields'=>array('Quotation.id'),
					'conditions'=>array(
						'Quotation.id'=>$quotationIds,
						'Quotation.user_id'=>$this->Auth->User('id'),
					),
				));
				$salesOrderIdsForSalesExecutive=$this->SalesOrder->find('list',array(
					'fields'=>array('SalesOrder.id'),
					'conditions'=>array(
						'SalesOrder.quotation_id'=>$quotationIdsForSalesExecutive,
					),
				));
				$salesOrderProductIds=$this->SalesOrderProduct->find('list',array(
					'fields'=>array('SalesOrderProduct.id'),
					'conditions'=>array(
						'SalesOrderProduct.sales_order_id >='=>$salesOrderIdsForSalesExecutive,
					),
				));
				if (!empty($salesOrderProductIds)){
					$productionProcessProductConditions[]=array('ProductionProcessProduct.sales_order_product_id'=>$salesOrderProductIds);
				}
			}
			
			$productionProcessProductCount=$this->ProductionProcessProduct->find('count', array(
				'fields'=>array('ProductionProcessProduct.id'),
				'conditions' => $productionProcessProductConditions,
			));
			
			$this->Paginator->settings = array(
				'conditions' => $productionProcessProductConditions,
				'contain'=>array(			
					'Product'=>array(
						'ProductCategory',
					),
					'ProductionProcess',
					'ProductionProcessProductOperationLocation'=>array(
						'OperationLocation',
					),
					'Operator',
					'Machine',
				),
				'order'=>'ProductionProcessProduct.production_process_id',
				'limit'=>($productionProcessProductCount!=0?$productionProcessProductCount:1),
			);
			$productionProcessProducts = $this->Paginator->paginate('ProductionProcessProduct');
			$selectedDepartments[$d]['processproducts']=$productionProcessProducts;
		}
		$this->set(compact('selectedDepartments'));
		//pr($selectedDepartments);
		
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
		
		$machines=$this->Machine->find('list',array('order'=>'Machine.name'));
		$this->set(compact('machines'));
		
		$operators=$this->User->find('list',array(
			'conditions'=>array(
				'User.role_id'=>ROLE_OPERATOR,
			),
			'order'=>'User.first_name,User.last_name',
		));
		$this->set(compact('operators'));
	}
	
	public function guardarReporteProduccion() {
		$exportData=$_SESSION['reporteProduccion'];
		$this->set(compact('exportData'));
	}
	
	public function index() {
		$this->ProductionProcessProduct->recursive = -1;
		
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
		
		$productionProcessProductCount=	$this->ProductionProcessProduct->find('count', array(
			'fields'=>array('ProductionProcessProduct.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($productionProcessProductCount!=0?$productionProcessProductCount:1),
		);

		$productionProcessProducts = $this->Paginator->paginate('ProductionProcessProduct');
		$this->set(compact('productionProcessProducts'));
	}

	public function view($id = null) {
		if (!$this->ProductionProcessProduct->exists($id)) {
			throw new NotFoundException(__('Invalid production process product'));
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
		$options = array('conditions' => array('ProductionProcessProduct.' . $this->ProductionProcessProduct->primaryKey => $id));
		$this->set('productionProcessProduct', $this->ProductionProcessProduct->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ProductionProcessProduct->create();
			if ($this->ProductionProcessProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The production process product has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production process product could not be saved. Please, try again.'));
			}
		}
		$productionProcesses = $this->ProductionProcessProduct->ProductionProcess->find('list');
		$products = $this->ProductionProcessProduct->Product->find('list');
		$operators = $this->ProductionProcessProduct->Operator->find('list');
		$machines = $this->ProductionProcessProduct->Machine->find('list');
		$salesOrders = $this->ProductionProcessProduct->SalesOrder->find('list');
		$this->set(compact('productionProcesses', 'products', 'operators', 'machines', 'salesOrders'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ProductionProcessProduct->exists($id)) {
			throw new NotFoundException(__('Invalid production process product'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ProductionProcessProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The production process product has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production process product could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ProductionProcessProduct.' . $this->ProductionProcessProduct->primaryKey => $id));
			$this->request->data = $this->ProductionProcessProduct->find('first', $options);
		}
		$productionProcesses = $this->ProductionProcessProduct->ProductionProcess->find('list');
		$products = $this->ProductionProcessProduct->Product->find('list');
		$operators = $this->ProductionProcessProduct->Operator->find('list');
		$machines = $this->ProductionProcessProduct->Machine->find('list');
		$salesOrders = $this->ProductionProcessProduct->SalesOrder->find('list');
		$this->set(compact('productionProcesses', 'products', 'operators', 'machines', 'salesOrders'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionProcessProduct->id = $id;
		if (!$this->ProductionProcessProduct->exists()) {
			throw new NotFoundException(__('Invalid production process product'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductionProcessProduct->delete()) {
			$this->Session->setFlash(__('The production process product has been deleted.'));
		} else {
			$this->Session->setFlash(__('The production process product could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
