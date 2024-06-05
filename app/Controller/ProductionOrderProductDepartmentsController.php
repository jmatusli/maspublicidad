<?php
App::uses('AppController', 'Controller');
/**
 * ProductionOrderProductDepartments Controller
 *
 * @property ProductionOrderProductDepartment $ProductionOrderProductDepartment
 * @property PaginatorComponent $Paginator
 */
class ProductionOrderProductDepartmentsController extends AppController {

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
		$this->ProductionOrderProductDepartment->recursive = -1;
		
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
		
		$productionOrderProductDepartmentCount=	$this->ProductionOrderProductDepartment->find('count', array(
			'fields'=>array('ProductionOrderProductDepartment.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($productionOrderProductDepartmentCount!=0?$productionOrderProductDepartmentCount:1),
		);

		$productionOrderProductDepartments = $this->Paginator->paginate('ProductionOrderProductDepartment');
		$this->set(compact('productionOrderProductDepartments'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ProductionOrderProductDepartment->exists($id)) {
			throw new NotFoundException(__('Invalid production order product department'));
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
		$options = array('conditions' => array('ProductionOrderProductDepartment.' . $this->ProductionOrderProductDepartment->primaryKey => $id));
		$this->set('productionOrderProductDepartment', $this->ProductionOrderProductDepartment->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ProductionOrderProductDepartment->create();
			if ($this->ProductionOrderProductDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The production order product department has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production order product department could not be saved. Please, try again.'));
			}
		}
		$productionOrderProducts = $this->ProductionOrderProductDepartment->ProductionOrderProduct->find('list');
		$departments = $this->ProductionOrderProductDepartment->Department->find('list');
		$this->set(compact('productionOrderProducts', 'departments'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ProductionOrderProductDepartment->exists($id)) {
			throw new NotFoundException(__('Invalid production order product department'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ProductionOrderProductDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The production order product department has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production order product department could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ProductionOrderProductDepartment.' . $this->ProductionOrderProductDepartment->primaryKey => $id));
			$this->request->data = $this->ProductionOrderProductDepartment->find('first', $options);
		}
		$productionOrderProducts = $this->ProductionOrderProductDepartment->ProductionOrderProduct->find('list');
		$departments = $this->ProductionOrderProductDepartment->Department->find('list');
		$this->set(compact('productionOrderProducts', 'departments'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionOrderProductDepartment->id = $id;
		if (!$this->ProductionOrderProductDepartment->exists()) {
			throw new NotFoundException(__('Invalid production order product department'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductionOrderProductDepartment->delete()) {
			$this->Session->setFlash(__('The production order product department has been deleted.'));
		} else {
			$this->Session->setFlash(__('The production order product department could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
