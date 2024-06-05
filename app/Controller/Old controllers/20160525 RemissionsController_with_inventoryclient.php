<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
/**
 * Remissions Controller
 *
 * @property Remission $Remission
 * @property PaginatorComponent $Paginator
 */
class RemissionsController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');
	
	public function beforeFilter() {
		parent::beforeFilter();
		//$this->Auth->allow('getquotationproducts','getquotationinfo','getnewquotationcode','getquotationcurrencyid','getquotationsforclient','generarOrdenDeVenta');		
	}

	public function index() {
		$this->Remission->recursive = -1;
		
		$inventoryProductId=0;
		$currencyId=CURRENCY_USD;
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			//pr($startDateArray);
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$inventoryProductId=$this->request->data['Report']['inventory_product_id'];
			$currencyId=$this->request->data['Report']['currency_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$_SESSION['currencyId']=$currencyId;
		
		$this->set(compact('startDate','endDate'));
		$this->set(compact('inventoryProductId','currencyId'));
		
		$conditions=array(	
			'Remission.remission_date >='=>$startDate,
			'Remission.remission_date <'=>$endDatePlusOne,
		);
		$inventoryProductConditions=array();
		if ($inventoryProductId>0){
			$this->loadModel('StockMovement');
			$remissionIds=$this->StockMovement->find('list',array(
				'fields'=>array('StockMovement.remission_id'),
				'conditions'=>array(
					'StockMovement.movement_date >='=>$startDate,
					'StockMovement.movement_date <'=>$endDatePlusOne,
					'StockMovement.bool_input'=>false,
					'StockMovement.inventory_product_id'=>$inventoryProductId,
					'StockMovement.product_quantity >'=>0,
				),
			));
			
			$conditions=array(	
				'Remission.id'=>$remissionIds,
			);
		}
		
		$remissionCount=$this->Remission->find('count', array(
			'fields'=>array('Remission.id'),
			'conditions' => $conditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $conditions,
			'contain'=>array(
				'Currency',
				'InventoryClient',
			),
			'order'=>'Remission.remission_date DESC, Remission.remission_code DESC',
			'limit'=>($remissionCount!=0?$remissionCount:1),
		);

		$remissions = $this->Paginator->paginate('Remission');
		$this->loadModel('StockMovement');
		for ($r=0;$r<count($remissions);$r++){
			// update exchange rates
			$remissionDate=$remissions[$r]['Remission']['remission_date'];
			$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($remissionDate);
			$remissionExchangeRate=$exchangeRate['ExchangeRate']['rate'];
			$remissions[$r]['Remission']['exchange_rate']=$remissionExchangeRate;
		}
		$this->set(compact('remissions'));
		//pr($remissions);
		$this->loadModel('InventoryProduct');
		$inventoryProducts=$this->InventoryProduct->find('list');
		$this->set(compact('inventoryProducts'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$aco_name="InventoryClients/index";		
		$bool_inventoryclient_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_index_permission'));
		$aco_name="InventoryClients/add";		
		$bool_inventoryclient_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_add_permission'));
		
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumenEntradas'];
		$this->set(compact('exportData'));
	}
	
	public function view($id = null) {
		if (!$this->Remission->exists($id)) {
			throw new NotFoundException(__('Invalid remission'));
		}
		
		$remission=$this->Remission->find('first',array(
			'conditions' => array(
				'Remission.id' => $id,
			),
			'contain'=>array(
				'Currency',
				'InventoryClient',
				'StockMovement'=>array(
					'InventoryProduct',
					'MeasuringUnit',
				),
			),
		));
		$this->set(compact('remission'));
		
		$filename='Salida_'.$remission['Remission']['remission_code'];
		$this->set(compact('filename'));
		
		$aco_name="InventoryClients/index";		
		$bool_inventoryclient_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_index_permission'));
		$aco_name="InventoryClients/add";		
		$bool_inventoryclient_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_add_permission'));
	}

	public function viewPdf($id = null) {
		if (!$this->Remission->exists($id)) {
			throw new NotFoundException(__('Invalid remission'));
		}
		
		$remission=$this->Remission->find('first',array(
			'conditions' => array(
				'Remission.id' => $id,
			),
			'contain'=>array(
				'Currency',
				'InventoryClient',
				'StockMovement'=>array(
					'InventoryProduct',
					'MeasuringUnit',
				),
			),
		));
		$this->set(compact('remission'));
		
		$filename='Salida_'.$remission['Remission']['remission_code'];
		$this->set(compact('filename'));
	}
	
	public function add() {
		$this->loadModel('InventoryProduct');
		$numberOfProducts=0;
		$remissionDate=date('Y-m-d');
		if ($this->request->is('post')) {
			//pr($this->request->data);
			$boolItemQuantitiesOK=true;
			$itemsExceededMessage="";
			foreach($this->request->data['StockMovement'] as $stockMovement){
				if ($stockMovement['inventory_product_id']){
					$numberOfProducts++;
				}
				if ($stockMovement['product_quantity']>$stockMovement['stock_quantity_value']){
					$boolItemQuantitiesOK=false;
					if (empty($itemsExceededMessage)){
						$itemsExceededMessage.="No se podía guardar porque la cantidad de productos excede la cantidad en bodega para productos";
					}
					$relatedInventoryProduct=$this->InventoryProduct->find('first',array(
						'conditions'=>array(
							'InventoryProduct.id'=>$stockMovement['inventory_product_id'],
						),
					));
					$itemsExceededMessage.=" ".$relatedInventoryProduct['InventoryProduct']['name']." Requerido:".$stockMovement['product_quantity']." (en bodega:".$stockMovement['stock_quantity_value'].")";
				}
			}
			
			$remissionDateArray=$this->request->data['Remission']['remission_date'];
			//pr($entryDateArray);
			$remissionDateString=$remissionDateArray['year'].'-'.$remissionDateArray['month'].'-'.$remissionDateArray['day'];
			$remissionDate=date( "Y-m-d", strtotime($remissionDateString));
			
			$previousRemissionsWithThisCode=array();
			$previousRemissionsWithThisCode=$this->Remission->find('all',array(
				'conditions'=>array(
					'Remission.remission_code'=>$this->request->data['Remission']['remission_code'],
					'Remission.inventory_client_id'=>$this->request->data['Remission']['inventory_client_id'],
				),
			));
						
			if ($remissionDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de salida no puede estar en el futuro!  No se guardó la salida.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolItemQuantitiesOK){
				$itemsExceededMessage.=".  No se guardó la salida.";
				$this->Session->setFlash($itemsExceededMessage, 'default',array('class' => 'error-message'));
			}
			elseif (count($previousRemissionsWithThisCode)>0){
				$this->Session->setFlash(__('Ya se registró una salida con este número para este cliente!  No se guardó la salida.'), 'default',array('class' => 'error-message'));
			}
			elseif (empty($this->request->data['Remission']['inventory_client_id'])){
				$this->Session->setFlash(__('Se debe seleccionar el cliente para la salida!  No se guardó la salida.'), 'default',array('class' => 'error-message'));
			}
			else {
				$this->loadModel('StockItem');
				$this->loadModel('StockItemLog');
				$this->loadModel('StockMovement');
				
				$datasource=$this->Remission->getDataSource();
				if ($this->request->data['Remission']['bool_annulled']){
					try {
						//pr($this->request->data);
						$datasource->begin();	
						
						//echo "before creating the annulled entry<br/>";
						$this->Remission->create();
						if (!$this->Remission->save($this->request->data)) {
							echo "Problema al guardar la salida";
							pr($this->validateErrors($this->Remission));
							throw new Exception();
						}
						//echo "after having created the annulled entry".$this->Entry->id."<br/>";
						
						$datasource->commit();
						
						$this->recordUserAction($this->Remission->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se registró la salida anulada número ".$this->request->data['Remission']['remission_code']);
						
						$this->Session->setFlash(__('The remission has been saved.'),'default',array('class' => 'success'));				
						return $this->redirect(array('action' => 'index'));
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('The remission could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
					}
				}
				else {
					$this->loadModel('InventoryProduct');
					$this->loadModel('InventoryClient');
					$this->loadModel('StockItem');
					$this->loadModel('StockMovement');
					$this->loadModel('StockItemLog');
					
					try {
						//pr($this->request->data);
						$datasource->begin();	
						
						$this->Remission->create();
						if (!$this->Remission->save($this->request->data)) {
							echo "Problema al guardar la salida";
							pr($this->validateErrors($this->Remission));
							throw new Exception();
						}
						$remissionId=$this->Remission->id;
						
						$inventoryClient=$this->InventoryClient->find('first',array(
							'conditions'=>array(
								'InventoryClient.id'=>$this->request->data['Remission']['inventory_client_id'],
							),
						));
						//pr($inventoryClient);
						$inventoryClientName=$inventoryClient['InventoryClient']['name'];
						
						foreach ($this->request->data['StockMovement'] as $stockMovement){
							if ($stockMovement['product_quantity']>0 && $stockMovement['inventory_product_id']>0){								
								$relatedInventoryProduct=$this->InventoryProduct->find('first',array(
									'conditions'=>array(
										'InventoryProduct.id'=>$stockMovement['inventory_product_id'],
									),
								));
								$usedMaterials= $this->StockItem->getMaterialsForRemission($stockMovement['inventory_product_id'],$stockMovement['product_quantity'],$remissionDateString);									
								//pr($usedMaterials);
								for ($k=0;$k<count($usedMaterials);$k++){
									$materialUsed=$usedMaterials[$k];
									$stock_item_id=$materialUsed['id'];
									$quantity_present=$materialUsed['quantity_present'];
									$quantity_used=$materialUsed['quantity_used'];
									$quantity_remaining=$materialUsed['quantity_remaining'];
									if (!$this->StockItem->exists($stock_item_id)) {
										throw new NotFoundException(__('Invalid StockItem'));
									}
									$linkedStockItem=$this->StockItem->read(null,$stock_item_id);
									$message="Se vendió lote ".$relatedInventoryProduct['InventoryProduct']['name']." (Cantidad:".$quantity_used.") para Salida ".$this->request->data['Remission']['remission_code'];
									
									$stockItemData=array();
									$stockItemData['id']=$stock_item_id;
									$stockItemData['description']=$linkedStockItem['StockItem']['description']."|".$message;
									$stockItemData['product_remaining_quantity']=$quantity_remaining;
									$this->StockItem->id=$stock_item_id;
									if (!$this->StockItem->save($stockItemData)) {
										echo "problema al guardar el lote";
										pr($this->validateErrors($this->StockItem));
										throw new Exception();
									}
									
									// STEP 2: SAVE THE STOCK MOVEMENT
									$message="Se remitió ".$relatedInventoryProduct['InventoryProduct']['name']." (Cantidad:".$quantity_used.", para Salida ".$this->request->data['Remission']['remission_code'];
									$stockMovementData=array();
									$stockMovementData['movement_date']=$remissionDateArray;
									$stockMovementData['bool_input']=false;
									$stockMovementData['name']=$remissionDateArray['day'].$remissionDateArray['month'].$remissionDateArray['year']."_".$this->request->data['Remission']['remission_code']."_".$relatedInventoryProduct['InventoryProduct']['name'];
									$stockMovementData['description']=$message;
									$stockMovementData['remission_id']=$remissionId;
									$stockMovementData['stock_item_id']=$stock_item_id;
									$stockMovementData['inventory_product_id']=$stockMovement['inventory_product_id'];
									$stockMovementData['product_quantity']=$quantity_used;
									$stockMovementData['measuring_unit_id']=$relatedInventoryProduct['InventoryProduct']['measuring_unit_id'];
									$stockMovementData['product_unit_cost']=$linkedStockItem['StockItem']['product_unit_cost'];
									$stockMovementData['product_unit_price']=$stockMovement['product_unit_price'];
									$stockMovementData['currency_id']=$this->request->data['Remission']['currency_id'];
									if (!empty($stockMovement['width'])){
										$stockMovementData['width']=$stockMovement['width'];
									}
									if (!empty($stockMovement['height'])){
										$stockMovementData['height']=$stockMovement['height'];
									}
									if (!empty($stockMovement['color'])){
										$stockMovementData['color']=$stockMovement['color'];
									}
									
									$this->StockMovement->create();
									if (!$this->StockMovement->save($stockMovementData)) {
										echo "problema al guardar el movimiento de lote";
										pr($this->validateErrors($this->StockMovement));
										throw new Exception();
									}
								
									// STEP 3: SAVE THE STOCK ITEM LOG
									$this->recreateStockItemLogs($stock_item_id);
											
									// STEP 4: SAVE THE USERLOG FOR THE STOCK MOVEMENT
									$this->recordUserActivity($this->Session->read('User.username'),$message);
								}
							}
						}
						$datasource->commit();
						$this->recordUserAction($this->Remission->id,null,null);
						$this->recordUserActivity($this->Session->read('User.username'),"Se registró la salida número ".$this->request->data['Remission']['remission_code']);
						
						$this->Session->setFlash(__('La salida se guardó.'),'default',array('class' => 'success'));				
						return $this->redirect(array('action' => 'index'));
					}
					catch(Exception $e){
						$datasource->rollback();
						pr($e);
						$this->Session->setFlash(__('No se podía guardar la salida.'), 'default',array('class' => 'error-message'));
					}
					
				}
			}
			
		}
		$this->set(compact('numberOfProducts'));
		
		$inventoryClients = $this->Remission->InventoryClient->find('list');
		$currencies = $this->Remission->Currency->find('list');
		$this->set(compact('inventoryClients', 'currencies'));
		
		$this->loadModel('InventoryProduct');
		$this->InventoryProduct->recursive=-1;
		$allInventoryProducts=$this->InventoryProduct->find('all',array(
			'conditions'=>array(
				'bool_active'=>true,
			),
		));
		
		$this->loadModel('StockItemLog');
		for ($ip=0;$ip<count($allInventoryProducts);$ip++){
			//echo "inventory product id is ".$allInventoryProducts[$ip]['InventoryProduct']['id']."<br/>";
			$quantityInStock=$this->StockItemLog->getQuantityInStock($allInventoryProducts[$ip]['InventoryProduct']['id'],date('Y-m-d'));
			//echo "inventory product id is ".$quantityInStock."<br/>";
			$allInventoryProducts[$ip]['InventoryProduct']['stock_quantity']=$quantityInStock;
		}
		//pr($allInventoryProducts);
		
		$inventoryProducts=array();
		$inventoryProductStockQuantities=array();
		$inventoryProductLines=array();
		
		for ($ip=0;$ip<count($allInventoryProducts);$ip++){
			if ($allInventoryProducts[$ip]['InventoryProduct']['stock_quantity']>0){
				$inventoryProducts[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['name'];
				$inventoryProductStockQuantities[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['stock_quantity'];
				$inventoryProductLines[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['inventory_product_line_id'];
			}
		}
		
		//$inventoryProducts=$this->InventoryProduct->find('list');
		$this->set(compact('inventoryProducts','inventoryProductStockQuantities','inventoryProductLines'));
		
		
		$this->loadModel('ExchangeRate');
		$remissionExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($remissionDate);
		$exchangeRateRemission=$remissionExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateRemission'));
		
		$aco_name="InventoryClients/index";		
		$bool_inventoryclient_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_index_permission'));
		$aco_name="InventoryClients/add";		
		$bool_inventoryclient_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->Remission->exists($id)) {
			throw new NotFoundException(__('Invalid remission'));
		}
		$remissionId=$id;
		$this->set(compact('remissionId'));
		$this->loadModel('InventoryProduct');
		$numberOfProducts=0;
		
		if ($this->request->is(array('post', 'put'))) {
			//pr($this->request->data);
			$boolItemQuantitiesOK=true;
			$itemsExceededMessage="";
			foreach($this->request->data['StockMovement'] as $stockMovement){
				if ($stockMovement['inventory_product_id']){
					$numberOfProducts++;
				}
				if ($stockMovement['product_quantity']>$stockMovement['stock_quantity_value']){
					$boolItemQuantitiesOK=false;
					if (empty($itemsExceededMessage)){
						$itemsExceededMessage.="No se podía guardar porque la cantidad de productos excede la cantidad en bodega para productos";
					}
					$relatedInventoryProduct=$this->InventoryProduct->find('first',array(
						'conditions'=>array(
							'InventoryProduct.id'=>$stockMovement['inventory_product_id'],
						),
					));
					$itemsExceededMessage.=" ".$relatedInventoryProduct['InventoryProduct']['name']." Requerido:".$stockMovement['product_quantity']." (en bodega:".$stockMovement['stock_quantity_value'].")";
				}
			}
			
			$remissionDateArray=$this->request->data['Remission']['remission_date'];
			//pr($entryDateArray);
			$remissionDateString=$remissionDateArray['year'].'-'.$remissionDateArray['month'].'-'.$remissionDateArray['day'];
			$remissionDate=date( "Y-m-d", strtotime($remissionDateString));
			
			$previousRemissionsWithThisCode=array();
			$previousRemissionsWithThisCode=$this->Remission->find('all',array(
				'conditions'=>array(
					'Remission.remission_code'=>$this->request->data['Remission']['remission_code'],
					'Remission.inventory_client_id'=>$this->request->data['Remission']['inventory_client_id'],
					'Remission.id !='=>$id,
				),
			));
						
			if ($remissionDateString>date('Y-m-d')){
				$this->Session->setFlash(__('La fecha de salida no puede estar en el futuro!  No se guardó la salida.'), 'default',array('class' => 'error-message'));
			}
			elseif (!$boolItemQuantitiesOK){
				$itemsExceededMessage.=".  No se guardó la salida.";
				$this->Session->setFlash($itemsExceededMessage, 'default',array('class' => 'error-message'));
			}
			elseif (count($previousRemissionsWithThisCode)>0){
				$this->Session->setFlash(__('Ya se registró una salida con este número para este cliente!  No se guardó la salida.'), 'default',array('class' => 'error-message'));
			}
			elseif (empty($this->request->data['Remission']['inventory_client_id'])){
				$this->Session->setFlash(__('Se debe seleccionar el cliente para la salida!  No se guardó la salida.'), 'default',array('class' => 'error-message'));
			}
			else {
				$this->loadModel('StockItem');
				$this->loadModel('StockItemLog');
				$this->loadModel('StockMovement');
				
				// first undo previous
				$boolPreviousDeleted=false;
				$previousRemission=$this->Remission->find('first', array(
					'conditions' => array(
						'Remission.id' => $id,
					),
					'contain'=>array(
						'StockMovement'=>array(
							'StockItem'=>array(
								'StockItemLog',
							),
						),
					),
				));
				$datasource=$this->Remission->getDataSource();
				try {
					$datasource->begin();	
					foreach ($previousRemission['StockMovement'] as $stockMovement){
						$stockItemArray=array();
						$stockItemArray['StockItem']['product_remaining_quantity']=$stockMovement['StockItem']['product_remaining_quantity']+$stockMovement['product_quantity'];
						$stockItemArray['StockItem']['description']=$stockMovement['StockItem']['description']."| se eliminó la salida ".$previousRemission['Remission']['remission_code'];
						$this->StockItem->id=$stockMovement['StockItem']['id'];
						if (!$this->StockItem->save($stockItemArray)) {
							echo "problema reseteando el estado de lote";
							pr($this->validateErrors($this->StockItem));
							throw new Exception();
						}
						
						$this->StockMovement->id=$stockMovement['id'];
						if (!$this->StockMovement->delete()) {
							echo "problema eliminando el movimiento de lote";
							pr($this->validateErrors($this->Order->StockMovement));
							throw new Exception();
						}
					
						$this->recreateStockItemLogs($stockMovement['stock_item_id']);
					}
							
					$datasource->commit();
					$boolPreviousDeleted=true;
				}
				catch(Exception $e){
					$datasource->rollback();
					pr($e);
					$this->Session->setFlash(__('No se podían eliminar los datos de la salida previa.'), 'default',array('class' => 'error-message'));
				}
						
				if ($boolPreviousDeleted){
					$datasource=$this->Remission->getDataSource();
					if ($this->request->data['Remission']['bool_annulled']){
						try {
							//pr($this->request->data);
							$datasource->begin();	
							
							$this->Remission->id=$id;
							if (!$this->Remission->save($this->request->data)) {
								echo "Problema al guardar la salida";
								pr($this->validateErrors($this->Remission));
								throw new Exception();
							}
							
							$datasource->commit();
							
							$this->recordUserAction($this->Remission->id,null,null);
							$this->recordUserActivity($this->Session->read('User.username'),"Se registró la salida anulada número ".$this->request->data['Remission']['remission_code']);
							
							$this->Session->setFlash(__('Se guardó la salida.'),'default',array('class' => 'success'));				
							return $this->redirect(array('action' => 'index'));
						}
						catch(Exception $e){
							$datasource->rollback();
							pr($e);
							// regenerate the eliminated movements and stockitems
							foreach ($previousRemission['StockMovement'] as $stockMovement){
								$stockMovementArray=array();
								$stockMovementData['movement_date']=$remissionDateArray;
								$stockMovementData['bool_input']=$stockMovement['bool_input'];
								$stockMovementData['name']=$stockMovement['name'];
								$stockMovementData['description']=$stockMovement['description'];
								$stockMovementData['remission_id']=$stockMovement['remission_id'];
								$stockMovementData['stock_item_id']=$stockMovement['stock_item_id'];
								$stockMovementData['inventory_product_id']=$stockMovement['inventory_product_id'];
								$stockMovementData['product_quantity']=$stockMovement['product_quantity'];
								$stockMovementData['measuring_unit_id']=$stockMovement['measuring_unit_id'];
								$stockMovementData['product_unit_cost']=$stockMovement['product_unit_cost'];
								$stockMovementData['product_unit_price']=$stockMovement['product_unit_price'];
								$stockMovementData['currency_id']=$stockMovement['currency_id'];
								$stockMovementData['width']=$stockMovement['width'];							
								$stockMovementData['height']=$stockMovement['height'];
								$stockMovementData['color']=$stockMovement['color'];
								$this->StockMovement->create();
								if (!$this->StockMovement->save($stockMovementData)) {
									echo "problema al restaurar el movimiento de lote";
									pr($this->validateErrors($this->StockMovement));
									throw new Exception();
								}
								
								$stockItemArray=array();
								$stockItemArray['description']=$stockMovement['StockItem']['description'];
								$stockItemArray['product_remaining_quantity']=$stockMovement['StockItem']['product_remaining_quantity'];
								$this->StockItem->id=$stockMovement['stock_item_id'];
								if (!$this->StockItem->save($stockItemArray)) {
									echo "problema al restaurar el lote";
									pr($this->validateErrors($this->StockItem));
									throw new Exception();
								}
								
								$this->recreateStockItemLogs($stockMovement['stock_item_id']);
							}
							$this->Session->setFlash(__('No se guardó la salida.'), 'default',array('class' => 'error-message'));
						}
					}
					else {
						$this->loadModel('InventoryProduct');
						$this->loadModel('InventoryClient');
						$this->loadModel('StockItem');
						$this->loadModel('StockMovement');
						$this->loadModel('StockItemLog');
						
						try {
							//pr($this->request->data);
							$datasource->begin();	
							
							$this->Remission->id=$id;
							if (!$this->Remission->save($this->request->data)) {
								echo "Problema al guardar la salida";
								pr($this->validateErrors($this->Remission));
								throw new Exception();
							}
							$remissionId=$this->Remission->id;
							
							$inventoryClient=$this->InventoryClient->find('first',array(
								'conditions'=>array(
									'InventoryClient.id'=>$this->request->data['Remission']['inventory_client_id'],
								),
							));
							//pr($inventoryClient);
							$inventoryClientName=$inventoryClient['InventoryClient']['name'];
							
							foreach ($this->request->data['StockMovement'] as $stockMovement){
								if ($stockMovement['product_quantity']>0 && $stockMovement['inventory_product_id']>0){								
									$relatedInventoryProduct=$this->InventoryProduct->find('first',array(
										'conditions'=>array(
											'InventoryProduct.id'=>$stockMovement['inventory_product_id'],
										),
									));
									$usedMaterials= $this->StockItem->getMaterialsForRemission($stockMovement['inventory_product_id'],$stockMovement['product_quantity'],$remissionDateString);									
									//pr($usedMaterials);
									for ($k=0;$k<count($usedMaterials);$k++){
										$materialUsed=$usedMaterials[$k];
										$stock_item_id=$materialUsed['id'];
										$quantity_present=$materialUsed['quantity_present'];
										$quantity_used=$materialUsed['quantity_used'];
										$quantity_remaining=$materialUsed['quantity_remaining'];
										if (!$this->StockItem->exists($stock_item_id)) {
											throw new NotFoundException(__('Invalid StockItem'));
										}
										$linkedStockItem=$this->StockItem->read(null,$stock_item_id);
										$message="Se vendió lote ".$relatedInventoryProduct['InventoryProduct']['name']." (Cantidad:".$quantity_used.") para Salida ".$this->request->data['Remission']['remission_code'];
										
										$stockItemData=array();
										$stockItemData['id']=$stock_item_id;
										$stockItemData['description']=$linkedStockItem['StockItem']['description']."|".$message;
										$stockItemData['product_remaining_quantity']=$quantity_remaining;
										$this->StockItem->id=$stock_item_id;
										if (!$this->StockItem->save($stockItemData)) {
											echo "problema al guardar el lote";
											pr($this->validateErrors($this->StockItem));
											throw new Exception();
										}
										
										// STEP 2: SAVE THE STOCK MOVEMENT
										$message="Se remitió ".$relatedInventoryProduct['InventoryProduct']['name']." (Cantidad:".$quantity_used.", para Salida ".$this->request->data['Remission']['remission_code'];
										$stockMovementData=array();
										$stockMovementData['movement_date']=$remissionDateArray;
										$stockMovementData['bool_input']=false;
										$stockMovementData['name']=$remissionDateArray['day'].$remissionDateArray['month'].$remissionDateArray['year']."_".$this->request->data['Remission']['remission_code']."_".$relatedInventoryProduct['InventoryProduct']['name'];
										$stockMovementData['description']=$message;
										$stockMovementData['remission_id']=$remissionId;
										$stockMovementData['stock_item_id']=$stock_item_id;
										$stockMovementData['inventory_product_id']=$stockMovement['inventory_product_id'];
										$stockMovementData['product_quantity']=$quantity_used;
										$stockMovementData['measuring_unit_id']=$relatedInventoryProduct['InventoryProduct']['measuring_unit_id'];
										$stockMovementData['product_unit_cost']=$linkedStockItem['StockItem']['product_unit_cost'];
										$stockMovementData['product_unit_price']=$stockMovement['product_unit_price'];
										$stockMovementData['currency_id']=$this->request->data['Remission']['currency_id'];
										if (!empty($stockMovement['width'])){
											$stockMovementData['width']=$stockMovement['width'];
										}
										if (!empty($stockMovement['height'])){
											$stockMovementData['height']=$stockMovement['height'];
										}
										if (!empty($stockMovement['color'])){
											$stockMovementData['color']=$stockMovement['color'];
										}
										
										$this->StockMovement->create();
										if (!$this->StockMovement->save($stockMovementData)) {
											echo "problema al guardar el movimiento de lote";
											pr($this->validateErrors($this->StockMovement));
											throw new Exception();
										}
									
										// STEP 3: SAVE THE STOCK ITEM LOG
										$this->recreateStockItemLogs($stock_item_id);
												
										// STEP 4: SAVE THE USERLOG FOR THE STOCK MOVEMENT
										$this->recordUserActivity($this->Session->read('User.username'),$message);
									}
								}
							}
							$datasource->commit();
							$this->recordUserAction($this->Remission->id,null,null);
							$this->recordUserActivity($this->Session->read('User.username'),"Se registró la salida número ".$this->request->data['Remission']['remission_code']);
							
							$this->Session->setFlash(__('La salida se guardó.'),'default',array('class' => 'success'));				
							return $this->redirect(array('action' => 'index'));
						}
						catch(Exception $e){
							$datasource->rollback();
							pr($e);
							// regenerate the eliminated movements and stockitems
							foreach ($previousRemission['StockMovement'] as $stockMovement){
								$stockMovementArray=array();
								$stockMovementData['movement_date']=$remissionDateArray;
								$stockMovementData['bool_input']=$stockMovement['bool_input'];
								$stockMovementData['name']=$stockMovement['name'];
								$stockMovementData['description']=$stockMovement['description'];
								$stockMovementData['remission_id']=$stockMovement['remission_id'];
								$stockMovementData['stock_item_id']=$stockMovement['stock_item_id'];
								$stockMovementData['inventory_product_id']=$stockMovement['inventory_product_id'];
								$stockMovementData['product_quantity']=$stockMovement['product_quantity'];
								$stockMovementData['measuring_unit_id']=$stockMovement['measuring_unit_id'];
								$stockMovementData['product_unit_cost']=$stockMovement['product_unit_cost'];
								$stockMovementData['product_unit_price']=$stockMovement['product_unit_price'];
								$stockMovementData['currency_id']=$stockMovement['currency_id'];
								$stockMovementData['width']=$stockMovement['width'];							
								$stockMovementData['height']=$stockMovement['height'];
								$stockMovementData['color']=$stockMovement['color'];
								$this->StockMovement->create();
								if (!$this->StockMovement->save($stockMovementData)) {
									echo "problema al restaurar el movimiento de lote";
									pr($this->validateErrors($this->StockMovement));
									throw new Exception();
								}
								
								$stockItemArray=array();
								$stockItemArray['description']=$stockMovement['StockItem']['description'];
								$stockItemArray['product_remaining_quantity']=$stockMovement['StockItem']['product_remaining_quantity'];
								$this->StockItem->id=$stockMovement['stock_item_id'];
								if (!$this->StockItem->save($stockItemArray)) {
									echo "problema al restaurar el lote";
									pr($this->validateErrors($this->StockItem));
									throw new Exception();
								}
								
								$this->recreateStockItemLogs($stockMovement['stock_item_id']);
							}
							$this->Session->setFlash(__('No se podía guardar la salida.'), 'default',array('class' => 'error-message'));
						}
						
					}
				}
			}
		} 
		else {
			$this->request->data = $this->Remission->find('first', array(
				'conditions' => array(
					'Remission.id' => $id,
				),
				'contain'=>array(
					'StockMovement',
				),
			));
			foreach($this->request->data['StockMovement'] as $stockMovement){
				if ($stockMovement['inventory_product_id']){
					$numberOfProducts++;
				}
			}
		}
		$remissionDate=$this->request->data['Remission']['remission_date'];
		$this->set(compact('numberOfProducts'));
		
		$inventoryClients = $this->Remission->InventoryClient->find('list');
		$currencies = $this->Remission->Currency->find('list');
		$this->set(compact('inventoryClients', 'currencies'));
		
		$this->loadModel('InventoryProduct');
		$this->InventoryProduct->recursive=-1;
		$allInventoryProducts=$this->InventoryProduct->find('all',array(
			'conditions'=>array(
				'bool_active'=>true,
			),
		));
		
		$this->loadModel('StockItemLog');
		for ($ip=0;$ip<count($allInventoryProducts);$ip++){
			
			//echo "inventory product id is ".$allInventoryProducts[$ip]['InventoryProduct']['id']."<br/>";
			$quantityInStock=$this->StockItemLog->getQuantityInStock($allInventoryProducts[$ip]['InventoryProduct']['id'],date('Y-m-d'));
			//echo "inventory product id is ".$quantityInStock."<br/>";
			$allInventoryProducts[$ip]['InventoryProduct']['stock_quantity']=$quantityInStock;
		}
		//pr($allInventoryProducts);
		
		$inventoryProducts=array();
		$inventoryProductStockQuantities=array();
		$inventoryProductLines=array();
		
		for ($ip=0;$ip<count($allInventoryProducts);$ip++){
			if ($allInventoryProducts[$ip]['InventoryProduct']['id']==51){
				//pr($allInventoryProducts[$ip]);
			}
			foreach ($this->request->data['StockMovement'] as $stockMovement){
				if ($stockMovement['inventory_product_id']){
					if ($stockMovement['inventory_product_id']==$allInventoryProducts[$ip]['InventoryProduct']['id']){
						$allInventoryProducts[$ip]['InventoryProduct']['stock_quantity']+=$stockMovement['product_quantity'];
						break;
					}
				}
			}
			if ($allInventoryProducts[$ip]['InventoryProduct']['stock_quantity']>0){
				$inventoryProducts[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['name'];
				$inventoryProductStockQuantities[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['stock_quantity'];
				$inventoryProductLines[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['inventory_product_line_id'];
			}
		}
		
		//$inventoryProducts=$this->InventoryProduct->find('list');
		$this->set(compact('inventoryProducts','inventoryProductStockQuantities','inventoryProductLines'));
		
		
		$this->loadModel('ExchangeRate');
		$remissionExchangeRate=$this->ExchangeRate->getApplicableExchangeRate($this->request->data['Remission']['remission_date']);
		$exchangeRateRemission=$remissionExchangeRate['ExchangeRate']['rate'];
		$this->set(compact('exchangeRateRemission'));
		
		$aco_name="InventoryClients/index";		
		$bool_inventoryclient_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_index_permission'));
		$aco_name="InventoryClients/add";		
		$bool_inventoryclient_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryclient_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Remission->id = $id;
		if (!$this->Remission->exists()) {
			throw new NotFoundException(__('Invalid remission'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$this->loadModel('StockMovement');
		$this->loadModel('StockItem');
		$this->loadModel('StockItemLog');
		$this->StockMovement->recursive=-1;

		$this->Remission->recursive=-1;
		$remission=$this->Remission->find('first',array(
			'conditions'=>array(
				'Remission.id'=>$id,
			),
			'contain'=>array(
				'StockMovement'=>array(
					'StockItem'=>array(
						'StockItemLog',
					),
				),
			),
		));
		$datasource=$this->Remission->getDataSource();
		try {
			$datasource->begin();	
			foreach ($remission['StockMovement'] as $stockMovement){
				$stockItemArray=array();
				$stockItemArray['StockItem']['product_remaining_quantity']=$stockMovement['StockItem']['product_remaining_quantity']+$stockMovement['product_quantity'];
				$stockItemArray['StockItem']['description']=$stockMovement['StockItem']['description']."| se eliminó la salida ".$remission['Remission']['remission_code'];
				$this->StockItem->id=$stockMovement['StockItem']['id'];
				if (!$this->StockItem->save($stockItemArray)) {
					echo "problema reseteando el estado de lote";
					pr($this->validateErrors($this->StockItem));
					throw new Exception();
				}
				
				$this->StockMovement->id=$stockMovement['id'];
				if (!$this->StockMovement->delete()) {
					echo "problema eliminando el movimiento de lote";
					pr($this->validateErrors($this->Order->StockMovement));
					throw new Exception();
				}
			}
			
			if (!$this->Remission->delete($id)) {
				echo "Problema al eliminar la salida";
				pr($this->validateErrors($this->Remission));
				throw new Exception();
			}
			
			foreach ($remission['StockMovement'] as $stockMovement){
				$this->recreateStockItemLogs($stockMovement['stock_item_id']);
			}
					
			$datasource->commit();
					
			$this->recordUserActivity($this->Session->read('User.username'),"Se eliminó la salida número ".$remission['Remission']['remission_code']);
					
			$this->Session->setFlash(__('Se eliminó la salida.'),'default',array('class' => 'success'));				
			return $this->redirect(array('action' => 'index'));
		}
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			$this->Session->setFlash(__('No se podía eliminar la salida.'), 'default',array('class' => 'error-message'));
			return $this->redirect(array('action' => 'view',$id));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
