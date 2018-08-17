;( function ( $, window, document, undefined ) {

    $( '.mnm_form' ).on( 'wc-mnm-initialized', function( event, container ) {

        // Unbind MNM triggers.
        container.$mnm_reset.off();

        // Reset link.
		container.$mnm_reset.on( 'click', function( event ) { console.log('wtf');
			
			event.preventDefault();

			// Loop through quantity inputs.
			container.$mnm_items.each( function() {
                $(this).find( '.qty' ).prop( 'checked', false );
			} );

			// Manually trigger the update method.
			container.update();
		} );

     } );
     
    // Validate quantities based on checkbox status.
    $( '.mnm_form' ).on( 'wc-mnm-validation', function( event, container, total_qty ) { 

		var min_container_size  = container.get_min_container_size();
		var max_container_size  = container.get_max_container_size();

		var per_item_pricing    = container.$mnm_cart.data( 'per_product_pricing' );
		var total_qty           = 0;
		var total_price         = parseFloat( container.$mnm_cart.data( 'base_price' ) );
		var total_regular_price = parseFloat( container.$mnm_cart.data( 'base_regular_price' ) );
		var formatted_price     = '';
		var error_message 		= '';

		// Reset status/error messages state.
		container.reset_messages();

		// Add up quantities + prices.
		container.$mnm_items.each( function() {

			var quantity = 0;

			$input = $(this).find( '.qty' );

			if ( $input.length > 0 ) {

				// Calculate total container quantity.
				quantity   = $input.is(':checked') ? parseInt( $input.val() ) : 0;
				total_qty += quantity;

				if ( per_item_pricing == true ) {
					total_price         += parseFloat( $(this).data( 'price' ) ) * quantity;
					total_regular_price += parseFloat( $(this).data( 'regular_price' ) ) * quantity;
				}
			}
		
		} );
		
		// Reproduce Validation: needed until MNM is refactored.
		if( min_container_size === max_container_size && total_qty !== min_container_size ){
			error_message = total_qty == 1 ? wc_mnm_params.i18n_qty_error_single : wc_mnm_params.i18n_qty_error;
			error_message = error_message.replace( '%s', min_container_size );
		}
		// Validate a range.
    	else if( max_container_size > 0 && min_container_size > 0 && ( total_qty < min_container_size || total_qty > max_container_size ) ){
			error_message = wc_mnm_params.i18n_min_max_qty_error.replace( '%max', max_container_size ).replace( '%min', min_container_size );
		}
		// Validate that a container has minimum number of items.
		else if( min_container_size > 0 && total_qty < min_container_size ){
			error_message = min_container_size > 1 ? wc_mnm_params.i18n_min_qty_error : wc_mnm_params.i18n_min_qty_error_singular;
			error_message = error_message.replace( '%min', min_container_size );
		// Validate that a container has fewer than the maximum number of items.
		} else if ( max_container_size > 0 && total_qty > max_container_size ){
			error_message = max_container_size > 1 ? wc_mnm_params.i18n_max_qty_error : wc_mnm_params.i18n_max_qty_error_singular;
			error_message = error_message.replace( '%max', max_container_size );
		}

		// Add error message.
		if ( error_message != '' ) {
			// "Selected X total".
			var selected_qty_message = container.selected_quantity_message( total_qty );

			// Add error message, replacing placeholders with current values.
			container.add_message( error_message.replace( '%v', selected_qty_message ), 'error' );
		}
    } );

} ) ( jQuery, window, document );