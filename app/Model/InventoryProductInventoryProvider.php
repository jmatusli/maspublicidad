<?php
App::uses('AppModel', 'Model');
/**
 * InventoryProductInventoryProvider Model
 *
 * @property InventoryProduct $InventoryProduct
 * @property InventoryProvider $InventoryProvider
 */
class InventoryProductInventoryProvider extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'inventory_product_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'inventory_provider_id' => array(
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
		'InventoryProduct' => array(
			'className' => 'InventoryProduct',
			'foreignKey' => 'inventory_product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InventoryProvider' => array(
			'className' => 'InventoryProvider',
			'foreignKey' => 'inventory_provider_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
