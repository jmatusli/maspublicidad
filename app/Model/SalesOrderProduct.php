<?php
App::uses('AppModel', 'Model');

class SalesOrderProduct extends AppModel {

	public function calculateSalesOrderProductStatus($sales_order_product_id, $invoice_id=0){
		$salesOrderProduct=$this->find('first',[
      'fields'=>['SalesOrderProduct.id','SalesOrderProduct.product_quantity'],
			'conditions'=>[
				'SalesOrderProduct.id'=>$sales_order_product_id,
			],
			'contain'=>[
				'SalesOrder'=>[
					'fields'=>['SalesOrder.id','SalesOrder.sales_order_code','SalesOrder.bool_authorized'],
				],
				'ProductionOrderProduct'=>[
					'ProductionOrderProductDepartment',
				],
				'PurchaseOrderProduct'=>[
					'fields'=>['PurchaseOrderProduct.bool_received'],
				],
				'ProductionProcessProduct'=>[
					'ProductionProcess'=>[
						'fields'=>['ProductionProcess.department_id'],
					],
				],
				'InvoiceProduct'=>[
          'fields'=>['InvoiceProduct.id','InvoiceProduct.product_quantity'],
					'Invoice',
				],
			],
		]);
		if (!empty($salesOrderProduct)){
      $totalQuantityInvoices=0;
			if (!empty($salesOrderProduct['InvoiceProduct'])){
        foreach ($salesOrderProduct['InvoiceProduct'] as $invoiceProduct){
          $totalQuantityInvoices += $invoiceProduct['product_quantity'];
        }
      }
      if ($totalQuantityInvoices == $salesOrderProduct['SalesOrderProduct']['product_quantity']){
        return PRODUCT_STATUS_DELIVERED;  
      }
			else {
				$productProducedForAllDepartments=true;
				if (!empty($salesOrderProduct['ProductionOrderProduct'])){
					foreach ($salesOrderProduct['ProductionOrderProduct'][0]['ProductionOrderProductDepartment'] as $productDepartment){
						$boolDepartmentPresent=false;
						foreach ($salesOrderProduct['ProductionProcessProduct'] as $processProduct){
							if ($processProduct['ProductionProcess']['department_id']==$productDepartment['department_id']){
								$boolDepartmentPresent=true;
							}
						}
						$productProducedForAllDepartments=$productProducedForAllDepartments&&$boolDepartmentPresent;
					}
				}
				if ($productProducedForAllDepartments){
					return PRODUCT_STATUS_READY_FOR_DELIVERY;
				}
				elseif (!empty($salesOrderProduct['PurchaseOrderProduct'])){
					if ($salesOrderProduct['PurchaseOrderProduct']['bool_received']){
						return PRODUCT_STATUS_AWAITING_PRODUCTION;
					}
					else {
						return PRODUCT_STATUS_AWAITING_RECEPTION;
					}
				}
				elseif (!empty($salesOrderProduct['ProductionOrderProduct'])){
					return PRODUCT_STATUS_AWAITING_PURCHASE;
				}
				elseif (!empty($salesOrderProduct['SalesOrder'])){
					if ($salesOrderProduct['SalesOrder']['bool_authorized']){
						if ($salesOrderProduct['SalesOrderProduct']['bool_no_production']){
							return PRODUCT_STATUS_READY_FOR_DELIVERY;
						}
						else {
							return PRODUCT_STATUS_AUTHORIZED;
						}
					}
					else {
						return PRODUCT_STATUS_REGISTERED;
					}
				}
			}
		}
		else {
			// ACTUALLY THIS SHOULD RETURN 0 FOR AN ERROR	
			return PRODUCT_STATUS_REGISTERED;
		}
	}
	
	public function obsolete_splitSalesOrderProduct($salesOrderProductId, $newSalesOrderProductStatusId,$newProductQuantity){
		$productionOrderProductModel=ClassRegistry::init('ProductionOrderProduct');
		$productionOrderProductOperationLocationModel=ClassRegistry::init('ProductionOrderProductOperationLocation');
		$productionOrderProductDepartmentModel=ClassRegistry::init('ProductionOrderProductDepartment');
		$purchaseOrderProductModel=ClassRegistry::init('PurchaseOrderProduct');
		$productionProcessProductModel=ClassRegistry::init('ProductionProcessProduct');
		$productionProcessProductOperationLocationModel=ClassRegistry::init('ProductionProcessProductOperationLocation');
		$salesOrderModel=ClassRegistry::init('SalesOrder');
		$quotationModel=ClassRegistry::init('Quotation');
		
		$originalSalesOrderProduct=$this->find('first',[
			'conditions'=>[
				'SalesOrderProduct.id'=>$salesOrderProductId,
			],
			'contain'=>[
				'ProductionOrderProduct',
				'PurchaseOrderProduct',
				'ProductionProcessProduct',
			],
		]);
		
		$productUnitPrice=$originalSalesOrderProduct['SalesOrderProduct']['product_unit_price'];
		$originalProductQuantity=$originalSalesOrderProduct['SalesOrderProduct']['product_quantity'];
		//echo "before starting the splitting";
		$datasource=$this->getDataSource();
		$datasource->begin();
		try {
			//echo "datasource started";
			// FIRST UPDATE THE PRODUCT WITH THE NEW STATUS AND THE QUANTITY
			$this->id=$salesOrderProductId;
			$salesOrderProductArray=[
        'SalesOrderProduct'=>[
          'id'=>$salesOrderProductId,
          'product_quantity'=>$newProductQuantity,
          'product_total_price'=>$newProductQuantity*$productUnitPrice,
          'sales_order_product_status_id'=>$newSalesOrderProductStatusId,
        ],
      ];
			//pr($salesOrderProductArray);
			if (!$this->save($salesOrderProductArray)){
				pr($this->validateErrors($this));
				echo "Problema separando los productos entregados de los pendientes en la orden de venta";
				throw new Exception();
			}
			//echo "the updated record has id ".($this->id)."<br/>";
			
			// THEN CREATE A NEW SALES ORDER PRODUCT WITH WHAT REMAINS OF THE ORIGINAL PRODUCT
			$this->create();
			$salesOrderProductArray=[
        'SalesOrderProduct'=>[
          'sales_order_id'
          =>$originalSalesOrderProduct['SalesOrderProduct']['sales_order_id'],
          'product_id'=>$originalSalesOrderProduc,['SalesOrderProduct']['product_id'],
          'product_description'=>$originalSalesOrderProduct['SalesOrderProduct']['product_description'],
          'product_unit_price'=>$originalSalesOrderProduct['SalesOrderProduct']['product_unit_price'],
          'product_quantity'=>$originalProductQuantity-$newProductQuantity,
          'product_total_price'=>($originalProductQuantity-$newProductQuantity)*$productUnitPrice,
          'currency_id'=>$originalSalesOrderProduct['SalesOrderProduct']['currency_id'],
          'bool_iva'=>$originalSalesOrderProduct['SalesOrderProduct']['bool_iva'],
          'sales_order_product_status_id'=>$originalSalesOrderProduct['SalesOrderProduct']['sales_order_product_status_id'],
          'bool_no_production'=>$originalSalesOrderProduct['SalesOrderProduct']['bool_no_production'],
        ],
      ];  
			//pr($salesOrderProductArray),
			if (!$this->save($salesOrderProductArray)){
				pr($this->validateErrors($this));
				echo "Problema separando los productos de la orden de venta";
				throw new Exception();
			}
			$newSalesOrderProductId=$this->id;
			//echo "the new record has id ".($this->id)."<br/>";
			
			if (!empty($originalSalesOrderProduct['ProductionOrderProduct'])){
				$originalProductionOrderProduct=$productionOrderProductModel->find('first',[
					'conditions'=>[
						'ProductionOrderProduct.id'=>$originalSalesOrderProduct['ProductionOrderProduct'][0]['id'],
					],
					'contain'=>[
						'ProductionOrderProductOperationLocation',
						'ProductionOrderProductDepartment',
					],
				]);
				if (!empty($originalProductionOrderProduct)){
					$productionOrderProductModel->id=$originalSalesOrderProduct['ProductionOrderProduct'][0]['id'];
					$productionOrderProductArray=[
            'ProductionOrderProduct'=>[
              'id'=>$originalSalesOrderProduct['ProductionOrderProduct'][0]['id'],
              'product_quantity'=>$newProductQuantity,
            ],
          ];
					//pr($productionOrderProductArray);
					if (!$productionOrderProductModel->save($productionOrderProductArray)){
						pr($productionOrderProductModel->validateErrors($this));
						echo "Problema separando los productos de la orden de producción";
						throw new Exception();
					}
						
					// THEN CREATE A NEW PRODUCTION ORDER PRODUCT WITH WHAT REMAINS OF THE ORIGINAL PRODUCT
					$productionOrderProductModel->create();
					$productionOrderProductArray=[
            'ProductionOrderProduct'=>[
              'production_order_id'=>$originalProductionOrderProduct['ProductionOrderProduct']['production_order_id'],
              'product_id'=>$originalProductionOrderProduct['ProductionOrderProduct']['product_id'],
              'product_description'=>$originalProductionOrderProduct['ProductionOrderProduct']['product_description'],
              'product_instruction'=>$originalProductionOrderProduct['ProductionOrderProduct']['product_instruction'],
              'product_quantity'=>$originalProductionOrderProduct['ProductionOrderProduct']['product_quantity']-$newProductQuantity,
              'sales_order_product_id'=>$newSalesOrderProductId,
            ],
          ];
					//pr($salesOrderProductArray);
					if (!$productionOrderProductModel->save($productionOrderProductArray)){
						pr($productionOrderProductModel->validateErrors($this));
						echo "Problema separando los productos de la orden de producción";
						throw new Exception();
					}		
					$newProductionOrderProductId=$productionOrderProductModel->id;
					
					if (!empty($originalProductionOrderProduct['ProductionOrderProductOperationLocation'])){
						foreach($originalProductionOrderProduct['ProductionOrderProductOperationLocation'] as $orderProductOperationLocation){
							// THEN CREATE A NEW PRODUCTION ORDER PRODUCT WITH WHAT REMAINS OF THE ORIGINAL PRODUCT
							$productionOrderProductOperationLocationModel->create();
							$productionOrderProductOperationLocationArray=[
                'ProductionOrderProductOperationLocation'=>[
                  'production_order_product_id'=>$newProductionOrderProductId,
                  'operation_location_id'=>$orderProductOperationLocation['operation_location_id'],
                ],
              ];
							if (!$productionOrderProductOperationLocationModel->save($productionOrderProductOperationLocationArray)){
								pr($productionOrderProductOperationLocationModel->validateErrors($this));
								echo "Problema separando las ubicaciones de los productos de la orden de producción";
								throw new Exception();
							}
						}
					}
					if (!empty($originalProductionOrderProduct['ProductionOrderProductDepartment'])){
						foreach($originalProductionOrderProduct['ProductionOrderProductDepartment'] as $orderProductDepartment){
							// THEN CREATE A NEW PRODUCTION ORDER PRODUCT WITH WHAT REMAINS OF THE ORIGINAL PRODUCT
							$productionOrderProductDepartmentModel->create();
							$productionOrderProductDepartmentArray=[
                'ProductionOrderProductDepartment'=>[
                  'production_order_product_id'=>$newProductionOrderProductId,
                  'department_id'=>$orderProductDepartment['department_id'],
                  'rank'=>$orderProductDepartment['rank'],
                ],
              ];  
							if (!$productionOrderProductDepartmentModel->save($productionOrderProductDepartmentArray)){
								pr($productionOrderProductDepartmentModel->validateErrors($this));
								echo "Problema separando los departamentos de los productos de la orden de producción";
								throw new Exception();
							}
						}
					}
				}
			}
			
			if (!empty($originalSalesOrderProduct['PurchaseOrderProduct'])){
				$originalPurchaseOrderProduct=$purchaseOrderProductModel->find('first',[
					'conditions'=>[
						'PurchaseOrderProduct.id'=>$originalSalesOrderProduct['PurchaseOrderProduct'][0]['id'],
					],
				]);
				if (!empty($originalPurchaseOrderProduct)){
					$purchaseOrderProductModel->id=$originalSalesOrderProduct['PurchaseOrderProduct'][0]['id'];
					$purchaseOrderProductArray=[  
            'PurchaseOrderProduct'=>[
              'id'=>$originalSalesOrderProduct['PurchaseOrderProduct'][0]['id'],
              'product_quantity'=>$newProductQuantity,
              'product_total_cost'=>$newProductQuantity*$originalPurchaseOrderProduct['PurchaseOrderProduct']['product_unit_cost'],
            ],
          ];  
					//pr($purchaseOrderProductArray);
					if (!$purchaseOrderProductModel->save($purchaseOrderProductArray)){
						pr($purchaseOrderProductModel->validateErrors($this));
						echo "Problema separando los productos de la orden de producción";
						throw new Exception();
					}
						
					// THEN CREATE A NEW PRODUCTION ORDER PRODUCT WITH WHAT REMAINS OF THE ORIGINAL PRODUCT
					$purchaseOrderProductModel->create();
					$purchaseOrderProductArray=[
            'PurchaseOrderProduct'=>[
              'purchase_order_id'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['purchase_order_id'],
              'product_id'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['product_id'],
              'production_order_product_id'=>$newProductionOrderProductId,
              'product_description'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['product_description'],
              'product_unit_cost'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['product_unit_cost'],
              'product_quantity'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['product_quantity']-$newProductQuantity,
              'product_total_cost'=>($originalPurchaseOrderProduct['PurchaseOrderProduct']['product_quantity']-$newProductQuantity)*$originalPurchaseOrderProduct['PurchaseOrderProduct']['product_unit_cost'],
              'currency_id'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['currency_id'],
              'bool_received'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['bool_received'],
              'date_received'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['date_received'],
              'production_order_id'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['production_order_id'],
              'department_id'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['department_id'],
              'bool_processed'=>$originalPurchaseOrderProduct['PurchaseOrderProduct']['bool_processed'],
              'sales_order_product_id'=>$newSalesOrderProductId,
            ],
          ];  
					//pr($salesOrderProductArray);
					if (!$purchaseOrderProductModel->save($purchaseOrderProductArray)){
						pr($purchaseOrderProductModel->validateErrors($this));
						echo "Problema separando los productos de la orden de producción";
						throw new Exception();
					}				
				}
			}

			if (!empty($originalSalesOrderProduct['ProductionProcessProduct'])){
				$originalProductionProcessProduct=$productionProcessProductModel->find('first',[
					'conditions'=>[
						'ProductionProcessProduct.id'=>$originalSalesOrderProduct['ProductionProcessProduct'][0]['id'],
					],
					'contain'=>[
						'ProductionProcessProductOperationLocation',
					],
				]);
				if (!empty($originalProductionProcessProduct)){
					$productionProcessProductModel->id=$originalSalesOrderProduct['ProductionProcessProduct'][0]['id'];
					$productionProcessProductArray=[
            'id'=>$originalSalesOrderProduct['ProductionProcessProduct'][0]['id'],
            'product_quantity'=>$newProductQuantity,
          ];  
					//pr($productionProcessProductArray);
					if (!$productionProcessProductModel->save($productionProcessProductArray)){
						pr($productionProcessProductModel->validateErrors($this));
						echo "Problema separando los productos del proceso de producción";
						throw new Exception();
					}
						
					// THEN CREATE A NEW PRODUCTION ORDER PRODUCT WITH WHAT REMAINS OF THE ORIGINAL PRODUCT
					$productionProcessProductModel->create();
					$productionProcessProductArray=[  
            'ProductionProcessProduct'=>[
              'production_process_id'=>$originalProductionProcessProduct['ProductionProcessProduct']['production_process_id'],
              'product_id'=>$originalProductionProcessProduct['ProductionProcessProduct']['product_id'],
              'product_description'=>$originalProductionProcessProduct['ProductionProcessProduct']['product_description'],
              'product_quantity'=>$originalProductionProcessProduct['ProductionProcessProduct']['product_quantity']-$newProductQuantity,
              'operator_id'=>$originalProductionProcessProduct['ProductionProcessProduct']['operator_id'],
              'machine_id'=>$originalProductionProcessProduct['ProductionProcessProduct']['machine_id'],
              'sales_order_id'=>$originalProductionProcessProduct['ProductionProcessProduct']['sales_order_id'],
              'sales_order_product_id'=>$newSalesOrderProductId,
            ],
          ];
					//pr($productionProcessProductArray);
					if (!$productionProcessProductModel->save($productionProcessProductArray)){
						pr($productionProcessProductModel->validateErrors($this));
						echo "Problema separando los productos del proceso de producción";
						throw new Exception();
					}
					
					$newProductionProcessProductId=$productionProcessProductModel->id;

					if (!empty($originalProductionProcessProduct['ProductionProcessProductOperationLocation'])){
						foreach($originalProductionProcessProduct['ProductionProcessProductOperationLocation'] as $processProductOperationLocation){
							// THEN CREATE A NEW PRODUCTION ORDER PRODUCT WITH WHAT REMAINS OF THE ORIGINAL PRODUCT
							$productionProcessProductOperationLocationModel->create();
							$productionProcessProductOperationLocationArray=[
                'ProductionProcessProductOperationLocation'=>[
                  'operation_location_id'=>$processProductOperationLocation['operation_location_id'],
                  'production_process_product_id'=>$newProductionProcessProductId,
                ],
              ];
							if (!$productionProcessProductOperationLocationModel->save($productionProcessProductOperationLocationArray)){
								pr($productionProcessProductOperationLocationModel->validateErrors($this));
								echo "Problema separando las ubicaciones de los productos del proceso de producción";
								throw new Exception();
							}
						}
					}
				}
			}
		
			//TIME FOR A FINAL CHECK
			$salesOrder=$salesOrderModel->find('first',[
				'fields'=>['SalesOrder.id','SalesOrder.sales_order_code','SalesOrder.quotation_id'],
				'conditions'=>[
					'SalesOrder.id'=>$originalSalesOrderProduct['SalesOrderProduct']['sales_order_id'],
				],
				'contain'=>[
					'SalesOrderProduct'=>[
						'fields'=>['SalesOrderProduct.product_quantity'],
					],
				],
			]);			
			$quotation=$quotationModel->find('first',[
				'fields'=>['Quotation.id','Quotation.quotation_code'],
				'conditions'=>[
					'Quotation.id'=>$salesOrder['SalesOrder']['quotation_id'],
				],
				'contain'=>[
					'QuotationProduct'=>[
						'fields'=>['QuotationProduct.product_quantity'],
					],
				],
			]);
			$totalQuantityProductsInSalesOrder=0;
			$totalQuantityProductsInQuotation=0;
			foreach ($salesOrder['SalesOrderProduct'] as $salesOrderProduct){
				$totalQuantityProductsInSalesOrder+=$salesOrderProduct['product_quantity'];
			}
			foreach ($quotation['QuotationProduct'] as $quotationProduct){
				$totalQuantityProductsInQuotation+=$quotationProduct['product_quantity'];
			}
			if ($totalQuantityProductsInSalesOrder!=$totalQuantityProductsInQuotation){
			}
      $datasource->commit();
			return true;
			
		} 
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			//$this->Session->setFlash(__('No se podía dividir el producto de la orden de venta.'], 'default',['class' => 'error-message'));
			return false;
		}
	}

	public $validate = [
		'sales_order_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'product_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
		'sales_order_product_status_id' => [
			'numeric' => [
				'rule' => ['numeric'],
			],
		],
	];

	public $belongsTo = [
		'SalesOrder' => [
			'className' => 'SalesOrder',
			'foreignKey' => 'sales_order_id',
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
		'SalesOrderProductStatus' => [
			'className' => 'SalesOrderProductStatus',
			'foreignKey' => 'sales_order_product_status_id',
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
		'ProductionOrderProduct' => [
			'className' => 'ProductionOrderProduct',
			'foreignKey' => 'sales_order_product_id',
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
			'foreignKey' => 'sales_order_product_id',
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
			'foreignKey' => 'sales_order_product_id',
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
		'InvoiceProduct' => [
			'className' => 'InvoiceProduct',
			'foreignKey' => 'sales_order_product_id',
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
