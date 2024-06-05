<?php
App::uses('AppController', 'Controller');
/**
 * InvoiceProducts Controller
 *
 * @property InvoiceProduct $InvoiceProduct
 * @property PaginatorComponent $Paginator
 */
class InvoiceProductsController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->InvoiceProduct->recursive = -1;
		
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
		
		$invoiceProductCount=	$this->InvoiceProduct->find('count', array(
			'fields'=>array('InvoiceProduct.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($invoiceProductCount!=0?$invoiceProductCount:1),
		);

		$invoiceProducts = $this->Paginator->paginate('InvoiceProduct');
		$this->set(compact('invoiceProducts'));
	}

	public function view($id = null) {
		if (!$this->InvoiceProduct->exists($id)) {
			throw new NotFoundException(__('Invalid invoice product'));
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
		$options = array('conditions' => array('InvoiceProduct.' . $this->InvoiceProduct->primaryKey => $id));
		$this->set('invoiceProduct', $this->InvoiceProduct->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->InvoiceProduct->create();
			if ($this->InvoiceProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice product has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice product could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		$invoices = $this->InvoiceProduct->Invoice->find('list');
		$products = $this->InvoiceProduct->Product->find('list');
		$currencies = $this->InvoiceProduct->Currency->find('list');
		$this->set(compact('invoices', 'products', 'currencies'));
	}

	public function edit($id = null) {
		if (!$this->InvoiceProduct->exists($id)) {
			throw new NotFoundException(__('Invalid invoice product'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->InvoiceProduct->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice product has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice product could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} else {
			$options = array('conditions' => array('InvoiceProduct.' . $this->InvoiceProduct->primaryKey => $id));
			$this->request->data = $this->InvoiceProduct->find('first', $options);
		}
		$invoices = $this->InvoiceProduct->Invoice->find('list');
		$products = $this->InvoiceProduct->Product->find('list');
		$currencies = $this->InvoiceProduct->Currency->find('list');
		$this->set(compact('invoices', 'products', 'currencies'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InvoiceProduct->id = $id;
		if (!$this->InvoiceProduct->exists()) {
			throw new NotFoundException(__('Invalid invoice product'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->InvoiceProduct->delete()) {
			$this->Session->setFlash(__('The invoice product has been deleted.'));
		} else {
			$this->Session->setFlash(__('The invoice product could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
