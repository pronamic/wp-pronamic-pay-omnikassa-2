<?php

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Title: OmniKassa 2.0 gateway
 * Description:
 * Copyright: Copyright (c) 2017
 * Company: Pronamic
 *
 * @author Remco Tolsma
 * @version 1.0.0
 * @since 1.0.0
 */
class Gateway extends \Pronamic_WP_Pay_Gateway {
	/**
	 * The OmniKassa client object
	 *
	 * @var Pronamic_WP_Pay_Gateways_OmniKassa_Client
	 */
	private $client;

	/////////////////////////////////////////////////

	/**
	 * Constructs and initializes an OmniKassa gateway
	 *
	 * @param Pronamic_WP_Pay_Gateways_OmniKassa_Config $config
	 */
	public function __construct( Config $config ) {
		parent::__construct( $config );

		$this->set_method( \Pronamic_WP_Pay_Gateway::METHOD_HTML_FORM );
		$this->set_has_feedback( true );
		$this->set_amount_minimum( 0.01 );

		// Client
		$this->client = new Client();

		$url = Client::URL_PRODUCTION;

		if ( \Pronamic_IDeal_IDeal::MODE_TEST === $config->mode ) {
			$url = Client::URL_ACCEPTANCE;
			$url = Client::URL_SANDBOX;
		}

		$this->client->set_url( $url );
		$this->client->set_refresh_token( $config->refresh_token );
		$this->client->set_signing_key( $config->signing_key );
	}

	/////////////////////////////////////////////////

	/**
	 * Get supported payment methods
	 *
	 * @see Pronamic_WP_Pay_Gateway::get_supported_payment_methods()
	 */
	public function get_supported_payment_methods() {
		return array(
			\Pronamic_WP_Pay_PaymentMethods::IDEAL,
			\Pronamic_WP_Pay_PaymentMethods::CREDIT_CARD,
			\Pronamic_WP_Pay_PaymentMethods::DIRECT_DEBIT,
			\Pronamic_WP_Pay_PaymentMethods::BANCONTACT,
		);
	}

	/////////////////////////////////////////////////

	/**
	 * Start
	 *
	 * @see Pronamic_WP_Pay_Gateway::start()
	 * @param Pronamic_Pay_PaymentDataInterface $data
	 */
	public function start( \Pronamic_Pay_Payment $payment ) {
		$order = new Order();
		$order->timestamp           = date( DATE_ATOM );
		$order->merchant_order_id   = $payment->get_id();
		$order->description         = $payment->get_description();
		$order->amount              = $payment->get_amount();
		$order->currency            = $payment->get_currency();
		$order->language            = $payment->get_language();
		$order->merchant_return_url = $payment->get_return_url();

		if ( ! $this->config->is_access_token_valid() ) {
			$data = $this->client->get_access_token_data();
var_dump( $data );
			if ( false !== $data ) {
				return;
			}

			$this->config->acces_token              = $data->token;
			$this->config->access_token_valid_until = $data->validUntil;

			update_post_meta( $this->config->post_id, '_pronamic_gateway_omnikassa_2_access_token', $data->token );
			update_post_meta( $this->config->post_id, '_pronamic_gateway_omnikassa_2_access_token_valid_until', $data->validUntil );
		}

		$result = $this->client->order_announce( $this->config, $order );

		var_dump( $result );
		exit;
	}

	/////////////////////////////////////////////////

	/**
	 * Get the output HTML
	 *
	 * @since 1.1.2
	 * @see Pronamic_WP_Pay_Gateway::get_output_html()
	 */
	public function get_output_fields() {
		return $this->client->get_fields();
	}

	/////////////////////////////////////////////////

	/**
	 * Update status of the specified payment
	 *
	 * @param Pronamic_Pay_Payment $payment
	 */
	public function update_status( \Pronamic_Pay_Payment $payment ) {
		
	}
}
