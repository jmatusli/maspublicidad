<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class InventoryProvidersController extends AppController {

	public $components = array('Paginator');
	public $helpers = array('PhpExcel');

	public function index() {
		$this->InventoryProvider->recursive = -1;
		
		$inventoryProviderCount=	$this->InventoryProvider->find('count', array(
			'fields'=>array('InventoryProvider.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($inventoryProviderCount!=0?$inventoryProviderCount:1),
		);

		$inventoryProviders = $this->Paginator->paginate('InventoryProvider');
		$this->set(compact('inventoryProviders'));
		
		$aco_name="InventoryProducts/index";		
		$bool_inventoryproduct_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_index_permission'));
		$aco_name="InventoryProducts/add";		
		$bool_inventoryproduct_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_add_permission'));
		
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->InventoryProvider->exists($id)) {
			throw new NotFoundException(__('Invalid provider'));
		}
		
		$options = array('conditions' => array('InventoryProvider.' . $this->InventoryProvider->primaryKey => $id));
		$this->set('inventoryProvider', $this->InventoryProvider->find('first', $options));
		
		$inventoryInventoryProductList=$this->InventoryProvider->InventoryProductInventoryProvider->find('list',array(
			'fields'=>array('InventoryProductInventoryProvider.inventory_product_id'),
			'conditions'=>array(
				'InventoryProductInventoryProvider.inventory_provider_id'=>$id,
			),
		));
		$this->loadModel('InventoryProduct');
		$this->InventoryProduct->recursive=-1;
		$inventoryProducts=$this->InventoryProduct->find('all',array(
			'conditions'=>array(
				'InventoryProduct.id'=>$inventoryInventoryProductList,
			),
			'contain'=>array(
				'InventoryProductLine'=>array(
					'fields'=>array(
						'InventoryProductLine.id','InventoryProductLine.name',
					),
				),
			),
		));
		$this->set(compact('inventoryProducts'));
		
		$aco_name="InventoryProducts/index";		
		$bool_inventoryproduct_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_index_permission'));
		$aco_name="InventoryProducts/add";		
		$bool_inventoryproduct_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_add_permission'));
	}

	public function add() {
		$this->loadModel('InventoryProduct');
		$this->InventoryProduct->recursive=-1;
		$inventoryProducts = $this->InventoryProduct->find('all',array(
			'fields'=>array('InventoryProduct.id','InventoryProduct.name','InventoryProduct.code'),
			'contain'=>array(
				'InventoryProductLine'=>array(
					'fields'=>array(
						'InventoryProductLine.id','InventoryProductLine.name',
					),
				),
			),
		));
		$this->set(compact('inventoryProducts'));
		
		if ($this->request->is('post')) {
			$datasource=$this->InventoryProvider->getDataSource();
			$datasource->begin();
			try {
				$inventoryProduct_id=0;
				
				$this->InventoryProvider->create();
				if (!$this->InventoryProvider->save($this->request->data)) {
					echo "Problema guardando el proveedor";
					pr($this->validateErrors($this->InventoryProvider));
					throw new Exception();
				}
				$inventory_provider_id=$this->InventoryProvider->id;
				for ($pr=0;$pr<count($this->request->data['InventoryProduct']);$pr++){
					if ($this->request->data['InventoryProduct'][$pr]['inventory_product_id']){
						
						$inventoryProductInventoryProviderArray=array();
						$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_product_id']=$inventoryProducts[$pr]['InventoryProduct']['id'];
						$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_provider_id']=$inventory_provider_id;
						//pr($inventoryProductInventoryProviderArray);
						$this->InventoryProvider->InventoryProductInventoryProvider->create();
						
						if (!$this->InventoryProvider->InventoryProductInventoryProvider->save($inventoryProductInventoryProviderArray)){
							echo "Problema guardando el producto para el proveedor";
							pr($this->validateErrors($this->InventoryProductInventoryProvider));
							throw new Exception();
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->InventoryProvider->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se creó el proveedor ".$this->request->data['InventoryProvider']['name']);
				$this->Session->setFlash(__('The provider has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The provider could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		
		$aco_name="InventoryProducts/index";		
		$bool_inventoryproduct_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_index_permission'));
		$aco_name="InventoryProducts/add";		
		$bool_inventoryproduct_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->InventoryProvider->exists($id)) {
			throw new NotFoundException(__('Invalid provider'));
		}
		$this->loadModel('InventoryProduct');
		$this->InventoryProduct->recursive=-1;
		$inventoryProducts = $this->InventoryProduct->find('all',array(
			'fields'=>array('InventoryProduct.id','InventoryProduct.name','InventoryProduct.code'),
			'contain'=>array(
				'InventoryProductInventoryProvider'=>array(
					'conditions'=>array(
						'InventoryProductInventoryProvider.inventory_provider_id'=>$id,
					)
				),
				'InventoryProductLine'=>array(
					'fields'=>array(
						'InventoryProductLine.id','InventoryProductLine.name',
					),
				),
			),
		));
		$this->set(compact('inventoryProducts'));
		//pr($inventoryProducts);
		
		if ($this->request->is(array('post', 'put'))) {
			$datasource=$this->InventoryProvider->getDataSource();
			$datasource->begin();
			try {
				$this->InventoryProvider->InventoryProductInventoryProvider->recursive=-1;
				$previousInventoryProductInventoryProviders=$this->InventoryProvider->InventoryProductInventoryProvider->find('all',array(
					'fields'=>array('InventoryProductInventoryProvider.id'),
					'conditions'=>array(
						'InventoryProductInventoryProvider.inventory_provider_id'=>$id,
					),
				));
				if (!empty($previousInventoryProductInventoryProviders)){
					foreach ($previousInventoryProductInventoryProviders as $previousInventoryProductInventoryProvider){
						$this->InventoryProvider->InventoryProductInventoryProvider->id=$previousInventoryProductInventoryProvider['InventoryProductInventoryProvider']['id'];
						$this->InventoryProvider->InventoryProductInventoryProvider->delete($previousInventoryProductInventoryProvider['InventoryProductInventoryProvider']['id']);
					}
				}
				
				$this->InventoryProvider->id=$id;
				if (!$this->InventoryProvider->save($this->request->data)) {
					echo "Problema guardando el proveedor";
					pr($this->validateErrors($this->InventoryProvider));
					throw new Exception();
				}
				$inventory_provider_id=$this->InventoryProvider->id;
				//pr($this->request->data['InventoryProduct']);
				for ($pr=0;$pr<count($this->request->data['InventoryProduct']);$pr++){
					if ($this->request->data['InventoryProduct'][$pr]['inventory_product_id']){
						//echo "we have a winner for an associated product";
						//pr($this->request->data['InventoryProduct'][$pr]['inventory_product_id']);
						//pr($inventoryProducts);
						
						$inventoryProductInventoryProviderArray=array();
						$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_product_id']=$inventoryProducts[$pr]['InventoryProduct']['id'];
						$inventoryProductInventoryProviderArray['InventoryProductInventoryProvider']['inventory_provider_id']=$inventory_provider_id;
						//pr($inventoryProductInventoryProviderArray);
						$this->InventoryProvider->InventoryProductInventoryProvider->create();
						
						if (!$this->InventoryProvider->InventoryProductInventoryProvider->save($inventoryProductInventoryProviderArray)){
							echo "Problema guardando el producto para el proveedor";
							pr($this->validateErrors($this->InventoryProductInventoryProvider));
							throw new Exception();
						}
						//echo "saved the product";
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->InventoryProvider->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se creó el proveedor ".$this->request->data['InventoryProvider']['name']);
				$this->Session->setFlash(__('The provider has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The provider could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} 
		else {
			$options = array('conditions' => array('InventoryProvider.' . $this->InventoryProvider->primaryKey => $id));
			$this->request->data = $this->InventoryProvider->find('first', $options);
		}
		
		$aco_name="InventoryProducts/index";		
		$bool_inventoryproduct_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_index_permission'));
		$aco_name="InventoryProducts/add";		
		$bool_inventoryproduct_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_inventoryproduct_add_permission'));
		
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InventoryProvider->id = $id;
		if (!$this->InventoryProvider->exists()) {
			throw new NotFoundException(__('Invalid provider'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$inventoryProvider=$this->InventoryProvider->find('first',array(
			'conditions'=>array(
				'InventoryProvider.id'=>$id,
			),
		));
		
		if ($this->InventoryProvider->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$inventoryProvider['InventoryProvider']['id'];
			$deletionArray['Deletion']['reference']=$inventoryProvider['InventoryProvider']['name'];
			$deletionArray['Deletion']['type']='InventoryProvider';
			$this->Deletion->save($deletionArray);
		
			$this->Session->setFlash(__('The provider has been deleted.'));
		} 
		else {
			$this->Session->setFlash(__('The provider could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
