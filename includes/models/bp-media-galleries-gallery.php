<?php

/**
 * This function should include all classes and functions that access the database.
 * In most BuddyPress components the database access classes are treated like a model,
 * where each table has a class that can be used to create an object populated with a row
 * from the corresponding database table.
 * 
 * By doing this you can easily save, update and delete records using the class, you're also
 * abstracting database access.
 */

class BP_Media_Galleries_Gallery {
	var $id;
	var $user_id;
	var $gallery_name;
	var $gallery_description;
	var $privacy;
	var $slug;
	
	/**
	 * bp_example_tablename()
	 *
	 * This is the constructor, it is auto run when the class is instantiated.
	 * It will either create a new empty object if no ID is set, or fill the object
	 * with a row from the table if an ID is provided.
	 */
	function bp_media_galleries_gallery( $id = null ) {
		global $wpdb, $bp;
		
		if ( $id ) {
			$this->id = $id;
			$this->populate( $this->id );
		}
	}
	
	/**
	 * populate()
	 *
	 * This method will populate the object with a row from the database, based on the
	 * ID passed to the constructor.
	 */
	function populate() {
		global $wpdb, $bp;
		
		if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->media_galleries->gallery_table} WHERE id = %d", $this->id ) ) ) {
			$this->user_id = $row->user_id;
			$this->gallery_name = $row->gallery_name;
			$this->gallery_description = $row->gallery_description;
			$this->privacy = $row->privacy;
			$this->slug = $row->slug;
		}
	}
	
	/**
	 * save()
	 *
	 * This method will save an object to the database. It will dynamically switch between
	 * INSERT and UPDATE depending on whether or not the object already exists in the database.
	 */
	
	function save() {
		global $wpdb, $bp;
		
		/***
		 * In this save() method, you should add pre-save filters to all the values you are saving to the
		 * database. This helps with two things -
		 * 
		 * 1. Blanket filtering of values by plugins (for example if a plugin wanted to force a specific 
		 *	  value for all saves)
		 * 
		 * 2. Security - attaching a wp_filter_kses() call to all filters, so you are not saving
		 *	  potentially dangerous values to the database.
		 *
		 * It's very important that for number 2 above, you add a call like this for each filter to
		 * 'bp-example-filters.php'
		 *
		 *   add_filter( 'example_data_fieldname1_before_save', 'wp_filter_kses' );
		 */	
		
		$this->gallery_name = apply_filters( 'bp_media_galleries_data_gallery_name_before_save', $this->gallery_name, $this->id );
		$this->gallery_description = apply_filters( 'bp_media_galleries_data_gallery_description_before_save', $this->gallery_description, $this->id );
		$this->privacy = apply_filters( 'bp_media_galleries_data_privacy_before_save', $this->privacy, $this->id );
		$this->slug = apply_filters( 'bp_media_galleries_data_slug_before_save', $this->slug, $this->id );
		
		/* Call a before save action here */
		do_action( 'bp_media_galleries_gallery_data_before_save', $this );
						
		if ( $this->id ) {
			// Update
			$result = $wpdb->query( $wpdb->prepare( 
					"UPDATE {$bp->media_galleries->gallery_table} SET 
						user_id = %d,
						gallery_name = '%s',
						gallery_description = '%s',
						privacy = %d,
						slug = '%s'
					WHERE id = %d",
						$this->user_id,
						$this->gallery_name,
						$this->gallery_description,
						$this->privacy,
						$this->slug,
						$this->id 
					) );
		} else {
			// Save
			$result = $wpdb->query( $wpdb->prepare( 
					"INSERT INTO {$bp->media_galleries->gallery_table} ( 
						user_id,
						gallery_name,
						gallery_description,
						privacy,
						slug
					) VALUES ( 
						%d, '%s', '%s', %d, %d
					)", 
						$this->user_id,
						$this->gallery_name,
						$this->gallery_description,
						$this->privacy,
						$this->slug
					) );
		}
				
		if ( !$result )
			return false;
		
		if ( !$this->id ) {
			$this->id = $wpdb->insert_id;
		}	
		
		/* Add an after save action here */
		do_action( 'bp_media_galleries_gallery_data_after_save', $this ); 
		
		return $result;
	}

	/**
	 * delete()
	 *
	 * This method will delete the corresponding row for an object from the database.
	 */	
	function delete() {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->media_galleries->gallery_table} g INNER JOIN {$bp->media_galleries->media_table} m ON g.id = m.gallery_id WHERE id = %d", $this->id ) );
	}

	/* Static Functions */

	/**
	 * Static functions can be used to bulk delete items in a table, or do something that
	 * doesn't necessarily warrant the instantiation of the class.
	 *
	 * Look at bp-core-classes.php for examples of mass delete.
	 */

	function delete_all() {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->media_galleries->gallery_table}, {$bp->media_galleries->media_table}" ) );
	}

	function delete_by_user_id( $user_id ) {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->media_galleries->gallery_table} g INNER JOIN {$bp->media_galleries->media_table} m ON g.id = m.gallery_id WHERE user_id = %d", $user_id ) );
	}
}