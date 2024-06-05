<?php
App::uses('AppController', 'Controller');
/**
 * InvoiceRemarks Controller
 *
 * @property InvoiceRemark $InvoiceRemark
 * @property PaginatorComponent $Paginator
 */
class InvoiceRemarksController extends AppController {

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
		$this->InvoiceRemark->recursive = -1;
		
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
		
		$invoiceRemarkCount=	$this->InvoiceRemark->find('count', array(
			'fields'=>array('InvoiceRemark.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($invoiceRemarkCount!=0?$invoiceRemarkCount:1),
		);

		$invoiceRemarks = $this->Paginator->paginate('InvoiceRemark');
		$this->set(compact('invoiceRemarks'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->InvoiceRemark->exists($id)) {
			throw new NotFoundException(__('Invalid invoice remark'));
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
		$options = array('conditions' => array('InvoiceRemark.' . $this->InvoiceRemark->primaryKey => $id));
		$this->set('invoiceRemark', $this->InvoiceRemark->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->InvoiceRemark->create();
			if ($this->InvoiceRemark->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice remark has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice remark could not be saved. Please, try again.'));
			}
		}
		$users = $this->InvoiceRemark->User->find('list');
		$invoices = $this->InvoiceRemark->Invoice->find('list');
		$actionTypes = $this->InvoiceRemark->ActionType->find('list');
		$this->set(compact('users', 'invoices', 'actionTypes'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->InvoiceRemark->exists($id)) {
			throw new NotFoundException(__('Invalid invoice remark'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->InvoiceRemark->save($this->request->data)) {
				$this->Session->setFlash(__('The invoice remark has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The invoice remark could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('InvoiceRemark.' . $this->InvoiceRemark->primaryKey => $id));
			$this->request->data = $this->InvoiceRemark->find('first', $options);
		}
		$users = $this->InvoiceRemark->User->find('list');
		$invoices = $this->InvoiceRemark->Invoice->find('list');
		$actionTypes = $this->InvoiceRemark->ActionType->find('list');
		$this->set(compact('users', 'invoices', 'actionTypes'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InvoiceRemark->id = $id;
		if (!$this->InvoiceRemark->exists()) {
			throw new NotFoundException(__('Invalid invoice remark'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->InvoiceRemark->delete()) {
			$this->Session->setFlash(__('The invoice remark has been deleted.'));
		} else {
			$this->Session->setFlash(__('The invoice remark could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
