<?php
/**
 * Foodpress Install
 *
 * Plugin install script which adds default pages to WordPress. Runs on activation and upgrade.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin/Install
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// add initial taxonomies
function add_initial_meal_types(){
	$meal_types = array(
			'breakfast'=>'Breakfast',
			'lunch'=>'Lunch',
			'dinner'=>'Dinner',
			'drinks'=>'Drinks',
		);
	$dish_types = array(				
			'appetizer'=>'Appetizer',
			'entree'=>'Entrée',
			'dessert'=>'Dessert',
		);

	foreach($meal_types as $f=>$v){
		wp_insert_term( $v,'meal_type', array( 'slug'=>$f) );
	}
	foreach($dish_types as $f=>$v){
		wp_insert_term( $v,'dish_type', array( 'slug'=>$f) );
	}


	
}
add_initial_meal_types();