<?php
App::uses('AppController', 'Controller');
/**
 * InventoryProductInventoryProviders Controller
 *
 * @property InventoryProductInventoryProvider $InventoryProductInventoryProvider
 * @property PaginatorComponent $Paginator
 */
class InventoryProductInventoryProvidersController extends AppController {

	public $components = array('Paginator');

	public function index() {
		$this->InventoryProductInventoryProvider->recursive = -1;
		
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
		
		$inventoryProductInventoryProviderCount=	$this->InventoryProductInventoryProvider->find('count', array(
			'fields'=>array('InventoryProductInventoryProvider.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($inventoryProductInventoryProviderCount!=0?$inventoryProductInventoryProviderCount:1),
		);

		$inventoryProductInventoryProviders = $this->Paginator->paginate('InventoryProductInventoryProvider');
		$this->set(compact('inventoryProductInventoryProviders'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->InventoryProductInventoryProvider->exists($id)) {
			throw new NotFoundException(__('Invalid inventory product inventory provider'));
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
		$options = array('conditions' => array('InventoryProductInventoryProvider.' . $this->InventoryProductInventoryProvider->primaryKey => $id));
		$this->set('inventoryProductInventoryProvider', $this->InventoryProductInventoryProvider->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->InventoryProductInventoryProvider->create();
			if ($this->InventoryProductInventoryProvider->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory product inventory provider has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The inventory product inventory provider could not be saved. Please, try again.'));
			}
		}
		$inventoryProducts = $this->InventoryProductInventoryProvider->InventoryProduct->find('list');
		$inventoryProviders = $this->InventoryProductInventoryProvider->InventoryProvider->find('list');
		$this->set(compact('inventoryProducts', 'inventoryProviders'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->InventoryProductInventoryProvider->exists($id)) {
			throw new NotFoundException(__('Invalid inventory product inventory provider'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->InventoryProductInventoryProvider->save($this->request->data)) {
				$this->Session->setFlash(__('The inventory product inventory provider has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The inventory product inventory provider could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('InventoryProductInventoryProvider.' . $this->InventoryProductInventoryProvider->primaryKey => $id));
			$this->request->data = $this->InventoryProductInventoryProvider->find('first', $options);
		}
		$inventoryProducts = $this->InventoryProductInventoryProvider->InventoryProduct->find('list');
		$inventoryProviders = $this->InventoryProductInventoryProvider->InventoryProvider->find('list');
		$this->set(compact('inventoryProducts', 'inventoryProviders'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->InventoryProductInventoryProvider->id = $id;
		if (!$this->InventoryProductInventoryProvider->exists()) {
			throw new NotFoundException(__('Invalid inventory product inventory provider'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->InventoryProductInventoryProvider->delete()) {
			$this->Session->setFlash(__('The inventory product inventory provider has been deleted.'));
		} else {
			$this->Session->setFlash(__('The inventory product inventory provider could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
