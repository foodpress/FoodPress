<?php
/**
 * foodpress Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author 		AJDE
 * @category 	Core
 * @package 	foodpress/Functions
 * @version     1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	

// find if foodpress exists and get url to classes file
	function foodpress_exists(){
		$_active_plugins = get_option( 'active_plugins' );

		if(!empty($_active_plugins)){
			$_fpInstalled = false;
			foreach($_active_plugins as $plugin){
				// check if foodpress is in activated plugins list
				if(strpos( $plugin, 'foodpress.php') !== false){
					$_fpInstalled= true;
					$fpSlug = explode('/', $plugin);
				}
			}

			if(!empty($fpSlug) && $_fpInstalled){
				$url = FP_PATH. '/classes/class-fp-addons.php';

				return (file_exists($url))? $url: false;
			}else{ 	return false;	}
		}else{
			return false;
		}
	}


// fixed word excert from description
function foodpress_get_normal_excerpt($text, $excerpt_length){
	$content='';

	$words = explode(' ', $text, $excerpt_length + 1);
	if(count($words) > $excerpt_length) :
		array_pop($words);
		array_push($words, '...');
		$content = implode(' ', $words);
	endif;
	$content = strip_shortcodes($content);
	$content = str_replace(']]>', ']]&gt;', $content);
	$content = strip_tags($content);

	return $content;
}

function has_foodpress_shortcode($post_content){
	if(has_shortcode($post_content, 'add_foodpress_menu') || 
		has_shortcode($post_content, 'add_foodpress_menu_item')){

		return true;
	}else{
		return false;
	}
}

if(!function_exists('has_shortcode')){
	function has_shortcode($content, $shortcode = '') {  
	      
	    // false because we have to search through the post content first  
	    $found = false;  
	      
	    // if no short code was provided, return false  
	    if (!$shortcode) {  
	        return $found;  
	    }  
	    // check the post content for the short code  
	    if ( stripos($content, '[' . $shortcode) !== false ) {  
	        // we have found the short code  
	        $found = true;  
	    }  
	      
	    // return our final results  
	    return $found;  
	}  
}

/**
 * Get template part (for templates like the event-loop).
 */
function foodpress_get_template_part( $slug, $name = '' , $preurl='') {
	global $foodpress;
	$template = '';
	
	
	if($preurl){
		$template =$preurl."/{$slug}-{$name}.php";
	}else{
		// Look in yourtheme/slug-name.php and yourtheme/foodpress/slug-name.php
		if ( $name )
			$template = locate_template( array ( "{$slug}-{$name}.php", "{$foodpress->template_url}{$slug}-{$name}.php" ) );

		// Get default slug-name.php
		if ( !$template && $name && file_exists( FP_PATH . "/templates/{$slug}-{$name}.php" ) )
			$template = FP_PATH . "/templates/{$slug}-{$name}.php";

		// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/foodpress/slug.php
		if ( !$template )
			$template = locate_template( array ( "{$slug}.php", "{$foodpress->template_url}{$slug}.php" ) );

		
	}
	
	if ( $template )
		load_template( $template, false );
}


// Returns a proper form of labeling for custom post type
/**
 * Function that returns an array containing the IDs of the products that are on sale.
 */
if( !function_exists ('foodpress_get_proper_labels')){
	function foodpress_get_proper_labels($sin, $plu){
		return array(
		'name' => _x($plu, 'post type general name'),
		'singular_name' => _x($sin, 'post type singular name'),
		'add_new' => _x('Add New', $sin),
		'add_new_item' => __('Add New '.$sin),
		'edit_item' => __('Edit '.$sin),
		'new_item' => __('New '.$sin),
		'all_items' => __('All '.$plu),
		'view_item' => __('View '.$sin),
		'search_items' => __('Search '.$plu),
		'not_found' =>  __('No '.$plu.' found'),
		'not_found_in_trash' => __('No '.$plu.' found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => $plu
	  );
	}
}



// foodpress PHP template tagsperpage
function add_foodpress_menu($atts){
	global $foodpress;
		
	// connect to get supported arguments for shortcodes
	$supported_defaults = $foodpress->foodpress_menus->get_acceptable_shortcode_atts();
	
	$args = shortcode_atts( $supported_defaults, $atts ) ;		
	//print_r($atts);

	// OUT PUT		
		
	echo $foodpress->foodpress_menus->generate_content($args);
	
}


// =========
// LANGUAGE 

/** return custom language text saved in settings **/
	function foodpress_get_custom_language($fp_options='', $field, $default_val, $lang=''){
		global $foodpress;
			
		// check which language is called for
		$fp_options = (!empty($fp_options))? $fp_options: get_option('fp_options_food_2');
		
		// check for language preference
		$shortcode_arg = $foodpress->foodpress_menus->shortcode_args;
		$_lang_variation = (!empty($lang))? 
			$lang: 
			((!empty($shortcode_arg['lang']))? $shortcode_arg['lang']:'L1');
				
		$new_lang_val = (!empty($fp_options[$_lang_variation][$field]) )?
			stripslashes($fp_options[$_lang_variation][$field]): $default_val;
			
		return $new_lang_val;
	}

	function foodpress_process_lang_options($options){
		$new_options = array();
		
		foreach($options as $f=>$v){
			$new_options[$f]= stripslashes($v);
		}
		return $new_options;
	}
	// Return translated language
	function fp_get_language($text, $opt_val='', $lang=''){
		global $foodpress;
		return $foodpress->functions->fp_get_language($text, $opt_val='', $lang='');
	}


// meta value check and return
	function fp_menumeta($meta_array, $fieldname){

		return (!empty($meta_array[$fieldname]))? $meta_array[$fieldname][0]:null;

	}
	function fp_menumeta_yesno($meta_array, $fieldname, $check_value, $yes_value, $no_value){
		
		return (!empty($meta_array[$fieldname]) && $meta_array[$fieldname][0] == $check_value)? $yes_value:$no_value;
	}
	function fp_menumeta_select($meta_array, $fieldname, $value){
		return (!empty($meta_array[$fieldname]) && $meta_array[$fieldname][0]==$value)?
			"selected='selected'":null;
	}

	function fp_menumeta_($meta_array, $fieldname, $check){
		return (!empty($meta_array[$fieldname]) && $meta_array[$fieldname][0] == $check)? true:false;
	}

	function foodpress_opt_val($options, $var, $default){
		$options = (!empty($options))? $options : get_option('fp_options_food_1');	
		return (!empty($options[$var]))? $options[$var] : $default;

	}

	function fp_placeholder_img_src(){
		return FP_URL.'/assets/images/placeholder.png';
	}

/** 1.2 */
	// return the reservation form field count
	function foodpress_get_reservation_form_fields(){
		return apply_filters('foodpress_reservation_form_fields', '5');
	}	

/** getting setting options values */
	function foodpress_get_option($option_index){
		return get_option('fp_options_food_'.$option_index);
	}
	function foodpress_get_menu_archive_page_id(){
		$opt = foodpress_get_option(1);
		return !empty($opt['fp_menu_archive_page_id'])? $opt['fp_menu_archive_page_id']: false;
	}

/*
	return jquery and HTML UNIVERSAL date format for the site
	added: version 1.2
	updated: 
*/
	function foodpress_get_timeNdate_format($foopt=''){
		
		if(empty($foopt))
			$foopt = get_option('fp_options_food_6');
		
		if(!empty($foopt['fpr_univ_date_format']) && $foopt['fpr_univ_date_format']=='yes'){
					
			/** get date formate and convert to JQ datepicker format**/				
			$wp_date_format = get_option('date_format');
			$format_str = str_split($wp_date_format);
			
			foreach($format_str as $str){
				switch($str){							
					case 'j': $nstr = 'd'; break;
					case 'd': $nstr = 'dd'; break;	
					case 'D': $nstr = 'D'; break;	
					case 'l': $nstr = 'DD'; break;	
					case 'm': $nstr = 'mm'; break;
					case 'M': $nstr = 'M'; break;
					case 'n': $nstr = 'm'; break;
					case 'F': $nstr = 'MM'; break;							
					case 'Y': $nstr = 'yy'; break;
					case 'y': $nstr = 'y'; break;
											
					default :  $nstr = ''; break;							
				}
				$jq_date_format[] = (!empty($nstr))?$nstr:$str;
				
			}
			$jq_date_format = implode('',$jq_date_format);
			$fp_date_format = $wp_date_format;
		}else{
			$jq_date_format ='yy/mm/dd';
			$fp_date_format = 'Y/m/d';
		}
		
		return array(
			$jq_date_format, 
			$fp_date_format,
		);
	}


// get times for reservation form time selector
// interval values, 12 (5), 6(10), 4(15), 2(30), 1(60)
	function foodpress_get_times($interval=4, $start='-', $end='-'){	
		$WP_timeformat = get_option('time_format');
		$format = (strpos($WP_timeformat, 'H')=== false)?
			((strpos($WP_timeformat, 'G')=== false)?'12':'24'):'24';

		if($format=='24' ){

			if($start!='-') $startX = explode(':', $start);
			if($end!='-') $endX = explode(':', $end);

			for($y=0; $y<24; $y++){

				if($start!= '-' && ($y< (int)$startX[0])) continue;
				if($end!= '-' && ($y>= (int)$endX[0])) continue;

				if($interval==1){
					$data = sprintf("%02d",$y).':00';
					$HOURS[$data] = $data;
				}else{
					for($z=0; $z<$interval; $z++){
						$min = sprintf("%02d",(60/$interval)*$z);
						$data = sprintf("%02d",$y).':'.$min;
						$HOURS[$data] = $data;
					}
				}				
			}
		}else{

			if($start!='-') $startX = explode(':',date('g:a', strtotime($start)));
			if($end!='-') $endX = explode(':',date('g:a', strtotime($end)));

			foreach(array('am','pm') as $AMPM){

				if($start!='-'&& $AMPM =='am' && $startX[1]=='pm' ) continue;
				
				for($y=1; $y<13; $y++){

					if($start!= '-' && 
						( ($AMPM =='am' && $startX[1]=='pm') || 
							($AMPM == $startX[1] && $y< (int)$startX[0])
						)
					) 
						continue;

					if($end!= '-' && 
						( ($AMPM =='pm' && $endX[1]=='am') || 
							($AMPM == $endX[1] && $y>= (int)$endX[0])
						)
					) 
						continue;


					$AMPM = ($y==12)? (($AMPM=='am')?'pm':'am'): $AMPM;
					for($z=0; $z<$interval; $z++){
						$min = sprintf("%02d",(60/$interval)*$z);
						$data = $y.':'.$min.$AMPM;
						$HOURS[$data] = $data;
					}
				}
			}
		}
		return $HOURS;
	}


// custom menu item fields
	function fp_calculate_cmd_count($opt=''){
		$opt = (!empty($opt))? $opt: get_option('fp_options_food_1');
		$count=0;
		for($x=1; $x<=fp_max_cmd_count(); $x++ ){
			if(!empty($opt['fp_af_'.$x]) && $opt['fp_af_'.$x]=='yes' && !empty($opt['fp_ec_f'.$x])){
				$count = $x;
			}else{
				break;
			}
		}
		return $count;
	}
	function fp_max_cmd_count(){
		return apply_filters('fp_max_cmd_count', 3);
	}


?>