<?php
/**
 * Plugin Name: foodPress
 * Plugin URI: http://www.myfoodpress.com/
 * Description: Restaurant Menu & Reservation Plugin
 * Version: 1.5.6
 * Author: Michael Gamble & Josh Riley
 * Author URI: http://www.myfoodpress.com
 * Requires at least: 4.5
 * Tested up to: 5.0
 * WC requires at least: 3.0.0
 * WC tested up to: 3.5.1
 *
 * Text Domain: foodpress
 * Domain Path: /lang/languages/
 *
 * @package foodPress
 * @category Core
 * @author Michael Gamble & Josh Riley
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// main foodpress class
if ( ! class_exists( 'foodpress' ) ) {

class foodpress {

	public $version = '1.5.6';

	public $foodpress_menus;
	public $reservations;
	public $fpOpt;
	public $fp_updater;
	public $foodpress_onlineorder_updater;
	public $foodpress_importexport_updater;
	public $foodpress_single_menu_updater;

	private $content;
	public $template_url;

	protected static $_instance = null;

	// setup one instance
	public static function instance(){
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function template_path(){
		return $this->template_url;
	}

	// Construct
		public function __construct() {

			// Define constants
			$this->define_constants();

			// Installation
			register_activation_hook( __FILE__, array( $this, 'activate' ) );

			// Include required files
			$this->includes();

			// Hooks
			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'widgets_init', array( $this, 'register_widgets' ) );
			add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );

			// Deactivation
			register_deactivation_hook( FP_FILE, array($this,'deactivate'));
		}

	/* define FP constants */
		public function define_constants(){
			define( "FP_DIR", WP_PLUGIN_DIR );
			define( "FP_PATH", dirname( __FILE__ ) );
			define( "FP_FILE", ( __FILE__ ) );
			define( "FP_URL", path_join(plugins_url(), basename(dirname(__FILE__))) );
			define( "FP_BASENAME", plugin_basename(__FILE__) );
			define( "FP_BASE", basename(dirname(__FILE__)) );
			define( "FP_PATH2", plugin_dir_path( __FILE__ ));
			define( "FP_BACKEND_URL", get_bloginfo('url').'/wp-admin/' );
			$this->assets_path = str_replace(array('http:','https:'), '',FP_URL).'/assets/';
		}

	/** Include required core files used in admin and on the frontend.	 */
		function includes() {

			include_once( 'includes/foodpress-core-functions.php' );
			include_once( 'includes/class-helper.php' );
			include_once( 'includes/class-frontend.php' );
			include_once( 'includes/class-fp-post-types.php' );
			include_once( 'includes/class_functions.php' );

			// admin only files
			if ( is_admin() ){
				include_once( 'includes/admin/class-admin-init.php' );
				include_once( 'includes/class-fp-github-updater.php' );

				$activePlugins = get_option('active_plugins');
				$secret = '2c037d608fcb96c0c51227ffa611b4c3584a6367';

				if(in_array( 'foodpress/foodpress.php', $activePlugins)) {
					global $foodpress;
					$this->fp_updater = new foodpress_github_updater(FP_FILE, 'foodpress/FoodPress', $secret, $this->version);
				}

				if(in_array( 'foodpress-onlineorder/foodpress-onlineorder.php', $activePlugins)) {
					global $foodpress_oo;
					$pluginFile = WP_PLUGIN_DIR . '/foodpress-onlineorder/foodpress-onlineorder.php';
					$this->foodpress_onlineorder_updater = new foodpress_github_updater($pluginFile, 'foodpress/Online-Order', $secret, $foodpress_oo->version);

					//if($this->foodpress->product)
				}

				if(in_array( 'foodpress-importexport/foodpress-importexport.php', $activePlugins)) {
					global $foodpress_ie;
					$pluginFile = WP_PLUGIN_DIR . '/foodpress-importexport/foodpress-importexport.php';
					$this->foodpress_importexport_updater = new foodpress_github_updater($pluginFile, 'foodpress/Import-Export', $secret, $foodpress_ie->version);
				}

				if(in_array( 'foodpress-single-menu/foodpress-single-menu.php', $activePlugins)) {
					global $foodpress_sin_mi;
					$pluginFile = WP_PLUGIN_DIR . '/foodpress-single-menu/foodpress-single-menu.php';
					$this->foodpress_single_menu_updater = new foodpress_github_updater($pluginFile, 'foodpress/Single-Menu-Item', $secret, $foodpress_sin_mi->version);
				}

				$this->admin = new fp_Admin();
			}

			if ( !is_admin() || defined('DOING_AJAX') )
				include_once( 'foodpress-functions.php' );
				include_once( 'includes/class-fp-shortcodes.php' );
				include_once( 'includes/class-fp-template-loader.php' );

			if ( defined('DOING_AJAX') ){
				include_once( 'includes/foodpress-ajax.php' );
			}

			// Functions
			include_once( 'includes/class-menus.php' );	// Main class to generate foodpress	menus
			include_once( 'includes/class-reservations.php' );

			//include_once('classes/class-fp-updater.php');

		}

	/** Init foodpress when WordPress Initialises. */
		public function init() {

			// Set up localisation
			$this->load_plugin_textdomain();
			$this->fpOpt = get_option('fp_options_food_1');

			$this->template_url = apply_filters('foodpress_template_url','foodpress/');

			// reservations class
			$this->reservations = new foodpress_reservations();
			$this->frontend = new fp_frontend();
			$this->functions = new fp_functions();
			$this->foodpress_menus = new foodpress_menus();

			// Classes/actions loaded for the frontend and for ajax requests
			if ( ! is_admin() || defined('DOING_AJAX') ) {
				// Class instances
				$this->shortcodes = new fp_shortcodes();	// Shortcodes class
			}

			// Init action
			do_action( 'fp_init' );
		}

	/** register_widgets function. */
		function register_widgets() {
			include_once( 'includes/class-fp-widget-main.php' );
			register_widget( 'foodpress_Widget' );
		}

	// functions that were moved
		// script and styles
			public function load_default_fp_scripts(){ $this->frontend->load_default_fp_scripts(); }
			public function load_default_fp_styles(){ $this->frontend->load_default_fp_styles(); }
			public function load_dynamic_fp_styles(){ $this->frontend->load_dynamic_fp_styles(); }


	/** output the in-page popup window for foodpress */
		public function output_foodpress_pop_window($arg){
			$defaults = array(
				'content'=>'',
				'class'=>'',
				'attr'=>'',
				'title'=>'',
				'type'=>'normal',
				'hidden_content'=>''
			);
			$args = (!empty($arg) && is_array($arg) && count($arg)>0) ?
				array_merge($defaults, $arg) : $defaults;

			//print_r($arg);

			$_padding_class = (!empty($args['type']) && $args['type']=='padded')? ' padd':null;

			$content =
			"<div id='foodpress_popup_outter'>
				<div id='foodpress_popup' class='{$args['class']}{$_padding_class}' {$args['attr']} style='display:none'>
					<div class='fpPOP_header'><p id='fpPOP_title'>{$args['title']}</p><a class='foodpress_close_pop_btn'>x</a></div><div id='foodpress_loading'></div>
					<div class='foodpress_popup_text'>{$args['content']}</div>
					";
				$content .= "	<p class='message'></p>

				</div>
			</div><div id='fp_popup_bg'></div>";

			$this->content = $content;
			add_action('admin_footer', array($this, 'actual_output_popup'));
		}


		function actual_output_popup($content){
			echo $this->content;
		}

	/*	Legend popup box across wp-admin	*/
		public function throw_guide($content, $position='', $echo=true){
			$L = (!empty($position) && $position=='L')? ' L':null;
			$content = "<span class='fpGuideCall{$L}'>?<em>{$content}</em></span>";
			if($echo){ echo $content;  }else{ return $content; }
		}

	/** Activate function to store version.	 */
		public function activate() {
			$this->addInstallInstance();
			set_transient( '_fp_activation_redirect', 1, 60 * 60 );
			do_action('foodpress_activate');
		}

		public function deactivate() {
			$this->removeInstallInstance();

			do_action('foodpress_deactivate');
		}

		public function is_foodpress_activated() {
			$licenses =get_option('_fp_licenses');

			if(!empty($licenses)){
				$status = $licenses['foodpress']['status'];
				return ($status=='active') ? true : false;
			} else {
				return false;
			}
		}

	/** * Add setup_environment for various bits. */
		public function setup_environment() {
			// Post thumbnail support
			if ( ! current_theme_supports( 'post-thumbnails', 'menu' ) ) {
				add_theme_support( 'post-thumbnails' );
				remove_post_type_support( 'post', 'thumbnail' );
				remove_post_type_support( 'page', 'thumbnail' );
			} else {
				add_post_type_support( 'menu', 'thumbnail' );
			}
		}

	/** LOAD Backender UI and functionalities for settings.	 */
		public function load_ajde_backender(){
			// thick box
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');

			wp_enqueue_script('backender_colorpicker_fp');
			wp_enqueue_script('ajde_backender_script_fp');
			include_once('includes/admin/ajde_backender.php');
		}

		public function enqueue_backender_styles(){
			wp_enqueue_style( 'ajde_backender_styles_fp',FP_URL.'/assets/css/admin/ajde_backender_style.css');
			wp_enqueue_style( 'colorpicker_styles',FP_URL.'/assets/css/colorpicker_styles.css');

		}
		public function register_backender_scripts(){
			wp_register_script('backender_colorpicker_fp',FP_URL.'/assets/js/colorpicker.js' ,array('jquery'),'1.0', true);
			wp_register_script('ajde_backender_script_fp',FP_URL.'/assets/js/admin/ajde_backender_script.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '1.0', true );
		}

	/**
	 * Load Localisation files.
	 *
	 * ONLY for admin translation
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 * Admin Locale. Looks in:
	 * - WP_LANG_DIR/foodpress/foodpress-LOCALE.mo
	 * - WP_LANG_DIR/plugins/foodpress-LOCALE.mo
	 *
	 * @access public
	 * @return void
	 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'foodpress' );

			load_textdomain( 'foodpress', WP_LANG_DIR . "/foodpress/foodpress-admin".$locale.".mo" );
			load_textdomain( 'foodpress', WP_LANG_DIR . "/plugins/foodpress-admin".$locale.".mo" );

			if ( is_admin() ) {
				load_plugin_textdomain( 'foodpress', false, plugin_basename( dirname( __FILE__ ) ) . "/lang" );
			}
		}
		public function get_current_version(){
			return $this->version;
		}

	// ADDON new version notifications
		private $addon_info ='';
		public function addon_has_new_version($values){
			add_action('after_plugin_row_'.$values['slug'].'.php',array($this,'addon_new_version_message'));
			$this->addon_info = $values;
		}
		public function addon_new_version_message(){
			$addon = $this->addon_info;

			$new_version =  __('There is a new version of '.$addon['name'].' available.', 'foodpress') .' <a class="thickbox" title="'.$addon['name'].'" href="plugin-install.php?tab=plugin-information&plugin='.$addon['slugf'].'&TB_iframe=true&width=640&height=808">'.
				sprintf(__('View version %s Details', 'foodpress'), $addon['version']) . '</a>. ';

			echo '</tr><tr class="plugin-update-tr"><td colspan="3" class="plugin-update"><div class="update-message">' . $new_version . __(' Download the new version from <a href="http://www.myfoodpress.com/my-account/" target="_blank">My Account</a> at myfoodpress.com.', 'foodpress') . '</div></td>';
		}

		public function addInstallInstance() {
			global $wp_version;
			$args = [
				'user' => get_bloginfo('admin_email'),
				'site_name' => get_bloginfo('name'),
				'site_url' => get_bloginfo('url'),
				'wp_version' => get_bloginfo('version'),
				'fp_version' => $this->version
			];
			//var_dump($args);
			$headers = array(
				'body' => array(
					'action' => 'fp_install', 'request' => serialize($args), 'api-key' => md5(get_bloginfo('url'))
				), 'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
			);
			//$url = 'http://localhost/fp_internal';
			$url = 'https://myfoodpress.com/fp_internal';
			$request = wp_remote_post($url, $headers);
	        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {

			}
		}

		public function removeInstallInstance() {
			global $wp_version;
			$args = [
				'user' => get_bloginfo('admin_email'),
				'site_name' => get_bloginfo('name'),
				'site_url' => get_bloginfo('url'),
				'wp_version' => get_bloginfo('version'),
				'fp_version' => $this->version
			];

			$headers = array(
				'body' => array(
					'action' => 'fp_install_remove', 'request' => serialize($args), 'api-key' => md5(get_bloginfo('url'))
				), 'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
			);
			//$url = 'http://localhost/fp_internal';
			$url = 'https://myfoodpress.com/fp_internal';
			$request = wp_remote_post($url, $headers);
	        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {

			}
		}

}

}// class exists


// Main instance of foodpress
// @version 1.4
 function FP(){ return foodpress::instance(); }
// init class
$GLOBALS['foodpress'] = new foodpress();
?>
