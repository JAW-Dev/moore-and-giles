<?php
/**
 * Call Template Function.
 *
 * @package    MG_VIP_Customer
 * @subpackage MG_VIP_Customer/Inlcudes/Classes/Core
 * @author     Objectiv
 * @copyright  Copyright (c) Plugin_Boilerplate_Date, Objectiv
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since      1.0.0
 */

namespace MG_VIP_Customer\Includes\Classes;

if ( ! defined( 'WPINC' ) ) {
	wp_die( 'No Access Allowed!', 'Error!', array( 'back_link' => true ) );
}

if ( ! class_exists( __NAMESPACE__ . '\\Call_Template_Function' ) ) {

	/**
	 * Enqueue Styles
	 *
	 * @author Jason Witt
	 * @since  1.0.0
	 */
	class Call_Template_Function {

		/**
		 * Initialize the class
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @return void
		 */
		public function __construct() {}

		/**
		 * Initialize.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $callback The function to callback.
		 * @param mixed  ...$args  The arguments for the function.
		 *
		 * @return void
		 */
		public function init( $callback, ...$args ) {
			$this->include_file( $callback );

			call_user_func( $callback, $args );
		}

		/**
		 * Scan the Directory.
		 *
		 * @author Jason Witt
		 * @since  1.0.0
		 *
		 * @param string $pattern The file to find.
		 *
		 * @return void
		 */
		public function include_file( $pattern ) {

			if ( null === $pattern ) {
				return;
			}

			// Recursively scan the dirs for files.
			$files = new \RecursiveDirectoryIterator( trailingslashit( plugin_dir_path( __FILE__ ) ) . trailingslashit( 'classes' ) );

			// Loop through the files.
			foreach ( new \RecursiveIteratorIterator( $files ) as $file ) {
				$filename = 'moore-and-giles' . $file->getFilename();
				$filepath = $file->getPathname();

				// Exclude dot files.
				if ( '.' === substr( $filename, 0, 1 ) ) {
					continue;
				}

				// Get the path to the file.
				$file = $filepath;

				// Get the file extension.
				// $extension = substr( $file, strrpos( $file, '.' ) + 1 );.
				// If 'Load' is true and the file is a PHP file.
				if ( $filename === $pattern . '.php' ) {

					include $file;
				}
			}
		}
	}
}
