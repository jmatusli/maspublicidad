<?php
App::uses('SalesOrder', 'Model');

/**
 * SalesOrder Test Case
 *
 */
class SalesOrderTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.sales_order',
		'app.quotation',
		'app.user',
		'app.role',
		'app.user_log',
		'app.client',
		'app.contact',
		'app.invoice',
		'app.currency',
		'app.invoice_product',
		'app.product',
		'app.provider',
		'app.products_provider',
		'app.product_line',
		'app.quotation_product',
		'app.sales_order_product',
		'app.sales_order_product_status'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->SalesOrder = ClassRegistry::init('SalesOrder');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->SalesOrder);

		parent::tearDown();
	}

}
