<?php
/**
 * Plugin Name: WooCommerce Mix and Match -  Checkboxes
 * Plugin URI: http://www.woocommerce.com/products/woocommerce-mix-and-match-products/
 * Description: Convert quantity inputs to checkboxes. Requires Mix and Match 1.4.1+
 * Version: 2.0.0
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
    
    const REQ_MNM_VERSION = '2.0.0';

	/**
	 * Pseudo "Constructor"
	 */
	public static function init() {

		// Quietly quit if Mix and Match is not active.
		if ( ! function_exists( 'wc_mix_and_match' ) || version_compare( wc_mix_and_match()->version, self::REQ_MNM_VERSION ) < 0 ) {
			return false;
		}

		// Load translation files.
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

		// Add extra meta.
		add_action( 'wc_mnm_admin_product_options', array( __CLASS__, 'additional_container_option') , 15, 2 );
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'process_meta' ), 20 );

		// Add max child quantity to 1 early so it can be modified by other filters.
		add_action( 'wc_mnm_child_item_quantity_input_max', array( __CLASS__, 'apply_max_limit' ), 0, 2 );

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
		if ( isset( $_POST[ '_mnm_checkboxes' ] ) ) {
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
	 * 
	 * @deprecated 2.0.0
	 */
	public static function maybe_change_template() {
	    wc_deprecated_function( 'WC_MNM_Checkboxes::maybe_change_template()', '2.0.0', 'Function is no longer used. Core Mix and Match auto displays checkboxes where appropriate.' );
		global $product;
		if ( 'yes' == $product->get_meta( '_mnm_checkboxes', true, 'edit' ) ) {
			add_filter( 'woocommerce_locate_template', array( __CLASS__, 'plugin_template' ), 10, 3 );
		}
	}

	/**
	 * Remove the plugin's template version
	 * 
	 * @deprecated 2.0.0
	 */
	public static function remove_plugin_template() {
	    wc_deprecated_function( 'WC_MNM_Checkboxes::remove_plugin_template()', '2.0.0', 'Function is no longer used. Core Mix and Match auto displays checkboxes where appropriate.' );
		remove_filter( 'woocommerce_locate_template', array( __CLASS__, 'plugin_template' ), 10, 3 );
	}	
	
	/**
	 * Use the plugin's template version
	 * 
	 * @deprecated 2.0.0
	 *
	 * @param string $template_name Template name.
	 * @param string $template_path Template path. (default: '').
	 * @param string $default_path  Default path. (default: '').
	 * @return string
	 */
	public static function plugin_template( $template, $template_name, $template_path ) {
	    wc_deprecated_function( 'WC_MNM_Checkboxes::plugin_template()', '2.0.0', 'Function is no longer used. Core Mix and Match auto displays checkboxes where appropriate.' );
		if ( 'single-product/mnm/mnm-product-quantity.php' == $template_name ) {
			$new_template = plugin_dir_path( __FILE__ ) . 'templates/' . $template_name;
			$template = file_exists( $new_template ) ? $new_template : $template;
		}
		
		return $template;
	}


	/**
	 * Add a tiny style.
	 * 
	 * @deprecated 2.0.0
	 */
	public static function add_styles() {
	    wc_deprecated_function( 'WC_MNM_Checkboxes::add_styles()', '2.0.0', 'Function is no longer used. Core Mix and Match handles styles checkboxes automatically' );
	    ?>
		<style>
			.single-product .mnm_form .mnm-checkbox { width: initial; }
			.theme-twentytwentyone .mnm_form .mnm-checkbox { width: 25px; height: 25px; }
		</style>
		<?php
	}


	/*-----------------------------------------------------------------------------------*/
	/* Cart validation                                                                   */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Limit the max to 1 early so it can be overriden.
	 *
	 * @param  int $qty Quantity.
	 * @param int $value - The min/max quantity input value.
	 * @param WC_MNM_Child_Item $child_item the child item object.
	 * @return string
	 */
	public static function apply_max_limit( $qty, $child_item ) {
	    $container_product = $child_item->get_container();
		if ( $container_product && 'yes' == $container_product->get_meta( '_mnm_checkboxes', true, 'edit' ) ) {
			$qty = 1;
		}
		return $qty;
	}

} //end class: do not remove or there will be no more guacamole for you

endif; // end class_exists check

// Launch the whole plugin.
add_action( 'plugins_loaded', array( 'WC_MNM_Checkboxes', 'init' ), 20 );
