<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class InventoryClientsController extends AppController {


	public $components = array('Paginator');
	public $helpers = array('PhpExcel');

	public function index() {
		$this->InventoryClient->recursive = -1;
		
		$inventoryInventoryClientCount=	$this->InventoryClient->find('count', array(
			'fields'=>array('InventoryClient.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'order'=>'InventoryClient.name',
			'limit'=>($inventoryInventoryClientCount!=0?$inventoryInventoryClientCount:1),
		);

		$inventoryClients = $this->Paginator->paginate('InventoryClient');
		$this->set(compact('inventoryClients'));
		
		$aco_name="InventoryContacts/index";		
		$bool_inventorycontact_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventorycontact_index_permission'));
		$aco_name="InventoryContacts/add";		
		$bool_inventorycontact_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventorycontact_add_permission'));
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->InventoryClient->exists($id)) {
			throw new NotFoundException(__('Invalid client'));
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
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$this->set(compact('startDate','endDate'));
		
		$options = array(
			'conditions' => array(
				'InventoryClient.id' => $id
			),
			'contain'=>array(
				'InventoryContact',
				
			),
		);
		$this->set('inventoryClient', $this->InventoryClient->find('first', $options));
		
		$aco_name="InventoryContacts/index";		
		$bool_inventorycontact_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventorycontact_index_permission'));
		$aco_name="InventoryContacts/add";		
		$bool_inventorycontact_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventorycontact_add_permission'));
	}

	public function add() {
		
		if ($this->request->is('post')) {
			$this->loadModel('InventoryContact');
			$datasource=$this->InventoryClient->getDataSource();
			try {
				//pr($this->request->data);
				$datasource->begin();
				
				$this->InventoryClient->create();
				if (!$this->InventoryClient->save($this->request->data)) {
					echo "Problema guardando el cliente";
					pr($this->validateErrors($this->InventoryClient));
					throw new Exception();
				}
				$inventoryClient_id=$this->InventoryClient->id;
				
				foreach ($this->request->data['InventoryContact'] as $inventoryContact){
					if (!empty($inventoryContact['first_name'])&&!empty($inventoryContact['last_name'])){
						
						$inventoryContactArray=array();
						$inventoryContactArray['InventoryContact']['first_name']=$inventoryContact['first_name'];
						$inventoryContactArray['InventoryContact']['last_name']=$inventoryContact['last_name'];
						$inventoryContactArray['InventoryContact']['phone']=$inventoryContact['phone'];
						$inventoryContactArray['InventoryContact']['cell']=$inventoryContact['cell'];
						$inventoryContactArray['InventoryContact']['email']=$inventoryContact['email'];
						$inventoryContactArray['InventoryContact']['department']=$inventoryContact['department'];
						$inventoryContactArray['InventoryContact']['bool_active']=true;
						$inventoryContactArray['InventoryContact']['inventory_client_id']=$inventoryClient_id;
						$this->InventoryContact->create();
						if (!$this->InventoryContact->save($inventoryContactArray)) {
							echo "Problema guardando los contactos del cliente";
							pr($this->validateErrors($this->InventoryContact));
							throw new Exception();
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->InventoryClient->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se registró el cliente ".$this->request->data['InventoryClient']['name']);
				
				$this->Session->setFlash(__('The client has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The client could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		
		$aco_name="InventoryContacts/index";		
		$bool_inventorycontact_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventorycontact_index_permission'));
		$aco_name="InventoryContacts/add";		
		$bool_inventorycontact_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventorycontact_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->InventoryClient->exists($id)) {
			throw new NotFoundException(__('Invalid client'));
		}
		$this->InventoryClient->InventoryContact->recursive=-1;
		$existingInventoryContacts=$this->InventoryClient->InventoryContact->find('all',array(
			'conditions'=>array(
				'InventoryContact.inventory_client_id'=>$id,
			),
		));
		$this->set(compact('existingInventoryContacts'));
		if ($this->request->is(array('post', 'put'))) {
			$this->loadModel('InventoryContact');
			$datasource=$this->InventoryClient->getDataSource();
			try {
				//pr($this->request->data);
				$datasource->begin();
				
				$this->InventoryClient->id=$id;
				if (!$this->InventoryClient->save($this->request->data)) {
					echo "Problema guardando el cliente";
					pr($this->validateErrors($this->InventoryClient));
					throw new Exception();
				}
				$inventoryClient_id=$this->InventoryClient->id;
				$i=0;
				foreach ($this->request->data['InventoryContact'] as $inventoryContact){
					if ($i<count($existingInventoryContacts)){
						if (!empty($inventoryContact['first_name'])&&!empty($inventoryContact['last_name'])){
							
							$inventoryContactArray=array();
							$inventoryContactArray['InventoryContact']['id']=$existingInventoryContacts[$i]['InventoryContact']['id'];
							$inventoryContactArray['InventoryContact']['first_name']=$inventoryContact['first_name'];
							$inventoryContactArray['InventoryContact']['last_name']=$inventoryContact['last_name'];
							$inventoryContactArray['InventoryContact']['phone']=$inventoryContact['phone'];
							$inventoryContactArray['InventoryContact']['cell']=$inventoryContact['cell'];
							$inventoryContactArray['InventoryContact']['email']=$inventoryContact['email'];
							$inventoryContactArray['InventoryContact']['department']=$inventoryContact['department'];
							$inventoryContactArray['InventoryContact']['bool_active']=true;
							$inventoryContactArray['InventoryContact']['inventory_client_id']=$inventoryClient_id;
							if (!$this->InventoryContact->save($inventoryContactArray)) {
								echo "Problema guardando los contactos del cliente";
								pr($this->validateErrors($this->InventoryContact));
								throw new Exception();
							}
						}
					}
					else { 
						if (!empty($inventoryContact['first_name'])&&!empty($inventoryContact['last_name'])){
							$inventoryContactArray=array();
							$inventoryContactArray['InventoryContact']['first_name']=$inventoryContact['first_name'];
							$inventoryContactArray['InventoryContact']['last_name']=$inventoryContact['last_name'];
							$inventoryContactArray['InventoryContact']['phone']=$inventoryContact['phone'];
							$inventoryContactArray['InventoryContact']['cell']=$inventoryContact['cell'];
							$inventoryContactArray['InventoryContact']['email']=$inventoryContact['email'];
							$inventoryContactArray['InventoryContact']['department']=$inventoryContact['department'];
							$inventoryContactArray['InventoryContact']['bool_active']=true;
							$inventoryContactArray['InventoryContact']['inventory_client_id']=$inventoryClient_id;
							$this->InventoryContact->create();
							if (!$this->InventoryContact->save($inventoryContactArray)) {
								echo "Problema guardando los contactos del cliente";
								pr($this->validateErrors($this->InventoryContact));
								throw new Exception();
							}
						}
					}
					$i++;
				}
				$datasource->commit();
				$this->recordUserAction($this->InventoryClient->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se registró el cliente ".$this->request->data['InventoryClient']['name']);
				
				$this->Session->setFlash(__('The client has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The client could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} 
		else {
			$options = array('conditions' => array('InventoryClient.' . $this->InventoryClient->primaryKey => $id));
			$this->request->data = $this->InventoryClient->find('first', $options);
		}
		
		$aco_name="InventoryContacts/index";		
		$bool_inventorycontact_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventorycontact_index_permission'));
		$aco_name="InventoryContacts/add";		
		$bool_inventorycontact_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventorycontact_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InventoryClient->id = $id;
		if (!$this->InventoryClient->exists()) {
			throw new NotFoundException(__('Invalid client'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$inventoryClient=$this->InventoryClient->find('first',array(
			'conditions'=>array(
				'InventoryClient.id'=>$id,
			),
		));
		
		if ($this->InventoryClient->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$inventoryClient['InventoryClient']['id'];
			$deletionArray['Deletion']['reference']=$inventoryClient['InventoryClient']['name'];
			$deletionArray['Deletion']['type']='InventoryClient';
			$this->Deletion->save($deletionArray);
			
			$this->Session->setFlash(__('The client has been deleted.'));
		} 
		else {
			$this->Session->setFlash(__('The client could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function saveclient() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$inventoryClientid=trim($_POST['clientid']);
		$boolnewclient=($_POST['boolnewclient']=="true");
		
		$inventoryClientname=trim($_POST['clientname']);
		$inventoryClientruc=trim($_POST['clientruc']);
		$inventoryClientaddress=trim($_POST['clientaddress']);
		$inventoryClientphone=trim($_POST['clientphone']);
		$inventoryClientcell=trim($_POST['clientcell']);
		
		$datasource=$this->InventoryClient->getDataSource();
		try {
			//pr($this->request->data);
			$datasource->begin();
			
			$inventoryClientArray=array();
			$inventoryClientArray['InventoryClient']['name']=$inventoryClientname;
			$inventoryClientArray['InventoryClient']['ruc']=$inventoryClientruc;
			$inventoryClientArray['InventoryClient']['address']=$inventoryClientaddress;
			$inventoryClientArray['InventoryClient']['phone']=$inventoryClientphone;
			$inventoryClientArray['InventoryClient']['cell']=$inventoryClientcell;
			$inventoryClientArray['InventoryClient']['bool_active']=true;
			if ($boolnewclient){
				$this->InventoryClient->create();
			}
			else {
				$this->InventoryClient->id=$inventoryClientid;
				//pr($inventoryClientid);
				if (!$this->InventoryClient->exists($inventoryClientid)) {
					throw new Exception(__('InventoryCliente inválido'));
				}				
			}
			if (!$this->InventoryClient->save($inventoryClientArray)) {
				echo "Problema guardando el cliente";
				pr($this->validateErrors($this->InventoryClient));
				throw new Exception();
			}
			$inventoryClient_id=$this->InventoryClient->id;
			
			$datasource->commit();
			$this->recordUserAction($this->InventoryClient->id,"add",null);
			$this->recordUserActivity($this->Session->read('User.username'),"Se registró el cliente ".$inventoryClientname);
			
			$this->Session->setFlash(__('The client has been saved.'),'default',array('class' => 'success'));
			//return $this->redirect(array('action' => 'index'));
			return true;
		} 
		catch(Exception $e){
			$datasource->rollback();
			//pr($e);
			$this->Session->setFlash(__('The client could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			return false;
		}
	}
	
	public function getclientlist() {
		$this->layout = "ajax";
		
		$this->InventoryClient->recursive=-1;
		$inventoryClients=$this->InventoryClient->find('all',array(
			'fields'=>array('InventoryClient.id','InventoryClient.name'),
		));
		pr($inventoryClients);
		$this->set(compact('clients'));
	}
	
	public function getclientlistforclientname() {
		$this->layout = "ajax";
		
		$inventoryClientval=trim($_POST['clientval']);
		
		$this->InventoryClient->recursive=-1;
		$inventoryClients=$this->InventoryClient->find('all',array(
			'fields'=>array('InventoryClient.id','InventoryClient.name'),
			'conditions'=>array(
				'InventoryClient.name LIKE'=> "%$inventoryClientval%",
			),
		));
		//pr($inventoryClients);
		$this->set(compact('clients'));
	}
	
	public function getclientinfo() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$inventoryClientid=trim($_POST['clientid']);
		
		$this->InventoryClient->recursive=-1;
		$inventoryClient=$this->InventoryClient->find('first',array(
			'fields'=>array('InventoryClient.id','InventoryClient.name','InventoryClient.ruc','InventoryClient.address','InventoryClient.phone','InventoryClient.cell'),
			'conditions'=>array(
				'InventoryClient.id'=> $inventoryClientid,
			),
		));
		return json_encode($inventoryClient['InventoryClient']);
	}
}
