<?php
App::uses('AppController', 'Controller');
/**
 * MeasuringUnits Controller
 *
 * @property MeasuringUnit $MeasuringUnit
 * @property PaginatorComponent $Paginator
 */
class MeasuringUnitsController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->MeasuringUnit->recursive = -1;
		
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
		
		$measuringUnitCount=	$this->MeasuringUnit->find('count', array(
			'fields'=>array('MeasuringUnit.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($measuringUnitCount!=0?$measuringUnitCount:1),
		);

		$measuringUnits = $this->Paginator->paginate('MeasuringUnit');
		$this->set(compact('measuringUnits'));
	}

	public function view($id = null) {
		if (!$this->MeasuringUnit->exists($id)) {
			throw new NotFoundException(__('Invalid measuring unit'));
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
		$options = array('conditions' => array('MeasuringUnit.' . $this->MeasuringUnit->primaryKey => $id));
		$this->set('measuringUnit', $this->MeasuringUnit->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->MeasuringUnit->create();
			if ($this->MeasuringUnit->save($this->request->data)) {
				$this->Session->setFlash(__('The measuring unit has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The measuring unit could not be saved. Please, try again.'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->MeasuringUnit->exists($id)) {
			throw new NotFoundException(__('Invalid measuring unit'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->MeasuringUnit->save($this->request->data)) {
				$this->Session->setFlash(__('The measuring unit has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The measuring unit could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('MeasuringUnit.' . $this->MeasuringUnit->primaryKey => $id));
			$this->request->data = $this->MeasuringUnit->find('first', $options);
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
		$this->MeasuringUnit->id = $id;
		if (!$this->MeasuringUnit->exists()) {
			throw new NotFoundException(__('Invalid measuring unit'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$measuringUnit=$this->MeasuringUnit->find('first',array(
			'conditions'=>array(
				'MeasuringUnit.id'=>$id,
			),
		));
		
		if ($this->MeasuringUnit->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$measuringUnit['MeasuringUnit']['id'];
			$deletionArray['Deletion']['reference']=$measuringUnit['MeasuringUnit']['name'];
			$deletionArray['Deletion']['type']='MeasuringUnit';
			$this->Deletion->save($deletionArray);
		
			$this->Session->setFlash(__('The measuring unit has been deleted.'));
		} 
		else {
			$this->Session->setFlash(__('The measuring unit could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
