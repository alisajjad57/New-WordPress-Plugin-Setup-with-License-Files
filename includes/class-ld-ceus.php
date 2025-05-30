<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://wooninjas.com/
 * @since      1.0.0
 *
 * @package    Ld_Ceus
 * @subpackage Ld_Ceus/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ld_Ceus
 * @subpackage Ld_Ceus/includes
 * @author     WooNinjas <info@wooninjas.com>
 */
class Ld_Ceus {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ld_Ceus_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'LEARNDASH_CEUS_VERSION' ) ) {
			$this->version = LEARNDASH_CEUS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'learndash-ceus';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ld_Ceus_Loader. Orchestrates the hooks of the plugin.
	 * - Ld_Ceus_i18n. Defines internationalization functionality.
	 * - Ld_Ceus_Admin. Defines all hooks for the admin area.
	 * - Ld_Ceus_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ld-ceus-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ld-ceus-i18n.php';

		/**
		 * The class responsible for defining global/helper functions
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ld-ceus-helper.php';

		/**
		 * This class is responsible to define all cron related functionality
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ld-ceus-cron.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ld-ceus-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ld-ceus-public.php';

		/**
		 * The class responsible for defining all actions that occur to verify license.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/license/class-ld-ceus-license.php';

		/**
		 * The class responsible for defining all actions that occur to verify and update plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/license/class-ld-ceus-plugin-updater.php';

		$this->loader = new Ld_Ceus_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ld_Ceus_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ld_Ceus_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_license = new Ld_Ceus_License( $this->get_plugin_name(), $this->get_version() );
		$plugin_admin = new Ld_Ceus_Admin( $this->get_plugin_name(), $this->get_version(), $plugin_license );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Plugin related menu, links and notifications
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'plugin_action_links_' . plugin_basename(LEARNDASH_CEUS_FILE), $plugin_admin, 'plugin_action_links' );
		$this->loader->add_action( 'plugin_row_meta', 	$plugin_admin, 'plugin_row_meta', 10, 2 );
		$this->loader->add_action( 'admin_footer_text', $plugin_admin, 'admin_footer_text' );
		$this->loader->add_action( 'admin_notices', 	$plugin_admin, 'plugin_review_notice' );
		$this->loader->add_action( 'admin_init', 		$plugin_admin, 'save_settings' );
		
		// Plugin license initialize and notices
		$this->loader->add_action( 'admin_init', 	$plugin_license, 'plugin_updater' );
		$this->loader->add_action( 'admin_init', 	$plugin_license, 'register_license_option' );
		$this->loader->add_action( 'admin_init', 	$plugin_license, 'activate_license' );
		$this->loader->add_action( 'admin_init', 	$plugin_license, 'deactivate_license' );
		$this->loader->add_action( 'admin_notices', $plugin_license, 'admin_notices' );
		$this->loader->add_action( 'admin_notices', $plugin_license, 'license_activation_notices' );

		if ( Ld_Ceus_Helper::doing_cron() ) {
			$this->loader->add_action( 'ld_ceus_weekly_scheduled_events', $plugin_license, 'weekly_license_check' );
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ld_Ceus_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ld_Ceus_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
