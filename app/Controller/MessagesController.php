<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');

class MessagesController extends AppController {
	public $components = array('Paginator','RequestHandler');
	public $helpers = array('PhpExcel');

	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->Auth->allow('savemessage');		
	}

	public function savemessage() {
		$this->layout = "ajax";
		$this->autoRender = false;
		
		$this->loadModel('MessageRecipient');
		// when data are passed with serialize, the ajax parameter processdata must be true, if not the values don't get parsed correctly
		var_dump($_POST);
		$messageArray=$_POST['data'];
		pr ($messageArray);
		$filesArray=$_FILES['data'];
		pr ($filesArray);
		
		$datasource=$this->Message->getDataSource();
		$datasource->begin();
		try {
			//$message_id=0;
			$this->Message->create();
			if (!$this->Message->save($messageArray)) {
				echo "Problema guardando el mensaje";
				pr($this->validateErrors($this->Message));
				throw new Exception();
			}			
			$message_id=$this->Message->id;
			
			if (!empty($filesArray['tmp_name']['Document'])){
				$cleanFilesArray=array();
				foreach ($filesArray as $key =>$value){
					$cleanFilesArray[0][$key]=$filesArray[$key]['Document']['url_image'];
				}
				//pr($cleanFilesArray);
				$imageOK=$this->uploadFiles('MessageAttachment/'.$message_id,$cleanFilesArray);
				echo "image OK<br/>";
				pr($imageOK);
				
				if (array_key_exists('urls',$imageOK)){
					$messageArray['Message']['url_image']=$imageOK['urls'][0];
				}
			}
			
			$this->Message->id=$message_id;
			if (!$this->Message->save($messageArray)) {
				echo "Problema guardando el mensaje";
				pr($this->validateErrors($this->Message));
				throw new Exception();
			}
			
			foreach ($messageArray['Message']['recipient_user_id'] as $messageRecipient){
				if ($messageRecipient>0){
					$this->MessageRecipient->create();
					$messageRecipientArray=array();
					$messageRecipientArray['MessageRecipient']['message_id']=$message_id;
					$messageRecipientArray['MessageRecipient']['recipient_user_id']=$messageRecipient;
					//pr($messageRecipientArray);
					if (!$this->MessageRecipient->save($messageRecipientArray)) {
						echo "Problema guardando los destinatarios del mensaje";
						pr($this->validateErrors($this->MessageRecipient));
						throw new Exception();
					}
				}
			}
			
			$datasource->commit();
			$this->recordUserAction($this->Message->id,null,null);
			$this->recordUserActivity($this->Session->read('User.username'),"Se creó el mensaje ".$this->request->data['Message']['subject']);
			$this->Session->setFlash(__('Se guardó el mensaje.'),'default',array('class' => 'success'));
			//return $this->redirect(array('action' => 'index'));
			return true;
			//return $messageArray;
		} 
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			$this->Session->setFlash(__('No se podía guardar el mensaje.'), 'default',array('class' => 'error-message'));
			return false;
		}
		
		
	}
		
	public function index() {
		$this->loadModel('MessageRecipient');
	
		$this->Message->recursive = -1;
		
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
		
		$receivedMessageConditions=$sentMessageConditions=array(
			'Message.created >='=>$startDate,
			'Message.created <'=>$endDatePlusOne,
		);
		
		$receivedMessageIds=$this->MessageRecipient->find('list',array(
			'fields'=>array('MessageRecipient.message_id'),
			'conditions'=>array(
				'MessageRecipient.recipient_user_id'=>$this->Auth->User('id')
			),
		));
		
		$receivedMessageConditions[]=array('Message.id'=>$receivedMessageIds);
		$sentMessageConditions[]=array('Message.from_user_id'=>$this->Auth->User('id'));
		
		$receivedMessageCount=$this->Message->find('count', array(
			'fields'=>array('Message.id'),
			'conditions' => $receivedMessageConditions,
		));
		$this->Paginator->settings = array(
			'conditions' => $receivedMessageConditions,
			'contain'=>array(		
				'FromUser',
				'MessageRecipient'=>array(
					'fields'=>array(
						'MessageRecipient.id',
						'MessageRecipient.message_id',
						'MessageRecipient.bool_read',
					),
				),
			),
			'order'=>'Message.created DESC',
			'limit'=>($receivedMessageCount!=0?$receivedMessageCount:1),
		);
		$receivedMessages = $this->Paginator->paginate('Message');
		$this->set(compact('receivedMessages'));
		//pr($receivedMessages);
		
		$sentMessageCount=	$this->Message->find('count', array(
			'fields'=>array('Message.id'),
			'conditions' => $sentMessageConditions,
		));
		$this->Paginator->settings = array(
			'conditions' => $sentMessageConditions,
			'contain'=>array(				
				'MessageRecipient'=>array(
					'RecipientUser',
				),
			),
			'order'=>'Message.created DESC',
			'limit'=>($sentMessageCount!=0?$sentMessageCount:1),
		);

		$sentMessages = $this->Paginator->paginate('Message');
		$this->set(compact('sentMessages'));
	}

	public function guardarResumen() {
		$exportData=$_SESSION['resumen'];
		$this->set(compact('exportData'));
	}

	public function view($id = null) {
		if (!$this->Message->exists($id)) {
			throw new NotFoundException(__('Invalid message'));
		}

		$message=$this->Message->find('first', array(
			'conditions' => array(
				'Message.id'=> $id,
			),
			'contain'=>array(
				'FromUser',
				'MessageRecipient'=>array(
					'RecipientUser',
				)
			),
		));
		$this->set(compact('message'));
		
		$this->loadModel('MessageRecipient');
		$messageRecipient=$this->MessageRecipient->find('first',array(
			'conditions'=>array(
				'MessageRecipient.message_id'=>$id,
				'MessageRecipient.recipient_user_id'=>$this->Auth->User('id'),
			),
		));
		
		if (!empty($messageRecipient)){
			$messageRecipientArray=array();
			$messageRecipientArray['MessageRecipient']['id']=$messageRecipient['MessageRecipient']['id'];
			$messageRecipientArray['MessageRecipient']['bool_read']=true;
			//pr($messageRecipientArray);
			if (!$this->MessageRecipient->save($messageRecipientArray)){
				echo "Problema cambiando el estado del mensaje a leído";
				pr($this->validateErrors($this->MessageRecipient));
			}
		}
		
		
		
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Message->create();
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__('The message has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.'));
			}
		}
		$fromUsers = $this->Message->FromUser->find('list');
		$this->set(compact('fromUsers'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Message->exists($id)) {
			throw new NotFoundException(__('Invalid message'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Message->save($this->request->data)) {
				$this->Session->setFlash(__('The message has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Message.' . $this->Message->primaryKey => $id));
			$this->request->data = $this->Message->find('first', $options);
		}
		$fromUsers = $this->Message->FromUser->find('list');
		$this->set(compact('fromUsers'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Invalid message'));
		}
		$this->request->allowMethod('post', 'delete');
		/*
		if ($this->Message->delete()) {
			$this->Session->setFlash(__('The message has been deleted.'));
		} 
		else {
			$this->Session->setFlash(__('The message could not be deleted. Please, try again.'));
		}
		*/
		$this->Session->setFlash(__('Messages cannot be deleted.'));
		return $this->redirect(array('action' => 'index'));
	}
}
