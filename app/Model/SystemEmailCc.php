<?php
App::uses('AppModel', 'Model');
/**
 * SystemEmailCc Model
 *
 * @property SystemEmail $SystemEmail
 */
class SystemEmailCc extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'system_email_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'email_cc' => array(
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
		'SystemEmail' => array(
			'className' => 'SystemEmail',
			'foreignKey' => 'system_email_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
