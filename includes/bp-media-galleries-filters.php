<?php

 /**
  * Some WP filters you may want to use:
  *  - wp_filter_kses() VERY IMPORTANT see below.
  *  - wptexturize()
  *  - convert_smilies()
  *  - convert_chars()
  *  - wpautop()
  *  - stripslashes_deep()
  *  - make_clickable()
  */

/**
 * --- NOTE ----
 * It's very very important that you use the wp_filter_kses() function to filter all
 * input AND output in your plugin. This will stop users adding malicious scripts and other
 * bad things onto any page.
 */

/**
 * In all your template tags that output data, you should have an apply_filters() call, you can
 * then use those filters to automatically add the wp_filter_kses() call.
 * The third parameter "1" adds the highest priority to the filter call.
 */
 
 add_filter( 'bp_example_get_item_name', 'wp_filter_kses', 1 );

/**
 * FILTER DATA BEFORE DATABASE WRITES
 */
 add_filter( 'bp_media_galleries_data_gallery_name_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_gallery_description_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_privacy_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_slug_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_gallery_id_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_media_type_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_media_name_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_cover_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_filename_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_thumbname_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_width_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_height_before_save', 'wp_filter_kses', 1 );
 add_filter( 'bp_media_galleries_data_media_description_before_save', 'wp_filter_kses', 1 );
?>