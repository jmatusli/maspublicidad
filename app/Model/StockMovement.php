<?php
App::uses('AppModel', 'Model');
/**
 * StockMovement Model
 *
 * @property Entry $Entry
 * @property Remission $Remission
 * @property StockItem $StockItem
 * @property Product $Product
 * @property MeasuringUnit $MeasuringUnit
 * @property Currency $Currency
 * @property StockItemLog $StockItemLog
 */
class StockMovement extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'movement_date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'bool_input' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'stock_item_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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
		'measuring_unit_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'currency_id' => array(
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
		'Entry' => array(
			'className' => 'Entry',
			'foreignKey' => 'entry_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Remission' => array(
			'className' => 'Remission',
			'foreignKey' => 'remission_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'StockItem' => array(
			'className' => 'StockItem',
			'foreignKey' => 'stock_item_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'InventoryProduct' => array(
			'className' => 'InventoryProduct',
			'foreignKey' => 'inventory_product_id',
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
		'Currency' => array(
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'StockItemLog' => array(
			'className' => 'StockItemLog',
			'foreignKey' => 'stock_movement_id',
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
