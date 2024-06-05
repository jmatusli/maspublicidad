<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');


class ProductsController extends AppController {

	public $components = array('Paginator');
	public $helpers = array('PhpExcel');
	
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('saveproduct','getproductimage','getproductprice','getproductinfo','getproductsfordepartment','getproductsfordepartmentlisted');		
	}

	public function saveproduct() {
		$this->layout = "ajax";
		
		// when data are passed with serialize, the ajax parameter processdata must be true, if not the values don't get parsed correctly
		//var_dump($_POST);
		//$productArray1=$_POST['data'];
		//pr ($productArray1);
		$productArray=$_POST['data'];
		//pr ($productArray);
		$filesArray=$_FILES['data'];
		//pr ($filesArray);
		
		$datasource=$this->Product->getDataSource();
		$datasource->begin();
		try {
			// load providers
			$this->loadModel('Provider');
			$this->Provider->recursive=-1;
			$providers = $this->Provider->find('all',array(
				'fields'=>array('Provider.id','Provider.name'),
				'conditions'=>array(
					'Provider.bool_active'=>true,
				),
			));
		
			
			$product_id=0;
			if (!empty($filesArray['tmp_name']['Document'])){
				$cleanFilesArray=array();
				foreach ($filesArray as $key =>$value){
					$cleanFilesArray[0][$key]=$filesArray[$key]['Document']['url_image'];
				}
				//pr($cleanFilesArray);
				$imageOK=$this->uploadFiles('productimages/'.$product_id,$cleanFilesArray);
				//echo "image OK<br/>";
				//pr($imageOK);
				
				if (array_key_exists('urls',$imageOK)){
					$productArray['Product']['url_image']=$imageOK['urls'][0];
				}
			}
			
			$this->Product->create();
			if (!$this->Product->save($productArray)) {
				echo "Problema guardando el producto";
				pr($this->validateErrors($this->Product));
				throw new Exception();
			}
			$product_id=$this->Product->id;
			for ($pr=0;$pr<count($productArray['Provider']);$pr++){
				if ($productArray['Provider'][$pr]['provider_id']){
					$productProviderArray=array();
					$this->Product->ProductProvider->create();
					$productProviderArray['ProductProvider']['product_id']=$product_id;
					$productProviderArray['ProductProvider']['provider_id']=$providers[$pr]['Provider']['id'];
					if (!$this->Product->ProductProvider->save($productProviderArray)){
						echo "Problema guardando el proveedor para el producto";
						pr($this->validateErrors($this->ProductProvider));
						throw new Exception();
					}
				}
			}
			$datasource->commit();
			$this->recordUserAction($this->Product->id,null,null);
			$this->recordUserActivity($this->Session->read('User.username'),"Se creó el producto ".$this->request->data['Product']['name']);
			$this->Session->setFlash(__('The product has been saved.'),'default',array('class' => 'success'));
			//return $this->redirect(array('action' => 'index'));
		} 
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			$this->Session->setFlash(__('The product could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
		}
				
		$products=$this->Product->find('all',array(
			'fields'=>array(
				'Product.id','Product.name',
			),
			'order'=>'Product.name',
		));
		//pr($products);
		$this->set(compact('products'));
	}
	
	public function index() {
		$this->Product->recursive = -1;
		
		$currencyId=CURRENCY_USD;
		if ($this->request->is('post')) {
			$currencyId=$this->request->data['Report']['currency_id'];
		}
		$this->set(compact('currencyId'));
		
		$productCount=	$this->Product->find('count', array(
			'fields'=>array('Product.id'),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(		
				'Currency',
				'Department',
			),
			'order'=>'Department.name, Product.name',
			'limit'=>($productCount!=0?$productCount:1),
		);

		$products = $this->Paginator->paginate('Product');
		
		if (!empty($products)){
			$this->loadModel('ExchangeRate');
			$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate(date('Y-m-d'));
			$rate=$exchangeRate['ExchangeRate']['rate'];
			for ($i=0;$i<count($products);$i++){
				if ($products[$i]['Currency']['id']!=$currencyId){
					if ($currencyId==CURRENCY_USD){
						$products[$i]['Product']['product_unit_price']=round($products[$i]['Product']['product_unit_price']/$rate,2);
						$products[$i]['Product']['product_unit_cost']=round($products[$i]['Product']['product_unit_cost']/$rate,2);
					}
					else {
						$products[$i]['Product']['product_unit_price']=round($products[$i]['Product']['product_unit_price']*$rate,2);
						$products[$i]['Product']['product_unit_cost']=round($products[$i]['Product']['product_unit_cost']*$rate,2);
					}
				}
			}
		}
		
		$this->set(compact('products'));
		
		$this->loadModel('Currency');
		$currencies=$this->Currency->find('list');
		$this->set(compact('currencies'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
		
	}
	
	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->Product->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}
		
		$options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
		$this->set('product', $this->Product->find('first', $options));
		
		$providerList=$this->Product->ProductProvider->find('list',array(
			'fields'=>array('ProductProvider.provider_id'),
			'conditions'=>array(
				'ProductProvider.product_id'=>$id,
			),
		));
		$this->loadModel('Provider');
		$this->Provider->recursive=-1;
		$providers=$this->Provider->find('all',array(
			'conditions'=>array(
				'Provider.id'=>$providerList,
			),
		));
		$this->set(compact('providers'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function add() {
		$this->loadModel('Provider');
		$this->Provider->recursive=-1;
		$providers = $this->Provider->find('all',array(
			'fields'=>array('Provider.id','Provider.name'),
			'conditions'=>array(
				'Provider.bool_active'=>true,
			),
		));
		
		if ($this->request->is('post')) {
			$datasource=$this->Product->getDataSource();
			$datasource->begin();
			try {
				
				$this->Product->create();
				if (!$this->Product->save($this->request->data)) {
					echo "Problema guardando el producto";
					pr($this->validateErrors($this->Product));
					throw new Exception();
				}
				$product_id=$this->Product->id;
				if (!empty($this->request->data['Document']['url_image'][0]['tmp_name'])){
					$imageOK=$this->uploadFiles('productimages/'.$product_id,$this->request->data['Document']['url_image']);
					//echo "image OK<br/>";
					//pr($imageOK);
					if (array_key_exists('urls',$imageOK)){
						$this->request->data['Product']['url_image']=$imageOK['urls'][0];
					}
				}
				if (!$this->Product->save($this->request->data)) {
					echo "Problema guardando el producto con su imagen";
					pr($this->validateErrors($this->Product));
					throw new Exception();
				}
				for ($pr=0;$pr<count($this->request->data['Provider']);$pr++){
					if ($this->request->data['Provider'][$pr]['provider_id']){
						$productProviderArray=array();
						$this->Product->ProductProvider->create();
						$productProviderArray['ProductProvider']['product_id']=$product_id;
						$productProviderArray['ProductProvider']['provider_id']=$providers[$pr]['Provider']['id'];
						if (!$this->Product->ProductProvider->save($productProviderArray)){
							echo "Problema guardando el proveedor para el producto";
							pr($this->validateErrors($this->ProductProvider));
							throw new Exception();
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->Product->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se creó el producto ".$this->request->data['Product']['name']);
				$this->Session->setFlash(__('The product has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The product could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		$departments = $this->Product->Department->find('list');
		$currencies = $this->Product->Currency->find('list');
		
		//pr($providers);
		$this->set(compact('departments', 'currencies', 'providers'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->Product->exists($id)) {
			throw new NotFoundException(__('Invalid product'));
		}
		$this->loadModel('Provider');
		$this->Provider->recursive=-1;
		$providers = $this->Provider->find('all',array(
			'fields'=>array('Provider.id','Provider.name'),
			'conditions'=>array(
				'Provider.bool_active'=>true,
			),
			'contain'=>array(
				'ProductProvider'=>array(
					'conditions'=>array(
						'ProductProvider.product_id'=>$id,
					)
				),
			),
		));
		if ($this->request->is(array('post', 'put'))) {
			$datasource=$this->Product->getDataSource();
			$datasource->begin();
			try {
				$this->Product->ProductProvider->recursive=-1;
				$previousProductProviders=$this->Product->ProductProvider->find('all',array(
					'fields'=>array('ProductProvider.id'),
					'conditions'=>array(
						'ProductProvider.product_id'=>$id,
					),
				));
				if (!empty($previousProductProviders)){
					foreach ($previousProductProviders as $previousProductProvider){
						$this->Product->ProductProvider->id=$previousProductProvider['ProductProvider']['id'];
						$this->Product->ProductProvider->delete($previousProductProvider['ProductProvider']['id']);
					}
				}
				
				$product_id=$id;
				if (!empty($this->request->data['Document']['url_image'][0]['tmp_name'])){
					$imageOK=$this->uploadFiles('productimages/'.$product_id,$this->request->data['Document']['url_image']);
					if (array_key_exists('urls',$imageOK)){
						$this->request->data['Product']['url_image']=$imageOK['urls'][0];
					}
				}
				$this->Product->id=$id;
				if (!$this->Product->save($this->request->data)) {
					echo "Problema guardando el producto";
					pr($this->validateErrors($this->Product));
					throw new Exception();
				}
				
				$product_id=$this->Product->id;
				//pr($this->request->data);
				for ($pr=0;$pr<count($this->request->data['Provider']);$pr++){
					if ($this->request->data['Provider'][$pr]['provider_id']){
						$productProviderArray=array();
						$this->Product->ProductProvider->create();
						$productProviderArray['ProductProvider']['product_id']=$product_id;
						$productProviderArray['ProductProvider']['provider_id']=$providers[$pr]['Provider']['id'];
						if (!$this->Product->ProductProvider->save($productProviderArray)){
							echo "Problema guardando el proveedor para el cliente";
							pr($this->validateErrors($this->ProductProvider));
							throw new Exception();
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->Product->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se creó el producto ".$this->request->data['Product']['name']);
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
			//$this->Product->recursive=-1;
			$options = array('conditions' => array('Product.' . $this->Product->primaryKey => $id));
			$this->request->data = $this->Product->find('first', $options);
		}
		$departments = $this->Product->Department->find('list');
		$currencies = $this->Product->Currency->find('list');
		
		$this->set(compact('departments', 'currencies', 'providers'));
		
		$aco_name="Providers/index";		
		$bool_provider_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_index_permission'));
		$aco_name="Providers/add";		
		$bool_provider_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_provider_add_permission'));
		$aco_name="Departments/index";		
		$bool_department_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_index_permission'));
		$aco_name="Departments/add";		
		$bool_department_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_department_add_permission'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Product->id = $id;
		if (!$this->Product->exists()) {
			throw new NotFoundException(__('Invalid product'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Product->delete()) {
			$this->Session->setFlash(__('The product has been deleted.'));
		} else {
			$this->Session->setFlash(__('The product could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
	
	public function getproductimage() {
		$this->layout = "ajax";
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$productid=trim($_POST['productid']);
		if (!$productid){
			throw new NotFoundException(__('Producto no presente'));
		}
		
		$this->Product->recursive=-1;
		$product=$this->Product->find('first',array(
			'conditions'=>array('Product.id'=>$productid,),
		));
		
		$this->set(compact('product'));
	}
	
	public function getproductprice() {
		//$this->layout = "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$productid=trim($_POST['productid']);
		$currencyid=trim($_POST['currencyid']);
		$quotationdateday=trim($_POST['quotationdateday']);
		$quotationdatemonth=trim($_POST['quotationdatemonth']);
		$quotationdateyear=trim($_POST['quotationdateyear']);
		
		//if (!$productid){
		//	throw new NotFoundException(__('Producto no presente'));
		//}
		
		$quotationDateString=$quotationdateyear.'-'.$quotationdatemonth.'-'.$quotationdateday;
		$quotationDate=date( "Y-m-d", strtotime($quotationDateString));
		
		$this->Product->recursive=-1;
		$product=$this->Product->find('first',array(
			'conditions'=>array('Product.id'=>$productid,),
		));
		//pr($product);
		$this->LoadModel('ExchangeRate');
		
		$productUnitPrice=$product['Product']['product_unit_price'];
		if ($currencyid==CURRENCY_USD){
			if ($product['Product']['currency_id']==CURRENCY_CS){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
				if (!empty($exchangeRate)){
					$productUnitPrice=round($productUnitPrice/$exchangeRate['ExchangeRate']['rate'],2);
				}
			}
		}
		elseif ($currencyid==CURRENCY_CS){
			if ($product['Product']['currency_id']==CURRENCY_USD){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($quotationDate);
				if (!empty($exchangeRate)){
					$productUnitPrice=round($productUnitPrice*$exchangeRate['ExchangeRate']['rate'],2);
				}
			}
		}
		
		return $productUnitPrice;
	}
	
	public function getproductinfo() {
		//$this->layout = "ajax";
		$this->autoRender = false; // We don't render a view in this example    
		$this->request->onlyAllow('ajax'); // No direct access via browser URL
		
		$productid=trim($_POST['productid']);
		$currencyid=trim($_POST['currencyid']);
		$dateday=trim($_POST['dateday']);
		$datemonth=trim($_POST['datemonth']);
		$dateyear=trim($_POST['dateyear']);
		
		$dateString=$dateyear.'-'.$datemonth.'-'.$dateday;
		$relevantDate=date( "Y-m-d", strtotime($dateString));
		
		$this->Product->recursive=-1;
		$product=$this->Product->find('first',array(
			'conditions'=>array('Product.id'=>$productid,),
		));
		
		$this->LoadModel('ExchangeRate');
		$productUnitPrice=$product['Product']['product_unit_price'];
		if ($currencyid==CURRENCY_USD){
			if ($product['Product']['currency_id']==CURRENCY_CS){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($relevantDate);
				if (!empty($exchangeRate)){
					$productUnitPrice=round($productUnitPrice/$exchangeRate['ExchangeRate']['rate'],2);
				}
			}
		}
		elseif ($currencyid==CURRENCY_CS){
			if ($product['Product']['currency_id']==CURRENCY_USD){
				$exchangeRate=$this->ExchangeRate->getApplicableExchangeRate($relevantDate);
				if (!empty($exchangeRate)){
					$productUnitPrice=round($productUnitPrice*$exchangeRate['ExchangeRate']['rate'],2);
				}
			}
		}
		$product['Product']['calculated_unit_price']=$productUnitPrice;
		echo json_encode($product);
	}
	
	public function getproductsfordepartment() {
		$this->layout = "ajax";
		
		$departmentid=trim($_POST['departmentid']);
		if (!$departmentid){
			throw new NotFoundException(__('Departamento no presente'));
		}
		
		$this->loadModel('Department');
		$departmentConditions=array();
		if ($departmentid>0){
			$departmentConditions['Department.id']=$departmentid;
		}
		$selectedDepartmentList=$this->Department->find('list',array(
			'fields'=>array('Department.id'),
			'conditions'=>$departmentConditions,
		));
		
		$productsForDepartment=$this->Product->find('all',array(
			'fields'=>array(
				'Product.id','Product.name',
			),
			'conditions'=>array(
				'Product.department_id'=>$selectedDepartmentList,
			),
		));
		//pr($productsForDepartment);
		$this->set(compact('productsForDepartment'));
	}

	public function getproductsfordepartmentlisted() {
		$this->layout = "ajax";
		
		$departmentid=trim($_POST['departmentid']);
		if (!$departmentid){
			throw new NotFoundException(__('Departamento no presente'));
		}
		
		$this->loadModel('Department');
		$departmentConditions=array();
		if ($departmentid>0){
			$departmentConditions['Department.id']=$departmentid;
		}
		$selectedDepartmentList=$this->Department->find('list',array(
			'fields'=>array('Department.id'),
			'conditions'=>$departmentConditions,
		));
		
		$productsForDepartment=$this->Product->find('all',array(
			'fields'=>array(
				'Product.id','Product.name',
			),
			'conditions'=>array(
				'Product.department_id'=>$selectedDepartmentList,
			),
		));
		//pr($productsForDepartment);
		$this->set(compact('productsForDepartment'));
	}
}
