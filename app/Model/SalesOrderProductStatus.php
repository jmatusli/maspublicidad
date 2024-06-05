<?php
App::uses('AppModel', 'Model');


class SalesOrderProductStatus extends AppModel {

	public $displayField="status";

  public function getSalesOrderProductList(){
    return $this->find('list');
  }

	public $validate = [
		'status' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
			],
		],
	];

	public $hasMany = [
		'SalesOrderProduct' => [
			'className' => 'SalesOrderProduct',
			'foreignKey' => 'sales_order_product_status_id',
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
