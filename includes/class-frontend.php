<?php
/**
 * frontend for foodpress
 * @version 0.1
 */
class fp_frontend{
	public function __construct(){
		global $foodpress;
		$this->fpOpt = $foodpress->fpOpt;

		add_action( 'init', array( $this, 'register_scripts' ), 10 );
		add_action( 'wp_head', array( $this, 'load_dynamic_fp_styles' ) );

		add_action( 'wp_head', array( $this, 'generator' ) );
	}

	/** Register/queue frontend scripts. */
		public function register_scripts() {
			global $foodpress;

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.10.4';	

			// javascripts
			wp_register_script( 'fp_ajax_handle', $foodpress->assets_path. 'js/foodpress_frontend.js', array('jquery','jquery-ui-core'),$foodpress->version,true );
			wp_localize_script( 
				'fp_ajax_handle', 
				'fp_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' )
				)
			);
			
			// Reservation
			wp_register_script('fp_reservation_timepicker',$foodpress->assets_path. 'js/jquery.timepicker.js' ,array('jquery', 'jquery-ui-core','jquery-ui-datepicker'),$foodpress->version, true);

			// styles
			wp_register_style('fp_font_icons',$foodpress->assets_path. 'fonts/font-awesome.css', '4.6.3');
			wp_register_style('fp_fonts',$foodpress->assets_path. 'fonts/fp_fonts.css', $foodpress->version);
			wp_register_style('fp_default',$foodpress->assets_path. 'css/foodpress_styles.css', $foodpress->version);

			// jQuery UI Custom CSS
			wp_enqueue_style("fp_res_jquery_ui_style", "//ajax.googleapis.com/ajax/libs/jqueryui/{$jquery_version}/themes/smoothness/jquery-ui.min.css");

			// reservation modal
			wp_register_style('fp_res_timepicker_style',$foodpress->assets_path. 'css/jquery.timepicker.css');

			// LOAD custom google fonts for skins
			$gfont="//fonts.googleapis.com/css?family=Open+Sans:400italic,600,700,400,300";
			wp_register_style( 'fp_google_fonts', $gfont, '', '', 'screen' );

			// international phone number input
			wp_register_script('fp_res_intl_phone_script', FP_URL.'/assets/js/intlTelInput.min.js', array('jquery'), '1.0', true);
			wp_register_script('fp_res_intl_phone_utils_script', FP_URL.'/assets/js/intlTelInputUtils.js', array('jquery'), '1.0', true);
			wp_register_style('fp_res_intl_phone_input', FP_URL.'/assets/css/intlTelInput.css');

			$this->register_fp_dynamic_styles();

			do_action('fp_register_scripts');
		}
		public function register_fp_dynamic_styles(){
			$opt= $this->fpOpt;

			if(!empty($opt['fp_css_head']) && $opt['fp_css_head'] =='no' || empty($opt['fp_css_head'])){
				if(is_multisite()) {
					$uploads = wp_upload_dir();
					wp_register_style('foodpress_dynamic_styles', $uploads['baseurl'] . '/foodpress_dynamic_styles.css', 'style');
				} else {
					wp_register_style('foodpress_dynamic_styles',
						FP_URL. '/assets/css/foodpress_dynamic_styles.css', 'style');
				}
			}
		}
		public function load_dynamic_fp_styles(){
			$opt= $this->fpOpt;
			if(!empty($opt['fp_css_head']) && $opt['fp_css_head'] =='yes'){
				$dynamic_css = get_option('fp_dyn_css');
				if(!empty($dynamic_css)){
					echo '<style type ="text/css" class="fp_styles">'.$dynamic_css.'</style>';
				}
			}else{
				wp_enqueue_style( 'foodpress_dynamic_styles');
			}
		}
		public function load_default_fp_scripts(){
			//wp_enqueue_script('add_to_cal');
			wp_enqueue_script('fp_reservation_timepicker');
			wp_enqueue_script('fp_ajax_handle');
			wp_enqueue_script('fp_res_intl_phone_script');
			wp_enqueue_script('fp_res_intl_phone_utils_script');
		}
		public function load_default_fp_styles(){
			wp_enqueue_style( 'fp_font_icons');
			wp_enqueue_style( 'fp_fonts');
			wp_enqueue_style( 'fp_default');
			wp_enqueue_style( 'fp_google_fonts' );
			wp_enqueue_style( 'foodpress_dynamic_styles' );
			//wp_enqueue_style( 'fp_res_modal' );
			wp_enqueue_style( 'fp_res_timepicker_style' );
			wp_enqueue_style( 'fp_res_jquery_ui_style' );
			wp_enqueue_style( 'fp_res_intl_phone_input' );
		}

	/** Output generator to aid debugging. */
		public function generator() {
			global $foodpress;
			echo "\n\n" . '<!-- foodPress Version -->' . "\n" . '<meta name="generator" content="foodPress ' . esc_attr( $foodpress->version ) . '" />' . "\n\n";
		}
}
