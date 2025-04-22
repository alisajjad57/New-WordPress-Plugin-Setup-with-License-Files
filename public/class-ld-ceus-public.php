<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wooninjas.com/
 * @since      1.0.0
 *
 * @package    Ld_Ceus
 * @subpackage Ld_Ceus/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ld_Ceus
 * @subpackage Ld_Ceus/public
 * @author     WooNinjas <info@wooninjas.com>
 */
class Ld_Ceus_Public {

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


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'init', [ $this, 'generate_ld_ceus_shortcode'] ); 

	}



	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		// Check if the page has the [wn_ld_ceus] shortcode
		global $post;
		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'wn_ld_ceus' ) ) {
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ld-ceus-public.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-fontawesome', 'https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Red+Hat+Text:wght@300;400;500;600;700&display=swap', array(), $this->version, 'all' );
		}
		wp_enqueue_style( $this->plugin_name . '-notification', plugin_dir_url( __FILE__ ) . 'css/ld-ceus-public-notification.css', array(), $this->version, 'all' );
	}


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $post;
		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'wn_ld_ceus' ) ) {
			wp_enqueue_editor();
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ld-ceus-public.js', array( 'jquery' ), $this->version, false );
			$data = $this->get_localize_data();
			wp_localize_script( $this->plugin_name, 'LDCEUVars', $data );
		}

		wp_enqueue_script( $this->plugin_name . '-notification', plugin_dir_url( __FILE__ ) . 'js/ld-ceus-public-notification.js', array( 'jquery' ), $this->version, false );
		$data = $this->get_localize_data();
		wp_localize_script( $this->plugin_name . '-notification', 'LDCEUVars', $data );
	}


	/**
	 * Get the localized data for JavaScript.
	 *
	 * @return array Localized data.
	 */
	public function get_localize_data() {
		// $wn_ct_settings   = get_option( 'wn_ct_plugin_settings', array() );
		// $upload_media 	  = !empty( $wn_ct_settings['not_upload_media'] ) ? false : true;
		// $publish_comments = !empty( $wn_ct_settings['not_publish_comments'] ) ? false : true;

		$data = array(
			'ajaxurl'      		=> esc_url( admin_url( 'admin-ajax.php' ) ),
			'debug'        		=> defined( 'WP_DEBUG' ) ? true : false,
			'siteURL'     		=> site_url(),
			'_ajax_nonce'		=> wp_create_nonce( 'learndash_ceus' ),
			'err_msg1'    		=> __( 'Invalid format!', 'learndash-ceus' ),
			'err_msg2'   		=> __( 'Error occurred!', 'learndash-ceus' ),
			// 'upload_media' 		=> $upload_media,
			// 'publish_comments'  => $publish_comments,
		);

		return $data;
	}


	/**
	 * Handle the ld_ceus shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Greeting message.
	 */
	public function generate_ld_ceus_shortcode() {
    	add_shortcode( 'wn_ld_ceus', [ $this, 'add_ld_ceus_shortcode_callback' ] );
	}

	/**
	 * Handle the ld_ceus shortcode callback.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Greeting message.
	 */
	public function add_ld_ceus_shortcode_callback() {
		if( is_user_logged_in() ) {
			return $this->template_ld_ceus();
		}
		ob_start();
		?>
		<h2><?php echo esc_html__('Hello World', 'learndash-ceus'); ?></h2>
		<?php
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Render the ld_ceus template.
	 *
	 * @param int $course_id The ID of the course.
	 * @param int $user_id   The ID of the user.
	 *
	 * @return string The rendered content of the template.
	 */
	public function template_ld_ceus() {
		ob_start();

		$template_create_posts = plugin_dir_path( __DIR__ ) . 'public/partials/template-ld-ceus.php';
		
		if ( file_exists( $template_create_posts ) ) {
			// $wn_ld_ceus_settings    = get_option( 'wn_ld_ceus_settings', array() );
			// $required_capability = isset( $wn_ld_ceus_settings['required_capability'] ) ? $wn_ld_ceus_settings['required_capability'] : 'manage_options';
			
			$shortcode_page_id = get_the_ID();
			// update_option('wn_ld_ceus_shortcode_page_id', $shortcode_page_id);

			include $template_create_posts;
		}

		$content = ob_get_clean();
		return $content;
	}



}
