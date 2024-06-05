<?php
App::uses('AppController', 'Controller');
/**
 * SystemEmailCcs Controller
 *
 * @property SystemEmailCc $SystemEmailCc
 * @property PaginatorComponent $Paginator
 */
class SystemEmailCcsController extends AppController {

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
		$this->SystemEmailCc->recursive = -1;
		
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
		
		$systemEmailCcCount=	$this->SystemEmailCc->find('count', array(
			'fields'=>array('SystemEmailCc.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($systemEmailCcCount!=0?$systemEmailCcCount:1),
		);

		$systemEmailCcs = $this->Paginator->paginate('SystemEmailCc');
		$this->set(compact('systemEmailCcs'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->SystemEmailCc->exists($id)) {
			throw new NotFoundException(__('Invalid system email cc'));
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
		$options = array('conditions' => array('SystemEmailCc.' . $this->SystemEmailCc->primaryKey => $id));
		$this->set('systemEmailCc', $this->SystemEmailCc->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->SystemEmailCc->create();
			if ($this->SystemEmailCc->save($this->request->data)) {
				$this->Session->setFlash(__('The system email cc has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email cc could not be saved. Please, try again.'));
			}
		}
		$systemEmails = $this->SystemEmailCc->SystemEmail->find('list');
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
		if (!$this->SystemEmailCc->exists($id)) {
			throw new NotFoundException(__('Invalid system email cc'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SystemEmailCc->save($this->request->data)) {
				$this->Session->setFlash(__('The system email cc has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email cc could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SystemEmailCc.' . $this->SystemEmailCc->primaryKey => $id));
			$this->request->data = $this->SystemEmailCc->find('first', $options);
		}
		$systemEmails = $this->SystemEmailCc->SystemEmail->find('list');
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
		$this->SystemEmailCc->id = $id;
		if (!$this->SystemEmailCc->exists()) {
			throw new NotFoundException(__('Invalid system email cc'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SystemEmailCc->delete()) {
			$this->Session->setFlash(__('The system email cc has been deleted.'));
		} else {
			$this->Session->setFlash(__('The system email cc could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
