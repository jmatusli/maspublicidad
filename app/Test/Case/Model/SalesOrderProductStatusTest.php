<?php
App::uses('SalesOrderProductStatus', 'Model');

/**
 * SalesOrderProductStatus Test Case
 *
 */
class SalesOrderProductStatusTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.sales_order_product_status',
		'app.sales_order_product'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SalesOrderProductStatus = ClassRegistry::init('SalesOrderProductStatus');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SalesOrderProductStatus);

		parent::tearDown();
	}

}
