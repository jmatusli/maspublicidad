<?php
App::uses('AppController', 'Controller');
/**
 * ProductionOrderProductOperationLocations Controller
 *
 * @property ProductionOrderProductOperationLocation $ProductionOrderProductOperationLocation
 * @property PaginatorComponent $Paginator
 */
class ProductionOrderProductOperationLocationsController extends AppController {

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
		$this->ProductionOrderProductOperationLocation->recursive = -1;
		
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
		
		$productionOrderProductOperationLocationCount=	$this->ProductionOrderProductOperationLocation->find('count', array(
			'fields'=>array('ProductionOrderProductOperationLocation.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($productionOrderProductOperationLocationCount!=0?$productionOrderProductOperationLocationCount:1),
		);

		$productionOrderProductOperationLocations = $this->Paginator->paginate('ProductionOrderProductOperationLocation');
		$this->set(compact('productionOrderProductOperationLocations'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ProductionOrderProductOperationLocation->exists($id)) {
			throw new NotFoundException(__('Invalid production order product operation location'));
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
		$options = array('conditions' => array('ProductionOrderProductOperationLocation.' . $this->ProductionOrderProductOperationLocation->primaryKey => $id));
		$this->set('productionOrderProductOperationLocation', $this->ProductionOrderProductOperationLocation->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ProductionOrderProductOperationLocation->create();
			if ($this->ProductionOrderProductOperationLocation->save($this->request->data)) {
				$this->Session->setFlash(__('The production order product operation location has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production order product operation location could not be saved. Please, try again.'));
			}
		}
		$productionOrderProducts = $this->ProductionOrderProductOperationLocation->ProductionOrderProduct->find('list');
		$operationLocations = $this->ProductionOrderProductOperationLocation->OperationLocation->find('list');
		$this->set(compact('productionOrderProducts', 'operationLocations'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ProductionOrderProductOperationLocation->exists($id)) {
			throw new NotFoundException(__('Invalid production order product operation location'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ProductionOrderProductOperationLocation->save($this->request->data)) {
				$this->Session->setFlash(__('The production order product operation location has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production order product operation location could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ProductionOrderProductOperationLocation.' . $this->ProductionOrderProductOperationLocation->primaryKey => $id));
			$this->request->data = $this->ProductionOrderProductOperationLocation->find('first', $options);
		}
		$productionOrderProducts = $this->ProductionOrderProductOperationLocation->ProductionOrderProduct->find('list');
		$operationLocations = $this->ProductionOrderProductOperationLocation->OperationLocation->find('list');
		$this->set(compact('productionOrderProducts', 'operationLocations'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionOrderProductOperationLocation->id = $id;
		if (!$this->ProductionOrderProductOperationLocation->exists()) {
			throw new NotFoundException(__('Invalid production order product operation location'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductionOrderProductOperationLocation->delete()) {
			$this->Session->setFlash(__('The production order product operation location has been deleted.'));
		} else {
			$this->Session->setFlash(__('The production order product operation location could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
