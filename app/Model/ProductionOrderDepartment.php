<?php
App::uses('AppModel', 'Model');

class ProductionOrderDepartment extends AppModel {

	public $belongsTo = array(
		'ProductionOrder' => array(
			'className' => 'ProductionOrder',
			'foreignKey' => 'production_order_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Department' => array(
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public $hasMany = array(
		'ProductionOrderDepartmentInstruction' => array(
			'className' => 'ProductionOrderDepartmentInstruction',
			'foreignKey' => 'production_order_department_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductionOrderDepartmentState' => array(
			'className' => 'ProductionOrderDepartmentState',
			'foreignKey' => 'production_order_department_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
