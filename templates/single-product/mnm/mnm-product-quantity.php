<?php
/**
 * Mix and Match Product Quantity
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/mnm-product-quantity.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match/Templates
 * @since   1.0.0
 * @version 1.3.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}

global $product;

$mnm_id = $mnm_item->get_id();

if ( $mnm_item->is_purchasable() && $mnm_item->is_in_stock() ) {

	$quantity = isset( $_REQUEST[ 'mnm_quantity' ] ) && isset( $_REQUEST[ 'mnm_quantity' ][ $mnm_id ] ) ? 1 : 0;

	printf( '<input type="checkbox" class="qty mnm-quantity" name="mnm_quantity[%s]" value="1" %s min="1" max="1" />',
	    $mnm_id,
	    checked( $quantity, 1, false )
	);

} else {
	/**
	 * Bundled child item availability message.
	 *
	 * @param str $availability
	 * @param obj WC_Product
	 */
	echo apply_filters( 'woocommerce_mnm_availability_html', $product->get_child_availability_html( $mnm_id ), $mnm_item );
}
