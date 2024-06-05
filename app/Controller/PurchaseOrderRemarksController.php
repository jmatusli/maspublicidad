<?php
App::uses('AppController', 'Controller');
/**
 * PurchaseOrderRemarks Controller
 *
 * @property PurchaseOrderRemark $PurchaseOrderRemark
 * @property PaginatorComponent $Paginator
 */
class PurchaseOrderRemarksController extends AppController {

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
		$this->PurchaseOrderRemark->recursive = -1;
		
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
		
		$purchaseOrderRemarkCount=	$this->PurchaseOrderRemark->find('count', array(
			'fields'=>array('PurchaseOrderRemark.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($purchaseOrderRemarkCount!=0?$purchaseOrderRemarkCount:1),
		);

		$purchaseOrderRemarks = $this->Paginator->paginate('PurchaseOrderRemark');
		$this->set(compact('purchaseOrderRemarks'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->PurchaseOrderRemark->exists($id)) {
			throw new NotFoundException(__('Invalid purchase order remark'));
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
		$options = array('conditions' => array('PurchaseOrderRemark.' . $this->PurchaseOrderRemark->primaryKey => $id));
		$this->set('purchaseOrderRemark', $this->PurchaseOrderRemark->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->PurchaseOrderRemark->create();
			if ($this->PurchaseOrderRemark->save($this->request->data)) {
				$this->Session->setFlash(__('The purchase order remark has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The purchase order remark could not be saved. Please, try again.'));
			}
		}
		$users = $this->PurchaseOrderRemark->User->find('list');
		$purchaseOrders = $this->PurchaseOrderRemark->PurchaseOrder->find('list');
		$actionTypes = $this->PurchaseOrderRemark->ActionType->find('list');
		$this->set(compact('users', 'purchaseOrders', 'actionTypes'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->PurchaseOrderRemark->exists($id)) {
			throw new NotFoundException(__('Invalid purchase order remark'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->PurchaseOrderRemark->save($this->request->data)) {
				$this->Session->setFlash(__('The purchase order remark has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The purchase order remark could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('PurchaseOrderRemark.' . $this->PurchaseOrderRemark->primaryKey => $id));
			$this->request->data = $this->PurchaseOrderRemark->find('first', $options);
		}
		$users = $this->PurchaseOrderRemark->User->find('list');
		$purchaseOrders = $this->PurchaseOrderRemark->PurchaseOrder->find('list');
		$actionTypes = $this->PurchaseOrderRemark->ActionType->find('list');
		$this->set(compact('users', 'purchaseOrders', 'actionTypes'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->PurchaseOrderRemark->id = $id;
		if (!$this->PurchaseOrderRemark->exists()) {
			throw new NotFoundException(__('Invalid purchase order remark'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->PurchaseOrderRemark->delete()) {
			$this->Session->setFlash(__('The purchase order remark has been deleted.'));
		} else {
			$this->Session->setFlash(__('The purchase order remark could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
