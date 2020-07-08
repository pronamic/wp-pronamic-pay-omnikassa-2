<?php
/**
 * Message
 *
 * @author    Pronamic <info@pronamic.eu>
 * @copyright 2005-2020 Pronamic
 * @license   GPL-3.0-or-later
 * @package   Pronamic\WordPress\Pay\Gateways\OmniKassa2
 */

namespace Pronamic\WordPress\Pay\Gateways\OmniKassa2;

/**
 * Message
 *
 * @author  Remco Tolsma
 * @version 2.2.4
 * @since   2.0.2
 */
abstract class Message implements Signable {
	/**
	 * Signature.
	 *
	 * @var string|null
	 */
	private $signature;

	/**
	 * Get signature.
	 *
	 * @return string|null
	 */
	public function get_signature() {
		return $this->signature;
	}

	/**
	 * Set signature.
	 *
	 * @param string|null $signature Signature.
	 * @return void
	 */
	protected function set_signature( $signature ) {
		$this->signature = $signature;
	}

	/**
	 * Sign this message with specified signing key.
	 *
	 * @param string $signing_key Signing key.
	 * @return void
	 */
	public function sign( $signing_key ) {
		$signature = Security::get_signature( $this, $signing_key );

		$this->set_signature( $signature );
	}

	/**
	 * Check if this message is valid.
	 *
	 * @param string $signing_key Signing key.
	 * @return bool True if valid, false otherwise.
	 */
	public function is_valid( $signing_key ) {
		$signature_a = Security::get_signature( $this, $signing_key );

		if ( empty( $signature_a ) ) {
			return false;
		}

		$signature_b = $this->get_signature();

		if ( empty( $signature_b ) ) {
			return false;
		}

		return Security::validate_signature( $signature_a, $signature_b );
	}
}
