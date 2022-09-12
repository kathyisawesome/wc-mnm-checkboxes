<?php
/**
 * DEPRECATED: Mix and Match Product Quantity - Checkbox display handled in core.
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
 * @version 1.2.1
 * @deprecated 2.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}

global $product;

$mnm_id = $mnm_item->get_id();

if ( $mnm_item->is_purchasable() && $mnm_item->is_in_stock() ) {

	$input_name = wc_mnm_get_child_input_name( $product->get_id() );
	$input_id = $product->get_id() . '-' . $input_name . '-' . $mnm_item->get_id();

	$min_quantity = $product->get_child_quantity( 'min', $mnm_id );
	$max_quantity = $product->get_child_quantity( 'max', $mnm_id );

	if ( $min_quantity === $max_quantity ) {
		$checkbox_label = sprintf( __( '%1$d <span class="screen-reader-text">%2$s</span> required', 'wc-mnm-checkboxes' ),
			$max_quantity,
			$mnm_item->get_title()
		);
		$is_checked = true;
		$input_type = 'hidden';
	} else {
		$checkbox_label = sprintf( __( 'Add %1$d <span class="screen-reader-text">%2$s</span>', 'wc-mnm-checkboxes' ),
			$max_quantity,
			$mnm_item->get_title()
		);
		$checked_quantity = isset( $_REQUEST[ $input_name ] ) && ! empty ( $_REQUEST[ $input_name ][ $child_id ] ) ? intval( $_REQUEST[ $input_name ][ $child_id ] ) : apply_filters( 'woocommerce_mnm_quantity_input', 0, $mnm_item, $product );

		$is_checked = $checked_quantity === $max_quantity;
		$input_type = 'checkbox';
	}

	printf( '<input id="%s" type="%s" class="mnm-quantity mnm-checkbox qty" name="%s[%s]" value="%s" %s/>',
		esc_attr( $input_id ),
		esc_attr( $input_type ),
	    esc_attr( $input_name ),
	    esc_attr( $mnm_id ),
	    esc_attr( $max_quantity ),
	    checked( $is_checked, true, false )
	);

	printf( '<label for="%s">%s</label>',
		esc_attr( $input_id ),
		wp_kses_post( $checkbox_label )
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
