<?php
/**
 * menu card functions
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/admin/includes
 * @version     1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


function foodpress_menucard_print($array, $fp_options, $fp_options_2, $pmv='', $args=''){

	// initial values
		$__content_filter = ( !empty($fp_options['fp_dis_conFilter']) && $fp_options['fp_dis_conFilter']=='yes')? true:false;
		$lang = !empty($args['lang'])? $args['lang']:'L1';

	$OT='';
	$items = count($array);
	$count=1;

	// additional fields array
	$_additions = apply_filters('fp_menucard_adds' , array());

	// FOREACH
	foreach($array as $box_f=>$box){

		if($box_f!='custom_fields'){
			// convert to an object
			$object = new stdClass();
			foreach ($box as $key => $value){
				$object->$key = $value;
			}
		}

		$boxname = (in_array($box_f, $_additions))? $box_f: null;

		// EACH menu card types
		switch($box_f){

			// pluggable additions
				case has_filter("foodpress_menuCard_{$boxname}"):

					$helpers = array(
						'fpOPT'=>$fp_options,
						'fpoOPT2'=>$fp_options_2,
					);

					$OT.= apply_filters("eventon_eventCard_{$boxname}", $object, $helpers);

				break;

			// header image
				case 'header':

					ob_start();
					echo "<div class='fp_popup_img fp_header ". (!empty($object->imgurl)?'image':'noimg')."'>";

					if(!empty($object->imgurl))
						echo "<img src='{$object->imgurl}'/>";

					echo "<div class='fp_pop_headerS'>";
					if(!empty($object->title)){
						echo "<span class='fp_popup_img_title'>". $object->title ."</span>";
						if($object->subtitle) echo "<span class='fp_popup_img_subtitle'>". $object->subtitle ."</span>";
					}

					if(!empty($object->price)){
						echo "<span class='fp_popup_img_price'>". apply_filters('foodpress_price_value',$object->price, $pmv) ."</span>";
					}
					echo "</div>";

					echo "</div>";

					$OT.= ob_get_clean();
				break;

			// menu details section
				case 'details':
					$OT.= "<div class='fp_inner_box fp_details'>";
					if(!empty($object->terms)):
						$OT.="<p class='fp_menu_type'>". $object->terms ."</p>";
					endif;
							$OT.= "<h3>". $object->title ."</h3>";
							$OT.= "<div class='menu_description'>". ((!$__content_filter)? apply_filters('the_content', $object->description): $object->description)
							."</div>";

							if($object->additionaltext) $OT.= "<p class='menu_additional_details'>". $object->additionaltext ."</p>";
						$OT.="</div>";
				break;

			// ingredients
				case 'ingredients':

					$OT.= "<div class='fp_popup_option fp_ingredients tint iconrow'>
							<span class='fp_menudata_icon'><i title='". $object->title ."' class='fa ".foodpress_opt_val($fp_options, 'fp__f2','fa-book' ) ."'></i></span>
							<div class='fp_inner_box'>
								<h4 class='fp_popup_option_title'>". $object->title ."</h4>
								<div class='clear'></div>
								<div class='fp_text ffgeo'>". ((!$__content_filter)? apply_filters('the_content',$object->content):$object->content) ."</div>
							</div>
						</div>";

				break;

			// nutritions
				case 'nutritions':
					$OT.= "<div class='fp_popup_option tint iconrow'>
							<span class='fp_menudata_icon'><i title='". $object->title ."' class='fa ".foodpress_opt_val($fp_options, 'fp__f3','fa-cutlery' ) ."'></i></span>
							<div class='fp_inner_box'>
								<h4 class='fp_popup_option_title'>". $object->title ."</h4>

								<div class='fp_nutritions'>
									<p class='fp_text'>". $object->left ."</p><p class='fp_text'>". $object->right ."</p><div class='clear'></div>
								</div>
							</div>
						</div>";
				break;

			case 'spicelevel':

				$title = fp_get_language('Spicy Level', $fp_options_2, $lang);

				if(!empty($fp_options['fs_spicemeter_style']) && $fp_options['fs_spicemeter_style']=='bar'){
					$spiceperc = (int)(($object->level/5)*100);
					$spice_icon = foodpress_opt_val($fp_options, 'fp_icon_spice','fp-chili-alt' );

					$OT.= "<div class='fp_popup_option tint iconrow'>
							<span class='fp_menudata_icon'><i title='". $title ."' class='fa {$spice_icon}'></i></span>
							<div class='fp_inner_box'>
								<h4 class='fp_popup_option_title spicemeter2'>". $title .' <dt>'.$object->level ."/5</dt> <span><em style='width:{$spiceperc}%'></em></span></h4>
							</div>
						</div>";
				}else{ // default style
					$before='';
					for($x=1; $x <= ($object->level-1); $x++){
						$before.= '<p class="spix_'.($x).'"></p>';
					}
					$after='';
					for($x=1; $x <= (5- $object->level); $x++){
						$after.= '<p class="spix_'.($x+$object->level).'"></p>';
					}
					$OT.= "<div class='fp_popup_option tint'>
						<div class='fp_spicebox spix_". $object->level ."'>
							<div class='spice_before spix_".($object->level-1) ."'>". $before."</div>
							<div class='spice_this'><p class='spice_text brbox'>".fp_get_language('Spicy Level', $fp_options_2, $lang) ." ".  $object->level ."/5</p></div>
							<div class='spice_after spix_".(5-$object->level) ."'>". $after ."</div>
							<div class='clear'></div>
						</div>
					</div>";
				}

			break;
		}// endswitch


		// for custom meta data fields
			if(!empty($object->x) && $box_f == 'customfield'.$object->x){

				$status = true;

				// content
				if($object->content_type =='menuadditions'){
					if(!empty($object->content)){
						//$OT.=$object->content;
						$menuadd = get_post($object->content);
						$__this_content = (!empty($menuadd->post_content)?
							((!$__content_filter)? apply_filters('the_content',$menuadd->post_content): $menuadd->post_content):'');
					}else{$status = false;	}
				}else{
					$__this_content = (!$__content_filter)?
						apply_filters('the_content',$object->content): $object->content;
				}

				if($status):
				$OT.= "<div class='fp_popup_option tint iconrow'>
							<span class='fp_menudata_icon'><i title='". $object->title ."' class='fa ". $object->iconname ."'></i></span>
							<div class='fp_inner_box'>
								<h4 class='fp_popup_option_title'>". $object->title ."</h4>
								<div class='clear'></div>
								<div class='fp_text ffgeo'>".$__this_content ."</div>
							</div>
						</div>";
				endif;
			}


		$count++;
	}// endforeach

	return $OT;
}
