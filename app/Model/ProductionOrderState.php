<?php
App::uses('AppModel', 'Model');

class ProductionOrderState extends AppModel {
  
  function getProductionOrderStateList(){
    return $this->find('list',['order'=>['list_order'=>'ASC']]);
  }

	public $validate = [
		'name' => [
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
	public $hasMany = [
		'ProductionOrderDepartmentState' => [
			'className' => 'ProductionOrderDepartmentState',
			'foreignKey' => 'production_order_state_id',
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
		'ProductionOrderProductDepartmentState' => [
			'className' => 'ProductionOrderProductDepartmentState',
			'foreignKey' => 'production_order_state_id',
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
	] ;

}
