<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
/**
 * Contacts Controller
 *
 * @property Contact $Contact
 * @property PaginatorComponent $Paginator
 */
class ContactsController extends AppController {

	public $components = array('Paginator');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('savecontact','getcontactlist','getcontactlistforcontactname','getcontactcount','getcontactinfo');		
	}
	
	public function index() {
		$this->Contact->recursive = -1;
    
    $this->loadModel('ClientUser');

		$this->loadModel('User');
    $this->loadModel('UserPageRight');
		
		$loggedUserId=$this->Auth->User('id');
    $userRoleId = $this->Auth->User('role_id');
    $this->set(compact('loggedUserId','userRoleId'));
    //echo 'loggedUserId is '.$loggedUserId.'<br/>';
    //echo 'userRoleId is '.$userRoleId.'<br/>';
    
    $canSeeAllUsers=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_USUARIOS',$userRoleId,$loggedUserId,'Quotations','verReporteGestionDeVentas');
    $this->set(compact('canSeeAllUsers'));
    
    $canSeeAllSalesExecutives=$this->UserPageRight->hasUserPageRight('VER_LISTA_TODOS_VENDEDORES',$userRoleId,$loggedUserId,'Quotations','verReporteGestionDeVentas');
    $this->set(compact('canSeeAllSalesExecutives'));
    //echo 'can see all sales executives:'.$canSeeAllSalesExecutives.'<br/>';
	
    if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers || $canSeeAllSalesExecutives){
			$userId=0;
		}
    else {
      $userId=$loggedUserId;
    }
		
		$activeDisplayOptions=[
			'0'=>'Mostrar solamente clientes activos',
			'1'=>'Mostrar clientes activos y no activos',
			'2'=>'Mostrar clientes desactivados',
		];
		$this->set(compact('activeDisplayOptions'));
		
		define('SHOW_CLIENT_ACTIVE_YES','0');
		define('SHOW_CLIENT_ACTIVE_ALL','1');
		define('SHOW_CLIENT_ACTIVE_NO','2');
		
		$activeDisplayOptionId=SHOW_CLIENT_ACTIVE_YES;
		
		if ($this->request->is('post')) {
			$userId=$this->request->data['Report']['user_id'];
			$activeDisplayOptionId=$this->request->data['Report']['active_display_option_id'];
		}		
		
		$this->set(compact('activeDisplayOptionId'));
    $this->set(compact('userId'));
		
    $clientConditions=[];
    $clientUserConditions=[];
    
    if ($userId > 0) { 
      // in this case the user_id is set to the logged in user explicitly
      // the clients are limited to those that have at least at one time been associated with the user
    	$clientUserIds=$this->ClientUser->find('list',[
				'fields'=>['ClientUser.client_id'],
				'conditions'=>['ClientUser.user_id'=>$userId,],
			]);
		
			$clientConditions['Client.id']=$clientUserIds;
      $clientUserConditions['ClientUser.user_id']=$userId;
		}
    elseif ($userRoleId != ROLE_ADMIN && !$canSeeAllUsers && $canSeeAllSalesExecutives) {
      $vendorUserIds=array_keys($users=$this->User->getActiveSalesUsersOnlyList());
      
      $clientUserIds=$this->ClientUser->find('list',[
				'fields'=>['ClientUser.client_id'],
				'conditions'=>['ClientUser.user_id'=>$vendorUserIds,],
			]);
		
			//$clientConditions['Client.id']=$vendorUserIds;
      //$clientUserConditions['ClientUser.user_id']=$vendorUserIds;
    }
		
    
		if ($activeDisplayOptionId!=SHOW_CLIENT_ACTIVE_ALL){
			if ($activeDisplayOptionId==SHOW_CLIENT_ACTIVE_YES){
				$clientConditions['Client.bool_active']=true;
			}
			else {
				$clientConditions['Client.bool_active']=false;
			}
		}
		//pr($clientConditions);
		$clientCount=	$this->Contact->Client->find('count', [
			'fields'=>['Client.id'],
			'conditions' => $clientConditions,
		]);
		
		$allClients=$this->Contact->Client->find('all',[
			'conditions' => $clientConditions,
			'contain'=>[	
				'ClientUser'=>[
          'conditions' => $clientUserConditions,  
					'User',
					'order'=>'ClientUser.assignment_datetime DESC,ClientUser.id DESC',
          'limit'=>1,
				],
			],
			'order'=>'Client.name ASC',
			'limit'=>($clientCount!=0?$clientCount:1),
		]);
    //pr($allClients);
    $clientIds=[];	
    for ($c=0;$c<count($allClients);$c++){
			if (empty($userId)||$allClients[$c]['ClientUser'][0]['bool_assigned']){
        $thisClient=$allClients[$c];
        $clientId=$thisClient['Client']['id'];
        $clientIds[]=$clientId;        
      }
		}
    //pr($clientIds);
    $contactConditions=[
      'Contact.client_id'=>$clientIds,
    ];
		$contactCount=	$this->Contact->find('count', [
			'fields'=>['Contact.id'],
			'conditions' => $contactConditions,
		]);
		
		$this->Paginator->settings = [
			'conditions' => $contactConditions,
			'contain'=>[
				'Client'
			],
			'limit'=>($contactCount!=0?$contactCount:1),
		];
    
    if ($userRoleId == ROLE_ADMIN || $canSeeAllUsers){
      $users=$this->User->getActiveVendorAllUserList();
    }
    elseif ($canSeeAllSalesExecutives) {
      $users=$this->User->getActiveSalesUsersOnlyList();
    }
    else {
      $users=$this->User->getUserList($loggedUserId);
    }
		$this->set(compact('users'));  

		$contacts = $this->Paginator->paginate('Contact');
		usort($contacts,array($this,'sortByClientName'));
		$this->set(compact('contacts'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
		
	}
	
	public function sortByClientName($firstTerm,$secondTerm){
		return ($firstTerm['Client']['name'] < $secondTerm['Client']['name']) ? -1 : 1;
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->Contact->exists($id)) {
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
				'Contact.id' => $id,
			),
			'contain'=>array(
				'Client',
				'Quotation'=>array(
					'Client',
					'User',
				),
			),
		);
		$this->set('contact', $this->Contact->find('first', $options));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Contact->create();
			if ($this->Contact->save($this->request->data)) {
				$this->recordUserAction($this->Contact->id,null,null);
				$this->Session->setFlash(__('The contact has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			}
			else {
				$this->Session->setFlash(__('The contact could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		$clients = $this->Contact->Client->find('list',array('order'=>'Client.name'));
		$this->set(compact('clients'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
		$aco_name="Quotations/index";		
		$bool_quotation_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_index_permission'));
		$aco_name="Quotations/add";		
		$bool_quotation_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_quotation_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->Contact->exists($id)) {
			throw new NotFoundException(__('Invalid contact'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Contact->save($this->request->data)) {
				$this->recordUserAction($this->Contact->id,null,null);
				$this->Session->setFlash(__('The contact has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			}
			else {
				$this->Session->setFlash(__('The contact could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		else {
			$options = array('conditions' => array('Contact.' . $this->Contact->primaryKey => $id));
			$this->request->data = $this->Contact->find('first', $options);
		}
		$clients = $this->Contact->Client->find('list',array('order'=>'Client.name'));
		$this->set(compact('clients'));
		
		$aco_name="Clients/index";		
		$bool_client_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_index_permission'));
		$aco_name="Clients/add";		
		$bool_client_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_client_add_permission'));
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
		$this->Contact->id = $id;
		if (!$this->Contact->exists()) {
			throw new NotFoundException(__('Invalid contact'));
			$this->Session->setFlash(__('Contacto inválido. El contaco no se eliminó.'), 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'index'));
		}
		
		$this->request->allowMethod('post', 'delete');
		
		$this->Contact->recursive=-1;
		$contact=$this->Contact->find('first',array(
			'conditions'=>array(
				'Contact.id'=>$id,
			),
			'contain'=>array(
				'Quotation',
			),
		));
		
		$flashMessage="";
		$boolDeletionAllowed=true;
		if (count($contact['Quotation'])>0){
			$boolDeletionAllowed=false;
			$flashMessage.="Este contacto aparece en cotizaciones.  Para poder eliminar el contacto, primero hay que eliminar o modificar las cotizaciones ";
			if (count($contact['Quotation'])==1){
				$flashMessage.=$contact['Quotation'][0]['quotation_code'].".";
			}
			else {
				for ($i=0;$i<count($contact['Quotation']);$i++){
					$flashMessage.=$contact['Quotation'][$i]['quotation_code'];
					if ($i==count($contact['Quotation'])-1){
						$flashMessage.=".";
					}
					else {
						$flashMessage.=" y ";
					}
				}
			}
		}
		
		if (!$boolDeletionAllowed){
			$flashMessage.=" No se eliminó el contacto.";
			$this->Session->setFlash($flashMessage, 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		else {	
			$datasource=$this->Contact->getDataSource();
			$datasource->begin();
			try {
				// remove the provider
				if (!$this->Contact->delete($contact['Contact']['id'])) {
					echo "problema al eliminar el contacto";
					pr($this->validateErrors($this->Contact));
					throw new Exception();
				}			
				$datasource->commit();
				
				$this->loadModel('Deletion');
				$this->Deletion->create();
				$deletionArray=[];
				$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
				$deletionArray['Deletion']['reference_id']=$contact['Contact']['id'];
				$deletionArray['Deletion']['reference']=$contact['Contact']['first_name']." ".$contact['Contact']['last_name'];
				$deletionArray['Deletion']['type']='Contact';
				$this->Deletion->save($deletionArray);
				
				$activityText="Contacto ".$contact['Contact']['first_name']."".$contact['Contact']['first_name']." ha estado eliminado";
				$this->recordUserActivity($this->Session->read('User.username'),$activityText);			
				$this->Session->setFlash(__('Se eliminó el contacto.'), 'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 		
			catch(Exception $e){
				$datasource->rollback();
				pr($e);					
				$this->Session->setFlash(__('No se podía eliminar el contacto. Por favor intente de nuevo.'), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'view',$id));
			}
		}
	}
	
	public function savecontact() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$clientid=trim($_POST['clientid']);
		$contactid=trim($_POST['contactid']);
		$boolnewcontact=($_POST['boolnewcontact']=="true");
		
		$contactfirstname=trim($_POST['contactfirstname']);
		$contactlastname=trim($_POST['contactlastname']);
		$contactemail=trim($_POST['contactemail']);
		$contactphone=trim($_POST['contactphone']);
		$contactcell=trim($_POST['contactcell']);
		$contactdepartment=trim($_POST['contactdepartment']);
		
		$datasource=$this->Contact->getDataSource();
		$datasource->begin();
		try {
			//pr($this->request->data);
			$contactArray=[];
			$contactArray['Contact']['client_id']=$clientid;
			$contactArray['Contact']['first_name']=$contactfirstname;
			$contactArray['Contact']['last_name']=$contactlastname;
			$contactArray['Contact']['phone']=$contactphone;
			$contactArray['Contact']['cell']=$contactcell;
			$contactArray['Contact']['email']=$contactemail;
			$contactArray['Contact']['department']=$contactdepartment;
			$contactArray['Contact']['bool_active']=true;
			if ($boolnewcontact){
				$this->Contact->create();
			}
			else {
				$this->Contact->id=$contactid;
				//pr($contactid);
				if (!$this->Contact->exists($contactid)) {
					throw new Exception(__('Contacto inválido'));
				}				
			}
			if (!$this->Contact->save($contactArray)) {
				echo "Problema guardando el contacto";
				//pr($this->validateErrors($this->Contact));
				throw new Exception();
			}
			$contact_id=$this->Contact->id;
			
			$datasource->commit();
			$this->recordUserAction($this->Contact->id,"add",null);
			$this->recordUserActivity($this->Session->read('User.username'),"Se registró el contacto ".$contactfirstname." ".$contactlastname);
			
			$this->Session->setFlash(__('The contact has been saved.'),'default',array('class' => 'success'));
			//return $this->redirect(array('action' => 'index'));
			return $contact_id;
		} 
		catch(Exception $e){
			$datasource->rollback();
			//pr($e);
			$this->Session->setFlash(__('The contact could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			return false;
		}
	}
	
	public function getcontactlist() {
		$this->layout = "ajax";
		
		$clientid=trim($_POST['clientid']);
		
		$this->Contact->recursive=-1;
		$contacts=$this->Contact->find('all',[
			'fields'=>['Contact.id','Contact.first_name','Contact.last_name','Contact.bool_active'],
			'conditions'=>[
        'Contact.client_id'=>$clientid,
        // 20201214 ADDED TO ONLY GET ACTIVE CLIENTS
        'Contact.bool_active'=>true,
      ],
      'recursive'=>-1,
		]);
		//pr($contacts);
		$this->set(compact('contacts'));
	}
	
	public function getcontactlistforcontactname() {
		$this->layout = "ajax";
		
		$clientid=trim($_POST['clientid']);
		$contactfirstnameval=trim($_POST['contactfirstnameval']);
		$contactlastnameval=trim($_POST['contactlastnameval']);
		
		$this->Contact->recursive=-1;
		$contacts=$this->Contact->find('all',array(
			'fields'=>array('Contact.id','Contact.first_name','Contact.last_name','Contact.bool_active'),
			'conditions'=>array(
				'Contact.client_id'=> $clientid,
				'Contact.first_name LIKE'=> "%$contactfirstnameval%",
				'Contact.last_name LIKE'=> "%$contactlastnameval%"
			),
		));
		//pr($clients);
		$this->set(compact('contacts'));
	}
	
	public function getcontactcount() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$clientid=trim($_POST['clientid']);
		$contactfirstnameval=trim($_POST['contactfirstnameval']);
		$contactlastnameval=trim($_POST['contactlastnameval']);
		
		$this->Contact->recursive=-1;
		$contacts=$this->Contact->find('all',array(
			'fields'=>array('Contact.id','Contact.first_name','Contact.last_name'),
			'conditions'=>array(
				'Contact.client_id'=> $clientid,
				/*
				'OR'=>array(
					array('Contact.first_name LIKE'=> "%$contactfirstnameval%"),
					array('Contact.last_name LIKE'=> "%$contactlastnameval%"),
				),
				*/
				'Contact.first_name LIKE'=> "%$contactfirstnameval%",
				'Contact.last_name LIKE'=> "%$contactlastnameval%"
			),
		));
		//pr($clients);
		echo count($contacts);
	}
	
	public function getcontactinfo() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		$this->layout = "ajax";// just in case to reduce the error message;
		
		$contactid=trim($_POST['contactid']);
		
		$this->Contact->recursive=-1;
		$contact=$this->Contact->find('first',array(
			'fields'=>array('Contact.id','Contact.first_name','Contact.last_name','Contact.phone','Contact.cell','Contact.email','Contact.department'),
			'conditions'=>array(
				'Contact.id'=> $contactid,
			),
		));
		return json_encode($contact['Contact']);
	}
}
