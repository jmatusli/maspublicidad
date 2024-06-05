<?php
App::uses('AppController', 'Controller');
/**
 * SystemEmailRecipients Controller
 *
 * @property SystemEmailRecipient $SystemEmailRecipient
 * @property PaginatorComponent $Paginator
 */
class SystemEmailRecipientsController extends AppController {

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
		$this->SystemEmailRecipient->recursive = -1;
		
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
		
		$systemEmailRecipientCount=	$this->SystemEmailRecipient->find('count', array(
			'fields'=>array('SystemEmailRecipient.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($systemEmailRecipientCount!=0?$systemEmailRecipientCount:1),
		);

		$systemEmailRecipients = $this->Paginator->paginate('SystemEmailRecipient');
		$this->set(compact('systemEmailRecipients'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->SystemEmailRecipient->exists($id)) {
			throw new NotFoundException(__('Invalid system email recipient'));
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
		$options = array('conditions' => array('SystemEmailRecipient.' . $this->SystemEmailRecipient->primaryKey => $id));
		$this->set('systemEmailRecipient', $this->SystemEmailRecipient->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SystemEmailRecipient->create();
			if ($this->SystemEmailRecipient->save($this->request->data)) {
				$this->Session->setFlash(__('The system email recipient has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email recipient could not be saved. Please, try again.'));
			}
		}
		$systemEmails = $this->SystemEmailRecipient->SystemEmail->find('list');
		$this->set(compact('systemEmails'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->SystemEmailRecipient->exists($id)) {
			throw new NotFoundException(__('Invalid system email recipient'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SystemEmailRecipient->save($this->request->data)) {
				$this->Session->setFlash(__('The system email recipient has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email recipient could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SystemEmailRecipient.' . $this->SystemEmailRecipient->primaryKey => $id));
			$this->request->data = $this->SystemEmailRecipient->find('first', $options);
		}
		$systemEmails = $this->SystemEmailRecipient->SystemEmail->find('list');
		$this->set(compact('systemEmails'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->SystemEmailRecipient->id = $id;
		if (!$this->SystemEmailRecipient->exists()) {
			throw new NotFoundException(__('Invalid system email recipient'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SystemEmailRecipient->delete()) {
			$this->Session->setFlash(__('The system email recipient has been deleted.'));
		} else {
			$this->Session->setFlash(__('The system email recipient could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
