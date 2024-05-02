<?php
/**
 * Order item test
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Order item test
 *
 * @author  Remco Tolsma
 * @version 2.1.8
 * @since   2.0.2
 */
class OrderItemTest extends TestCase {
	/**
	 * Test name.
	 */
	public function test_order_item() {
		$order_item = new OrderItem(
			'Jackie O Round Sunglasses',
			1,
			new Money( 'EUR', 22500 ),
			ProductCategories::PHYSICAL
		);

		$this->assertEquals( 'Jackie O Round Sunglasses', $order_item->get_name() );
	}
}
