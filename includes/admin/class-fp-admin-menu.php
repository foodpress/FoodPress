<?php
/**
 * Setup menus in WP admin.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'fp_Admin_Menus' ) ) :

/**
 * fp_Admin_Menus Class
 */
class fp_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action('admin_menu',  array( $this, 'admin_menu'), 9);
		add_action('admin_action_duplicate_menu',  array( $this, 'foodpress_duplicate_menu_action'));
		add_action('admin_head',array( $this, 'foodpress_admin_head'));
		add_action( 'admin_head', array($this,'foodpress_admin_menu_highlight' ));
	}

	/* Admin Menu */
	function admin_menu() {
	    global $menu, $foodpress, $pagenow;

		
		// check for saved plugin update status to modify menu button
		$licenses = get_option('_fp_licenses');
		$fp_notification = (!empty($licenses['foodpress']['has_new_update']) && $licenses['foodpress']['has_new_update'])? ' <span class="update-plugins count-1" title="1 Plugin Update"><span class="update-count">1</span></span>':null;
		
		// Create admin menu page 
		$main_page = add_menu_page(__('foodPress','foodpress'), 'foodPress','manage_options','foodpress', array( $this, 'foodpress_settings_page'), FP_URL.'/assets/images/icons/foodpress_menu_icon.png');

	    add_action( 'load-' . $main_page, array( $this,'foodpress_admin_help_tab' ));

	    // add submenus to the eventon menu
		add_submenu_page( 'foodpress', 'Language', 'Language', 'manage_options', 'admin.php?page=foodpress&tab=food_2', '' );
		add_submenu_page( 'foodpress', 'Styles', 'Styles', 'manage_options', 'admin.php?page=foodpress&tab=food_3', '' );
		add_submenu_page( 'foodpress', 'Reservations', 'Reservations', 'manage_options', 'admin.php?page=foodpress&tab=food_6', '' );
		add_submenu_page( 'foodpress', 'Addons & Licenses', 'Addons & Licenses', 'manage_options', 'admin.php?page=foodpress&tab=food_5', '' );
		add_submenu_page( 'foodpress', 'Support', 'Support', 'manage_options', 'admin.php?page=foodpress&tab=food_4', '' );
		
	}
	/**
	 * Highlights the correct top level admin menu item for Settings
	 */
		function foodpress_admin_menu_highlight() {
			global $submenu;
			
			if ( isset( $submenu['foodpress'] )  )  {
				$submenu['foodpress'][0][0] = 'Settings';
				//unset( $submenu['foodpress'][2] );
			}

			ob_start();
			?>
				<style>
					.evo_yn_btn .btn_inner:before{content:"<?php _e('NO','foodpress');?>";}
					.evo_yn_btn .btn_inner:after{content:"<?php _e('YES','foodpress');?>";}
				</style>
			<?php
			echo ob_get_clean();
		}

	

	/** 	Duplicate menu action	 */
		function foodpress_duplicate_menu_action() {
			include_once('post_types/duplicate_menu.php');
			foodpress_duplicate_menu();
		}

	/** Include and add help tabs to WordPress admin.	 */
		function foodpress_admin_help_tab() {
			include_once( 'foodpress-admin-content.php' );
			foodpress_admin_help_tab_content();
		}

	/** Include and display the settings page. */
		function foodpress_settings_page() {
			include_once( 'settings/foodpress-admin-settings.php' );
			foodpress_settings();
		}

	/*** Admin Head */
	function foodpress_admin_head() {
			?>
		
		<style type="text/css">
			#adminmenuwrap #menu-posts-menu .wp-menu-image:before{
				content:"\e00c";
				font-family: "foodpress_font" !important;
				font-style: normal !important;
				font-weight: normal !important;
				font-variant: normal !important;
				text-transform: none !important;
				speak: none;
				line-height: 1;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				padding-top: 4px;
			}
			#adminmenu #menu-posts-menu div.wp-menu-image{height: 30px; margin-top: 3px;}
		</style>
		<?php
	}
}
endif;
new fp_Admin_Menus();