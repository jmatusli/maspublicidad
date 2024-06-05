<?php
App::uses('AppModel', 'Model');
/**
 * ProductionOrderProductDepartmentInstruction Model
 *
 * @property User $User
 * @property ProductionOrder $ProductionOrder
 * @property ProductionOrderProduct $ProductionOrderProduct
 * @property ProductionOrderProductDepartment $ProductionOrderProductDepartment
 */
class ProductionOrderProductDepartmentInstruction extends AppModel {

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
		'ProductionOrderProduct' => array(
			'className' => 'ProductionOrderProduct',
			'foreignKey' => 'production_order_product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProductionOrderProductDepartment' => array(
			'className' => 'ProductionOrderProductDepartment',
			'foreignKey' => 'production_order_product_department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
