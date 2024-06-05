<?php
App::uses('AppController', 'Controller');
/**
 * InventoryDepartments Controller
 *
 * @property InventoryDepartment $InventoryDepartment
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
		
		$inventoryDepartmentCount=	$this->InventoryDepartment->find('count', array(
			'fields'=>array('InventoryDepartment.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($inventoryDepartmentCount!=0?$inventoryDepartmentCount:1),
		);

		$inventoryDepartments = $this->Paginator->paginate('InventoryDepartment');
		$this->set(compact('inventoryDepartments'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->InventoryDepartment->exists($id)) {
			throw new NotFoundException(__('Invalid inventory department'));
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
		$options = array('conditions' => array('InventoryDepartment.' . $this->InventoryDepartment->primaryKey => $id));
		$this->set('inventoryDepartment', $this->InventoryDepartment->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->InventoryDepartment->create();
			if ($this->InventoryDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory department has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The inventory department could not be saved. Please, try again.'));
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->InventoryDepartment->exists($id)) {
			throw new NotFoundException(__('Invalid inventory department'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->InventoryDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory department has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The inventory department could not be saved. Please, try again.'));
			}
		} else {
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
			throw new NotFoundException(__('Invalid inventory department'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->InventoryDepartment->delete()) {
			$this->Session->setFlash(__('The inventory department has been deleted.'));
		} else {
			$this->Session->setFlash(__('The inventory department could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
