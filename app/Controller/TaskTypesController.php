<?php
App::uses('AppController', 'Controller');

class TaskTypesController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->TaskType->recursive = -1;
				
		$taskTypeCount=	$this->TaskType->find('count', array(
			'fields'=>array('TaskType.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($taskTypeCount!=0?$taskTypeCount:1),
		);

		$taskTypes = $this->Paginator->paginate('TaskType');
		$this->set(compact('taskTypes'));
	}

	public function view($id = null) {
		if (!$this->TaskType->exists($id)) {
			throw new NotFoundException(__('Invalid task type'));
		}
		$options = array('conditions' => array('TaskType.id' => $id));
		$this->set('taskType', $this->TaskType->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->TaskType->create();
			if ($this->TaskType->save($this->request->data)) {
				$this->Session->setFlash(__('Se guardó el tipo de tarea.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('No se podía guardar el tipo de tarea.'), 'default',array('class' => 'error-message'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->TaskType->exists($id)) {
			throw new NotFoundException(__('Invalid task type'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->TaskType->save($this->request->data)) {
				$this->Session->setFlash(__('Se guardó el tipo de tarea.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
      else {
				$this->Session->setFlash(__('No se podía guardar el tipo de tarea.'), 'default',array('class' => 'error-message'));
			}
		} 
    else {
			$options = array('conditions' => array('TaskType.id' => $id));
			$this->request->data = $this->TaskType->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->TaskType->id = $id;
		if (!$this->TaskType->exists()) {
			throw new NotFoundException(__('Invalid task type'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->TaskType->delete()) {
			$this->Session->setFlash(__('Se eliminó el tipo de tarea.'), 'default',array('class' => 'success'));
		} else {
			$this->Session->setFlash(__('No se podía eliminar el tipo de tarea.'), 'default',array('class' => 'error-message'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
