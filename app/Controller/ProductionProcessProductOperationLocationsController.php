<?php
App::uses('AppController', 'Controller');
/**
 * ProductionProcessProductOperationLocations Controller
 *
 * @property ProductionProcessProductOperationLocation $ProductionProcessProductOperationLocation
 * @property PaginatorComponent $Paginator
 */
class ProductionProcessProductOperationLocationsController extends AppController {

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
		$this->ProductionProcessProductOperationLocation->recursive = -1;
		
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
		
		$productionProcessProductOperationLocationCount=	$this->ProductionProcessProductOperationLocation->find('count', array(
			'fields'=>array('ProductionProcessProductOperationLocation.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($productionProcessProductOperationLocationCount!=0?$productionProcessProductOperationLocationCount:1),
		);

		$productionProcessProductOperationLocations = $this->Paginator->paginate('ProductionProcessProductOperationLocation');
		$this->set(compact('productionProcessProductOperationLocations'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ProductionProcessProductOperationLocation->exists($id)) {
			throw new NotFoundException(__('Invalid production process product operation location'));
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
		$options = array('conditions' => array('ProductionProcessProductOperationLocation.' . $this->ProductionProcessProductOperationLocation->primaryKey => $id));
		$this->set('productionProcessProductOperationLocation', $this->ProductionProcessProductOperationLocation->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ProductionProcessProductOperationLocation->create();
			if ($this->ProductionProcessProductOperationLocation->save($this->request->data)) {
				$this->Session->setFlash(__('The production process product operation location has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production process product operation location could not be saved. Please, try again.'));
			}
		}
		$productionProcessProducts = $this->ProductionProcessProductOperationLocation->ProductionProcessProduct->find('list');
		$operationLocations = $this->ProductionProcessProductOperationLocation->OperationLocation->find('list');
		$this->set(compact('productionProcessProducts', 'operationLocations'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ProductionProcessProductOperationLocation->exists($id)) {
			throw new NotFoundException(__('Invalid production process product operation location'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ProductionProcessProductOperationLocation->save($this->request->data)) {
				$this->Session->setFlash(__('The production process product operation location has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production process product operation location could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ProductionProcessProductOperationLocation.' . $this->ProductionProcessProductOperationLocation->primaryKey => $id));
			$this->request->data = $this->ProductionProcessProductOperationLocation->find('first', $options);
		}
		$productionProcessProducts = $this->ProductionProcessProductOperationLocation->ProductionProcessProduct->find('list');
		$operationLocations = $this->ProductionProcessProductOperationLocation->OperationLocation->find('list');
		$this->set(compact('productionProcessProducts', 'operationLocations'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionProcessProductOperationLocation->id = $id;
		if (!$this->ProductionProcessProductOperationLocation->exists()) {
			throw new NotFoundException(__('Invalid production process product operation location'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductionProcessProductOperationLocation->delete()) {
			$this->Session->setFlash(__('The production process product operation location has been deleted.'));
		} else {
			$this->Session->setFlash(__('The production process product operation location could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
