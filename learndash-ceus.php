<?php

/**
 * @link              https://wooninjas.com/
 * @since             1.0.0
 * @package           Ld_Ceus
 *
 * @wordpress-plugin
 * Plugin Name:       LearnDash CEUs
 * Plugin URI:        https://wooninjas.com/downloads/learndash-ceus/
 * Description:       The LearnDash CEUs Add-on allows you to track, manage, and report on credits earned for learning activities. Assign credits, CEUs, or CPD values to your courses and award certificates, track compliance training, and report on the cumulative accomplishments of your students.
 * Version:           1.0.0
 * Author:            WooNinjas
 * Author URI:        https://wooninjas.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       learndash-ceus
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'LEARNDASH_CEUS_VERSION', '1.0.0' );


/**
 * Set plugin FILE to access it globally
 */
define( 'LEARNDASH_CEUS_FILE', __FILE__ );
define( 'LEARNDASH_CEUS_URL', plugin_dir_url( __FILE__ )) ;
define( 'LEARNDASH_CEUS_PLUGIN_NAME', 'LearnDash CEUs' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ld-ceus-activator.php
 */
function activate_ld_ceus() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ld-ceus-activator.php';
	Ld_Ceus_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ld-ceus-deactivator.php
 */
function deactivate_ld_ceus() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ld-ceus-deactivator.php';
	Ld_Ceus_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ld_ceus' );
register_deactivation_hook( __FILE__, 'deactivate_ld_ceus' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ld-ceus.php';

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_ld_ceus() {
    $plugin = new Ld_Ceus();
    $plugin->run();
}

/**
 * Deactivate plugin if learndash is not available.
 */
function ld_ceus_activation_dependency_check(){
	if( ! current_user_can('manage_options') || ! is_admin()  ) {
		return;
	}
	if ( ! class_exists( 'SFWD_LMS' ) ) {
		$class   = 'notice is-dismissible error';
		$message = sprintf(__( 'LearnDash CEUs add-on requires %1$s plugin to be activated.', 'learndash-ceus' ), '<a href="https://www.learndash.com" target="_BLANK">LearnDash</a>');
		printf( '<div id="message" class="%s"> <p>%s</p></div>', $class, $message );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	return true;
}

/**
 * Callback function for the 'plugins_loaded' action hook.
 */
function ld_ceus_plugins_loaded_cb() {
	if ( ! class_exists( 'SFWD_LMS' ) ) {
		add_action( 'admin_notices', 'ld_ceus_activation_dependency_check' );
		return false;
    }
    run_ld_ceus();
}
add_action( 'plugins_loaded', 'ld_ceus_plugins_loaded_cb' );

