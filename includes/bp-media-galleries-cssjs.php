<?php

/**
 * bp_media_galleries_add_js()
 *
 * This function will enqueue the components javascript file, so that you can make
 * use of any javascript you bundle with your component within your interface screens.
 */
function bp_media_galleries_add_js() {
	global $bp;

	if ( $bp->current_component == $bp->media_galleries->slug )
		wp_enqueue_script( 'bp-media-galleries-js', plugins_url( '/bp-media-galleries/includes/js/general.js' ) );
}
add_action( 'template_redirect', 'bp_media_galleries_add_js', 1 );