<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');


class InventoryProductsController extends AppController {


	public $components = array('Paginator');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('saveproduct','getproductimage','getproductprice','getproductsforinventoryproductline','getproductsforinventorproductlinelisted','getproductmeasuringunitid','getproductstatsfordate','getproductwidth');		
	}
	/*
	public function saveproduct() {
		$this->layout = "ajax";
		
		// when data are passed with serialize, the ajax parameter processdata must be true, if not the values don't get parsed correctly
		//var_dump($_POST);
		//$inventoryProductArray1=$_POST['data'];
		//pr ($inventoryProductArray1);
		$inventoryProductArray=$_POST['data'];
		//pr ($inventoryProductArray);
		$filesArray=$_FILES['data'];
		//pr ($filesArray);
		
		$datasource=$this->InventoryProduct->getDataSource();
		try {
			// load providers
			$this->loadModel('InventoryProvider');
			$this->InventoryProvider->recursive=-1;
			$inventoryProviders = $this->InventoryProvider->find('all',array(
				'fields'=>array('InventoryProvider.id','InventoryProvider.name'),
				'conditions'=>array(
					'InventoryProvider.bool_active'=>true,
				),
			));
		
			$datasource->begin();
			$inventory_product_id=0;
			if (!empty($filesArray['tmp_name']['Document'])){
				$cleanFilesArray=array();
				foreach ($filesArray as $key =>$value){
					$cleanFilesArray[0][$key]=$filesArray[$key]['Document']['url_image'];
				}
				//pr($cleanFilesArray);
				$imageOK=$this->uploadFiles('inventoryproductimages/'.$inventory_product_id,$cleanFilesArray);
				//echo "image OK<br/>";
				//pr($imageOK);
				
				if (array_key_exists('urls',$imageOK)){
					$inventoryProductArray['InventoryProduct']['url_image']=$imageOK['urls'][0];
				}
			}
			
			$this->InventoryProduct->create();
			if (!$this->InventoryProduct->save($inventoryProductArray)) {
				echo "Problema guardando el producto";
				pr($this->validateErrors($this->InventoryProduct));
				throw new Exception();
			}
			$inventory_product_id=$this->InventoryProduct->id;
			for ($pr=0;$pr<count($inventoryProductArray['InventoryProvider']);$pr++){
				if ($inventoryProductArray['InventoryProvider'][$pr]['inventory_provider_id']){
					$inventoryProductInventoryProviderArray=array();
					$this->InventoryProduct->InventoryProductInventoryProvider->create();
					$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_product_id']=$inventory_product_id;
					$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_provider_id']=$inventoryProviders[$pr]['InventoryProvider']['id'];
					if (!$this->InventoryProduct->InventoryProductInventoryProvider->save($inventoryProductInventoryProviderArray)){
						echo "Problema guardando el proveedor para el producto";
						pr($this->validateErrors($this->InventoryProductInventoryProvider));
						throw new Exception();
					}
				}
			}
			$datasource->commit();
			$this->recordUserAction($this->InventoryProduct->id,null,null);
			$this->recordUserActivity($this->Session->read('User.username'),"Se creó el producto ".$this->request->data['InventoryProduct']['name']);
			$this->Session->setFlash(__('The product has been saved.'),'default',array('class' => 'success'));
			//return $this->redirect(array('action' => 'index'));
		} 
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			$this->Session->setFlash(__('The product could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
		}
				
		$inventoryProducts=$this->InventoryProduct->find('all',array(
			'fields'=>array(
				'InventoryProduct.id','InventoryProduct.name',
			),
			'order'=>'InventoryProduct.name',
		));
		//pr($inventoryProducts);
		$this->set(compact('products'));
	}
	*/
	public function index() {
		$this->InventoryProduct->recursive = -1;
		
		$currencyId=CURRENCY_USD;
		if ($this->request->is('post')) {
			$currencyId=$this->request->data['Report']['currency_id'];
		}
		$this->set(compact('currencyId'));
		
		$inventoryProductCount=	$this->InventoryProduct->find('count', array(
			'fields'=>array('InventoryProduct.id'),
		));
		
		$this->Paginator->settings = array(
			'contain'=>array(		
				'Currency',
				'InventoryProductLine',
			),
			'limit'=>($inventoryProductCount!=0?$inventoryProductCount:1),
		);

		$inventoryProducts = $this->Paginator->paginate('InventoryProduct');
		
		if (!empty($inventoryProducts)){
			$this->loadModel('ExchangeRate');
			$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate(date('Y-m-d'));
			$rate=$exchangeRate['ExchangeRate']['rate'];
			for ($i=0;$i<count($inventoryProducts);$i++){
				if ($inventoryProducts[$i]['Currency']['id']!=$currencyId){
					if ($currencyId==CURRENCY_USD){
						$inventoryProducts[$i]['InventoryProduct']['product_unit_price_A']=round($inventoryProducts[$i]['InventoryProduct']['product_unit_price_A']/$rate,2);
						$inventoryProducts[$i]['InventoryProduct']['product_unit_price_B']=round($inventoryProducts[$i]['InventoryProduct']['product_unit_price_B']/$rate,2);
						$inventoryProducts[$i]['InventoryProduct']['product_unit_price_C']=round($inventoryProducts[$i]['InventoryProduct']['product_unit_price_C']/$rate,2);
						$inventoryProducts[$i]['InventoryProduct']['product_unit_cost']=round($inventoryProducts[$i]['InventoryProduct']['product_unit_cost']/$rate,2);
					}
					else {
						$inventoryProducts[$i]['InventoryProduct']['product_unit_price_A']=round($inventoryProducts[$i]['InventoryProduct']['product_unit_price_A']*$rate,2);
						$inventoryProducts[$i]['InventoryProduct']['product_unit_price_B']=round($inventoryProducts[$i]['InventoryProduct']['product_unit_price_B']*$rate,2);
						$inventoryProducts[$i]['InventoryProduct']['product_unit_price_C']=round($inventoryProducts[$i]['InventoryProduct']['product_unit_price_C']*$rate,2);
						$inventoryProducts[$i]['InventoryProduct']['product_unit_cost']=round($inventoryProducts[$i]['InventoryProduct']['product_unit_cost']*$rate,2);
					}
				}
			}
		}
		
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
		$aco_name="InventoryProductLines/index";		
		$bool_inventoryproductline_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_index_permission'));
		$aco_name="InventoryProductLines/add";		
		$bool_inventoryproductline_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_add_permission'));
		
	}
	
	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->InventoryProduct->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}
		
		$options = array('conditions' => array('InventoryProduct.id' => $id));
		$this->set('inventoryProduct', $this->InventoryProduct->find('first', $options));
		
		$inventoryProviderList=$this->InventoryProduct->InventoryProductInventoryProvider->find('list',array(
			'fields'=>array('InventoryProductInventoryProvider.inventory_provider_id'),
			'conditions'=>array(
				'InventoryProductInventoryProvider.inventory_product_id'=>$id,
			),
		));
		$this->loadModel('InventoryProvider');
		$this->InventoryProvider->recursive=-1;
		$inventoryProviders=$this->InventoryProvider->find('all',array(
			'conditions'=>array(
				'InventoryProvider.id'=>$inventoryProviderList,
			),
		));
		$this->set(compact('inventoryProviders'));
		
		$aco_name="InventoryProviders/index";		
		$bool_inventoryprovider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_index_permission'));
		$aco_name="InventoryProviders/add";		
		$bool_inventoryprovider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_add_permission'));
		$aco_name="InventoryProductLines/index";		
		$bool_inventoryproductline_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_index_permission'));
		$aco_name="InventoryProductLines/add";		
		$bool_inventoryproductline_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_add_permission'));
	}

	public function add() {
		$this->loadModel('InventoryProvider');
		$this->InventoryProvider->recursive=-1;
		$inventoryProviders = $this->InventoryProvider->find('all',array(
			'fields'=>array('InventoryProvider.id','InventoryProvider.name'),
			'conditions'=>array(
				'InventoryProvider.bool_active'=>true,
			),
		));
		
		if ($this->request->is('post')) {
			$datasource=$this->InventoryProduct->getDataSource();
			try {
				$datasource->begin();
				$inventory_product_id=0;
				if (!empty($this->request->data['Document']['url_image'][0]['tmp_name'])){
					$imageOK=$this->uploadFiles('inventoryproductimages/'.$inventory_product_id,$this->request->data['Document']['url_image']);
					//echo "image OK<br/>";
					//pr($imageOK);
					if (array_key_exists('urls',$imageOK)){
						$this->request->data['InventoryProduct']['url_image']=$imageOK['urls'][0];
					}
				}
				$this->InventoryProduct->create();
				if (!$this->InventoryProduct->save($this->request->data)) {
					echo "Problema guardando el producto";
					pr($this->validateErrors($this->InventoryProduct));
					throw new Exception();
				}
				$inventory_product_id=$this->InventoryProduct->id;
				for ($pr=0;$pr<count($this->request->data['InventoryProvider']);$pr++){
					if ($this->request->data['InventoryProvider'][$pr]['inventory_provider_id']){
						$inventoryProductInventoryProviderArray=array();
						$this->InventoryProduct->InventoryProductInventoryProvider->create();
						$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_product_id']=$inventory_product_id;
						$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_provider_id']=$inventoryProviders[$pr]['InventoryProvider']['id'];
						if (!$this->InventoryProduct->InventoryProductInventoryProvider->save($inventoryProductInventoryProviderArray)){
							echo "Problema guardando el proveedor para el producto";
							pr($this->validateErrors($this->InventoryProductInventoryProvider));
							throw new Exception();
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->InventoryProduct->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se creó el producto ".$this->request->data['InventoryProduct']['name']);
				$this->Session->setFlash(__('The product has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The product could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		$inventoryProductLines = $this->InventoryProduct->InventoryProductLine->find('list');
		$currencies = $this->InventoryProduct->Currency->find('list');
		$measuringUnits = $this->InventoryProduct->MeasuringUnit->find('list');
		
		//pr($inventoryProviders);
		$this->set(compact('inventoryProductLines', 'currencies', 'inventoryProviders','measuringUnits'));
		
		$aco_name="InventoryProviders/index";		
		$bool_inventoryprovider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_index_permission'));
		$aco_name="InventoryProviders/add";		
		$bool_inventoryprovider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_add_permission'));
		$aco_name="InventoryProductLines/index";		
		$bool_inventoryproductline_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_index_permission'));
		$aco_name="InventoryProductLines/add";		
		$bool_inventoryproductline_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->InventoryProduct->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}
		$this->loadModel('InventoryProvider');
		$this->InventoryProvider->recursive=-1;
		$inventoryProviders = $this->InventoryProvider->find('all',array(
			'fields'=>array('InventoryProvider.id','InventoryProvider.name'),
			'conditions'=>array(
				'InventoryProvider.bool_active'=>true,
			),
			'contain'=>array(
				'InventoryProductInventoryProvider'=>array(
					'conditions'=>array(
						'InventoryProductInventoryProvider.inventory_product_id'=>$id,
					)
				),
			),
		));
		if ($this->request->is(array('post', 'put'))) {
			$datasource=$this->InventoryProduct->getDataSource();
			try {
				$datasource->begin();
				
				$this->InventoryProduct->InventoryProductInventoryProvider->recursive=-1;
				$previousInventoryProductInventoryProviders=$this->InventoryProduct->InventoryProductInventoryProvider->find('all',array(
					'fields'=>array('InventoryProductInventoryProvider.id'),
					'conditions'=>array(
						'InventoryProductInventoryProvider.inventory_product_id'=>$id,
					),
				));
				if (!empty($previousInventoryProductInventoryProviders)){
					foreach ($previousInventoryProductInventoryProviders as $previousInventoryProductInventoryProvider){
						$this->InventoryProduct->InventoryProductInventoryProvider->id=$previousInventoryProductInventoryProvider['InventoryProductInventoryProvider']['id'];
						$this->InventoryProduct->InventoryProductInventoryProvider->delete($previousInventoryProductInventoryProvider['InventoryProductInventoryProvider']['id']);
					}
				}
				
				$inventory_product_id=$id;
				if (!empty($this->request->data['Document']['url_image'][0]['tmp_name'])){
					$imageOK=$this->uploadFiles('inventoryproductimages/'.$inventory_product_id,$this->request->data['Document']['url_image']);
					if (array_key_exists('urls',$imageOK)){
						$this->request->data['InventoryProduct']['url_image']=$imageOK['urls'][0];
					}
				}
				$this->InventoryProduct->id=$id;
				if (!$this->InventoryProduct->save($this->request->data)) {
					echo "Problema guardando el producto";
					pr($this->validateErrors($this->InventoryProduct));
					throw new Exception();
				}
				
				$inventory_product_id=$this->InventoryProduct->id;
				//pr($this->request->data);
				for ($pr=0;$pr<count($this->request->data['InventoryProvider']);$pr++){
					if ($this->request->data['InventoryProvider'][$pr]['inventory_provider_id']){
						$inventoryProductInventoryProviderArray=array();
						$this->InventoryProduct->InventoryProductInventoryProvider->create();
						$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_product_id']=$inventory_product_id;
						$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_provider_id']=$inventoryProviders[$pr]['InventoryProvider']['id'];
						if (!$this->InventoryProduct->InventoryProductInventoryProvider->save($inventoryProductInventoryProviderArray)){
							echo "Problema guardando el proveedor para el cliente";
							pr($this->validateErrors($this->InventoryProductInventoryProvider));
							throw new Exception();
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->InventoryProduct->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se creó el producto ".$this->request->data['InventoryProduct']['name']);
				$this->Session->setFlash(__('The product has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The product could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} 
		else {
			//$this->InventoryProduct->recursive=-1;
			$options = array('conditions' => array('InventoryProduct.' . $this->InventoryProduct->primaryKey => $id));
			$this->request->data = $this->InventoryProduct->find('first', $options);
		}
		$inventoryProductLines = $this->InventoryProduct->InventoryProductLine->find('list');
		$currencies = $this->InventoryProduct->Currency->find('list');
		$measuringUnits = $this->InventoryProduct->MeasuringUnit->find('list');
		$this->set(compact('inventoryProductLines', 'currencies', 'inventoryProviders','measuringUnits'));
		
		$aco_name="InventoryProviders/index";		
		$bool_inventoryprovider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_index_permission'));
		$aco_name="InventoryProviders/add";		
		$bool_inventoryprovider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryprovider_add_permission'));
		$aco_name="InventoryProductLines/index";		
		$bool_inventoryproductline_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_index_permission'));
		$aco_name="InventoryProductLines/add";		
		$bool_inventoryproductline_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproductline_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InventoryProduct->id = $id;
		if (!$this->InventoryProduct->exists()) {
			throw new NotFoundException(__('Invalid product'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$inventoryProduct=$this->InventoryProduct->find('first',array(
			'conditions'=>array(
				'InventoryProduct.id'=>$id,
			),
		));
		if ($this->InventoryProduct->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$inventoryProduct['InventoryProduct']['id'];
			$deletionArray['Deletion']['reference']=$inventoryProduct['InventoryProduct']['name'];
			$deletionArray['Deletion']['type']='InventoryProduct';
			$this->Deletion->save($deletionArray);
		
			$this->Session->setFlash(__('The product has been deleted.'),'default',array('class' => 'success'));
		} 
		else {
			$this->Session->setFlash(__('The product could not be deleted. Please, try again.'),'default',array('class' => 'error-message'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function getproductimage() {
		$this->layout = "ajax";
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$inventoryProductid=trim($_POST['productid']);
		if (!$inventoryProductid){
			throw new NotFoundException(__('Producto no presente'));
		}
		
		$this->InventoryProduct->recursive=-1;
		$inventoryProduct=$this->InventoryProduct->find('first',array(
			'conditions'=>array('InventoryProduct.id'=>$inventoryProductid,),
		));
		
		$this->set(compact('product'));
	}
	
	public function getproductprice() {
		//$this->layout = "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$inventoryProductid=trim($_POST['productid']);
		$currencyid=trim($_POST['currencyid']);
		$selectedday=trim($_POST['selectedday']);
		$selectedmonth=trim($_POST['selectedmonth']);
		$selectedyear=trim($_POST['selectedyear']);
		
		//if (!$inventoryProductid){
		//	throw new NotFoundException(__('InventoryProducto no presente'));
		//}
		
		$selectedDateString=$selectedyear.'-'.$selectedmonth.'-'.$selectedday;
		$selectedDate=date( "Y-m-d", strtotime($selectedDateString));
		
		$this->InventoryProduct->recursive=-1;
		$inventoryProduct=$this->InventoryProduct->find('first',array(
			'conditions'=>array('InventoryProduct.id'=>$inventoryProductid,),
		));
		//pr($inventoryProduct);
		$this->LoadModel('ExchangeRate');
		
		$inventoryProductUnitPrice=$inventoryProduct['InventoryProduct']['product_unit_price'];
		if ($currencyid==CURRENCY_USD){
			if ($inventoryProduct['InventoryProduct']['currency_id']==CURRENCY_CS){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
				if (!empty($exchangeRate)){
					$inventoryProductUnitPrice=round($inventoryProductUnitPrice/$exchangeRate['ExchangeRate']['rate'],2);
				}
			}
		}
		elseif ($currencyid==CURRENCY_CS){
			if ($inventoryProduct['InventoryProduct']['currency_id']==CURRENCY_USD){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
				if (!empty($exchangeRate)){
					$inventoryProductUnitPrice=round($inventoryProductUnitPrice*$exchangeRate['ExchangeRate']['rate'],2);
				}
			}
		}
		
		return $inventoryProductUnitPrice;
	}
	
	public function getproductsforinventoryproductline() {
		$this->layout = "ajax";
		
		$inventoryProductLineId=trim($_POST['inventoryproductlineid']);
		if (!$inventoryProductLineId){
			throw new NotFoundException(__('Línea de Producto no presente'));
		}
		
		$this->loadModel('InventoryProductLine');
		$inventoryProductLineConditions=array();
		if ($inventoryProductLineid>0){
			$inventoryProductLineConditions['InventoryProductLine.id']=$inventoryProductLineid;
		}
		$selectedInventoryProductLineList=$this->InventoryProductLine->find('list',array(
			'fields'=>array('InventoryProductLine.id'),
			'conditions'=>$inventoryProductLineConditions,
		));
		
		$inventoryProductsForInventoryProductLine=$this->InventoryProduct->find('all',array(
			'fields'=>array(
				'InventoryProduct.id','InventoryProduct.name',
			),
			'conditions'=>array(
				'InventoryProduct.inventory_product_line_id'=>$selectedInventoryProductLineList,
			),
		));
		//pr($inventoryProductsForInventoryProductLine);
		$this->set(compact('inventoryProductsForInventoryProductLine'));
	}

	public function getproductsforinventoryproductlinelisted() {
		$this->layout = "ajax";
		
		$inventoryProductLineId=trim($_POST['inventoryproductlineid']);
		if (!$inventoryProductLineId){
			throw new NotFoundException(__('Línea de Producto no presente'));
		}
		
		$this->loadModel('InventoryProductLine');
		$inventoryProductLineConditions=array();
		if ($inventoryProductLineId>0){
			$inventoryProductLineConditions['InventoryProductLine.id']=$inventoryProductLineId;
		}
		$selectedInventoryProductLineList=$this->InventoryProductLine->find('list',array(
			'fields'=>array('InventoryProductLine.id'),
			'conditions'=>$inventoryProductLineConditions,
		));
		
		$inventoryProductsForInventoryProductLine=$this->InventoryProduct->find('all',array(
			'fields'=>array(
				'InventoryProduct.id','InventoryProduct.name',
			),
			'conditions'=>array(
				'InventoryProduct.inventory_product_line_id'=>$selectedInventoryProductLineList,
			),
		));
		//pr($inventoryProductsForInventoryProductLine);
		$this->set(compact('inventoryProductsForInventoryProductLine'));
	}
	
	public function getproductmeasuringunitid() {
		//$this->layout = "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$inventoryProductid=trim($_POST['productid']);
		
		$this->InventoryProduct->recursive=-1;
		$inventoryProduct=$this->InventoryProduct->find('first',array(
			'conditions'=>array('InventoryProduct.id'=>$inventoryProductid,),
			'contain'=>array(
				'MeasuringUnit',
			),
		));
		
		return $inventoryProduct['MeasuringUnit']['abbreviation'];
	}

	public function getproductwidth() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$inventoryProductid=trim($_POST['productid']);
		
		$this->InventoryProduct->recursive=-1;
		$inventoryProduct=$this->InventoryProduct->find('first',array(
			'conditions'=>array('InventoryProduct.id'=>$inventoryProductid,),
		));
		
		return $inventoryProduct['InventoryProduct']['width'];
	}

	public function getproductstatsfordate() {
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$selectedday=trim($_POST['selectedday']);
		$selectedmonth=trim($_POST['selectedmonth']);
		$selectedyear=trim($_POST['selectedyear']);
		$remissionid=0;
		if (!empty($_POST['remissionid'])){
			$remissionid=trim($_POST['remissionid']);
		}
		$remission=array();
		if ($remissionid>0){
			$this->loadModel('Remission');
			$remission = $this->Remission->find('first', array(
				'conditions' => array(
					'Remission.id' => $remissionid,
				),
				'contain'=>array(
					'StockMovement',
				),
			));
		}
		$selectedDateString=$selectedyear.'-'.$selectedmonth.'-'.$selectedday;
		$selectedDate=date( "Y-m-d", strtotime($selectedDateString));
		
		$this->InventoryProduct->recursive=-1;
		$allInventoryProducts=$this->InventoryProduct->find('all',array(
			'conditions'=>array(
				'bool_active'=>true,
			),
			'order'=>'InventoryProduct.name',
		));
		
		$this->loadModel('StockItemLog');
		for ($ip=0;$ip<count($allInventoryProducts);$ip++){
			$quantityInStock=$this->StockItemLog->getQuantityInStock($allInventoryProducts[$ip]['InventoryProduct']['id'],$selectedDate);
			$allInventoryProducts[$ip]['InventoryProduct']['stock_quantity']=$quantityInStock;
		}
		//pr($allInventoryProducts);
		
		$inventoryProducIds=array();
		$inventoryProducts=array();
		$inventoryProductStockQuantities=array();
		$inventoryProductLines=array();
		
		for ($ip=0;$ip<count($allInventoryProducts);$ip++){
			if(!empty($remission)){
				foreach ($remission['StockMovement'] as $stockMovement){
					if ($stockMovement['inventory_product_id']){
						if ($stockMovement['inventory_product_id']==$allInventoryProducts[$ip]['InventoryProduct']['id']){
							$allInventoryProducts[$ip]['InventoryProduct']['stock_quantity']+=$stockMovement['product_quantity'];
							break;
						}
					}
				}
			}
			if ($allInventoryProducts[$ip]['InventoryProduct']['stock_quantity']>0){
				$inventoryProductIds[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['id'];
				$inventoryProducts[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['name'];
				$inventoryProductStockQuantities[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['stock_quantity'];
				$inventoryProductLines[$allInventoryProducts[$ip]['InventoryProduct']['id']]=$allInventoryProducts[$ip]['InventoryProduct']['inventory_product_line_id'];
			}
		}
		
		$productOptions="<option value='0'>Seleccione Producto</option>";
		//pr($products);
		if (!empty($inventoryProducts)){
			foreach ($inventoryProducts as $key=>$value){
				$productOptions.="<option value='".$key."'>".$value."</option>";
			}
		}
		
		$stockQuantityOptions="<option value='0'>Cantidad</option>";
		//pr($products);
		if (!empty($inventoryProductStockQuantities)){
			foreach ($inventoryProductStockQuantities as $key=>$value){
				$stockQuantityOptions.="<option value='".$key."'>".$value."</option>";
			}
		}
		
		$productLineOptions="<option value='0'>Línea de Producto</option>";
		//pr($products);
		if (!empty($inventoryProductLines)){
			foreach ($inventoryProductLines as $key=>$value){
				$productLineOptions.="<option value='".$key."'>".$value."</option>";
			}
		}
		
		echo json_encode(array('products'=>$productOptions,'stockquantities'=>$stockQuantityOptions,'productlines'=>$productLineOptions,'productids'=>$inventoryProductIds));
	}
}
