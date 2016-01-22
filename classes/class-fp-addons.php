<?php
/**
 * 
 * foodpress addons class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Classes
 * @version     0.1
 */
 
class fp_addons{
	
	// Add Addon to the list
	public function add_to_foodpress_addons_list($args){	
		
		$foodpress_addons_opt = get_option('foodpress_addons');
		
		
		$foodpress_addons_ar[$args['slug']]=$args;
		if(is_array($foodpress_addons_opt)){
			$foodpress_addons_new_ar = array_merge($foodpress_addons_opt, $foodpress_addons_ar );
		}else{
			$foodpress_addons_new_ar = $foodpress_addons_ar;
		}
		
		update_option('foodpress_addons',$foodpress_addons_new_ar);
		
		
	}

	public function remove_from_foodpress_addon_list($slug){
		$foodpress_addons_opt = get_option('foodpress_addons');
			
		if(is_array($foodpress_addons_opt) && array_key_exists($slug, $foodpress_addons_opt)){
			foreach($foodpress_addons_opt as $addon_name=>$addon_ar){
				
				if($addon_name==$slug){
					unset($foodpress_addons_opt[$addon_name]);
				}
			}
		}
		update_option('foodpress_addons',$foodpress_addons_opt);
	}

	/**
	 * update a field for addon
	 */
	public function foodpress_update_addon_field($addon_name, $field_name, $new_value){
		$foodpress_addons_opt = get_option('foodpress_addons');
		
		$newarray = array();
		
		// the array that contain addon details in array
		$addon_array = $foodpress_addons_opt[$addon_name];
		
		if(is_array($addon_array)){
			foreach($addon_array as $field=>$val){
				if($field==$field_name){ 
					$val=$new_value;
				}
				$newarray[$field]=$val;
			}
			$new_ar[$addon_name] = $newarray;
			
			$merged=array_merge($foodpress_addons_opt,$new_ar);
			
			
			update_option('foodpress_addons',$merged);
		}
	}

	
}
?>