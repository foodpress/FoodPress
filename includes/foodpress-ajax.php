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
			'fp_ajax_popup'=>'add_new_reservation',
			'fp_validate_license'=>'validate_license',
			'foodpress_verify_lic'=>'foodpress_license_verification',
			'fp_remote_validity'=>'remote_validity',
			'fp_deactivate_license'=>'deactivate_license',
		);
		foreach ( $ajax_events as $ajax_event => $class ) {
			add_action( 'wp_ajax_'. $ajax_event, array( $this, $class ) );
			add_action( 'wp_ajax_nopriv_'. $ajax_event, array( $this, $class ) );
		}

		add_action('wp_ajax_foodpress-feature-menuitem', array($this,'foodpress_feature_menuitem'));

	}

	/** Feature a menu item from admin */
		public function foodpress_feature_menuitem() {

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
		function foodpress_dymanic_css() {
			//global $foodpress_menus;
			require('admin/inline-styles.php');
			exit;
		}

	// Activation of FoodPress product
		// validate the license key
			function validate_license() {
				global $foodpress;

				$key = $_POST['key'];
				$verifyformat = $foodpress->admin->product->purchase_key_format($key);

				$return_content = array(
					'status'=>($verifyformat?'good':'bad'),
					'error_msg'=>(!$verifyformat? $foodpress->admin->product->error_code_('10'):''),
				);
				echo json_encode($return_content);
				exit;
			}
	// Verify foodpress Licenses AJAX function
		function foodpress_license_verification() {
			global $foodpress;

			$debug = $content = $addition_msg ='';
			$status = 'success';
			$error_code = '11';
			$error_msg='';

			// Passing Data
			$key = $_POST['key'];
			$slug = $_POST['slug'];
			$__passing_instance = (!empty($_POST['instance'])?(int)$_POST['instance']:'1');
			$__data = array(
				'slug' => addslashes($_POST['slug']),
				'key' => addslashes( str_replace(' ','',$_POST['key']) ),
				'email' => (!empty($_POST['email'])? $_POST['email']: null),
				'product_id' => (!empty($_POST['product_id'])?$_POST['product_id']:''),
				'instance' => $__passing_instance
			);

			// verify license from foodpress server
			$json_content = $foodpress->admin->product->verify_product_license($__data);
			//var_dump($json_content);

			if($json_content['envatoVerified'] || $json_content['fpVerified']) {
				$__save_new_lic = $foodpress->admin->product->save_license_key(
					$__data['slug'],
					$__data['key']
				);
				$status = 'success';
			} else {
				$error_code = (isset($json_content['error_code'])) ? $json_content['error_code'] : '11';
				$status = 'error';
			}

			$content = $status; // url to envato json API

			$return_content = array(
				'status' => $status,
				'success_msg' => '',
				'error_msg' => ($status == 'success') ? '' : $foodpress->admin->product->error_code_($error_code),
				'addition_msg' => $addition_msg,
				'json_url' => (!is_array($json_content) ? $json_content : 'data')
			);

			if($status == 'success') {
				$return_content['success_msg'] = $json_content['result'];
			}

			echo json_encode($return_content);
			exit;
		}
		// update remote validity status of a license
			function remote_validity(){
				global $foodpress;

				$status = $foodpress->admin->product->update_field($_POST['slug'], 'remote_validity', $_POST['remote_validity']);
				$return_content = array(	'status'=>($status?'good':'bad')	);
				echo json_encode($return_content);
				exit;
			}
		// deactivate license
			function deactivate_license(){
				global $foodpress;

				// deactivate license locally
					$foodpress->admin->product->deactivate($_POST['slug']);

				echo json_encode( array(
					'status'=>'good'
				));
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
				'reservation_id'=>$id,
				'i18n_date'=>date_i18n( get_option( 'date_format'), strtotime($date))
			);

			echo json_encode($return_content);
			exit;

		}
}

new foodpress_ajax();
?>
