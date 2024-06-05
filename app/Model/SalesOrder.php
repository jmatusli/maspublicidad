<?php
App::uses('AppModel', 'Model');

class SalesOrder extends AppModel {

	public $displayField='sales_order_code';

  public function getClientsBySalesOrder($salesOrderIds=[]){
    $conditions=[];
    if (!empty($salesOrderIds)){
      $conditions['SalesOrder.id']=$salesOrderIds;  
    }
    return $this->find('list',[
      'fields'=>['SalesOrder.id','SalesOrder.client_id'],
      'conditions'=>$conditions,
    ]);
  }

	public $validate = [
		'quotation_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'sales_order_date' => [
			'date' => [
				'rule' => ['date'],
			],
		],
		'sales_order_code' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
			],
			'unique' => [
				'rule' => 'isUnique',
				'message' => 'Ya existe una orden de venta con este código',
			],
		],
	];

	public $belongsTo = [
    'Client' => [
			'className' => 'Client',
			'foreignKey' => 'client_id',
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
    'Quotation' => [
			'className' => 'Quotation',
			'foreignKey' => 'quotation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'AuthorizingUser' => [
			'className' => 'User',
			'foreignKey' => 'authorizing_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
    'VendorUser' => [
			'className' => 'User',
			'foreignKey' => 'vendor_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];

	public $hasMany = [
		'SalesOrderProduct' => [
			'className' => 'SalesOrderProduct',
			'foreignKey' => 'sales_order_id',
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
    'SalesOrderRemark' => [
			'className' => 'SalesOrderRemark',
			'foreignKey' => 'sales_order_id',
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
		'InvoiceSalesOrder' => [
			'className' => 'InvoiceSalesOrder',
			'foreignKey' => 'sales_order_id',
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
		'ProductionOrder' => [
			'className' => 'ProductionOrder',
			'foreignKey' => 'sales_order_id',
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
		'ProductionProcessProduct' => [
			'className' => 'ProductionProcessProduct',
			'foreignKey' => 'sales_order_id',
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
