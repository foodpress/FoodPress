<?php
/**
 * foodpress Functions
 *
 * Hooked-in functions for foodpress related events on the front-end.
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	foodpress/Functions
 * @version     0.1
 */
// PHP function tag
	function add_foodpress($args){
		global $foodpress;

		// OUT PUT		
		ob_start();
			
		echo $foodpress->foodpress_menus->generate_content($args, 'php');
		
		return ob_get_clean();
	}