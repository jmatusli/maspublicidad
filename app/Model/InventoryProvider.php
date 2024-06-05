<?php
App::uses('AppModel', 'Model');
/**
 * InventoryProvider Model
 *
 * @property Product $Product
 * @property Product $Product
 */
class InventoryProvider extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Ya existe un proveedor con este nombre',
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'InventoryProductInventoryProvider' => array(
			'className' => 'InventoryProductInventoryProvider',
			'foreignKey' => 'inventory_provider_id',
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
		'InventoryProduct' => array(
			'className' => 'InventoryProduct',
			'joinTable' => 'products_providers',
			'foreignKey' => 'provider_id',
			'associationForeignKey' => 'inventory_product_id',
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
