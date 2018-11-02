<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * foodpress Admin.
 *
 * @class 		fp_Admin 
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin
 * @version     1.1.5
 */
class fp_Admin {

	// constructor
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditonal_includes' ) );
		//add_action( 'admin_init', array( $this, 'prevent_admin_access' ) );
		
		add_action( 'admin_init', array( $this, 'foodpress_admin_init' ) );
		//add_action( 'admin_footer', 'wc_print_js', 25 );

		add_action( 'admin_enqueue_scripts', array( $this,'foodpress_all_backend_files' ));
		add_action( 'admin_enqueue_scripts', array( $this,'foodpress_admin_scripts' ));

		// links into plugins page in wp-admin
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

		// dashboard widget
		add_action('wp_dashboard_setup', array($this,'add_dashboard_widgets' ));
	}

	/* INCLUDES */
		public function includes() {
			// Functions
			include_once( 'fp-admin-functions.php' );
			include_once( 'class-product.php' );
			$this->product = new FP_product();

			// Classes
			include_once( 'post_types/class-fp-admin-post-types.php' );
			
			// Classes we only need if the ajax is not-ajax
			if ( ! defined( 'DOING_AJAX' ) ) {
				include( 'class-fp-admin-menu.php' );				
				include( 'welcome.php' );
			}

			// Taxonomies
			include_once( 'fp-admin-taxonomies.php' );

		}

	/* conditonal_includes */
		function conditonal_includes(){
			$screen = get_current_screen();
			global $menu, $foodpress, $pagenow, $typenow;
			
			if ( $screen->post_type == "menu" ) {
				// includes
				if( $pagenow == 'post-new.php' || $pagenow == 'post.php' || $pagenow == 'edit.php' ) {
					include_once( 'post_types/class-menu_meta_boxes.php' );
					include_once( 'post_types/class-fp-admin-cpt-menu.php' );
				}
			}
			if ( $screen->post_type == "reservation" ) {
				include_once( 'post_types/reservation_meta_boxes.php' );
			}
		}
	
	/* Initiate admin */
		function foodpress_admin_init() {
			global $pagenow, $typenow, $wpdb, $foodpress;	

			// updates
			$this->verify_plugin_version();
				
			if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
//				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $post = get_post( $_GET['post'] );
		        $typenow = $post->post_type;
		    }
			
			if ( $typenow == '' || $typenow == "menu"|| $typenow == "reservation" ) {
				// filter event post permalink edit options
				if(!defined('FP_SIN_MI')){
					$this->foodpress_perma_filter();
				}					
			}
			
			// install or update foodpress
				$_foodpress_install = get_option('_foodpress_install');
				if(empty($_foodpress_install) ){
					include_once( 'foodpress-admin-install.php' );
					$value = array('installed', $foodpress->version);

					update_option('_foodpress_install',$value);
				}

			// All FoodPress admin pages
				$this->wp_admin_scripts_styles();
		}

		
		// if events single page hide permalink and preview changes that links to single event post page -- which doesnt have supported template without foodpress single event addon
			function foodpress_perma_filter(){
				add_action('admin_print_styles', array( $this,'foodpress_remove_mipost_previewbtn'));
				add_filter('get_sample_permalink_html', array( $this,'foodpress_perm'), 10,4);
			}
			function foodpress_perm($return, $id, $new_title, $new_slug){
				$ret2 = preg_replace('/<span id="edit-slug-buttons">.*<\/span>|<span id=\'view-post-btn\'>.*<\/span>/i', '', $return);
				return $ret2 ='';	
			}
			function foodpress_remove_mipost_previewbtn() {
				?>
				<style>#edit-slug-box, #preview-action{ display:none; }</style>
				<?php
			}

	// 
		function wp_admin_scripts_styles(){
			global $foodpress, $pagenow;

			if( (!empty($pagenow) && $pagenow=='admin.php')
			 && (isset($_GET['page']) && ($_GET['page']=='foodpress') ) 
			){

				// only licenses page
			 	if(!empty($_GET['tab']) && $_GET['tab']=='food_5'){
			 		wp_enqueue_script('foodpress_licenses',$foodpress->assets_path. 'js/admin/settings_addons_licenses.js',array('jquery'),$foodpress->version,true);
			 	}
			 }
		}

	/** Plugin version for updates **/
		public function verify_plugin_version(){
			global $foodpress;

			$plugin_version = $foodpress->version;
				
			// check installed version
			$installed_version = get_option('foodpress_plugin_version');
			
			if($installed_version != $plugin_version){
				update_option('foodpress_plugin_version', $plugin_version);
				wp_safe_redirect( admin_url( 'index.php?page=fp-about&fp-updated=true' ) );
				
			}else if(!$installed_version ){
				add_option('foodpress_plugin_version', $plugin_version);			
			}else{
				update_option('foodpress_plugin_version', $plugin_version);			
			}
			
			// delete options saved on previous version
			delete_option('fp_plugin_version');
		}

	// RESERVATIONS 
	// dashboard widget
		function add_dashboard_widgets(){
			wp_add_dashboard_widget('fp_res_dashboard_widget', __('Upcoming Reservations','foodpress'), array($this,'dashboard_widget_function'));
		}
		function dashboard_widget_function( $post, $callback_args ) {
			echo $this->reservation_UI();
		}
		public function reservation_UI(){
			ob_start();
			global $foodpress;
			$counts = $foodpress->reservations->get_rsvp_count();
			?>				
				<div class='fpr_sections'>
					<?php if(!empty($counts['pending']) && $counts['pending']>0):?>
					<div class='fpr_box fpr_upcoming pending'>
						<span class="fpr_title"><?php _e('Unconfirmed <br>Reservations','foodpress');?></span>
						<span class="fpr_count"><?php echo $counts['pending'];?></span>
					</div>
					<div class='clear'></div>
					<?php endif;?>

					<div class='fpr_box fpr_upcoming'>
						<span class="fpr_title"><?php _e('Upcoming <br>Reservations','foodpress');?></span>
						<span class="fpr_count"><?php echo $counts['upcoming'];?></span>
						<span class="fpr_view_res fp_popup_trig " data-type='upcoming'><?php _e('View Reservations','foodpress');?></span>
					</div>
					<div class='clear'></div>
					<div class='fpr_box fpr_past'>
						<span class="fpr_title"><?php _e('Past <br>Reservations','foodpress');?></span>
						<span class="fpr_count"><?php echo $counts['past'];?></span>
						<span class="fpr_view_res fp_popup_trig "  data-type='past'><?php _e('View Reservations','foodpress');?></span>
					</div>
					<div class='clear'></div>

					<a class='btn_tritiary fp_admin_btn ' style='margin-top:20px'href='<?php echo get_admin_url();?>edit.php?post_type=reservation'><?php _e('View all reservations','foodpress');?></a><br/>

					<a class='btn_tritiary fp_admin_btn ' style='margin-top:10px'href='<?php echo get_admin_url();?>post-new.php?post_type=reservation'><?php _e('Manually submit reservation','foodpress');?></a>
				</div>
				
			<?php 

			$foodpress->output_foodpress_pop_window(array('content'=>__('Loading...','foodpress'), 'title'=>__('Reservation Information','foodpress'), 'class'=>'fp_set_res','type'=>'padded'));

			return ob_get_clean();
		}


	/** * action_links function.	 */
		function action_links( $links ) {

			$plugin_links = array(
				'<a href="' . admin_url( 'admin.php?page=foodpress' ) . '">' . __( 'Settings', 'foodpress' ) . '</a>',
				'<a href="http://myfoodpress.com/documents/">' . __( 'Docs', 'foodpress' ) . '</a>',
				'<a href="https://foodpressplugin.freshdesk.com/support/home/">' . __( 'Support', 'foodpress' ) . '</a>',
			);

			return array_merge( $plugin_links, $links );
		}
	
	/** Include admin scripts and styles.	 ONLY on foodpress settings page*/
		function foodpress_admin_scripts() {
			global $foodpress, $pagenow, $typenow;

			if ( $typenow == 'post' && ! empty( $_GET['post'] ) ) {
//				$typenow = $post->post_type;
			} elseif ( empty( $typenow ) && ! empty( $_GET['post'] ) ) {
		        $post = get_post( $_GET['post'] );
		        $typenow = $post->post_type;
		    }
			
			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.10.4';

			// ONLY menu and reservations page
			if ( $typenow == '' || $typenow == "menu" || $typenow == "reservation" ) {
				wp_enqueue_style( 'backend_food_post',FP_URL.'/assets/css/admin/backend_post.css');
				wp_enqueue_script('food_backend_post',FP_URL.'/assets/js/admin/backend_post.js', array('jquery','jquery-ui-core','jquery-ui-datepicker'), 1.0, true );
				
				wp_localize_script( 'food_backend_post', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

				do_action('foodpress_load_admin_post_script');
			}

			// ONLY for reservations
			if ( $typenow == '' || $typenow == "reservation" ) {
				wp_enqueue_style("fp_res_jquery_ui_style", "//ajax.googleapis.com/ajax/libs/jqueryui/{$jquery_version}/themes/smoothness/jquery-ui.min.css");
				
				wp_register_style('fp_res_timepicker_style',FP_URL.'/assets/css/jquery.timepicker.css');
				wp_enqueue_style( 'fp_res_timepicker_style' );
				wp_enqueue_style( 'fp_res_jquery_ui_style' );

				wp_enqueue_script('food_backend_post',FP_URL.'/assets/js/backend_reservation_post.js', array('jquery','jquery-ui-core','jquery-ui-datepicker'), 1.0, true );
				
				wp_register_script('fp_reservation_timepicker',FP_URL.'/assets/js/jquery.timepicker.js' ,array('jquery', 'jquery-ui-core','jquery-ui-datepicker'),'1.0', true);
				wp_enqueue_script('fp_reservation_timepicker');	
							
				do_action('foodpress_load_resadmin_post_script');

			}
			
			// foodPress Settings page only		
				if($pagenow=='admin.php' && $_GET['page']=='foodpress'){
					wp_enqueue_script('food_backend_all',FP_URL.'/assets/js/admin/all_backend.js',array('jquery'),1.0,true);
					wp_enqueue_script('food_settings',FP_URL.'/assets/js/admin/settings.js',array('jquery'),1.0,true);

					// timepicker
					wp_enqueue_script('food_timepicker',FP_URL.'/assets/js/jquery.timepicker.js',array('jquery'),1.0,true);
					wp_enqueue_style('fp_res_timepicker_style',FP_URL.'/assets/css/jquery.timepicker.css');

					wp_enqueue_style( 'backend_settings',FP_URL.'/assets/css/admin/backend_settings.css');

					wp_enqueue_script('fp_reservation_timepicker',FP_URL.'/assets/js/jquery.timepicker.js' ,array('jquery', 'jquery-ui-core','jquery-ui-datepicker'),$foodpress->version, true);
					wp_enqueue_style('fp_res_timepicker_style',FP_URL.'/assets/css/jquery.timepicker.css');

					wp_localize_script( 
						'food_settings', 
						'food_settings', 
						array( 
							'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
							'postnonce' => wp_create_nonce( 'reservation_settings_nonce' )
						)
					);
					
					wp_localize_script( 'food_backend_all', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
					
					// LOAD thickbox
					if(isset($_GET['tab']) && $_GET['tab']=='food_5'){
						wp_enqueue_script('thickbox');
						wp_enqueue_style('thickbox');
					}
					$foodpress->enqueue_backender_styles();
					$foodpress->register_backender_scripts();
					
					do_action('foodpress_admin_scripts');
				}

			// all over wp-admin
				if(is_admin()){
					wp_enqueue_script('fp_reservations',FP_URL.'/assets/js/admin/backend_reservations.js',array('jquery'),1.0,true);
					wp_localize_script( 
						'fp_reservations', 
						'fp_reservations', 
						array( 
							'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
							'postnonce' => wp_create_nonce( 'reservation_settings_nonce' )
						)
					);
					wp_enqueue_style( 'reservation_styles',FP_URL.'/assets/css/admin/reservations.css');
				}

			if($pagenow == 'edit-tags.php'){
				wp_enqueue_style( 'backend_settings',FP_URL.'/assets/css/admin/menu_tax.css');
			}
			// WIDGETS only
				if($pagenow=='widgets.php'){
					wp_enqueue_script('food_widget',FP_URL.'/assets/js/admin/fp_widget.js',array('jquery'),1.0,true);
				}
		}

	/** scripts and styles for all backend **/
		function foodpress_all_backend_files(){
			global $wp_version;
			
			wp_localize_script( 'fp_backend_post', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
			wp_enqueue_script('fp_backend_all',FP_URL.'/assets/js/admin/all_backend.js',array('jquery'),1.0,true);

			// styles for WP<3.8
			if($wp_version<3.8)
				wp_enqueue_style( 'oldwp',FP_URL.'/assets/css/admin/backend_wp_old.css');

			// tax order 
			//wp_enqueue_script('fp_tax_order',FP_URL.'/assets/js/admin-tax-order.js',array('jquery'),1.0,true);

			wp_enqueue_style( 'foodpress_admin_menu_styles', FP_URL . '/assets/css/admin/menu.css' );
			wp_register_style('evo_font_icons',FP_URL.'/assets/fonts/font-awesome.css');
			wp_enqueue_style('fp_fonts',FP_URL.'/assets/fonts/fp_fonts.css');
			wp_enqueue_style( 'evo_font_icons' );
		}
}

