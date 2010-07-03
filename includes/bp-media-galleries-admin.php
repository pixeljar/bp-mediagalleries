<?php

/**
 * bp_media_galleries_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 */
function bp_media_galleries_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('bp-media-galleries-settings') ) {
		update_option( 'media-galleries-audio', $_POST['media-galleries-audio'] );
		update_option( 'media-galleries-images', $_POST['media-galleries-images'] );
		update_option( 'media-galleries-video', $_POST['media-galleries-video'] );

		$updated = true;
	}

	$audio = ( !get_option( 'media-galleries-audio' ) ) ? 'on' : get_option( 'media-galleries-audio' );
	$images = ( !get_option( 'media-galleries-images' ) ) ? 'on' : get_option( 'media-galleries-images' );
	$video = ( !get_option( 'media-galleries-video' ) ) ? 'on' : get_option( 'media-galleries-video' );
?>
	<div class="wrap">
		<h2><?php _e( 'Media Galleries', 'bp-media-galleries' ) ?></h2>
		<br />

		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-media-galleries' ) . "</p></div>" ?><?php endif; ?>

		<form action="<?php echo admin_url('admin.php?page=bp-media-galleries') ?>" name="media-galleries-settings-form" id="media-galleries-settings-form" method="post">

			<table class="form-table">
				<tr valign="top">
					<th scope="row" nowrap="nowrap"><label for="media-galleries-audio"><?php _e( 'Allow users to post audio (i.e. .mp3, .wav, .aif) files?', 'bp-media-galleries' ) ?></label></th>
					<td>
						<input name="media-galleries-audio" type="radio" id="media-galleries-audio" value="on"<?php echo ( $audio == 'on' ) ? ' checked="checked"' : ''; ?> /> On
						<input name="media-galleries-audio" type="radio" id="media-galleries-audio" value="off"<?php echo ( $audio == 'off' ) ? ' checked="checked"' : ''; ?> /> Off
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" nowrap="nowrap"><label for="media-galleries-images"><?php _e( 'Allow users to post images (i.e. .jpg, .gif, .png) files?', 'bp-media-galleries' ) ?></label></th>
					<td>
						<input name="media-galleries-images" type="radio" id="media-galleries-images" value="on"<?php echo ( $images == 'on' ) ? ' checked="checked"' : ''; ?>  /> On
						<input name="media-galleries-images" type="radio" id="media-galleries-images" value="off"<?php echo ( $images == 'off' ) ? ' checked="checked"' : ''; ?>  /> Off
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" nowrap="nowrap"><label for="media-galleries-video"><?php _e( 'Allow users to post video (i.e. .mp4, .mov, .wmv) files?', 'bp-media-galleries' ) ?></label></th>
					<td>
						<input name="media-galleries-video" type="radio" id="media-galleries-video" value="on"<?php echo ( $video == 'on' ) ? ' checked="checked"' : ''; ?> /> On
						<input name="media-galleries-video" type="radio" id="media-galleries-video" value="off"<?php echo ( $video == 'off' ) ? ' checked="checked"' : ''; ?> /> Off
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-media-galleries' ) ?>"/>
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'bp-media-galleries-settings' );
			?>
		</form>
	</div>
<?php
}