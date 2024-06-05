<?php
App::uses('AppController', 'Controller');

class ProductionOrderStatesController extends AppController {

	public $components = array('Paginator');

public function resumen() {
		$this->ProductionOrderState->recursive = -1;
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
		$productionOrderStateCount=	$this->ProductionOrderState->find('count', [
			'fields'=>['ProductionOrderState.id'],
			'conditions' => [
			],
		]);
		
		$this->Paginator->settings = [
			'contain'=>[				
			],
      'order'=>['ProductionOrderState.list_order'=>'ASC'],
			'limit'=>($productionOrderStateCount!=0?$productionOrderStateCount:1),
		] ;

		$productionOrderStates = $this->Paginator->paginate('ProductionOrderState');
    //pr($productionOrderStates);
		$this->set(compact('productionOrderStates'));
	}

	public function detalle($id = null) {
		if (!$this->ProductionOrderState->exists($id)) {
			throw new NotFoundException(__('Invalid production order state'));
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
    
		$options = [
      'conditions' => [
        'ProductionOrderState.id' => $id,
      ],
    ];
		$this->set('productionOrderState', $this->ProductionOrderState->find('first', $options));
	}

	public function crear() {
		if ($this->request->is('post')) {
			$this->ProductionOrderState->create();
			if ($this->ProductionOrderState->save($this->request->data)) {
				$this->Session->setFlash(__('The production order state has been saved.'), 'default',['class' => 'success']);
				return $this->redirect(['action' => 'resumen'] );
			} 
      else {
				$this->Session->setFlash(__('The production order state could not be saved. Please, try again.'), 'default',['class' => 'error-message'] );
			}
		}
	}


	public function editar($id = null) {
		if (!$this->ProductionOrderState->exists($id)) {
			throw new NotFoundException(__('Invalid production order state'));
		}
		if ($this->request->is(['post', 'put'])) {
			if ($this->ProductionOrderState->save($this->request->data)) {
				$this->Session->setFlash(__('The production order state has been saved.'), 'default',['class' => 'success']);
				return $this->redirect(array('action' => 'resumen'));
			} else {
				$this->Session->setFlash(__('The production order state could not be saved. Please, try again.'), 'default',['class' => 'error-message']);
			}
		} else {
			$options = ['conditions' => ['ProductionOrderState.id' => $id]
      ];
			$this->request->data = $this->ProductionOrderState->find('first', $options);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->ProductionOrderState->id = $id;
		if (!$this->ProductionOrderState->exists()) {
			throw new NotFoundException(__('Invalid production order state'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->ProductionOrderState->delete()) {
			$this->Session->setFlash(__('The production order state has been deleted.'), 'default',['class' => 'success']);
		} 
    else {
			$this->Session->setFlash(__('The production order state could not be deleted. Please, try again.'), 'default',['class' => 'error-message']);
		}
		return $this->redirect(['action' => 'resumen']);
	}
}
