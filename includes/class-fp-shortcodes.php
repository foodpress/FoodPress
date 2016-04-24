<?php
/**
 * fp_shortcodes class.
 *
 * @class 		fp_shortcodes
 * @version		1.0.0
 * @package		foodpress/Classes
 * @category	Class
 * @author 		AJDE
 */

class fp_shortcodes {
	public function __construct(){
		// regular shortcodes
		add_shortcode('add_foodpress_menu',array($this,'foodpress_show_menu'));
		add_shortcode('add_foodpress_menu_item',array($this,'foodpress_show_menu_item'));
		add_shortcode('add_reservation_form',array($this,'foodpress_show_reservation_form'));
		
	}
	
	
	/** Show calendar shortcode	 */
		public function foodpress_show_menu($atts){
			global $foodpress;
			
			// connect to get supported arguments for shortcodes
			$supported_defaults = $foodpress->foodpress_menus->get_acceptable_shortcode_atts();
			
			$args = shortcode_atts( $supported_defaults, $atts ) ;		
			//print_r($args);

			// OUT PUT		
			ob_start();
				
			echo $foodpress->foodpress_menus->generate_content($args);
			
			return ob_get_clean();
		}

	// show single menu item box
		public function foodpress_show_menu_item($atts){
			global $foodpress;

			add_filter('foodpress_default_args', array($this,'shortcode_defaults'), 10, 1);

			// connect to get supported arguments for shortcodes
			$supported_defaults = $foodpress->foodpress_menus->get_acceptable_shortcode_atts();
			
			$args = shortcode_atts( $supported_defaults, $atts ) ;
			
			// OUT PUT		
			ob_start();
				
			echo $foodpress->foodpress_menus->get_individual_item_content(
				array($args['item_id']), $args, 'single');
			
			return ob_get_clean();
			
		}

	// shortcode defaults for menu single item
		function shortcode_defaults($arr){
			return array_merge($arr, array(
				'item_id'=>'all',
				'ind_style'=>'one',
			));
		}


	// show reservation button - this enables a call to action to the front end that tells the user they can reserve a table
		public function foodpress_show_reservation_form($atts){
			global $foodpress;

			ob_start();
			
			// this will output reservation button along with once instane of reservation form
			// allows for multiple reservation buttons on several places in the website
			echo $foodpress->reservations->output_reservation_button($atts);

			return ob_get_clean();
			
		}
}
?>