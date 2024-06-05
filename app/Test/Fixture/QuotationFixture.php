<?php
/**
 * QuotationFixture
 *
 */
class QuotationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'client_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'contact_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'quotation_date' => array('type' => 'date', 'null' => false, 'default' => null),
		'quotation_code' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'bool_active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'bool_IVA' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
		'price_subtotal' => array('type' => 'decimal', 'null' => false, 'default' => null, 'length' => '10,2', 'unsigned' => false),
		'price_IVA' => array('type' => 'decimal', 'null' => false, 'default' => null, 'length' => '10,2', 'unsigned' => false),
		'price_total' => array('type' => 'decimal', 'null' => false, 'default' => null, 'length' => '10,2', 'unsigned' => false),
		'currency_id' => array('type' => 'integer', 'null' => false, 'default' => '1', 'unsigned' => false),
		'bool_annulled' => array('type' => 'boolean', 'null' => true, 'default' => '0'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'client_id' => 1,
			'contact_id' => 1,
			'quotation_date' => '2015-09-22',
			'quotation_code' => 'Lorem ipsum dolor sit amet',
			'bool_active' => 1,
			'bool_IVA' => 1,
			'price_subtotal' => '',
			'price_IVA' => '',
			'price_total' => '',
			'currency_id' => 1,
			'bool_annulled' => 1,
			'created' => '2015-09-22 14:17:17',
			'modified' => '2015-09-22 14:17:17'
		),
	);

}
