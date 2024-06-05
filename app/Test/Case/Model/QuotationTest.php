<?php
App::uses('Quotation', 'Model');

/**
 * Quotation Test Case
 *
 */
class QuotationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
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
		'app.sales_order'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Quotation = ClassRegistry::init('Quotation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Quotation);

		parent::tearDown();
	}

}
