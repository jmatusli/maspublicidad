<?php
App::uses('SalesOrderProduct', 'Model');

/**
 * SalesOrderProduct Test Case
 *
 */
class SalesOrderProductTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.sales_order_product',
		'app.sales_order',
		'app.product',
		'app.provider',
		'app.products_provider',
		'app.product_line',
		'app.currency',
		'app.invoice_product',
		'app.invoice',
		'app.quotation',
		'app.user',
		'app.role',
		'app.user_log',
		'app.client',
		'app.contact',
		'app.quotation_product',
		'app.sales_order_product_status'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SalesOrderProduct = ClassRegistry::init('SalesOrderProduct');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SalesOrderProduct);

		parent::tearDown();
	}

}
