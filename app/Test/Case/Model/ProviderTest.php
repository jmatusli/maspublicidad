<?php
App::uses('Provider', 'Model');

/**
 * Provider Test Case
 *
 */
class ProviderTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.provider',
		'app.product',
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
		$this->Provider = ClassRegistry::init('Provider');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Provider);

		parent::tearDown();
	}

}
