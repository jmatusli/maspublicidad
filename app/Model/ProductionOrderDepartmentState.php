<?php
App::uses('AppModel', 'Model');
/**
 * ProductionOrderDepartmentState Model
 *
 * @property User $User
 * @property ProductionOrder $ProductionOrder
 * @property ProductionOrderDepartment $ProductionOrderDepartment
 * @property Product $Product
 * @property Department $Department
 * @property ProductionOrderState $ProductionOrderState
 */
class ProductionOrderDepartmentState extends AppModel {

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'production_order_state_id';


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
		'Product' => array(
			'className' => 'Product',
			'foreignKey' => 'product_id',
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
		),
		'ProductionOrderState' => array(
			'className' => 'ProductionOrderState',
			'foreignKey' => 'production_order_state_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
