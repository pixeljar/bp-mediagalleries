<?php

if ( file_exists( dirname( __FILE__ ) . '/languages/' . get_locale() . '.mo' ) )
	load_textdomain( 'bp-media-galleries', dirname( __FILE__ ) . '/bp-media-galleries/languages/' . get_locale() . '.mo' );

/* The classes file should hold all database access classes and functions */
require ( dirname( __FILE__ ) . '/models/bp-media-galleries-gallery.php' );
require ( dirname( __FILE__ ) . '/models/bp-media-galleries-media.php' );

/* The ajax file should hold all functions used in AJAX queries */
require ( dirname( __FILE__ ) . '/bp-media-galleries-ajax.php' );

/* The cssjs file should set up and enqueue all CSS and JS files used by the component */
require ( dirname( __FILE__ ) . '/bp-media-galleries-cssjs.php' );

/* The templatetags file should contain classes and functions designed for use in template files */
require ( dirname( __FILE__ ) . '/bp-media-galleries-templatetags.php' );

/* The widgets file should contain code to create and register widgets for the component */
require ( dirname( __FILE__ ) . '/bp-media-galleries-widgets.php' );

/* The notifications file should contain functions to send email notifications on specific user actions */
require ( dirname( __FILE__ ) . '/bp-media-galleries-notifications.php' );

/* The filters file should create and apply filters to component output functions. */
require ( dirname( __FILE__ ) . '/bp-media-galleries-filters.php' );

/**
 * bp_media_galleries_setup_globals()
 *
 * Sets up global variables for your component.
 */
function bp_media_galleries_setup_globals() {
	global $bp, $wpdb;

	/* For internal identification */
	$bp->media_galleries->id = 'media-galleries';

	$bp->media_galleries->gallery_table = $wpdb->base_prefix . 'bp_media_galleries_gallery';
	$bp->media_galleries->media_table = $wpdb->base_prefix . 'bp_media_galleries_media';
	$bp->media_galleries->format_notification_function = 'bp_media_galleries_format_notifications';
	$bp->media_galleries->slug = BP_MEDIA_GALLERIES_SLUG;

	/* Register this in the active components array */
	$bp->active_components[$bp->media_galleries->slug] = $bp->media_galleries->id;
}
add_action( 'wp', 'bp_media_galleries_setup_globals', 2 );
add_action( 'admin_menu', 'bp_media_galleries_setup_globals', 2 );

/**
 * bp_media_galleries_add_admin_menu()
 *
 * This function will add a WordPress wp-admin admin menu for your component under the
 * "BuddyPress" menu.
 */
function bp_media_galleries_add_admin_menu() {
	global $bp;

	if ( !$bp->loggedin_user->is_site_admin )
		return false;

	require ( dirname( __FILE__ ) . '/bp-media-galleries-admin.php' );

	add_submenu_page( 'bp-general-settings', __( 'Media Galleries', 'bp-media-galleries' ), __( 'Media Galleries', 'bp-media-galleries' ), 'manage_options', 'bp-media-galleries-settings', 'bp_media_galleries_admin' );
}
add_action( 'admin_menu', 'bp_media_galleries_add_admin_menu' );

/**
 * bp_media_galleries_setup_nav()
 *
 * Sets up the user profile navigation items for the component. This adds the top level nav
 * item and all the sub level nav items to the navigation array. This is then
 * rendered in the template.
 */
function bp_media_galleries_setup_nav() {
	global $bp;

	/* Add 'Example' to the main user profile navigation */
	bp_core_new_nav_item( array(
		'name' => __( 'Media Galleries', 'bp-media-galleries' ),
		'slug' => $bp->media_galleries->slug,
		'position' => 80,
		'screen_function' => 'bp_media_galleries_view',
		'default_subnav_slug' => 'view'
	) );

	$media_galleries_link = $bp->loggedin_user->domain . $bp->media_galleries->slug . '/';
	
	bp_core_new_subnav_item( array(
		'name' => __( 'Create Gallery', 'bp-media-galleries' ),
		'slug' => 'create',
		'parent_slug' => $bp->media_galleries->slug,
		'parent_url' => $media_galleries_link,
		'screen_function' => 'bp_media_galleries_create',
		'position' => 10,
		'user_has_access' => bp_is_home() // Only the logged in user can access this on his/her profile
	) );
		
	bp_core_new_subnav_item( array(
		'name' => __( 'Upload Audio', 'bp-media-galleries' ),
		'slug' => 'upload-audio',
		'parent_slug' => $bp->media_galleries->slug,
		'parent_url' => $media_galleries_link,
		'screen_function' => 'bp_media_galleries_upload_audio',
		'position' => 20,
		'user_has_access' => bp_is_home() // Only the logged in user can access this on his/her profile
	) );

	bp_core_new_subnav_item( array(
		'name' => __( 'Upload Images', 'bp-media-galleries' ),
		'slug' => 'upload-images',
		'parent_slug' => $bp->media_galleries->slug,
		'parent_url' => $media_galleries_link,
		'screen_function' => 'bp_media_galleries_upload_images',
		'position' => 30,
		'user_has_access' => bp_is_home() // Only the logged in user can access this on his/her profile
	) );
	
	bp_core_new_subnav_item( array(
		'name' => __( 'Upload Videos', 'bp-media-galleries' ),
		'slug' => 'upload-video',
		'parent_slug' => $bp->media_galleries->slug,
		'parent_url' => $media_galleries_link,
		'screen_function' => 'bp_media_galleries_upload_video',
		'position' => 40,
		'user_has_access' => bp_is_home() // Only the logged in user can access this on his/her profile
	) );

	/* Add a nav item for this component under the settings nav item. See bp_example_screen_settings_menu() for more info */
	bp_core_new_subnav_item( array(
		'name' => __( 'Media Galleries', 'bp-media-galleries' ),
		'slug' => 'media-galleries-admin',
		'parent_slug' => $bp->settings->slug,
		'parent_url' => $bp->loggedin_user->domain . $bp->settings->slug . '/',
		'screen_function' => 'bp_media_galleries_screen_settings_menu',
		'position' => 40,
		'user_has_access' => bp_is_my_profile() // Only the logged in user can access this on his/her profile
	) );
}
add_action( 'wp', 'bp_media_galleries_setup_nav', 2 );
add_action( 'admin_menu', 'bp_media_galleries_setup_nav', 2 );

/**
 * bp_media_galleries_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 *
 * This will also allow users to override these templates in their active theme and
 * replace the ones that are stored in the plugin directory.
 *
 * If you're not interested in using template files, then you don't need this function.
 *
 * This will become clearer in the function bp_media_galleries_screen_one() when you want to load
 * a template file.
 */
function bp_media_galleries_load_template_filter( $found_template, $templates ) {
	global $bp;

	/**
	 * Only filter the template location when we're on the media-galleries component pages.
	 */
	if ( $bp->current_component != $bp->media_galleries->slug )
		return $found_template;

	foreach ( (array) $templates as $template ) {
		if ( file_exists( STYLESHEETPATH . '/' . $template ) )
			$filtered_templates[] = STYLESHEETPATH . '/' . $template;
		else
			$filtered_templates[] = dirname( __FILE__ ) . '/views/' . $template;
	}

	$found_template = $filtered_templates[0];

	return apply_filters( 'bp_media_galleries_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_media_galleries_load_template_filter', 10, 2 );


/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * bp_media_galleries_screen_one()
 *
 * Sets up and displays the screen output for the sub nav item "media-galleries/screen-one"
 */
function bp_media_galleries_view() {
	global $bp;

	/**
	 * There are three global variables that you should know about and you will
	 * find yourself using often.
	 *
	 * $bp->current_component (string)
	 * $bp->current_action (string)
	 * $bp->action_variables (array)
	 * 
	 */

	/**
	 * We need to run a check to see if the current user has clicked on the 'send high five' link.
	 * If they have, then let's send the five, and redirect back with a nice error/success message.
	 */
	if ( $bp->current_component == $bp->media_galleries->slug && 'view' == $bp->current_action && 'send-h5' == $bp->action_variables[0] ) {
		/* The logged in user has clicked on the 'send high five' link */
		if ( bp_is_my_profile() ) {
			/* Don't let users high five themselves */
			bp_core_add_message( __( 'No self-fives! :)', 'bp-media-galleries' ), 'error' );
		} else {
			if ( bp_media_galleries_send_highfive( $bp->displayed_user->id, $bp->loggedin_user->id ) )
				bp_core_add_message( __( 'High-five sent!', 'bp-media-galleries' ) );
			else
				bp_core_add_message( __( 'High-five could not be sent.', 'bp-media-galleries' ), 'error' );
		}

		bp_core_redirect( $bp->displayed_user->domain . $bp->media_galleries->slug . '/view' );
	}

	/* Add a do action here, so your component can be extended by others. */
	do_action( 'bp_media_galleries_view' );

	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
	bp_core_load_template( apply_filters( 'bp_media_galleries_template_view', 'media-galleries/view' ) );
}

function bp_media_galleries_create() {
	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
	bp_core_load_template( apply_filters( 'bp_media_galleries_template_create', 'media-galleries/create' ) );
}
function bp_media_galleries_upload_audio() {
	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
	bp_core_load_template( apply_filters( 'bp_media_galleries_template_upload_audio', 'media-galleries/upload-audio' ) );
}
function bp_media_galleries_upload_images() {
	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
	bp_core_load_template( apply_filters( 'bp_media_galleries_template_upload_images', 'media-galleries/upload-images' ) );
}
function bp_media_galleries_upload_video() {
	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
	bp_core_load_template( apply_filters( 'bp_media_galleries_template_upload_video', 'media-galleries/upload-video' ) );
}

function bp_media_galleries_screen_settings_menu() {
	global $bp, $current_user, $bp_settings_updated, $pass_error;

	if ( isset( $_POST['submit'] ) ) {
		/* Check the nonce */
		check_admin_referer('bp-media-galleries-admin');

		$bp_settings_updated = true;

		/**
		 * This is when the user has hit the save button on their settings.
		 * The best place to store these settings is in wp_usermeta.
		 */
		update_usermeta( $bp->loggedin_user->id, 'bp-media-galleries-option-one', esc_attr( $_POST['bp-media-galleries-option-one'] ) );
	}

	add_action( 'bp_template_content_header', 'bp_media_galleries_screen_settings_menu_header' );
	add_action( 'bp_template_title', 'bp_media_galleries_screen_settings_menu_title' );
	add_action( 'bp_template_content', 'bp_media_galleries_screen_settings_menu_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'plugin-template' ) );
}

	function bp_media_galleries_screen_settings_menu_header() {
		_e( 'Media Galleries Settings', 'bp-media-galleries' );
	}

	function bp_media_galleries_screen_settings_menu_title() {
		_e( 'Media Galleries Settings', 'bp-media-galleries' );
	}

	function bp_media_galleries_screen_settings_menu_content() {
		global $bp, $bp_settings_updated; ?>

		<?php if ( $bp_settings_updated ) { ?>
			<div id="message" class="updated fade">
				<p><?php _e( 'Changes Saved.', 'bp-media-galleries' ) ?></p>
			</div>
		<?php } ?>

		<form action="<?php echo $bp->loggedin_user->domain . 'settings/media-galleries-admin'; ?>" name="bp-media-galleries-admin-form" id="account-delete-form" class="bp-media-galleries-admin-form" method="post">

			<input type="checkbox" name="bp-media-galleries-option-one" id="bp-media-galleries-option-one" value="1"<?php if ( '1' == get_usermeta( $bp->loggedin_user->id, 'bp-media-galleries-option-one' ) ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Do you love clicking checkboxes?', 'bp-media-galleries' ); ?>
			<p class="submit">
				<input type="submit" value="<?php _e( 'Save Settings', 'bp-media-galleries' ) ?> &raquo;" id="submit" name="submit" />
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-media-galleries-admin' );
			?>

		</form>
	<?php
	}


/********************************************************************************
 * Activity & Notification Functions
 *
 * These functions handle the recording, deleting and formatting of activity and
 * notifications for the user and for this specific component.
 */


/**
 * bp_media_galleries_screen_notification_settings()
 *
 * Adds notification settings for the component, so that a user can turn off email
 * notifications set on specific component actions.
 */
function bp_media_galleries_screen_notification_settings() {
	global $current_user;

	 /**
	  * Each option is stored in a posted array notifications[SETTING_NAME]
	  * When saved, the SETTING_NAME is stored as usermeta for that user.
	  *
	  * For media-galleries, notifications[notification_friends_friendship_accepted] could be
	  * used like this:
	  *
	  * if ( 'no' == get_usermeta( $bp['loggedin_userid], 'notification_friends_friendship_accepted' ) )
	  *		// don't send the email notification
	  *	else
	  *		// send the email notification.
      */

	?>
	<table class="notification-settings" id="bp-media-galleries-notification-settings">
		<tr>
			<th class="icon"></th>
			<th class="title"><?php _e( 'Media Galleries', 'bp-media-galleries' ) ?></th>
			<th class="yes"><?php _e( 'Yes', 'bp-media-galleries' ) ?></th>
			<th class="no"><?php _e( 'No', 'bp-media-galleries' )?></th>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'Action One', 'bp-media-galleries' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_media-galleries_action_one]" value="yes" <?php if ( !get_usermeta( $current_user->id,'notification_media-galleries_action_one') || 'yes' == get_usermeta( $current_user->id,'notification_media-galleries_action_one') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_media-galleries_action_one]" value="no" <?php if ( get_usermeta( $current_user->id,'notification_media-galleries_action_one') == 'no' ) { ?>checked="checked" <?php } ?>/></td>
		</tr>
		<tr>
			<td></td>
			<td><?php _e( 'Action Two', 'bp-media-galleries' ) ?></td>
			<td class="yes"><input type="radio" name="notifications[notification_media-galleries_action_two]" value="yes" <?php if ( !get_usermeta( $current_user->id,'notification_media-galleries_action_two') || 'yes' == get_usermeta( $current_user->id,'notification_media-galleries_action_two') ) { ?>checked="checked" <?php } ?>/></td>
			<td class="no"><input type="radio" name="notifications[notification_media-galleries_action_two]" value="no" <?php if ( 'no' == get_usermeta( $current_user->id,'notification_media-galleries_action_two') ) { ?>checked="checked" <?php } ?>/></td>
		</tr>

		<?php do_action( 'bp_media_galleries_notification_settings' ); ?>
	</table>
<?php
}
add_action( 'bp_notification_settings', 'bp_media_galleries_screen_notification_settings' );

/**
 * bp_media_galleries_record_activity()
 *
 * If the activity stream component is installed, this function will record activity items for your
 * component.
 *
 * You must pass the function an associated array of arguments:
 *
 *     $args = array(
 *	 	 REQUIRED PARAMS
 *		 'action' => For media-galleries: "Andy high-fived John", "Andy posted a new update".
 *       'type' => The type of action being carried out, for media-galleries 'new_friendship', 'joined_group'. This should be unique within your component.
 *
 *		 OPTIONAL PARAMS
 *		 'id' => The ID of an existing activity item that you want to update.
 * 		 'content' => The content of your activity, if it has any, for media-galleries a photo, update content or blog post excerpt.
 *       'component' => The slug of the component.
 *		 'primary_link' => The link for the title of the item when appearing in RSS feeds (defaults to the activity permalink)
 *       'item_id' => The ID of the main piece of data being recorded, for media-galleries a group_id, user_id, forum_post_id - useful for filtering and deleting later on.
 *		 'user_id' => The ID of the user that this activity is being recorded for. Pass false if it's not for a user.
 *		 'recorded_time' => (optional) The time you want to set as when the activity was carried out (defaults to now)
 *		 'hide_sitewide' => Should this activity item appear on the site wide stream?
 *		 'secondary_item_id' => (optional) If the activity is more complex you may need a second ID. For media-galleries a group forum post may need the group_id AND the forum_post_id.
 *     )
 *
 * Example usage would be:
 *
 *   bp_media_galleries_record_activity( array( 'type' => 'new_gallery', 'action' => 'Andy high-fived John', 'user_id' => $bp->loggedin_user->id, 'item_id' => $bp->displayed_user->id ) );
 *
 */
function bp_media_galleries_record_activity( $args = '' ) {
	global $bp;

	if ( !function_exists( 'bp_activity_add' ) )
		return false;

	$defaults = array(
		'id' => false,
		'user_id' => $bp->loggedin_user->id,
		'action' => '',
		'content' => '',
		'primary_link' => '',
		'component' => $bp->media_galleries->id,
		'type' => false,
		'item_id' => false,
		'secondary_item_id' => false,
		'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		'hide_sitewide' => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r );

	return bp_activity_add( array( 'id' => $id, 'user_id' => $user_id, 'action' => $action, 'content' => $content, 'primary_link' => $primary_link, 'component' => $component, 'type' => $type, 'item_id' => $item_id, 'secondary_item_id' => $secondary_item_id, 'recorded_time' => $recorded_time, 'hide_sitewide' => $hide_sitewide ) );
}

/**
 * bp_media_galleries_format_notifications()
 *
 * The format notification function will take DB entries for notifications and format them
 * so that they can be displayed and read on the screen.
 *
 * Notifications are "screen" notifications, that is, they appear on the notifications menu
 * in the site wide navigation bar. They are not for email notifications.
 *
 *
 * The recording is done by using bp_core_add_notification() which you can search for in this file for
 * media-galleriess of usage.
 */
function bp_media_galleries_format_notifications( $action, $item_id, $secondary_item_id, $total_items ) {
	global $bp;

	switch ( $action ) {
		case 'new_high_five':
			/* In this case, $item_id is the user ID of the user who sent the high five. */

			/***
			 * We don't want a whole list of similar notifications in a users list, so we group them.
			 * If the user has more than one action from the same component, they are counted and the
			 * notification is rendered differently.
			 */
			if ( (int)$total_items > 1 ) {
				return apply_filters( 'bp_media_galleries_multiple_new_high_five_notification', '<a href="' . $bp->loggedin_user->domain . $bp->media_galleries->slug . '/screen-one/" title="' . __( 'Multiple high-fives', 'bp-media-galleries' ) . '">' . sprintf( __( '%d new high-fives, multi-five!', 'bp-media-galleries' ), (int)$total_items ) . '</a>', $total_items );
			} else {
				$user_fullname = bp_core_get_user_displayname( $item_id, false );
				$user_url = bp_core_get_userurl( $item_id );
				return apply_filters( 'bp_media_galleries_single_new_high_five_notification', '<a href="' . $user_url . '?new" title="' . $user_fullname .'\'s profile">' . sprintf( __( '%s sent you a high-five!', 'bp-media-galleries' ), $user_fullname ) . '</a>', $user_fullname );
			}
		break;
	}

	do_action( 'bp_media_galleries_format_notifications', $action, $item_id, $secondary_item_id, $total_items );

	return false;
}


/***
 * From now on you will want to add your own functions that are specific to the component you are developing.
 * For media-galleries, in this section in the friends component, there would be functions like:
 *    friends_add_friend()
 *    friends_remove_friend()
 *    friends_check_friendship()
 *
 * Some guidelines:
 *    - Don't set up error messages in these functions, just return false if you hit a problem and
 *		deal with error messages in screen or action functions.
 *
 *    - Don't directly query the database in any of these functions. Use database access classes
 * 		or functions in your bp-media-galleries-classes.php file to fetch what you need. Spraying database
 * 		access all over your plugin turns into a maintainence nightmare, trust me.
 *
 *	  - Try to include add_action() functions within all of these functions. That way others will find it
 *		easy to extend your component without hacking it to pieces.
 */

/**
 * bp_media_galleries_accept_terms()
 *
 * Accepts the terms and conditions screen for the logged in user.
 * Records an activity stream item for the user.
 */
function bp_media_galleries_accept_terms() {
	global $bp;

	/**
	 * First check the nonce to make sure that the user has initiated this
	 * action. Remember the wp_nonce_url() call? The second parameter is what
	 * you need to check for.
	 */
	check_admin_referer( 'bp_media_galleries_accept_terms' );

	/***
	 * Here is a good media-galleries of where we can post something to a users activity stream.
	 * The user has excepted the terms on screen two, and now we want to post
	 * "Andy accepted the really exciting terms and conditions!" to the stream.
	 */
	$user_link = bp_core_get_userlink( $bp->loggedin_user->id );

	bp_media_galleries_record_activity( array(
		'type' => 'accepted_terms',
		'action' => apply_filters( 'bp_media_galleries_accepted_terms_activity_action', sprintf( __( '%s accepted the really exciting terms and conditions!', 'bp-media-galleries' ), $user_link ), $user_link ),
	) );

	/* See bp_media_galleries_reject_terms() for an explanation of deleting activity items */
	if ( function_exists( 'bp_activity_delete') )
		bp_activity_delete( array( 'type' => 'rejected_terms', 'user_id' => $bp->loggedin_user->id ) );

	/* Add a do_action here so other plugins can hook in */
	do_action( 'bp_media_galleries_accept_terms', $bp->loggedin_user->id );

	/***
	 * You'd want to do something here, like set a flag in the database, or set usermeta.
	 * just for the sake of the demo we're going to return true.
	 */

	return true;
}

/**
 * bp_media_galleries_reject_terms()
 *
 * Rejects the terms and conditions screen for the logged in user.
 * Records an activity stream item for the user.
 */
function bp_media_galleries_reject_terms() {
	global $bp;

	check_admin_referer( 'bp_media_galleries_reject_terms' );

	/***
	 * In this media-galleries component, the user can reject the terms even after they have
	 * previously accepted them.
	 *
	 * If a user has accepted the terms previously, then this will be in their activity
	 * stream. We don't want both 'accepted' and 'rejected' in the activity stream, so
	 * we should remove references to the user accepting from all activity streams.
	 * A real world media-galleries of this would be a user deleting a published blog post.
	 */

	$user_link = bp_core_get_userlink( $bp->loggedin_user->id );

	/* Now record the new 'rejected' activity item */
	bp_media_galleries_record_activity( array(
		'type' => 'rejected_terms',
		'action' => apply_filters( 'bp_media_galleries_rejected_terms_activity_action', sprintf( __( '%s rejected the really exciting terms and conditions.', 'bp-media-galleries' ), $user_link ), $user_link ),
	) );

	/* Delete any accepted_terms activity items for the user */
	if ( function_exists( 'bp_activity_delete') )
		bp_activity_delete( array( 'type' => 'accepted_terms', 'user_id' => $bp->loggedin_user->id ) );

	do_action( 'bp_media_galleries_reject_terms', $bp->loggedin_user->id );

	return true;
}

/**
 * bp_media_galleries_send_high_five()
 *
 * Sends a high five message to a user. Registers an notification to the user
 * via their notifications menu, as well as sends an email to the user.
 *
 * Also records an activity stream item saying "User 1 high-fived User 2".
 */
function bp_media_galleries_send_highfive( $to_user_id, $from_user_id ) {
	global $bp;

	check_admin_referer( 'bp_media_galleries_send_high_five' );

	/**
	 * We'll store high-fives as usermeta, so we don't actually need
	 * to do any database querying. If we did, and we were storing them
	 * in a custom DB table, we'd want to reference a function in
	 * bp-media-galleries-classes.php that would run the SQL query.
	 */

	/* Get existing fives */
	$existing_fives = maybe_unserialize( get_usermeta( $to_user_id, 'high-fives' ) );

	/* Check to see if the user has already high-fived. That's okay, but lets not
	 * store duplicate high-fives in the database. What's the point, right?
	 */
	if ( !in_array( $from_user_id, (array)$existing_fives ) ) {
		$existing_fives[] = (int)$from_user_id;

		/* Now wrap it up and fire it back to the database overlords. */
		update_usermeta( $to_user_id, 'high-fives', serialize( $existing_fives ) );
	}

	/***
	 * Now we've registered the new high-five, lets work on some notification and activity
	 * stream magic.
	 */

	/***
	 * Post a screen notification to the user's notifications menu.
	 * Remember, like activity streams we need to tell the activity stream component how to format
	 * this notification in bp_media_galleries_format_notifications() using the 'new_high_five' action.
	 */
	bp_core_add_notification( $from_user_id, $to_user_id, $bp->media_galleries->slug, 'new_high_five' );

	/* Now record the new 'new_high_five' activity item */
	$to_user_link = bp_core_get_userlink( $to_user_id );
	$from_user_link = bp_core_get_userlink( $from_user_id );

	bp_media_galleries_record_activity( array(
		'type' => 'rejected_terms',
		'action' => apply_filters( 'bp_media_galleries_new_high_five_activity_action', sprintf( __( '%s high-fived %s!', 'bp-media-galleries' ), $from_user_link, $to_user_link ), $from_user_link, $to_user_link ),
		'item_id' => $to_user_id,
	) );

	/* We'll use this do_action call to send the email notification. See bp-media-galleries-notifications.php */
	do_action( 'bp_media_galleries_send_high_five', $to_user_id, $from_user_id );

	return true;
}

/**
 * bp_media_galleries_get_highfives_for_user()
 *
 * Returns an array of user ID's for users who have high fived the user passed to the function.
 */
function bp_media_galleries_get_highfives_for_user( $user_id ) {
	global $bp;

	if ( !$user_id )
		return false;

	return maybe_unserialize( get_usermeta( $user_id, 'high-fives' ) );
}

/**
 * bp_media_galleries_remove_screen_notifications()
 *
 * Remove a screen notification for a user.
 */
function bp_media_galleries_remove_screen_notifications() {
	global $bp;

	/**
	 * When clicking on a screen notification, we need to remove it from the menu.
	 * The following command will do so.
 	 */
	bp_core_delete_notifications_for_user_by_type( $bp->loggedin_user->id, $bp->media_galleries->slug, 'new_high_five' );
}
add_action( 'bp_media_galleries_screen_one', 'bp_media_galleries_remove_screen_notifications' );
add_action( 'xprofile_screen_display_profile', 'bp_media_galleries_remove_screen_notifications' );

/**
 * bp_media_galleries_remove_data()
 *
 * It's always wise to clean up after a user is deleted. This stops the database from filling up with
 * redundant information.
 */
function bp_media_galleries_remove_data( $user_id ) {
	/* You'll want to run a function here that will delete all information from any component tables
	   for this $user_id */
	
	BP_Media_Galleries_Gallery::delete_by_user_id( $user_id );
	BP_Media_Galleries_Media::delete_by_user_id( $user_id );

	/* Remember to remove usermeta for this component for the user being deleted */
	delete_usermeta( $user_id, 'bp_media_galleries_some_setting' );

	do_action( 'bp_media_galleries_remove_data', $user_id );
}
add_action( 'wpmu_delete_user', 'bp_media_galleries_remove_data', 1 );
add_action( 'delete_user', 'bp_media_galleries_remove_data', 1 );

/***
 * Object Caching Support ----
 *
 * It's a good idea to implement object caching support in your component if it is fairly database
 * intensive. This is not a requirement, but it will help ensure your component works better under
 * high load environments.
 *
 * In parts of this media-galleries component you will see calls to wp_cache_get() often in template tags
 * or custom loops where database access is common. This is where cached data is being fetched instead
 * of querying the database.
 *
 * However, you will need to make sure the cache is cleared and updated when something changes. For media-galleries,
 * the groups component caches groups details (such as description, name, news, number of members etc).
 * But when those details are updated by a group admin, we need to clear the group's cache so the new
 * details are shown when users view the group or find it in search results.
 *
 * We know that there is a do_action() call when the group details are updated called 'groups_settings_updated'
 * and the group_id is passed in that action. We need to create a function that will clear the cache for the
 * group, and then add an action that calls that function when the 'groups_settings_updated' is fired.
 *
 * Example:
 *
 *   function groups_clear_group_object_cache( $group_id ) {
 *	     wp_cache_delete( 'groups_group_' . $group_id );
 *	 }
 *	 add_action( 'groups_settings_updated', 'groups_clear_group_object_cache' );
 *
 * The "'groups_group_' . $group_id" part refers to the unique identifier you gave the cached object in the
 * wp_cache_set() call in your code.
 *
 * If this has completely confused you, check the function documentation here:
 * http://codex.wordpress.org/Function_Reference/WP_Cache
 *
 * If you're still confused, check how it works in other BuddyPress components, or just don't use it,
 * but you should try to if you can (it makes a big difference). :)
 */