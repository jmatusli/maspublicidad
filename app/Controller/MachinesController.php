<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class MachinesController extends AppController {

	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function index() {
		$this->Machine->recursive = -1;
		$this->set('machines', $this->Paginator->paginate());
	}

	public function view($id = null) {
		if (!$this->Machine->exists($id)) {
			throw new NotFoundException(__('Invalid machine'));
		}

		$this->loadModel('Product');
		//$this->loadModel('Operator');
				
		$startDate = null;
		$endDate = null;
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$this->set(compact('startDate','endDate'));
		
		$machine=$this->Machine->find('first', array(
			'conditions' => array(
				'Machine.id'=> $id,
			),
			'contain'=>array(
				'ProductionProcessProduct'=>array(
					'ProductionProcess',
					
				),
			),
		));
		
		
		$operators=$this->Operator->find('all',array('fields'=>array('Operator.id','Operator.name')));
		$operatorCounter=0;
		foreach ($operators as $operator){
			$rawMaterialCounter=0;
			foreach ($rawMaterials as $rawMaterial){
				if ($rawMaterialsUse[$rawMaterial['Product']['id']]>0){
					$producedProductsPerOperator[$operatorCounter]['operator_id']=$operator['Operator']['id'];
					$producedProductsPerOperator[$operatorCounter]['operator_name']=$operator['Operator']['name'];
					$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['raw_material_id']=$rawMaterial['Product']['id'];
					$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['raw_material_name']=$rawMaterial['Product']['name'];
					$productCounter=0;
					foreach ($finishedProducts as $finishedProduct){
						$arrayForProduct=array();
						foreach ($productionResultCodes as $productionResultCode){
							$quantityForProductInMonth=$this->ProductionMovement->find('first',array(
								'fields'=>array('ProductionMovement.product_id', 'SUM(ProductionMovement.product_quantity) AS product_total','SUM(ProductionMovement.product_quantity*ProductionMovement.product_unit_price) AS total_value'),
								'conditions'=>array(
									'ProductionRun.production_run_date >='=> $startDate,
									'ProductionRun.production_run_date <'=> $endDatePlusOne,
									'ProductionRun.machine_id'=>$id,
									'ProductionRun.operator_id'=>$operator['Operator']['id'],
									'ProductionRun.finished_product_id'=>$finishedProduct['Product']['id'],
									'ProductionRun.raw_material_id'=>$rawMaterial['Product']['id'],
									'ProductionMovement.production_result_code_id'=>$productionResultCode['ProductionResultCode']['id'],
								),
								'group'=>'ProductionMovement.product_id',
							));
							if (!empty($quantityForProductInMonth)){
								//$valueCounterForProduct+=$quantityForProductInMonth[0]['total_value'];
								$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['products'][$productCounter]['finished_product_id']=$finishedProduct['Product']['id'];
								$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['products'][$productCounter]['finished_product_name']=$finishedProduct['Product']['name'];
								$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['products'][$productCounter]['product_quantity'][$productionResultCode['ProductionResultCode']['id']]=$quantityForProductInMonth[0]['product_total'];
								//$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['products'][$productCounter]['total_value']=$quantityForProductInMonth[0]['total_value'];
							}
							else {
								$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['products'][$productCounter]['finished_product_id']=$finishedProduct['Product']['id'];
								$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['products'][$productCounter]['finished_product_name']=$finishedProduct['Product']['name'];
								$producedProductsPerOperator[$operatorCounter]['rawmaterial'][$rawMaterialCounter]['products'][$productCounter]['product_quantity'][$productionResultCode['ProductionResultCode']['id']]=0;
							}
						}
						$productCounter++;
					}
				}
				$rawMaterialCounter++;
			}
			$operatorCounter++;
		}
		
		//pr($producedProductsPerOperator);
		/*
		$visibleArray=array();
		//initialize		
		foreach ($rawMaterials as $rawMaterial){
			if ($rawMaterialsUse[$rawMaterial['Product']['id']]>0){
				foreach ($finishedProducts as $finishedProduct){
					foreach ($productionResultCodes as $productionResultCode){
						$visibleArray[$rawMaterial['Product']['id']][$finishedProduct['Product']['id']]['visible']=0;
					}
				}
			}
		}
		// set visual products to 1
		foreach ($producedProductsPerOperator as $producedProductPerOperator){
			foreach ($producedProductPerOperator['rawmaterial'] as $producedProductPerOperatorAndRawMaterial){
				foreach ($producedProductPerOperatorAndRawMaterial['products'] as $finishedProduct){
					foreach ($finishedProduct['product_quantity'] as $quantity){
						if ($quantity>0){
							$visibleArray[$producedProductPerOperatorAndRawMaterial['raw_material_id']][$finishedProduct['finished_product_id']]['visible']=1;
						}
					}
				}
			}
		}
		
		//pr($visibleArray);		
		$producedProductsPerShift=array();
		*/
		//pr($producedProductsPerShift);
		
		$this->set(compact('machine'));
		
		$this->Machine->recursive=-1;
		$otherMachines=$this->Machine->find('all',array(
			'fields'=>array('Machine.id','Machine.name'),
			'conditions'=>array('Machine.id !='=>$id),
		));
		$this->set(compact('otherMachines'));
	}

	public function add() {

		$this->loadModel('Product');
		
		$this->Product->recursive=-1;
		$products = $this->Product->find('all',array(
			'fields'=>array('Product.id','Product.name'),
			'order'=>'Product.name',
		));
		$this->set(compact('products'));
	
		if ($this->request->is('post')) {
			$datasource=$this->Machine->getDataSource();
			try {
				$datasource->begin();
				$product_id=0;
				
				$this->Machine->create();
				if (!$this->Machine->save($this->request->data)) {
					echo "Problema guardando la máquina";
					pr($this->validateErrors($this->Machine));
					throw new Exception();
				}
				$machine_id=$this->Machine->id;
				/*
				for ($pr=0;$pr<count($this->request->data['Product']);$pr++){
					if ($this->request->data['Product'][$pr]['product_id']){
						$machineProductArray=array();
						$this->Machine->MachineProduct->create();
						
						$machineProductArray['MachineProduct']['machine_id']=$machine_id;
						$machineProductArray['MachineProduct']['product_id']=$products[$pr]['Product']['id'];
						if (!$this->Machine->MachineProduct->save($machineProductArray)){
							pr($this->validateErrors($this->MachineProduct));
							echo "Problema guardando el producto para la máquina";
							throw new Exception();
						}
					}
				}
				*/
				$datasource->commit();
				$this->recordUserAction($this->Machine->id,null,null);
				$this->Session->setFlash(__('The machine has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The machine could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		}
	}

	public function edit($id = null) {
		if (!$this->Machine->exists($id)) {
			throw new NotFoundException(__('Invalid machine'));
		}
		$this->loadModel('Product');
		
		
		$this->Product->recursive=-1;
		$products = $this->Product->find('all',array(
			'fields'=>array('Product.id','Product.name'),
			'contain'=>array(
				/*
				'MachineProduct'=>array(
					'conditions'=>array(
						'MachineProduct.machine_id'=>$id,
					)
				),
				*/
			),
			'order'=>'Product.name',
		));
		//pr($products);
		$this->set(compact('products'));
		if ($this->request->is(array('post', 'put'))) {
			$datasource=$this->Machine->getDataSource();
			try {
				$datasource->begin();
				/*
				$this->Machine->MachineProduct->recursive=-1;
				$previousMachineProducts=$this->Machine->MachineProduct->find('all',array(
					'fields'=>array('MachineProduct.id'),
					'conditions'=>array(
						'MachineProduct.machine_id'=>$id,
					),
				));
				if (!empty($previousMachineProducts)){
					foreach ($previousMachineProducts as $previousMachineProduct){
						$this->Machine->MachineProduct->id=$previousMachineProduct['MachineProduct']['id'];
						$this->Machine->MachineProduct->delete($previousMachineProduct['MachineProduct']['id']);
					}
				}
				*/
				$this->Machine->id=$id;
				if (!$this->Machine->save($this->request->data)) {
					echo "Problema guardando la máquina";
					pr($this->validateErrors($this->Machine));
					throw new Exception();
				}
				$machine_id=$this->Machine->id;
				/*
				for ($pr=0;$pr<count($this->request->data['Product']);$pr++){
					if ($this->request->data['Product'][$pr]['product_id']){
						$machineProductArray=array();
						$this->Machine->MachineProduct->create();
						
						$machineProductArray['MachineProduct']['machine_id']=$machine_id;
						$machineProductArray['MachineProduct']['product_id']=$products[$pr]['Product']['id'];
						if (!$this->Machine->MachineProduct->save($machineProductArray)){
							pr($this->validateErrors($this->MachineProduct));
							echo "Problema guardando el producto para la máquina";
							throw new Exception();
						}
					}
				}
				*/
				$datasource->commit();
				$this->recordUserAction($this->Machine->id,null,null);
				$this->Session->setFlash(__('The machine has been saved.'),'default',array('class' => 'success'));
				return $this->redirect(array('action' => 'index'));
			} 
			catch(Exception $e){
				$datasource->rollback();
				pr($e);
				$this->Session->setFlash(__('The machine could not be saved. Please, try again.'), 'default',array('class' => 'error-message'));
			}
		} 
		else {
			$options = array('conditions' => array('Machine.' . $this->Machine->primaryKey => $id));
			$this->request->data = $this->Machine->find('first', $options);
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
		$this->Machine->id = $id;
		if (!$this->Machine->exists()) {
			throw new NotFoundException(__('Invalid machine'));
		}
		$this->request->allowMethod('post', 'delete');
		
		$machine=$this->Machine->find('first',array(
			'conditions'=>array(
				'Machine.id'=>$id,
			),
		));
		
		if ($this->Machine->delete()) {
			$this->loadModel('Deletion');
			$this->Deletion->create();
			$deletionArray=array();
			$deletionArray['Deletion']['user_id']=$this->Auth->User('id');
			$deletionArray['Deletion']['reference_id']=$machine['Machine']['id'];
			$deletionArray['Deletion']['reference']=$machine['Machine']['name'];
			$deletionArray['Deletion']['type']='Machine';
			$this->Deletion->save($deletionArray);

			$this->Session->setFlash(__('The machine has been deleted.'));
		} 
		else {
			$this->Session->setFlash(__('The machine could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
