<?php
/**
 * Class MG_Shopp_Rest Test
 *
 * @package Mg_Shopp_Rest
 */

/**
 * Testing Methods of MG_Shopp_Rest_Class
 */
class MG_Shopp_Rest_Test extends WP_UnitTestCase {

	/**
	 * Get Reflection method for testing private or protected functions.
	 *
	 * @param string $name Name of the method to get.
	 */
	protected static function getMethod( $name ) {
		$class  = new ReflectionClass( 'MG_Shopp_Rest' );
		$method = $class->getMethod( $name );
		$method->setAccessible( true );
		return $method;
	}

	/**
	 * @covers MG_Shopp_Rest::is_valid_http_request
	 */
	function testValidateHttpRequest() {
		$callable = self::getMethod( 'is_valid_http_request' );

		$good_request = new WP_REST_Request();
		$good_request->set_header( 'secret', 'Eao2HnLbFDAZtJwHbKG82qarqns2OOhty849GnDNpApFvqZmevc9wXnUxkZyud01' );
		$this->assertTrue( $callable->invoke( new MG_Shopp_Rest, $good_request ), 'Evaluated false with the correct secret' );

		$secretless_request = new WP_REST_Request();
		$this->assertFalse( $callable->invoke( new MG_Shopp_Rest, $secretless_request ), 'Evaluated true with no secret' );

		$bad_request = new WP_REST_Request();
		$bad_request->set_header( 'secret', '' );
		$this->assertFalse( $callable->invoke( new MG_Shopp_Rest, $bad_request ), 'Evaluated true from a secret of an empty string.' );
	}

	/**
	 * @covers MG_Shopp_Rest::is_valid_swatch_request
	 */
	function testValidateSwatchRequest() {
		$callable = self::getMethod( 'is_valid_swatch_request' );

		/**
		 * Helper function to remove keys from array in order to test arrays missing those keys.
		 *
		 * @param array  $array The array to copy.
		 * @param string $key_to_remove The key to leave off the new copy.
		 */
		function copy_minus_one_key( $array, $key_to_remove ) {
			return array_filter(
				$array,
				function( $key ) use ( $key_to_remove ) {
					return $key !== $key_to_remove;
				},
				ARRAY_FILTER_USE_KEY
			);
		}

		$correct_params = array(
			'leathers'        => array(
				'6.1' => 'American Bison',
				'6.2' => 'Modern Saddle',
			),
			'first_name'      => 'John',
			'last_name'       => 'Jingleheimerschmidt',
			'email'           => 'john@mynametoo.com',
			'phone'           => '(123) 456-7890',
			'address_line_1'  => 'Drury Ln',
			'address_city'    => 'Drurysville',
			'address_state'   => 'Rhode Island',
			'address_zip'     => '12345',
			'address_country' => 'US',
		);

		// Testing correct case.
		$this->assertTrue( $callable->invoke( new MG_Shopp_Rest, $correct_params ), 'A valid swatch request failed the validation.' );

		// Testing for missing indices.
		foreach ( array_keys( $correct_params ) as $key ) {
			$this->assertFalse(
				$callable->invoke( new MG_Shopp_Rest, copy_minus_one_key( $correct_params, $key ) ),
				"A swatch request missing $key passed validation."
			);
		}

		// Testing an invalid email.
		$has_invalid_email          = copy_minus_one_key( $correct_params, '' );
		$has_invalid_email['email'] = 'wrong';
		$this->assertFalse( $callable->invoke( new MG_Shopp_Rest, $has_invalid_email ), 'A swatch request with an invalid email address passed validation.' );
	}
}
