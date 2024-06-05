<?php
App::uses('Product', 'Model');

/**
 * Product Test Case
 *
 */
class ProductTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.product',
		'app.provider',
		'app.products_provider',
		'app.product_line',
		'app.currency',
		'app.invoice_product',
		'app.invoice',
		'app.quotation',
		'app.client',
		'app.contact',
		'app.user',
		'app.role',
		'app.user_log',
		'app.quotation_product'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Product = ClassRegistry::init('Product');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Product);

		parent::tearDown();
	}

}
