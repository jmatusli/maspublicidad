<?php
App::uses('AppController', 'Controller');
/**
 * ProductionProcessRemarks Controller
 *
 * @property ProductionProcessRemark $ProductionProcessRemark
 * @property PaginatorComponent $Paginator
 */
class ProductionProcessRemarksController extends AppController {

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
		$this->ProductionProcessRemark->recursive = -1;
		
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
		
		$productionProcessRemarkCount=	$this->ProductionProcessRemark->find('count', array(
			'fields'=>array('ProductionProcessRemark.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($productionProcessRemarkCount!=0?$productionProcessRemarkCount:1),
		);

		$productionProcessRemarks = $this->Paginator->paginate('ProductionProcessRemark');
		$this->set(compact('productionProcessRemarks'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ProductionProcessRemark->exists($id)) {
			throw new NotFoundException(__('Invalid production process remark'));
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
		$options = array('conditions' => array('ProductionProcessRemark.' . $this->ProductionProcessRemark->primaryKey => $id));
		$this->set('productionProcessRemark', $this->ProductionProcessRemark->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ProductionProcessRemark->create();
			if ($this->ProductionProcessRemark->save($this->request->data)) {
				$this->Session->setFlash(__('The production process remark has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production process remark could not be saved. Please, try again.'));
			}
		}
		$users = $this->ProductionProcessRemark->User->find('list');
		$productionProcesses = $this->ProductionProcessRemark->ProductionProcess->find('list');
		$actionTypes = $this->ProductionProcessRemark->ActionType->find('list');
		$this->set(compact('users', 'productionProcesses', 'actionTypes'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ProductionProcessRemark->exists($id)) {
			throw new NotFoundException(__('Invalid production process remark'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ProductionProcessRemark->save($this->request->data)) {
				$this->Session->setFlash(__('The production process remark has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production process remark could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ProductionProcessRemark.' . $this->ProductionProcessRemark->primaryKey => $id));
			$this->request->data = $this->ProductionProcessRemark->find('first', $options);
		}
		$users = $this->ProductionProcessRemark->User->find('list');
		$productionProcesses = $this->ProductionProcessRemark->ProductionProcess->find('list');
		$actionTypes = $this->ProductionProcessRemark->ActionType->find('list');
		$this->set(compact('users', 'productionProcesses', 'actionTypes'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionProcessRemark->id = $id;
		if (!$this->ProductionProcessRemark->exists()) {
			throw new NotFoundException(__('Invalid production process remark'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductionProcessRemark->delete()) {
			$this->Session->setFlash(__('The production process remark has been deleted.'));
		} else {
			$this->Session->setFlash(__('The production process remark could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
