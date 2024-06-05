<?php
App::uses('AppModel', 'Model');
/**
 * ProductionOrderProductOperationLocation Model
 *
 * @property ProductionOrderProduct $ProductionOrderProduct
 * @property OperationLocation $OperationLocation
 */
class ProductionOrderProductOperationLocation extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'production_order_product_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'operation_location_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ProductionOrderProduct' => array(
			'className' => 'ProductionOrderProduct',
			'foreignKey' => 'production_order_product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'OperationLocation' => array(
			'className' => 'OperationLocation',
			'foreignKey' => 'operation_location_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
