<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class ProvidersController extends AppController {

	public $components = array('Paginator');
	public $helpers = array('PhpExcel');

	public function index() {
		$this->Provider->recursive = -1;
		
		$providerCount=	$this->Provider->find('count', array(
			'fields'=>array('Provider.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($providerCount!=0?$providerCount:1),
		);

		$providers = $this->Paginator->paginate('Provider');
		$this->set(compact('providers'));
		
		$aco_name="Products/index";		
		$bool_product_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_product_index_permission'));
		$aco_name="Products/add";		
		$bool_product_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_product_add_permission'));
		
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->Provider->exists($id)) {
			throw new NotFoundException(__('Invalid provider'));
		}
		/*
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
		*/
		$options = array('conditions' => array('Provider.' . $this->Provider->primaryKey => $id));
		$this->set('provider', $this->Provider->find('first', $options));
		
		$productList=$this->Provider->ProductProvider->find('list',array(
			'fields'=>array('ProductProvider.product_id'),
			'conditions'=>array(
				'ProductProvider.provider_id'=>$id,
			),
		));
		$this->loadModel('Product');
		$this->Product->recursive=-1;
		$products=$this->Product->find('all',array(
			'conditions'=>array(
				'Product.id'=>$productList,
			),
		));
		$this->set(compact('products'));
		
		$aco_name="Products/index";		
		$bool_product_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_product_index_permission'));
		$aco_name="Products/add";		
		$bool_product_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_product_add_permission'));
	}

	public function add() {
		$this->loadModel('Product');
		$this->Product->recursive=-1;
		$products = $this->Product->find('all',array(
			'fields'=>array('Product.id','Product.name'),
		));
		$this->set(compact('products'));
		
		if ($this->request->is('post')) {
			$datasource=$this->Provider->getDataSource();
			$datasource->begin();
			try {
				$product_id=0;
				
				$this->Provider->create();
				if (!$this->Provider->save($this->request->data)) {
					echo "Problema guardando el proveedor";
					pr($this->validateErrors($this->Provider));
					throw new Exception();
				}
				$provider_id=$this->Provider->id;
				for ($pr=0;$pr<count($this->request->data['Product']);$pr++){
					if ($this->request->data['Product'][$pr]['product_id']){
						$productProviderArray=array();
						$this->Provider->ProductProvider->create();
						
						$productProviderArray['ProductProvider']['product_id']=$products[$pr]['Product']['id'];
						$productProviderArray['ProductProvider']['provider_id']=$provider_id;
						if (!$this->Provider->ProductProvider->save($productProviderArray)){
							echo "Problema guardando el producto para el proveedor";
							pr($this->validateErrors($this->ProductProvider));
							throw new Exception();
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->Provider->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se creó el proveedor ".$this->request->data['Provider']['name']);
				$this->Session->setFlash(__('The provider has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The provider could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		
		$aco_name="Products/index";		
		$bool_product_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_product_index_permission'));
		$aco_name="Products/add";		
		$bool_product_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_product_add_permission'));
	}

	public function edit($id = null) {
		if (!$this->Provider->exists($id)) {
			throw new NotFoundException(__('Invalid provider'));
		}
		$this->loadModel('Product');
		$this->Product->recursive=-1;
		$products = $this->Product->find('all',array(
			'fields'=>array('Product.id','Product.name'),
			'contain'=>array(
				'ProductProvider'=>array(
					'conditions'=>array(
						'ProductProvider.provider_id'=>$id,
					)
				),
			),
		));
		$this->set(compact('products'));
		
		if ($this->request->is(array('post', 'put'))) {
			$datasource=$this->Provider->getDataSource();
			$datasource->begin();
			try {
				$this->Provider->ProductProvider->recursive=-1;
				$previousProductProviders=$this->Provider->ProductProvider->find('all',array(
					'fields'=>array('ProductProvider.id'),
					'conditions'=>array(
						'ProductProvider.provider_id'=>$id,
					),
				));
				if (!empty($previousProductProviders)){
					foreach ($previousProductProviders as $previousProductProvider){
						$this->Provider->ProductProvider->id=$previousProductProvider['ProductProvider']['id'];
						$this->Provider->ProductProvider->delete($previousProductProvider['ProductProvider']['id']);
					}
				}
				
				$this->Provider->id=$id;
				if (!$this->Provider->save($this->request->data)) {
					echo "Problema guardando el proveedor";
					pr($this->validateErrors($this->Provider));
					throw new Exception();
				}
				$provider_id=$this->Provider->id;
				for ($pr=0;$pr<count($this->request->data['Product']);$pr++){
					if ($this->request->data['Product'][$pr]['product_id']){
						$productProviderArray=array();
						$this->Provider->ProductProvider->create();
						
						$productProviderArray['ProductProvider']['product_id']=$products[$pr]['Product']['id'];
						$productProviderArray['ProductProvider']['provider_id']=$provider_id;
						if (!$this->Provider->ProductProvider->save($productProviderArray)){
							echo "Problema guardando el producto para el proveedor";
							pr($this->validateErrors($this->Provider->ProductProvider));
							throw new Exception();
						}
					}
				}
				$datasource->commit();
				$this->recordUserAction($this->Provider->id,null,null);
				$this->recordUserActivity($this->Session->read('User.username'),"Se creó el proveedor ".$this->request->data['Provider']['name']);
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
			$options = array('conditions' => array('Provider.' . $this->Provider->primaryKey => $id));
			$this->request->data = $this->Provider->find('first', $options);
		}
		
		$aco_name="Products/index";		
		$bool_product_index_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_product_index_permission'));
		$aco_name="Products/add";		
		$bool_product_add_permission=$this->hasPermission($this->Session->read('User.id'),$aco_name);
		$this->set(compact('bool_product_add_permission'));
		
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Provider->id = $id;
		if (!$this->Provider->exists()) {
			throw new NotFoundException(__('Invalid provider'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$provider=$this->Provider->find('first',array(
			'conditions'=>array(
				'Provider.id'=>$id,
			),
		));
		
		if ($this->Provider->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$provider['Provider']['id'];
			$deletionArray['Deletion']['reference']=$provider['Provider']['name'];
			$deletionArray['Deletion']['type']='Provider';
			$this->Deletion->save($deletionArray);
			
			$this->Session->setFlash(__('The provider has been deleted.'));
		} 
		else {
			$this->Session->setFlash(__('The provider could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
