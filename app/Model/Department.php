<?php
App::uses('AppModel', 'Model');

class Department extends AppModel {
	
  public function getDepartmentList(){
    return $this->find('list',['order'=>'Department.name']);
  }
  
  public $validate = [
		'name' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
			],
		],
	];
	public $hasMany = [
		'InventoryProductLine' => [
			'className' => 'InventoryProductLine',
			'foreignKey' => 'department_id',
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
			'foreignKey' => 'department_id',
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
