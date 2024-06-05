<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class StockItemsController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function inventario() {
		$inventoryDate = null;
		$currency_id=CURRENCY_USD;
		if ($this->request->is('post')) {
			$inventoryDateArray=$this->request->data['Report']['inventorydate'];
			$inventoryDateString=$inventoryDateArray['year'].'-'.$inventoryDateArray['month'].'-'.$inventoryDateArray['day'];
			$inventoryDate=date( "Y-m-d", strtotime($inventoryDateString));
			$inventoryDatePlusOne=date("Y-m-d",strtotime($inventoryDateString."+1 days"));
			
			$currency_id=$this->request->data['Report']['currency_id'];
		}
		if (!isset($inventoryDate)){
			$inventoryDate = date("Y-m-d",strtotime(date("Y-m-d")));
			$inventoryDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['inventoryDate']=$inventoryDate;
		$this->set(compact('inventoryDate'));
		$this->set(compact('currency_id'));
		
		$this->StockItem->recursive=-1;
		
		$conditions=array(
			'StockItem.product_remaining_quantity >'=>0,
		);
		$productCount= $this->StockItem->find('count', array(
			'fields'=>array(
				//'SUM(StockItem.product_remaining_quantity) AS Remaining',
				//'SUM(StockItem.product_remaining_quantity*StockItem.product_unit_cost) AS Saldo',
			),
			'conditions' => $conditions,
			'contain'=>array(
				'InventoryProduct'=>array(
					'fields'=>array(
						'InventoryProduct.id',
						'InventoryProduct.name',
						'InventoryProduct.code',
						'InventoryProduct.inventory_product_line_id',
						'InventoryProduct.measuring_unit_id',
					),
					'InventoryProductLine',
					'MeasuringUnit',
				),
			),
			'order'=>'InventoryProduct.name',
			'group'=>'InventoryProduct.name',
			//'order'=>'Saldo DESC'
		));
		$inventoryProducts =array();
		if ($productCount>0){
			$this->Paginator->settings = array(
				'fields'=>array(
					//'SUM(StockItem.product_remaining_quantity) AS Remaining',
					//'SUM(StockItem.product_remaining_quantity*StockItem.product_unit_cost) AS Saldo', 
				),
				'conditions' => $conditions,
				'contain'=>array(
					'InventoryProduct'=>array(
						'fields'=>array(
							'InventoryProduct.id',
							'InventoryProduct.name',
							'InventoryProduct.code',
							'InventoryProduct.inventory_product_line_id',
							'InventoryProduct.measuring_unit_id',
						),
						'InventoryProductLine',
						'MeasuringUnit',
					),
				),
				'group'=>'InventoryProduct.name',
				'order'=>'InventoryProduct.name',
				'limit'=>$productCount,
			);		
			$inventoryProducts = $this->Paginator->paginate('StockItem');
		}
		
		//pr($inventoryProducts);
		usort($inventoryProducts,array($this,'sortByInventoryProductLineNameProductName'));
		//pr($inventoryProducts);
		$this->loadModel('StockItemLog');
		$this->loadModel('ExchangeRate');
		
		// now overwrite based on StockItemLogs
		for ($i=0;$i<count($inventoryProducts);$i++){
			$this->StockItem->recursive=-1;
			$allStockItems=$this->StockItem->find('all',array(
				'fields'=>array('StockItem.id'),
				'conditions'=>array('StockItem.inventory_product_id'=>$inventoryProducts[$i]['InventoryProduct']['id']),
			));
			
			$totalStockInventoryDate=0;
			$totalValueInventoryDate=0;
			if (count($allStockItems)>0){
				$lastStockItemLog=array();
				foreach ($allStockItems as $stockItem){		
					$this->StockItemLog->recursive=-1;
					$lastStockItemLog=$this->StockItemLog->find('first',array(
						'fields'=>array(	
							'StockItemLog.product_quantity',
							'StockItemLog.product_unit_cost',
							'StockItemLog.stock_item_date',
							'StockItemLog.currency_id',
						),
						'conditions'=>array(
							'StockItemLog.stock_item_id'=>$stockItem['StockItem']['id'],
							'StockItemLog.stock_item_date <='=>$inventoryDatePlusOne,
						),
						'order'=>'StockItemLog.id DESC',
					));
					if (!empty($lastStockItemLog)){
						$totalStockInventoryDate+=$lastStockItemLog['StockItemLog']['product_quantity'];
						if ($lastStockItemLog['StockItemLog']['currency_id']==$currency_id){
							$totalValueInventoryDate+=$lastStockItemLog['StockItemLog']['product_quantity']*$lastStockItemLog['StockItemLog']['product_unit_cost'];
						}
						else {
							$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($lastStockItemLog['StockItemLog']['stock_item_date']);
							$rate=$exchangeRate['ExchangeRate']['rate'];
							if ($currency_id==CURRENCY_USD && $lastStockItemLog['StockItemLog']['currency_id']==CURRENCY_CS){
								$totalValueInventoryDate+=round($lastStockItemLog['StockItemLog']['product_quantity']*$lastStockItemLog['StockItemLog']['product_unit_cost']/$rate,2);
							}
							elseif ($currency_id==CURRENCY_CS && $lastStockItemLog['StockItemLog']['currency_id']==CURRENCY_USD){
								$totalValueInventoryDate+=round($lastStockItemLog['StockItemLog']['product_quantity']*$lastStockItemLog['StockItemLog']['product_unit_cost']*$rate,2);
							}
							else {
								$totalValueInventoryDate+=33333333;
							}
						}
					}
				}
			}
			$inventoryProducts[$i][0]['Remaining']=$totalStockInventoryDate;
			$inventoryProducts[$i][0]['Saldo']=$totalValueInventoryDate;
		}
		
		$this->set(compact('inventoryProducts'));
		
		$filename="Hoja_Inventario_".date('d_m_Y');
		$this->set(compact('filename'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
	}

	public function guardarReporteInventario() {
		$exportData=$_SESSION['inventoryReport'];
		$this->set(compact('exportData'));
	}	

	public function verPdfHojaInventario($inventoryDate=null,$currency_id) {
		if ($inventoryDate==null){
			$startDateString=$_SESSION['inventoryDate'];
		}
		else {
			$inventoryDateString=$inventoryDate;
		}
		$inventoryDate=date( "Y-m-d", strtotime($inventoryDateString));
		$inventoryDatePlusOne=date("Y-m-d",strtotime($inventoryDateString."+1 days"));
		$this->set(compact('inventoryDate','inventoryDatePlusOne'));
	
		$this->StockItem->recursive=-1;
		
		$conditions=array(
			'StockItem.product_remaining_quantity >'=>0,
		);
		$productCount= $this->StockItem->find('count', array(
			'fields'=>array(
				//'SUM(StockItem.product_remaining_quantity) AS Remaining',
				//'SUM(StockItem.product_remaining_quantity*StockItem.product_unit_cost) AS Saldo',
			),
			'conditions' => $conditions,
			'contain'=>array(
				'InventoryProduct'=>array(
					'fields'=>array(
						'InventoryProduct.id',
						'InventoryProduct.name',
						'InventoryProduct.inventory_product_line_id',
						'InventoryProduct.measuring_unit_id',
					),
					'InventoryProductLine',
					'MeasuringUnit',
				),
			),
			'group'=>'InventoryProduct.name',
			//'order'=>'Saldo DESC'
		));
		$inventoryProducts =array();
		if ($productCount>0){
			$this->Paginator->settings = array(
				'fields'=>array(
					//'SUM(StockItem.product_remaining_quantity) AS Remaining',
					//'SUM(StockItem.product_remaining_quantity*StockItem.product_unit_cost) AS Saldo', 
				),
				'conditions' => $conditions,
				'contain'=>array(
					'InventoryProduct'=>array(
						'fields'=>array(
							'InventoryProduct.id',
							'InventoryProduct.name',
							'InventoryProduct.inventory_product_line_id',
							'InventoryProduct.measuring_unit_id',
						),
						'InventoryProductLine',
						'MeasuringUnit',
					),
				),
				'group'=>'InventoryProduct.name',
				'order'=>'InventoryProduct.name',
				'limit'=>$productCount,
			);		
			$inventoryProducts = $this->Paginator->paginate('StockItem');
		}
		usort($inventoryProducts,array($this,'sortByInventoryProductLineNameProductName'));
		//pr($inventoryProducts);
		
		$this->loadModel('StockItemLog');
		$this->loadModel('ExchangeRate');
		
		// now overwrite based on StockItemLogs
		for ($i=0;$i<count($inventoryProducts);$i++){
			$this->StockItem->recursive=-1;
			$allStockItems=$this->StockItem->find('all',array(
				'fields'=>array('StockItem.id'),
				'conditions'=>array('StockItem.inventory_product_id'=>$inventoryProducts[$i]['InventoryProduct']['id']),
			));
			
			$totalStockInventoryDate=0;
			$totalValueInventoryDate=0;
			if (count($allStockItems)>0){
				$lastStockItemLog=array();
				foreach ($allStockItems as $stockItem){		
					$this->StockItemLog->recursive=-1;
					$lastStockItemLog=$this->StockItemLog->find('first',array(
						'fields'=>array(	
							'StockItemLog.product_quantity',
							'StockItemLog.product_unit_cost',
							'StockItemLog.stock_item_date',
							'StockItemLog.currency_id',
						),
						'conditions'=>array(
							'StockItemLog.stock_item_id'=>$stockItem['StockItem']['id'],
							'StockItemLog.stock_item_date <='=>$inventoryDatePlusOne,
						),
						'order'=>'StockItemLog.id DESC',
					));
					if (!empty($lastStockItemLog)){
						$totalStockInventoryDate+=$lastStockItemLog['StockItemLog']['product_quantity'];
						if ($lastStockItemLog['StockItemLog']['currency_id']==$currency_id){
							$totalValueInventoryDate+=$lastStockItemLog['StockItemLog']['product_quantity']*$lastStockItemLog['StockItemLog']['product_unit_cost'];
						}
						else {
							$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($lastStockItemLog['StockItemLog']['stock_item_date']);
							$rate=$exchangeRate['ExchangeRate']['rate'];
							if ($currency_id==CURRENCY_USD && $lastStockItemLog['StockItemLog']['currency_id']==CURRENCY_CS){
								$totalValueInventoryDate+=round($lastStockItemLog['StockItemLog']['product_quantity']*$lastStockItemLog['StockItemLog']['product_unit_cost']/$rate,2);
							}
							elseif ($currency_id==CURRENCY_CS && $lastStockItemLog['StockItemLog']['currency_id']==CURRENCY_USD){
								$totalValueInventoryDate+=round($lastStockItemLog['StockItemLog']['product_quantity']*$lastStockItemLog['StockItemLog']['product_unit_cost']*$rate,2);
							}
							else {
								$totalValueInventoryDate+=33333333;
							}
						}
					}
				}
			}
			$inventoryProducts[$i][0]['Remaining']=$totalStockInventoryDate;
			$inventoryProducts[$i][0]['Saldo']=$totalValueInventoryDate;
		}
		
		$this->set(compact('inventoryProducts'));
		
		$filename="Hoja_Inventario_".date('d_m_Y');
		$this->set(compact('filename'));
	}
	
	public function sortByInventoryProductLineNameProductName($firstTerm,$secondTerm){
		
		if($firstTerm['InventoryProduct']['InventoryProductLine']['name'] == $secondTerm['InventoryProduct']['InventoryProductLine']['name']){ 		
			return ($firstTerm['InventoryProduct']['name'] < $secondTerm['InventoryProduct']['name']) ? -1 : 1;
		}
		else {
			return ($firstTerm['InventoryProduct']['InventoryProductLine']['name'] < $secondTerm['InventoryProduct']['InventoryProductLine']['name']) ? -1 : 1;
		}
	}

	/******************** CUADRAR LOTES *******************/
	
	public function cuadrarEstadosDeLote(){
		$this->loadModel('StockMovement');
		$this->loadModel('StockItemLog');
		$this->loadModel('InventoryProductLine');
		
		$this->InventoryProductLine->recursive=-1;
		
		$otherInventoryProductLineIDs=$this->InventoryProductLine->find('list',array(
			'fields'=>array('InventoryProductLine.id'),
			//'conditions'=>array('InventoryProductLine.product_category_id'=>CATEGORY_OTHER)
		));
		
		// for other
		$allOtherStockItems=$this->StockItem->find('all',array(
			'fields'=>array('StockItem.id, StockItem.product_original_quantity, StockItem.product_remaining_quantity, InventoryProduct.id, InventoryProduct.inventory_product_line_id, InventoryProduct.name'),
			'conditions'=>array(
				'InventoryProduct.inventory_product_line_id'=>$otherInventoryProductLineIDs,
			),
			'order'=>'InventoryProduct.name, StockItem.id'
		));
		//pr($allOtherStockItems);
		for ($i=0;$i<count($allOtherStockItems);$i++){
			$inputStockMovementTotalForStockItem=$this->StockMovement->find('first',array(
				'fields'=>array('StockItem.id, SUM(StockMovement.product_quantity) AS total_product_quantity'),
				'conditions'=>array(
					'StockMovement.stock_item_id'=>$allOtherStockItems[$i]['StockItem']['id'],
					'bool_input'=>true,
				),
				'group'=>array('StockItem.id'),
			));
			if (!empty($inputStockMovementTotalForStockItem)){
				$allOtherStockItems[$i]['StockItem']['total_moved_in']=$inputStockMovementTotalForStockItem[0]['total_product_quantity'];
			}
			else {
				$allOtherStockItems[$i]['StockItem']['total_moved_in']=0;
			}
			$allOtherStockItems[$i]['StockItem']['total_used_in_production']=0;
			$stockMovementTotalForStockItem=$this->StockMovement->find('first',array(
				'fields'=>array('StockItem.id, SUM(StockMovement.product_quantity) AS total_product_quantity'),
				'conditions'=>array(
					'StockMovement.stock_item_id'=>$allOtherStockItems[$i]['StockItem']['id'],
					'bool_input'=>false,
				),
				'group'=>array('StockItem.id'),
			));
			if (!empty($stockMovementTotalForStockItem)){
				$allOtherStockItems[$i]['StockItem']['total_moved_out']=$stockMovementTotalForStockItem[0]['total_product_quantity'];
			}
			else {
				$allOtherStockItems[$i]['StockItem']['total_moved_out']=0;
			}
			$lastStockItemLog=$this->StockItemLog->find('first',array(
				'fields'=>array('StockItemLog.id, StockItemLog.product_quantity'),
				'conditions'=>array(
					'StockItemLog.stock_item_id'=>$allOtherStockItems[$i]['StockItem']['id'],
				),
				'order'=>array('StockItemLog.id DESC,StockItemLog.stock_item_date DESC'),
			));
			//echo "stockitemlog for stockitem ".$allOtherStockItems[$i]['StockItem']['id']."<br/>";
			//pr($lastStockItemLog);
			if (!empty($lastStockItemLog['StockItemLog'])){
				$allOtherStockItems[$i]['StockItem']['latest_log_quantity']=$lastStockItemLog['StockItemLog']['product_quantity'];
				$allOtherStockItems[$i]['StockItem']['latest_log_id']=$lastStockItemLog['StockItemLog']['id'];
			}
			else {
				$allOtherStockItems[$i]['StockItem']['latest_log_quantity']=0;
				$allOtherStockItems[$i]['StockItem']['latest_log_id']=0;
			}
		}
		
		$this->set(compact('allRawStockItems','allFinishedStockItems','allOtherStockItems'));
	}
	
	public function recreateStockItemLogsForSquaring($id = null) {
		$this->StockItem->id = $id;
		if (!$this->StockItem->exists()) {
			throw new NotFoundException(__('Invalid stock item'));
		}
		$success=$this->recreateStockItemLogs($id);
		if ($success){
			$this->Session->setFlash(__('Los estados de lote han estado recreados para el lote '.$id),'default',array('class' => 'success'));
		}
		else {
			$this->Session->setFlash(__('No se podían recrear los estados de lote para el lote '.$id), 'default',array('class' => 'error-message'));
		}
		return $this->redirect(array('action'=> 'cuadrarEstadosDeLote'));
	}
	
	public function recreateAllStockItemLogs() {
		$allStockItems=$this->StockItem->find('list');
		//pr($allStockItems);
		foreach (array_keys($allStockItems) as $StockItemID){
			$success=$this->recreateStockItemLogs($StockItemID);
			if (!$success){
				$this->Session->setFlash(__('No se podían recrear los estados de lote para el lote '.$StockItemID), 'default',array('class' => 'error-message'));
				return $this->redirect(array('action' => 'cuadrarEstadosDeLote'));
			}
		}
		$this->Session->setFlash(__('Los estados de lote han estado recreados'),'default',array('class' => 'success'));
		return $this->redirect(array('action' => 'cuadrarEstadosDeLote'));
	}

	/******************** CUADRAR PRECIOS *******************/
	
	public function cuadrarPreciosBotellas(){
		$this->loadModel('StockMovement');
		$this->loadModel('InventoryProductionMovement');
		$this->loadModel('StockItemLog');
		$this->loadModel('InventoryProductLine');
		
		$this->InventoryProductLine->recursive=0;
				
		$finishedInventoryProductLineIDs=$this->InventoryProductLine->find('list',array(
			'fields'=>array('InventoryProductLine.id'),
			'conditions'=>array('InventoryProductLine.product_category_id'=>CATEGORY_PRODUCED)
		));
		
		// for finished
		$allFinishedStockItems=$this->StockItem->find('all',array(
			'fields'=>array('StockItem.id, StockItem.product_original_quantity, StockItem.stockitem_creation_date, StockItem.product_unit_cost, ProductionResultCode.code, RawMaterial.name, InventoryProduct.name'),
			'conditions'=>array(
				'InventoryProduct.inventory_product_line_id'=>$finishedInventoryProductLineIDs,
			),
			'order'=>'RawMaterial.name, InventoryProduct.name,ProductionResultCode.code, StockItem.id'
		));
		
		for ($i=0;$i<count($allFinishedStockItems);$i++){
			$productionMovementForStockItem=$this->ProductionMovement->find('first',array(
				'fields'=>array('StockItem.id, ProductionMovement.product_unit_cost,ProductionMovement.production_run_id','InventoryProductionMovement.id'),
				'conditions'=>array(
					'InventoryProductionMovement.stock_item_id'=>$allFinishedStockItems[$i]['StockItem']['id'],
					'bool_input'=>false,
				),
			));
			
			$inputProductionMovementsForStockItem=array();
			if (!empty($productionMovementForStockItem)){
				//pr($productionMovementForStockItem);
				$allFinishedStockItems[$i]['StockItem']['InventoryProduction_movement_id']=$productionMovementForStockItem['InventoryProductionMovement']['id'];
				$allFinishedStockItems[$i]['StockItem']['movement_price']=$productionMovementForStockItem['InventoryProductionMovement']['InventoryProduct_unit_cost'];
				$inputProductionMovementsForStockItem=$this->ProductionMovement->find('all',array(
				'fields'=>array('StockItem.id, ProductionMovement.product_unit_cost, ProductionMovement.product_quantity'),
				'conditions'=>array(
					'InventoryProduction_run_id'=>$productionMovementForStockItem['InventoryProductionMovement']['InventoryProduction_run_id'],
					'InventoryProduct_quantity >'=>0,
					'bool_input'=>true,
				),
			));
			}
			else {
				$allFinishedStockItems[$i]['StockItem']['movement_price']=-1;
			}


			$rightPrice=-1;
			$totalPriceInput=0;
			$totalQuantityInput=0;
			foreach ($inputProductionMovementsForStockItem as $inputProductionMovementForStockItem){
				$totalPriceInput+=$inputProductionMovementForStockItem['InventoryProductionMovement']['InventoryProduct_unit_cost']*$inputProductionMovementForStockItem['InventoryProductionMovement']['InventoryProduct_quantity'];
				$totalQuantityInput+=$inputProductionMovementForStockItem['InventoryProductionMovement']['InventoryProduct_quantity'];
			}
			if (!empty($inputProductionMovementsForStockItem)){
				$allFinishedStockItems[$i]['StockItem']['right_price']=$totalPriceInput/$totalQuantityInput;
			}
			else {
				$allFinishedStockItems[$i]['StockItem']['right_price']=0;
			}
		}
		
		$this->set(compact('allFinishedStockItems'));
	}

	public function recreateAllBottleCosts(){}
	
	public function recreateProductionMovementPriceForSquaring($productionmovementid,$rightprice){
		$this->loadModel('InventoryProductionMovement');
		try {
			$datasource=$this->ProductionMovement->getDataSource();
			$datasource->begin();
			
			$productionMovementData['id']=$productionmovementid;
			$productionMovementData['InventoryProduct_unit_cost']=$rightprice;
			$logsuccess=$this->ProductionMovement->save($productionMovementData);
			if (!$logsuccess){
				echo "Error al guardar el movimiento de producción.  No se guardó<br/>";
				pr($this->validateErrors($this->ProductionMovement));
				throw new Exception();
			}
			$datasource->commit();
			$this->Session->setFlash(__('The production movement has been saved.'), 'default',array('class' => 'success'));
			return $this->redirect(array('action' => 'cuadrarPreciosBotellas'));
		}
		catch(Exception $e){
			$datasource->rollback();
			$this->Session->setFlash(__('The production movement could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
		}	
	}
	
	public function recreateStockItemPriceForSquaring($stockitemid,$rightprice){
		try {
			$datasource=$this->StockItem->getDataSource();
			$datasource->begin();
			
			$stockItemData['id']=$stockitemid;
			$stockItemData['InventoryProduct_unit_cost']=$rightprice;
			$logsuccess=$this->StockItem->save($stockItemData);
			if (!$logsuccess){
				echo "Error al guardar el movimiento de producción.  No se guardó<br/>";
				pr($this->validateErrors($this->StockItem));
				throw new Exception();
			}
			$datasource->commit();
			$this->Session->setFlash(__('The stock item has been saved.'), 'default',array('class' => 'success'));
			return $this->redirect(array('action' => 'cuadrarPreciosBotellas'));
		}
		catch(Exception $e){
			$datasource->rollback();
			$this->Session->setFlash(__('The stock item could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
		}
	}
	

	
	public function index() {
		$this->StockItem->recursive = -1;
		
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
		}
		
		if (!isset($startDate)){
			$startDate = date("Y-m-01");
		}
		if (!isset($endDate)){
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		$this->set(compact('startDate','endDate'));
		
		$stockItemCount=	$this->StockItem->find('count', array(
			'fields'=>array('StockItem.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($stockItemCount!=0?$stockItemCount:1),
		);

		$stockItems = $this->Paginator->paginate('StockItem');
		$this->set(compact('stockItems'));
	}

	

	public function view($id = null) {
		if (!$this->StockItem->exists($id)) {
			throw new NotFoundException(__('Invalid stock item'));
		}
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
		}
		if (!isset($startDate)){
			$startDate = date("Y-m-01");
		}
		if (!isset($endDate)){
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		$this->set(compact('startDate','endDate'));
		$options = array('conditions' => array('StockItem.' . $this->StockItem->primaryKey => $id));
		$this->set('stockItem', $this->StockItem->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->StockItem->create();
			if ($this->StockItem->save($this->request->data)) {
				$this->Session->setFlash(__('The stock item has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The stock item could not be saved. Please, try again.'));
			}
		}
		$products = $this->StockItem->Product->find('list');
		$currencies = $this->StockItem->Currency->find('list');
		$measuringUnits = $this->StockItem->MeasuringUnit->find('list');
		$this->set(compact('InventoryProducts', 'currencies', 'measuringUnits'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->StockItem->exists($id)) {
			throw new NotFoundException(__('Invalid stock item'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->StockItem->save($this->request->data)) {
				$this->Session->setFlash(__('The stock item has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The stock item could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('StockItem.' . $this->StockItem->primaryKey => $id));
			$this->request->data = $this->StockItem->find('first', $options);
		}
		$products = $this->StockItem->Product->find('list');
		$currencies = $this->StockItem->Currency->find('list');
		$measuringUnits = $this->StockItem->MeasuringUnit->find('list');
		$this->set(compact('InventoryProducts', 'currencies', 'measuringUnits'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->StockItem->id = $id;
		if (!$this->StockItem->exists()) {
			throw new NotFoundException(__('Invalid stock item'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->StockItem->delete()) {
			$this->Session->setFlash(__('The stock item has been deleted.'));
		} else {
			$this->Session->setFlash(__('The stock item could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
