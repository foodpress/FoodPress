<?php
/**
 * Foodpress Admin Functions
 *
 * Hooked-in functions for foodpress related menu items in admin.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin
 * @version     1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
function foodpress_prevent_admin_access() {
	if ( get_option('foodpress_lock_down_admin') == 'yes' && ! is_ajax() && ! ( current_user_can('edit_posts') || current_user_can('manage_foodpress') ) ) {
		//wp_safe_redirect(get_permalink(woocommerce_get_page_id('myaccount')));
		exit;
	}
}


/*	Dynamic styles generation */
	function foodpress_generate_options_css($newdata='') {
	 
		/** Define some vars **/
		$data = $newdata; 
		$uploads = wp_upload_dir();
		
		//$css_dir = get_template_directory() . '/css/'; // Shorten code, save 1 call
		//$css_dir = FP_DIR . '/'. FP_BASE.  '/assets/css/'; // Shorten code, save 1 call
		$css_dir = FP_PATH.  '/assets/css/'; 
		
		/** Save on different directory if on multisite **/
		if(is_multisite()) {
			$aq_uploads_dir = trailingslashit($uploads['basedir']);
		} else {
			$aq_uploads_dir = $css_dir;
		}
		
		/** Capture CSS output **/
		ob_start();
		require($css_dir . 'dynamic_styles.php');
		$css = ob_get_clean();

		//print_r($css);
		
		/** Write to options.css file **/
		WP_Filesystem();
		global $wp_filesystem;
		if ( ! $wp_filesystem->put_contents( $aq_uploads_dir . 'foodpress_dynamic_styles.css', $css, 0777) ) {
		    return true;
		}
		
	}


/** Add a SHORTCODE BUTTON to the WP editor. */
	add_action('media_buttons_context',  'foodpress_add_shortcode_button');
	function foodpress_add_shortcode_button($context) {
		global $pagenow, $typenow, $wpdb, $post;	
		
		
		if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
//			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $post = get_post( $_GET['post'] );
	        $typenow = $post->post_type;
	    }
		
		if ( $typenow =='' || $typenow == "menu" ) return;		
		
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) return;
		
//		//our popup's title
//	  	$text = '[ ]';
//	  	$title = 'foodpress Shortcode Generator';

		//append the icon
//	  	$context .= "<a id='fp_shortcode_btn' class='fp_popup_trig fp_admin_btn btn_prime' title='{$title}' href=''>{$text}</a>";

	  	foodpress_shortcode_pop_content();

	  	return $context;

	}



// foodpress shortcode generator button for WYSIWYG editor 
	 add_action('admin_init', 'foodpress_shortcode_button_initiat');
	 function foodpress_shortcode_button_initiat() {

	 	global $pagenow, $typenow, $post;

	 	if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
//			$typenow = $post->post_type;
		} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
	        $post = get_post( $_GET['post'] );
	        $typenow = (!empty($post) )? $post->post_type : '';
	    }
		
		if ( $typenow == '' || $typenow == "menu" ) return;
		

	      //Abort early if the user will never see TinyMCE
	      if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
	           return;

	      //Add a callback to regiser our tinymce plugin   
	      add_filter("mce_external_plugins", "foodpress_register_tinymce_plugin"); 

	      // Add a callback to add our button to the TinyMCE toolbar
	      add_filter('mce_buttons', 'foodpress_add_tinymce_button');


	}


	//This callback registers our plug-in
	function foodpress_register_tinymce_plugin($plugin_array) {
	    $plugin_array['foodpress_shortcode_button'] = FP_URL.'/assets/js/admin/shortcode.js';
	    return $plugin_array;
	}

	//This callback adds our button to the toolbar
	function foodpress_add_tinymce_button($buttons) {
	            //Add the button ID to the $button array
	    $buttons[] = "foodpress_shortcode_button";
	    return $buttons;
	}


/** Short code popup content */
	function foodpress_shortcode_pop_content(){
		global $foodpress, $fp_shortcode_box;	
		$content='';

		require_once(FP_PATH.'/includes/class-shortcode_box_generator.php');
		
		$content = $fp_shortcode_box->get_content();
		
		echo $foodpress->output_foodpress_pop_window(array(
				'content'=>$content,
				'class'=>'foodpress_shortcode',
				'attr'=>'clear="false"',
				'title'=>'Shortcode Generator'
		));
	}

/** Force TinyMCE to refresh. */
	function foodpress_refresh_mce( $ver ) {
		$ver += 3;
		return $ver;
	}

	add_filter( 'tiny_mce_version', 'foodpress_refresh_mce' );



// SAVE: closed meta field boxes
	function foodpress_save_collapse_metaboxes( $page, $post_value) {
	    
		if(empty($post_value)){
			
			$user_id = get_current_user_id();
			$option_name = 'closedmetaboxes_' . $page; 
			$opts = get_user_option( $option_name, $user_id );

			if(!empty($opts)){

				delete_user_option($user_id, $option_name, true);
			}

			return;
		}else{
			
			$user_id = get_current_user_id();
			$option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
			
			$meta_box_ids = array_unique(array_filter(explode(',',$post_value)));
			
			$meta_box_id_ar =serialize($meta_box_ids);
			
			update_user_option( $user_id, $option_name,  $meta_box_id_ar , true );
		}
		
	}


	function foodpress_get_collapsed_metaboxes($page){
		
		$user_id = get_current_user_id();
	    $option_name = 'closedmetaboxes_' . $page; // use the "pagehook" ID
		$option_arr = get_user_option( $option_name, $user_id );
		
		if(empty($option_arr)) return;
		
		return unserialize($option_arr);
		//return ($option_arr);

	}


// OUT PUT HTML Codes
function foodpress_io_yn($var='', $afterstatement='', $codevar=''){
	// var == N
	return "<span class='fp_yn_btn".( ($var=='no')? ' NO':null)."' ".( (!empty($afterstatement))? "afterstatement='".$afterstatement."'":null )." ".( (!empty($codevar))? "data-codevar='".$codevar."'":null )."><span class='btn_inner'><span class='catchHandle'></span></span></span>";
}

/* Load font popup box */
// @version 1.2
// used in menu tax edit page
	function foodpress_admin_load_font_icons_box(){
		wp_enqueue_script( 'fp_font_icons_script',FP_URL.'/assets/js/admin/font_icon_box_script.js');
		wp_enqueue_style( 'fp_font_icons_styles',FP_URL.'/assets/css/admin/font_icon_box_styles.css');

		$rightside = '';
		// font awesome
		require_once(FP_PATH.'/includes/admin/fa_fonts.php');

		$rightside.= "<div style='display:none' class='fa_icons_selection'><div class='fai_in'><ul class='faicon_ul text'>";
		foreach($font_ as $fa){
			$rightside.= "<li><i title='{$fa}' data-name='".$fa."' class='fa ".$fa."'></i></li>";
		}

		$rightside.= "</ul>";
		$rightside.= "</div></div>";

		return $rightside;
	}

// FoodPress addons list
	function foodpress_addon_list(){
		return  array(
			'foodpress-onlineorder' => array(
				'name'=>'Online Order',
				'link'=>'http://myfoodpress.com/addons/online-ordering/',
				'download'=>'http://myfoodpress.com/addons/online-ordering/',
				'icon'=>'assets/images/icons/icon_oo.jpg',
				'iconty'=>'local',
				'desc'=>'Online Ordering for foodPress seamlessly integrate woocommerce into our foodpress menu items allowing your customers to order menu items online.',
			),'foodpress-single-menu' => array(
				'name'=>'Single Menu',
				'link'=>'http://myfoodpress.com/addons/single-menu-item/',
				'download'=>'http://myfoodpress.com/addons/single-menu-item/',
				'icon'=>'assets/images/icons/icon_sm.jpg',
				'iconty'=>'local',
				'desc'=>'Want to add social share buttons and share direct link to individual menu items from your delicious menu? Single Menu Items is the perfect addon that will allow you to do that.',
			),'foodpress-importexport' => array(
				'name'=>'Import Export',
				'link'=>'http://myfoodpress.com/addons/import-export-menu-items/',
				'download'=>'http://myfoodpress.com/addons/import-export-menu-items/',
				'icon'=>'assets/images/icons/icon_ie.jpg',
				'iconty'=>'local',
				'desc'=>'Import and export foodpress menu items easily using this addon in CSV format',
			)
		);

	}


?>