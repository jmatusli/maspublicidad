<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
/**
 * Contacts Controller
 *
 * @property InventoryContact $InventoryContact
 * @property PaginatorComponent $Paginator
 */
class InventoryContactsController extends AppController {

	public $components = array('Paginator');
	public $helpers = array('PhpExcel');

	public function index() {
		$this->InventoryContact->recursive = -1;
		
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
		
		$inventoryContactCount=	$this->InventoryContact->find('count', array(
			'fields'=>array('InventoryContact.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(	
				'InventoryClient'
			),
			'limit'=>($inventoryContactCount!=0?$inventoryContactCount:1),
		);

		$inventoryContacts = $this->Paginator->paginate('InventoryContact');
		$this->set(compact('inventoryContacts'));
		
		$aco_name="InventoryClients/index";		
		$bool_inventoryclient_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_index_permission'));
		$aco_name="InventoryClients/add";		
		$bool_inventoryclient_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
		
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->InventoryContact->exists($id)) {
			throw new NotFoundException(__('Invalid contact'));
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
				'InventoryContact.id' => $id,
			),
			'contain'=>array(
				'InventoryClient',
			),
		);
		$this->set('inventoryContact', $this->InventoryContact->find('first', $options));
		
		$aco_name="InventoryClients/index";		
		$bool_inventoryclient_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_index_permission'));
		$aco_name="InventoryClients/add";		
		$bool_inventoryclient_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->InventoryContact->create();
			if ($this->InventoryContact->save($this->request->data)) {
				$this->recordUserAction($this->InventoryContact->id,null,null);
				$this->Session->setFlash(__('The contact has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The contact could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		$inventoryClients = $this->InventoryContact->InventoryClient->find('list',array('order'=>'InventoryClient.name'));
		$this->set(compact('clients'));
		
		$aco_name="InventoryClients/index";		
		$bool_inventoryclient_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_index_permission'));
		$aco_name="InventoryClients/add";		
		$bool_inventoryclient_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->InventoryContact->exists($id)) {
			throw new NotFoundException(__('Invalid contact'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->InventoryContact->save($this->request->data)) {
				$this->recordUserAction($this->InventoryContact->id,null,null);
				$this->Session->setFlash(__('The contact has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The contact could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} else {
			$options = array('conditions' => array('InventoryContact.' . $this->InventoryContact->primaryKey => $id));
			$this->request->data = $this->InventoryContact->find('first', $options);
		}
		$inventoryClients = $this->InventoryContact->InventoryClient->find('list',array('order'=>'InventoryClient.name'));
		$this->set(compact('clients'));
		
		$aco_name="InventoryClients/index";		
		$bool_inventoryclient_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_index_permission'));
		$aco_name="InventoryClients/add";		
		$bool_inventoryclient_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InventoryContact->id = $id;
		if (!$this->InventoryContact->exists()) {
			throw new NotFoundException(__('Invalid contact'));
			$this->Session->setFlash(__('InventoryContacto de inventario inválido. El contaco no se eliminó.'), 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		
		$this->request->allowMethod('post', 'delete');
		
		$this->InventoryContact->recursive=-1;
		$inventoryContact=$this->InventoryContact->find('first',array(
			'conditions'=>array(
				'InventoryContact.id'=>$id,
			),
		));
		
		$flashMessage="";
		$boolDeletionAllowed=true;
		
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó el contacto de inventario.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {	
			$datasource=$this->InventoryContact->getDataSource();
			$datasource->begin();
			try {
				// remove the provider
				if (!$this->InventoryContact->delete($inventoryContact['InventoryContact']['id'])) {
					echo "problema al eliminar el contacto de inventario";
					pr($this->validateErrors($this->InventoryContact));
					throw new Exception();
				}			
				$datasource->commit();
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=array();
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$inventoryContact['InventoryContact']['id'];
				$deletionArray['Deletion']['reference']=$inventoryContact['InventoryContact']['first_name']."".$inventoryContact['InventoryContact']['first_name'];
				$deletionArray['Deletion']['type']='InventoryContact';
				$this->Deletion->save($deletionArray);
				
				$activityText="Contacto de inventario ".$inventoryContact['InventoryContact']['first_name']."".$inventoryContact['InventoryContact']['first_name']." ha estado eliminado";
				$this->recordUserActivity($this->Session->read('User.username'),$activityText);			
				$this->Session->setFlash(__('Se eliminó el contacto.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 		
			catch(Exception $e){
				$datasource->rollback();
				pr($e);					
				$this->Session->setFlash(__('No se podía eliminar el contacto de inventario. Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}
	
	public function savecontact() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$inventoryClientid=trim($_POST['clientid']);
		$inventoryContactid=trim($_POST['contactid']);
		$boolnewcontact=($_POST['boolnewcontact']=="true");
		
		$inventoryContactfirstname=trim($_POST['contactfirstname']);
		$inventoryContactlastname=trim($_POST['contactlastname']);
		$inventoryContactemail=trim($_POST['contactemail']);
		$inventoryContactphone=trim($_POST['contactphone']);
		$inventoryContactcell=trim($_POST['contactcell']);
		$inventoryContactdepartment=trim($_POST['contactdepartment']);
		
		$datasource=$this->InventoryContact->getDataSource();
		$datasource->begin();
		try {
			//pr($this->request->data);
			$inventoryContactArray=array();
			$inventoryContactArray['InventoryContact']['inventory_client_id']=$inventoryClientid;
			$inventoryContactArray['InventoryContact']['first_name']=$inventoryContactfirstname;
			$inventoryContactArray['InventoryContact']['last_name']=$inventoryContactlastname;
			$inventoryContactArray['InventoryContact']['phone']=$inventoryContactphone;
			$inventoryContactArray['InventoryContact']['cell']=$inventoryContactcell;
			$inventoryContactArray['InventoryContact']['email']=$inventoryContactemail;
			$inventoryContactArray['InventoryContact']['department']=$inventoryContactdepartment;
			$inventoryContactArray['InventoryContact']['bool_active']=true;
			if ($boolnewcontact){
				$this->InventoryContact->create();
			}
			else {
				$this->InventoryContact->id=$inventoryContactid;
				//pr($inventoryContactid);
				if (!$this->InventoryContact->exists($inventoryContactid)) {
					throw new Exception(__('InventoryContacto de inventario inválido'));
				}				
			}
			if (!$this->InventoryContact->save($inventoryContactArray)) {
				echo "Problema guardando el contacto";
				pr($this->validateErrors($this->InventoryContact));
				throw new Exception();
			}
			$inventoryContact_id=$this->InventoryContact->id;
			
			$datasource->commit();
			$this->recordUserAction($this->InventoryContact->id,"add",null);
			$this->recordUserActivity($this->Session->read('User.username'),"Se registró el contacto ".$inventoryContactfirstname." ".$inventoryContactlastname);
			
			$this->Session->setFlash(__('The inventory contact has been saved.'),'default',array('class' => 'success'));
			//return $this->redirect(array('action' => 'index'));
			return true;
		} 
		catch(Exception $e){
			$datasource->rollback();
			//pr($e);
			$this->Session->setFlash(__('The inventory contact could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			return false;
		}
	}
	
	public function getcontactlist() {
		$this->layout = "ajax";
		
		$this->InventoryContact->recursive=-1;
		$inventoryContacts=$this->InventoryContact->find('all',array(
			'fields'=>array('InventoryContact.id','InventoryContact.first_name','InventoryContact.last_name'),
		));
		pr($inventoryContacts);
		$this->set(compact('inventoryContacts'));
	}
	
	public function getcontactlistforcontactname() {
		$this->layout = "ajax";
		
		$inventoryClientid=trim($_POST['clientid']);
		$inventoryContactfirstnameval=trim($_POST['contactfirstnameval']);
		$inventoryContactlastnameval=trim($_POST['contactlastnameval']);
		
		$this->InventoryContact->recursive=-1;
		$inventoryContacts=$this->InventoryContact->find('all',array(
			'fields'=>array('InventoryContact.id','InventoryContact.first_name','InventoryContact.last_name'),
			'conditions'=>array(
				'InventoryContact.inventory_client_id'=> $inventoryClientid,
				/*
				'OR'=>array(
					array('InventoryContact.first_name LIKE'=> "%$inventoryContactfirstnameval%"),
					array('InventoryContact.last_name LIKE'=> "%$inventoryContactlastnameval%"),
				),
				*/
				'InventoryContact.first_name LIKE'=> "%$inventoryContactfirstnameval%",
				'InventoryContact.last_name LIKE'=> "%$inventoryContactlastnameval%"
			),
		));
		//pr($inventoryClients);
		$this->set(compact('inventoryContacts'));
	}
	
	public function getcontactcount() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$inventoryClientid=trim($_POST['clientid']);
		$inventoryContactfirstnameval=trim($_POST['contactfirstnameval']);
		$inventoryContactlastnameval=trim($_POST['contactlastnameval']);
		
		$this->InventoryContact->recursive=-1;
		$inventoryContacts=$this->InventoryContact->find('all',array(
			'fields'=>array('InventoryContact.id','InventoryContact.first_name','InventoryContact.last_name'),
			'conditions'=>array(
				'InventoryContact.inventory_client_id'=> $inventoryClientid,
				/*
				'OR'=>array(
					array('InventoryContact.first_name LIKE'=> "%$inventoryContactfirstnameval%"),
					array('InventoryContact.last_name LIKE'=> "%$inventoryContactlastnameval%"),
				),
				*/
				'InventoryContact.first_name LIKE'=> "%$inventoryContactfirstnameval%",
				'InventoryContact.last_name LIKE'=> "%$inventoryContactlastnameval%"
			),
		));
		//pr($inventoryClients);
		echo count($inventoryContacts);
	}
	
	public function getcontactinfo() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$inventoryContactid=trim($_POST['contactid']);
		
		$this->InventoryContact->recursive=-1;
		$inventoryContact=$this->InventoryContact->find('first',array(
			'fields'=>array('InventoryContact.id','InventoryContact.first_name','InventoryContact.last_name','InventoryContact.phone','InventoryContact.cell','InventoryContact.email','InventoryContact.department'),
			'conditions'=>array(
				'InventoryContact.id'=> $inventoryContactid,
			),
		));
		return json_encode($inventoryContact['InventoryContact']);
	}
}
