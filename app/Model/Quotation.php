<?php
App::uses('AppModel', 'Model');
class Quotation extends AppModel {
	public $displayField = 'quotation_code';

  public function getQuotationById($quotationId){
    return $this->find('first',[
      'conditions'=>['Quotation.id'=>$quotationId],
      'recursive'=>-1,
    ]);
  }
  public function getQuotationData($quotationId){
     return $this->getQuotationById($quotationId);
  }
  
  public function getClientIdsForQuotationIds($quotationIds=[]){
    return $this->find('list',[
			'fields'=>['Quotation.client_id'],
			'conditions'=>[
				'Quotation.id'=>$quotationIds,
			],
		]);
  }
  
  public $validate = [
		'user_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
			'positive'=>[
				'rule' => ['comparison',">",'0'],
				'message' => 'Se debe seleccionar el ejecutivo',
			],
		],
		'client_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
			'positive'=>[
				'rule' => ['comparison',">",'0'],
				'message' => 'Se debe indicar el cliente',
			],
		],
		'contact_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
			'positive'=>[
				'rule' => ['comparison',">",'0'],
				'message' => 'Se debe indicar el contacto',
			],
			
		],
		'quotation_date' => [
			'date' => [
				'rule' => ['date'],
			],
		],
		'quotation_code' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
			],
			'unique' => [
				'rule' => ['checkUnique',['quotation_code','user_id'],false],
				'message' => 'Ya existe una cotización con este número para este ejecutivo de venta',
			],
		],
		'currency_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
			'positive'=>[
				'rule' => ['comparison',">",'0'],
				'message' => 'Se debe seleccionar la moneda',
			],
		],
	];


	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Client' => [
			'className' => 'Client',
			'foreignKey' => 'client_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
		'Contact' => [
			'className' => 'Contact',
			'foreignKey' => 'contact_id',
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
		'RejectedReason' => [
			'className' => 'RejectedReason',
			'foreignKey' => 'rejected_reason_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];


	public $hasMany = [
		'QuotationImage' => [
			'className' => 'QuotationImage',
			'foreignKey' => 'quotation_id',
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
		'QuotationProduct' => [
			'className' => 'QuotationProduct',
			'foreignKey' => 'quotation_id',
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
		'QuotationRemark' => [
			'className' => 'QuotationRemark',
			'foreignKey' => 'quotation_id',
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
		'SalesOrder' => [
			'className' => 'SalesOrder',
			'foreignKey' => 'quotation_id',
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
