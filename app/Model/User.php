<?php
App::uses('AppModel', 'Model');


class User extends AppModel {
	public $actsAs = ['Acl' => ['type' => 'requester', 'enabled' => false]];

  public $displayField="username";

  public function bindNode($user) {
		return array('model' => 'Role', 'foreign_key' => $user['User']['role_id']);
	}
	
  public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['role_id'])) {
            $roleId = $this->data['User']['role_id'];
        } 
		else {
            $roleId = $this->field('role_id');
        }
        if (!$roleId) {
            return null;
        } else {
            return array('Role' => array('id' => $roleId));
        }
    }

	public function beforeSave($options = array()) {
		//echo "executing beforesave";
		//pr($this->data[$this->alias]);
		
		if (array_key_exists('pwd',$this->data[$this->alias])){
			if (!empty($this->data[$this->alias]['pwd'])){
				//echo "now I am encrypting the password with the AuthComponent for the pwd";
				$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['pwd']);
				//pr($this->request->data[$this->alias]);
			}
			else {
				// if password is not set, auth data is taken into account
				unset($this->data[$this->alias]['password']);
				if(!empty($this->data[$this->alias]['id'])){
					$currentUser=$this->find('first',array(
						'conditions'=>array(
							'User.id'=>$this->data[$this->alias]['id'],
						),
					));
					if (!empty($currentUser)){
						$this->data[$this->alias]['password']=$currentUser[$this->alias]['password'];
					}
				}
			}
		}
		elseif (array_key_exists('password',$this->data[$this->alias])){
			if (!empty($this->data[$this->alias]['password'])){
				//echo "now I am encrypting the password with the AuthComponent for the pwd";
				$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
				//pr($this->request->data[$this->alias]);
			}
		}
		
		//echo "printing the request data<br/>";
		//pr($this->data[$this->alias]);
    return true;
	}
	
  public function getUserList($userIds){
    $conditions=[];
    if (!empty($userIds)){
      $conditions=['User.id'=>$userIds];
    }
    $users=$this->find('list',[
      'conditions'=>$conditions,
      'order'=>'User.username ASC',
    ]);
    return $users;
  }
  
  public function getUserNaturalNameList(){
    $allUsers=$this->find('all',[
      'recursive'=>-1,
    ]);
    $userList=[];
    foreach ($allUsers as $user){
      $userList[$user['User']['id']]=$user['User']['first_name'].' '.$user['User']['last_name'];
    }
    return $userList;
  }
  
  public function getActiveUsersForRole($roleId){
    $users=$this->find('list',[
      'conditions'=>[
        'User.bool_active'=>true,
        'User.bool_show_in_list'=>true,
        'User.bool_test'=>false,
        'User.role_id'=>$roleId,
        
      ],
      'order'=>'User.username ASC',
    ]);
    return $users;
  }
  
  public function getActiveSalesUsersOnlyList(){
    $users=$this->find('all',[
      'fields'=>['User.id','User.first_name','User.last_name'],
      'conditions'=>[
        'User.bool_active'=>true,
        'User.bool_show_in_list'=>true,
        'User.bool_test'=>false,
        'User.role_id' => [ROLE_SALES_EXECUTIVE, ROLE_DEPARTMENT_SUPERVISOR_SALES],
      ],
      'recursive'=>-1,
      'order'=>'User.username ASC',
    ]);
    $userList=[];
    if (!empty($users)){
      foreach ($users as $user){
        $userList[$user['User']['id']]=$user['User']['first_name'].' '.$user['User']['last_name'];
      }
    }
    return $userList;
  }

  public function getActiveVendorAllUsers($userId = 0){
    $userConditions=[
        'User.bool_active'=>true,
        'User.bool_show_in_list'=>true,
        'User.bool_test'=>false,
        'User.role_id' => [ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE, ROLE_DEPARTMENT_SUPERVISOR_SALES],
      ];
    if ($userId>0){
			$userConditions['User.id']=$userId;
		}
    return $this->find('all',[
      'fields'=>['User.id','User.username','User.first_name','User.last_name'],
      'conditions'=>$userConditions,
      'recursive'=>-1,
      'order'=>'User.username ASC',
    ]);
  }
  public function getActiveVendorAllVendors($userId = 0){
    $userConditions=[
        'User.bool_active'=>true,
        'User.bool_show_in_list'=>true,
        'User.bool_test'=>false,
        'User.role_id' => [ROLE_SALES_EXECUTIVE, ROLE_DEPARTMENT_SUPERVISOR_SALES],
      ];
    if ($userId>0){
			$userConditions['User.id']=$userId;
		}
    return $this->find('all',[
      'fields'=>['User.id','User.username','User.first_name','User.last_name'],
      'conditions'=>$userConditions,
      'recursive'=>-1,
      'order'=>'User.username ASC',
    ]);
  }

  public function getActiveVendorAllUserList($userId = 0){
    $userConditions=[
        'User.bool_active'=>true,
        'User.bool_show_in_list'=>true,
        'User.bool_test'=>false,
        'User.role_id' => [ROLE_ADMIN,ROLE_ASSISTANT,ROLE_SALES_EXECUTIVE, ROLE_DEPARTMENT_SUPERVISOR_SALES],
      ];
    if ($userId>0){
			$userConditions['User.id']=$userId;
		}
    $users=$this->find('all',[
      'fields'=>['User.id','User.first_name','User.last_name'],
      'conditions'=>$userConditions,
      'recursive'=>-1,
      'order'=>'User.username ASC',
    ]);
    $userList=[];
    if (!empty($users)){
      foreach ($users as $user){
        $userList[$user['User']['id']]=$user['User']['first_name'].' '.$user['User']['last_name'];
      }
    }
    return $userList;
  }

	public function getAllActiveListUsers($userId = 0){
    $userConditions=[
      'User.bool_active'=>true,
      'User.bool_show_in_list'=>true,
    ];
		if ($userId>0){
			$userConditions['User.id']=$userId;
		}
    return $this->find('all',[
			'fields'=>['User.id','User.username'],
			'conditions'=>$userConditions,
      'recursive'=>-1,
      'order'=>'User.username ASC'
		]);
  }
  
  public function getActiveListUserList($userId = 0){
    $userConditions=[
      'User.bool_active'=>true,
      'User.bool_show_in_list'=>true,
    ];
		if ($userId>0){
			$userConditions['User.id']=$userId;
		}
    return $this->find('list',[
			'fields'=>['User.id','User.username'],
			'conditions'=>$userConditions,
      'order'=>'User.username ASC'
		]);
  }
  public function getActiveUserList($userId = 0){
    $userConditions=[
      'User.bool_active'=>true,
      'User.bool_show_in_list'=>true,
    ];
		if ($userId>0){
			$userConditions['User.id']=$userId;
		}
    return $this->find('list',[
			'fields'=>['User.id','User.username'],
			'conditions'=>$userConditions,
      'order'=>'User.username ASC'
		]);
  }
  
  public function getUserById($userId){
    return $this->find('first',[
			'conditions'=>['User.id'=>$userId,],
      'recursive'=>-1,
		]);
  }
  public function getUserAbbreviationById($userId){
    $selectedUser=$this->getUserById($userId);
    return $selectedUser['User']['abbreviation'];
  }
  
	public $validate = [
		'username' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
		'password' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
		'role_id' => [
			'numeric' => [
				'rule' => ['numeric'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
		'first_name' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
		'last_name' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
		'abbreviation' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
	];

	public $belongsTo = [
		'Role' => [
			'className' => 'Role',
			'foreignKey' => 'role_id',
			'conditions' => '',
			'fields' => '',
			'order' => 'list_order ASC'
		],
		'Department' => [
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Company' => [
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Employee' => [
			'className' => 'Employee',
			'foreignKey' => 'employee_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];

	public $hasMany = [
		'Quotation' => [
			'className' => 'Quotation',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
		'InvoiceSalesOrder' => [
			'className' => 'InvoiceSalesOrder',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
		
		'UserLog' => [
			'className' => 'UserLog',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
		//Client association are the clients CREATED by the user
		'Client'=>[
			'className' => 'Client',
			'foreignKey' => 'creating_user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
		//Client User association is the relation between user and client created manually
		'ClientUser' => [
			'className' => 'ClientUser',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
		'ProductionProcessProduct' => [
			'className' => 'ProductionProcessProduct',
			'foreignKey' => 'operator_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
    'DepartmentUser' => [
			'className' => 'DepartmentUser',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
	];

}
