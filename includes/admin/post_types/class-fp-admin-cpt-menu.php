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

if ( ! class_exists( 'fp_admin_cpt_menu' ) ) :

/**
 * fp_admin_cpt_menu Class
 */
class fp_admin_cpt_menu {

	/** Constructor */
	public function __construct() {
		add_filter( 'post_row_actions', array( $this,'foodpress_duplicate_menuitem_link_row'),10,2 );
		add_filter( 'page_row_actions', array( $this,'foodpress_duplicate_menuitem_link_row'),10,2 );
		add_action( 'post_submitbox_misc_actions', array( $this,'foodpress_duplicate_menuitem_post_button' ));

		add_filter( 'admin_post_thumbnail_html', array( $this,'foodpress_add_featured_image_instruction'));
		add_filter( 'media_view_strings', array( $this,'foodpress_change_insert_into_post' ));
	}

	/** Duplicate a menuitem link on menu items list  */
	function foodpress_duplicate_menuitem_link_row($actions, $post) {

		if ( function_exists( 'duplicate_post_plugin_activation' ) )
			return $actions;
		
		if ( $post->post_type != 'menu' )
			return $actions;

		$actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=duplicate_menu&amp;post=' . $post->ID ), 'foodpress-duplicate-menu_' . $post->ID ) . '" title="' . __( 'Make a duplicate from this Menu Item', 'foodpress' )
			. '" rel="permalink">' .  __( 'Duplicate', 'foodpress' ) . '</a>';

		return $actions;
	}

	/* Duplicate a menu item link on edit screen*/
	function foodpress_duplicate_menuitem_post_button() {
		global $post;

		if ( function_exists( 'duplicate_post_plugin_activation' ) ) return;
		
		if ( ! is_object( $post ) ) return;

		if ( $post->post_type != 'menu' ) return;

		if ( isset( $_GET['post'] ) ) {
			$notifyUrl = wp_nonce_url( admin_url( "admin.php?action=duplicate_menu&post=" . absint( $_GET['post'] ) ), 'foodpress-duplicate-menu_' . $_GET['post'] );
			?>
			<div class="misc-pub-section" >
			<div id="duplicate-action"><a class="submitduplicate duplication button" href="<?php echo esc_url( $notifyUrl ); ?>"><?php _e( 'Duplicate this Menu Item', 'foodpress' ); ?></a></div>
			</div>
			<?php
		}
	}

	function foodpress_add_featured_image_instruction( $content ) {
	    return $content .= '<p><i>('.__('We recommend 600px X 400px image size for menu item images for best results.','foodpress').')</i></p>';
	}

	/* Change label for insert buttons.*/
	function foodpress_change_insert_into_post( $strings ) {
		global $post_type;

		if ( $post_type == 'menu' ) {
			$strings['insertIntoPost']     = __( 'Insert into menu item', 'foodpress' );
			$strings['uploadedToThisPost'] = __( 'Uploaded to this menu item', 'foodpress' );
		}

		return $strings;
	}

}
endif;

return new fp_admin_cpt_menu();
