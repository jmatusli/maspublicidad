<?php
App::uses('AppModel', 'Model');

class ProductionOrderProduct extends AppModel {

  public function getProductionOrderProductData($productionOrderProductId){
    return $this->find('first',[
      'conditions'=>['ProductionOrderProduct.id'=>$productionOrderProductId],
      'contain'=>[
        'Product',
        'ProductionOrder'=>['SalesOrder'],
        'ProductionOrderProductDepartment'=>[
          'ProductionOrderProductDepartmentInstruction',
          'ProductionOrderProductDepartmentState'=>[
            'ProductionOrderState',
          ],
        ],
        'ProductionOrderProductOperationLocation',
      ],
    ]);
  } 

	public $validate = [
		'production_order_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'product_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'product_quantity' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
	];

	public $belongsTo = [
		'ProductionOrder' => [
			'className' => 'ProductionOrder',
			'foreignKey' => 'production_order_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Product' => [
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'SalesOrderProduct' => [
			'className' => 'SalesOrderProduct',
			'foreignKey' => 'sales_order_product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];

	public $hasMany = [
		'ProductionOrderProductOperationLocation' => [
			'className' => 'ProductionOrderProductOperationLocation',
			'foreignKey' => 'production_order_product_id',
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
		'ProductionOrderProductDepartment' => [
			'className' => 'ProductionOrderProductDepartment',
			'foreignKey' => 'production_order_product_id',
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
		'PurchaseOrderProduct' => [
			'className' => 'PurchaseOrderProduct',
			'foreignKey' => 'production_order_product_id',
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
