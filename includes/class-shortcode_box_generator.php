<?php
/**
 * foodpress Admin Include
 * Include for foodpress related events in admin.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin
 * @version     0.1
 */

class foodpress_admin_shortcode_box{
	
	private $_in_select_step=false;
	private $fp_opt;
	private $fp_opt_2;

	function __construct(){
		$this->fp_opt= get_option('fp_options_food_1');
		$this->fp_opt_2= get_option('fp_options_food_2');
	}
	
	// default fields that are re-used 
		public function shortcode_default_field($key){		
			$SC_defaults = array(
				'lang'=>array(
					'name'=>'Language Variation (<a href="'.get_admin_url().'admin.php?page=foodpress&tab=evcal_2">Update Language Text</a>)',
					'type'=>'select',
					'guide'=>'Select which language variation text to use',
					'var'=>'lang',
					'default'=>'L1',
					'options'=>array('L1'=>'L1','L2'=>'L2','L3'=>'L3')
				)
			);
			
			return $SC_defaults[$key];		
		}	
	
	// ALL the fields for shortcode generator INIT
		public function get_shortcode_field_array(){

			// taxonomy names
				$mealTypeName = (!empty($this->fp_opt['fp_mty1']))?$this->fp_opt['fp_mty1']:'Meal Type';
				$dishTypeName = (!empty($this->fp_opt['fp_mty2']))?$this->fp_opt['fp_mty2']:'Dish Type';
				$menuLocaiton = fp_get_language('Location',$this->fp_opt_2 );

			// additonal taxonomy
				$__additional_f = ( !empty($this->fp_opt['fp_cusTax']) && $this->fp_opt['fp_cusTax']=='yes')? true:false;
				$cusTaxName = (!empty($this->fp_opt['fp_mty3']))?$this->fp_opt['fp_mty3']:'Custom Category';
				
				if($__additional_f){
					$cusTax_ar =array(
						'name'=>$cusTaxName.' ID',
						'type'=>'menuitemtype',
						'var'=>'menu_type_3',
						'placeholder'=>'eg. 3',
						'guide'=>'Select the ID of '.$cusTaxName.' to pull menu items for that dish type category',
						'default'=>'none',
					);
				}else{
					$cusTax_ar=null;
				}

			// ARRAY
			$shortcode_guide_array = apply_filters('foodpress_shortcode_popup', array(
				array(
					'id'=>'fp_s1',
					'name'=>'Add a Menu',
					'code'=>'add_foodpress_menu',
					'variables'=>apply_filters('foodpress_basiccal_shortcodebox', array(
						array(
							'name'=>'Custom Code',
							'type'=>'customcode',
							'value'=>$this->html___regular_item_options()
						),array(
							'name'=>'Menu Item Box min-height (px)',
							'type'=>'text',						
							'guide'=>'Pixel value for the height of the box',
							'placeholder'=>'eg. 20',
							'var'=>'boxhei',
							'default'=>'20',
						),$this->shortcode_default_field('lang'),array(
								'name'=>'Differentiate Featured Items',
								'type'=>'YN',
								'guide'=>'Select whether you like to use a different menu item style for featured items.',
								'default'=>'no',
								'var'=>'ft_dif',
								'afterstatement'=>'ft_style'
							),array(
								'name'=>'Custom Code',
								'type'=>'customcode',
								'value'=>$this->html___features_item_options(),
								'closestatement'=>'ft_style'
							)
						,array(
							'name'=>'Menu Item Order By',
							'type'=>'select',
							'options'=>array(
									'title'=>'Item Name', 
									'date'=>'Posted Date',
									'menu_order'=>'Menu Order',
									),
							'guide'=>'Select how you want to order the menu items by',
							'var'=>'orderby',
							'default'=>'name',
						),array(
							'name'=>'Menu Item Order',
							'type'=>'select',
							'options'=>array(
									'ASC'=>'ASC', 
									'DESC'=>'DESC',
									),
							'guide'=>'Select the order direction',
							'var'=>'order',
							'default'=>'asc',
						),array(
							'name'=>'Excerpt Word count',
							'type'=>'text',						
							'guide'=>'Number of word count to show for menu item description text',
							'placeholder'=>'eg. 20',
							'var'=>'wordcount',
							'default'=>'20',
						),array(
							'name'=>'User Interaction',
							'type'=>'select',
							'guide'=>'Select how you want the user to interact with menu items. Do not link to anything will only show menu items and user clicks on menu items will do nothing.',
							'options'=>apply_filters('foodpress_sc_user_interaction', array(
								'lightbox'=>'Open Lightbox',
								'none'=>'Do not link to anything',							
							)),
							'var'=>'ux',
							'default'=>'no',
						),array(
							'name'=>'Show menu last updated date',
							'type'=>'YN',						
							'guide'=>'This will show the last date a menu item was updated',
							'var'=>'show_menu_updated','default'=>'no',
						)

						,array(
							'name'=> 'Select '.$menuLocaiton.' ID',
							'type'=>'menuitemtype',
							'var'=>'menu_location',
							'placeholder'=>'eg. 3,23',
							'guide'=>'Select the ID of '.$menuLocaiton.' to show menu items only for that '.$menuLocaiton,
							'default'=>'none',
						), $cusTax_ar


						,array(
							'name'=>'Select the Menu Type',
							'type'=>'select_step',
							'options'=>array(
									'ss_1'=>'Uncategorize Menu', 
									'ss_2'=>'Categorized Menu', 
									'ss_5'=>'Specific Type Menu', 
									//'ss_3'=> $mealTypeName.' Menu',
									//'ss_4'=> $dishTypeName.' Menu',							
								),
							'guide'=>'Select the type of menu to further customize options for that particular menu type',
							'var'=>'menu_type'
						),array('type'=>'open_select_steps','id'=>'ss_1'
						),array(	'type'=>'close_select_step',	)

					// specific type ss5
						,array('type'=>'open_select_steps','id'=>'ss_5')
							,array(	'type'=>'subheader', 'name'=>'Specify just one meal type and dish type, both required.')
							,array(
								'name'=> $mealTypeName.' ID',
								'type'=>'taxselect',
								'var'=>'meal_type',
								'placeholder'=>'eg. 3',
								'guide'=>'Select the '.$mealTypeName.' to pull menu items for the meal type category',
								'default'=>'none',
							),array(
								'name'=> $dishTypeName.' ID',
								'type'=>'taxselect',
								'var'=>'dish_type',
								'placeholder'=>'eg. 3',
								'guide'=>'Select the '.$dishTypeName.' to pull menu items for the dish type category',
								'default'=>'none',
							)
						,array(	'type'=>'close_select_step')

					// SS_2 - categorized menu
						,array('type'=>'open_select_steps','id'=>'ss_2')
							,array(
								'name'=>'Custom Code',
								'type'=>'customcode',
								'value'=>$this->custom_shortcode_option_3()
							),

							array('type'=>'sectionopen', 'name'=>'de')								
								,array( // only for normal list menu
									'name'=>'Center align menu',
									'type'=>'YN',
									'guide'=>'This will center align content of menu.',
									'var'=>'tac',
									'default'=>'no',
								)
								,array('type'=>'sectionclose', 'name'=>'de')	
							,array('type'=>'sectionopen', 'name'=>'tb', 'display'=>'hide')								
								,array( // only for tabbed version
									'name'=>'Tabbed Menu focused tab term ID',
									'type'=>'text',
									'guide'=>'Set the focused term id (meal type or dish type tab term id) to focus on the tabbed view',
									'var'=>'focused_tab',
									'default'=>'no',
								)
								,array('type'=>'sectionclose', 'name'=>'tb')	

							,array(
								'name'=>'Primary Categorization By',
								'type'=>'select_step',
								'options'=>array(
									''=>__('Select','foodpress'),
									'meal_type'=>$mealTypeName, 
									'dish_type'=>$dishTypeName,					
								),
								'guide'=>'Select which category type to use as primary categorization type',
								'var'=>'primary'
							),
								// meal type only settings
								array('type'=>'open_select_steps','id'=>'meal_type'),
									array(
										'name'=> $mealTypeName.' ID',
										'type'=>'menuitemtype',
										'var'=>'meal_type',
										'placeholder'=>'eg. 3',
										'guide'=>'Select the ID of '.$mealTypeName.' to pull menu items for that meal type category',
										'default'=>'none',
									),array(
										'name'=>'Enable collapsable '.$mealTypeName.' headers',
										'type'=>'YN',
										'guide'=>'This will allow visitors to collapse '.$mealTypeName.' headers in the menu.',
										'var'=>'collapsable',
										'default'=>'no','afterstatement'=>'clps_mt_style'
									),array(
										'name'=>'Collapse '.$mealTypeName.' on-load',
										'type'=>'YN',
										'guide'=>'This will collapse the menus on page load.',
										'var'=>'collapsed',
										'default'=>'no','closestatement'=>'clps_mt_style'
									),array(
										'name'=>'Show '.$mealTypeName.' description',
										'type'=>'YN',
										'var'=>'mt_des',
										'default'=>'no',
									),array(
										'name'=>'Sub-categorize by Dish Type',
										'type'=>'YN',
										'guide'=>'Selecting this will categorize each '.$mealTypeName.' categories into '.$dishTypeName.' sub categories.',
										'var'=>'cat_by_dish',
										'default'=>'no', 'afterstatement'=>'clps_dt_style'
										
									)
										,array(
											'name'=>'Enable collapsable '.$dishTypeName.' headers',
											'type'=>'YN',
											'guide'=>'This will allow visitors to collapse '.$dishTypeName.' headers in the menu.',
											'var'=>'collapsable_dt',
											'default'=>'no',
										),array(
											'name'=>'Collapse '.$dishTypeName.' on-load',
											'type'=>'YN',
											'guide'=>'This will collapse the menus on page load.',
											'var'=>'collapsed_dt',
											'default'=>'no','closestatement'=>'clps_dt_style'
										)
								,array(	'type'=>'close_select_step')

								// dish type only settings
								,array('type'=>'open_select_steps','id'=>'dish_type')
									,array(
										'name'=> $dishTypeName.' ID',
										'type'=>'menuitemtype',
										'var'=>'dish_type',
										'placeholder'=>'eg. 3',
										'guide'=>'Select the ID of '.$dishTypeName.' to pull menu items for that dish type category',
										'default'=>'none',
									),array(
										'name'=>'Enable collapsable '.$dishTypeName.' headers',
										'type'=>'YN',
										'guide'=>'This will allow visitors to collapse '.$dishTypeName.' headers in the menu.',
										'var'=>'collapsable_dt',
										'default'=>'no',
									),array(
										'name'=>'Collapse '.$dishTypeName.' on-load',
										'type'=>'YN',
										'guide'=>'This will collapse the menus on page load.',
										'var'=>'collapsed_dt',
										'default'=>'no',
									),array(
										'name'=>'Show '.$dishTypeName.' description',
										'type'=>'YN',
										'var'=>'dt_des',
										'default'=>'no',
									)
								,array(	'type'=>'close_select_step')
						
						,array(	'type'=>'close_select_step',	)
					))
				),
				array(
					'id'=>'fp_s2',
					'name'=>'Single Menu Item',
					'code'=>'add_foodpress_menu_item',
					'variables'=>apply_filters('foodpress_shortcode_singlemenuitem', array(
						array(
							'name'=>'Item ID (integer',
							'placeholder'=>'eg. 3',
							'type'=>'text',
							'var'=>'item_id',
							'default'=>'0'
						),array(
							'name'=>'Custom Code',
							'type'=>'customcode',
							'value'=>$this->custom_shortcode_option_2()
						),array(
							'name'=>'User Interaction',
							'type'=>'select',
							'guide'=>'Select how you want the user to interact with menu items. Do not link to anything will only show menu items and user clicks on menu items will do nothing.',
							'options'=>apply_filters('foodpress_sc_user_interaction', array(
								'lightbox'=>'Open Lightbox',
								'none'=>'Do not link to anything',							
							)),
							'var'=>'ux',
							'default'=>'no',
						)
					))
				),
				array(
					'id'=>'fp_s3',
					'name'=>'Reservation Menu Form',
					'code'=>'add_reservation_form',
					'variables'=>array(
						array(
							'name'=>'Button Header Text',
							'placeholder'=>'Make a reservation now',
							'type'=>'text',
							'guide'=>'Header text on the reservation button',
							'var'=>'header',
						),array(
							'name'=>'Button Subheader Text',
							'placeholder'=>'Click here to reserve a day and time',
							'type'=>'text',
							'guide'=>'Subheader text on the reservation button',
							'var'=>'subheader',
						),array(
							'name'=>'Form Type',
							'type'=>'select',
							'guide'=>'Select how you would like the reservation form to be displayed',
							'var'=>'type',
							'options'=>array('popup'=>'PopUp', 'onpage'=>'OnPage')
						),$this->shortcode_default_field('lang')
						,array(
							'name'=>'Link for reservation',
							'placeholder'=>'http://',
							'type'=>'text',
							'guide'=>'Type a URL you want the reservation button to redirect to instead of build in form.',
							'var'=>'res_link',
						),array(
							'name'=>'Open link in new window',
							'type'=>'YN',
							'guide'=>'Open link for reservation set above in a new window',
							'var'=>'res_link_new',
							'default'=>'no',
						)
					)
				)
			));


			
			return $shortcode_guide_array;
		}
	
	// interpret the array of shortcode data into HTML output
		public function shortcode_interpret($var){
			global $foodpress;
			$line_class = array('fp_fieldline');

			ob_start();
					

			// GUIDE popup
			$guide = (!empty($var['guide']))? $foodpress->throw_guide($var['guide'], 'L',false):null;

			// afterstatemnt class
			if(!empty($var['afterstatement'])){	$line_class[]='trig_afterst'; }

			// select step class
			if($this->_in_select_step){ $line_class[]='ss_in'; }


			// -----------------------
			switch($var['type']){
				// custom type and its html pluggability
				case has_action("foodpress_shortcode_box_interpret_{$var['type']}"):
					do_action("foodpress_shortcode_box_interpret_{$var['type']}");
				
				case 'YN':
					$line_class[]='fpYN_row';
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'>".foodpress_io_yn($var['default'],'', $var['var'])."
						<span >".__($var['name'],'foodpress')."</span>".$guide."</p>							
					</div>";
				break;

				case 'customcode':
					echo $var['value'];
				break;
				
				case 'text':
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'><input class='fpPOSH_input' type='text' data-codevar='".$var['var']."' placeholder='".( (!empty($var['placeholder']))?$var['placeholder']:null) ."'/> ".__($var['name'],'foodpress')."".$guide."</p>
					</div>";
				break;

				case 'subheader':
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'>".$var['name']."</p>
					</div>";
				break;
				
				// MENU type item taxonomies
				case 'menuitemtype':
					
					$terms = get_terms($var['var']);	
					
					$view ='';
					if(!empty($terms) && count($terms)>0){
						foreach($terms as $term){
							$view.= '<em>'.$term->name .' ('.$term->term_id.')</em>';
						}
					}
					
					$view_html = (!empty($view))? '<span class="fpPOSH_tax">Values<span >'. $view .'</span></span>': '<span class="fpPOSH_tax">Values<span >You dont have any tags with menu items in it.</span></span>';
										
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'><input class='fpPOSH_input' type='text' data-codevar='".$var['var']."' placeholder='".( (!empty($var['placeholder']))?$var['placeholder']:null) ."'/> ".__($var['name'],'foodpress')." {$view_html}</p>
					</div>";
				break;

				// Menu item taxonomy select
				case 'taxselect':					
					$terms = get_terms($var['var']);
					
					$view ='';
					if(!empty($terms) && count($terms)>0){
						$view.="<option>".__('Select','foodpress')."</option>";
						foreach($terms as $term){
							$view.= '<option value="'.$term->term_id.'">'.$term->name .' ('.$term->term_id.')</option>';
						}
					}

					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'>
							<select class='fpPOSH_select' data-codevar='{$var['var']}'>{$view}
						</select> ".__($var['name'],'foodpress')." {$guide}</p>
					</div>";
				break;
				
				case 'select':
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label'>
							<select class='fpPOSH_select' data-codevar='".$var['var']."'>";
							
							foreach($var['options'] as $f=>$val){
								echo "<option value='".$f."'>".$val."</option>";
							}		
							
							echo 
							"</select> ".__($var['name'],'foodpress')."".$guide."</p>
					</div>";
				break;

				// select steps
				case 'select_step':
					$line_class[]='select_step_line';
					echo 
					"<div class='".implode(' ', $line_class)."'>
						<p class='label '>
							<select class='fpPOSH_select_step' data-codevar='".$var['var']."'>";
							
							foreach($var['options'] as $f=>$val){
								echo (!empty($val))? "<option value='".$f."'>".$val."</option>":null;
							}		
							echo 
							"</select> ".__($var['name'],'foodpress')."".$guide."</p>
					</div>";
				break;

				case 'open_select_steps':
					echo "<div id='".$var['id']."' class='fp_open_ss' style='display:none' data-step='".$var['id']."' >";
					$this->_in_select_step=true;	// set select step section to on
				break;

				case 'close_select_step':	echo "</div>";	$this->_in_select_step=false; break;
				
			}// end switch

			// afterstatement
			if(!empty($var['afterstatement'])){
				echo "<div class='fp_afterst ".$var['afterstatement']."' style='display:none'>";
			}

			// closestatement
			if(!empty($var['closestatement']) || (!empty($var['type']) && $var['type']=='sectionclose' )  ){
				echo "</div>";
			}

			// section only open
			if(!empty($var['type']) && $var['type']=='sectionopen'){
				$display = (!empty($var['display']) && $var['display']=='hide')? 'none':'block';
				echo "<div class='fp_section ".$var['name']."' style='display:{$display}'>";
			}
			
			return ob_get_clean();
		}
	
	// RETURN: HTML inside shortcode generator
		public function get_content(){
			
			$shortcode_guide_array = $this->get_shortcode_field_array();
			
			ob_start();
			?>
			
			<div id='fpPOSH_outter'>
				<h3 class='notifications '><em id='fpPOSH_back'></em><span data-bf='Select option below to customize shortcode variable values'>Select option below to customize shortcode variable values</span></h3>
				<div class='fpPOSH_inner'>
					<div class='step1 steps'>
					<?php					
						foreach($shortcode_guide_array as $options){
							$__step_2 = (empty($options['variables']))? ' nostep':null;
							
							echo "<div class='fpPOSH_btn{$__step_2}' data-step2='".$options['id']."' data-code='".$options['code']."'>".$options['name']."</div>";
						}	
					?>				
					</div>
					<div class='step2 steps' >
						<?php
							foreach($shortcode_guide_array as $options){
								
								if(!empty($options['variables'])) {
								
									echo "<div id='".$options['id']."' class='step2_in' style='display:none'>";
									
									foreach($options['variables'] as $var){									
										echo (!empty($var))? $this->shortcode_interpret($var):null;
									}									
									
									echo "</div>";
								}
							}
						?>
						
					</div>
					<div class='clear'></div>
				</div>
				<div class='fpPOSH_footer'>
					<p id='fpPOSH_code' data-defsc='add_foodpress_menu' data-curcode='add_foodpress_menu' >[add_foodpress_menu]</p>
					<span class='fpPOSH_insert' title="<?php _e('Click to insert shortcode','foodpress');?>"></span>
				</div>
			</div>
		
			<?php
			return ob_get_clean();
		
		}


	

	// HTML code for custom options
		public function html___regular_item_options(){
			ob_start();
			?>			
			<div class='fp_sc_menu_1'>	
				<?php echo $this->get_custom_short_code_styles();?>			
			</div>
			<?php
			return ob_get_clean();

		}
		// HTML box width seelction
		function html___opt_box_width($codevar){
			global $foodpress;
			return "<div class='fpopt_box_size' data-codevar='{$codevar}'>
						<p class='selected' data-size='33'>1/3</p>
						<p data-size='50'>1/2</p>
						<p data-size='100'>".__('full width','foodpress')."</p>".$foodpress->throw_guide("Select the width size of each menu item box.", '',false)."
						<div class='clear'></div>
					</div>";
		}

	// HTML featured items options
		function html___features_item_options(){
			ob_start();
			?>
			<div class='fp_sc_menu_1'>	
				<div class='fpPop_options brdb'>
					<h3><?php _e('Select the featured item style','foodpress');?></h3>
					<p class='fpPop_option selected' data-codevar='ft_style' data-value='one'><img src='<?php echo FP_URL?>/assets/images/backend/ft_i_1.jpg'/>Highlight<br/>Item</p>
					<p class='fpPop_option' data-codevar='ft_style' data-value='two'><img src='<?php echo FP_URL?>/assets/images/backend/ft_i_2.jpg'/>Info over<br/>Image</p><p class='fpPop_option' data-codevar='ft_style' data-value='three'><img src='<?php echo FP_URL?>/assets/images/backend/ft_i_3.jpg'/>Info under<br/>Image</p>
					<div class='clear'></div>
					
					<?php echo $this->html___opt_box_width('fbox_width');?>
					
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

	// HTML code for custom options
		public function custom_shortcode_option_2(){

			ob_start();
			?>
			
			<div class='fp_sc_menu_1'>	
				<div class='fpPop_options brdb'>
					<h3><?php _e('Select the single item style','foodpress');?></h3>
					<p class='fpPop_option selected' data-codevar='ind_style' data-value='one'><img src='<?php echo FP_URL?>/assets/images/backend/ft_i_0.jpg'/>Text only</p>			
					<p class='fpPop_option' data-codevar='ind_style' data-value='two'><img src='<?php echo FP_URL?>/assets/images/backend/ft_i_2.jpg'/>Info over<br/>Image</p>
					<p class='fpPop_option' data-codevar='ind_style' data-value='three'><img src='<?php echo FP_URL?>/assets/images/backend/ft_i_3.jpg'/>Info under<br/>Image</p>
					<p class='fpPop_option' data-codevar='ind_style' data-value='four'><img src='<?php echo FP_URL?>/assets/images/backend/info_thumb_opp.jpg'/>Info next to<br/>Image</p>				
								
					<div class='clear'></div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

	// HTML code for categorized menu
		public function custom_shortcode_option_3(){
			ob_start();
			?>			
			<div class='fp_sc_menu_1'>	
				<div class='fpPop_options brdb section_selection'>
					<h3><?php _e('Select Categorized Menu Style','foodpress');?></h3>
					<p class='fpPop_option selected' data-codevar='cat_sty' data-value='de'><img src='<?php echo FP_URL?>/assets/images/backend/cm_1.png'/><?php _e('Normal List','foodpress');?></p>			
					<p class='fpPop_option' data-codevar='cat_sty' data-value='bx'><img src='<?php echo FP_URL?>/assets/images/backend/cm_2.png'/><?php _e('Box Style','foodpress');?></p>
					<p class='fpPop_option' data-codevar='cat_sty' data-value='tb'><img src='<?php echo FP_URL?>/assets/images/backend/cm_3.png'/><?php _e('Tabbed Style','foodpress');?></p>
					<p class='fpPop_option' data-codevar='cat_sty' data-value='sc'><img src='<?php echo FP_URL?>/assets/images/backend/cm_4.png'/><?php _e('Scroll Style','foodpress');?></p>
					<div class='clear'></div>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}

	function get_custom_short_code_styles(){
		ob_start();?>
		<div class='fpPop_options brdb'>
			<h3><?php _e('Select the Menu Item Style','foodpress');?></h3>			
			<p class='fpPop_option selected' data-codevar='style' data-value='one'><img src='<?php echo FP_URL?>/assets/images/backend/lines.jpg'/>Text Based</p>
			<p class='fpPop_option ' data-codevar='style' data-value='two'><img src='<?php echo FP_URL?>/assets/images/backend/thumb_lines.jpg'/>Thumb and Text</p>
			<div class='clear'></div>
			<?php echo $this->html___opt_box_width('box_width');?>
		</div>
		<?php
		return ob_get_clean();
	}

}

$GLOBALS['fp_shortcode_box'] = new foodpress_admin_shortcode_box();


?>