<?php
/**
 * Helper function class to be used globally
 *
 * @link       https://wooninjas.com/
 * @since      1.0.0
 *
 * @package    Ld_Ceus
 * @subpackage Ld_Ceus/includes
 * @since 1.0.0
 */
if( !class_exists('Ld_Ceus_Helper') ) {

	class Ld_Ceus_Helper {

		/**
		 * Debug Log
		 *
		 * @param $var
		 * @param bool $print
		 * @param bool $show_execute_at
		 */
		public static function debug_log($var, $print=true, $show_execute_at=false) {
			ob_start();

			if($show_execute_at) {
				$bt = debug_backtrace();
				$caller = array_shift($bt);
				$execute_at = $caller['file'] . ':' . $caller['line'] . "\n";
				echo $execute_at;
			}

			if( $print ) {
				if( is_object($var) || is_array($var) ) {
					echo print_r($var, true);
				} else {
					echo $var;
				}
			} else {
				var_dump($var);
			}

			error_log(ob_get_clean());
		}

		/**
		 * Check if the current context is a WordPress cron execution.
		 *
		 * @return bool Whether the current context is a WordPress cron execution.
		 */
		public static function doing_cron() {
			// Bail if using WordPress cron (>4.8.0)
			if ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) {
				return true;
			}

			// Bail if using WordPress cron (<4.8.0)
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				return true;
			}

			// Default to false
			return false;
		}


	}
}