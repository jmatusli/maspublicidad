<?php
App::uses('AppModel', 'Model');

class Product extends AppModel {

  public function getProductList(){
    return $this->find('list',['order'=>'Product.name']);
  }		

	public $validate = [
		'product_category_id' => [
			'numeric' => [
				'rule' => ['numeric'],
				
			],
		],
		'name' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				
			],
			'unique' => [
				'rule' => ['checkUnique',['name','code'],false],
				'message' => 'Ya existe un producto con este nombre, código y marca',
			],
		],
	];

	public $belongsTo = [
		'ProductCategory' => [
			'className' => 'ProductCategory',
			'foreignKey' => 'product_category_id',
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
	];
	
	public $hasMany = [
		'ProductProvider' => [
			'className' => 'ProductProvider',
			'foreignKey' => 'product_id',
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
