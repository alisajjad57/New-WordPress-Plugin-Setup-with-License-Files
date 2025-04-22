<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wooninjas.com/
 * @since      1.0.0
 *
 * @package    Ld_Ceus
 * @subpackage Ld_Ceus/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Ld_Ceus
 * @subpackage Ld_Ceus/admin
 * @author     WooNinjas <info@wooninjas.com>
 */
class Ld_Ceus_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $license;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      string    $license    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $license = null ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->license = $license;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
						
		if ( isset( $_GET['page'] ) && 'learndash-ceus' === rtrim( $_GET['page'] ) ){
			
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ld-ceus-admin.css', array(), $this->version, 'all' );
			// Font awesome
			wp_enqueue_style( $this->plugin_name . '-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css', array(), $this->version );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		if ( isset( $_GET['page'] ) && 'learndash-ceus' === rtrim( $_GET['page'] ) ){
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ld-ceus-admin.js', array( 'jquery' ), $this->version, false );
			$data = $this->get_localize_data();
			wp_localize_script( $this->plugin_name, 'LDCEUVars', $data );
		}
	}

	/**
	 * Get the localized data for JavaScript.
	 *
	 * @return array Localized data.
	 */
	public function get_localize_data() {
		$data = array(
			'ajaxurl'      => esc_url( admin_url( 'admin-ajax.php' ) ),
			'debug'        => defined( 'WP_DEBUG' ) ? true : false,
			'siteURL'      => site_url(),
			'_ajax_nonce'  => wp_create_nonce( 'learndash_ceus' ),
			'err_msg1'     => __( 'Invalid format!', 'learndash-ceus' ),
			'err_msg2'     => __( 'Error occurred!', 'learndash-ceus' ),
		);

		return $data;
	}

	/**
	 * Add plugin action links
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function plugin_action_links( $links ) {
		$settings_link = '<a href="' . admin_url('admin.php?page=' . $this->plugin_name . '&tab=settings') . '">'. __( 'Settings' ). '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	/**
	 * Add the plugin's menu to the WordPress admin.
	 */
	public function admin_menu() {
		add_submenu_page(
			'learndash-lms',									// Parent Slug (Menu Slug)
			__( 'LearnDash CEU\'s', 'learndash-ceus' ),			// Page Title
			__( 'LearnDash CEU\'s', 'learndash-ceus' ),			// Menu Title
			'manage_options',									// Capability
			$this->plugin_name,									// Menu Slug
			[ $this, 'plugin_page' ]							// Callback Function
		);
	}


	/**
	 * Modify the plugin's row meta links displayed on the plugins page.
	 *
	 * @param array  $links Array of existing row meta links.
	 * @param string $file  Path to the plugin file.
	 *
	 * @return array Modified row meta links.
	 */
	public function plugin_row_meta($links, $file) {
		if ( plugin_basename( LEARNDASH_CEUS_FILE ) === $file ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'learndash-ceus_docs_url', 'https://wooninjas.com/docs/learndash-addons/learndash-ceus/' ) ) . '" aria-label="' . sprintf( esc_attr__( 'View %s documentation', 'learndash-ceus' ), $this->plugin_name ) . '" target="_blank">' . esc_html__( 'Docs', 'learndash-ceus' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'learndash-ceus_support_url', 'https://wooninjas.com/open-support-ticket/' ) ) . '" aria-label="' . esc_attr__( 'Visit premium customer support', 'learndash-ceus' ) . '" target="_blank">' . esc_html__( 'Premium support', 'learndash-ceus' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}


	/**
	 * Display the plugin review notice on the plugin settings page.
	 */
	public function plugin_review_notice() {
		if ( ! current_user_can( 'manage_options' ) || ! is_admin() || ! is_plugin_active( plugin_basename( LEARNDASH_CEUS_FILE ) ) ) {
			return;
		}

		$user_id                   = get_current_user_id();
		$review_dismissed_key      = $this->plugin_name . '_review_dismissed_' . $user_id;
		$review_dismissed_action_key = $this->plugin_name . '_dismiss_notice';

		if ( isset( $_GET[ $review_dismissed_action_key ] ) ) {
			set_transient( $review_dismissed_key, 1, MONTH_IN_SECONDS );
		}

		// Show review notice on plugin setting page.
		$is_settings_page = ( isset( $_GET['page'] ) && $_GET['page'] == $this->plugin_name );

		if ( $is_settings_page ) {
			$user_data        = get_userdata( get_current_user_id() );
			$review_dismissed = get_transient( $review_dismissed_key );
			$dismiss_url      = add_query_arg( $review_dismissed_action_key, 1 );

			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			$plugin_data = get_plugin_data( LEARNDASH_CEUS_FILE );

			$message      = __( 'Hey %s, Thank you for using <strong>%s</strong>. If you find our plugin useful please take some time to leave a review <a href="%s" target="_blank">here</a>, it will really help us to grow our business.' );
			$message      = sprintf( $message, esc_html( $user_data->user_nicename ), $plugin_data['Name'], $plugin_data['PluginURI'] );
			$message_html = sprintf(
				__(
					'<div class="notice notice-info wn-review-notice" style="padding-right: 38px; position: relative;"><p>%s</p><button type="button" class="notice-dismiss" onclick="location.href=\'%s\';"><span class="screen-reader-text">%s</span></button></div>',
					'learndash-ceus'
				),
				$message,
				$dismiss_url,
				__( 'Dismiss this notice.', 'learndash-ceus' )
			);

			if ( ! $review_dismissed ) {
				echo $message_html;
			}
		}
	}


	/**
	 * Add branding to footer
	 *
	 * @param $footer_text
	 *
	 * @return mixed
	 */
	function admin_footer_text( $footer_text ) {
		if( isset( $_GET['page'] ) && ( $_GET['page'] == $this->plugin_name ) ) {
			_e('Powered by <a href="http://www.wordpress.org" target="_blank">WordPress</a> | Designed &amp; Developed by <a href="https://wooninjas.com" target="_blank">WooNinjas</a></p>', 'learndash-ceus');
		} else {
			return $footer_text;
		}
	}

	/**
	 * Render the plugin's main settings page.
	 */
	public function plugin_page() {
		// Determine the active tab based on user capabilities
		if ( current_user_can( 'manage_options' ) ) {
			$page_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'license';
		} else {
			$page_tab = isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ? $_GET['tab'] : 'license';
		}
		?>
		<div class="wrap wn_wrap wooninjas_addon">

			<?php
			// Display any settings errors
			settings_errors();
			?>
			<div id="icon-options-general" class="icon32"></div>
			<h1 class="wooninjas_addon_main_heading"><?php echo esc_html__( 'LearnDash CEUs', 'learndash-ceus' ); ?></h1>

			<div class="wooninjas-nav-wrapper">
				<?php
				// Get the sections for the navigation tabs
				$sections = $this->get_sections();
				$url      = admin_url( 'admin.php?page=' . $this->plugin_name );

				foreach ( $sections as $key => $section ) {
					$url = add_query_arg( 'tab', $key, $url );

					if ( isset( $section['action'] ) ) {
						$url = add_query_arg( 'action', $section['action'], $url );
					} else {
						remove_query_arg( 'action', $url );
					}

					if ( isset( $section['id'] ) ) {
						$url = add_query_arg( 'id', $section['id'], $url );
					} else {
						remove_query_arg( 'id', $url );
					}
					?>
					<a href="<?php echo esc_url( $url ); ?>"
					   class="nav-tab <?php echo $page_tab === $key ? 'nav-tab-active' : ''; ?>">
						<i class="dashicons dashicons-<?php echo esc_attr( $section['icon'] ); ?>" aria-hidden="true"></i>
						<?php echo esc_html( $section['title'] ); ?>
					</a>
					<?php
				}
				?>
			</div>

			<?php
			foreach ( $sections as $key => $section ) {
				if ( $page_tab === $key ) {
					include( 'partials/' . $key . '.php' );
				}
			}
			?>

		</div>
		<?php
	}


	/**
	 * Get the sections for the plugin's navigation tabs.
	 *
	 * @return array The sections array.
	 */
	private function get_sections() {

		if ( current_user_can( 'manage_options' ) ) {
			$ld_ceus = array();

			$ld_ceus['license'] = array(
				'title' => __( 'License', 'learndash-ceus' ),
				'icon'  => 'update',
			);

			$ld_ceus['settings'] = array(
				'title' => __( 'General Settings', 'learndash-ceus' ),
				'icon'  => 'admin-settings',
			);

			$ld_ceus['shortcode'] = array(
				'title' => __( 'Shortcode', 'learndash-ceus' ),
				'icon'  => 'admin-settings',
			);
		}

		// Merge the sections arrays
		return $ld_ceus;
	}


	/**
	 * Save the plugin's settings.
	 */
	public function save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Check if we are on the plugin settings page.
		if ( isset( $_REQUEST['page'] ) && 'learndash-ceus' === rtrim( $_REQUEST['page'] ) ) {

			// Check if the active tab is settings.
			if ( isset( $_REQUEST['tab'] ) && 'settings' === rtrim( $_REQUEST['tab'] ) ) {

				if ( ! empty( $_POST ) ) {
					$wn_ld_ceus_nonce = isset( $_POST['learndash_ceus_nonce'] ) ? $_POST['learndash_ceus_nonce'] : -100;

					// Check if the nonce is valid.
					if ( ! wp_verify_nonce( $wn_ld_ceus_nonce, 'learndash_ceus_nonce' ) ) {
						die( 'Process stopped, request could not be verified. Please contact the administrator.' );
					}

					$wn_ld_ceus_settings = get_option( 'wn_ld_ceus_settings', array() );

					$wn_ld_ceus_settings['first_setting_checkbox'] = isset( $_POST['first_setting_checkbox'] ) ? $_POST['first_setting_checkbox'] : '';
					
					// Update learndash ceus settings.
					update_option( 'wn_ld_ceus_settings', $wn_ld_ceus_settings );
				}
			}
		}
	}

	public function add_ld_ceus_metabox() {
	    add_meta_box(
	        'wn_ld_ceus_courses',         				// Unique ID for the metabox
	        'Course CEUs',         						// Title of the metabox
	        [ $this, 'render_ld_ceus_metabox' ],  		// Callback function to render the metabox content
	        'sfwd-courses',                 			// Post type(s) to display the metabox (change 'post' to 'page' for pages)
	        'side',                    					// Metabox position (e.g., 'normal', 'side', 'advanced')
	        'high'                    					// Metabox priority (e.g., 'default', 'high', 'low', 'core')
	    );
	}
	

	public function render_ld_ceus_metabox( $post ) {
		
	}


}
