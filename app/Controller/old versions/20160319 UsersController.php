<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 */
class UsersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('login','logout');		

		// Allow users to register and logout.
		//$this->Auth->allow('add','logout');		
	}
	
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				/*
				print "<pre>";
				echo "<p>Auth->user</p>";
				print_r($this->Auth->user);
				print "</pre>";
				*/
				$this->recordUserActivity($this->data['User']['username'],"Login successful");
				$this->Session->write('User.username',$this->data['User']['username']);
				$this->Session->write('User.userid',$this->Auth->User('id'));
				$this->Session->write('User.role_id',$this->Auth->User('role_id'));
				$this->Session->write('User.id',$this->Auth->User('id'));
				
				//$userid = $this->Auth->User('id');
				//echo "user id ".$userid."!<br/>";
				$role = $this->Auth->User('role_id');
				//echo "role id ".$role."!<br/>";
				return $this->redirect(parent::userhome($role));
			}
			$this->recordUserActivity($this->data['User']['username'],"Invalid username or password");
			$this->Session->setFlash(__('Invalid username or password, try again'));
		}
	}

	public function logout() {
		$this->recordUserActivity($this->Session->read('User.username'),"Logout");
		return $this->redirect($this->Auth->logout());
	}	
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$users=$this->Paginator->paginate();
		$this->set(compact('users'));
		//pr($users);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$this->set('user', $this->User->find('first', $options));
	}
	
/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'),'default',array('class' => 'success'));
				$this->recordUserActivity($this->Session->read('User.username'),"Added new user ".$this->request->data['User']['username']);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
				$this->recordUserActivity($this->Session->read('User.username'),"Tried to add user unsuccessfully");
			}
		}
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles'));
		
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved.'),'default',array('class' => 'success'));
				$this->recordUserActivity($this->Session->read('User.username'),"Edited user ".$this->request->data['User']['username']);
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->recordUserActivity($this->Session->read('User.username'),"Tried to edit user ".$this->request->data['User']['username']." without success");
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
		$roles = $this->User->Role->find('list');
		$this->set(compact('roles'));
		
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete()) {
			$this->Session->setFlash(__('The user has been deleted.'));
		} else {
			$this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
		}
		$this->recordUserActivity($this->Session->read('User.username'),"Deleted user with id ".$id);
		return $this->redirect(array('action' => 'index'));
	}

	public function rolePermissions(){
		$this->loadModel('Role');
		
		$roles=$this->Role->find('all');
		//pr($roles);
		$this->set(compact('roles'));
		
		$consideredControllerAliases=array(
			'Clients',
			'Contacts',
			'Invoices',
			'Products',
			'Providers',
			'Quotations',
			'SalesOrders',
			'Users',
			'ProductCategories',
		);
		
		$selectedControllers=$this->Acl->Aco->find('all',array(
			'conditions'=>array(
				'Aco.parent_id'=>'1',
				'Aco.alias'=>$consideredControllerAliases,
			),
		));
		//pr($selectedControllers);
		
		$excludedActions=array(
			'controllers',
			'recordUserActivity',
			'userhome',
			'get_date',
			'recordUserAction',
			'uploadFiles',
			'hasPermission',
			'delete',
			
			
			'getclientlist',// for clients
			'getclientinfo',// for clients
			
			'getcontactlist',// for contacts
			'getcontactinfo',// for contacts
			
			'getproductimage', // for products
			
			'getquotationproducts', // for quotations
			'getquotationinfo', // for quotations
			'getnewquotationcode', // for quotations
			'getquotationsforclient', // for quotations
			
			'getsalesorderproducts', // for sales orders
			'getsalesorderinfo', // for sales orders
			'getsalesordersforclient', // for sales orders
			
			'login', // for users
			'logout', // for users
			'init_DB_permissions',
			'rolePermissions',
		);
		
		for ($c=0;$c<count($selectedControllers);$c++){
			$selectedActions=array();
			$selectedActions=$this->Acl->Aco->find('all',array(
				'conditions'=>array(
					'Aco.parent_id'=>$selectedControllers[$c]['Aco']['id'],
					'Aco.alias !='=>$excludedActions,
				),
			));
			if (!empty($selectedActions)){
				for ($a=0;$a<count($selectedActions);$a++){
					$rolePermissions=array();
					for ($r=0;$r<count($roles);$r++){
						$aco_name=$selectedControllers[$c]['Aco']['alias']."/".$selectedActions[$a]['Aco']['alias'];
						//pr($aco_name);
						$hasPermission=$this->Acl->check(array('Role'=>array('id'=>$roles[$r]['Role']['id'])),$aco_name);
						//if ($selectedActions[$a]['Aco']['id']==15){
						//	echo "permission for ".$aco_name." is ".$hasPermission."<br/>";
						//}
						if ($hasPermission){
							$rolePermissions[$r]=$hasPermission;
						}
						else {
							$rolePermissions[$r]=0;
						}						
					}
					//if ($selectedActions[$a]['Aco']['id']==15){
					//	pr($rolePermissions);
					//}
					$selectedActions[$a]['rolePermissions']=$rolePermissions;
				}
			}
			//pr($selectedActions);
			
			$selectedControllers[$c]['actions']=$selectedActions;
		}
		$this->set(compact('selectedControllers'));
		//pr($selectedControllers);
		if ($this->request->is('post')) {
			//pr($this->request->data);
			$role = $this->User->Role;
			for ($r=0;$r<count($this->request->data['Role']);$r++){
				$thisRole=$roles[$r];
				//pr($role);
				$role_id=$thisRole['Role']['id'];
				
				$role->id=$role_id;
				
				for ($c=0;$c<count($this->request->data['Role'][$r]['Controller']);$c++){
					$controller=$selectedControllers[$c];
					//pr($controller);
					$controller_alias=$controller['Aco']['alias'];
				
					for ($a=0;$a<count($this->request->data['Role'][$r]['Controller'][$c]['Action']);$a++){
						//pr($this->request->data['Role'][$r]['Controller'][$c]['Action'][$a]);
						$action=$selectedControllers[$c]['actions'][$a];
						//pr($action);
						$action_alias=$action['Aco']['alias'];
						//pr($action_alias);
						
						if ($this->request->data['Role'][$r]['Controller'][$c]['Action'][$a]){
							$this->Acl->allow($role, 'controllers/'.$controller_alias."/".$action_alias);
						}
						else {
							$this->Acl->deny($role, 'controllers/'.$controller_alias."/".$action_alias);
						}
						$this->Session->setFlash(__('Los permisos se guardaron.'),'default',array('class' => 'success'));
						//$role->id = 5;
						//$this->Acl->allow($role, 'controllers');
						//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
						//$this->Acl->deny($role, 'controllers/StockMovementTypes');
						//$this->Acl->deny($role, 'controllers/Role');			
					}					
				}				
			}
			/*
			$this->Client->create();
			if ($this->Client->save($this->request->data)) {
				$this->Session->setFlash(__('The client has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			else {
				$this->Session->setFlash(__('The client could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
			*/
		}
		
		for ($c=0;$c<count($selectedControllers);$c++){
			$selectedActions=array();
			$selectedActions=$this->Acl->Aco->find('all',array(
				'conditions'=>array(
					'Aco.parent_id'=>$selectedControllers[$c]['Aco']['id'],
					'Aco.alias !='=>$excludedActions,
				),
			));
			if (!empty($selectedActions)){
				for ($a=0;$a<count($selectedActions);$a++){
					$rolePermissions=array();
					for ($r=0;$r<count($roles);$r++){
						$aco_name=$selectedControllers[$c]['Aco']['alias']."/".$selectedActions[$a]['Aco']['alias'];
						//pr($aco_name);
						$hasPermission=$this->Acl->check(array('Role'=>array('id'=>$roles[$r]['Role']['id'])),$aco_name);
						//if ($selectedActions[$a]['Aco']['id']==15){
						//	echo "permission for ".$aco_name." is ".$hasPermission."<br/>";
						//}
						if ($hasPermission){
							$rolePermissions[$r]=$hasPermission;
						}
						else {
							$rolePermissions[$r]=0;
						}						
					}
					//if ($selectedActions[$a]['Aco']['id']==15){
					//	pr($rolePermissions);
					//}
					$selectedActions[$a]['rolePermissions']=$rolePermissions;
				}
			}
			//pr($selectedActions);
			
			$selectedControllers[$c]['actions']=$selectedActions;
		}
		$this->set(compact('selectedControllers'));
	}
	
	public function init_DB_permissions() {
	
		$role = $this->User->Role;

		// Allow admins to access everything
		$role->id = 5;
		$this->Acl->allow($role, 'controllers');
		//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
		//$this->Acl->deny($role, 'controllers/StockMovementTypes');
		//$this->Acl->deny($role, 'controllers/Role');
		
		// Allow assistants to access everything but leave out editing rights in the views and controllers
		$role->id = 6;
		$this->Acl->allow($role, 'controllers');
		//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
		//$this->Acl->deny($role, 'controllers/StockMovementTypes');
		//$this->Acl->deny($role, 'controllers/Role');
		
		// Allow assistants to access everything but leave out editing rights in the views and controllers
		$role->id = 7;
		$this->Acl->allow($role, 'controllers');
		//$this->Acl->deny($role, 'controllers/ProductionResultCodes');
		//$this->Acl->deny($role, 'controllers/StockMovementTypes');
		//$this->Acl->deny($role, 'controllers/Role');
		
		// we add an exit to avoid an ugly "missing views" error message
		echo "all done";
		exit;
	
	}

}
