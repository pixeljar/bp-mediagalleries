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

class BP_Media_Galleries_Media {
	var $id;
	var $gallery_id;
	var $media_type;
	var $media_name;
	var $cover;
	var $filename;
	var $thumbname;
	var $width;
	var $height;
	var $media_description;
	
	/**
	 * bp_example_tablename()
	 *
	 * This is the constructor, it is auto run when the class is instantiated.
	 * It will either create a new empty object if no ID is set, or fill the object
	 * with a row from the table if an ID is provided.
	 */
	function bp_media_galleries_media( $id = null ) {
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
		
		if ( $row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bp->media_galleries->media_table} WHERE id = %d", $this->id ) ) ) {
			$this->gallery_id = $row->gallery_id;
			$this->media_type = $row->media_type;
			$this->media_name = $row->media_name;
			$this->cover = $row->cover;
			$this->filename = $row->filename;
			$this->thumbname = $row->thumbname;
			$this->width = $row->width;
			$this->height = $row->height;
			$this->media_description = $row->media_description;
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
		
		$this->gallery_id = apply_filters( 'bp_media_galleries_data_gallery_id_before_save', $this->gallery_id, $this->id );
		$this->media_type = apply_filters( 'bp_media_galleries_data_media_type_before_save', $this->media_type, $this->id );
		$this->media_name = apply_filters( 'bp_media_galleries_data_media_name_before_save', $this->media_name, $this->id );
		$this->cover = apply_filters( 'bp_media_galleries_data_cover_before_save', $this->cover, $this->id );
		$this->filename = apply_filters( 'bp_media_galleries_data_filename_before_save', $this->filename, $this->id );
		$this->thumbname = apply_filters( 'bp_media_galleries_data_thumbname_before_save', $this->thumbname, $this->id );
		$this->width = apply_filters( 'bp_media_galleries_data_width_before_save', $this->width, $this->id );
		$this->height = apply_filters( 'bp_media_galleries_data_height_before_save', $this->height, $this->id );
		$this->media_description = apply_filters( 'bp_media_galleries_data_media_description_before_save', $this->media_description, $this->id );
		
		/* Call a before save action here */
		do_action( 'bp_media_galleries_media_data_before_save', $this );
						
		if ( $this->id ) {
			// Update
			$result = $wpdb->query( $wpdb->prepare( 
					"UPDATE {$bp->media_galleries->gallery_table} SET 
						gallery_id = %d,
						media_type = '%s',
						media_name = '%s',
						cover = %d,
						filename = '%s',
						thumbname = '%s',
						width = %d,
						height = %d,
						media_description = '%s'
					WHERE id = %d",
						$this->gallery_id,
						$this->media_type,
						$this->media_name,
						$this->cover,
						$this->filename,
						$this->thumbname,
						$this->width,
						$this->height,
						$this->media_description,
						$this->id
					) );
		} else {
			// Save
			$result = $wpdb->query( $wpdb->prepare( 
					"INSERT INTO {$bp->media_galleries->gallery_table} ( 
						gallery_id,
						media_type,
						media_name,
						cover,
						filename,
						thumbname,
						width,
						height,
						media_description
					) VALUES ( 
						%d, '%s', '%s', %d, '%s', '%s', %d, %d, '%s'
					)", 
						$this->gallery_id,
						$this->media_type,
						$this->media_name,
						$this->cover,
						$this->filename,
						$this->thumbname,
						$this->width,
						$this->height,
						$this->media_description
					) );
		}
				
		if ( !$result )
			return false;
		
		if ( !$this->id ) {
			$this->id = $wpdb->insert_id;
		}	
		
		/* Add an after save action here */
		do_action( 'bp_media_galleries_media_data_after_save', $this ); 
		
		return $result;
	}

	/**
	 * delete()
	 *
	 * This method will delete the corresponding row for an object from the database.
	 */	
	function delete() {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->media_galleries->media_table} WHERE id = %d", $this->id ) );
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
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->media_galleries->media_table}" ) );
	}

	function delete_by_media_type( $media_type ) {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->media_galleries->media_table} WHERE media_type = %s", $media_type ) );
	}

	function delete_by_gallery_id( $gallery_id ) {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->media_galleries->media_table} WHERE gallery_id = %d", $gallery_id ) );
	}

	function delete_by_user_id( $user_id ) {
		global $wpdb, $bp;
		
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$bp->media_galleries->media_table} WHERE user_id = %d", $user_id ) );
	}
}