<?php
App::uses('AppModel', 'Model');
class PurchaseOrderProduct extends AppModel {

	public $validate = [
		'purchase_order_id' => [
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
		'PurchaseOrder' => [
			'className' => 'PurchaseOrder',
			'foreignKey' => 'purchase_order_id',
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
		'Currency' => [
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'ProductionOrderProduct' => [
			'className' => 'ProductionOrderProduct',
			'foreignKey' => 'production_order_product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Department' => [
			'className' => 'Department',
			'foreignKey' => 'department_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'ProductionOrder' => [
			'className' => 'ProductionOrder',
			'foreignKey' => 'production_order_id',
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
}
