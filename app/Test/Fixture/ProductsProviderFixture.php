<?php
/**
 * ProductsProviderFixture
 *
 */
class ProductsProviderFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'product_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'provider_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'application_date' => array('type' => 'date', 'null' => false, 'default' => null),
		'bool_active' => array('type' => 'boolean', 'null' => true, 'default' => '1'),
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
			'product_id' => 1,
			'provider_id' => 1,
			'application_date' => '2015-09-22',
			'bool_active' => 1,
			'created' => '2015-09-22 14:16:45',
			'modified' => '2015-09-22 14:16:45'
		),
	);

}
