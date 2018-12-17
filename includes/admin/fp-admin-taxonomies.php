<?php
/**
 * Admin taxonomy functions
 *
 * These functions control admin interface bits like category ordering.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin/Taxonomies
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action( 'admin_init', 'foodpress_taxonomy_admin' );
function foodpress_taxonomy_admin(){
	add_action( 'meal_type_pre_add_form', 'foodpress_meal_type_description' );
	add_action( 'dish_type_pre_add_form', 'foodpress_dish_type_description' );
	add_action( 'menu_location_pre_add_form', 'foodpress_menu_location_description' );

	add_filter( 'manage_edit-meal_type_columns', 'fp_meal_type_edit_columns',5 );
	add_filter( 'manage_meal_type_custom_column', 'fp_meal_type_custom_columns',5,3 );

	add_filter( 'manage_edit-dish_type_columns', 'dish_type_edit_columns',5 );
	add_filter( 'manage_dish_type_custom_column', 'dish_type_custom_columns',5,3 );

	add_filter( 'manage_edit-menu_location_columns', 'location_edit_columns',5 );
	add_filter( 'manage_menu_location_custom_column', 'location_custom_columns',5,3 );

	add_action( 'meal_type_add_form_fields', 'fp_add_new_tems', 10, 2 );
	add_action( 'meal_type_edit_form_fields', 'fp_edit_term_page', 10, 2 );

	add_action( 'dish_type_add_form_fields', 'fp_add_new_tems_disht', 10, 2 );
	add_action( 'dish_type_edit_form_fields', 'fp_edit_term_page_disht', 10, 2 );

	add_action( 'edited_meal_type', 'fp_save_new_terms', 10, 2 );
	add_action( 'create_meal_type', 'fp_save_new_terms', 10, 2 );

	add_action( 'edited_dish_type', 'fpdt_save_new_terms', 10, 2 );
	add_action( 'create_dish_type', 'fpdt_save_new_terms', 10, 2 );


	global $pagenow;

	//if($pagenow =='edit-tags.php' && !empty($_GET['taxonomy']) && $_GET['taxonomy']=='meal_type' && $_GET['post_type']=='menu'){
		wp_enqueue_media();
	//}


}

// descriptions
	/** Description for meal_type page to aid users.  */
		function foodpress_meal_type_description() {
			echo wpautop( __( 'Meal Type Categories can be managed here. You can add menu items to meal types and later create menus with meal types.', 'foodpress' ) );
		}

	/** Description for dish_type page to aid users. */
		function foodpress_dish_type_description() {
			echo wpautop( __( 'Dish Type Categories can be managed here. You can add menu items to dish types and later create menus with dish types.', 'foodpress' ) );
		}
	/** Description for location terms to aid users. */
		function foodpress_menu_location_description() {
			echo wpautop( __( 'Menu Locations Categories can be managed here. You can add menu items to locations and later create menus menu items from only certain locations.', 'foodpress' ) );
		}

// additional columns to menu items

	// MEAL type
		function fp_meal_type_edit_columns($defaults){
			$defaults['meal_type_id'] = __('ID');
		    $defaults['menuicon'] = __('Icon');
		    return $defaults;
		}

		function fp_meal_type_custom_columns($value, $column_name, $id){
			if($column_name == 'menuicon'){
				$term_meta = get_option( "fp_taxonomy_$id" );

				$icon = (!empty($term_meta['fpm_iconname']))?
					"<p><i class='fa ".$term_meta['fpm_iconname']. "'/></p>":
					"<p><i class='fa'>--</i></p>";

				return $icon;
			}
			if($column_name == 'meal_type_id'){
				return (int)$id;
			}
		}


	// DISH type and additional Menu type #3
		function dish_type_edit_columns($defaults){
		    $defaults['dish_type_id'] = __('ID');
		    $defaults['dishicon'] = __('Icon');
		    return $defaults;
		}

		function dish_type_custom_columns($value, $column_name, $id){
			if($column_name == 'dishicon'){
				$term_meta = get_option( "fp_taxonomy_$id" );

				$icon = (!empty($term_meta['fpm_iconname']))?
					"<p><i class='fa ".$term_meta['fpm_iconname']. "'/></p>":
					"<p><i class='fa'>--</i></p>";

				return $icon;
			}
			if($column_name == 'dish_type_id'){
				return (int)$id;
			}
		}


	// Location
		function location_edit_columns($defaults){
		    $defaults['location_id'] = __('ID');
		    return $defaults;
		}
		function location_custom_columns($value, $column_name, $id){
			if($column_name == 'location_id'){
				return (int)$id;
			}
		}

// Menu item type extra field
	// add term page
		function fp_add_new_tems() {
			global $foodpress;
			echo foodpress_admin_load_font_icons_box();
			// this will add the custom meta field to the add new term page

			?>
			<div class="form-field" id='fp_icon_field'>
				<label for="term_meta[fpm_iconname]"><?php _e( 'Menu Type Icon', 'foodpress' ); ?> <p class='faicon'><i class='fp fp-apple'></i><input type="hidden" name="term_meta[fpm_iconname]" id="term_meta[fpm_iconname]" value=""></p></label>
			</div>
			<div class="form-field">
				<label><?php _e( 'Thumbnail', 'foodpress' ); ?></label>
				<div id="meal_type_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo fp_placeholder_img_src(); ?>" width="60px" height="60px" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="meal_type_thumbnail_id" name="term_meta[meal_type_thumbnail_id]" />
					<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'foodpress' ); ?></button>
					<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'foodpress' ); ?></button>
				</div>
				<script type="text/javascript">

					 // Only show the "remove image" button when needed
					 if ( ! jQuery('#meal_type_thumbnail_id').val() )
						 jQuery('.remove_image_button').hide();

					// Uploading files
					var file_frame;

					jQuery(document).on( 'click', '.upload_image_button', function( event ){

						event.preventDefault();

						// If the media frame already exists, reopen it.
						if ( file_frame ) {
							file_frame.open();
							return;
						}

						// Create the media frame.
						file_frame = wp.media.frames.downloadable_file = wp.media({
							title: '<?php _e( 'Choose an image', 'foodpress' ); ?>',
							button: {
								text: '<?php _e( 'Use image', 'foodpress' ); ?>',
							},
							multiple: false
						});



						// When an image is selected, run a callback.
						file_frame.on( 'select', function() {
							attachment = file_frame.state().get('selection').first().toJSON();

							jQuery('#meal_type_thumbnail_id').val( attachment.id );
							jQuery('#meal_type_thumbnail img').attr('src', attachment.url );
							jQuery('.remove_image_button').show();
						});

						// Finally, open the modal.
						file_frame.open();
					});

					jQuery(document).on( 'click', '.remove_image_button', function( event ){
						jQuery('#meal_type_thumbnail img').attr('src', '<?php echo fp_placeholder_img_src(); ?>');
						jQuery('#meal_type_thumbnail_id').val('');
						jQuery('.remove_image_button').hide();
						return false;
					});

				</script>
				<div class="clear"></div>
			</div>

		<?php
		}

	// Edit term page
		function fp_edit_term_page($term) {

			global $foodpress;
			echo foodpress_admin_load_font_icons_box();

			// put the term ID into a variable
			$t_id = $term->term_id;
			// retrieve the existing value(s) for this meta field. This returns an array
			$term_meta = get_option( "fp_taxonomy_$t_id" );


			//print_r($term_meta);

			// thumbnail value
			$thumbnail_id 	= (!empty($term_meta['meal_type_thumbnail_id']))? absint( $term_meta['meal_type_thumbnail_id'] ): null;
			if ( $thumbnail_id )
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			else
				$image = fp_placeholder_img_src();


			// menu type icon value
		 	$__this_value = esc_attr( $term_meta['fpm_iconname'] ) ? esc_attr( $term_meta['fpm_iconname'] ) : '';

			?>
			<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[fpm_iconname]"><?php _e( 'Menu Type Icon', 'foodpress' ); ?></label></th>
				<td id='fp_menuicon'>
					<p class='fp_icon_p faicon' ><i class='fa <?php echo (!empty($__this_value)? $__this_value: 'fp-apple');?>'></i><input type="hidden" name="term_meta[fpm_iconname]" id="term_meta[fpm_iconname]" value="<?php echo $__this_value;?>"></p>
					<p class="description"><?php _e( 'Click on the icon to change','foodpress' ); ?></p>
					<button class="remove_icon_button button"><?php _e( 'Remove Icon', 'foodpress' ); ?></button>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'foodpress' ); ?></label></th>
				<td>
					<div id="meal_type_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
					<div style="line-height:60px;">
						<input type="hidden" id="meal_type_thumbnail_id" name="term_meta[meal_type_thumbnail_id]" value="<?php echo $thumbnail_id; ?>" />
						<button type="submit" class="upload_image_button button"><?php _e( 'Upload/Add Image', 'foodpress' ); ?></button>
						<button type="submit" class="remove_image_button button"><?php _e( 'Remove Image', 'foodpress' ); ?></button>
					</div>
					<script type="text/javascript">

						// remove icon
							jQuery('body').on('click','.remove_icon_button', function(event){
								event.preventDefault();
								jQuery(this).siblings('.fp_icon_p').find('input').attr({'value':''});
							});

						// Uploading files
						var file_frame;

						jQuery(document).on( 'click', '.upload_image_button', function( event ){

							event.preventDefault();

							// If the media frame already exists, reopen it.
							if ( file_frame ) {
								file_frame.open();
								return;
							}

							// Create the media frame.
							file_frame = wp.media.frames.downloadable_file = wp.media({
								title: '<?php _e( 'Choose an image', 'foodpress' ); ?>',
								button: {
									text: '<?php _e( 'Use image', 'foodpress' ); ?>',
								},
								multiple: false
							});

							// When an image is selected, run a callback.
							file_frame.on( 'select', function() {
								attachment = file_frame.state().get('selection').first().toJSON();

								jQuery('#meal_type_thumbnail_id').val( attachment.id );
								jQuery('#meal_type_thumbnail img').attr('src', attachment.url );
								jQuery('.remove_image_button').show();
							});

							// Finally, open the modal.
							file_frame.open();
						});

						jQuery(document).on( 'click', '.remove_image_button', function( event ){
							jQuery('#meal_type_thumbnail img').attr('src', '<?php echo fp_placeholder_img_src(); ?>');
							jQuery('#meal_type_thumbnail_id').val('');
							jQuery('.remove_image_button').hide();
							return false;
						});

					</script>
					<div class="clear"></div>
				</td>
			</tr>

		<?php
		}

	// Save extra taxonomy fields callback function.
		function fp_save_new_terms( $term_id ) {
			if ( isset( $_POST['term_meta'] ) ) {
				$t_id = $term_id;
				$term_meta = get_option( "fp_taxonomy_$t_id" );

				$cat_keys = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ) {
					if ( isset ( $_POST['term_meta'][$key] ) ) {
						//echo $key;
						$term_meta[$key] = $_POST['term_meta'][$key];
					}
				}
				// Save the option array.
				update_option( "fp_taxonomy_$t_id", $term_meta );
			}
		}

// dish type extra fields
	// add dish type term page
		function fp_add_new_tems_disht() {
			global $foodpress;
			echo foodpress_admin_load_font_icons_box();
			// this will add the custom meta field to the add new term page

			?>
			<div class="form-field" id='fp_icon_field'>
				<label for="term_meta[fpm_iconname]"><?php _e( 'Dish Type Icon', 'foodpress' ); ?> <p class='faicon'><i class='fp fp-apple'></i><input type="hidden" name="term_meta[fpm_iconname]" id="term_meta[fpm_iconname]" value=""></p></label>
			</div>
			<?php /*
			<div class="form-field">
				<label><?php _e( 'Thumbnail', 'foodpress' ); ?></label>
				<div id="dish_type_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo fp_placeholder_img_src(); ?>" width="60px" height="60px" /></div>
				<div style="line-height:60px;">
					<input type="hidden" id="dish_type_thumbnail_id" name="term_meta[dish_type_thumbnail_id]" />
					<button type="button" class="upload_image_button button"><?php _e( 'Upload/Add image', 'foodpress' ); ?></button>
					<button type="button" class="remove_image_button button"><?php _e( 'Remove image', 'foodpress' ); ?></button>
				</div>

				<div class="clear"></div>
			</div>
			*/?>
			<script type="text/javascript">
				// Only show the "remove image" button when needed
					 if ( ! jQuery('#dish_type_thumbnail_id').val() )
						 jQuery('.remove_image_button').hide();

				// Uploading files
				var file_frame;

				jQuery(document).on( 'click', '.upload_image_button', function( event ){

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( 'Choose an image', 'foodpress' ); ?>',
						button: {
							text: '<?php _e( 'Use image', 'foodpress' ); ?>',
						},
						multiple: false
					});



					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();

						jQuery('#dish_type_thumbnail_id').val( attachment.id );
						jQuery('#dish_type_thumbnail img').attr('src', attachment.url );
						jQuery('.remove_image_button').show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery(document).on( 'click', '.remove_image_button', function( event ){
					jQuery('#dish_type_thumbnail img').attr('src', '<?php echo fp_placeholder_img_src(); ?>');
					jQuery('#dish_type_thumbnail_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});

			</script>

		<?php
		}

	// Edit Dish type term page
		function fp_edit_term_page_disht($term) {

			global $foodpress;
			echo foodpress_admin_load_font_icons_box();

			// put the term ID into a variable
			$t_id = $term->term_id;
			// retrieve the existing value(s) for this meta field. This returns an array
			$term_meta = get_option( "fp_taxonomy_$t_id" );


			//print_r($term_meta);

			// thumbnail value
			$thumbnail_id 	= (!empty($term_meta['dish_type_thumbnail_id']))? absint( $term_meta['dish_type_thumbnail_id'] ): null;
			if ( $thumbnail_id )
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			else
				$image = fp_placeholder_img_src();


			// menu type icon value
		 	$__this_value = esc_attr( $term_meta['fpm_iconname'] ) ? esc_attr( $term_meta['fpm_iconname'] ) : '';

			?>
			<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[fpm_iconname]"><?php _e( 'Menu Type Icon', 'foodpress' ); ?></label></th>
				<td id='fp_menuicon'>
					<p class='fp_icon_p faicon' ><i class='fa <?php echo (!empty($__this_value)? $__this_value: 'fp-apple');?>'></i><input type="hidden" name="term_meta[fpm_iconname]" id="term_meta[fpm_iconname]" value="<?php echo $__this_value;?>"></p>
					<p class="description"><?php _e( 'Click on the icon to change','foodpress' ); ?></p>
					<button class="remove_icon_button button"><?php _e( 'Remove Icon', 'foodpress' ); ?></button>
				</td>
			</tr>
			<?php /*
			<tr class="form-field">
				<th scope="row" valign="top"><label><?php _e( 'Thumbnail', 'foodpress' ); ?></label></th>
				<td>
					<div id="dish_type_thumbnail" style="float:left;margin-right:10px;"><img src="<?php echo $image; ?>" width="60px" height="60px" /></div>
					<div style="line-height:60px;">
						<input type="hidden" id="dish_type_thumbnail_id" name="term_meta[dish_type_thumbnail_id]" value="<?php echo $thumbnail_id; ?>" />
						<button type="submit" class="upload_image_button button"><?php _e( 'Upload/Add Image', 'foodpress' ); ?></button>
						<button type="submit" class="remove_image_button button"><?php _e( 'Remove Image', 'foodpress' ); ?></button>
					</div>

					<div class="clear"></div>
				</td>
			</tr>
			*/?>
			<script type="text/javascript">

				// remove icon
					jQuery('body').on('click','.remove_icon_button', function(event){
						event.preventDefault();
						jQuery(this).siblings('.fp_icon_p').find('input').attr({'value':''});
					});

				// Uploading files
				var file_frame;

				jQuery(document).on( 'click', '.upload_image_button', function( event ){

					event.preventDefault();

					// If the media frame already exists, reopen it.
					if ( file_frame ) {
						file_frame.open();
						return;
					}

					// Create the media frame.
					file_frame = wp.media.frames.downloadable_file = wp.media({
						title: '<?php _e( 'Choose an image', 'foodpress' ); ?>',
						button: {
							text: '<?php _e( 'Use image', 'foodpress' ); ?>',
						},
						multiple: false
					});

					// When an image is selected, run a callback.
					file_frame.on( 'select', function() {
						attachment = file_frame.state().get('selection').first().toJSON();

						jQuery('#dish_type_thumbnail_id').val( attachment.id );
						jQuery('#dish_type_thumbnail img').attr('src', attachment.url );
						jQuery('.remove_image_button').show();
					});

					// Finally, open the modal.
					file_frame.open();
				});

				jQuery(document).on( 'click', '.remove_image_button', function( event ){
					jQuery('#dish_type_thumbnail img').attr('src', '<?php echo fp_placeholder_img_src(); ?>');
					jQuery('#dish_type_thumbnail_id').val('');
					jQuery('.remove_image_button').hide();
					return false;
				});

			</script>

		<?php
		}

	// Save extra taxonomy fields callback function.
		function fpdt_save_new_terms( $term_id ) {
			if ( isset( $_POST['term_meta'] ) ) {
				$t_id = $term_id;
				$term_meta = get_option( "fp_taxonomy_$t_id" );

				$cat_keys = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ) {
					if ( isset ( $_POST['term_meta'][$key] ) ) {
						//echo $key;
						$term_meta[$key] = $_POST['term_meta'][$key];
					}
				}
				// Save the option array.
				update_option( "fp_taxonomy_$t_id", $term_meta );
			}
		}

?>
