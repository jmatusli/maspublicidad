<?php
App::uses('AppController', 'Controller');
/**
 * SalesOrderProductDepartments Controller
 *
 * @property SalesOrderProductDepartment $SalesOrderProductDepartment
 * @property PaginatorComponent $Paginator
 */
class SalesOrderProductDepartmentsController extends AppController {

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
		$this->SalesOrderProductDepartment->recursive = -1;
		
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
		
		$salesOrderProductDepartmentCount=	$this->SalesOrderProductDepartment->find('count', array(
			'fields'=>array('SalesOrderProductDepartment.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($salesOrderProductDepartmentCount!=0?$salesOrderProductDepartmentCount:1),
		);

		$salesOrderProductDepartments = $this->Paginator->paginate('SalesOrderProductDepartment');
		$this->set(compact('salesOrderProductDepartments'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->SalesOrderProductDepartment->exists($id)) {
			throw new NotFoundException(__('Invalid sales order product department'));
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
		$options = array('conditions' => array('SalesOrderProductDepartment.' . $this->SalesOrderProductDepartment->primaryKey => $id));
		$this->set('salesOrderProductDepartment', $this->SalesOrderProductDepartment->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SalesOrderProductDepartment->create();
			if ($this->SalesOrderProductDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The sales order product department has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The sales order product department could not be saved. Please, try again.'));
			}
		}
		$salesOrderProducts = $this->SalesOrderProductDepartment->SalesOrderProduct->find('list');
		$departments = $this->SalesOrderProductDepartment->Department->find('list');
		$this->set(compact('salesOrderProducts', 'departments'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->SalesOrderProductDepartment->exists($id)) {
			throw new NotFoundException(__('Invalid sales order product department'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SalesOrderProductDepartment->save($this->request->data)) {
				$this->Session->setFlash(__('The sales order product department has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The sales order product department could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SalesOrderProductDepartment.' . $this->SalesOrderProductDepartment->primaryKey => $id));
			$this->request->data = $this->SalesOrderProductDepartment->find('first', $options);
		}
		$salesOrderProducts = $this->SalesOrderProductDepartment->SalesOrderProduct->find('list');
		$departments = $this->SalesOrderProductDepartment->Department->find('list');
		$this->set(compact('salesOrderProducts', 'departments'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->SalesOrderProductDepartment->id = $id;
		if (!$this->SalesOrderProductDepartment->exists()) {
			throw new NotFoundException(__('Invalid sales order product department'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SalesOrderProductDepartment->delete()) {
			$this->Session->setFlash(__('The sales order product department has been deleted.'));
		} else {
			$this->Session->setFlash(__('The sales order product department could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
