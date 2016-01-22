<?php
/**
 * Post Types Admin
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin
 * @version     1.1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'FP_Admin_Post_Types' ) ) :

/**
 * FP_Admin_Post_Types Class
 */
class FP_Admin_Post_Types {

	/** Constructor */
	public function __construct() {

		// menu posts
		add_filter( 'manage_edit-menu_columns', array( $this,'foodpress_edit_menu_columns') );
		add_action('manage_menu_posts_custom_column', array( $this,'foodpress_custom_menuitems_columns'), 10, 2 );
		add_filter( 'manage_edit-menu_sortable_columns', array( $this,'foodpress_custom_menu_sort'));
		add_filter( 'request', array( $this,'foodpress_custom_product_orderby' ));

		// Bulk/ quick edit
		add_action('bulk_edit_custom_box',  array( $this,'foodpress_admin_product_bulk_edit'), 10, 2);
		add_action('quick_edit_custom_box',  array( $this,'quick_edit_form'), 10, 2);
		add_action( 'admin_enqueue_scripts', array( $this,'foodpress_admin_events_quick_edit_scripts'), 10 );
		add_action( 'save_post', array( $this,'foodpress_admin_product_bulk_quick_edit_save'), 10, 2 );


		// reservation posts
		add_filter( 'manage_edit-reservation_columns', array( $this,'edit_reservation_column') );
		add_action('manage_reservation_posts_custom_column', array( $this,'custom_reservation_columns'), 10, 2 );
		add_filter( 'manage_edit-reservation_sortable_columns', array( $this,'reservation_sort'));
		add_filter( 'request', array( $this,'reservation_request' ));
	}

	/** Columns for Menu Items page	 */
		function foodpress_edit_menu_columns( $existing_columns ) {
			global $foodpress;
				
			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
				$existing_columns = array();

			unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

			//----
			$fp_opt= get_option('fp_options_food_1');
			$fp_opt_2= get_option('fp_options_food_2');	
			//menu item types category 
			$fp_mty1 = (!empty($fp_opt['fp_mty1']))?$fp_opt['fp_mty1']:'Meal Type';
			$fp_mty2 = (!empty($fp_opt['fp_mty2']))?$fp_opt['fp_mty2']:'Dish Type';
			$fp_taxL =  fp_get_language('Location',$fp_opt_2 );
			//----
			
			
			$columns = array();
			$columns["cb"] = "<input type=\"checkbox\" />";
			
			//$columns["title"] = __( 'Menu Item Name', 'foodpress ' );
			//$columns["thumb"] = __( 'Image', 'foodpress' );
			$columns["featured"] = '<img src="' . FP_URL . '/assets/images/featured.png" title="' . __( 'Featured', 'foodpress' ) . '" class="tips" data-tip="' . __( 'Featured', 'foodpress' ) . '" width="12" height="12" />';
			$columns["name"] = __( 'Item Name', 'foodpress' );

			$columns["meal_type"] = __( $fp_mty1, 'foodpress' );
			$columns["dish_type"] = __( $fp_mty2, 'foodpress' );
			$columns["menu_location"] = __( $fp_taxL, 'foodpress' );
			//$columns["item_price"] = __( 'Price', 'foodpress' );
			//$columns["spice_level"] = __( 'Spice Level', 'foodpress' );
			

			$columns = apply_filters('foodpress_menuitems_columns', $columns);	
			
			return array_merge( $columns, $existing_columns );
		}

	/** Custom Columns for Products page */
		function foodpress_custom_menuitems_columns( $column , $post_id) {
			global $post, $foodpress;

			$pmv = get_post_custom($post_id);

			switch ($column) {
				case has_filter("foodpress_mi_column_type_{$column}"):
					$content = apply_filters("foodpress_mi_column_type_{$column}", $post_id);
					echo $content;
				break;
				case "thumb" :
					
				break;
				
				case "name" :
					$edit_link = get_edit_post_link( $post_id );
					$title = _draft_or_post_title();
					$post_type_object = get_post_type_object( $post->post_type );
					$can_edit_post = current_user_can( $post_type_object->cap->edit_post, $post_id );


					echo "<div class='fpmenu_item'>";
					// thumbnail
					$img = $foodpress->foodpress_menus->get_image($post_id);				
					echo '<a class="fpmenu_image" href="' . get_edit_post_link( $post_id ) . '">' . $img . '</a><div class="fpmenu_item_details">';

					// name and stuff
					echo '<strong><a class="row-title" href="'.$edit_link.'">' . $title.'</a>';
					_post_states( $post );
					echo '</strong>';

					// Price
					$price = get_post_meta($post_id, 'fp_price', true);
					echo (!empty($price))? '<br/><i>'.apply_filters('foodpress_price_value', $price, $pmv).'</i>': null;

					// Spicy level
					$slevel = get_post_meta($post_id, 'fp_spicy', true);

					echo (!empty($slevel))? '<br/><i>Spicy Level: '.$slevel.'/5</i>': '';

					
					if ( $post->post_parent > 0 )
						echo '&nbsp;&nbsp;&larr; <a href="'. get_edit_post_link($post->post_parent) .'">'. get_the_title($post->post_parent) .'</a>';

					// Excerpt view
					if (isset($_GET['mode']) && $_GET['mode']=='excerpt') echo apply_filters('the_excerpt', $post->post_excerpt);

					// Get actions
					$actions = array();

					$actions['id'] = 'ID: ' . $post_id;

					if ( $can_edit_post && 'trash' != $post->post_status ) {
						$actions['edit'] = '<a href="' . get_edit_post_link( $post_id, true ) . '" title="' . esc_attr( __( 'Edit this item' ) ) . '">' . __( 'Edit' ) . '</a>';
						$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr( __( 'Edit this item inline' ) ) . '">' . __( 'Quick&nbsp;Edit' ) . '</a>';
					}

					if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
						if ( 'trash' == $post->post_status )
							$actions['untrash'] = "<a title='" . esc_attr( __( 'Restore this item from the Trash', 'foodpress' ) ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore', 'foodpress' ) . "</a>";
						elseif ( EMPTY_TRASH_DAYS )
							$actions['trash'] = "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash', 'foodpress' ) ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash', 'foodpress' ) . "</a>";
						if ( 'trash' == $post->post_status || !EMPTY_TRASH_DAYS )
							$actions['delete'] = "<a class='submitdelete' title='" . esc_attr( __( 'Delete this item permanently', 'foodpress' ) ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently', 'foodpress' ) . "</a>";
					}
					if ( $post_type_object->public ) {
						if ( in_array( $post->post_status, array( 'pending', 'draft', 'future' ) ) ) {
							if ( $can_edit_post )
								$actions['view'] = '<a href="' . esc_url( add_query_arg( 'preview', 'true', get_permalink( $post->ID ) ) ) . '" title="' . esc_attr( sprintf( __( 'Preview &#8220;%s&#8221;', 'foodpress' ), $title ) ) . '" rel="permalink">' . __( 'Preview', 'foodpress' ) . '</a>';
						} elseif ( 'trash' != $post->post_status ) {
							$actions['view'] = '<a href="' . get_permalink( $post->ID ) . '" title="' . esc_attr( sprintf( __( 'View &#8220;%s&#8221;', 'foodpress' ), $title ) ) . '" rel="permalink">' . __( 'View', 'foodpress' ) . '</a>';
						}
					}

					$actions = apply_filters( 'post_row_actions', $actions, $post );

					echo '<div class="row-actions">';

					$i = 0;
					$action_count = sizeof($actions);

					foreach ( $actions as $action => $link ) {
						++$i;
						( $i == $action_count ) ? $sep = '' : $sep = ' | ';
						echo "<span class='$action'>$link$sep</span>";
					}
					echo '</div>';

					get_inline_data( $post );

					$the_item = get_post_meta($post->ID);

					/* Custom inline data for foodpress */
					echo '
						<div class="hidden" id="foodpress_inline_' . $post->ID . '">
							<div class="menu_order">' . $post->menu_order . '</div>
							<div class="price">' . ( !empty($the_item['fp_price'])? $the_item['fp_price'][0]: null) . '</div>
							<div class="spicelevel">' . ( !empty($the_item['fp_spicy'])? $the_item['fp_spicy'][0]: null) . '</div>
							<div class="vegetarian">' . ( !empty($the_item['fp_vege'])? $the_item['fp_vege'][0]: null) . '</div>
							<div class="featured">' . ( !empty($the_item['_featured'])? $the_item['_featured'][0]: null) . '</div>
							
						</div>
					';

					echo "</div>"; //fpmenu_item_details 
					echo "</div>"; //fpmenu_item
					
				break;
				
				case "meal_type" :		
					if ( ! $terms = get_the_terms( $post_id, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=menu' ) . ' ">' . $term->name . '</a>';
						}

						echo implode( ', ', $termlist );
					}
				break;
				case "dish_type" :		
					if ( ! $terms = get_the_terms( $post_id, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=menu' ) . ' ">' . $term->name . '</a>';
						}

						echo implode( ', ', $termlist );
					}
				break;
				case "menu_location" :		
					if ( ! $terms = get_the_terms( $post_id, $column ) ) {
						echo '<span class="na">&ndash;</span>';
					} else {
						foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=menu' ) . ' ">' . $term->name . '</a>';
						}

						echo implode( ', ', $termlist );
					}
				break;
				case "item_price" :		
					
					$pmv = get_post_custom($post_id);
					$price = get_post_meta($post_id, 'fp_price', true);

					echo (!empty($price))? apply_filters('foodpress_price_value', $price, $pmv): null;

				break;

				case "spice_level" :		
					
					$pmv = get_post_custom($post_id);
					$slevel = get_post_meta($post_id, 'fp_spicy', true);

					echo (!empty($slevel))? $slevel.'/5': '--';

				break;
				case "featured":
					
					$url = wp_nonce_url( admin_url( 'admin-ajax.php?action=foodpress-feature-menuitem&menu_id=' . $post_id ), 'foodpress-feature-menuitem' );
					echo '<a href="' . $url . '" title="'. __( 'Toggle featured', 'foodpress' ) . '">';
					if ( get_post_meta($post_id, '_featured', true)=='yes' ) {
						echo '<img src="' . FP_URL . '/assets/images/featured.png" title="'. __( 'Yes', 'foodpress' ) . '" height="14" width="14" />';
					} else {
						echo '<img src="' . FP_URL . '/assets/images/featured-off.png" title="'. __( 'No', 'foodpress' ) . '" height="14" width="14" />';
					}
					echo '</a>';
					
					//echo get_post_meta($post->ID, 'fp_feature', true);		
				break;
				
			}
		}

		function foodpress_custom_menu_sort($columns) {
			$custom = array(
				'item_price'			=> 'item_price',
				'featured'			=> 'featured',
				'spice_level'			=> 'spice_level',
				'name'					=> 'title'
			);
			return wp_parse_args( $custom, $columns );
		}
		function foodpress_custom_product_orderby( $vars ) {
			if (isset( $vars['orderby'] )) :
				
				if ( 'featured' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> '_featured',
						'orderby' 	=> 'meta_value'
					) );
				endif;
				if ( 'item_price' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'fp_price',
						'orderby' 	=> 'meta_value'
					) );
				endif;	
				if ( 'spice_level' == $vars['orderby'] ) :
					$vars = array_merge( $vars, array(
						'meta_key' 	=> 'fp_spicy',
						'orderby' 	=> 'meta_value'
					) );
				endif;		
				
			endif;

			return $vars;
		}

	// QUICK EDIT

		/** Custom quick edit - script */
		function foodpress_admin_events_quick_edit_scripts( $hook ) {
			global $foodpress, $post_type;

			if ( $hook == 'edit.php' && $post_type == 'menu' )
		    	wp_enqueue_script( 'foodpress_quick-edit', FP_URL. '/assets/js/admin/quick-edit.js', array('jquery') );
		}

		// Quick edit form
		function quick_edit_form($column_name, $post_type){
			if ($post_type != 'menu') return;
			if($column_name == 'featured'):
			?>
			
			 <fieldset class="inline-edit-col-right">
				<div id="foodpress-fields-bulk" class="inline-edit-col">

					<h4><?php _e( 'Menu Data', 'foodpress' ); ?></h4>

					<?php do_action( 'foodpress_product_bulk_edit_start' ); ?>

					<label>
					    <span class="title"><?php _e( 'Price', 'foodpress' ); ?></span>
					    <span class="input-text-wrap">
							<input type="text" name="fp_price" class="text" placeholder="<?php _e( '00.00', 'foodpress' ); ?>" value="">
						</span>
					</label>
					
					<label>
					    <span class="title"><?php _e( 'Spicy Level', 'foodpress' ); ?></span>
					    <span class="input-text-wrap">
					    	<select class="spice-level" name="fp_spicy">
							<?php
								$options = array(
									'0' => '0',
									'1' => '1',
									'2' => '2',
									'3' => '3',
									'4' => '4',
									'5' => '5',
								);
								foreach ($options as $key => $value) {
									echo '<option value="'.$key.'">'. $value .'</option>';
								}
							?>
							</select>
						</span>
					</label>

					<label>
					    <span class="title"><?php _e( 'Vegetarian', 'foodpress' ); ?></span>
					    <span class="input-text-wrap">
					    	<select class="vegetarian" name="fp_vege">
							<?php
								$options = array(
									'' => __('No Change', 'foodpress' ),
									'yes' => __( 'Yes', 'foodpress' ),
									'no' => __( 'No', 'foodpress' )
								);
								foreach ($options as $key => $value) {
									echo '<option value="'.$key.'">'. $value .'</option>';
								}
							?>
							</select>
						</span>
					</label>
					<label class="alignleft featured">
						<input type="checkbox" name="_featured" value="1">
						<span class="checkbox-title"><?php _e( 'Featured', 'foodpress' ); ?></span>
					</label>
				
					
					<?php do_action( 'foodpress_product_bulk_edit_end' ); ?>

					<input type="hidden" name="foodpress_bulk_edit_nonce" value="<?php echo wp_create_nonce( 'foodpress_bulk_edit_nonce' ); ?>" />
				</div>
			</fieldset>

			<?php 

			endif;
		}

	// BULK EDIT

		/** Custom bulk edit - form */
		function foodpress_admin_product_bulk_edit( $column_name, $post_type ) {
			if ($post_type != 'menu') return;
			
				if($column_name == 'featured'):
			?>
		    <fieldset class="inline-edit-col-right">
				<div id="foodpress-fields-bulk" class="inline-edit-col">

					<h4><?php _e( 'Menu Data', 'foodpress' ); ?></h4>

					<?php do_action( 'foodpress_product_bulk_edit_start' ); ?>

					
					<label>
					    <span class="title"><?php _e( 'Featured', 'foodpress' ); ?></span>
					    <span class="input-text-wrap">
					    	<select class="featured" name="_featured">
							<?php
								$options = array(
									'' => __( 'No Change', 'foodpress' ),
									'yes' => __( 'Yes', 'foodpress' ),
									'no' => __( 'No', 'foodpress' )
								);
								foreach ($options as $key => $value) {
									echo '<option value="'.$key.'">'. $value .'</option>';
								}
							?>
							</select>
						</span>
					</label>

					<label>
					    <span class="title"><?php _e( 'Spicy Level', 'foodpress' ); ?></span>
					    <span class="input-text-wrap">
					    	<select class="spice-level" name="fp_spicy">
							<?php
								$options = array(
									'' => __( 'No Change', 'foodpress' ),
									'1' => '1',
									'2' => '2',
									'3' => '3',
									'4' => '4',
									'5' => '5',
								);
								foreach ($options as $key => $value) {
									echo '<option value="'.$key.'">'. $value .'</option>';
								}
							?>
							</select>
						</span>
					</label>

					<label>
					    <span class="title"><?php _e( 'Vegetarian', 'foodpress' ); ?></span>
					    <span class="input-text-wrap">
					    	<select class="vegetarian" name="fp_vege">
							<?php
								$options = array(
									'' => __( 'No Change', 'foodpress' ),
									'yes' => __( 'Yes', 'foodpress' ),
									'no' => __( 'No', 'foodpress' )
								);
								foreach ($options as $key => $value) {
									echo '<option value="'.$key.'">'. $value .'</option>';
								}
							?>
							</select>
						</span>
					</label>
					<label class="alignleft featured">
						<input type="checkbox" name="_featured" value="1">
						<span class="checkbox-title"><?php _e( 'Featured', 'foodpress' ); ?></span>
					</label>
				
					
					<?php do_action( 'foodpress_product_bulk_edit_end' ); ?>

					<input type="hidden" name="foodpress_bulk_edit_nonce" value="<?php echo wp_create_nonce( 'foodpress_bulk_edit_nonce' ); ?>" />
				</div>
			</fieldset>
			<?php

				endif;
		}

		/** Custom bulk edit - save */
		function foodpress_admin_product_bulk_quick_edit_save( $post_id, $post ) {

			if ( is_int( wp_is_post_revision( $post_id ) ) ) return;
			if ( is_int( wp_is_post_autosave( $post_id ) ) ) return;
			if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
			// dont save for revisions
		  	if ( isset( $post->post_type ) && $post->post_type == 'revision' )
		      return $post_id;

			//if ( ! isset( $_REQUEST['foodpress_bulk_edit_nonce'] ) || ! wp_verify_nonce( $_REQUEST['foodpress_bulk_edit_nonce'], 'foodpress_bulk_edit_nonce' ) ) return $post_id;
			if ( ! current_user_can( 'edit_post', $post_id ) ) return $post_id;
			if ( $post->post_type != 'menu' ) return $post_id;

			global $foodpress, $wpdb;

			// Save fields

			if ( ! empty( $_REQUEST['fp_spicy'] ) )
				update_post_meta( $post_id, 'fp_spicy', stripslashes( $_REQUEST['fp_spicy'] ) );
			
			if ( ! empty( $_REQUEST['fp_price'] ) )
				update_post_meta( $post_id, 'fp_price', stripslashes( $_REQUEST['fp_price'] ) );

			if ( ! empty( $_REQUEST['_featured'] ) )
				update_post_meta( $post_id, '_featured', stripslashes( $_REQUEST['_featured'] ) );
			
			if ( ! empty( $_REQUEST['fp_vege'] ) )
				update_post_meta( $post_id, 'fp_vege', stripslashes( $_REQUEST['fp_vege'] ) );



			do_action( 'foodpress_product_bulk_edit_save', $post_id );
		}

	// reservation edit column
	function edit_reservation_column( $existing_columns ) {
		global $foodpress;
		
		// GET event type custom names
		
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) )
			$existing_columns = array();

		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns = array();
		$columns["cb"] = "<input type=\"checkbox\" />";	

		$columns['reservation_status'] = __( 'Status', 'foodpress' );
		$columns['reservation_title'] = __( 'Reservation', 'foodpress' );			
		$columns["date_"] = __( 'Date', 'foodpress' );
		$columns["time"] = __( 'Time', 'foodpress' );
		$columns["count"] = __( 'Size', 'foodpress' );
		$columns["location"] = __( 'Location', 'foodpress' );
		return array_merge( $columns, $existing_columns );
	}
	function custom_reservation_columns($column){
		global $post, $foodpress;

		//if ( empty( $ajde_events ) || $ajde_events->id != $post->ID )
			//$ajde_events = get_product( $post );

		$opt = get_option('evcal_options_evcal_2');

		$meta = get_post_meta($post->ID);

		switch ($column) {		
			case "reservation_title":
				$edit_link = get_edit_post_link( $post->ID );
				$email = (!empty($meta['email_address'])?
					($meta['email_address'][0]): 
						(!empty($meta['email'])? $meta['email'][0]:null)
				);
				$_email = (!empty($email))? '<a href="mailto:'.$email.'">'.$email.'</a>': null;
				$_name = !empty($meta['name'])? $meta['name'][0]: 
					((!empty($meta['first_name']) && !empty($meta['last_name']) )?$meta['first_name'][0]." ".$meta['last_name'][0]:'-');

				echo "<strong><a class='row-title' href='".$edit_link."'>#{$post->ID}</a></strong> by ".$_name." ".$_email;
			break;
			case "reservation_status":
				
				$post_status = get_post_status($post->ID);
				if($post_status!='publish'){
					echo "<p class='reservation_status_list {$post_status}'>".__('Pending','foodpress')."</p>";
				}else{
					$rsvp_status = get_post_meta($post->ID, 'status', true);			
					$rsvp_status_ =  (!empty($rsvp_status))? $rsvp_status: 'check-in';		

					$rsvp_status = $foodpress->reservations->get_checkin_status($rsvp_status_);
					echo "<p class='reservation_status_list {$rsvp_status_}'>".$rsvp_status."</p>";
				}			
					
			break;
			case "date_":
				echo get_post_meta($post->ID, 'date', true);
			break;
			case "time":
				$end_time = !empty($meta['end_time'])? ' - '.$meta['end_time'][0]: null;
				echo (!empty( $meta['time'])? $meta['time'][0]:'-').$end_time;

			break;
			case "count":
				echo (!empty($meta['people'])? $meta['people'][0]:'-');
			break;
			case "location":
				echo (!empty($meta['location'])? $meta['location'][0]:'-');

			break;
		}

	}
	function reservation_sort($columns) {
		$custom = array(
			'date_'		=> 'date',
			'time'		=> 'time',
			'reservation_status'		=> 'status',
		);
		return wp_parse_args( $custom, $columns );
	}
	function reservation_request( $vars ) {
		if (isset( $vars['orderby'] )) :
			if ( 'date_' == $vars['orderby'] ) :
				$vars = array_merge( $vars, array(
					'meta_key' 	=> 'date',
					'orderby' 	=> 'meta_value'
				) );
			endif;
			if ( 'time' == $vars['orderby'] ) :
				$vars = array_merge( $vars, array(
					'meta_key' 	=> 'time',
					'orderby' 	=> 'meta_value'
				) );
			endif;
			if ( 'status' == $vars['orderby'] ) :
				$vars = array_merge( $vars, array(
					'meta_key' 	=> 'status',
					'orderby' 	=> 'meta_value'
				) );
			endif;
			
		endif;

		return $vars;
	}
}
endif;
return new FP_Admin_Post_Types();