<?php
App::uses('AppController', 'Controller');
/**
 * PurchaseOrderOtherCosts Controller
 *
 * @property PurchaseOrderOtherCost $PurchaseOrderOtherCost
 * @property PaginatorComponent $Paginator
 */
class PurchaseOrderOtherCostsController extends AppController {

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
		$this->PurchaseOrderOtherCost->recursive = -1;
		
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
		
		$purchaseOrderOtherCostCount=	$this->PurchaseOrderOtherCost->find('count', array(
			'fields'=>array('PurchaseOrderOtherCost.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($purchaseOrderOtherCostCount!=0?$purchaseOrderOtherCostCount:1),
		);

		$purchaseOrderOtherCosts = $this->Paginator->paginate('PurchaseOrderOtherCost');
		$this->set(compact('purchaseOrderOtherCosts'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->PurchaseOrderOtherCost->exists($id)) {
			throw new NotFoundException(__('Invalid purchase order other cost'));
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
		$options = array('conditions' => array('PurchaseOrderOtherCost.' . $this->PurchaseOrderOtherCost->primaryKey => $id));
		$this->set('purchaseOrderOtherCost', $this->PurchaseOrderOtherCost->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->PurchaseOrderOtherCost->create();
			if ($this->PurchaseOrderOtherCost->save($this->request->data)) {
				$this->Session->setFlash(__('The purchase order other cost has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The purchase order other cost could not be saved. Please, try again.'));
			}
		}
		$purchaseOrders = $this->PurchaseOrderOtherCost->PurchaseOrder->find('list');
		$departments = $this->PurchaseOrderOtherCost->Department->find('list');
		$this->set(compact('purchaseOrders', 'departments'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->PurchaseOrderOtherCost->exists($id)) {
			throw new NotFoundException(__('Invalid purchase order other cost'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->PurchaseOrderOtherCost->save($this->request->data)) {
				$this->Session->setFlash(__('The purchase order other cost has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The purchase order other cost could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('PurchaseOrderOtherCost.' . $this->PurchaseOrderOtherCost->primaryKey => $id));
			$this->request->data = $this->PurchaseOrderOtherCost->find('first', $options);
		}
		$purchaseOrders = $this->PurchaseOrderOtherCost->PurchaseOrder->find('list');
		$departments = $this->PurchaseOrderOtherCost->Department->find('list');
		$this->set(compact('purchaseOrders', 'departments'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->PurchaseOrderOtherCost->id = $id;
		if (!$this->PurchaseOrderOtherCost->exists()) {
			throw new NotFoundException(__('Invalid purchase order other cost'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->PurchaseOrderOtherCost->delete()) {
			$this->Session->setFlash(__('The purchase order other cost has been deleted.'));
		} else {
			$this->Session->setFlash(__('The purchase order other cost could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
