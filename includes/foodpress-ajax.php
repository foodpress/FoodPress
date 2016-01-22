<?php
/**
 * foodpress Ajax Handlers
 *
 * Handles AJAX requests via wp_ajax hook (both admin and front-end events)
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	foodpress/Functions/AJAX
 * @version     0.1
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class foodpress_ajax{
	public function __construct(){
		$ajax_events = array(
			'fp_ajax_set_res'=>'foodpress_get_reservations',
			'fp_ajax_delete_res'=>'foodpress_delete_reservation',
			'the_ajax_res01x'=>'foodpress_res01x',
			'fp_ajax_content'=>'foodpress_get_menu_item',
			'fp_dynamic_css'=>'foodpress_dymanic_css',
			'foodpress_verify_lic'=>'foodpress_license_verification',
			'fp_ajax_popup'=>'add_new_reservation',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {			

			add_action( 'wp_ajax_'. $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'. $ajax_event, array( $this, $class ) );
		}
	}

	// GET list of reservations for settings page
		function foodpress_get_reservations(){
			global $foodpress;
			$status=0;

			ob_start();

			echo "<div class='fp_res_list'>";

			$return = $foodpress->reservations->get_rsvp_list($_POST['type']) ;

			echo ($return)? $return: "<p>No reservations found.</p>";
			echo "</div>";


			$content = ob_get_clean();

			$return = array(
				'status'=>$status,
				'content'=>$content
			);
			
			echo json_encode($return);
			exit;
		}

	// delete a reservation from the list
		function foodpress_delete_reservation(){
			global $foodpress;
			$status=0;

			$status = $foodpress->reservations->delete_reservation($_POST['rid']) ;
			$return = array(
				'status'=>$status,
			);
			
			echo json_encode($return);
			exit;
		}

	// check-in reservations
		function foodpress_res01x(){
			global $foodpress;

			$res_id = $_POST['res_id'];
			$status = $_POST['status'];

			update_post_meta($res_id, 'status',$status);

			$return_content = array(
				'new_status_lang'=>$foodpress->reservations->get_checkin_status($status),
			);
			
			echo json_encode($return_content);		
			exit;
		}

	// GET menu item details for the popup
		function foodpress_get_menu_item(){
			global $foodpress;
			
			$item_id = (int)($_POST['menuitem_id']);
			
			$content = $foodpress->foodpress_menus->get_detailed_menu_item_content($item_id, '',$_POST['args']);
			
			//$popup_frame = $foodpress->foodpress_menus->get_popup_info_html();
			
			$return = array(
				//'popupframe'=>$popup_frame,
				'content'=>$content
			);
			
			echo json_encode($return);
			exit;
		}

	/* dynamic styles */
		function foodpress_dymanic_css(){
			//global $foodpress_menus;
			require('admin/inline-styles.php');
			exit;
		}

	// Verify foodpress Licenses AJAX function
		function foodpress_license_verification(){
			global $foodpress;	
			$new_license_content= '';
			$error_msg='00';
			
			$license_errors = array( 
				'01'=>"No data returned from envato API",
				"02"=>'Your license is not a valid one!, please check and try again.',
				"03"=>'envato verification API is busy at moment, please try later.',
				"00"=>'Could not verify the License key. Please try again.'
			);			
			
			$key = $_POST['key'];
			$slug = $_POST['slug'];			

			// validate correct license format
			$validated =$foodpress->fp_updater->purchase_key_format($key);
			
			// verify license from foodpress server
			$status = $foodpress->fp_updater->_verify_license_key($slug, $key);
						
			if($status || $validated){
				$save_license_date = $foodpress->fp_updater->save_license_key($slug, $key);				
						
				// successfully saved new verified license
				if($save_license_date!=false){
					$status = 'success';
					
					$new_license_content ="
					<h2>{$save_license_date['name']}</h2>
					<p>Version: {$save_license_date['current_version']}</p>
					<p>Type: {$save_license_date['type']}</p>
					<p class='license_key'>{$save_license_date['key']}</p>";
				}else{
					$status='error';
				}
			}else{					
				$error_msg = $license_errors['00'];
				$status='error';				
			}
			
			
			$return_content = array(
				'status'=>$status,		
				'new_content'=>$new_license_content,
				'error_msg'=>$error_msg
			);
			echo json_encode($return_content);		
			exit;
			
		}

	// save new reservation
		function add_new_reservation() {
			
			$status = 0;

			// Reservation Post Meta Information
			foreach($_POST as $key=>$val){
				if(is_array($val)) continue;
				$post[$key]= sanitize_text_field(urldecode($val));
			}
			
		    $date = $post['date'];
		    $people = $post['party'];
		    $name = !empty($post['name'])? $post['name']:null;
		    $email = !empty($post['email'])? $post['email']:null;	    
		    $phone = !empty($post['phone'])? $post['phone']:null;	    
		    $location = !empty($post['location'])? $post['location']:null;	    
		    $time = $post['time'];	    
		   	
		   	// arguments for reservation form
		   	$sc_args = '';
		   	if(!empty($_POST['args'])){
		   		$sc_args = $_POST['args'];
		   	}  

		   	//print_r($sc_args);
		    
		    $opt6 = get_option('fp_options_food_6');

		    // status of the reservation based on admin approval settings
		    $poststatus = (!empty($opt6['fpr_draft']) && $opt6['fpr_draft']=='yes')? 'draft':'publish'; 

		    // end time field
		    $endTime = (!empty($post['end_time']))? $post['end_time']: null;

		    // custom title
		    $title = $name.' - Date: '. $date . " - Time: " . $post['time'] . " - People: " . $people;

		    // reservation post 
		    $post = array(
		        'post_title'    => $title,
		        'post_status'   => $poststatus,
		        'post_type' => 'reservation'
		    );
		    
		    // Insert post and update meta
		    $id = wp_insert_post( $post );
		    if(!empty($id)){
		    	update_post_meta($id, 'date', $date, true);
			    update_post_meta($id, 'time', $time, true);

			    //update_post_meta($id, 'aa_end_time', $_POST['end_time']);

			    if(!empty($endTime))
			    	update_post_meta($id, 'end_time', $endTime);

			    update_post_meta($id, 'people', $people, true);
			    update_post_meta($id, 'location', $location, true);
			    update_post_meta($id, 'name', $name, true);
			    update_post_meta($id, 'email', $email, true);

			    if(!empty($phone))
			    	update_post_meta($id, 'phone', $phone, true);

			    update_post_meta($id, 'lang', (!empty($sc_args['lang'])? $sc_args['lang']:'L1'));
			    
			    // for additional fields
			    for($x=1; $x<=foodpress_get_reservation_form_fields(); $x++){
					// check if fields are good					
					if( !empty($opt6['fp_af_'.$x]) && $opt6['fp_af_'.$x]=='yes' && !empty($opt6['fp_ec_f'.$x]) ){
						add_post_meta($id, 'fp_af_'.$x, sanitize_text_field(urldecode($_POST['fp_af_'.$x]) ) );
					}
				}

			    // send confirmation emails
		    	global $foodpress;
		    	$foodpress->reservations->successful_reservation_emails($id, $sc_args);
		    }else{
		    	$status = 01;
		    }
		    

		    $return_content = array(
				'status'=>$status,
				'reservation_id'=>$id
			);
			
			echo json_encode($return_content);		
			exit;
		
		}
}
new foodpress_ajax();


/** Feature a menu item from admin */
	function foodpress_feature_menuitem() {

		if ( ! is_admin() ) die;

		//if ( ! current_user_can('edit_products') ) wp_die( __( 'You do not have sufficient permissions to access this page.', 'foodpress' ) );

		if ( ! check_admin_referer('foodpress-feature-menuitem')) wp_die( __( 'You have taken too long. Please go back and retry.', 'foodpress' ) );

		$post_id = isset( $_GET['menu_id'] ) && (int) $_GET['menu_id'] ? (int) $_GET['menu_id'] : '';

		if (!$post_id) die;

		$post = get_post($post_id);

		if ( ! $post || $post->post_type !== 'menu' ) die;

		$featured = get_post_meta( $post->ID, '_featured', true );

		if ( $featured == 'yes' )
			update_post_meta($post->ID, '_featured', 'no');
		else
			update_post_meta($post->ID, '_featured', 'yes');

		wp_safe_redirect( remove_query_arg( array('trashed', 'untrashed', 'deleted', 'ids'), wp_get_referer() ) );
	}
	add_action('wp_ajax_foodpress-feature-menuitem', 'foodpress_feature_menuitem');



?>