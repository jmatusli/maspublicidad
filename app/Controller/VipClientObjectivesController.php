<?php
App::uses('AppController', 'Controller');

class VipClientObjectivesController extends AppController {


	public $components = array('Paginator');

	public function index() {
		$this->VipClientObjective->recursive = -1;
		$clientId=0;
		if ($this->request->is('post')) {
			/*
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			*/
			$clientId=$this->request->data['Report']['client_id'];
		}
		/*
		if (!isset($startDate)){
			$startDate = date("Y-m-01");
		}
		if (!isset($endDate)){
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		$this->set(compact('startDate','endDate'));
		*/
		$this->set(compact('clientId'));
		
		$conditions=array();
		if ($clientId>0){
			$conditions[]=array('VipClientObjective.client_id'=>$clientId);
		}
		
		$vipClientObjectiveCount=$this->VipClientObjective->find('count', array(
			'fields'=>array('VipClientObjective.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(
				'Client',
			),
			'limit'=>($vipClientObjectiveCount!=0?$vipClientObjectiveCount:1),
		);

		$vipClientObjectives = $this->Paginator->paginate('VipClientObjective');
		$this->set(compact('vipClientObjectives'));
		
		$this->loadModel('Client');
		$clients=$this->Client->find('list',array(
			'conditions'=>array(
				'bool_vip'=>true,
			),
			'order'=>'Client.name',
		));
		$this->set(compact('clients'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
	}

	public function view($id = null) {
		if (!$this->VipClientObjective->exists($id)) {
			throw new NotFoundException(__('Invalid vip client objective'));
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
		
		$options = array('conditions' => array('VipClientObjective.id' => $id));
		$this->set('vipClientObjective', $this->VipClientObjective->find('first', $options));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->VipClientObjective->create();
			if ($this->VipClientObjective->save($this->request->data)) {
				$this->Session->setFlash(__('The vip client objective has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The vip client objective could not be saved. Please, try again.'),'default',array('class' => 'error-message'));
			}
		}
		$this->loadModel('Client');
		$clients=$this->Client->find('list',array(
			'conditions'=>array(
				'bool_vip'=>true,
			),
			'order'=>'Client.name',
		));
		$this->set(compact('clients'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->VipClientObjective->exists($id)) {
			throw new NotFoundException(__('Invalid vip client objective'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->VipClientObjective->save($this->request->data)) {
				$this->Session->setFlash(__('The vip client objective has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The vip client objective could not be saved. Please, try again.'),'default',array('class' => 'error-message'));
			}
		} 
		else {
			$options = array('conditions' => array('VipClientObjective.id' => $id));
			$this->request->data = $this->VipClientObjective->find('first', $options);
		}
		
		$this->loadModel('Client');
		$clients=$this->Client->find('list',array(
			'conditions'=>array(
				'bool_vip'=>true,
			),
			'order'=>'Client.name',
		));
		$this->set(compact('clients'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->VipClientObjective->id = $id;
		if (!$this->VipClientObjective->exists()) {
			throw new NotFoundException(__('Invalid vip client objective'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$vipClientObjective=$this->VipClientObjective->find('first',array(
			'conditions'=>array(
				'VipClientObjective.id'=>$id,
			),
			'contain'=>array(
				'Client',
				//'ProductionProcessRemark',
				
			)
		));
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		/*
		if (count($vipClientObjective['VipClientObjective'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Esta orden de compra tiene procesos de producción correspondientes.  Para poder eliminar la orden de compra, primero hay que eliminar o modificar l0s procesos de producción ";
			if (count($vipClientObjective['VipClientObjective'])==1){
				$flashMessage.=$vipClientObjective['VipClientObjective'][0]['production_process_code'].".";
			}
			else {
				for ($i=0;$i<count($vipClientObjective['VipClientObjective']);$i++){
					$flashMessage.=$vipClientObjective['VipClientObjective'][$i]['production_process_code'];
					if ($i==count($vipClientObjective['VipClientObjective'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		*/
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó el objectivo para el cliente VIP.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {
			$datasource=$this->VipClientObjective->getDataSource();
			$datasource->begin();	
			try {
				//delete all products, remarks and other costs
				/*foreach ($vipClientObjective['VipClientObjectiveProduct'] as $vipClientObjectiveProduct){
					if (!$this->VipClientObjective->VipClientObjectiveProduct->delete($vipClientObjectiveProduct['id'])) {
						echo "Problema al eliminar el producto del objectivo para el cliente VIP";
						pr($this->validateErrors($this->VipClientObjective->VipClientObjectiveProduct));
						throw new Exception();
					}
				}
				*/
				
				if (!$this->VipClientObjective->delete($id)) {
					echo "Problema al eliminar el objectivo para el cliente VIP";
					pr($this->validateErrors($this->VipClientObjective));
					throw new Exception();
				}
						
				$datasource->commit();
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$vipClientObjective['VipClientObjective']['id'];
				$deletionArray['Deletion']['reference']=$vipClientObjective['Client']['name']." ".$vipClientObjective['VipClientObjective']['objective_date'];
				$deletionArray['Deletion']['type']='VipClientObjective';
				$this->Deletion->save($deletionArray);
						
				$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó el objectivo para el cliente VIP ".$vipClientObjective['VipClientObjective']['production_process_code']);
						
				$this->Session->setFlash(__('Se eliminó el objectivo para el cliente VIP.'),'default',array('class' => 'success'));				
				return $this->redirect(array('action' => 'index'));
			}
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('No se podía eliminar el objectivo para el cliente VIP.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}
}
