<?php
App::uses('ProductsProvider', 'Model');

/**
 * ProductsProvider Test Case
 *
 */
class ProductsProviderTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.products_provider',
		'app.product',
		'app.provider',
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
		$this->ProductsProvider = ClassRegistry::init('ProductsProvider');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProductsProvider);

		parent::tearDown();
	}

}
