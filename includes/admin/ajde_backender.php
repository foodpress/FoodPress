<?php
/**
 * AJDE Backender 
 * print out back end customization form set up for the plugin settings
 * 
 * @version 2.11
 * @updated 2015-11
 */

/** Store settings in this array */
global $print_ajde_customization_form;


if ( ! function_exists( 'print_ajde_customization_form' ) ) {
function print_ajde_customization_form($cutomization_pg_array, $fpOPT, $extra_tabs=''){

	global $foodpress;

	$font_sizes = array('10px','11px','12px','13px','14px','16px','18px','20px', '22px', '24px','28px','30px','36px','42px','48px','54px','60px');
		$opacity_values = array('0.0','0.1','0.2','0.3','0.4','0.5','0.6','0.7','0.8','0.9','1',);
		$font_styles = array('normal','bold','italic','bold-italic');

	$__no_hr_types = array('begin_afterstatement','end_afterstatement','hiddensection_open','hiddensection_close');
	
	//define variables
	$leftside=$rightside='';
	$count=1;
	
	foreach($cutomization_pg_array as $cpa=>$cpav){								
		// left side tabs with different level colors
		$ls_level_code = (isset($cpav['level']))? 'class="'.$cpav['level'].'"': null;
		
		$leftside .= "<li ".$ls_level_code."><a class='".( ($count==1)?'focused':null)."' data-c_id='".$cpav['id']."' title='".$cpav['tab_name']."'>".$cpav['tab_name']."</a></li>";								
		$tab_type = (isset($cpav['tab_type'] ) )? $cpav['tab_type']:'';
		if( $tab_type !='empty'){ // to not show the right side
			
			// RIGHT SIDE
			$display_default = (!empty($cpav['display']) && $cpav['display']=='show')?'':'display:none';
			
			$rightside.= "<div id='".$cpav['id']."' style='".$display_default."' class='nfer'>
				<h3 style='margin-bottom:15px' >".$cpav['name']."</h3>
				<em class='hr_line'></em>";

				if($cpav['id'] == 'food_003'){
					// color selector guide box
					$rightside.= "<div style='display:none' id='fp_color_guide'>Testing</div>";
				}else{	
					// font awesome
					require_once('fa_fonts.php');
	
					$rightside.= "<div style='display:none' class='fa_icons_selection'><div class='fai_in'><ul class='faicon_ul'>";
					foreach($font_ as $fa){
						$rightside.= "<li><i title='{$fa}' data-name='".$fa."' class='fa ".$fa."'></i></li>";
					}

					$rightside.= "</ul>";
					$rightside.= "</div></div>";
				}
				
			// EACH FEILD
			foreach($cpav['fields'] as $field){

				if(empty($field['type'])) continue;
				
				// LEGEND
				$legend_code = (!empty($field['legend']) )? 
						$foodpress->throw_guide($field['legend'], 'L', false):null;

				switch ($field['type']){

					// notices
					case 'notice':
						$rightside.= "<div class='evos_notice'>".__($field['name'],'eventon')."</div>";
					break;
					case 'code':
						$rightside.= $field['value'];
					break;
					// custom code
					case 'customcode':						
						$rightside.=$field['code'];						
					break;
					case 'subheader':
						$rightside.= "<h4 class='acus_subheader'>".__($field['name'],'eventon')."</h4>";
					break;
					case 'note':
						$rightside.= "<p class='foodpress_note'><i>".$field['name']."</i></p>";
					break;
					case 'hr': $rightside.= "<em class='hr_line'></em>"; break;

					case 'timeslots':
						ob_start();

							$slots = (!empty($fpOPT[$field['id']]))? $fpOPT[$field['id']]:null;

						?>
							<p class="fpr_meal_subtitle"><?php echo $field['tooltip']?></p>
							<div class='fpr_timeslots'>
								<input type='hidden' name='<?php echo $field['id']?>' value='<?php echo $slots;?>'/>
								<div class='fpr_timeslot_in'>
									<?php
										if(!empty($slots)){
											$_slots = explode(',', $slots);
											foreach($_slots as $_s){
												echo "<span class='fpr_meal_time_slot'>".$_s."</span>";
											}
										}
									?>
									<span class='fpr_meal_time_slot'></span>
									</div>
								<div class='clear'></div>
								<span class='fpr_add_slot'>+ Add new time slot</span>
							</div>
						<?php
						$rightside.= ob_get_clean();

					break;
					
					//IMAGE
					case 'image':
						$image = ''; 
						$meta = (!empty($fpOPT[$field['id']]))? $fpOPT[$field['id']]:null;
						
						$preview_img_size = (empty($field['preview_img_size']))?'medium'
							: $field['preview_img_size'];
						
						$rightside.= "<div id='pa_".$field['id']."'><p class='foodpress_img'>".$field['name'].$legend_code."</p>";
						$rightside.= '<span class="custom_default_image" style="display:none">'.$image.'</span>';  
						
						if ($meta) { $image = wp_get_attachment_image_src($meta, $preview_img_size); $image = $image[0]; } 
						
						$img_code = (empty($image))? "<p class='custom_no_preview_img'><i>No Image Selected</i></p><img id='ev_".$field['id']."' src='' style='display:none' class='custom_preview_image' />"
							: '<p class="custom_no_preview_img" style="display:none"><i>No Image Selected</i></p><img src="'.$image.'" class="custom_preview_image" alt="" />';
						
						$rightside.= '<input name="'.$field['id'].'" type="hidden" class="custom_upload_image" value="'.$meta.'" />'.$img_code.'<br />';
							
						$display_choose = (empty($image))?'block':'none';
						$display_remove = (empty($image))?'none':'block';
						
						$rightside.='<input style="display:'.$display_choose.'" parent="pa_'.$field['id'].'" class="custom_upload_image_button button" type="button" value="Choose Image" />
							<small > <a href="#" style="display:'.$display_remove.'" class="custom_clear_image_button">Remove Image</a></small> 
							<br clear="all" /></div>';
					break;

					case 'icon':

						$field_value = (!empty($fpOPT[ $field['id']]) )? 
							$fpOPT[ $field['id']]:$field['default'];

						$rightside.= "<div class='row_faicons'><p class='fieldname'>".__($field['name'],'foodpress')."</p>";
						// code
						$rightside.= "<p class='acus_line faicon'>
							<i class='fa ".$field_value."'></i>
							<input name='".$field['id']."' class='backender_colorpicker' type='hidden' value='".$field_value."' /></p>";
						$rightside.= "<div class='clear'></div></div>";

					break;
					
					case 'checkbox':
						$rightside.= "<p><input type='checkbox' name='".$field['id']."' value='yes' ".(($fpOPT[$field['id']]=='yes')?'checked="/checked"/':'')."/> ".$field['name']."</p>";
					break;
					case 'text':
						$this_value= (!empty($fpOPT[ $field['id']]))? stripcslashes($fpOPT[ $field['id']]): null;
						
						$default_value = (!empty($field['default']) )? 'placeholder="'.$field['default'].'"':null;
						
						$rightside.= '<p>'.__($field['name'],'foodpress').' '.$legend_code.'</p><p><span class="nfe_f_width"><input type="text" name="'.$field['id'].'" value="'.$this_value.'" '.$default_value.'/></span></p>';
					break;
					case 'textarea':
						
						$textarea_value= (!empty($fpOPT[ $field['id']]))?$fpOPT[ $field['id']]:null;
						
						$rightside.= "<p>".__($field['name'],'foodpress')."</p><p><span class='nfe_f_width'><textarea name='".$field['id']."'>".$textarea_value."</textarea></span></p>";
					break;
					case 'font_size':
						$rightside.= "<p>".__($field['name'],'foodpress')." <select name='".$field['id']."'>";
								
								$f1_fs = (!empty($fpOPT[ $field['id'] ]))?
									$fpOPT[ $field['id'] ]:$field['default'] ;
								
								foreach($font_sizes as $fs){
									$selected = ($f1_fs == $fs)?"selected='selected'":null;	
									$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
								}
						$rightside.= "</select></p>";
					break;
					case 'font_style':
						$rightside.= "<p>".__($field['name'],'foodpress')." <select name='".$field['id']."'>";
								$f1_fs = (!empty($fpOPT[ $field['id'] ]))?
									$fpOPT[ $field['id'] ]:$field['default'] ;
								foreach($font_styles as $fs){
									$selected = ($f1_fs == $fs)?"selected='selected'":null;	
									$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
								}
						$rightside.= "</select></p>";
					break;
					case 'border_radius':
						$rightside.= "<p>".__($field['name'],'foodpress')." <select name='".$field['id']."'>";
								$f1_fs = $fpOPT[ $field['id'] ];
								$border_radius = array('0px','2px','3px','4px','5px','6px','8px','10px');
								foreach($border_radius as $br){
									$selected = ($f1_fs == $br)?"selected='selected'":null;	
									$rightside.=  "<option value='$br' ".$selected.">$br</option>";
								}
						$rightside.= "</select></p>";
					break;
					case 'color':
						
						// default hex color
						$hex_color = (!empty($fpOPT[ $field['id']]) )? 
							$fpOPT[ $field['id']]:$field['default'];
						$hex_color_val = (!empty($fpOPT[ $field['id'] ]))? $fpOPT[ $field['id'] ]: null;

						// RGB Color for the color box
						$rgb_color_val = (!empty($field['rgbid']) && !empty($fpOPT[ $field['rgbid'] ]))? $fpOPT[ $field['rgbid'] ]: null;
						$__em_class = (!empty($field['rgbid']))? ' rgb': null;
						
						// code
						$rightside.= "<p class='acus_line color'>
							<em><span class='colorselector{$__em_class}' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$hex_color."'></span>
							<input name='".$field['id']."' class='backender_colorpicker' type='hidden' value='".$hex_color_val."' data-default='".$field['default']."'/>";
						if(!empty($field['rgbid'])){
							$rightside .= "<input name='".$field['rgbid']."' class='rgb' type='hidden' value='".$rgb_color_val."' />";
						}

						$rightside.= "</em>".__($field['name'],'foodpress')." </p>";
						
					break;

					case 'fontation':

						$variations = $field['variations'];
						$rightside.= "<div class='row_fontation'><p class='fieldname'>".__($field['name'],'foodpress')."</p>";

						foreach($variations as $variation){
							switch($variation['type']){
								case 'color':
									// default hex color
									$hex_color = (!empty($fpOPT[ $variation['id']]) )? 
										$fpOPT[ $variation['id']]:$variation['default'];
									$hex_color_val = (!empty($fpOPT[ $variation['id'] ]))? $fpOPT[ $variation['id'] ]: null;
										
									$title = (!empty($variation['title']))? $variation['title']:$hex_color;
									$_has_title = (!empty($variation['title']))? true:false;

									// code
									$rightside.= "<p class='acus_line color'>
										<em><span class='colorselector ".( ($_has_title)? 'hastitle': '')."' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$title."' alt='".$title."'></span>
										<input name='".$variation['id']."' class='backender_colorpicker' type='hidden' value='".$hex_color_val."' data-default='".$variation['default']."'/></em></p>";

								break;

								case 'font_style':
									$rightside.= "<p><select title='".__('Font Style','foodpress')."' name='".$variation['id']."'>";
											$f1_fs = (!empty($fpOPT[ $variation['id'] ]))?
												$fpOPT[ $variation['id'] ]:$variation['default'] ;
											foreach($font_styles as $fs){
												$selected = ($f1_fs == $fs)?"selected='selected'":null;	
												$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
											}
									$rightside.= "</select></p>";
								break;

								case 'font_size':
									$rightside.= "<p><select title='".__('Font Size','foodpress')."' name='".$variation['id']."'>";
											
											$f1_fs = (!empty($fpOPT[ $variation['id'] ]))?
												$fpOPT[ $variation['id'] ]:$variation['default'] ;
											
											foreach($font_sizes as $fs){
												$selected = ($f1_fs == $fs)?"selected='selected'":null;	
												$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
											}
									$rightside.= "</select></p>";
								break;

								case 'opacity_value':
									$rightside.= "<p style='margin:0'><select title='".__('Opacity Value',$textdomain)."' name='".$variation['id']."'>";
											
											$f1_fs = (!empty($ajdePT[ $variation['id'] ]))?
												$ajdePT[ $variation['id'] ]:$variation['default'] ;
											
											foreach($opacity_values as $fs){
												$selected = ($f1_fs == $fs)?"selected='selected'":null;	
												$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
											}
									$rightside.= "</select></p>";
								break;
							}

							
						}

						$rightside.= "<div class='clear'></div></div>";

					break;

					case 'multicolor':

						$variations = $field['variations'];

						$rightside.= "<div class='row_multicolor'>";

						foreach($variations as $variation){
							// default hex color
							$hex_color = (!empty($fpOPT[ $variation['id']]) )? 
								$fpOPT[ $variation['id']]:$variation['default'];
							$hex_color_val = (!empty($fpOPT[ $variation['id'] ]))? $fpOPT[ $variation['id'] ]: null;

							$rightside.= "<p class='acus_line color'>
							<em data-name='".__($variation['name'],'foodpress')."'><span class='colorselector' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$hex_color."'></span>
							<input name='".$variation['id']."' class='backender_colorpicker' type='hidden' value='".$hex_color_val."' data-default='".$variation['default']."'/></em></p>";
						}

						$rightside.= "<div class='clear'></div><p class='multicolor_alt'></p></div>";

					break;

					case 'radio':
						$rightside.= "<p class='acus_line acus_radio'>".__($field['name'],'foodpress')."</br></br>";
						$cnt =0;
						foreach($field['options'] as $option=>$option_val){
							$this_value = (!empty($fpOPT[ $field['id'] ]))? $fpOPT[ $field['id'] ]:null;
							
							$checked_or_not = ((!empty($this_value) && ($option == $this_value) ) || (empty($this_value) && $cnt==0) )?
								'checked=\"checked\"':null;
							
							$rightside.="<em><input id='".$field['id'].$option_val."' type='radio' name='".$field['id']."' value='".$option."' "
							.  $checked_or_not  ."/><label for='".$field['id'].$option_val."'><span></span>".$option_val."</label></em>";
							
							$cnt++;
						}						
						$rightside.= "</p>";
						
					break;
					case 'dropdown':
						
						$dropdown_opt = (!empty($fpOPT[ $field['id'] ]))? $fpOPT[ $field['id'] ]:null;
						
						$rightside.= "<p class='acus_line'>".__($field['name'],'foodpress')." <select name='".$field['id']."'>";
						
						foreach($field['options'] as $option=>$option_val){
							$rightside.="<option type='radio' name='".$field['id']."' value='".$option."' "
							.  ( ($option == $dropdown_opt)? 'selected=\"selected\"':null)  ."/> ".$option_val."</option>";
						}						
						$rightside.= "</select>";

							// description text for this field
							if(!empty( $field['desc'] )){
								$rightside.= "<br/><i style='opacity:0.6'>".$field['desc']."</i>";
							}
						$rightside.= "</p>";

					break;
					case 'checkboxes':
						
						$meta_ar = (!empty($fpOPT[ $field['id'] ]) )? $fpOPT[ $field['id'] ]: null;
						$meta_arr= $meta_ar;
						
						$rightside.= "<p class='acus_line acus_checks'>".__($field['name'],'foodpress')."<br/><br/> ";
						
						foreach($field['options'] as $option=>$option_val){
							$checked='';
							if(is_array($meta_arr)){
								$checked = (in_array($option, $meta_arr))?'checked':'';
							}
							
							$rightside.="<span><input id='".$field['id'].$option_val."' type='checkbox' name='".$field['id']."[]' value='".$option."' ".$checked."/><label for='".$field['id'].$option_val."'><span></span>".$option_val."</label></span>";							
						}						
						$rightside.= "</p>";						
					break;

					// rearrange field
						// fields_array - array(key=>var)
						// order_var
						// selected_var
						// title
						// (o)notes
					case 'rearrange':

						ob_start();
							$_ORDERVAR = $field['order_var'];
							$_SELECTEDVAR = $field['selected_var'];
							$_FIELDSar = $field['fields_array']; // key(var) => value(name)

							
							// saved order
							if(!empty($fpOPT[$_ORDERVAR])){								
								
								$allfields_ = explode(',',$fpOPT[$_ORDERVAR]);
								$fieldsx = array();
								//print_r($allfields_);
								foreach($allfields_ as $fielders){									
									if(!in_array($fielders, $fieldsx)){
										$fieldsx[]= $fielders;
									}
								}
								//print_r($fieldsx);
								$allfields = implode(',', $fieldsx);

								$SAVED_ORDER = array_filter(explode(',', $allfields));
								
							}else{
								$SAVED_ORDER = false;
								$allfields = '';
							}

							$SELECTED = (!empty($fpOPT[$_SELECTEDVAR]))?
								( (is_array( $fpOPT[$_SELECTEDVAR] ))?
									$fpOPT[$_SELECTEDVAR]:
									array_filter( explode(',', $fpOPT[$_SELECTEDVAR]))):
								false;

							$SELECTED_VALS = (is_array($SELECTED))? implode(',', $SELECTED): $SELECTED;

							echo '<h4 class="acus_subheader">'.$field['title'].'</h4>';
							echo !empty($field['notes'])? '<p><i>'.$field['notes'].'</i></p>':'';
							echo '<input class="ajderearrange_order" name="'.$_ORDERVAR.'" value="'.$allfields.'" type="hidden"/>
								<input class="ajderearrange_selected" type="hidden" name="'.$_SELECTEDVAR.'" value="'.( (!empty($SELECTED_VALS))? $SELECTED_VALS:null).'"/>
								<div id="ajdeEVC_arrange_box" class="ajderearrange_box '.$field['id'].'">';

							// if an order array exists already
							if($SAVED_ORDER){
								// for each saved order
								foreach($SAVED_ORDER as $VAL){
									if(!isset($_FIELDSar[$VAL])) continue;

									$FF = (is_array($_FIELDSar[$VAL]))? 
										$_FIELDSar[$VAL][1]:
										$_FIELDSar[$VAL];
									echo (array_key_exists($VAL, $_FIELDSar))? 
										"<p val='".$VAL."'><span class='fa ". ( !empty($SELECTED) && in_array($VAL, $SELECTED)?''
											:'hide') ."'></span>".$FF."</p>":	null;
								}	
								
								// if there are new values in possible items add them to the bottom
								if(count($SAVED_ORDER) < count($_FIELDSar)){						
									foreach($_FIELDSar as $f=>$v){
										$FF = (is_array($v))? $v[1]:$v;
										echo (!in_array($f, $SAVED_ORDER))? 
											"<p val='".$f."'><span class='fa ". ( !empty($SELECTED) && in_array($f, $SELECTED)?'':'hide') ."'></span>".$FF."</p>": null;
									}
								}			
							}else{
							// if there isnt a saved order	
								foreach($_FIELDSar as $f=>$v){
									$FF = (is_array($v))? $v[1]:$v;
									echo "<p val='".$f."'><span class='fa ". ( !empty($SELECTED) && in_array($f, $SELECTED)?'hide':'') ."'></span>".$FF."</p>";
								}				
							}

							echo "</div>";

						$rightside .= ob_get_clean();

					break;
					
					case 'yesno':						
						$yesno_value = (!empty( $fpOPT[$field['id'] ]) )? 
							$fpOPT[$field['id']]:'no';
						
						$after_statement = (isset($field['afterstatement']) )?$field['afterstatement']:'';	

						$rightside.= "<p class='yesno_row'>".foodpress_io_yn($yesno_value, $after_statement)."<input type='hidden' name='".$field['id']."' value='".(($yesno_value=='yes')?'yes':'no')."'/><span>".__($field['name'],'foodpress').$legend_code."</span>";

							// description text for this field
							if(!empty( $field['desc'] )){
								$rightside.= "<i style='opacity:0.6; padding-top:8px; display:block'>".$field['desc']."</i>";
							}
						$rightside .= '</p>';

					break;
					case 'begin_afterstatement': 						
						$yesno_val = (!empty($fpOPT[$field['id']]))? $fpOPT[$field['id']]:'no';						
						$rightside.= "<div class='backender_yn_sec' id='".$field['id']."' style='display:".(($yesno_val=='yes')?'block':'none')."'>";
					break;
					case 'end_afterstatement': $rightside.= "</div><em class='hr_line yesnoEnd'></em>"; break;	

					// hidden section open
					case 'hiddensection_open':
						
						$__display = (!empty($field['display']) && $field['display']=='none')? 'style="display:none"':null;
						$__diclass = (!empty($field['display']) && $field['display']=='none')? '':'open';
						
						$rightside.="<div class='evoSET_hidden_open {$__diclass}'><h4>{$field['name']}{$legend_code}</h4></div>
						<div class='evoSET_hidden_body' {$__display}>";
						
					break;					
					case 'hiddensection_close':	$rightside.="</div>";	break;
				}

				// print seperatino line
				if(!empty($field['type']) && $field['type'] =='yesno'){
					$rightside.= "<em class='hr_line yesno'></em>";
				}else if(!empty($field['type']) && !in_array($field['type'], $__no_hr_types)){ $rightside.= "<em class='hr_line'></em>";
				}
				
			}		
			$rightside.= "</div>";
		}
		$count++;
	}
	
	//built out the backender section
	echo "<table id='ajde_customization'>
			<tr><td class='backender_left' valign='top'>
				<div id='acus_left'>
					<ul>".$leftside."</ul>					
				</div>
				</td><td width='100%'  valign='top'>
					<div id='acus_right' class='fp_backender_uix'>
						<p id='acus_arrow' style='top:4px'></p>
						<div class='customization_right_in'>
							".$rightside.$extra_tabs."
						</div>
					</div>
				</td></tr>
			</table>";
	
	
}
}
?>