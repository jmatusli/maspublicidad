<?php
App::uses('InvoiceProduct', 'Model');

/**
 * InvoiceProduct Test Case
 *
 */
class InvoiceProductTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.invoice_product',
		'app.invoice',
		'app.product',
		'app.currency',
		'app.quotation_product',
		'app.quotation'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->InvoiceProduct = ClassRegistry::init('InvoiceProduct');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->InvoiceProduct);

		parent::tearDown();
	}

}
