<?php
App::uses('AppController', 'Controller');

class TasksController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->Task->recursive = -1;
		
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
		
    $conditions=[
      'Task.created >='=>$startDate,
      'Task.created <'=>$endDatePlusOne,
    ];
    
		$taskCount=	$this->Task->find('count', array(
			'fields'=>array('Task.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>[
        'TaskType',
        'RequestingUser',
        'ActingUser',
        'ReceivingUser'
			],
			'limit'=>($taskCount!=0?$taskCount:1),
		);

		$tasks = $this->Paginator->paginate('Task');
		$this->set(compact('tasks'));
	}

  public function guardarResumen() {
		$exportData=$_SESSION['resumenTareas'];
		$this->set(compact('exportData'));
	}
  
	public function view($id = null) {
		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
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
		$task = $this->Task->find('first',[
      'conditions' => ['Task.id'  => $id],
      'contain'=>[
        'TaskType',
        'RequestingUser',
        'ActingUser',
        'ReceivingUser'
      ]
    ]);
		$this->set(compact('task'));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Task->create();
			if ($this->Task->save($this->request->data)) {
				$this->Session->setFlash(__('Se guardó la tarea.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
      else {
				$this->Session->setFlash(__('The task could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		$receivingUsers =$actingUsers =$requestingUsers = $this->Task->RequestingUser->find('list',[
      'conditions'=>['bool_active'=>true],
      'order'=>'username'
    ]);
		$taskTypes = $this->Task->TaskType->find('list');
		$this->set(compact('requestingUsers', 'taskTypes', 'actingUsers', 'receivingUsers'));
    
    $loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
	}

	public function edit($id = null) {
		if (!$this->Task->exists($id)) {
			throw new NotFoundException(__('Invalid task'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Task->save($this->request->data)) {
				$this->Session->setFlash(__('Se guardó la tarea.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
      else {
				$this->Session->setFlash(__('No se guardó la tarea.'), 'default',array('class' => 'error-message'));
			}
		} 
    else {
			$options = array('conditions' => array('Task.id' => $id));
			$this->request->data = $this->Task->find('first', $options);
		}
		$receivingUsers =$actingUsers =$requestingUsers = $this->Task->RequestingUser->find('list',[
      'conditions'=>['bool_active'=>true],
      'order'=>'username'
    ]);
		$taskTypes = $this->Task->TaskType->find('list');
		$this->set(compact('requestingUsers', 'taskTypes', 'actingUsers', 'receivingUsers'));
    
    $loggedUserId=$this->Auth->User('id');
		$this->set(compact('loggedUserId'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Task->id = $id;
		if (!$this->Task->exists()) {
			throw new NotFoundException(__('Invalid task'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Task->delete()) {
			$this->Session->setFlash(__('Se eliminó la tarea.'), 'default',array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('No se podía eliminar la tarea.'), 'default',array('class' => 'error-message'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
