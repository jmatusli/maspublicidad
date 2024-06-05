<?php
App::uses('AppModel', 'Model');
/**
 * StockItemLog Model
 *
 * @property StockItem $StockItem
 * @property Product $Product
 * @property MeasuringUnit $MeasuringUnit
 * @property Currency $Currency
 * @property StockMovement $StockMovement
 */
class StockItemLog extends AppModel {

	function getQuantityInStock($inventoryProductId,$inventoryDate){
		$this->recursive=-1;
		
		$stockItemModel=ClassRegistry::init('StockItem');
		$stockItemIds=$stockItemModel->find('list',array(
			'fields'=>'StockItem.id',
			'conditions'=>array(
				'StockItem.inventory_product_id'=>$inventoryProductId,
				'StockItem.stock_item_creation_date <='=>$inventoryDate,
			),
		));
		if ($inventoryProductId==51){
			//pr($stockItemIds);
		}
		$stockItemLog=array();
		if (!empty($stockItemIds)){
			$stockItemLog=$this->find('first', array(
				'fields' => array(
					'StockItemLog.inventory_product_id',
					'StockItemLog.product_quantity'
				),
				'conditions' => array(
					'StockItemLog.inventory_product_id'=>$inventoryProductId,
					'StockItemLog.stock_item_id'=>$stockItemIds,
				),
				'order'=>'StockItemLog.stock_item_date DESC, StockItemLog.id DESC'
			));
		}
		if (!empty($stockItemLog)){
			if ($inventoryProductId==51){
				//pr($stockItemLog);
			}
			return $stockItemLog['StockItemLog']['product_quantity'];
		}
		else {
			return 0;
		}
	}

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
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
		'stock_item_date' => array(
			'date' => array(
				'rule' => array('date'),
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
		'stock_movement_id' => array(
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
		),
		'StockMovement' => array(
			'className' => 'StockMovement',
			'foreignKey' => 'stock_movement_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
