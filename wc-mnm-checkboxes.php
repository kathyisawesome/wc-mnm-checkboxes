<?php
/**
 * Plugin Name: WooCommerce Mix and Match -  Checkboxes
 * Plugin URI: http://www.woocommerce.com/products/woocommerce-mix-and-match-products/
 * Description: Convert quantity inputs to checkboxes. Requires Mix and Match 1.4.1+
 * Version: 1.1.0
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 * Developer: Kathy Darling, Manos Psychogyiopoulos
 * Developer URI: http://kathyisawesome.com/
 * Text Domain: wc-mnm-checkboxes
 * Domain Path: /languages
 *
 * Copyright: © 2018 Kathy Darling
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */


/**
 * The Main WC_MNM_Checkboxes class
 **/
if ( ! class_exists( 'WC_MNM_Checkboxes' ) ) :

class WC_MNM_Checkboxes {

	/**
	 * WC_MNM_Checkboxes Constructor
	 *
	 * @access 	public
     * @return 	WC_MNM_Checkboxes
	 */
	public static function init() {

		// Load translation files.
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

		// Add extra meta.
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'additional_container_option') , 15, 2 );
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'process_meta' ), 20 );

		// Switch the quantity input.
		add_action( 'woocommerce_before_mnm_items', array( __CLASS__, 'maybe_change_template' ) );
		add_action( 'woocommerce_after_mnm_items', array( __CLASS__, 'remove_plugin_template' ) );

    }


	/*-----------------------------------------------------------------------------------*/
	/* Localization */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Make the plugin translation ready
	 *
	 * @return void
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'wc-mnm-checkboxes' , false , dirname( plugin_basename( __FILE__ ) ) .  '/languages/' );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Admin */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Adds the container max weight option writepanel options.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 */
	public static function additional_container_option( $post_id, $mnm_product_object ) {
		woocommerce_wp_checkbox( array(
			'id'            => '_mnm_checkboxes',
			'label'       => __( 'Convert options to single checkboxes', 'wc-mnm-checkboxes' )
		) );
	}

	/**
	 * Saves the new meta field.
	 *
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 */
	public static function process_meta( $product ) {
		if( isset( $_POST[ '_mnm_checkboxes' ] ) ) {
			$product->update_meta_data( '_mnm_checkboxes', 'yes' );
		} else {
			$product->update_meta_data( '_mnm_checkboxes', 'no' );
		}
	}


	/*-----------------------------------------------------------------------------------*/
	/* Front End Display */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Maybe use the plugin's template version
	 */
	public static function maybe_change_template() {
		global $product;
		if( 'yes' == $product->get_meta( '_mnm_checkboxes', true, 'edit' ) ) {
			add_filter( 'woocommerce_locate_template', array( __CLASS__, 'plugin_template' ), 10, 3 );
		}
	}

	/**
	 * Remove the plugin's template version
	 */
	public static function remove_plugin_template() {
		remove_filter( 'woocommerce_locate_template', array( __CLASS__, 'plugin_template' ), 10, 3 );
	}	
	
	/**
	 * Use the plugin's template version
	 *
	 * @param string $template_name Template name.
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path  Default path. (default: '').
	 * @return string
	 */
	public static function plugin_template( $template, $template_name, $template_path ){
		if( 'single-product/mnm/mnm-product-quantity.php' == $template_name ) {
			$new_template = plugin_dir_path( __FILE__ ) . 'templates/' . $template_name;
			$template = file_exists( $new_template ) ? $new_template : $template;
		}
		
		return $template;
	}

} //end class: do not remove or there will be no more guacamole for you

endif; // end class_exists check

// Launch the whole plugin.
add_action( 'woocommerce_mnm_loaded', array( 'WC_MNM_Checkboxes', 'init' ) );
