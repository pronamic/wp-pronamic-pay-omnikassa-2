<?php
/**
 * Order
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2023 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

use DateTime;

/**
 * Order
 *
 * @author  Remco Tolsma
 * @version 2.2.4
 * @since   1.0.0
 */
class Order extends Message implements \JsonSerializable {
	/**
	 * ISO 8601 standard Date / time on which the order is announced at ROK.
	 * As a rule, this is the current date / time.
	 *
	 * This field is mandatory and provides protection against so-called
	 * replay (playback) attacks
	 *
	 * @var DateTime
	 */
	private $timestamp;

	/**
	 * Generated by Merchant. If your webshop wants to use AfterPay, this field must be unique.
	 *
	 * @var string
	 */
	private $merchant_order_id;

	/**
	 * Description of the order.
	 *
	 * @var string|null
	 */
	private $description;

	/**
	 * The order items.
	 *
	 * @var OrderItems|null
	 */
	private $order_items;

	/**
	 * Amount.
	 *
	 * @var Money
	 */
	private $amount;

	/**
	 * The shipping address.
	 *
	 * @var Address|null
	 */
	private $shipping_detail;

	/**
	 * The billing address.
	 *
	 * @var Address|null
	 */
	private $billing_detail;

	/**
	 * The customer information.
	 *
	 * @var CustomerInformation|null
	 */
	private $customer_information;

	/**
	 * Language.
	 *
	 * ISO 639-1 standard. Not Case sensitive.
	 *
	 * @var string|null
	 */
	private $language;

	/**
	 * Merchant return URL.
	 *
	 * The URL to which the consumer's browser will be sent after the payment.
	 *
	 * @var string
	 */
	private $merchant_return_url;

	/**
	 * Payment brand.
	 *
	 * This field is optional and is used to enforce a specific
	 * payment method with the consumer instead of the consumer
	 * selecting a payment method on the payment method selection
	 * page.
	 *
	 * Valid values are:
	 * • IDEAL
	 * • AFTERPAY
	 * • PAYPAL
	 * • MASTERCARD
	 * • VISA
	 * • BANCONTACT
	 * • MAESTRO
	 * • V_PAY
	 * • CARDS
	 *
	 * The CARDS value ensures that the consumer can choose
	 * between payment methods: MASTERCARD, VISA, BANCONTACT,
	 * MAESTRO and V_PAY
	 *
	 * @var string|null
	 */
	private $payment_brand;

	/**
	 * Payment brand force.
	 *
	 * This field should only be delivered if the paymentBrand field (see
	 * above) is also specified. This field can be used to send or, after
	 * a failed payment, the consumer can or can not select another payment
	 * method to still pay the payment.
	 *
	 * Valid values are:
	 * • FORCE_ONCE
	 * • FORCE_ALWAYS
	 *
	 * In the case of FORCE_ONCE, the indicated paymentBrand is only
	 * enforced on the first transaction. If this fails, the consumer
	 * can still choose another payment method. When FORCE_ALWAYS is
	 * chosen, the consumer can not choose another payment method.
	 *
	 * @var string|null
	 */
	private $payment_brand_force;

	/**
	 * Additional metadata specific to the brand as set in the paymentBrand field.
	 *
	 * @var object|null
	 */
	private $payment_brand_meta_data;

	/**
	 * Construct order.
	 *
	 * @param string $merchant_order_id    Merchant order ID.
	 * @param Money  $amount               Amount.
	 * @param string $merchant_return_url  Merchant return URL.
	 * @return void
	 */
	public function __construct( $merchant_order_id, $amount, $merchant_return_url ) {
		$this->set_timestamp( new DateTime() );
		$this->set_merchant_order_id( $merchant_order_id );
		$this->set_amount( $amount );
		$this->set_merchant_return_url( $merchant_return_url );
	}

	/**
	 * Set timestamp.
	 *
	 * @param DateTime $timestamp Timestamp.
	 * @return void
	 */
	public function set_timestamp( DateTime $timestamp ) {
		$this->timestamp = $timestamp;
	}

	/**
	 * Get merchant order ID.
	 *
	 * @return string
	 */
	public function get_merchant_order_id() {
		return $this->merchant_order_id;
	}

	/**
	 * Set merchant order ID.
	 *
	 * Generated by Merchant. If you want your webshop to use AfterPay, this field must be unique.
	 * If the ID contains more than 24 characters, the extra characters are removed after the 24th character.
	 *
	 * @param string $merchant_order_id Merchant order ID.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 10`.
	 */
	public function set_merchant_order_id( $merchant_order_id ) {
		DataHelper::validate_ans( $merchant_order_id, 24, 'Order.merchantOrderId' );

		$this->merchant_order_id = $merchant_order_id;
	}

	/**
	 * Set amount.
	 *
	 * @param Money $amount Amount.
	 * @return void
	 */
	public function set_amount( Money $amount ) {
		$this->amount = $amount;
	}

	/**
	 * Set merchant return URL.
	 *
	 * The URL to which the consumer's browser will be sent after the payment.
	 *
	 * @param string $url Merchant return URL.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 1024`.
	 */
	public function set_merchant_return_url( $url ) {
		DataHelper::validate_an( $url, 1024, 'Order.merchantReturnURL' );

		$this->merchant_return_url = $url;
	}

	/**
	 * Set description.
	 *
	 * @param string|null $description Description.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..max 35`.
	 */
	public function set_description( $description ) {
		DataHelper::validate_null_or_an( $description, 35, 'Order.description' );

		$this->description = $description;
	}

	/**
	 * Set language.
	 *
	 * @param string|null $language Language (ISO 3166-1 alpha-2).
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..2`.
	 */
	public function set_language( $language ) {
		DataHelper::validate_null_or_an( $language, 2, 'Order.language' );

		$this->language = $language;
	}

	/**
	 * Set payment brand.
	 *
	 * @param string|null $payment_brand Payment brand.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..50`.
	 */
	public function set_payment_brand( $payment_brand ) {
		DataHelper::validate_null_or_an( $payment_brand, 50, 'Order.paymentBrand' );

		$this->payment_brand = $payment_brand;
	}

	/**
	 * Set payment brand force.
	 *
	 * @param string|null $payment_brand_force Payment brand force.
	 * @return void
	 * @throws \InvalidArgumentException Throws invalid argument exception when value does not apply to format `AN..50`.
	 */
	public function set_payment_brand_force( $payment_brand_force ) {
		DataHelper::validate_null_or_an( $payment_brand_force, 50, 'Order.paymentBrandForce' );

		$this->payment_brand_force = $payment_brand_force;
	}

	/**
	 * Set payment brand meta data.
	 *
	 * @param object|null $payment_brand_meta_data Payment brand meta data.
	 * @return void
	 */
	public function set_payment_brand_meta_data( $payment_brand_meta_data ) {
		$this->payment_brand_meta_data = $payment_brand_meta_data;
	}

	/**
	 * Create and set new order items.
	 *
	 * @return OrderItems
	 */
	public function new_items() {
		$this->order_items = new OrderItems();

		return $this->order_items;
	}

	/**
	 * Set order items.
	 *
	 * @param OrderItems|null $order_items Order items.
	 * @return void
	 */
	public function set_order_items( OrderItems $order_items = null ) {
		$this->order_items = $order_items;
	}

	/**
	 * Set shipping detail.
	 *
	 * @param Address|null $shipping_detail Shipping address details.
	 * @return void
	 */
	public function set_shipping_detail( Address $shipping_detail = null ) {
		$this->shipping_detail = $shipping_detail;
	}

	/**
	 * Set billing detail.
	 *
	 * @param Address|null $billing_detail Billing address details.
	 * @return void
	 */
	public function set_billing_detail( Address $billing_detail = null ) {
		$this->billing_detail = $billing_detail;
	}

	/**
	 * Set customer information.
	 *
	 * @param CustomerInformation $customer_information Customer information.
	 * @return void
	 */
	public function set_customer_information( CustomerInformation $customer_information ) {
		$this->customer_information = $customer_information;
	}

	/**
	 * Get JSON object.
	 *
	 * @return object
	 */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		$data = [];

		$data['timestamp']       = $this->timestamp->format( \DATE_ATOM );
		$data['merchantOrderId'] = $this->merchant_order_id;

		if ( null !== $this->description ) {
			$data['description'] = $this->description;
		}

		if ( null !== $this->order_items ) {
			$data['orderItems'] = $this->order_items;
		}

		$data['amount'] = $this->amount;

		if ( null !== $this->shipping_detail ) {
			$data['shippingDetail'] = $this->shipping_detail;
		}

		if ( null !== $this->billing_detail ) {
			$data['billingDetail'] = $this->billing_detail;
		}

		if ( null !== $this->customer_information ) {
			$data['customerInformation'] = $this->customer_information;
		}

		if ( null !== $this->language ) {
			$data['language'] = $this->language;
		}

		$data['merchantReturnURL'] = $this->merchant_return_url;

		if ( null !== $this->payment_brand ) {
			$data['paymentBrand'] = $this->payment_brand;
		}

		if ( null !== $this->payment_brand_force ) {
			$data['paymentBrandForce'] = $this->payment_brand_force;
		}

		if ( null !== $this->payment_brand_meta_data ) {
			$data['paymentBrandMetaData'] = $this->payment_brand_meta_data;
		}

		return (object) $data;
	}

	/**
	 * Get signature fields.
	 *
	 * @param array<string> $fields Fields.
	 * @return array<string>
	 */
	public function get_signature_fields( $fields = [] ) {
		$fields[] = $this->timestamp->format( \DATE_ATOM );
		$fields[] = $this->merchant_order_id;

		$fields = $this->amount->get_signature_fields( $fields );

		$fields[] = \strval( (string) $this->language );
		$fields[] = \strval( (string) $this->description );
		$fields[] = $this->merchant_return_url;

		if ( null !== $this->order_items ) {
			$fields = $this->order_items->get_signature_fields( $fields );
		}

		if ( null !== $this->shipping_detail ) {
			$fields = $this->shipping_detail->get_signature_fields( $fields );
		}

		if ( null !== $this->payment_brand ) {
			$fields[] = $this->payment_brand;
		}

		if ( null !== $this->payment_brand_force ) {
			$fields[] = $this->payment_brand_force;
		}

		if ( null !== $this->customer_information ) {
			$fields = $this->customer_information->get_signature_fields( $fields );
		}

		if ( null !== $this->billing_detail ) {
			$fields = $this->billing_detail->get_signature_fields( $fields );
		}

		return $fields;
	}
}
