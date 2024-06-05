<?php
App::uses('AppController', 'Controller');
/**
 * OperationLocations Controller
 *
 * @property OperationLocation $OperationLocation
 * @property PaginatorComponent $Paginator
 */
class OperationLocationsController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->OperationLocation->recursive = -1;
		
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
		
		$operationLocationCount=	$this->OperationLocation->find('count', array(
			'fields'=>array('OperationLocation.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($operationLocationCount!=0?$operationLocationCount:1),
		);

		$operationLocations = $this->Paginator->paginate('OperationLocation');
		$this->set(compact('operationLocations'));
		//pr($operationLocations);
	}

	public function view($id = null) {
		if (!$this->OperationLocation->exists($id)) {
			throw new NotFoundException(__('Invalid operation location'));
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
		$options = array('conditions' => array('OperationLocation.' . $this->OperationLocation->primaryKey => $id));
		$this->set('operationLocation', $this->OperationLocation->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->OperationLocation->create();
			if ($this->OperationLocation->save($this->request->data)) {
				$this->Session->setFlash(__('The operation location has been saved.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The operation location could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->OperationLocation->exists($id)) {
			throw new NotFoundException(__('Invalid operation location'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->OperationLocation->save($this->request->data)) {
				$this->Session->setFlash(__('The operation location has been saved.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The operation location could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} else {
			$options = array('conditions' => array('OperationLocation.' . $this->OperationLocation->primaryKey => $id));
			$this->request->data = $this->OperationLocation->find('first', $options);
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
		$this->OperationLocation->id = $id;
		if (!$this->OperationLocation->exists()) {
			throw new NotFoundException(__('Invalid operation location'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$operationLocation=$this->OperationLocation->find('first',array(
			'conditions'=>array(
				'OperationLocation.id'=>$id,
			),
		));
		
		if ($this->OperationLocation->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$operationLocation['OperationLocation']['id'];
			$deletionArray['Deletion']['reference']=$operationLocation['OperationLocation']['name'];
			$deletionArray['Deletion']['type']='OperationLocation';
			$this->Deletion->save($deletionArray);
		
			$this->Session->setFlash(__('The operation location has been deleted.'));
		} 
		else {
			$this->Session->setFlash(__('The operation location could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
