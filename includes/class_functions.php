<?php
/**
 * foodpress ONLY frontend functions
 * @version  1.3
 */
class fp_functions{
	public function __construct(){}

	// menu term meta related
		function get_term_image($termid, $termmeta='', $size='medium'){
			$termmeta = (!empty($termmeta))?$termmeta: get_option( "fp_taxonomy_$termid" );

			$__mt_img_id = (!empty($termmeta['meal_type_thumbnail_id']))? $termmeta['meal_type_thumbnail_id']: false;
		 	if($__mt_img_id){
		 		$__mt_img_src = wp_get_attachment_image_src($__mt_img_id, $size);
		 	}
			return (!empty($__mt_img_src))? $__mt_img_src[0]:false;
		}

	// get menu term icons
		function get_term_icon($termid, $termmeta='', $html=false){	
			global $foodpress;

			// check if the icons are set to be hidden
			$hide_icons = (!empty($foodpress->fpOpt['fp_hide_icons']) && $foodpress->fpOpt['fp_hide_icons']=='yes')?
				true: false;

			if($hide_icons) return false;

			$termmeta = (!empty($termmeta))?$termmeta: get_option( "fp_taxonomy_$termid" );
			if(!$html){// dont return html
				return (!empty($termmeta['fpm_iconname']))? $termmeta['fpm_iconname']: false;
			}else{
				return (!empty($termmeta['fpm_iconname']))? "<i class='fa {$termmeta['fpm_iconname']}'></i>": false;
			}			
		}
	// Get description for terms
		function get_term_desc($desc='', $tax){
			global $foodpress;

			if(empty($desc)) return false;

			$shortcodeVar = ($tax=='meal_type')? 'mt_des':'dt_des';
			$shortcode = $foodpress->foodpress_menus->shortcode_args;

			if( $shortcode[$shortcodeVar] =='no') return false;

			return "<p class='fp_term_description'>".$desc."</p>";
		}

	// Language related
		function fp_get_language($text, $opt_val='', $lang=''){
			global $foodpress;

			$opt_val= (!empty($opt_val))? $opt_val: get_option('fp_options_food_2');

			$text_var = strtolower(str_replace(" ", "_", $text));

			// check for language preference
			$shortcode_arg = $foodpress->foodpress_menus->shortcode_args;
				$_lang_variation = (!empty($lang))? $lang:
					( (!empty($shortcode_arg['lang']))? $shortcode_arg['lang']:'L1' );
			
			$new_lang_val = (!empty($opt_val[$_lang_variation][$text_var]) )?
				stripslashes($opt_val[$_lang_variation][$text_var]): $text;

			return $new_lang_val;
		}

	// menu card sorting
	// @version 1.3
		function menucard_sort($array, $opt){
			$menuCard_order = !empty($opt['fpCard_order'])? $opt['fpCard_order']: false;
			$fpCard_selected = (!empty($opt['fpCard_selected']))? 
					explode(',',$opt['fpCard_selected']): false;
		
			$new_array = array();
			
			// create an array
			$correct_order = ($menuCard_order)? 
				explode(',',$menuCard_order): false;

			if($correct_order){

				// each saved order item
				foreach($correct_order as $box){
					if(array_key_exists($box, $array) 
						&& (!empty($fpCard_selected) && in_array($box, $fpCard_selected) || !$fpCard_selected ) 
					){
						$new_array[$box]=$array[$box];
					}
				}
			}else{
				$new_array = $array;
			}	
			return $new_array;
		}

	// get the last updated date of menu items
		function last_updated_date(){
			$output = false;
			$menu = new WP_Query(array('post_type'=>'menu', 'posts_per_page'=>1,'orderby'=>'modified'));
			if($menu->have_posts()):
				while($menu->have_posts()): $menu->the_post();
					$date_format = get_option('date_format');
					$output = get_the_date($date_format);
				endwhile;
			endif;
			wp_reset_postdata();

			return $output;
		}

	// reservation form related
		// redirect page url
			function redirect($opt){
				if(!empty($opt['fpr_redire']) && $opt['fpr_redire']=='yes' && !empty($opt['fpr_redire_url'])){
					return $opt['fpr_redire_url'];
				}else{ return false;}
			}
}