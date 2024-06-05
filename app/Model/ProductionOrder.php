<?php
App::uses('AppModel', 'Model');

class ProductionOrder extends AppModel {

	public $displayField='production_order_code';
	public function getPercentageProcessed($id){
		$productionOrder=$this->find('first',array(
			'conditions'=>array(
				'ProductionOrder.id'=>$id,
			),
			'contain'=>array(
				'ProductionOrderProduct'=>array(
					'SalesOrderProduct'=>array(
						'SalesOrderProductStatus',
					),
				),
			),
		));
		$totalSalesOrderProduct=0;
		$processedSalesOrderProduct=0;
		if (!empty($productionOrder['ProductionOrderProduct'])){
			foreach ($productionOrder['ProductionOrderProduct'] as $productionOrderProduct){
				if (!empty($productionOrderProduct['SalesOrderProduct'])){
					$totalSalesOrderProduct+=$productionOrderProduct['SalesOrderProduct']['product_quantity'];
					if ($productionOrderProduct['SalesOrderProduct']['SalesOrderProductStatus']['id']>=PRODUCT_STATUS_READY_FOR_DELIVERY){
						$processedSalesOrderProduct+=$productionOrderProduct['SalesOrderProduct']['product_quantity'];
					}
				}
			}
		}
		if ($totalSalesOrderProduct==0){
			return 0;
		}
		else {
			return round(100*$processedSalesOrderProduct/$totalSalesOrderProduct,2);
		}
		
	}
	
	public $validate = [
		'sales_order_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'production_order_date' => [
			'date' => [
				'rule' => ['date'],
			],
		],
		'production_order_code' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
			],
		],
	];

	public $belongsTo = [
		'SalesOrder' => [
			'className' => 'SalesOrder',
			'foreignKey' => 'sales_order_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];

	public $hasMany = [
    'ProductionOrderDepartment' => [
			'className' => 'ProductionOrderDepartment',
			'foreignKey' => 'production_order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
		'ProductionOrderProduct' => [
			'className' => 'ProductionOrderProduct',
			'foreignKey' => 'production_order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
		'ProductionOrderRemark' => [
			'className' => 'ProductionOrderRemark',
			'foreignKey' => 'production_order_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		],
	];

}
