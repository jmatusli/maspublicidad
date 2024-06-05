<?php
App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'PHPExcel')));
App::uses('AppController', 'Controller','PHPExcel');
App::import('Vendor', 'PHPExcel/Classes/PHPExcel');
App::import('Vendor', 'html2text-master/html2text');

class SystemEmailsController extends AppController {
	public $components = array('Paginator');
	public $helpers = array('PhpExcel'); 

	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('');
	}
	
	public function index() {
		$this->SystemEmail->recursive = -1;
		
		$userId=$this->Auth->User('id');
		if ($this->request->is('post')) {
			$startDateArray=$this->request->data['Report']['startdate'];
			$startDateString=$startDateArray['year'].'-'.$startDateArray['month'].'-'.$startDateArray['day'];
			$startDate=date( "Y-m-d", strtotime($startDateString));
		
			$endDateArray=$this->request->data['Report']['enddate'];
			$endDateString=$endDateArray['year'].'-'.$endDateArray['month'].'-'.$endDateArray['day'];
			$endDate=date("Y-m-d",strtotime($endDateString));
			$endDatePlusOne=date("Y-m-d",strtotime($endDateString."+1 days"));
			
			$userId=$this->request->data['Report']['user_id'];
		}
		else if (!empty($_SESSION['startDate']) && !empty($_SESSION['endDate'])){
			$startDate=$_SESSION['startDate'];
			$endDate=$_SESSION['endDate'];
			$endDatePlusOne=date("Y-m-d",strtotime($endDate."+1 days"));
			if ($this->Session->check('currencyId')){
				$currencyId=$_SESSION['currencyId'];
			}
			//if ($this->Session->check('userId')){
			//	//$user_id=$_SESSION['userId'];
			//}
		}
		else {
			$startDate = date("Y-m-01");
			$endDate=date("Y-m-d",strtotime(date("Y-m-d")));
			$endDatePlusOne= date( "Y-m-d", strtotime( date("Y-m-d")."+1 days" ) );
		}
		
		$_SESSION['startDate']=$startDate;
		$_SESSION['endDate']=$endDate;
		$_SESSION['userId']=$userId;
		
		$this->set(compact('startDate','endDate','userId'));
		
		$userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole'));
		
		$systemEmailConditions=array(
			'SystemEmail.datetime_sent >='=>$startDate,
			'SystemEmail.datetime_sent <'=>$endDatePlusOne,
		);
		
		$systemEmailCount=	$this->SystemEmail->find('count', array(
			'fields'=>array('SystemEmail.id'),
			'conditions' => $systemEmailConditions,
		));
		
		$this->Paginator->settings = array(
			'conditions' => $systemEmailConditions,
			'contain'=>array(				
				'SystemEmailAttachment',
				'SystemEmailCc',
				'SystemEmailBcc',
				'SystemEmailRecipient',
			),
			'order'=>'SystemEmail.id DESC',
			'limit'=>($systemEmailCount!=0?$systemEmailCount:1),
		);

		$systemEmails = $this->Paginator->paginate('SystemEmail');
		$this->set(compact('systemEmails'));
		
		$this->loadModel('User');
		$userConditions=array();
		if ($userrole!=ROLE_ADMIN&&$userrole!=ROLE_ASSISTANT){
			$userConditions=array('User.id'=>$user_id);
		}
		$users=$this->User->find('list',array(
			'conditions'=>$userConditions,
			'order'=>'User.username'
		));
		$this->set(compact('users'));
	}

	public function view($id = null) {
		if (!$this->SystemEmail->exists($id)) {
			throw new NotFoundException(__('Invalid system email'));
		}
		$options = array(
			'conditions' => array(
				'SystemEmail.id' => $id
			),
			'contain'=>array(				
				'SystemEmailAttachment',
				'SystemEmailCc',
				'SystemEmailBcc',
				'SystemEmailRecipient',
			),
		);
		$systemEmail=$this->SystemEmail->find('first', $options);
		$this->set(compact('systemEmail'));
	}

	public function add($quotationId=0,$implodedEmailList=null) {
		define('NUM_FILES','5');
		$this->loadModel('User');
		//if (!empty($implodedEmailList)){
		//	$emailList=explode("|",$implodedEmailList);
		//}
		
		$emailFrom=DEFAULT_EMAIL;
		$emailRecipient=DEFAULT_EMAIL;
		$emailCC=DEFAULT_EMAIL;
		$subject=COMPANY_NAME." le informa que ...";
		$body="";
		$attachments=array();
		
		//$quotationId=1244;
		if (!empty($quotationId)){
			$this->loadModel('Quotation');
			$this->Quotation->recursive=-1;
			$quotation=$this->Quotation->find('first',array(
				'conditions'=>array(
					'Quotation.id'=>$quotationId,
				),
				'contain'=>array(
					'Client',
					'Contact',
					'Currency',
					'QuotationImage',
					'QuotationProduct'=>array(
						'conditions'=>array(
							'QuotationProduct.product_quantity >'=>0,
						),
						'Currency',
						'Product',
					),
					'User',
				),
			));
			//pr($quotation);
			if (!empty($quotation)){
				if (!empty($quotation['User']['email'])){
					$emailFrom=$quotation['User']['email'];
				}
				if (!empty($quotation['Contact']['email'])){
					$emailRecipient=$quotation['Contact']['email'];
				}
				$subject="Cotización de ".COMPANY_NAME." # ".$quotation['Quotation']['quotation_code'];
				$body.="<p>Estimado cliente,</p>";
				$body.="<p>Le ofrecemos los siguientes productos:</p>";
				$body.="<ul>";
				if (!empty($quotation['QuotationProduct'])){
					foreach ($quotation['QuotationProduct'] as $quotationProduct){
						$body.="<li>";
						$body.=$quotationProduct['product_quantity']." ".$quotationProduct['Product']['name']." ".$quotationProduct['product_description'];
						$body.=" a precio unitario de ".$quotationProduct['Currency']['abbreviation']." ".number_format($quotationProduct['product_unit_price'],2,",",".");
						$body.="</li>";
					}
				}
				$body.="</ul>";
				$body.="<p>El precio total de la cotización es ".$quotation['Currency']['abbreviation']." ".$quotation['Quotation']['price_total']."</p>";
				$body.="<p>Cualquier pregunta, comuníquese con nosotros en número ".(empty($quotation['User']['phone'])?COMPANY_PHONE:$quotation['User']['phone'])." o via correo electrónico en ".(empty($quotation['User']['email'])?DEFAULT_EMAIL:$quotation['User']['email'])."</p>";
				$body.="<p>Atentamente,</p>";
				$body.="<p>".$quotation['User']['first_name']." ".$quotation['User']['last_name']."</p>";
			}
		}
		
		$loggedUserId=$this->Auth->User('id');
		if (!empty($loggedUserId)){
			$loggedUser=$this->User->find('first',array(
				'conditions'=>array(
					'User.id'=>$loggedUserId,
				),
			));
			if (!empty($loggedUser)){
				$emailFrom=$loggedUser['User']['email'];
			}
		}
		$userrole = $this->Auth->User('role_id');
		$this->set(compact('userrole','loggedUserId'));
		$this->set(compact('emailFrom','emailRecipient','emailCC','subject','body'));
		
		
		$this->User->recursive=-1;
		$allAvailableUsers=$this->User->find('all',array(
			'fields'=>array('User.email'),
			'conditions'=>array(
				
			),
		));
		$availableEmailAddresses=array();
		if (!empty($allAvailableUsers)){
			foreach ($allAvailableUsers as $user){
				$email=$user['User']['email'];
				if (!empty($email)){
					$availableEmailAddresses[$email]=$email;
				}
			}
		}
		$this->set(compact('availableEmailAddresses'));
		$emailTo=1;
		$this->set(compact('emailTo'));
		
		if ($this->request->is('post')) {
			$boundary=uniqid('dd');
			$from=$this->request->data['SystemEmail']['email_from'];
			$toAddresses=$this->SystemEmail->processEmails($this->request->data['SystemEmail']['email_recipient']);
			//pr($toAddresses);
			$ccAddresses=$this->SystemEmail->processEmails($this->request->data['SystemEmail']['email_cc']);
			$bccAddresses=$this->SystemEmail->processEmails($this->request->data['SystemEmail']['email_bcc']);
			$subject=$this->request->data['SystemEmail']['subject'];
			//pr($this->request->data);
			$body=trim($this->request->data['SystemEmail']['body_html']);
			
			//Html body
			$messageHtml=$body;							
			//Plain text body
			//https://ricochen.wordpress.com/2012/05/03/simple-html-to-text-converter/
			$messagePlain=convert_html_to_text($body);
			
			$message = "This is a MIME encoded message.";
			$message .= "\r\n\r\n--" . $boundary . "\r\n";
			$message .= "Content-type: text/plain;charset=utf-8\r\n\r\n";
			//Plain text body
			$message .= $messagePlain;
			$message .= "\r\n\r\n--" . $boundary . "\r\n";
			$message .= "Content-type: text/html;charset=utf-8\r\n\r\n";
			//Html body
			$message .= $messageHtml;
			$message .= "\r\n\r\n--" . $boundary . "--";
			$message=wordwrap($message,70);
			
			$system_email_id=$this->SystemEmail->saveSystemEmail($boundary,$from,$toAddresses, $subject, $messageHtml,$messagePlain,$message, $ccAddresses,$bccAddresses);
			$attachments=array();
			if (!empty($system_email_id)){
				$boolAttachmentsAdded=true;
				if (!empty($this->request->data['Document']['url_file'][1]['tmp_name'])){
					$this->loadModel('SystemEmailAttachment');
					//pr($this->request->data['Document']['url_file']);
					$fileOK=$this->uploadFiles('systememails/'.$system_email_id,$this->request->data['Document']['url_file'],null,false);
					//echo "file OK<br/>";
					//pr($fileOK);
					if (array_key_exists('urls',$fileOK)){
						for ($f=0;$f<count($fileOK['urls']);$f++){
							$systemEmailAttachmentArray=array();
							$systemEmailAttachmentArray['SystemEmailAttachment']['system_email_id']=$system_email_id;
							$systemEmailAttachmentArray['SystemEmailAttachment']['url_attachment']=$fileOK['urls'][$f];
							$this->SystemEmailAttachment->create();
							//pr($systemEmailAttachmentArray);
							if (!$this->SystemEmailAttachment->save($systemEmailAttachmentArray)) {
								$boolAttachmentsAdded=false;
								echo "Problema guardando el archivo adjunto para el coreo";
								pr($this->validateErrors($this->SystemEmailAttachment));
								throw new Exception();
							}
							$attachments[$fileOK['urls'][$f]]=$fileOK['urls'][$f];
						}
					}
				}
				//pr($attachments);
				if ($boolAttachmentsAdded){
					//pr($ccAddresses);
					//pr($toAddresses);
					$mailSuccess=$this->SystemEmail->send_multi_part_mail_with_attachment($boundary,$from,$toAddresses, $subject, $messageHtml, $ccAddresses,$bccAddresses,$attachments);
					//pr($mailSuccess);
					if ($mailSuccess){
						$this->Session->setFlash("El correo se envió correctamente.", 'default',array('class' => 'success'));
						return $this->redirect(array('action' => 'index'));
					}
					else {
						$this->Session->setFlash("No se podía enviar el correo.  Problema de configuración de correo.", 'default',array('class' => 'error-message'));
					}
				}
				else {
					$this->Session->setFlash("Problema con archivos adjuntos.  No se podía enviar el correo.", 'default',array('class' => 'error-message'));
				}
			}
			else {
				$this->Session->setFlash("Se guardó el correo pero no se envió correctamente.", 'default',array('class' => 'error-message'));
			}
		}
		
		$boolOptions=array(
			'0'=>'No',
			'1'=>'Yes'
		);
		$this->set(compact('boolOptions'));
		
		$defaultCC=array();
		$defaultCC[]="info@intersinaptico.com";
		$this->set(compact('defaultCC'));
	}
/*
	public function edit($id = null) {
		if (!$this->SystemEmail->exists($id)) {
			throw new NotFoundException(__('Invalid system email'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->SystemEmail->save($this->request->data)) {
				$this->Session->setFlash(__('The system email has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The system email could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('SystemEmail.' . $this->SystemEmail->primaryKey => $id));
			$this->request->data = $this->SystemEmail->find('first', $options);
		}
	}
*/
/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
 /*
	public function delete($id = null) {
		$this->SystemEmail->id = $id;
		if (!$this->SystemEmail->exists()) {
			throw new NotFoundException(__('Invalid system email'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->SystemEmail->delete()) {
			$this->Session->setFlash(__('The system email has been deleted.'));
		} else {
			$this->Session->setFlash(__('The system email could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
*/
}
