<?php
App::uses('AppController', 'Controller');
/**
 * ProductionOrderRemarks Controller
 *
 * @property ProductionOrderRemark $ProductionOrderRemark
 * @property PaginatorComponent $Paginator
 */
class ProductionOrderRemarksController extends AppController {

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
		$this->ProductionOrderRemark->recursive = -1;
		
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
		
		$productionOrderRemarkCount=	$this->ProductionOrderRemark->find('count', array(
			'fields'=>array('ProductionOrderRemark.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($productionOrderRemarkCount!=0?$productionOrderRemarkCount:1),
		);

		$productionOrderRemarks = $this->Paginator->paginate('ProductionOrderRemark');
		$this->set(compact('productionOrderRemarks'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ProductionOrderRemark->exists($id)) {
			throw new NotFoundException(__('Invalid production order remark'));
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
		$options = array('conditions' => array('ProductionOrderRemark.' . $this->ProductionOrderRemark->primaryKey => $id));
		$this->set('productionOrderRemark', $this->ProductionOrderRemark->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ProductionOrderRemark->create();
			if ($this->ProductionOrderRemark->save($this->request->data)) {
				$this->Session->setFlash(__('The production order remark has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production order remark could not be saved. Please, try again.'));
			}
		}
		$users = $this->ProductionOrderRemark->User->find('list');
		$productionOrders = $this->ProductionOrderRemark->ProductionOrder->find('list');
		$this->set(compact('users', 'productionOrders'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ProductionOrderRemark->exists($id)) {
			throw new NotFoundException(__('Invalid production order remark'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ProductionOrderRemark->save($this->request->data)) {
				$this->Session->setFlash(__('The production order remark has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The production order remark could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('ProductionOrderRemark.' . $this->ProductionOrderRemark->primaryKey => $id));
			$this->request->data = $this->ProductionOrderRemark->find('first', $options);
		}
		$users = $this->ProductionOrderRemark->User->find('list');
		$productionOrders = $this->ProductionOrderRemark->ProductionOrder->find('list');
		$this->set(compact('users', 'productionOrders'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionOrderRemark->id = $id;
		if (!$this->ProductionOrderRemark->exists()) {
			throw new NotFoundException(__('Invalid production order remark'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductionOrderRemark->delete()) {
			$this->Session->setFlash(__('The production order remark has been deleted.'));
		} else {
			$this->Session->setFlash(__('The production order remark could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
