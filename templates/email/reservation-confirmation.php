<?php
/**
 * Confirmation Email to customer
 *
 * @version 	2.0
 */


	global $foodpress;

	echo $foodpress->get_email_part('header');

	$fp_options = get_option('fp_options_food_1');
	$fp_options_2 = get_option('fp_options_food_2');
	$opt6 = get_option('fp_options_food_6');

	$args = $args;

	$lang = !empty($args['lang'])?$args['lang']: 'L1';

	// for preview vs actual
	if(!empty($args['temp']) && $args['temp']){
		$args = $r_pmv = $args['args'];	
	}else{
		$r_pmv = !empty($args['reservation_id'])? get_post_custom($args['reservation_id'] ):false;
		$args = $args;
	}
	

	//styles
		$__styles_date = "font-size:48px; color:#ABABAB; font-weight:bold; margin-top:5px";
		$__styles_em = "font-size:14px; font-weight:bold; text-transform:uppercase; display:block;font-style:normal";
		$__styles_button = "font-size:14px; background-color:#".( !empty($fp_options['fp_gen_btn_bgc'])? $fp_options['fp_gen_btn_bgc']: "237ebd")."; color:#".( !empty($fp_options['fp_gen_btn_fc'])? $fp_options['fp_gen_btn_fc']: "ffffff")."; padding: 5px 10px; text-decoration:none; border-radius:4px; ";
		$__styles_01 = "font-size:30px; color:#303030; font-weight:bold; text-transform:uppercase; margin-bottom:0px;  margin-top:0;";
		$__styles_02 = "font-size:18px; color:#303030; font-weight:normal; text-transform:uppercase; display:block; font-style:italic; margin: 4px 0; line-height:110%;";
		$__sty_lh = "line-height:110%;";
		$__styles_02a = "color:#afafaf; text-transform:none";
		$__styles_03 = "color:#afafaf; font-style:italic;font-size:14px; margin:0 0 10px 0;";
		$__styles_04 = "color:#303030; text-transform:uppercase; font-size:18px; font-style:italic; padding-bottom:0px; margin-bottom:0px; line-height:110%;";
		$__styles_05 = "padding-bottom:40px; ";
		$__styles_06 = "border-bottom:1px dashed #d1d1d1; padding:5px 20px";
		$__sty_td ="padding:0px;border:none";
		$__sty_m0 ="margin:0px;";
		
		// reused elements
		$__item_p_beg = "<p style='{$__styles_02}'><span style='{$__styles_02a}'>";
?>
<?php
	//print_r($args);
?>
<table width='100%' style='width:100%; margin:0;font-family:"open sans"'>
	<tr>
		<td style='<?php echo $__sty_td;?>'>			
			<div style="padding:20px; font-family:'open sans'">
				<p style='<?php echo $__sty_lh;?>font-size:18px; font-style:italic; margin:0'><?php echo foodpress_get_custom_language( $fp_options_2,'reservation_confirmation', 'Reservation confirmation!',$lang)?></p>
				
				<p style='<?php echo $__styles_02;?> padding-top:15px;'><span style='<?php echo $__styles_02a;?>'><?php echo foodpress_get_custom_language( $fp_options_2,'fprsvp_001', 'Reservation ID',$lang)?>:</span> # <?php echo $args['reservation_id'];?></p>

				<?php echo $__item_p_beg;?><?php echo foodpress_get_custom_language( $fp_options_2,'reservation_time', 'Reservation Time',$lang)?>:</span> <?php echo $r_pmv['date'][0].' '.$r_pmv['time'][0];?><?php echo !empty($r_pmv['end_time'])? '-'.$r_pmv['end_time'][0]:null;?></p>

				<?php echo $__item_p_beg;?><?php echo foodpress_get_custom_language( $fp_options_2,'primary_content', 'Primary Contact',$lang)?>:</span> <?php echo (!empty($r_pmv['name'])? $r_pmv['name'][0]:null);?></p>

				<?php if(!empty($r_pmv['phone'])): echo $__item_p_beg;?><?php echo foodpress_get_custom_language( $fp_options_2,'fprsvp_006', 'Phone Number',$lang)?>:</span> <?php echo $r_pmv['phone'][0];?></p><?php endif;?>


				<?php echo $__item_p_beg;?><?php echo foodpress_get_custom_language( $fp_options_2,'party_size', 'Party Size',$lang)?>:</span> <?php echo (!empty($r_pmv['people'])? $r_pmv['people'][0]:null);?></p>

				<?php
					 for($x=1; $x<=foodpress_get_reservation_form_fields(); $x++){
				    	if( !empty($opt6['fp_af_'.$x]) && $opt6['fp_af_'.$x]=='yes' && !empty($opt6['fp_ec_f'.$x]) ){

				    		$field_type = (!empty($opt6['fp_ec_fb'.$x])? $opt6['fp_ec_fb'.$x]:'text');
			    			$meta_value = (!empty($r_pmv['fp_af_'.$x]))? $r_pmv['fp_af_'.$x][0]: '-';

			    			// field value languaged
			    			echo $__item_p_beg. foodpress_get_custom_language($fp_options_2,'fp_ec_f'.$x, $opt6['fp_ec_f'.$x], $lang) .": </span>";				
			    			switch($field_type){
			    				case 'text':
			    					echo $meta_value;
			    				break;case 'multiline':
			    					echo $meta_value;
			    				break;
			    				case 'select':
									$values = $opt6['fp_ec_fv'.$x];
									if(!empty($values)){ // if field values present
										echo $meta_value;
									}
									break;
								case 'checkbox':
									$checked = ($meta_value=='yes')? 'Yes':'No';
									echo $checked;
									break;

			    			}
			    			echo "</p>";
						}
					}
				?>
				
			</div>
		</td>
	</tr>
	<tr>
		<td  style='padding:20px; text-align:left;border-top:1px dashed #d1d1d1; font-style:italic; color:#ADADAD'>
			<?php
				$__link = (!empty($fp_options['fprs_contact_link']))? $fp_options['fprs_contact_link']:site_url();
			?>
			<p style='<?php echo $__sty_lh.$__sty_m0;?> padding-bottom:5px;'><?php echo foodpress_get_custom_language( $fp_options_2,'we_look_forward_to_seeing_you', 'We look forward to seeing you!',$lang)?></p>
			<p style='<?php echo $__sty_lh.$__sty_m0;?>'><a style='' href='<?php echo $__link;?>'><?php echo foodpress_get_custom_language( $fp_options_2,'contact_us_for_questions_and_concerns', 'Contact Us for questions and concerns',$lang)?></a></p>
		</td>
	</tr>
</table>


<?php
	echo $foodpress->get_email_part('footer');
?>

