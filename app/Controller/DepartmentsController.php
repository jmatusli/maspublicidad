<?php
App::uses('AppController', 'Controller');

	class DepartmentsController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->Department->recursive = -1;
		
		$departmentCount=	$this->Department->find('count', array(
			'fields'=>array('Department.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
				'InventoryProductLine',
			),
			'limit'=>($departmentCount!=0?$departmentCount:1),
		);

		$departments = $this->Paginator->paginate('Department');
		$this->set(compact('departments'));
		
		$aco_name="InventoryProductLines/index";		
		$bool_inventoryproductline_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_index_permission'));
		$aco_name="InventoryProductLines/add";		
		$bool_inventoryproductline_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_add_permission'));
	}

	public function view($id = null) {
		if (!$this->Department->exists($id)) {
			throw new NotFoundException(__('Invalid department'));
		}
		
		$options = array('conditions' => array('Department.' . $this->Department->primaryKey => $id));
		$this->set('department', $this->Department->find('first', $options));
		
		$aco_name="InventoryProductLines/index";		
		$bool_inventoryproductline_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_index_permission'));
		$aco_name="InventoryProductLines/add";		
		$bool_inventoryproductline_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_add_permission'));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Department->create();
			if ($this->Department->save($this->request->data)) {
				$this->Session->setFlash(__('The department has been saved.'),array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The department could not be saved. Please, try again.'),array('class' => 'error-message'));
			}
		}
		
		$aco_name="InventoryProductLines/index";		
		$bool_inventoryproductline_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_index_permission'));
		$aco_name="InventoryProductLines/add";		
		$bool_inventoryproductline_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->Department->exists($id)) {
			throw new NotFoundException(__('Invalid department'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Department->save($this->request->data)) {
				$this->Session->setFlash(__('The department has been saved.'),array('class' => 'success'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The department could not be saved. Please, try again.'),array('class' => 'error-message'),'default',array('class' => 'error-message'));
			}
		} 
		else {
			$options = array('conditions' => array('Department.' . $this->Department->primaryKey => $id));
			$this->request->data = $this->Department->find('first', $options);
		}
		
		$aco_name="InventoryProductLines/index";		
		$bool_inventoryproductline_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_index_permission'));
		$aco_name="InventoryProductLines/add";		
		$bool_inventoryproductline_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Department->id = $id;
		if (!$this->Department->exists()) {
			throw new NotFoundException(__('Invalid department'));
		}
		$this->request->allowMethod('post', 'delete');
		$department=$this->Department->find('first',array(
			'conditions'=>array(
				'Department.id'=>$id,
			),
		));
		
		if ($this->Department->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$department['Department']['id'];
			$deletionArray['Deletion']['reference']=$department['Department']['name'];
			$deletionArray['Deletion']['type']='Department';
			$this->Deletion->save($deletionArray);
		
			$this->Session->setFlash(__('The department has been deleted.'));
		} else {
			$this->Session->setFlash(__('The department could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
