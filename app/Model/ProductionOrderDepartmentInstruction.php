<?php
App::uses('AppModel', 'Model');
/**
 * ProductionOrderDepartmentInstruction Model
 *
 * @property User $User
 * @property ProductionOrder $ProductionOrder
 * @property ProductionOrderDepartment $ProductionOrderDepartment
 * @property Department $Department
 */
class ProductionOrderDepartmentInstruction extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'instruction_text';


	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProductionOrder' => array(
			'className' => 'ProductionOrder',
			'foreignKey' => 'production_order_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProductionOrderDepartment' => array(
			'className' => 'ProductionOrderDepartment',
			'foreignKey' => 'production_order_department_id',
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
}
