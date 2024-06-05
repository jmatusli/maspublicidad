<?php
App::build(['Vendor' => [APP . 'Vendor' . DS . 'PHPExcel']]);
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class DepartmentUsersController extends AppController {

	public $components = ['Paginator'];
	public $helpers = ['PhpExcel'];
  public function asociarUsuariosDepartamentos($selectedDepartmentId=0){
		$this->loadModel('User');
    $this->loadModel('Department');
		$this->loadModel('Role');
		
		$this->User->recursive=-1;
    $this->DepartmentUser->recursive=-1;
		$this->Department->recursive=-1;
		
		$this->request->allowMethod('get','post', 'put');
		
    $selectedRoleId=ROLE_DEPARTMENT_SUPERVISOR_PRODUCTION;
    $selectedUserId=0;
    $selectedDepartmentId=0;
		
		if ($this->request->is('post')) {
			//pr($this->request->data);
      $selectedRoleId=$this->request->data['DepartmentUser']['role_id'];
			$selectedUserId=$this->request->data['DepartmentUser']['user_id'];
			$selectedDepartmentId=$this->request->data['DepartmentUser']['department_id'];
			
      if (!empty($this->request->data['submit'])){
        $currentDateTime=new DateTime();
        $datasource=$this->DepartmentUser->getDataSource();
        $datasource->begin();
        try {
          foreach ($this->request->data['User'] as $userId=>$userValue){
            //pr($userValue);
            if ($userValue['bool_changed']){
              foreach ($userValue['Department'] as $departmentId=>$departmentValue){
                $departmentUserArray=[
                  'DepartmentUser'=>[
                    'user_id'=>$userId,
                    'department_id'=>$departmentId,
                    'assignment_datetime'=>$currentDateTime->format('Y-m-d H:i:s'),
                    'bool_assigned'=>$departmentValue['bool_assigned'],
                  ],
              ];
                //pr($departmentUserArray);
                $this->DepartmentUser->create();
                if (!$this->DepartmentUser->save($departmentUserArray)){
                  echo "Problema creando la asociación entre usuario y departamento";
                  pr($this->validateErrors($this->DepartmentUser));
                  throw new Exception();
                }								
              }
            }					
          }
          $datasource->commit();
          
          $this->recordUserAction(null,'asociarUsuariosDepartamentos','userDepartments');
          $this->recordUserActivity($this->Session->read('User.username'),"Se asignaron usuarios a departamentos");
          $this->Session->setFlash(__('Se asociaron los usuarios a los departamentos.'),'default',['class' => 'success']);
        } 
        catch(Exception $e){
          $datasource->rollback();
          pr($e);
          $this->Session->setFlash(__('No se podían asociar usuarios y departamentos.'), 'default',['class' => 'error-message']);
          $this->recordUserActivity($this->Session->read('User.username')," intentó asociar usuarios y departamentos sin éxito");
        }
      
      }
      
		}
		$this->set(compact('selectedRoleId'));
		$this->set(compact('selectedUserId'));
		$this->set(compact('selectedDepartmentId'));
		
    $departmentConditions=[
			'Department.bool_active'=>true,     
		];
		if (!empty($selectedDepartmentId)){
			$departmentConditions['Department.id']=$selectedDepartmentId;
		}
    
    $selectedDepartments=$this->Department->find('list',[
			'fields'=>[
				'Department.id',
				'Department.name',
			],
			'conditions'=>$departmentConditions,
			'contain'=>[
				'DepartmentUser'=>[
					'fields'=>[
						'DepartmentUser.id',
						'DepartmentUser.user_id',
						'DepartmentUser.bool_assigned',
						'DepartmentUser.assignment_datetime',
					],
					'order'=>'DepartmentUser.assignment_datetime DESC,DepartmentUser.id DESC',
				],
			],
			'order'=>'Department.name',
		]);
		$this->set(compact('selectedDepartments'));
    
    $userConditions=['User.bool_active'=>true];
		if (!empty($selectedUserId)){
			$userConditions['User.id']=$selectedUserId;
		}
    $selectedUsers=$this->User->find('all',[
			'fields'=>['User.id','User.username'],
			'conditions'=>$userConditions,
      'contain'=>[
				'DepartmentUser'=>[
					'fields'=>[
						'DepartmentUser.id',
						'DepartmentUser.user_id',
						'DepartmentUser.bool_assigned',
						'DepartmentUser.assignment_datetime',
					],
					'order'=>'DepartmentUser.assignment_datetime DESC,DepartmentUser.id DESC',
				],
			],
			'order'=>'User.username',			
		]);
		$this->set(compact('selectedUsers'));
		//pr($selectedUsers);
    
    $roleConditions=[];
		if (!empty($selectedRoleId)){
			$roleConditions['Role.id']=$selectedRoleId;
		}
    $selectedRoles=$this->Role->find('all',[
			'fields'=>['Role.id','Role.name'],
			'conditions'=>$roleConditions,
      'contain'=>[
        'User'=>[
          'conditions'=>$userConditions,
          'order'=>'User.username',		
          'DepartmentUser'=>[
            'fields'=>[
              'DepartmentUser.id',
              'DepartmentUser.user_id',
              'DepartmentUser.department_id',
              'DepartmentUser.bool_assigned',
              'DepartmentUser.assignment_datetime',
            ],
            'order'=>'DepartmentUser.assignment_datetime DESC,DepartmentUser.id DESC',
          ],
        ],
      ],
      'order'=>'Role.list_order',			
		]);
		$this->set(compact('selectedUsers'));
		//pr($selectedUsers);
    
    
		for ($r=0;$r<count($selectedRoles);$r++){
      for ($u=0;$u<count($selectedRoles[$r]['User']);$u++){
        //pr($selectedUsers[$c]);
        $selectedRoles[$r]['User'][$u]['Department']=[];
        $departmentArray=[];
        if (!empty($selectedRoles[$r]['User'][$u]['DepartmentUser'])){
          foreach ($selectedDepartments as $departmentId=>$departmentValue){
            $departmentArray[$departmentId]=0;
            foreach ($selectedRoles[$r]['User'][$u]['DepartmentUser'] as $userDepartment){
              //pr($userDepartment);
              if ($userDepartment['department_id']==$departmentId){
                $departmentArray[$departmentId]=$userDepartment['bool_assigned'];
                break;
              }
            }
          }
        }
        $selectedRoles[$r]['User'][$u]['Department']=$departmentArray;
      }
    }
		$this->set(compact('selectedUsers'));
    $this->set(compact('selectedRoles'));
		//pr($selectedRoles);
		
    $roles=$this->Role->getRoles();
		$this->set(compact('roles'));
    
		$users=$this->User->find('list',[
			'fields'=>[
				'User.id',
				'User.username',
			],
			'order'=>'User.username',			
		]);
		$this->set(compact('users'));
		
		$departments=$this->Department->find('list',[
			'fields'=>[
				'Department.id',
				'Department.name',
			],
			'conditions'=>[
				'Department.bool_active'=>true,
			],
			'order'=>'Department.name',
		]);
		$this->set(compact('departments'));
	}
	
	public function guardarUsuariosDepartamentos() {
		$exportData=$_SESSION['resumenAsociacionesUsuariosDepartamentos'];
		$this->set(compact('exportData'));
	}

}
