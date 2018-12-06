<?php
/**
 * Functions used for the showing help/links to foodpress resources in admin
 *
 * @author 		foodpress
 * @category 	Admin
 * @package 	foodpress/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Help Tab Content
 *
 * Shows some text about foodpress and links to docs.
 *
 * @access public
 * @return void
 */
function foodpress_admin_help_tab_content() {
	$screen = get_current_screen();

	$screen->add_help_tab( array(
	    'id'	=> 'foodpress_overview_tab',
	    'title'	=> __( 'Overview', 'foodpress' ),
	    'content'	=>

	    	'<p>' . __( 'Thank you for using FoodPress plugin. ', 'foodpress' ). '</p>'

	) );




	$screen->set_help_sidebar(
		'<p><strong>' . __( 'For more information:', 'foodpress' ) . '</strong></p>' .
		'<p><a href="http://demo.myfoodpress.com/" target="_blank">' . __( 'foodpress Demo', 'foodpress' ) . '</a></p>' .
		
		'<p><a href="http://demo.myfoodpress.com/documentation/" target="_blank">' . __( 'Documentation', 'foodpress' ) . '</a></p>'.
		'<p><a href="https://foodpressplugin.freshdesk.com/support/home/" target="_blank">' . __( 'Support', 'foodpress' ) . '</a></p>'
	);
}
?>