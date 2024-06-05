<?php
App::uses('AppModel', 'Model');
/**
 * MessageRecipient Model
 *
 * @property Message $Message
 * @property RecipientUser $RecipientUser
 */
class MessageRecipient extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'message_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'recipient_user_id' => array(
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
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Message' => array(
			'className' => 'Message',
			'foreignKey' => 'message_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'RecipientUser' => array(
			'className' => 'User',
			'foreignKey' => 'recipient_user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
