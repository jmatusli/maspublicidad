<?php
App::uses('AppController', 'Controller');
/**
 * ProductsProviders Controller
 *
 * @property ProductsProvider $ProductsProvider
 * @property PaginatorComponent $Paginator
 */
class ProductsProvidersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->ProductsProvider->recursive = -1;
		
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
		
		$productsProviderCount=	$this->ProductsProvider->find('count', array(
			'fields'=>array('ProductsProvider.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($productsProviderCount!=0?$productsProviderCount:1),
		);

		$productsProviders = $this->Paginator->paginate('ProductsProvider');
		$this->set(compact('productsProviders'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ProductsProvider->exists($id)) {
			throw new NotFoundException(__('Invalid products provider'));
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
		$options = array('conditions' => array('ProductsProvider.' . $this->ProductsProvider->primaryKey => $id));
		$this->set('productsProvider', $this->ProductsProvider->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->ProductsProvider->create();
			if ($this->ProductsProvider->save($this->request->data)) {
				$this->Session->setFlash(__('The products provider has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The products provider could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
		$products = $this->ProductsProvider->Product->find('list');
		$providers = $this->ProductsProvider->Provider->find('list');
		$this->set(compact('products', 'providers'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->ProductsProvider->exists($id)) {
			throw new NotFoundException(__('Invalid products provider'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->ProductsProvider->save($this->request->data)) {
				$this->Session->setFlash(__('The products provider has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The products provider could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} else {
			$options = array('conditions' => array('ProductsProvider.' . $this->ProductsProvider->primaryKey => $id));
			$this->request->data = $this->ProductsProvider->find('first', $options);
		}
		$products = $this->ProductsProvider->Product->find('list');
		$providers = $this->ProductsProvider->Provider->find('list');
		$this->set(compact('products', 'providers'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductsProvider->id = $id;
		if (!$this->ProductsProvider->exists()) {
			throw new NotFoundException(__('Invalid products provider'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductsProvider->delete()) {
			$this->Session->setFlash(__('The products provider has been deleted.'));
		} else {
			$this->Session->setFlash(__('The products provider could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
