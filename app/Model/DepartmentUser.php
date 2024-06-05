<?php
App::uses('AppModel', 'Model');

class DepartmentUser extends AppModel {

  public function hasDepartment($userId,$departmentId){
    $userPlant=$this->find('first',[
      'conditions'=>[
        'DepartmentUser.user_id'=>$userId,
        'DepartmentUser.department_id'=>$departmentId
      ],
      'recursive'=>-1,
      'order'=>'DepartmentUser.id DESC',
    ]);
    if (empty($userPlant)){
      return false;
    }
    return $userPlant['DepartmentUser']['bool_assigned'];
  }

  public function getDepartmentListForUser($userId,$departmentId=0){
    $user=$this->User->find('first',[
      'conditions'=>['User.id'=>$userId],
      'recursive'=>-1,
    ]);
    if (empty($user)) {return null;}
    
    $departmentConditions=['Department.bool_active'=>true];
    
    if ($user['User']['role_id'] != ROLE_ADMIN){
      if($departmentId>0){
        $departmentIds=[
          $departmentId=>$departmentId,
        ];
      }
      else {
        $departmentIds=$this->getAssociatedDepartmentsForUser($userId);
      }
      $departmentConditions['Department.id']=$departmentIds;
    }
    //pr($departmentConditions);
    $departments=$this->Department->find('list',[
      'fields'=>['Department.id','Department.name'],
      'conditions'=>$departmentConditions,
      'order'=>'Department.series ASC',
    ]);
    return $departments;
  }
  
  public function getAssociatedDepartmentsForUser($userId){
    $this->recursive=-1;
		$departmentIdsAssociatedWithUserAtOneTime=$this->find('list',[
      'fields'=>['DepartmentUser.department_id'],
			'conditions'=>['DepartmentUser.user_id'=>$userId],
			'order'=>'DepartmentUser.id DESC',
		]);
    $departmentIdsAssociatedWithUserAtOneTime=array_unique($departmentIdsAssociatedWithUserAtOneTime);
    $this->Department->recursive=-1;
    $uniqueDepartments=$this->Department->find('all',[
      'conditions'=>['Department.id'=>$departmentIdsAssociatedWithUserAtOneTime,],
      'contain'=>[					
        'DepartmentUser'=>[
          'conditions'=>['DepartmentUser.user_id'=>$userId,],
          'order'=>'DepartmentUser.assignment_datetime DESC,DepartmentUser.id DESC',
        ],
  		],
    ]);
    $departmentIdsCurrentlyAssociated=[];
    foreach ($uniqueDepartments as $department){
      if ($department['DepartmentUser'][0]['bool_assigned']){
        $departmentIdsCurrentlyAssociated[]=$department['Department']['id'];
      }
    }
		return $departmentIdsCurrentlyAssociated;
	}

  public function getUsersForDepartment($departmentId){
    $users=$this->User->getActiveUserList();
    foreach ($users as $userId=>$userData){
      if (!$this->hasDepartment($userId,$departmentId)){
        unset($users[$userId]);
      }
    }
    return $users;
  }


	public $validate = [
		'user_id' => [
			'numeric' => [
				'rule' => ['numeric'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
		'department_id' => [
			'numeric' => [
				'rule' => ['numeric'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
		'assignment_datetime' => [
			'datetime' => [
				'rule' => ['datetime'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
		'bool_assigned' => [
			'boolean' => [
				'rule' => ['boolean'],
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			],
		],
	];


	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Department' => [
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];
}
