<?php
App::uses('AppController', 'Controller');
/**
 * Departments Controller
 *
 * @property Department $Department
 * @property PaginatorComponent $Paginator
 */
class InventoryDepartmentsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->InventoryDepartment->recursive = -1;
		
		$inventoryDepartmentCount=	$this->InventoryDepartment->find('count', array(
			'fields'=>array('InventoryDepartment.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
				'InventoryProductLine',
			),
			'limit'=>($inventoryDepartmentCount!=0?$inventoryDepartmentCount:1),
		);

		$inventoryDepartments = $this->Paginator->paginate('InventoryDepartment');
		$this->set(compact('inventoryDepartments'));
	}

	public function view($id = null) {
		if (!$this->InventoryDepartment->exists($id)) {
			throw new NotFoundException(__('Invalid department'));
		}
		
		$options = array('conditions' => array('InventoryDepartment.' . $this->InventoryDepartment->primaryKey => $id));
		$this->set('inventoryDepartment', $this->InventoryDepartment->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->InventoryDepartment->create();
			if ($this->InventoryDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The department has been saved.'),array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The department could not be saved. Please, try again.'),array('class' => 'error-message'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->InventoryDepartment->exists($id)) {
			throw new NotFoundException(__('Invalid department'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->InventoryDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The department has been saved.'),array('class' => 'success'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The department could not be saved. Please, try again.'),array('class' => 'error-message'),'default',array('class' => 'error-message'));
			}
		} 
		else {
			$options = array('conditions' => array('InventoryDepartment.' . $this->InventoryDepartment->primaryKey => $id));
			$this->request->data = $this->InventoryDepartment->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InventoryDepartment->id = $id;
		if (!$this->InventoryDepartment->exists()) {
			throw new NotFoundException(__('Invalid department'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->InventoryDepartment->delete()) {
			$this->Session->setFlash(__('The department has been deleted.'));
		} else {
			$this->Session->setFlash(__('The department could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
