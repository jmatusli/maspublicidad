<?php
App::uses('AppController', 'Controller');
/**
 * MessageRecipients Controller
 *
 * @property MessageRecipient $MessageRecipient
 * @property PaginatorComponent $Paginator
 */
class MessageRecipientsController extends AppController {

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
		$this->MessageRecipient->recursive = -1;
		
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
		
		$messageRecipientCount=	$this->MessageRecipient->find('count', array(
			'fields'=>array('MessageRecipient.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($messageRecipientCount!=0?$messageRecipientCount:1),
		);

		$messageRecipients = $this->Paginator->paginate('MessageRecipient');
		$this->set(compact('messageRecipients'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->MessageRecipient->exists($id)) {
			throw new NotFoundException(__('Invalid message recipient'));
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
		$options = array('conditions' => array('MessageRecipient.' . $this->MessageRecipient->primaryKey => $id));
		$this->set('messageRecipient', $this->MessageRecipient->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->MessageRecipient->create();
			if ($this->MessageRecipient->save($this->request->data)) {
				$this->Session->setFlash(__('The message recipient has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message recipient could not be saved. Please, try again.'));
			}
		}
		$messages = $this->MessageRecipient->Message->find('list');
		$recipientUsers = $this->MessageRecipient->RecipientUser->find('list');
		$this->set(compact('messages', 'recipientUsers'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->MessageRecipient->exists($id)) {
			throw new NotFoundException(__('Invalid message recipient'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MessageRecipient->save($this->request->data)) {
				$this->Session->setFlash(__('The message recipient has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message recipient could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('MessageRecipient.' . $this->MessageRecipient->primaryKey => $id));
			$this->request->data = $this->MessageRecipient->find('first', $options);
		}
		$messages = $this->MessageRecipient->Message->find('list');
		$recipientUsers = $this->MessageRecipient->RecipientUser->find('list');
		$this->set(compact('messages', 'recipientUsers'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->MessageRecipient->id = $id;
		if (!$this->MessageRecipient->exists()) {
			throw new NotFoundException(__('Invalid message recipient'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->MessageRecipient->delete()) {
			$this->Session->setFlash(__('The message recipient has been deleted.'));
		} else {
			$this->Session->setFlash(__('The message recipient could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
