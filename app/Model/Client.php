<?php
App::uses('AppModel', 'Model');
/**
 * Client Model
 *
 * @property Contact $Contact
 * @property Invoice $Invoice
 * @property Quotation $Quotation
 */
class Client extends AppModel {

	public $displayField="name";
 
  public function getClientListForClientIds($clientIds=[]){
    return $this->find('list',[
      'conditions'=>['Client.id'=>$clientIds],
      'order'=>'Client.name ASC',
    ]);
  }
  public function getClientDataByClient($clientIds=[]){
    $conditions=[];
    if (!empty($clientIds)){
      $conditions['Client.id']=$clientIds;
    }
    $allClients=$this->find('all',[
      'fields'=>['Client.id','Client.bool_generic','Client.name','Client.phone','Client.cell','Client.email','Client.address','Client.ruc',],
      'conditions'=>$conditions,
      'recursive'=>-1,
      'order'=>'Client.id ASC',
    ]);
    $clients=[];
    if (!empty($allClients)){
      foreach ($allClients as $client){
        $clients[$client['Client']['id']]=$client['Client'];
      }
    }
    return $clients;
  }
  
  public function getGenericClientIds(){
    $fields=['Client.id'];
    $conditions=[
      'Client.bool_generic'=>true,
    ];
    return $this->find('list',[
			'fields'=>$fields,
			'conditions'=>$conditions,
      'order'=>'Client.id',
		]);
  }
  
  
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
			),
			'unique' => array(
				'rule' => 'isUnique',
				'message' => 'Ya existe un cliente con este nombre',
			),
		),
	);

	public $belongsTo = [
		'CreatingUser' => [
			'className' => 'User',
			'foreignKey' => 'creating_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];
	
	public $hasMany = [
		'Contact' => [
			'className' => 'Contact',
			'foreignKey' => 'client_id',
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
		'Invoice' => [
			'className' => 'Invoice',
			'foreignKey' => 'client_id',
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
		'Quotation' => [
			'className' => 'Quotation',
			'foreignKey' => 'client_id',
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
		'ClientUser' => [
			'className' => 'ClientUser',
			'foreignKey' => 'client_id',
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
