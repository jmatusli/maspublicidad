<?php
App::uses('AppController', 'Controller');
/**
 * SystemEmailAttachments Controller
 *
 * @property SystemEmailAttachment $SystemEmailAttachment
 * @property PaginatorComponent $Paginator
 */
class SystemEmailAttachmentsController extends AppController {

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
		$this->SystemEmailAttachment->recursive = -1;
		
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
		
		$systemEmailAttachmentCount=	$this->SystemEmailAttachment->find('count', array(
			'fields'=>array('SystemEmailAttachment.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($systemEmailAttachmentCount!=0?$systemEmailAttachmentCount:1),
		);

		$systemEmailAttachments = $this->Paginator->paginate('SystemEmailAttachment');
		$this->set(compact('systemEmailAttachments'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->SystemEmailAttachment->exists($id)) {
			throw new NotFoundException(__('Invalid system email attachment'));
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
		$options = array('conditions' => array('SystemEmailAttachment.' . $this->SystemEmailAttachment->primaryKey => $id));
		$this->set('systemEmailAttachment', $this->SystemEmailAttachment->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SystemEmailAttachment->create();
			if ($this->SystemEmailAttachment->save($this->request->data)) {
				$this->Session->setFlash(__('The system email attachment has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email attachment could not be saved. Please, try again.'));
			}
		}
		$systemEmails = $this->SystemEmailAttachment->SystemEmail->find('list');
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
		if (!$this->SystemEmailAttachment->exists($id)) {
			throw new NotFoundException(__('Invalid system email attachment'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SystemEmailAttachment->save($this->request->data)) {
				$this->Session->setFlash(__('The system email attachment has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email attachment could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SystemEmailAttachment.' . $this->SystemEmailAttachment->primaryKey => $id));
			$this->request->data = $this->SystemEmailAttachment->find('first', $options);
		}
		$systemEmails = $this->SystemEmailAttachment->SystemEmail->find('list');
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
		$this->SystemEmailAttachment->id = $id;
		if (!$this->SystemEmailAttachment->exists()) {
			throw new NotFoundException(__('Invalid system email attachment'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SystemEmailAttachment->delete()) {
			$this->Session->setFlash(__('The system email attachment has been deleted.'));
		} else {
			$this->Session->setFlash(__('The system email attachment could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
