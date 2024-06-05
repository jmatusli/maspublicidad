<?php
App::uses('AppController', 'Controller');
/**
 * SystemEmailBccs Controller
 *
 * @property SystemEmailBcc $SystemEmailBcc
 * @property PaginatorComponent $Paginator
 */
class SystemEmailBccsController extends AppController {

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
		$this->SystemEmailBcc->recursive = -1;
		
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
		
		$systemEmailBccCount=	$this->SystemEmailBcc->find('count', array(
			'fields'=>array('SystemEmailBcc.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($systemEmailBccCount!=0?$systemEmailBccCount:1),
		);

		$systemEmailBccs = $this->Paginator->paginate('SystemEmailBcc');
		$this->set(compact('systemEmailBccs'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->SystemEmailBcc->exists($id)) {
			throw new NotFoundException(__('Invalid system email bcc'));
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
		$options = array('conditions' => array('SystemEmailBcc.' . $this->SystemEmailBcc->primaryKey => $id));
		$this->set('systemEmailBcc', $this->SystemEmailBcc->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SystemEmailBcc->create();
			if ($this->SystemEmailBcc->save($this->request->data)) {
				$this->Session->setFlash(__('The system email bcc has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email bcc could not be saved. Please, try again.'));
			}
		}
		$systemEmails = $this->SystemEmailBcc->SystemEmail->find('list');
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
		if (!$this->SystemEmailBcc->exists($id)) {
			throw new NotFoundException(__('Invalid system email bcc'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SystemEmailBcc->save($this->request->data)) {
				$this->Session->setFlash(__('The system email bcc has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email bcc could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SystemEmailBcc.' . $this->SystemEmailBcc->primaryKey => $id));
			$this->request->data = $this->SystemEmailBcc->find('first', $options);
		}
		$systemEmails = $this->SystemEmailBcc->SystemEmail->find('list');
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
		$this->SystemEmailBcc->id = $id;
		if (!$this->SystemEmailBcc->exists()) {
			throw new NotFoundException(__('Invalid system email bcc'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SystemEmailBcc->delete()) {
			$this->Session->setFlash(__('The system email bcc has been deleted.'));
		} else {
			$this->Session->setFlash(__('The system email bcc could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
