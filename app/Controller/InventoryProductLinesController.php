<?php
App::uses('AppController', 'Controller');

class InventoryProductLinesController extends AppController {

	public $components = array('Paginator');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('getboolpromotion');		
	}
	
	public function index() {
		$this->InventoryProductLine->recursive = -1;
		
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
		
		$inventoryProductLineCount=	$this->InventoryProductLine->find('count', array(
			'fields'=>array('InventoryProductLine.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
				'Department',
			),
			'limit'=>($inventoryProductLineCount!=0?$inventoryProductLineCount:1),
		);

		$inventoryProductLines = $this->Paginator->paginate('InventoryProductLine');
		$this->set(compact('inventoryProductLines'));
		
		$aco_name="InventoryProducts/index";		
		$bool_inventoryproduct_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_index_permission'));
		$aco_name="InventoryProducts/add";		
		$bool_inventoryproduct_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_add_permission'));
		
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function view($id = null) {
		if (!$this->InventoryProductLine->exists($id)) {
			throw new NotFoundException(__('Invalid inventory product line'));
		}
		
		$this->InventoryProductLine->recursive=-1;
		$inventoryProductLine=$this->InventoryProductLine->find('first', array(
			'conditions' => array(
				'InventoryProductLine.id' => $id,
			),
			'contain'=>array(
				'Department',
				'InventoryProduct'=>array(
					'Currency',
					'MeasuringUnit',
				),
			),
		));
		$this->set(compact('inventoryProductLine'));
		
		$aco_name="InventoryProducts/index";		
		$bool_inventoryproduct_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_index_permission'));
		$aco_name="InventoryProducts/add";		
		$bool_inventoryproduct_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_add_permission'));
		
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->InventoryProductLine->create();
			if ($this->InventoryProductLine->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory product line has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The inventory product line could not be saved. Please, try again.'),'default',array('class' => 'error-message'));
			}
		}
		$departments = $this->InventoryProductLine->Department->find('list');
		$this->set(compact('departments'));
		
		$aco_name="InventoryProducts/index";		
		$bool_inventoryproduct_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_index_permission'));
		$aco_name="InventoryProducts/add";		
		$bool_inventoryproduct_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_add_permission'));
		
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->InventoryProductLine->exists($id)) {
			throw new NotFoundException(__('Invalid inventory product line'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->InventoryProductLine->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory product line has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The inventory product line could not be saved. Please, try again.'),'default',array('class' => 'error-message'));
			}
		} else {
			$options = array('conditions' => array('InventoryProductLine.' . $this->InventoryProductLine->primaryKey => $id));
			$this->request->data = $this->InventoryProductLine->find('first', $options);
		}
		$departments = $this->InventoryProductLine->Department->find('list');
		$this->set(compact('departments'));
		
		$aco_name="InventoryProducts/index";		
		$bool_inventoryproduct_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_index_permission'));
		$aco_name="InventoryProducts/add";		
		$bool_inventoryproduct_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_add_permission'));
		
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
		$this->InventoryProductLine->id = $id;
		if (!$this->InventoryProductLine->exists()) {
			throw new NotFoundException(__('Invalid inventory product line'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$inventoryProductLine=$this->InventoryProductLine->find('first',array(
			'conditions'=>array(
				'InventoryProductLine.id'=>$id,
			),
		));
		$this->request->allowMethod('post', 'delete');
		if ($this->InventoryProductLine->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$inventoryProductLine['InventoryProductLine']['id'];
			$deletionArray['Deletion']['reference']=$inventoryProductLine['InventoryProductLine']['name'];
			$deletionArray['Deletion']['type']='InventoryProductLine';
			$this->Deletion->save($deletionArray);
			
			$this->Session->setFlash(__('The inventory product line has been deleted.'),'default',array('class' => 'success'));
		} 
		else {
			$this->Session->setFlash(__('The inventory product line could not be deleted. Please, try again.'),'default',array('class' => 'error-message'));
		}
		return $this->redirect(array('action' => 'index'));
	}

	public function getboolpromotion() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$inventoryProductLineId=trim($_POST['inventoryproductlineid']);
		
		$this->InventoryProductLine->recursive=-1;
		$inventoryProductLine=$this->InventoryProductLine->find('first',array(
			'conditions'=>array('InventoryProductLine.id'=>$inventoryProductLineId,),
		));
		//pr($inventoryProductLine);
	
		return $inventoryProductLine['InventoryProductLine']['bool_promotion'];
	}
}
