<?php
App::uses('AppController', 'Controller');
/**
 * ActionTypes Controller
 *
 * @property ActionType $actionType
 * @property PaginatorComponent $Paginator
 */
class ActionTypesController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->ActionType->recursive = -1;
		
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
		
		$actionTypeCount=	$this->ActionType->find('count', array(
			'fields'=>array('ActionType.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($actionTypeCount!=0?$actionTypeCount:1),
		);

		$actionTypes = $this->Paginator->paginate('ActionType');
		$this->set(compact('actionTypes'));
	}

	public function view($id = null) {
		if (!$this->ActionType->exists($id)) {
			throw new NotFoundException(__('Invalid action type'));
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
		$options = array('conditions' => array('ActionType.' . $this->ActionType->primaryKey => $id));
		$this->set('actionType', $this->ActionType->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->ActionType->create();
			if ($this->ActionType->save($this->request->data)) {
				$this->Session->setFlash(__('The action type has been saved.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The action type could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->ActionType->exists($id)) {
			throw new NotFoundException(__('Invalid action type'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ActionType->save($this->request->data)) {
				$this->Session->setFlash(__('The action type has been saved.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The action type could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} 
		else {
			$options = array('conditions' => array('ActionType.' . $this->ActionType->primaryKey => $id));
			$this->request->data = $this->ActionType->find('first', $options);
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
		$this->ActionType->id = $id;
		if (!$this->ActionType->exists()) {
			throw new NotFoundException(__('Invalid action type'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$actionType=$this->ActionType->find('first',array(
			'conditions'=>array(
				'ActionType.id'=>$id,
			),
			'contain'=>array(
				'QuotationRemark',
			),
		));
		
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (count($actionType['QuotationRemark'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Este tipo de seguimiento tiene remarcas de cotizaciones correspondientes.  Para poder eliminar el tipo de seguimiento, primero hay que eliminar o modificar las remarcas ";
			if (count($actionType['QuotationRemark'])==1){
				$flashMessage.=$actionType['QuotationRemark'][0]['remark_text'].".";
			}
			else {
				for ($i=0;$i<count($actionType['QuotationRemark']);$i++){
					$flashMessage.=$actionType['QuotationRemark'][$i]['remark_text'];
					if ($i==count($actionType['QuotationRemark'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó el tipo de seguimiento.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			if ($this->ActionType->delete()) {
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$actionType['ActionType']['id'];
				$deletionArray['Deletion']['reference']=$actionType['ActionType']['name'];
				$deletionArray['Deletion']['type']='ActionType';
				$this->Deletion->save($deletionArray);
				
				$this->Session->setFlash(__('The action type has been deleted.'),array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The action type could not be deleted. Please, try again.'),array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}
}
