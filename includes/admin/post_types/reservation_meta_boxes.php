<?php
/**
 * Meta boxes for reservation custom post type
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin/reservation
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'foodpress_reservation_metaboxes' ) ) :

class foodpress_reservation_metaboxes {

	public function __construct(){
		add_action( 'add_meta_boxes', array($this, 'add_metabox' ));		
		add_action( 'save_post', array($this, 'save_meta_data'), 1, 2 );
		add_filter('gettext', array($this,'translation_mangler'), 10, 4);
	}

	function add_metabox(){
		add_meta_box (
			'reservation_info_display', 
			__( 'Reservation Information', 'foodpress' ),
			array( __CLASS__, 'reservation_info_display'), 
			'reservation', 
			'normal', 
			'high'
		);	
	}
	function translation_mangler($translation, $text, $domain) {
        global $post, $typenow;
        if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
//			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $post = get_post( $_GET['post'] );
	        $typenow = $post->post_type;
	    }

	    if (!empty($typenow) && $typenow == 'reservation') { 
	        if ( $text == 'Publish')
	            return __('Confirm Reservation','foodpress'); 
	        if ( $text == 'Update')
	            return __('Update Reservation','foodpress');
	        if ( $text == 'Published')
	            return __('Confirmed','foodpress');
	    }
	 
	    return $translation;
	}

	// reservation information for reservation post type
	public static function reservation_info_display(){
		global $post, $foodpress, $pagenow;

		// Use nonce for verification
			wp_nonce_field( plugin_basename( __FILE__ ), 'fp_noncename' );


		$reservation_id = $post->ID;
		$rmeta = get_post_custom($reservation_id);
		$opt6 = get_option('fp_options_food_6');

		$__sta =fp_menumeta($rmeta, 'status');				

		// using fp_menumeta() for values from includes/foodpress-core-functions.php line 192

		$new_reservation = ($pagenow=='post-new.php')? true:false;

		ob_start();

		?>
		<div class='foodpress_mb' style='margin:-6px -12px -12px'>
		<?php if($new_reservation):?>
			<h4 style='padding:0 15px;'><?php _e('Fill in the information below for new reservation','foodpress');?></h4>
		<?php endif;?>
		<div class='reservation_cpt_data' style='background-color:#ECECEC; padding:15px;'>
			<div style='background-color:#fff; border-radius:8px;'>
				
				<?php
					if($post->post_status !='publish'){
						echo "<div style='text-align:center; background-color:#F9B379; padding:10px; color:#fff; font-weight:bold;text-transform:uppercase; border-top-left-radius:5px;border-top-right-radius:5px; font-size:16px'>".__('Reservation is not confirmed!','foodpress')."</div>";
					}
				?>

			<table width='100%' class='fp_metatable' id='reservation_table'>
				<tr><td><?php _e('Reservation ID','foodpress');?>: </td><td><?php  echo '#'.$reservation_id;?></td></tr>
				<tr><td><?php _e('Date','foodpress');?>: </td>
				<td><input type='text' id='fp_res_date' class='res_date' name='date' value='<?php  echo fp_menumeta($rmeta, 'date');?>'/></td></tr>

				<tr><td><?php _e('Time','foodpress');?>: </td>

				<td class='step' data-format='<?php echo (!empty($opt6['fpr_24hr']) &&  $opt6['fpr_24hr']=='yes')? '24':'12';?>' data-restrict='<?php echo (!empty($opt6['fpr_timesl']) &&  $opt6['fpr_timesl']=='yes')? 'yes':'no';?>' data-start='<?php echo (!empty($opt6['fpr_start_time']) )? $opt6['fpr_start_time']:'-';?>' data-end='<?php echo (!empty($opt6['fpr_end_time']) )? $opt6['fpr_end_time']:'-';?>'>
				<input type='text' class='fp_res_input_icon_clock' name='time' value='<?php  echo fp_menumeta($rmeta, 'time');?>'/> <?php if(!empty($opt6['fpr_startend']) && $opt6['fpr_startend']=='yes'):?><input type='text' class='fp_res_input_icon_clock' name='end_time' value='<?php echo (!empty($rmeta['end_time']))? fp_menumeta($rmeta, 'end_time'): null; ?>'/><?php endif;?></td></tr>

				<tr><td><?php _e('Primary Contact','foodpress');?>: </td>
				<td><input type='text' name='name' value='<?php  echo fp_menumeta($rmeta, 'name').' '.fp_menumeta($rmeta, 'first_name').' '.fp_menumeta($rmeta, 'last_name');?>'/></td></tr>

				<?php if(!empty($opt6['fpr_phonenumber']) && $opt6['fpr_phonenumber']=='yes'):?>
					<tr><td><?php _e('Phone Number','foodpress');?>: </td>
					<td><input type='text' name='phone' value='<?php  echo fp_menumeta($rmeta, 'phone');?>'/></td></tr>
				<?php endif;?>

				<?php 
				// restaurant Location
				if(!empty($opt6['fpr_location']) && $opt6['fpr_location']=='yes'):?>
					<tr><td><?php _e('Restaurant Location','foodpress');?>: </td>
					<td><input type='text' name='location' value='<?php  echo fp_menumeta($rmeta, 'location');?>'/></td></tr>
				<?php endif;?>

				<tr><td><?php _e('Reservation Count','foodpress');?>: </td>
				<td><input type='text' name='people' value='<?php  echo fp_menumeta($rmeta, 'people');?>'/></td></tr>

				<?php
					$email = (!empty($rmeta['email']))? $rmeta['email'][0]:
						( (!empty($rmeta['email_address']))? $rmeta['email_address'][0]:'');
				?>
				<tr><td><?php _e('Email','foodpress');?>: </td>
				<td><input type='text' name='email' value='<?php  echo $email;?>'/></td></tr>

				<?php if(!empty($rmeta['phone_number'])):?>
					<tr><td><?php _e('Phone','foodpress');?>: </td>
					<td><?php  echo fp_menumeta($rmeta, 'phone_number');?></td></tr>
				<?php endif;?>


				<?php
					// additional fields
					// for additional fields
					
				    for($x=1; $x<=foodpress_get_reservation_form_fields(); $x++){
				    	if( !empty($opt6['fp_af_'.$x]) && $opt6['fp_af_'.$x]=='yes' && !empty($opt6['fp_ec_f'.$x]) ){

				    		$field_type = (!empty($opt6['fp_ec_fb'.$x])? $opt6['fp_ec_fb'.$x]:'text');
			    			$meta_value = !empty($rmeta['fp_af_'.$x])? $rmeta['fp_af_'.$x][0]: null;
			    			echo "<tr class='custom_field'><td>". $opt6['fp_ec_f'.$x]."</td><td>";					    			
			    			switch($field_type){
			    				case 'text':
			    					echo "<input style='width:100%' type='text' name='".'fp_af_'.$x."' value='".$meta_value."'/>";
			    				break;
			    				case 'select':
									$values = $opt6['fp_ec_fv'.$x];
									if(!empty($values)){ // if field values present
										//$values = str_replace(' ', '', $values);
										$vals = explode(',', $values);
										echo "<select class='resinput' name='".'fp_af_'.$x."'>";
										foreach($vals as $values){
											$values = ltrim($values);
											$selected = ($meta_value == $values)? "selected='selected'": null;
											echo "<option {$selected} value='{$values}'>{$values}</option>";
										}
										echo "</select>";
									}
									break;
								case 'checkbox':
									$checked = ($meta_value=='yes')? 'checked="checked"':null;
									echo "<input class='resinput check' name='".'fp_af_'.$x."' type='checkbox' {$checked}/>";
									break;
								case 'multiline':
									
									echo "<textarea style='width:100%' class='resinput' name='".'fp_af_'.$x."'>{$meta_value}</textarea>";
									break;
			    			}
			    			echo "</td></tr>";
						}
					}
				?>
				<tr><td><?php _e('Status','foodpress');?>: </td>
				<?php
					$o_status = (!empty($__sta) && $__sta=='checked')? 'checked': 'check-in';
				?>
				<td class='reservation_status <?php echo $o_status?>'><p data-res_id='<?php echo $post->ID;?>' data-status='<?php echo $o_status;?>'><?php  echo $foodpress->reservations->get_checkin_status($__sta);	;?></p></td></tr>
			</table>
			</div>
		</div>
		</div>

		<?php

		echo ob_get_clean();

	}

	function save_meta_data($post_id, $post){
		global $pagenow, $foodpress, $post;

		$reservation_id = $post_id;

		if ( empty( $reservation_id ) || empty( $post ) ) return;
		if($post->post_type!='reservation') return;
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		// Prevent quick edit from clearing custom fields
		if (defined('DOING_AJAX') && DOING_AJAX) return;
		if ( is_int( wp_is_post_revision( $post ) ) ) return;
		if ( is_int( wp_is_post_autosave( $post ) ) ) return;
		
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if( isset($_POST['fp_noncename']) ){
			if ( !wp_verify_nonce( $_POST['fp_noncename'], plugin_basename( __FILE__ ) ) ){
				return;
			}
		}

		$opt6 = get_option('fp_options_food_6');


		$meta_fields = array(
			'date', 'time', 'end_time', 'name', 'people','email','phone'
		);
		// run through all the custom meta fields
		foreach($meta_fields as $fld){	
			$this->fp_individual_post_values($fld, $reservation_id);
		}

		// additional fields
		for($x=1; $x<= foodpress_get_reservation_form_fields(); $x++){
			$field_type = (!empty($opt6['fp_ec_fb'.$x])? $opt6['fp_ec_fb'.$x]:'text');

			if($field_type=='checkbox'){
				$val = isset($_POST['fp_af_'.$x])?'yes':'no';
				$this->fp_individual_post_values('fp_af_'.$x, $reservation_id, $val);
			}else{
				$this->fp_individual_post_values('fp_af_'.$x, $reservation_id);
			}
		}

		// set post title

	
		
	}

	// process saving or deleting post meta values
		function fp_individual_post_values($var, $post_id, $value=''){
			if(!empty($value)){
				update_post_meta( $post_id, $var,$value);
			}elseif(!empty ($_POST[$var])){					
				$post_value = ( $_POST[$var]);
				update_post_meta( $post_id, $var,$post_value);
							
			}else{
				if(defined('DOING_AUTOSAVE') && !DOING_AUTOSAVE){
					// if the meta value is set to empty, then delete that meta value
					delete_post_meta($post_id, $var);
				}
				delete_post_meta($post_id, $var);
			}
		}
}
endif;
new foodpress_reservation_metaboxes();
	

	

?>