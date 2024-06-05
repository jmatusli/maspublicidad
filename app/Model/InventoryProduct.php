<?php
App::uses('AppModel', 'Model');

class InventoryProduct extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'product_line_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/*
		'code' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		*/
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			//'unique' => array(
			//	'rule' => array('checkUnique',array('name','code','brand'),false),
			//	'message' => 'Ya existe un producto con este nombre, código y marca',
			//),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Currency' => array(
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InventoryProductLine' => array(
			'className' => 'InventoryProductLine',
			'foreignKey' => 'inventory_product_line_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'MeasuringUnit' => array(
			'className' => 'MeasuringUnit',
			'foreignKey' => 'measuring_unit_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);
	
	public $hasMany = array(
		'InventoryProductInventoryProvider' => array(
			'className' => 'InventoryProductInventoryProvider',
			'foreignKey' => 'inventory_product_id',
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

/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
 /*
	public $hasAndBelongsToMany = array(
		'InventoryProvider' => array(
			'className' => 'InventoryProvider',
			'joinTable' => 'products_providers',
			'foreignKey' => 'inventory_product_id',
			'associationForeignKey' => 'provider_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
		)
	);
*/
}
