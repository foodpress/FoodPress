<?php
/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version (major).
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin
 * @version     1.3.2
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class fp_welcome_page {
	public function __construct() {		
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/** Add admin menus/screens*/
	public function admin_menus() {
		$welcome_page_title = __( 'Welcome to FoodPress', 'foodpress' );
		// About
		$about = add_dashboard_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'fp-about', array( $this, 'about_screen' ) );
		
		add_action( 'admin_print_styles-'. $about, array( $this, 'admin_css' ) );
	}

	/** admin_css function. */
	public function admin_css() {
		wp_enqueue_style( 'foodpress-activation', FP_URL.'/assets/css/admin/activation.css' );
	}
	
	/** Add styles just for this page, and remove dashboard page links.	 */
	public function admin_head() {
		global $foodpress;
		remove_submenu_page( 'index.php', 'fp-about' );
		
		?>
		<style type="text/css"></style>
		<?php
	}
	
	/** Into text/links shown on all about pages.	 */
	private function intro() {
		global $foodpress;
		

		// Drop minor version if 0
		//$major_version = substr( $foodpress->version, 0, 3 );
		
	?>
		
		<div id='foodpress_welcome_header'>
			
			<p class='logo'><img src='<?php echo FP_URL?>/assets/images/welcome/fp_main_logo.jpg'/></p>
			<p class='h3'>
			<?php
				if(!empty($_GET['fp-updated']))
					$message = __( 'Thank you for updating foodpress to ver ', 'foodpress' );
				else
					$message = __( 'Thank you for purchasing foodpress ver ', 'foodpress' );
					
				printf( __( '%s%s', 'foodpress' ), $message,	$foodpress->version );
			?></p>
		</div>
		

		<p class="foodpress-actions">
			<a href="<?php echo admin_url('admin.php?page=foodpress'); ?>" class="btn_prime fp_admin_btn"><?php _e( 'Settings', 'foodpress' ); ?></a>
			
			<a class="btn_prime fp_admin_btn" href="http://demo.myfoodpress.com/documentation/" target='_blank'><?php _e( 'Documentation', 'foodpress' ); ?></a>
			
			<a class="btn_prime fp_admin_btn" href="https://foodpressplugin.freshdesk.com/support/home/" target='_blank'><?php _e( 'Support', 'foodpress' ); ?></a>
			
			<div class='foodpress-welcome-twitter'>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.myfoodpress.com/" data-text="Restaurant Meny Manager Plugin for WordPress." data-via="foodpress" data-size="large" data-hashtags="myfoodpress">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
			</div>
			
		</p>
		
		<?php 
	}
	
	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		global $foodpress;
		?>
		<div class="wrap about-wrap foodpress-welcome-box">

			<?php $this->intro(); ?>

			<!--<div class="changelog point-releases"></div>-->


			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'foodpress' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to myfoodpress Settings', 'foodpress' ); ?></a>
			</div>
		</div>
		<?php
	}
	
	// Sends user to the welcome page on first activation
		public function welcome() {
			// Bail if no activation redirect transient is set
		    if ( ! get_transient( '_fp_activation_redirect' )  )
				return;

			// Delete the redirect transient
			delete_transient( '_fp_activation_redirect' );

			// Bail if we are waiting to install or update via the interface update/install links
			if ( get_option( '_fp_needs_update' ) == 1  )
				return;

			// Bail if activating from network, or bulk, or within an iFrame
			if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) )
				return;
			
			// plugin is updated
			if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'foodpress.php' ) ) )
				return;
				//wp_safe_redirect( admin_url( 'index.php?page=fp-about&fp-updated=true' ) );
			
			
			wp_safe_redirect( admin_url( 'index.php?page=fp-about' ) );

			// update dynamic styles and appearance styles upon new update
			foodpress_generate_options_css();
			
			exit;
		}	
}

new fp_welcome_page();
?>