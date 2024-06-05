<?php
App::uses('AppModel', 'Model');

class ProductionOrderProductDepartment extends AppModel {

  public function getCurrentProductDepartmentStatus($currentProductionOrderProductDepartmentId,$productionOrderProductDepartmentData){
    $departmentStatus=[
      'department_id'=>0,
      'department_state_id'=>'',
      'department_state_name'=>'',
      'department_state_datetime'=>'',
      'department_state_user_id'=>'',
      'Instruction'=>[],
    ];
    if (!empty($productionOrderProductDepartmentData)){
      foreach ($productionOrderProductDepartmentData as $productDepartment){
        if ($productDepartment['id']==$currentProductionOrderProductDepartmentId){
          $departmentStatus=[
            'department_id'=>$productDepartment['department_id'],
            'department_state_id'=>end($productDepartment['ProductionOrderProductDepartmentState'])['production_order_state_id'],
            'department_state_name'=>end($productDepartment['ProductionOrderProductDepartmentState'])['ProductionOrderState']['name'],
            'department_state_datetime'=>end($productDepartment['ProductionOrderProductDepartmentState'])['state_datetime'],
            'department_state_user_id'=>end($productDepartment['ProductionOrderProductDepartmentState'])['user_id'],
            'Instruction'=>$productDepartment['ProductionOrderProductDepartmentState'],
          ];
          break;  
        }
      }
    }
    return $departmentStatus;
  }
/*
  public function getCurrentProductDepartmentStatus($productionOrderProductDepartmentData){
    $departmentStatus=[
      'department_id'=>0,
      'department_state_id'=>'',
      'department_state_name'=>'',
      'department_state_datetime'=>'',
      'department_state_user_id'=>'',
      'Instruction'=>[],
    ];
    if (!empty($productionOrderProductDepartmentData)){
      foreach ($productionOrderProductDepartmentData as $productDepartment){
        if (!in_array(end($productDepartment['ProductionOrderProductDepartmentState'])['production_order_state_id'],[PRODUCTION_ORDER_STATE_AWAITING_PREVIOUS,PRODUCTION_ORDER_STATE_SENT_NEXT_DEPARTMENT])){
          $departmentStatus=[
            'department_id'=>$productDepartment['department_id'],
            'department_state_id'=>end($productDepartment['ProductionOrderProductDepartmentState'])['production_order_state_id'],
            'department_state_name'=>end($productDepartment['ProductionOrderProductDepartmentState'])['ProductionOrderState']['name'],
            'department_state_datetime'=>end($productDepartment['ProductionOrderProductDepartmentState'])['state_datetime'],
            'department_state_user_id'=>end($productDepartment['ProductionOrderProductDepartmentState'])['user_id'],
            'Instruction'=>$productDepartment['ProductionOrderProductDepartmentState'],
          ];
          break;  
        }
      }
    }
    return $departmentStatus;
  }
*/
	public $validate = [
		'production_order_product_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'department_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'rank' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
	];

	public $belongsTo = [
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
    'Product' => [
			'className' => 'Product',
			'foreignKey' => 'product_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		],
	];
  
  public $hasMany = [
    'ProductionOrderProductDepartmentInstruction' => [
			'className' => 'ProductionOrderProductDepartmentInstruction',
			'foreignKey' => 'production_order_product_department_id',
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
    'ProductionOrderProductDepartmentState' => [
			'className' => 'ProductionOrderProductDepartmentState',
			'foreignKey' => 'production_order_product_department_id',
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
