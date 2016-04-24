<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Post types
 *
 * Registers post types and taxonomies
 *
 * @class 		fp_post_types
 * @version		1.1.5
 * @package		foodpress/Classes/menu-items
 * @category	Class
 * @author 		AJDE
 */

class fp_post_types{
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		
	}

	public static function register_taxonomies() {
		if ( post_type_exists('menu') )
			return;
		
		/**
		 * Taxonomies
		 **/
		do_action( 'foodpress_register_taxonomy' );		
		
		$fp_opt= get_option('fp_options_food_1');
		$fp_opt_2= get_option('fp_options_food_2');
		
		//menu item types category 
		$fp_tax = (!empty($fp_opt['fp_mty1']))?$fp_opt['fp_mty1']:'Meal Type';
			$fp_tax_plur = $fp_tax;
		$fp_tax2 = (!empty($fp_opt['fp_mty2']))?$fp_opt['fp_mty2']:'Dish Type';
			$fp_tax2_plur = $fp_tax2;
			// additional category type
			$fp_tax3 = (!empty($fp_opt['fp_mty3']))?$fp_opt['fp_mty3']:'Custom Category';
				$fp_tax3_plur = $fp_tax3;
		
		$_hierarchical = true;
		
		register_taxonomy('meal_type', 
			apply_filters( 'fp_taxonomy_objects_meal_type', array('menu') ),
			apply_filters( 'fp_taxonomy_args_meal_type', array(
				'hierarchical' => $_hierarchical, 
				'label' 				=> __( $fp_tax, 'foodpress'),
	            'labels' => array(
	                    'name' 				=> __( $fp_tax, 'foodpress'),
	                    'singular_name' 	=> __( $fp_tax_plur, 'foodpress'),
						'menu_name'			=> _x( $fp_tax, 'Admin menu name', 'foodpress' ),
	                    'search_items' 		=> __( 'Search '.$fp_tax_plur, 'foodpress'),
	                    'all_items' 		=> __( 'All '.$fp_tax_plur, 'foodpress'),
	                    'parent_item' 		=> __( 'Parent '.$fp_tax, 'foodpress'),
	                    'parent_item_colon' => __( 'Parent '.$fp_tax.':', 'foodpress'),
	                    'edit_item' 		=> __( 'Edit '.$fp_tax, 'foodpress'),
	                    'update_item' 		=> __( 'Update '.$fp_tax, 'foodpress'),
	                    'add_new_item' 		=> __( 'Add New '.$fp_tax, 'foodpress'),
	                    'new_item_name' 	=> __( 'New  Name'.$fp_tax, 'foodpress')
	            	),
				'show_ui' => true,
				'query_var' => true,				
				'rewrite' => array( 'slug' => 'meal-type' ),
				'sort'=>true
			) )
		);
		
		register_taxonomy('dish_type', 
			apply_filters( 'fp_taxonomy_objects_dish_type', array('menu') ),
			apply_filters( 'fp_taxonomy_args_dish_type', array(
				'hierarchical' => $_hierarchical, 
				'label' 				=> __( $fp_tax2, 'foodpress'),
	            'labels' => array(
	                    'name' 				=> __( $fp_tax2, 'foodpress'),
	                    'singular_name' 	=> __( $fp_tax2_plur, 'foodpress'),
						'menu_name'			=> _x( $fp_tax2, 'Admin menu name', 'foodpress' ),
	                    'search_items' 		=> __( 'Search '.$fp_tax2_plur, 'foodpress'),
	                    'all_items' 		=> __( 'All '.$fp_tax2_plur, 'foodpress'),
	                    'parent_item' 		=> __( 'Parent '.$fp_tax2, 'foodpress'),
	                    'parent_item_colon' => __( 'Parent '.$fp_tax2.':', 'foodpress'),
	                    'edit_item' 		=> __( 'Edit '.$fp_tax2, 'foodpress'),
	                    'update_item' 		=> __( 'Update '.$fp_tax2, 'foodpress'),
	                    'add_new_item' 		=> __( 'Add New '.$fp_tax2, 'foodpress'),
	                    'new_item_name' 	=> __( 'New  Name'.$fp_tax2, 'foodpress')
	            	), 
				'show_ui' => true,
				'query_var' => true,				
				'rewrite' => array( 'slug' => 'dish-type' )
			) )
		);
	

		// restaurant Location for menus

		$fp_taxL = $fp_taxL_plur = fp_get_language('Location',$fp_opt_2 );
		 
		register_taxonomy('menu_location', 
			apply_filters( 'fp_taxonomy_objects_location', array('menu') ),
			apply_filters( 'fp_taxonomy_args_location', array(
				'hierarchical' => $_hierarchical, 
				'label' 				=> __( $fp_taxL, 'foodpress'),
	            'labels' => array(
	                    'name' 				=> __( $fp_taxL, 'foodpress'),
	                    'singular_name' 	=> __( $fp_taxL_plur, 'foodpress'),
						'menu_name'			=> _x( $fp_taxL, 'Admin menu name', 'foodpress' ),
	                    'search_items' 		=> __( 'Search '.$fp_taxL_plur, 'foodpress'),
	                    'all_items' 		=> __( 'All '.$fp_taxL_plur, 'foodpress'),
	                    'parent_item' 		=> __( 'Parent '.$fp_taxL, 'foodpress'),
	                    'parent_item_colon' => __( 'Parent '.$fp_taxL.':', 'foodpress'),
	                    'edit_item' 		=> __( 'Edit '.$fp_taxL, 'foodpress'),
	                    'update_item' 		=> __( 'Update '.$fp_taxL, 'foodpress'),
	                    'add_new_item' 		=> __( 'Add New '.$fp_taxL, 'foodpress'),
	                    'new_item_name' 	=> __( 'New  Name'.$fp_taxL, 'foodpress')
	            	), 
				'show_ui' => true,
				'query_var' => true,				
				'rewrite' => array( 'slug' => 'menu-location' )
			) )
		);
		
		// Extra category if set
		if( !empty($fp_opt['fp_cusTax']) && $fp_opt['fp_cusTax']=='yes'){
			register_taxonomy('menu_type_3', 
				apply_filters( 'fp_taxonomy_objects_menu_type_3', array('menu') ),
				apply_filters( 'fp_taxonomy_args_menu_type_3', array(
					'hierarchical' => $_hierarchical, 
					'label' 				=> __( $fp_tax3, 'foodpress'),
		            'labels' => array(
		                    'name' 				=> __( $fp_tax3, 'foodpress'),
		                    'singular_name' 	=> __( $fp_tax3_plur, 'foodpress'),
							'menu_name'			=> _x( $fp_tax3, 'Admin menu name', 'foodpress' ),
		                    'search_items' 		=> __( 'Search '.$fp_tax3_plur, 'foodpress'),
		                    'all_items' 		=> __( 'All '.$fp_tax3_plur, 'foodpress'),
		                    'parent_item' 		=> __( 'Parent '.$fp_tax3, 'foodpress'),
		                    'parent_item_colon' => __( 'Parent '.$fp_tax3.':', 'foodpress'),
		                    'edit_item' 		=> __( 'Edit '.$fp_tax3, 'foodpress'),
		                    'update_item' 		=> __( 'Update '.$fp_tax3, 'foodpress'),
		                    'add_new_item' 		=> __( 'Add New '.$fp_tax3, 'foodpress'),
		                    'new_item_name' 	=> __( 'New  Name'.$fp_tax3, 'foodpress')
		            	), 
					'show_ui' => true,
					'query_var' => true,				
					'rewrite' => array( 'slug' => 'menu-type' ) 
				) )
			);
		}
	}

	/**
	 * Register core post types
	 */
	public static function register_post_types() {
		if ( post_type_exists('menu') || post_type_exists('reservation') )
			return;

		/**
		 * Post Types
		 **/
		do_action( 'foodpress_register_post_type' );
		
		$labels = foodpress_get_proper_labels(__('Menu Item', 'foodpress'),__('Menu Items', 'foodpress'));
		register_post_type('menu', 
			apply_filters( 'foodpress_register_post_type_menu',
				array(
					'labels' => $labels,
					'description' 			=> __( 'This is where you can add new items to your menu.', 'foodpress' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'show_in_menu'			=> true,
					'capability_type' 		=> 'post',
					'publicly_queryable' 	=> true,
					'hierarchical' 			=> false,
					'rewrite' 				=> apply_filters('foodpress_menu_cpt_slug', array('slug'=>'menuitems')),
					'query_var'		 		=> true,
					'supports' 				=> array('title','custom-fields','thumbnail', 'page-attributes'),
					//'supports' => array('title','thumbnail', 'page-attributes'),
					'menu_position' 		=> 5, 
					'has_archive' 			=> true
				)
			)
		);

		$labelX = foodpress_get_proper_labels(__('Menu Addition', 'foodpress'),__('Menu Additions', 'foodpress'));
		register_post_type('menu-additions', 
			apply_filters( 'foodpress_register_post_type_menu',
				array(
					'labels' => $labelX,
					'description' 			=> __( 'This is where you can add new items to your menu.', 'foodpress' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'capability_type' 		=> 'post',
					'publicly_queryable' 	=> true,
					'hierarchical' 			=> false,
					'rewrite' 				=> false,
					'query_var'		 		=> true,
					'supports' 				=> array('title','editor', 'page-attributes'),
					//'supports' => array('title','thumbnail', 'page-attributes'),
					'menu_position' 		=> 5, 
					'show_in_menu'			=>'edit.php?post_type=menu',
					'has_archive' 			=> true
				)
			)
		);


		$labels_2 = foodpress_get_proper_labels( __('Reservation','foodpress'),__('Reservations', 'foodpress'));
		register_post_type('reservation', 
			apply_filters( 'foodpress_register_post_type_reservation',
				array(
					'labels' => $labels_2,
					'description' 			=> __( 'Reservations for your restaurant.', 'foodpress' ),
					'public' 				=> true,
					'show_ui' 				=> true,
					'show_in_menu'			=> false,
					'capability_type' 		=> 'post',
					'publicly_queryable' 	=> true,
					'hierarchical' 			=> false,
					'rewrite' 				=> array('slug'=>'reservations'),
					'query_var'		 		=> true,
					'supports' 				=> array('title','custom-fields',),
					//'supports' => array('title','thumbnail', 'page-attributes'),
					'menu_position' 		=> 5, 
					'show_in_menu'			=>'foodpress',
					'has_archive' 			=> true
					
				)
			)
		);

	}

}
new fp_post_types();