<?php

class MG_VIP_Program {
	public function __construct() {
		// Add VIP prefix to orders view
		add_action( 'shopp_manage_orders_column_order_before', array( $this, 'add_vip_prefix' ) );

		// Override gift wrapping discount
		add_filter( 'shopp_apply_discount', array( $this, 'override_gift_wrapping_discount' ), 100, 2 );
		add_filter( 'shopp_apply_discount', array( $this, 'override_personalization_discount' ), 101, 2 );
		add_filter( 'shopp_apply_discount', array( $this, 'override_twoday_shipping_discount' ), 102, 2 );

		// During email address lookup
		add_filter( 'mg_email_matches_account_response', array( $this, 'maybe_add_vip_discounts_to_response' ), 10, 2 );

		// During impersonation
		add_filter( 'mg_impersonate_customer_return', array( $this, 'maybe_add_vip_discount_to_impersonate_response' ), 10, 1 );

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'shopp-orders' ) {
			add_action( 'admin_head', array( $this, 'admin_styles' ) );
		}
	}

	function add_vip_prefix( $Order ) {
		if ( $this->is_user_vip( $Order->email ) ) {
			echo "<span class='mg-order-label'>VIP</span>";
		}
	}

	function admin_styles() {
		echo '<style>.mg-order-label {
			background: red;
			border-radius: 6px;
			padding: 2px 4px;
			color: white;
		}</style>';
	}

	function override_gift_wrapping_discount( $apply, $promo ) {
		if ( $promo->name == 'Free Gift Wrapping' && ( ! isset( $_REQUEST['removecode'] ) || $_REQUEST['removecode'] != 51 ) ) {
			if ( $this->is_user_vip() ) {
				ShoppOrder()->Discounts->addcode( 'VIPFREEGIFTWRAPPING', $promo );
				$apply = true;
			}
		}

		return $apply;
	}

	function override_personalization_discount( $apply, $promo ) {
		if ( $promo->name == 'Free Personalization' && ( ! isset( $_REQUEST['removecode'] ) || $_REQUEST['removecode'] != 105 ) ) {
			if ( $this->is_user_vip() ) {
				ShoppOrder()->Discounts->addcode( 'VIPFREEPERSONALIZATION', $promo );
				$apply = true;
			}
		}

		return $apply;
	}

	function override_twoday_shipping_discount( $apply, $promo ) {
		if ( $promo->name == 'Free 2-Day Shipping' && ( ! isset( $_REQUEST['removecode'] ) || $_REQUEST['removecode'] != 106 ) ) {
			if ( $this->is_user_vip() ) {
				ShoppOrder()->Discounts->addcode( 'VIPFREE2DAYSHIPPING', $promo );
				$apply = true;
			}
		}

		return $apply;
	}

	function is_user_vip( $email = false ) {
		global $vip_emails;
		include 'vip-customers.php';

		if ( false !== $email ) {
			return in_array( strtolower( $email ), $vip_emails, true );
		}

		$customer = ShoppCustomer();

		if ( ! empty( $customer->email ) ) {
			$email = strtolower( $customer->email );

			if ( in_array( $email, $vip_emails, true ) ) {
				return true;
			}
		}

		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			$email        = strtolower( $current_user->user_email );

			if ( in_array( $email, $vip_emails, true ) ) {
				return true;
			}
		}

		return false;
	}

	function maybe_add_vip_discounts_to_response( $response, $email ) {
		if ( $this->is_user_vip( $email ) ) {
			ShoppOrder()->Customer->email = $email;

			$applied_codes = ShoppOrder()->Discounts->codes();
			$valid_promos  = array( 'VIPFREE2DAYSHIPPING', 'VIPFREEPERSONALIZATION', 'VIPFREEGIFTWRAPPING' );
			$valid_promos  = array_map( 'strtolower', $valid_promos );
			$intersection  = array_intersect( $applied_codes, $valid_promos );

			if ( count( $intersection ) === 0 ) {
				$response['message'] = 'VIP account confirmed. We have applied your discounts. To see them, please refresh the page, or you can continue with your order.';
			}

			ShoppOrder()->Cart->totals();
		}

		return $response;
	}

	function maybe_add_vip_discount_to_impersonate_response( $return ) {
		error_log( print_r($return, true) );
		if ( is_email( $return['email'] ) && $this->is_user_vip( $return['email'] ) ) {
			$applied_codes = ShoppOrder()->Discounts->codes();
			$valid_promos  = array( 'VIPFREE2DAYSHIPPING', 'VIPFREEPERSONALIZATION', 'VIPFREEGIFTWRAPPING' );
			$valid_promos  = array_map( 'strtolower', $valid_promos );
			$intersection  = array_intersect( $applied_codes, $valid_promos );

			error_log( print_r($applied_codes, true) );
			error_log( print_r($valid_promos, true) );
			error_log( print_r($intersection, true) );

			if ( count( $intersection ) === 0 ) {
				$return['message'] = 'VIP account confirmed and discounts applied. To see them, please refresh the page.';
			}

			ShoppOrder()->Cart->totals();
		}

		return $return;
	}
}
