<?php
App::uses('ProductLine', 'Model');

/**
 * ProductLine Test Case
 *
 */
class ProductLineTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.product_line',
		'app.product'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->ProductLine = ClassRegistry::init('ProductLine');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->ProductLine);

		parent::tearDown();
	}

}
