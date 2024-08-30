<?php
/**
 * The Stock availability text.
 * @author Jason Witt
 */

namespace Objectiv\Site\Utilities;

use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * StockAvailability
 *
 * @author Jason Witt
 */
class StockAvailability {

	/**
	 * Initialize the class
	 *
	 * @author Jason Witt
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * New Tag.
	 *
	 * @param array $availability The availability array.
	 * @param \WC_Product $product The product object.
	 *
	 * @return array
	 * @throws \Exception
	 * @author Jason Witt
	 *
	 */
	public static function stock_text( $availability, $product ) {
		if ( ! $product->is_in_stock() ) {
			$availability['availability'] = __( 'Out of Stock', 'moore-and-giles' );
		} elseif ( $product->is_on_backorder() ) {
			$availability['availability'] = self::get_estimated_ship_date( $product );
		} else {
			$availability['availability'] = '';
		}

		return $availability;
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function get_estimated_ship_date( $product ) {
		$timezone               = get_option( 'timezone_string' );
		$release_date           = $product->get_meta( 'release_date' );
		$processing_time_prefix = $product->get_meta( 'processing_time__prefix' ) === 'Array' ? '' : $product->get_meta( 'processing_time__prefix' );
		$release_date_range     = $product->get_meta( 'release_date_range' );
		$processing_date_range  = self::processing_date_range( $product );
		$preposition            = 'in';

		$processing_time_string = '24-48 hours';

		if ( ! empty( $release_date ) ) {
			$release_date = new Carbon( $release_date, $timezone );
			$now          = Carbon::now( $timezone );

			if ( $release_date->greaterThan( $now ) ) {
				if ( $release_date_range ) {
					$processing_time_string = $now->diffForHumans( $release_date, array( 'syntax' => CarbonInterface::DIFF_ABSOLUTE, 'options' => CarbonInterface::CEIL )  );
				} else {
					$preposition = 'on';
					$processing_time_string = $release_date->format( 'm/d/Y' );
				}
			}
		} elseif ( ! empty( $processing_date_range ) ) {
			$processing_time_string = $processing_date_range;
		} else {
			$processing_time_string = '1 - 2 weeks';
		}

		$pre_order_type = 'Pre-order';

		if ( has_term( 'Shop Home', 'product_cat', $product->get_parent_id() ? $product->get_parent_id() : $product->get_id()  ) ) {
			$pre_order_type = 'Made to Order';
		}

		if ( ! empty( get_field( 'mg_pre_order_text') ) ) {
			$pre_order_type = get_field( 'mg_pre_order_text');
		}

		if ( ! empty( $processing_time_prefix ) ) {
			$processing_time_string = "{$pre_order_type}: $processing_time_prefix $processing_time_string.";
		} else {
			$processing_time_string = "{$pre_order_type}: Expected to ship $preposition $processing_time_string.";
		}

		return $processing_time_string;
	}

	/**
	 * @param \WC_Product $product
	 *
	 * @return bool|string
	 */
	public static function processing_date_range( $product ) {
		$processing_from        = $product->get_meta( 'processing_time__from' );
		$processing_to          = $product->get_meta( 'processing_time__to' );

		if ( empty( $processing_from ) || empty( $processing_to ) ) {
			return false;
		}

		list ( $min_interval, $min_p ) = sscanf( $processing_from, '%d%s' );
		list ( $max_interval, $max_p ) = sscanf( $processing_to, '%d%s' );

		$same_unit     = ( $min_p == $max_p );
		$same_interval = ( $min_interval == $max_interval );

		// Same unit, different intervals
		if ( $same_unit && $same_interval && $max_p == 'd' && $max_interval == '1' ) {
			return '24 hours';
		} elseif ( $same_unit && ! $same_interval ) {
			$unit = str_replace( 'd', ' days', $min_p );
			$unit = str_replace( 'w', ' weeks', $unit );
			$unit = str_replace( 'm', ' months', $unit );

			return "$min_interval - $max_interval $unit";
		} elseif ( $same_unit && $same_interval ) {
			if ( $min_interval > 1 ) {
				$unit = str_replace( 'd', ' days', $min_p );
				$unit = str_replace( 'w', ' weeks', $unit );
				$unit = str_replace( 'm', ' months', $unit );
			} else {
				$unit = str_replace( 'd', ' day', $min_p );
				$unit = str_replace( 'w', ' week', $unit );
				$unit = str_replace( 'm', ' month', $unit );
			}

			return "$min_interval $unit";
		} elseif ( ! $same_unit ) {
			if ( $min_interval > 1 ) {
				$min_unit = str_replace( 'd', ' days', $min_p );
				$min_unit = str_replace( 'w', ' weeks', $min_unit );
				$min_unit = str_replace( 'm', ' months', $min_unit );
			} else {
				$min_unit = str_replace( 'd', ' day', $min_p );
				$min_unit = str_replace( 'w', ' week', $min_unit );
				$min_unit = str_replace( 'm', ' month', $min_unit );
			}

			if ( $max_interval > 1 ) {
				$max_unit = str_replace( 'd', ' days', $max_p );
				$max_unit = str_replace( 'w', ' weeks', $max_unit );
				$max_unit = str_replace( 'm', ' months', $max_unit );
			} else {
				$max_unit = str_replace( 'd', ' day', $max_p );
				$max_unit = str_replace( 'w', ' week', $max_unit );
				$max_unit = str_replace( 'm', ' month', $max_unit );
			}

			return "$min_interval $min_unit - $max_interval $max_unit";
		}

		return false;
	}
}
