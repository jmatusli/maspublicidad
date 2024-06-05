<?php
App::uses('AppModel', 'Model');
/**
 * StockItem Model
 *
 * @property Product $Product
 * @property Currency $Currency
 * @property MeasuringUnit $MeasuringUnit
 * @property StockItemLog $StockItemLog
 * @property StockMovement $StockMovement
 */
class StockItem extends AppModel {
/*
	function getInventoryTotals($productcategoryid,$producttypeids){
		return $this->find('all', array(
			'fields' => array(
				'StockItem.product_id',
				'Product.name',
				'ProductionResultCode.code',
				'SUM(StockItem.remaining_quantity) AS inventory_total'
			),
			'contain' => array(
				'ProductionResultCode'=>array(
					'fields'=>array('code')
				),
				'Product'=>array(
					'fields'=>array('name'),
					'ProductType'=>array(
						'fields'=> array('product_category_id')
					)
				)
			),				
			'conditions' => array(
				'StockItem.remaining_quantity >'=>'0',
				'Product.product_type_id'=> $producttypeids
			),
			'group' => array('StockItem.product_id','StockItem.production_result_code_id'), 
		));
	}
*/	
	function getMaterialsForRemission($productid=null,$quantityneeded=0,$remissionDate,$orderby="ASC"){
		$remissionDatePlusOne=date("Y-m-d",strtotime($remissionDate."+1 days"));
		$otherMaterialsComplete=false;
		$usedOtherMaterials=array();
		$otherMaterialsItems = $this->find('all', array(
			'fields' => array(
				'StockItem.id',
				'StockItem.name',
				'StockItem.product_unit_cost',
				'StockItem.product_remaining_quantity',
			),
			'conditions' => array(
				'StockItem.inventory_product_id'=>$productid,
				'StockItem.product_remaining_quantity >'=>'>0',
				'StockItem.stock_item_creation_date <'=>$remissionDatePlusOne,
			),
			'order'=>'StockItem.stock_item_creation_date '.$orderby,
		));
		for ($i=0;$i<sizeof($otherMaterialsItems);$i++){
			$otherid=$otherMaterialsItems[$i]['StockItem']['id'];
			$othername=$otherMaterialsItems[$i]['StockItem']['name'];
			$otherunitprice=$otherMaterialsItems[$i]['StockItem']['product_unit_cost'];
			$quantitypresent=$otherMaterialsItems[$i]['StockItem']['product_remaining_quantity'];
			if ($quantityneeded>$quantitypresent){
				// consume all the materials in the present stockitem and move to the next
				$quantityused=$quantitypresent;
				$quantityremaining=0;
				$quantityneeded-=$quantitypresent;
			}
			else {
				// consume the necessary materials and indicate the raw materials are complete
				$quantityused=$quantityneeded;
				$quantityremaining=$quantitypresent-$quantityneeded;
				$quantityneeded=0;
				$otherMaterialsComplete = true;
			}
			$usedOtherMaterials[$i]['id']=$otherid;
			$usedOtherMaterials[$i]['name']=$othername;
			$usedOtherMaterials[$i]['product_unit_cost']=$otherunitprice;
			
			$usedOtherMaterials[$i]['quantity_present']=$quantitypresent;
			$usedOtherMaterials[$i]['quantity_used']=$quantityused;
			$usedOtherMaterials[$i]['quantity_remaining']=$quantityremaining;
			if ($otherMaterialsComplete){
				break;
			}
		}
		return $usedOtherMaterials;
	}
	

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
		'stock_item_creation_date' => array(
			'date' => array(
				'rule' => array('date'),
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
		'Currency' => array(
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
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
			'foreignKey' => 'stock_item_id',
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
		'StockMovement' => array(
			'className' => 'StockMovement',
			'foreignKey' => 'stock_item_id',
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
