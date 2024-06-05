<?php
App::uses('AppModel', 'Model');
/**
 * SystemEmail Model
 *
 * @property SystemEmailAttachment $SystemEmailAttachment
 * @property SystemEmailBcc $SystemEmailBcc
 * @property SystemEmailCc $SystemEmailCc
 * @property SystemEmailRecipient $SystemEmailRecipient
 */
class SystemEmail extends AppModel {

	public function processEmails($addresses) {
		$addresses=str_replace(';','|',$addresses);
		$addresses=str_replace(',','|',$addresses);
		$addresses=str_replace(array("\r", "\n"), '|', $addresses);
		$addresses=str_replace('||','|',$addresses);
		$addresses=ltrim($addresses,'|');
		$addresses=rtrim($addresses,'|');
		//pr($addresses);
		if (strpos($addresses,'|')){
			$processedEmails=explode("|",$addresses);
		}
		else {
			$processedEmails=$addresses;
		}
		return $processedEmails;
	}
	
	public function saveSystemEmail($boundary,$from,$to, $subject, $messageHtml,$messagePlain,$message, $cc=null,$bcc=null) {
		$datasource=$this->getDataSource();
		$datasource->begin();
		try {
			$systemEmailArray=array();
			$systemEmailArray['SystemEmail']['datetime_sent']=date('Y-m-d H:i:s');
			$systemEmailArray['SystemEmail']['email_from']=$from;
			$systemEmailArray['SystemEmail']['subject']=$subject;
			$systemEmailArray['SystemEmail']['body_html']=$messageHtml;
			$systemEmailArray['SystemEmail']['body_plain']=$messagePlain;
			$systemEmailArray['SystemEmail']['body_complete']=$message;
			$systemEmailArray['SystemEmail']['boundary']=$boundary;
			//pr($systemEmailArray);
			$this->create();
			if (!$this->save($systemEmailArray)) {
				echo "Problem saving the email";
				pr($this->validateErrors($this->SystemEmail));
				throw new Exception();
			}
			$systemEmailId=$this->id;
		
			if (!empty($to)){
				if (is_string($to)){
					$systemEmailRecipientArray=array();
					$systemEmailRecipientArray['SystemEmailRecipient']['system_email_id']=$systemEmailId;
					$systemEmailRecipientArray['SystemEmailRecipient']['email_recipient']=$to;
					$this->SystemEmailRecipient->create();
					if (!$this->SystemEmailRecipient->save($systemEmailRecipientArray)) {
						echo "Problemas guardando los destinatarios del correo";
						pr($this->validateErrors($this->SystemEmailRecipient));
						throw new Exception();
					}
				}
				if (is_array($to)){
					foreach ($to as $toemail){
						$systemEmailRecipientArray=array();
						$systemEmailRecipientArray['SystemEmailRecipient']['system_email_id']=$systemEmailId;
						$systemEmailRecipientArray['SystemEmailRecipient']['cc_email']=$toemail;
						$this->SystemEmailRecipient->create();
						if (!$this->SystemEmailRecipient->save($systemEmailRecipientArray)) {
							echo "Problemas guardando los destinatarios del correo";
							pr($this->validateErrors($this->SystemEmailRecipient));
							throw new Exception();
						}
					}
				}
			}		
			if (!empty($cc)){
				if (is_string($cc)){
					$systemEmailCcArray=array();
					$systemEmailCcArray['SystemEmailCc']['system_email_id']=$systemEmailId;
					$systemEmailCcArray['SystemEmailCc']['email_cc']=$cc;
					$this->SystemEmailCc->create();
					if (!$this->SystemEmailCc->save($systemEmailCcArray)) {
						echo "Problemas guardando los cc del correo";
						pr($this->validateErrors($this->SystemEmailCc));
						throw new Exception();
					}
				}
				if (is_array($cc)){
					foreach ($cc as $ccemail){
						$systemEmailCcArray=array();
						$systemEmailCcArray['SystemEmailCc']['system_email_id']=$systemEmailId;
						$systemEmailCcArray['SystemEmailCc']['email_cc']=$ccemail;
						$this->SystemEmailCc->create();
						if (!$this->SystemEmailCc->save($systemEmailCcArray)) {
							echo "Problemas guardando los cc del correo";
							pr($this->validateErrors($this->SystemEmailCc));
							throw new Exception();
						}
					}
				}
			}
			if (!empty($bcc)){
				if (is_string($bcc)){
					$systemEmailBccArray=array();
					$systemEmailBccArray['SystemEmailBcc']['system_email_id']=$systemEmailId;
					$systemEmailBccArray['SystemEmailBcc']['email_bcc']=$bcc;
					$this->SystemEmailBcc->create();
					if (!$this->SystemEmailBcc->save($systemEmailBccArray)) {
						echo "Problemas guardando los bcc del correo";
						pr($this->validateErrors($this->SystemEmailBcc));
						throw new Exception();
					}
				}
				if (is_array($bcc)){
					foreach ($bcc as $bccemail){
						$systemEmailBccArray=array();
						$systemEmailBccArray['SystemEmailBcc']['system_email_id']=$systemEmailId;
						$systemEmailBccArray['SystemEmailBcc']['email_bcc']=$bccemail;
						$this->SystemEmailBcc->create();
						if (!$this->SystemEmailBcc->save($systemEmailBccArray)) {
							echo "Problemas guardando los bcc del correo";
							pr($this->validateErrors($this->SystemEmailBcc));
							throw new Exception();
						}
					}
				}
			}
			$datasource->commit();
			return $systemEmailId;
		}
		catch(Exception $e){
			$datasource->rollback();
			pr($e);
			return 0;
		}		
    }
		
	public function send_multi_part_mail_with_attachment($boundary,$from,$to,$subject, $message, $cc=null,$bcc=null,$attachments=null) {
        //echo "now printing the to-email<br/>";
		//pr($cc);
		
		App::uses('CakeEmail', 'Network/Email');
		if (empty($from)){
			$emailfrom = DEFAULT_EMAIL;
		}
		else {
			$emailfrom = $from;
		}
		$fromname = COMPANY_NAME;
		$subject = $subject;
		$messagebody = $message;
		
		//$headers .=	"X-Priority: 3 \r\n" ;
		//$headers .=	"X-Mailer: PHP " . phpversion() .  "\r\n" ;
		//$headers .=	"MIME-Version: 1.0 \r\n" ;
		//$headers .=	"Content-Transfer-Encoding: 8bit \r\n" ;
		//$headers .=	"Content-type: multipart/alternative;boundary=".$boundary."\n";

		App::uses('CakeEmail', 'Network/Email');
        $email = new CakeEmail('gmail');
		
		$email->from(array($emailfrom=>$fromname));
		$email->replyTo(array($emailfrom=>$fromname));
		$email->returnPath(array($emailfrom=>$fromname));
		$email->to($to);
		if (!empty($cc)){
			$email->cc($cc);
		}
		if (!empty($bcc)){
			$email->bcc($bcc);
		}
		$email->subject($subject);
		$email->template('intersinaptico','intersinaptico');
		$email->emailFormat('both');
		//$email->attachments(array(1=>$filePath.$fileName));
		$email->attachments($attachments);
		//->viewVars($contents);
		//attachment in array format because of http://stackoverflow.com/questions/35185961/send-email-with-attachment-in-cakephp		
		//pr($email);
        $mailSuccess=$email->send($message); 
		return $mailSuccess;		
    }
		
	public $validate = array(
		'email_from' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'subject' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'body_html' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'SystemEmailAttachment' => array(
			'className' => 'SystemEmailAttachment',
			'foreignKey' => 'system_email_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'SystemEmailBcc' => array(
			'className' => 'SystemEmailBcc',
			'foreignKey' => 'system_email_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'SystemEmailCc' => array(
			'className' => 'SystemEmailCc',
			'foreignKey' => 'system_email_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'SystemEmailRecipient' => array(
			'className' => 'SystemEmailRecipient',
			'foreignKey' => 'system_email_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
