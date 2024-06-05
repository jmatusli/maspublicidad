<?php
App::uses('AppModel', 'Model');

class OperationLocation extends AppModel {

  
  public function getOperationLocationList(){
    return $this->find('list',['order'=>'OperationLocation.name']);
  }	
		
	public $validate = [
		'name' => [
			'notEmpty' => [
				'rule' => ['notEmpty'],
				
			],
		],
	];
}
