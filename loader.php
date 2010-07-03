<?php
/*
Plugin Name: BuddyPress Media Galleries
Plugin URI: http://http://github.com/pixeljar/bp-mediagalleries
Description: This component enables users to upload Audio, Images and Video and create albums to share with other community users
Version: 1.0
Revision Date: 07 03, 2010
Requires at least: WordPress 3.0, BuddyPress 1.2.5
Tested up to: WordPress 3.0, BuddyPress 1.2.5
License: GNU General Public License 2.0 (GPL) http://www.gnu.org/licenses/gpl.html
Author: Brandon Dove
Author URI: http://pixeljar.net
Site Wide Only: true
*/

define ( 'BP_MEDIA_GALLERIES_IS_INSTALLED', 1 );
define ( 'BP_MEDIA_GALLERIES_VERSION', '1.0' );
define ( 'BP_MEDIA_GALLERIES_DB_VERSION', '1.0' );
if ( !defined( 'BP_MEDIA_GALLERIES_SLUG' ) )
	define ( 'BP_MEDIA_GALLERIES_SLUG', 'media-galleries' );

/* Only load the component if BuddyPress is loaded and initialized. */
function bp_media_galleries_init() {
	require( dirname( __FILE__ ) . '/includes/bp-media-galleries-core.php' );
}
add_action( 'bp_init', 'bp_media_galleries_init' );

/*
	CREATE TABLES
	The following function creates the necessary tables to 
	hold the media gallery data
/**/
function bp_media_galleries_activate() {
	global $wpdb, $bp;

	if ( !empty($wpdb->charset) )
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";

	/* Main Gallery Table /**/
	$sql[] = "
		CREATE TABLE `{$bp->media_galleries->gallery_table}` (
			`id` int(11) NOT NULL auto_increment,
			`user_id` int(11) NOT NULL,
			`gallery_name` varchar(255) NOT NULL,
			`gallery_description` text NOT NULL,
			`user_access` tinyint(1) NOT NULL default '2',
			`slug` varchar(255) NOT NULL,
			PRIMARY KEY  (`id`)
		) {$charset_collate};
	";
	/* Gallery Media Table /**/
	$sql[] = "
		CREATE TABLE `{$bp->media_galleries->media_table}` (
			`id` int(11) NOT NULL auto_increment,
			`gallery_id` int(11) NOT NULL,
			`media_type` set('image','video') NOT NULL,
			`media_name` varchar(255) NOT NULL,
			`cover` tinyint(1) NOT NULL default '0',
			`filename` varchar(255) NOT NULL,
			`thumbname` varchar(255) NOT NULL,
			`width` int(6) NOT NULL,
			`height` int(6) NOT NULL,
			`media_description` varchar(255) NOT NULL,
			PRIMARY KEY  (`id`)
		) {$charset_collate};
	";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	update_site_option( 'bp-media-galleries-db-version', BP_MEDIA_GALLERIES_DB_VERSION );
}
register_activation_hook( __FILE__, 'bp_media_galleries_activate' );

/* On deacativation, clean up anything your component has added. */
function bp_media_galleries_deactivate() {
	/* You might want to delete any options or tables that your component created. */
	delete_site_option( 'bp-media-galleries-db-version' );
}
register_deactivation_hook( __FILE__, 'bp_media_galleries_deactivate' );