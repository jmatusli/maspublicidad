<?php
App::uses('ClientsController', 'Controller');

/**
 * ClientsController Test Case
 *
 */
class ClientsControllerTest extends ControllerTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.client',
		'app.contact',
		'app.quotation',
		'app.user',
		'app.role',
		'app.user_log',
		'app.currency',
		'app.purchase_order_product',
		'app.invoice',
		'app.invoice_product',
		'app.product',
		'app.provider',
		'app.products_provider',
		'app.product_line',
		'app.quotation_product',
		'app.sales_order',
		'app.sales_order_product',
		'app.sales_order_product_status'
	);

/**
 * testIndex method
 *
 * @return void
 */
	public function testIndex() {
		$this->markTestIncomplete('testIndex not implemented.');
	}

/**
 * testView method
 *
 * @return void
 */
	public function testView() {
		$this->markTestIncomplete('testView not implemented.');
	}

/**
 * testAdd method
 *
 * @return void
 */
	public function testAdd() {
		$this->markTestIncomplete('testAdd not implemented.');
	}

/**
 * testEdit method
 *
 * @return void
 */
	public function testEdit() {
		$this->markTestIncomplete('testEdit not implemented.');
	}

/**
 * testDelete method
 *
 * @return void
 */
	public function testDelete() {
		$this->markTestIncomplete('testDelete not implemented.');
	}

}
