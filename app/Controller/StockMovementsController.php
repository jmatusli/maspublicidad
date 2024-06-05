<?php
App::uses('AppController', 'Controller');
/**
 * StockMovements Controller
 *
 * @property StockMovement $StockMovement
 * @property PaginatorComponent $Paginator
 */
class StockMovementsController extends AppController {

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
		$this->StockMovement->recursive = -1;
		
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
		
		$stockMovementCount=	$this->StockMovement->find('count', array(
			'fields'=>array('StockMovement.id'),
			'conditions' => array(
			),
		));
		
		$this->Paginator->settings = array(
			'conditions' => array(	
			),
			'contain'=>array(				
			),
			'limit'=>($stockMovementCount!=0?$stockMovementCount:1),
		);

		$stockMovements = $this->Paginator->paginate('StockMovement');
		$this->set(compact('stockMovements'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->StockMovement->exists($id)) {
			throw new NotFoundException(__('Invalid stock movement'));
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
		$options = array('conditions' => array('StockMovement.' . $this->StockMovement->primaryKey => $id));
		$this->set('stockMovement', $this->StockMovement->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->StockMovement->create();
			if ($this->StockMovement->save($this->request->data)) {
				$this->Session->setFlash(__('The stock movement has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The stock movement could not be saved. Please, try again.'));
			}
		}
		$entries = $this->StockMovement->Entry->find('list');
		$remissions = $this->StockMovement->Remission->find('list');
		$stockItems = $this->StockMovement->StockItem->find('list');
		$products = $this->StockMovement->Product->find('list');
		$measuringUnits = $this->StockMovement->MeasuringUnit->find('list');
		$currencies = $this->StockMovement->Currency->find('list');
		$this->set(compact('entries', 'remissions', 'stockItems', 'products', 'measuringUnits', 'currencies'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->StockMovement->exists($id)) {
			throw new NotFoundException(__('Invalid stock movement'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->StockMovement->save($this->request->data)) {
				$this->Session->setFlash(__('The stock movement has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The stock movement could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('StockMovement.' . $this->StockMovement->primaryKey => $id));
			$this->request->data = $this->StockMovement->find('first', $options);
		}
		$entries = $this->StockMovement->Entry->find('list');
		$remissions = $this->StockMovement->Remission->find('list');
		$stockItems = $this->StockMovement->StockItem->find('list');
		$products = $this->StockMovement->Product->find('list');
		$measuringUnits = $this->StockMovement->MeasuringUnit->find('list');
		$currencies = $this->StockMovement->Currency->find('list');
		$this->set(compact('entries', 'remissions', 'stockItems', 'products', 'measuringUnits', 'currencies'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->StockMovement->id = $id;
		if (!$this->StockMovement->exists()) {
			throw new NotFoundException(__('Invalid stock movement'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->StockMovement->delete()) {
			$this->Session->setFlash(__('The stock movement has been deleted.'));
		} else {
			$this->Session->setFlash(__('The stock movement could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
