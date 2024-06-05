<?php
App::uses('AppModel', 'Model');
/**
 * Invoice Model
 *
 * @property Quotation $Quotation
 * @property Client $Client
 * @property User $User
 * @property Currency $Currency
 * @property InvoiceProduct $InvoiceProduct
 */
class Invoice extends AppModel {

	public $displayField="invoice_code";
  
  public function getInvoiceCode(){
    $lastInvoiceCode=$this->find('first',[
			'order'=>['Invoice.invoice_code DESC'],
		]);
		if (!empty($lastInvoiceCode)){
			$newInvoiceCode=$lastInvoiceCode['Invoice']['invoice_code']+1;
		}
		else {
			$newInvoiceCode="2529";
		}
		return $newInvoiceCode;
  }

	public $validate = [
		'client_id' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
			],
		],
		'invoice_date' => [
			'date' => [
				'rule' => ['date'],
			],
		],
		'invoice_code' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
			],
			'unique' => [
				'rule' => 'isUnique',
				'message' => 'Ya existe una factura con este número',
			],
		],
		'currency_id' => [
			'numeric' => [
				'rule' => ['numeric'],
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
	];

	public $hasMany = [
		'InvoiceProduct' => [
			'className' => 'InvoiceProduct',
			'foreignKey' => 'invoice_id',
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
			'foreignKey' => 'invoice_id',
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
		'VendorCommissionPayment' => [
			'className' => 'VendorCommissionPayment',
			'foreignKey' => 'invoice_id',
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
