<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class EntriesController extends AppController {
	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function index() {
		$this->Entry->recursive = -1;
		
		$inventory_product_id=0;
		$currency_id=CURRENCY_USD;
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			//pr($startDateArray);
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$inventory_product_id=$this->request->data['Report']['inventory_product_id'];
			$currency_id=$this->request->data['Report']['currency_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('inventory_product_id','currency_id'));
		
		$conditions=array(	
			'Entry.entry_date >='=>$startDate,
			'Entry.entry_date <'=>$endDatePlusOne,
		);
		$inventoryProductConditions=array();
		if ($inventory_product_id>0){
			$this->loadModel('StockMovement');
			$entryIds=$this->StockMovement->find('list',array(
				'fields'=>array('StockMovement.entry_id'),
				'conditions'=>array(
					'StockMovement.movement_date >='=>$startDate,
					'StockMovement.movement_date <'=>$endDatePlusOne,
					'StockMovement.bool_input'=>true,
					'StockMovement.inventory_product_id'=>$inventory_product_id,
					'StockMovement.product_quantity >'=>0,
				),
			));
			
			$conditions=array(	
				'Entry.id'=>$entryIds,
			);
		}
		
		$entryCount=$this->Entry->find('count', array(
			'fields'=>array('Entry.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' =>$conditions, 
			'contain'=>array(
				'Currency',
				'InventoryProvider',
			),
			'order'=>'Entry.entry_date DESC, Entry.entry_code DESC',
			'limit'=>($entryCount!=0?$entryCount:1),
		);

		$entries = $this->Paginator->paginate('Entry');
		$this->loadModel('StockMovement');
		for ($e=0;$e<count($entries);$e++){
			// update exchange rates
			$entryDate=$entries[$e]['Entry']['entry_date'];
			$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($entryDate);
			$entryExchangeRate=$exchangeRate['ExchangeRate']['rate'];
			$entries[$e]['Entry']['exchange_rate']=$entryExchangeRate;
			
			// update editable status			
			$boolEditable=true;
			$stockItemIds=$this->StockMovement->find('list',array(
				'fields'=>array('StockMovement.stock_item_id'),
				'conditions'=>array(
					'StockMovement.entry_id'=>$entries[$e]['Entry']['id'],
					'StockMovement.product_quantity >'=>'0',
				),
			));
			$remissionIds=array();
			if (!empty($stockItemIds)){
				$remissionIds=$this->StockMovement->find('all',array(
					'fields'=>array('StockMovement.remission_id'),
					'conditions'=>array(
						'StockMovement.stock_item_id'=>$stockItemIds,
						'StockMovement.bool_input'=>false,
						'StockMovement.product_quantity >'=>'0',
					),
				));
			}
			if (!empty($remissionIds)){
				$boolEditable=false;
			}
			$entries[$e]['Entry']['bool_editable']=$boolEditable;
		}
		$this->set(compact('entries'));
		
		$this->loadModel('InventoryProduct');
		$inventoryProducts=$this->InventoryProduct->find('list');
		$this->set(compact('inventoryProducts'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$aco_name="InventoryProviders/index";		
		$bool_inventoryprovider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_index_permission'));
		$aco_name="InventoryProviders/add";		
		$bool_inventoryprovider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_add_permission'));
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumenEntradas'];
		$this->set(compact('exportData'));
	}
	
	public function view($id = null) {
		if (!$this->Entry->exists($id)) {
			throw new NotFoundException(__('Invalid entry'));
		}
		
		$this->Entry->recursive=-1;
		$entry=$this->Entry->find('first', array(
			'conditions'=>array(
				'Entry.id' => $id,
			),
			'contain'=>array(
				'Currency',
				'InventoryProvider',
				'StockMovement'=>array(
					'Currency',
					'InventoryProduct',
					'MeasuringUnit',
				),
			),
		));
		$this->set(compact('entry'));
		
		$this->loadModel('StockMovement');
		$boolEditable=true;
		$stockItemIds=$this->StockMovement->find('list',array(
			'fields'=>array('StockMovement.stock_item_id'),
			'conditions'=>array(
				'StockMovement.entry_id'=>$id,
				'StockMovement.product_quantity >'=>'0',
			),
		));
		$remissionIds=array();
		if (!empty($stockItemIds)){
			$remissionIds=$this->StockMovement->find('all',array(
				'fields'=>array('StockMovement.remission_id'),
				'conditions'=>array(
					'StockMovement.stock_item_id'=>$stockItemIds,
					'StockMovement.bool_input'=>false,
					'StockMovement.product_quantity >'=>'0',
				),
			));
		}
		if (!empty($remissionIds)){
			$boolEditable=false;
		}
		$this->set(compact('boolEditable'));
		
		$filename='Entrada_'.$entry['Entry']['entry_code'];
		$this->set(compact('filename'));
		
		$aco_name="InventoryProviders/index";		
		$bool_inventoryprovider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_index_permission'));
		$aco_name="InventoryProviders/add";		
		$bool_inventoryprovider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_add_permission'));
	}
	
	public function viewPdf($id = null) {
		if (!$this->Entry->exists($id)) {
			throw new NotFoundException(__('Invalid entry'));
		}
		
		$this->Entry->recursive=-1;
		$entry=$this->Entry->find('first', array(
			'conditions'=>array(
				'Entry.id' => $id,
			),
			'contain'=>array(
				'Currency',
				'InventoryProvider',
				'StockMovement'=>array(
					'Currency',
					'InventoryProduct',
					
				),
			),
		));
		$this->set(compact('entry'));
		
		$filename='Entrada_'.$entry['Entry']['entry_code'];
		$this->set(compact('filename'));
	}

	public function add() {
		$numberOfProducts=0;
		$entryDate=date('Y-m-d');
		if ($this->request->is('post')) {
			foreach($this->request->data['StockMovement'] as $stockMovement){
				if ($stockMovement['inventory_product_id']){
					$numberOfProducts++;
				}
			}
			
			$entryDateArray=$this->request->data['Entry']['entry_date'];
			//pr($entryDateArray);
			$entryDateString=$entryDateArray['year'].'-'.$entryDateArray['month'].'-'.$entryDateArray['day'];
			$entryDate=date( "Y-m-d", strtotime($entryDateString));
			
			$previousEntriesWithThisCode=array();
			$previousEntriesWithThisCode=$this->Entry->find('all',array(
				'conditions'=>array(
					'Entry.entry_code'=>$this->request->data['Entry']['entry_code'],
					'Entry.inventory_provider_id'=>$this->request->data['Entry']['inventory_provider_id'],
				),
			));
			
			if ($entryDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de entrada no puede estar en el futuro!  No se guardó la entrada.'), 'default',array('class' => 'error-message'));
			}
			if (count($previousEntriesWithThisCode)>0){
				$this->Session->setFlash(__('Ya se registró una entrada con este código para este proveedor!  No se guardó la entrada.'), 'default',array('class' => 'error-message'));
			}
			elseif (empty($this->request->data['Entry']['inventory_provider_id'])){
				$this->Session->setFlash(__('Se debe seleccionar el proveedor para la entrada!  No se guardó la entrada.'), 'default',array('class' => 'error-message'));
			}
			else {
				$datasource=$this->Entry->getDataSource();
				$datasource->begin();	
				if ($this->request->data['Entry']['bool_annulled']){
					try {
						//pr($this->request->data);
						//echo "before creating the annulled entry<br/>";
						$this->Entry->create();
						if (!$this->Entry->save($this->request->data)) {
							echo "Problema al guardar la entrada";
							pr($this->validateErrors($this->Entry));
							throw new Exception();
						}
						//echo "after having created the annulled entry".$this->Entry->id."<br/>";
						
						$datasource->commit();
						
						$this->recordUserAction($this->Entry->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se registró la entrada anulada número ".$this->request->data['Entry']['entry_code']);
						
						$this->Session->setFlash(__('The entry has been saved.'),'default',array('class' => 'success'));				
						return $this->redirect(array('action' => 'index'));
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('The entry could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
					}
				}
				else {
					$this->loadModel('InventoryProduct');
					$this->loadModel('InventoryProvider');
					$this->loadModel('StockItem');
					$this->loadModel('StockMovement');
					$this->loadModel('StockItemLog');
					try {
						//pr($this->request->data);
						$this->Entry->create();
						if (!$this->Entry->save($this->request->data)) {
							echo "Problema al guardar la entrada";
							pr($this->validateErrors($this->Entry));
							throw new Exception();
						}
						$entryId=$this->Entry->id;
						
						$inventoryProvider=$this->InventoryProvider->find('first',array(
							'conditions'=>array(
								'InventoryProvider.id'=>$this->request->data['Entry']['inventory_provider_id'],
							),
						));
						//pr($inventoryProvider);
						$inventoryProviderName=$inventoryProvider['InventoryProvider']['name'];
						
						foreach ($this->request->data['StockMovement'] as $stockMovement){
							if ($stockMovement['inventory_product_id']>0 && $stockMovement['product_quantity']>0){
								$linkedProduct=$this->InventoryProduct->find('first',array(
									'conditions'=>array(
										'InventoryProduct.id'=>$stockMovement['inventory_product_id'],
									),
								));
								$inventoryProductName=$linkedProduct['InventoryProduct']['name'];
								$itemName=$entryDateArray['day']."_".$entryDateArray['month']."_".$entryDateArray['year']."_".$inventoryProviderName."_".$this->request->data['Entry']['entry_code']."_".$inventoryProductName;
								$description="Nuevo lote ".$inventoryProductName." (Cantidad:".$stockMovement['product_quantity'].",Costo Unidad:".$stockMovement['product_unit_cost'].") de Entrada ".$inventoryProviderName."_".$this->request->data['Entry']['entry_code'];
								
							
								$stockItemData=array();
								$stockItemData['StockItem']['name']=$itemName;
								$StockItemData['StockItem']['description']=$description;
								$stockItemData['StockItem']['inventory_product_id']=$stockMovement['inventory_product_id'];
								$stockItemData['StockItem']['product_unit_cost']=$stockMovement['product_unit_cost'];
								$stockItemData['StockItem']['product_original_quantity']=$stockMovement['product_quantity'];
								$stockItemData['StockItem']['product_remaining_quantity']=$stockMovement['product_quantity'];
								$stockItemData['StockItem']['measuring_unit_id']=$linkedProduct['InventoryProduct']['measuring_unit_id'];
								$stockItemData['StockItem']['stock_item_creation_date']=$entryDate;
								$this->StockItem->create();
								$logsuccess=$this->StockItem->save($stockItemData);
								if (!$logsuccess) {
									echo "problema guardando el lote";
									pr($this->validateErrors($this->StockItem));
									throw new Exception();
								}
								$stockItemId=$this->StockItem->id;
							
								$stockMovementData=array();
								$stockMovementData['StockMovement']['movement_date']=$entryDate;
								$stockMovementData['StockMovement']['bool_input']=true;
								$stockMovementData['StockMovement']['entry_id']=$entryId;
								$stockMovementData['StockMovement']['remission_id']=0;
								$stockMovementData['StockMovement']['stock_item_id']=$stockItemId;
								$stockMovementData['StockMovement']['inventory_product_id']=$stockMovement['inventory_product_id'];
								$stockMovementData['StockMovement']['product_quantity']=$stockMovement['product_quantity'];
								$stockMovementData['StockMovement']['measuring_unit_id']=$linkedProduct['InventoryProduct']['measuring_unit_id'];
								$stockMovementData['StockMovement']['product_unit_cost']=$stockMovement['product_unit_cost'];
								$stockMovementData['StockMovement']['currency_id']=$this->request->data['Entry']['currency_id'];
								$this->StockMovement->create();
								if (!$this->StockMovement->save($stockMovementData)) {
									echo "Problema al guardar el movimiento de bodega";
									pr($this->validateErrors($this->StockMovement));
									throw new Exception();
								}
								$stockMovementId=$this->StockMovement->id;
								
								$stockItemLogData=array();
								$stockItemLogData['StockItemLog']['stock_item_id']=$stockItemId;
								$stockItemLogData['StockItemLog']['stock_item_date']=$entryDate;
								$stockItemLogData['StockItemLog']['inventory_product_id']=$stockMovement['inventory_product_id'];
								$stockItemLogData['StockItemLog']['product_unit_cost']=$stockMovement['product_unit_cost'];
								$stockItemLogData['StockItemLog']['currency_id']=$this->request->data['Entry']['currency_id'];
								$stockItemLogData['StockItemLog']['product_quantity']=$stockMovement['product_quantity'];
								$stockItemLogData['StockItemLog']['measuring_unit_id']=$linkedProduct['InventoryProduct']['measuring_unit_id'];
								$stockItemLogData['StockItemLog']['stock_movement_id']=$stockMovementId;
								$this->StockItemLog->create();
								$logsuccess=$this->StockItemLog->save($stockItemLogData);
								if (!$logsuccess) {
									echo "problema guardando el estado de lote";
									pr($this->validateErrors($this->StockItemLog));
									throw new Exception();
								}
							}
						}
						
						$datasource->commit();
						$this->recordUserAction($this->Entry->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se registró la entrada número ".$this->request->data['Entry']['entry_code']);
						
						$this->Session->setFlash(__('The entry has been saved.'),'default',array('class' => 'success'));				
						return $this->redirect(array('action' => 'index'));
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('The entry could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
					}
				}
			}
		}
		$this->set(compact('numberOfProducts'));
		
		$inventoryProviders = $this->Entry->InventoryProvider->find('list');
		$currencies = $this->Entry->Currency->find('list');
		$this->set(compact('inventoryProviders', 'currencies'));
		
		$this->loadModel('InventoryProduct');
		$inventoryProducts=$this->InventoryProduct->find('list',array(
			'conditions'=>array(
				//'InventoryProduct.id >'=>179,
			),
			'order'=>'InventoryProduct.name ASC',
			
		));
		$this->set(compact('inventoryProducts'));
		
		$this->loadModel('ExchangeRate');
		$entryExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($entryDate);
		$exchangeRateEntry=$entryExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateEntry'));
		
		$aco_name="InventoryProviders/index";		
		$bool_inventoryprovider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_index_permission'));
		$aco_name="InventoryProviders/add";		
		$bool_inventoryprovider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->Entry->exists($id)) {
			throw new NotFoundException(__('Invalid entry'));
		}
		
		if ($this->request->is(array('post', 'put'))) {
			$entryDateArray=$this->request->data['Entry']['entry_date'];
			//pr($entryDateArray);
			$entryDateString=$entryDateArray['year'].'-'.$entryDateArray['month'].'-'.$entryDateArray['day'];
			$entryDate=date( "Y-m-d", strtotime($entryDateString));
			
			$previousEntriesWithThisCode=array();
			$previousEntriesWithThisCode=$this->Entry->find('all',array(
				'conditions'=>array(
					'Entry.entry_code'=>$this->request->data['Entry']['entry_code'],
					'Entry.inventory_provider_id'=>$this->request->data['Entry']['inventory_provider_id'],
					'Entry.id !='=>$id,
				),
			));
			//pr($previousEntriesWithThisCode);
			
			if ($entryDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de entrada no puede estar en el futuro!  No se guardó la entrada.'), 'default',array('class' => 'error-message'));
			}
			if (count($previousEntriesWithThisCode)>0){
				$this->Session->setFlash(__('Ya se registró una entrada con este código para este proveedor!  No se guardó la entrada.'), 'default',array('class' => 'error-message'));
			}
			elseif (empty($this->request->data['Entry']['inventory_provider_id'])){
				$this->Session->setFlash(__('Se debe seleccionar el proveedor para la entrada!  No se guardó la entrada.'), 'default',array('class' => 'error-message'));
			}
			else {
				$this->loadModel('StockMovement');
				$this->StockMovement->recursive=-1;

				$stockMovements=$this->StockMovement->find('all',array(
					'conditions'=>array('StockMovement.entry_id'=>$id),
					'contain'=>array(
						'StockItem'=>array(
							'StockItemLog',
						),
					),
				));
				//pr($stockMovements);
				$datasource=$this->Entry->getDataSource();
				$datasource->begin();	
				if ($this->request->data['Entry']['bool_annulled']){
					try {
						//pr($this->request->data);
						//delete all stockMovements, stockItems and stockItemLogs
						foreach ($stockMovements as $stockMovement){
							if (!$this->StockMovement->delete($stockMovement['StockMovement']['id'])) {
								echo "Problema al eliminar el movimiento de entrada en bodega";
								pr($this->validateErrors($this->StockMovement));
								throw new Exception();
							}
							
							if (!empty($stockMovement['StockItem']['StockItemLog'])){
								foreach ($stockMovement['StockItem']['StockItemLog'] as $stockItemLog){
									if (!$this->StockItemLog->delete($stockItemLog['id'])) {
										echo "Problema al eliminar el estado de lote";
										pr($this->validateErrors($this->StockItemLog));
										throw new Exception();
									}
								}
							}
							
							if (!empty($stockMovement['StockItem']['id'])){
								if (!$this->StockItem->delete($stockMovement['StockItem']['id'])) {
									echo "Problema al eliminar el lote de bodega";
									pr($this->validateErrors($this->StockItem));
									throw new Exception();
								}
							}
						}
						
						//echo "before creating the annulled entry<br/>";
						$this->Entry->id=$id;
						if (!$this->Entry->save($this->request->data)) {
							echo "Problema al guardar la entrada";
							pr($this->validateErrors($this->Entry));
							throw new Exception();
						}
						
						//echo "after having created the annulled entry".$this->Entry->id."<br/>";
						
						$datasource->commit();
						
						$this->recordUserAction($this->Entry->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se editó la entrada anulada número ".$this->request->data['Entry']['entry_code']);
						
						$this->Session->setFlash(__('The entry has been saved.'),'default',array('class' => 'success'));				
						return $this->redirect(array('action' => 'index'));
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('The entry could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
					}
				}
				else {
					$this->loadModel('InventoryProduct');
					$this->loadModel('InventoryProvider');
					$this->loadModel('StockItem');
					$this->loadModel('StockMovement');
					$this->loadModel('StockItemLog');
					
					try {
						//pr($this->request->data);
						foreach ($stockMovements as $stockMovement){
							if (!$this->StockMovement->delete($stockMovement['StockMovement']['id'])) {
								echo "Problema al eliminar el movimiento de entrada en bodega";
								pr($this->validateErrors($this->StockMovement));
								throw new Exception();
							}
							
							if (!empty($stockMovement['StockItem']['StockItemLog'])){
								foreach ($stockMovement['StockItem']['StockItemLog'] as $stockItemLog){
									if (!$this->StockItemLog->delete($stockItemLog['id'])) {
										echo "Problema al eliminar el estado de lote";
										pr($this->validateErrors($this->StockItemLog));
										throw new Exception();
									}
								}
							}
							
							if (!empty($stockMovement['StockItem']['id'])){
								if (!$this->StockItem->delete($stockMovement['StockItem']['id'])) {
									echo "Problema al eliminar el lote de bodega";
									pr($this->validateErrors($this->StockItem));
									throw new Exception();
								}
							}
						}
						
						$this->Entry->id=$id;
						if (!$this->Entry->save($this->request->data)) {
							echo "Problema al guardar la entrada";
							pr($this->validateErrors($this->Entry));
							throw new Exception();
						}
						$entryId=$this->Entry->id;
						
						$inventoryProvider=$this->InventoryProvider->find('first',array(
							'conditions'=>array(
								'InventoryProvider.id'=>$this->request->data['Entry']['inventory_provider_id'],
							),
						));
						//pr($inventoryProvider);
						$inventoryProviderName=$inventoryProvider['InventoryProvider']['name'];
						
						foreach ($this->request->data['StockMovement'] as $stockMovement){
							if ($stockMovement['inventory_product_id']>0 && $stockMovement['product_quantity']>0){
								$linkedProduct=$this->InventoryProduct->find('first',array(
									'conditions'=>array(
										'InventoryProduct.id'=>$stockMovement['inventory_product_id'],
									),
								));
								$inventoryProductName=$linkedProduct['InventoryProduct']['name'];
								$itemName=$entryDateArray['day']."_".$entryDateArray['month']."_".$entryDateArray['year']."_".$inventoryProviderName."_".$this->request->data['Entry']['entry_code']."_".$inventoryProductName;
								$description="Nuevo lote ".$inventoryProductName." (Cantidad:".$stockMovement['product_quantity'].",Costo Unidad:".$stockMovement['product_unit_cost'].") de Entrada ".$inventoryProviderName."_".$this->request->data['Entry']['entry_code'];
							
								$stockItemData=array();
								$stockItemData['StockItem']['name']=$itemName;
								$StockItemData['StockItem']['description']=$description;
								$stockItemData['StockItem']['inventory_product_id']=$stockMovement['inventory_product_id'];
								$stockItemData['StockItem']['product_unit_cost']=$stockMovement['product_unit_cost'];
								$stockItemData['StockItem']['product_original_quantity']=$stockMovement['product_quantity'];
								$stockItemData['StockItem']['product_remaining_quantity']=$stockMovement['product_quantity'];
								$stockItemData['StockItem']['measuring_unit_id']=$linkedProduct['InventoryProduct']['measuring_unit_id'];
								$stockItemData['StockItem']['stock_item_creation_date']=$entryDate;
								$this->StockItem->create();
								$logsuccess=$this->StockItem->save($stockItemData);
								if (!$logsuccess) {
									echo "problema guardando el lote";
									pr($this->validateErrors($this->StockItem));
									throw new Exception();
								}
								$stockItemId=$this->StockItem->id;
							
								$stockMovementData=array();
								$stockMovementData['StockMovement']['movement_date']=$entryDate;
								$stockMovementData['StockMovement']['bool_input']=true;
								$stockMovementData['StockMovement']['entry_id']=$entryId;
								$stockMovementData['StockMovement']['remission_id']=0;
								$stockMovementData['StockMovement']['stock_item_id']=$stockItemId;
								$stockMovementData['StockMovement']['inventory_product_id']=$stockMovement['inventory_product_id'];
								$stockMovementData['StockMovement']['product_quantity']=$stockMovement['product_quantity'];
								$stockMovementData['StockMovement']['measuring_unit_id']=$linkedProduct['InventoryProduct']['measuring_unit_id'];
								$stockMovementData['StockMovement']['product_unit_cost']=$stockMovement['product_unit_cost'];
								$stockMovementData['StockMovement']['currency_id']=$this->request->data['Entry']['currency_id'];
								$this->StockMovement->create();
								if (!$this->StockMovement->save($stockMovementData)) {
									echo "Problema al guardar el movimiento de bodega";
									pr($this->validateErrors($this->StockMovement));
									throw new Exception();
								}
								$stockMovementId=$this->StockMovement->id;
								
								$stockItemLogData=array();
								$stockItemLogData['StockItemLog']['stock_item_id']=$stockItemId;
								$stockItemLogData['StockItemLog']['stock_item_date']=$entryDate;
								$stockItemLogData['StockItemLog']['inventory_product_id']=$stockMovement['inventory_product_id'];
								$stockItemLogData['StockItemLog']['product_unit_cost']=$stockMovement['product_unit_cost'];
								$stockItemLogData['StockItemLog']['currency_id']=$this->request->data['Entry']['currency_id'];
								$stockItemLogData['StockItemLog']['product_quantity']=$stockMovement['product_quantity'];
								$stockItemLogData['StockItemLog']['measuring_unit_id']=$linkedProduct['InventoryProduct']['measuring_unit_id'];
								$stockItemLogData['StockItemLog']['stock_movement_id']=$stockMovementId;
								$this->StockItemLog->create();
								$logsuccess=$this->StockItemLog->save($stockItemLogData);
								if (!$logsuccess) {
									echo "problema guardando el estado de lote";
									pr($this->validateErrors($this->StockItemLog));
									throw new Exception();
								}
							}
						}
						
						$datasource->commit();
						$this->recordUserAction($this->Entry->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se registró la entrada número ".$this->request->data['Entry']['entry_code']);
						
						$this->Session->setFlash(__('The entry has been saved.'),'default',array('class' => 'success'));				
						return $this->redirect(array('action' => 'index'));
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('The entry could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
					}
				}
			}
		}
	
		else {
			$options = array('conditions' => array('Entry.id'=> $id));
			$this->request->data = $this->Entry->find('first', $options);
		}
		$numberOfProducts=0;
		foreach($this->request->data['StockMovement'] as $stockMovement){
			if ($stockMovement['inventory_product_id']){
				$numberOfProducts++;
			}
		}
		$this->set(compact('numberOfProducts'));
		
		$inventoryProviders = $this->Entry->InventoryProvider->find('list');
		$currencies = $this->Entry->Currency->find('list');
		$this->set(compact('inventoryProviders', 'currencies'));
		
		$this->loadModel('InventoryProduct');
		$inventoryProducts=$this->InventoryProduct->find('list');
		$this->set(compact('inventoryProducts'));
		
		$aco_name="InventoryProviders/index";		
		$bool_inventoryprovider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_index_permission'));
		$aco_name="InventoryProviders/add";		
		$bool_inventoryprovider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Entry->id = $id;
		if (!$this->Entry->exists()) {
			throw new NotFoundException(__('Invalid entry'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$this->loadModel('StockMovement');
		$this->loadModel('StockItem');
		$this->loadModel('StockItemLog');
		$this->StockMovement->recursive=-1;

		$this->Entry->recursive=-1;
		$entry=$this->Entry->find('first',array('conditions'=>array('Entry.id'=>$id)));
		$stockMovements=$this->StockMovement->find('all',array(
			'conditions'=>array('StockMovement.entry_id'=>$id),
			'contain'=>array(
				'StockItem'=>array(
					'StockItemLog',
				),
			),
		));
		//pr($stockMovements);
		$datasource=$this->Entry->getDataSource();
		$datasource->begin();
		try {
			//delete all stockMovements, stockItems and stockItemLogs
			foreach ($stockMovements as $stockMovement){
				//pr($stockMovement['StockItem']);
			
				if (!$this->StockMovement->delete($stockMovement['StockMovement']['id'])) {
					echo "Problema al eliminar el movimiento de entrada en bodega";
					pr($this->validateErrors($this->StockMovement));
					throw new Exception();
				}
				
				if (!empty($stockMovement['StockItem']['StockItemLog'])){
					foreach ($stockMovement['StockItem']['StockItemLog'] as $stockItemLog){
						if (!$this->StockItemLog->delete($stockItemLog['id'])) {
							echo "Problema al eliminar el estado de lote";
							pr($this->validateErrors($this->StockItemLog));
							throw new Exception();
						}
					}
				}
				
				if (!empty($stockMovement['StockItem']['id'])){
					if (!$this->StockItem->delete($stockMovement['StockItem']['id'])) {
						echo "Problema al eliminar el lote de bodega";
						pr($this->validateErrors($this->StockItem));
						throw new Exception();
					}
				}
			}
			
			if (!$this->Entry->delete($id)) {
				echo "Problema al eliminar la entrada";
				pr($this->validateErrors($this->Entry));
				throw new Exception();
			}
					
			$datasource->commit();
			
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$entry['Entry']['id'];
			$deletionArray['Deletion']['reference']=$entry['Entry']['entry_code'];
			$deletionArray['Deletion']['type']='Entry';
			$this->Deletion->save($deletionArray);
					
			$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó la entrada número ".$entry['Entry']['entry_code']);
					
			$this->Session->setFlash(__('Se eliminó la entrada.'),'default',array('class' => 'success'));				
			return $this->redirect(array('action' => 'index'));
		}
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			$this->Session->setFlash(__('No se podía eliminar la entrada.'), 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
	}
}
