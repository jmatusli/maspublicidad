<?php
App::uses('AppController', 'Controller');
/**
 * InvoiceSalesOrders Controller
 *
 * @property InvoiceSalesOrder $InvoiceSalesOrder
 * @property PaginatorComponent $Paginator
 */
class InvoiceSalesOrdersController extends AppController {

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
		$this->InvoiceSalesOrder->recursive = -1;
		
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
		
		$invoiceSalesOrderCount=	$this->InvoiceSalesOrder->find('count', array(
			'fields'=>array('InvoiceSalesOrder.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($invoiceSalesOrderCount!=0?$invoiceSalesOrderCount:1),
		);

		$invoiceSalesOrders = $this->Paginator->paginate('InvoiceSalesOrder');
		$this->set(compact('invoiceSalesOrders'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->InvoiceSalesOrder->exists($id)) {
			throw new NotFoundException(__('Invalid invoice sales order'));
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
		$options = array('conditions' => array('InvoiceSalesOrder.' . $this->InvoiceSalesOrder->primaryKey => $id));
		$this->set('invoiceSalesOrder', $this->InvoiceSalesOrder->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->InvoiceSalesOrder->create();
			if ($this->InvoiceSalesOrder->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice sales order has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice sales order could not be saved. Please, try again.'));
			}
		}
		$invoices = $this->InvoiceSalesOrder->Invoice->find('list');
		$salesOrders = $this->InvoiceSalesOrder->SalesOrder->find('list');
		$this->set(compact('invoices', 'salesOrders'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->InvoiceSalesOrder->exists($id)) {
			throw new NotFoundException(__('Invalid invoice sales order'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->InvoiceSalesOrder->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice sales order has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice sales order could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('InvoiceSalesOrder.' . $this->InvoiceSalesOrder->primaryKey => $id));
			$this->request->data = $this->InvoiceSalesOrder->find('first', $options);
		}
		$invoices = $this->InvoiceSalesOrder->Invoice->find('list');
		$salesOrders = $this->InvoiceSalesOrder->SalesOrder->find('list');
		$this->set(compact('invoices', 'salesOrders'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InvoiceSalesOrder->id = $id;
		if (!$this->InvoiceSalesOrder->exists()) {
			throw new NotFoundException(__('Invalid invoice sales order'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->InvoiceSalesOrder->delete()) {
			$this->Session->setFlash(__('The invoice sales order has been deleted.'));
		} else {
			$this->Session->setFlash(__('The invoice sales order could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
