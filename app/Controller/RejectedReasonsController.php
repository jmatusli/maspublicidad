<?php
App::uses('AppController', 'Controller');
/**
 * RejectedReasons Controller
 *
 * @property RejectedReason $rejectedReason
 * @property PaginatorComponent $Paginator
 */
class RejectedReasonsController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->RejectedReason->recursive = -1;
		
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
		
		$rejectedReasonCount=	$this->RejectedReason->find('count', array(
			'fields'=>array('RejectedReason.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($rejectedReasonCount!=0?$rejectedReasonCount:1),
		);

		$rejectedReasons = $this->Paginator->paginate('RejectedReason');
		$this->set(compact('rejectedReasons'));
	}

	public function view($id = null) {
		if (!$this->RejectedReason->exists($id)) {
			throw new NotFoundException(__('Invalid rejected reason'));
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
		$options = array('conditions' => array('RejectedReason.' . $this->RejectedReason->primaryKey => $id));
		$this->set('rejectedReason', $this->RejectedReason->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->RejectedReason->create();
			if ($this->RejectedReason->save($this->request->data)) {
				$this->Session->setFlash(__('The rejected reason has been saved.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The rejected reason could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->RejectedReason->exists($id)) {
			throw new NotFoundException(__('Invalid rejected reason'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->RejectedReason->save($this->request->data)) {
				$this->Session->setFlash(__('The rejected reason has been saved.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The rejected reason could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} 
		else {
			$options = array('conditions' => array('RejectedReason.' . $this->RejectedReason->primaryKey => $id));
			$this->request->data = $this->RejectedReason->find('first', $options);
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
		$this->RejectedReason->id = $id;
		if (!$this->RejectedReason->exists()) {
			throw new NotFoundException(__('Invalid rejected reason'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$rejectedReason=$this->RejectedReason->find('first',array(
			'conditions'=>array(
				'RejectedReason.id'=>$id,
			),
			'contain'=>array(
				'Quotation',
			),
		));
		
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (count($rejectedReason['Quotation'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Esta razón de cáida tiene cotizaciones correspondientes.  Para poder eliminar la razón de caída, primero hay que eliminar o modificar las cotizacioens ";
			if (count($rejectedReason['Quotation'])==1){
				$flashMessage.=$rejectedReason['Quotation'][0]['quotation_code'].".";
			}
			else {
				for ($i=0;$i<count($rejectedReason['Quotation']);$i++){
					$flashMessage.=$rejectedReason['Quotation'][$i]['quotation_code'];
					if ($i==count($rejectedReason['Quotation'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó la razón de caída.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			if ($this->RejectedReason->delete()) {
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$rejectedReason['RejectedReason']['id'];
				$deletionArray['Deletion']['reference']=$rejectedReason['RejectedReason']['name'];
				$deletionArray['Deletion']['type']='RejectedReason';
				$this->Deletion->save($deletionArray);
				
			
				$this->Session->setFlash(__('The rejected reason has been deleted.'),array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The rejected reason could not be deleted. Please, try again.'),array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}
}
