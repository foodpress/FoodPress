<?php
/**
 * Meta boxes for menu
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin/menu
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Init the meta boxes.
 */
function foodpress_meta_boxes(){
	// menu meta boxes
	add_meta_box('menu_mb1','Menu Item Data', 'menu_meta_box_1','menu', 'normal', 'high');	

	do_action('foodpress_add_meta_boxes');
}
add_action( 'add_meta_boxes', 'foodpress_meta_boxes' );
add_action( 'save_post', 'foodpress_save_meta_data', 1, 2 );

function foodpress_menu_metabox_array($fmeta=''){
	$fp_opt1= get_option('fp_options_food_1');
	$__nutritions_code='';

	// nutrition information values
		$nutrition_info = array(
			array(
				'name'=>__('Calories','foodpress'),'slug'=>'calories','placeholder'=>'250kcal',
				'title'=>__('How many calories is in this item?','foodpress')
			),array(
				'name'=>__('Cholesterol','foodpress'),'slug'=>'cholesterol','placeholder'=>'50mg',
				'title'=>__('Cholesterol','foodpress')
			),array(
				'name'=>__('Fiber','foodpress'),'slug'=>'fiber','placeholder'=>'2.0g',
				'title'=>__('Fiber','foodpress')
			),array(
				'name'=>__('Sodium','foodpress'),'slug'=>'sodium','placeholder'=>'175mg',
				'title'=>__('Sodium','foodpress')
			),array(
				'name'=>__('Carbohydrates','foodpress'),'slug'=>'carbohydrates','placeholder'=>'4.5g',
				'title'=>__('Carbohydrates','foodpress')
			),array(
				'name'=>__('Fat','foodpress'),'slug'=>'fat','placeholder'=>'3.0g',
				'title'=>__('Fat','foodpress')
			),array(
				'name'=>__('Protein','foodpress'),'slug'=>'protein','placeholder'=>'1.7g',
				'title'=>__('Protein','foodpress')
			)
		);

		// build nutritions information array for meta box array
		foreach($nutrition_info as $nutr){
			$value = (!empty($fmeta[ $nutr['slug'] ]))? $fmeta[ $nutr['slug'] ][0]: null;

			$__nutritions_code.= '<p><input type="text" name="'.$nutr['slug'].'" title="'.$nutr['title'].'" value="'.$value.'" placeholder="'.$nutr['placeholder'].'"/> <label>'.$nutr['name'].'</label></p>';
		}

	// main meta box array for menu items post

		// PARTS
			$__meta_price = apply_filters('foodpress_meta_pricebox', 
				array(
					'id'=>'fp_price',
					'name'=>__('Price','foodpress'),
					'code'=>'<input type="text" name="fp_price" title="'.__('Enter the price for this item').'" value="'.( (!empty($fmeta['fp_price']))? $fmeta['fp_price'][0]: null).'" placeholder="$0.00"/>','type'=>''	,
					'slug'=>'price',
					'guide'=>'Write the currency symbol along with price numbers. eg. $15.00'
				)
			);

		// menu icon symbols
		// that will show up in the menu for each item			
			$__menu_icons=''; $__selected=''; $_menu_icons_active = false;
			$_menu_icons_value = (!empty($fmeta['fp_menuicons']))? $fmeta['fp_menuicons'][0]:null;

			$show_icons = false;
			for($x=1; $x<4; $x++){

				// if not activated
				if(empty($fp_opt1['fp_mi'.$x]) || (!empty($fp_opt1['fp_mi'.$x]) && $fp_opt1['fp_mi'.$x]=='no')) continue;

				$icon_name = $fp_opt1['fp_m_00'.$x];
				$icon = $fp_opt1['fp_m_00'.$x.'i'];

				// if icon name and icons have values
				if(empty($icon_name) && empty($icon)) continue;

				$show_icons = true;
				// check it the values is saved so can add selected class
				if($_menu_icons_value ){
					$pos = strpos($_menu_icons_value, 'fp_m_00'.$x.'i');
					$__selected = ( $pos!== false )? 'selected':null;
				}

				$__menu_icons .= "<p class='menu_item_icons {$__selected}' data-val='".'fp_m_00'.$x.'i'."' title='{$icon_name}'><i class='fa {$icon}'></i> <span>{$icon_name}</span></p>";
				$_menu_icons_active =true;
			}
			$__menu_icons .= ($_menu_icons_active)? "<input type='hidden' name='fp_menuicons' value='{$_menu_icons_value}'/>": null;

			if($show_icons){
				// array for meta fields
				$__menui_array = array(
					'id'=>'fp_menuicons',
					'name'=>__('Icons','foodpress'),
					'code'=>$__menu_icons,
					'type'=>'menuicons',
					'slug'=>'menuicons',
					'iconvar'=>'',
					'guide'=>__('Menu icon symbols to represent certain values for a menu item. Can be edit from foodpress settings > Icon Symbols','foodpress')
				);
			}else{
				$__menui_array = array();
			}
		
	
	$meta_boxes = apply_filters('foodpress_menu_metaboxes', array(
		array(
			'id'=>'fp_subheader',
			'name'=>__('Sub Header','foodpress'),
			'code'=>'single','type'=>'single',
			'slug'=>'subheader',
			'iconvar'=>''
		),array(
			'id'=>'fp_description',
			'name'=>__('Description','foodpress'),
			'code'=>'','type'=>'editor',
			'slug'=>'description',
			'iconvar'=>''
		),array(
			'id'=>'fp_addition',
			'name'=>__('Additional Text','foodpress'),
			'code'=>'single','type'=>'single',
			'placeholder'=>'eg. Add chicken for $2',
			'slug'=>'addition',
			'iconvar'=>''
		),$__meta_price,array(
			'id'=>'fp_spicy',
			'name'=>__('Spicy Level','foodpress'),
			'options'=>array(0,1,2,3,4,5),
			'code'=>'select','type'=>'select'	,
			'slug'=>'spicy',
			'guide'=>'Spicy level of 0 = Not hot & spicy at all, 5 = extremely hot and spicy',
			'iconvar'=>''
		),array(
			'id'=>'fp_nutrition',
			'name'=>__('Nutritional Information','foodpress'),
			'type'=>'multiinput', 'slug'=>'nutrition',
			'ids'=>array('calories','cholesterol','fiber','sodium','carbohydrates','fat','protein'),
			'slug'=>'cal',
			'code'=>$__nutritions_code,
			'iconvar'=>'fp__f3'
			
		),array(
			'id'=>'fp_ingredients',
			'name'=>__('Ingredients','foodpress'),
			'code'=>'','type'=>'editor'	,
			'slug'=>'ingredients',
			'iconvar'=>'fp__f2'
		),$__menui_array
	));

	// add additional custom fields
		for($x =1; $x<4; $x++){
			// field activated
			if(!empty($fp_opt1['fp_af_'.$x]) && $fp_opt1['fp_af_'.$x]=='yes'){

				// field name
				$_fieldName = (!empty($fp_opt1['fp_ec_f'.$x]))? stripslashes($fp_opt1['fp_ec_f'.$x]): 'Custom field '.$x;

				// single line or multi lines
				$__field_type = 'single';
				if(!empty($fp_opt1['fp_ec_f'.$x.'b']) && $fp_opt1['fp_ec_f'.$x.'b']=='textarea'){
					$__field_type = 'textarea';
				}elseif(!empty($fp_opt1['fp_ec_f'.$x.'b']) && $fp_opt1['fp_ec_f'.$x.'b']=='menuadditions'){
					$__field_type = 'menuadditions';
				}else{	$__field_type = 'single';	}
				

				$meta_boxes[]=array(
					'id'=>'fp_ec_f'.$x,
					'name'=>__($_fieldName,'foodpress'),
					'code'=>'cc',
					'type'=>'custom_field',
					'field_type'=>$__field_type,
					'x'=>$x,
					'slug'=>'fp_ec_f'.$x,
					'iconvar'=>'fp_ec_f'.$x.'a'
				);
			}
		}

	//print_r($meta_boxes);
	
	return $meta_boxes;
}
	
/**
 * Main meta box.
 */
	function menu_meta_box_1(){
		global $foodpress;
				
		// INITIALS
			$fp_opt1= get_option('fp_options_food_1');
			$fp_opt2= get_option('fp_options_food_2');
			
			// Use nonce for verification
			wp_nonce_field( plugin_basename( __FILE__ ), 'fp_noncename' );
			
			// The actual fields for data entry
			$p_id = get_the_ID();
			$fmeta = get_post_custom($p_id);
			
			
			$closedmeta = foodpress_get_collapsed_metaboxes($p_id);
			//print_r($closedmeta);

	?>
		<input type='hidden' id='fp_collapse_meta_boxes' name='fp_collapse_meta_boxes' value='<?php echo (!empty($closedmeta))? implode(',', $closedmeta): null;?>'/>
		<ul id='fp_meta_fields' class='foodpress'>
			<?php
								
				foreach(foodpress_menu_metabox_array($fmeta) as $mb){

					// verify valid values on array
					if(!empty($mb) || ( count($mb)>0 && $mb['id']=='fp_menuicons' && !empty($mb['code'])) ):

					// guide tool tips
					$guide = (!empty($mb['guide']))? $foodpress->throw_guide($mb['guide'],'',false):null;

					$sluggeed_id = str_replace(' ', '_', $mb['name']);


					// data icon
					$__icon_html = (!empty($mb['iconvar']))? 
						( (!empty($fp_opt1[ $mb['iconvar'] ]))? '<i class="fpicon fa '.$fp_opt1[ $mb['iconvar'] ] .'"></i>' :null ):null;

					// each item
					echo "<li>
						<h4>".$__icon_html.'<span class="txt">'.fp_get_language($mb['name'], $fp_opt2).$guide." </span></h4>
						<div data-box-id='".$mb['slug']."' class='meta_value foodpress_metafield ". ((!empty($closedmeta) && in_array($mb['slug'], $closedmeta))? 'closed':null )."'>";
					
					$value = (!empty($fmeta[$mb['id']][0]))? $fmeta[$mb['id']][0]:'';
					
					// special note for description
						if($mb['slug']=='description')
							echo "<p><i>".__('NOTE: When copying & pasting HTML content in here be very careful that you are copying complete & properly closed HTML code otherwise it will break the menu HTML on frontend.','foodpress')."</i></p>";
					
					// Conditional Statement
					if(empty($mb['code'])){							
						wp_editor( $value , $mb['id'] , array('media_buttons'=>false) );

					// select field
					}else if($mb['type']=='select'){
						
						echo '<select name="'.$mb['id'].'" title="'.__($mb['name'],'foodpress').'" >';
							
						foreach($mb['options'] as $option=>$option_val){
							$selected = ($value == $option)? 'selected="selected"':null;
							echo '<option value="'.$option.'" '.$selected.'>'.$option_val.'</option>';
						}
						
						echo '</select>';
					
					// single text box
					}else if($mb['type']=='single'){
						echo '<input class="fp_input_fullWidth" type="text" name="'.$mb['id'] .'" placeholder="'. (!empty($mb['placeholder'])? $mb['placeholder']: null).'" value="'.$value.'" />';

					// custom field
					}else if($mb['type']=='custom_field'){


						$__values = (!empty($fmeta['fp_ec_f'.$mb['x'] ]))? $fmeta['fp_ec_f'.$mb['x'] ][0]: null;
						
						
						if($mb['field_type']=='textarea'){
							wp_editor($__values, 'fp_ec_f'.$mb['x']);
						}else if($mb['field_type']=='menuadditions'){

							$menuads = new WP_Query( array('post_type'=>'menu-additions', 'posts_per_page'=>-1));
							if($menuads->have_posts()){
								echo "<select name='".'fp_ec_f'.$mb['x'] ."'> ";
									echo "<option value='--'>None</option>";
								while($menuads->have_posts()): $menuads->the_post();
									$__id = get_the_ID();
									echo "<option value='".$__id."' ". ( $__values ==$__id ? 'selected="selected"':null) . ">".get_the_title()."</option>";
								endwhile;
								wp_reset_postdata();
								echo "</select> ".__('Select Data from Menu Additions','foodpress')."<br/><i style='padding-top:10px;display:inline-block'><a href='".get_admin_url()."edit.php?post_type=menu-additions'>".__('Edit Menu Additions','foodpress')."</a></i>".$guide;
							}else{
								echo __("There are no menu additions.",'foodpress') .' '. "<i><a href='".get_admin_url()."edit.php?post_type=menu-additions'>".__('Create Menu Additions', 'foodpress')."</a></i>"; 
							}

						}else{
							echo '<input class="fp_input_fullWidth" type="text" name="fp_ec_f'.$mb['x'] .'" title="'.__('Enter the custom value here', 'foodpress').'" value="'.$__values.'" >';
						}

					}else{
						echo $mb['code'];
					}
					
					echo "</div></li>";

					endif;
				}	
				
			
			?>
		</ul>
		
		

<?php }
	
	
/**
 * Save the menu data meta box.
 **/
function foodpress_save_meta_data($post_id, $post){
	global $pagenow;

	if ( empty( $post_id ) || empty( $post ) ) return;
	if($post->post_type!='menu') return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	// Prevent quick edit from clearing custom fields
	if (defined('DOING_AJAX') && DOING_AJAX) return;
	if ( is_int( wp_is_post_revision( $post ) ) ) return;
	if ( is_int( wp_is_post_autosave( $post ) ) ) return;
	
	// verify this came from the our screen and with proper authorization,
	// because save_post can be triggered at other times
	if( isset($_POST['fp_noncename']) ){
		if ( !wp_verify_nonce( $_POST['fp_noncename'], plugin_basename( __FILE__ ) ) ){
			return;
		}
	}


	$_allowed = array( 'post-new.php', 'post.php' );
	if(!in_array($pagenow, $_allowed)) return;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );
	
	
	// Check permissions
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return;	

		
	
	
	//save the post meta values
	//$fields_ar =apply_filters();
	
	$meta_fields = foodpress_menu_metabox_array();
	
	
	// run through all the custom meta fields
	foreach($meta_fields as $mb=>$f_val){
		
		if(!empty($f_val)){
			if( $f_val['type']=='multiinput'){
				foreach($f_val['ids'] as $fvals){
					fp_individual_post_values($fvals, $post_id);
				}
			}else{
				fp_individual_post_values($f_val['id'], $post_id);
			}

		}
	}

	//update_post_meta($post_id, 'test', $_POST['fp_menuicons']);
	
	// save user closed meta field boxes
	$fp_closemeta_value = (isset($_POST['fp_collapse_meta_boxes']))? $_POST['fp_collapse_meta_boxes']: '';
	
	foodpress_save_collapse_metaboxes($post_id, $fp_closemeta_value );
	
	
	
	
	// (---) hook for addons
	do_action('foodpress_save_meta',  $post_id);	
		
}
	
// process saving or deleting post meta values
function fp_individual_post_values($val, $post_id){
	if(!empty ($_POST[$val])){
				
		$post_value = ( $_POST[$val]);
		update_post_meta( $post_id, $val,$post_value);
					
	}else{
		if(defined('DOING_AUTOSAVE') && !DOING_AUTOSAVE){
			// if the meta value is set to empty, then delete that meta value
			delete_post_meta($post_id, $val);
		}
		delete_post_meta($post_id, $val);
	}
}


	

?>