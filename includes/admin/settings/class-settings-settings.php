<?php
/**
 * Foodpress Settings Settings Tab
 * @version 0.2
 */

class foodpress_settings_settings{
	function __construct($fp_opt)	{
		$this->fp_opt = $fp_opt;
	}

	function content(){
		return apply_filters('foodpress_settings_page_content', array(
			array(
				'id'=>'food_001',
				'name'=>__('General foodpress Settings','foodpress'),
				'display'=>'show',
				'tab_name'=>__('General Settings','foodpress'),
				'top'=>'4',
				'fields'=>array(		
					array('id'=>'fp_css_head','type'=>'yesno','name'=>__('Write dynamic styles to header','foodpress'),'legend'=>__('If making changes to appearances dont reflect on front-end try this option. This will write those dynamic styles inline to page header.','foodpress'),),
					array('id'=>'fp_dis_conFilter','type'=>'yesno','name'=>__('Disable Content Filter','foodpress'),'legend'=>__('This will disable to use of the_content filter on menu description and custom field values.','foodpress')),


					array('id'=>'fp_note','type'=>'subheader','name'=>__('Menu Type Categories customization','foodpress')),					
					array('id'=>'fp_note','type'=>'note','name'=>__('Use this to assign custom names for the menu item type categories which you can use to categorize menu items in variety of ways. <b>NOTE:</b> Once you update these custom categories refresh the page for the values to show up.','foodpress'),),
					
					array('id'=>'fp_hide_icons','type'=>'yesno','name'=>__('Hide meal & dish type icons next to headers','foodpress'),'legend'=>__('This will hide the meal and dish type icons that appear next to the categorized menu header types.','foodpress')),
					
					array('id'=>'fp_mty1','type'=>'text','name'=>__('Custom name for Meal Type category (Meal type. eg. lunch, dinner)','foodpress'),),
					array('id'=>'fp_mty2','type'=>'text','name'=>__('Custom name for Dish Type category (Dish Type: Dessert, Entree)','foodpress'),),

					array('id'=>'fp_cusTax','type'=>'yesno','name'=>__('Add an Additional Category','foodpress'),'legend'=>__('You can use this to add an extra category type to categorize food items by.','foodpress'),'afterstatement'=>'fp_cusTax'),
					array('id'=>'fp_cusTax','type'=>'begin_afterstatement'),
					array('id'=>'fp_mty3','type'=>'text','name'=>__('Custom name for Additional Category','foodpress'),'legend'=>__('This additional category can be used to categorize food items for eg. locations of a restaurant)','foodpress'),),
					array('id'=>'fp_cusTax','type'=>'end_afterstatement'),
					
					
			)),array(
				'id'=>'food_003',
				'name'=>__('Look and Feel','foodpress'),
				'tab_name'=>__('Appearance','foodpress'),
				'fields'=> $this->appearnace()
			),array(
				'id'=>'food_002',
				'name'=>__('Menu Item Custom Meta Data Settings','foodpress'),
				'tab_name'=>__('Custom Meta Data','foodpress'),
				'top'=>'4',
				'fields'=>array(				
					array('id'=>'fp__note','type'=>'note','name'=>__('You can add up to 3 additional custom fields for each menu item using the below fields. (* Required values)','foodpress'),),
					
					array('id'=>'fp_af_1','type'=>'yesno','name'=>__('Activate Additional Field #1','foodpress'),'legend'=>__('This will activate additional menu item field.','foodpress'),'afterstatement'=>'fp_af_1'),
					array('id'=>'fp_af_1','type'=>'begin_afterstatement'),
					array('id'=>'fp_ec','type'=>'subheader','name'=>__('Custom field #1','foodpress'),),
					array('id'=>'fp_ec_f1','type'=>'text','name'=>__('Field Name*','foodpress'),),
					array('id'=>'fp_ec_f1b','type'=>'dropdown','name'=>__('Content Type','foodpress'), 'options'=>array(
						'text'=>__('Single line Text','foodpress'),
						'textarea'=>__('Multiple lines of text','foodpress'), 
						'menuadditions'=>__('Use Menu Additions Data','foodpress')
						)),
					array('id'=>'fp_ec_f1a','type'=>'icon','name'=>__('Icon','foodpress'),'default'=>'fa-asterisk'),
					array('id'=>'fp_af_1','type'=>'end_afterstatement'),


					array('id'=>'fp_af_2','type'=>'yesno','name'=>__('Activate Additional Field #2','foodpress'),'legend'=>__('This will activate a second additional menu item field.','foodpress'),'afterstatement'=>'fp_af_2'),
					array('id'=>'fp_af_2','type'=>'begin_afterstatement'),
					array('id'=>'fp_ec','type'=>'subheader','name'=>__('Custom field #2','foodpress'),),
					array('id'=>'fp_ec_f2','type'=>'text','name'=>__('Field Name*','foodpress'),),
					array('id'=>'fp_ec_f2b','type'=>'dropdown','name'=>__('Content Type','foodpress'), 'options'=>array(
						'text'=>__('Single line Text','foodpress'),
						'textarea'=>__('Multiple lines of text','foodpress'), 
						'menuadditions'=>__('Use Menu Additions Data','foodpress'),
						)),
					array('id'=>'fp_ec_f2a','type'=>'icon','name'=>__('Icon','foodpress'),'default'=>'fa-asterisk'),
					array('id'=>'fp_af_2','type'=>'end_afterstatement'),

					array('id'=>'fp_af_3','type'=>'yesno','name'=>__('Activate Additional Field #3','foodpress'),'legend'=>__('This will activate a second additional menu item field.','foodpress'),'afterstatement'=>'fp_af_3'),
					array('id'=>'fp_af_3','type'=>'begin_afterstatement'),
					array('id'=>'fp_ec','type'=>'subheader','name'=>__('Custom field #3','foodpress'),),
					array('id'=>'fp_ec_f3','type'=>'text','name'=>__('Field Name*','foodpress'),),
					array('id'=>'fp_ec_f3b','type'=>'dropdown','name'=>__('Content Type','foodpress'), 'options'=>array(
							'text'=>__('Single line Text','foodpress'),
							'textarea'=>__('Multiple lines of text','foodpress'),
							'menuadditions'=>__('Use Menu Additions Data','foodpress')
						 )
					),
					array('id'=>'fp_ec_f3a','type'=>'icon','name'=>__('Icon','foodpress'),'default'=>'fa-asterisk'),
					array('id'=>'fp_af_3','type'=>'end_afterstatement'),
					
			)),array(
				'id'=>'food_003a',
				'name'=>__('Menu Item Card Settings','foodpress'),
				'tab_name'=>__('Menu Card','foodpress'),
				'fields'=> array(
					array('id'=>'fp_mc_arrange',
						'type'=>'rearrange',
						'fields_array'=>$this->rearrange_code(),
						'order_var'=> 'fpCard_order',
						'selected_var'=> 'fpCard_selected',
						'title'=>__('Re-arrange the order of menu card data boxes','foodpress'),
						'notes'=>__('Fields selected below will show in menu card','foodpress')
					),
				)
			),
		
			// ICONS
			array(
				'id'=>'food_004',
				'name'=>__('Custom Icons for foodPress','foodpress'),
				'tab_name'=>__('Icons','foodpress'),
				'fields'=> apply_filters('foodpress_settings_icons', array(
					//array('id'=>'fp_note','type'=>'note','name'=>'<i>NOTE: Adding a custom icon in below settings will be used to replace font base icon you choose from list</i>',),
					array('id'=>'fs_fonti2','type'=>'fontation','name'=>__('Menu Card Icons','foodpress'),
						'variations'=>array(
							array('id'=>'fp__f1', 'type'=>'color', 'default'=>'999'),
						)
					),
					array('id'=>'fp__f2','type'=>'icon','name'=>__('Ingredients Icon','foodpress'),'default'=>'fa-book'),
					array('id'=>'fp__f3','type'=>'icon','name'=>__('Nutritions Icon','foodpress'),'default'=>'fa-cutlery'),
					array('id'=>'fp_icon_spice','type'=>'icon','name'=>__('Spice Meter Icon','foodpress'),'default'=>'fp-chili-alt'),
					
				))
			),

			// MENU ICONS
			array(
				'id'=>'food_004x',
				'name'=>__('Icon Symbols for various menu item data','foodpress'),
				'tab_name'=>__('Icon Symbols','foodpress'),
				'fields'=>$this->_array_get_icon_symbols()
			),

			// MENU TOP
			array(
				'id'=>'food_005',
				'name'=>__('MenuTop Settings (MenuTop is a row of menu item)','foodpress'),
				'tab_name'=>__('MenuTop','foodpress'),
				'fields'=> array(
					array('id'=>'fs_menutop','type'=>'checkboxes','name'=>__('Select additional data fields to be shown on menuTop <i>(Default Fields: Item Name, Description, Price)</i>','foodpress'),
						'options'=> apply_filters('foodpress_menutop_fields', array(
								'subheader'=>__('Sub Header','foodpress'),
								'addtext'=>__('Additional Text','foodpress'),											
							)),
					),				
				)
			)

			// menu paging
			,array(
				'id'=>'food_011',
				'name'=>__('Menu Paging','foodpress'),
				'tab_name'=>__('Menu Paging','foodpress'),
				'fields'=>array(			
					array('id'=>'fp__note','type'=>'note','name'=>'This page will allow you to control templates and permalinks related to foodpress menu pages.'),
					
					array('id'=>'fp_menu_archive_page_id','type'=>'dropdown','name'=>__('Select Menu Archive Page','foodpress'), 'legend'=>__('If making changes to appearances dont reflect on front-end try this option. This will write those dynamic styles inline to page header','foodpress'), 'options'=>$this->menu_pages(), 'desc'=>'This will allow you to use this page with url slug /menu/ as menu archive page'),
					array('id'=>'fp_menu_archive_page_template','type'=>'dropdown','name'=>__('Select Menu Archive Page Template','foodpress'), 'options'=>$this->theme_templates()),
					
					array('id'=>'fp_menu_slug','type'=>'text','name'=>__('foodpress menu Post Slug','foodpress'), 'default'=>'menu'),
				)
			)
			
		));	
	}

	// icon symboles
		function _array_get_icon_symbols(){
			$data = array(
				array('id'=>'fp_mi','type'=>'note','name'=>__('<i>Menu Icons can be added into each menu item to represent a data value in graphic form. eg. Vegan</i>','foodpress'),),
			);
			
			for($x=1; $x<4; $x++){
				$data[] = array('id'=>'fp_mi'.$x,'type'=>'yesno','name'=>__('Activate Icon Symbol #'.$x,'foodpress'),'legend'=>__('This will activate additional menu item field.','foodpress'),'afterstatement'=>'fp_mi'.$x);
				$data[] = array('id'=>'fp_mi'.$x,'type'=>'begin_afterstatement');
					$data[] = array('id'=>'fp_m_00'.$x,'type'=>'text','name'=>__('Name for the icon symbol','foodpress'),);
					$data[] = array('id'=>'fp_m_00'.$x.'i','type'=>'icon','name'=>__('Actual Icon','foodpress'),'default'=>'fa-leaf');
				$data[] = array('id'=>'fp_mi'.$x,'type'=>'end_afterstatement');
			}

			$data[] = '';

			return $data;
		}

	/**
	 * theme pages and templates
	 * @return  
	 */
		function menu_pages(){
			$pages = new WP_Query(array('post_type'=>'page','posts_per_page'=>-1));
			$_page_ar[]	='--';
			while($pages->have_posts()	){ $pages->the_post();								
				$page_id = get_the_ID();
				$_page_ar[$page_id] = get_the_title($page_id);
			}
			wp_reset_postdata();
			return $_page_ar;
		}
		function theme_templates(){
			// get all available templates for the theme
			$templates = get_page_templates();
			$_templates_ar['archive-menu.php'] = 'Default FoodPress Template';
			$_templates_ar['page.php'] = 'Default Page Template';
		   	foreach ( $templates as $template_name => $template_filename ) {
		       $_templates_ar[$template_filename] = $template_name;
		   	}
		   	return $_templates_ar;
		}

	// menu card boxes
		function rearrange_code(){
			$rearrange_items = apply_filters('foodpress_menucard_boxes',array(
				'header'=>array('header',__('Featured Image','eventon')),
				'details'=>array('details',__('Menu Details','eventon')),
				'spicelevel'=>array('spicelevel',__('Spicy Level','eventon')),
				'nutritions'=>array('nutritions',__('Nutritions','eventon')),
				'ingredients'=>array('ingredients',__('Ingredients','eventon')),
			));

			// custom fields
			$_cmd_num = fp_calculate_cmd_count($this->fp_opt[1]);
			for($x=1; $x<=$_cmd_num; $x++){
				if( !empty($this->fp_opt[1]['fp_ec_f'.$x]) && !empty($this->fp_opt[1]['fp_af_'.$x]) && $this->fp_opt[1]['fp_af_'.$x]=='yes')
					$rearrange_items['customfield'.$x] = array('customfield'.$x, stripslashes($this->fp_opt[1]['fp_ec_f'.$x]) );
			}

			return $rearrange_items;
		}

	// appearnace
		function appearnace(){
			return apply_filters('foodpress_settings_appearances', 
				array(
					
					array('id'=>'NOTE','type'=>'note','name'=>sprintf(__('NOTE: Once you make changed to appearance make sure to clear browser and website cache to see results.<br/>If you can not find appearance for items you want to change.. <a target="_blank" href="%s">See how you can add custom styles to change additional appearances</a>','foodpress'), 'http://myfoodpress.com/documents/override-css-foodpress-menu/'), 'default'=>'menu'),
					array('id'=>'fc_mcolor','type'=>'multicolor','name'=>'Multiple colors',
						'variations'=>array(
							array('id'=>'fc_1', 'default'=>'955181', 'name'=>'Primary color'),
							array('id'=>'fc_2', 'default'=>'a6be5c', 'name'=>'Secondary color'),
							array('id'=>'fc_pbg', 'default'=>'955181', 'name'=>'Price box color'),
							array('id'=>'fc_pbfc', 'default'=>'ffffff', 'name'=>'Price box font color'),
							array('id'=>'fc_fbh', 'default'=>'f2f6e8', 'name'=>'Featured box highlight color'),
							array('id'=>'fc_fbhh', 'default'=>'e4eccd', 'name'=>'Featured box highlight color (Hover state)'),
							array('id'=>'fc_boxH', 'default'=>'f5f4eb', 'name'=>'Menu box hover color'),
						)
					),

					array('id'=>'fp_font_fam','type'=>'text','name'=>'Primary Calendar Font family <br/><i>(Note: type the name of the font that is supported in your website. eg. Arial)</i>'),	
					

					array('id'=>'fp_fcxx','type'=>'subheader','name'=>'Fonts'),
					array('id'=>'fs_fonti','type'=>'fontation','name'=>'Menu header Text',
						'variations'=>array(
							array('id'=>'fc_tt1', 'type'=>'color', 'default'=>'7a2662'),
							array('id'=>'fs_001', 'type'=>'font_size', 'default'=>'18px'),
						)
					),array('id'=>'fs_fonti2','type'=>'fontation','name'=>'Menu Subheader Text',
						'variations'=>array(
							array('id'=>'fc_tt2', 'type'=>'color', 'default'=>'767676'),
							array('id'=>'fs_002', 'type'=>'font_size', 'default'=>'13px'),
						)
					),

					array('id'=>'fp_fcxx','type'=>'subheader','name'=>'Spice Meter'),
					array('id'=>'fs_spicemeter_style','type'=>'dropdown','name'=>'Select Spice Meter Style','options'=>array(
						'default'=>'Default',
						'bar'=>'Spice level bar'
						)),

					array('id'=>'fp_fcx','type'=>'hiddensection_open','name'=>'Menu Popup', 'display'=>'none'),
						array('id'=>'fs_fonti3','type'=>'fontation','name'=>'Header Text',
							'variations'=>array(
								array('id'=>'fc_tt3', 'type'=>'color', 'default'=>'7a2662'),
								array('id'=>'fs_003', 'type'=>'font_size', 'default'=>'24px'),
							)
						),array('id'=>'fs_fonti4','type'=>'fontation','name'=>'Subheader Text',
							'variations'=>array(
								array('id'=>'fc_tt4', 'type'=>'color', 'default'=>'767676'),
								array('id'=>'fs_004', 'type'=>'font_size', 'default'=>'22px'),
							)
						),
					array('id'=>'fp_fcx','type'=>'hiddensection_close',),
					
					
					
					array('id'=>'fp_fcx','type'=>'hiddensection_open','name'=>'Menu Categories', 'display'=>'none'),
						array('id'=>'fs_fonti5','type'=>'fontation','name'=>'Primary category text',
							'variations'=>array(
								array('id'=>'fc_tt5', 'type'=>'color', 'default'=>'7a2662'),
								array('id'=>'fs_005', 'type'=>'font_size', 'default'=>'30px'),
							)
						),array('id'=>'fs_fonti6','type'=>'fontation','name'=>'Secondary category text',
							'variations'=>array(
								array('id'=>'fc_tt6', 'type'=>'color', 'default'=>'7a2662'),
								array('id'=>'fs_006', 'type'=>'font_size', 'default'=>'24px'),
							)
						),
					array('id'=>'fp_fcx','type'=>'hiddensection_close',),
					
					/*Added by Mike */

					array('id'=>'fp_fcx','type'=>'hiddensection_open','name'=>'Tabbed Menu Styles', 'display'=>'none'),
						
						array('id'=>'fc_mcolor','type'=>'multicolor','name'=>'Multiple colors',
						'variations'=>array(
							array('id'=>'fc_tab1', 'default'=>'474747', 'name'=>'Tab Text Color'),
							array('id'=>'fc_tab2', 'default'=>'e1e1e1', 'name'=>'Tab Text Hover Color'),
							array('id'=>'fc_tab3', 'default'=>'ffffff', 'name'=>'Active Tab Text Color'),
							array('id'=>'fc_tab4', 'default'=>'ffffff', 'name'=>'Active Tab Text Hover Color'),
							array('id'=>'fc_tab5', 'default'=>'ffffff', 'name'=>'Tab Background Color'),
							array('id'=>'fc_tab6', 'default'=>'f1f1f1', 'name'=>'Tab Background Hover Color'),
							array('id'=>'fc_tab7', 'default'=>'e1e1e1', 'name'=>'Active Tab Background Color'),
							array('id'=>'fc_tab8', 'default'=>'f1f1f1', 'name'=>'Active Tab Background Hover Color'),
							)
						),
						
						array('id'=>'fs_fonti7','type'=>'fontation','name'=>'Tabbed Menu Category Text',
							'variations'=>array(
								array('id'=>'fs_007', 'type'=>'font_size', 'default'=>'20px'),
							)
						),
					array('id'=>'fp_fcx','type'=>'hiddensection_close',),
					
					
					array('id'=>'fp_fcx','type'=>'hiddensection_open','name'=>'Boxed Menu Styles', 'display'=>'none'),
						
						array('id'=>'fc_mcolor','type'=>'multicolor','name'=>'Multiple colors',
						'variations'=>array(
							array('id'=>'fc_box1', 'default'=>'7a2662', 'name'=>'Main Box Background Color'),
							)
						),
						
						array('id'=>'fs_fonti9','type'=>'fontation','name'=>'Main Box Icon Color',
							'variations'=>array(
								array('id'=>'fc_009', 'type'=>'color', 'default'=>'ffffff'),
							)
						),
						
						
						array('id'=>'fs_fonti10','type'=>'fontation','name'=>'Main Box Title Text',
							'variations'=>array(
								array('id'=>'fc_010', 'type'=>'color', 'default'=>'ffffff'),
								array('id'=>'fs_010', 'type'=>'font_size', 'default'=>'18px'),
							)
						),
						
						array('id'=>'fs_fonti11','type'=>'fontation','name'=>'Main Box Description Text',
							'variations'=>array(
								array('id'=>'fc_011', 'type'=>'color', 'default'=>'ffffff'),
								array('id'=>'fs_011', 'type'=>'font_size', 'default'=>'14px'),
							)
						),
						
					array('id'=>'fp_fcx','type'=>'hiddensection_close',),
					
					/* reservation form */
					array('id'=>'fp_fcx','type'=>'hiddensection_open','name'=>'Reservation System Styles', 'display'=>'none'),
						
						array('id'=>'fc_mcolor','type'=>'multicolor','name'=>'Multiple colors',
						'variations'=>array(
							array('id'=>'fc_res1', 'default'=>'7a2662', 'name'=>'Reservation Box Background Color'),
							array('id'=>'fc_res2', 'default'=>'8f3174', 'name'=>'Reservation Box Background Hover Color'),
						)
					),
					
					array('id'=>'fs_fontres3','type'=>'fontation','name'=>'Reservation Box Title Text',
						'variations'=>array(
							array('id'=>'fc_res3', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'fs_res3', 'type'=>'font_size', 'default'=>'22px'),
						)
					),
					
					array('id'=>'fs_fontres4','type'=>'fontation','name'=>'Reservation Box Subtitle Text',
						'variations'=>array(
							array('id'=>'fc_res4', 'type'=>'color', 'default'=>'ffffff'),
							array('id'=>'fs_res4', 'type'=>'font_size', 'default'=>'14px'),
						)
					),		
					
					
					array('id'=>'fp_res_formbd','type'=>'fontation','name'=>'Reservation Background',
						'variations'=>array(
							array('id'=>'fp_res_formbd_bgc', 'type'=>'color', 'title'=>'(LightBox) Default Background Color', 'default'=>'8a2945'),
							array('id'=>'fp_res_formbd_bgcS', 'type'=>'color', 'title'=>'(LightBox) Success Stage Background Color', 'default'=>'7A9E6B'),						
							array('id'=>'fp_res_formbd_bgcS_ONP', 'type'=>'color', 'title'=>'(OnPage Form) Background Color', 'default'=>'ffffff'),						
						)
					),

					array('id'=>'fp_res_formtext','type'=>'fontation','name'=>'Reservation Form Content',
						'variations'=>array(
							array('id'=>'fp_res_form_genfont', 'type'=>'color', 'title'=>'General Text Color', 'default'=>'ffffff'),					
							array('id'=>'fp_res_formbd_fields', 'type'=>'color', 'title'=>'Input/textarea Fields Background Color', 'default'=>'A54460'),	
							array('id'=>'fp_res_formbd_fieldtext', 'type'=>'color', 'title'=>'Input/textarea Fields Text Color', 'default'=>'FFFFFF'),				
							array('id'=>'fp_res_formbd_fieldborder', 'type'=>'color', 'title'=>'Input/textarea Fields Border Color', 'default'=>'8A2945'),	
							array('id'=>'fp_res_formbd_fieldplaceholder', 'type'=>'color', 'title'=>'Input Placeholder Text Color', 'default'=>'ffffff'),	
							array('id'=>'fp_res_links', 'type'=>'color', 'title'=>'Permalink Text Color', 'default'=>'ffffff'),	
							
						)
					),
					array('id'=>'fp_res_formtext','type'=>'fontation','name'=>'(OnPage) Form Content',
						'variations'=>array(							
							array('id'=>'fp_res_form_genfontOP', 'type'=>'color', 'title'=>'General Text Color', 'default'=>'717171'),
							array('id'=>'fp_res_linksOP', 'type'=>'color', 'title'=>'Permalink Text Color', 'default'=>'C34545'),	
					)),
					array('id'=>'fp_res_btn','type'=>'fontation','name'=>'Reservation Form Button',
						'variations'=>array(											
							array('id'=>'fp_res_btn_text', 'type'=>'color', 'title'=>'Button Text Color', 'default'=>'ffffff'),
							array('id'=>'fp_res_btn_bgc', 'type'=>'color', 'title'=>'Button Background Color', 'default'=>'8a2945'),				
							array('id'=>'fp_res_btn_bgcH', 'type'=>'color', 'title'=>'Button Background Color (Hover)', 'default'=>'8f3174'),				
						)
					),
					
						
					array('id'=>'fp_fcx','type'=>'hiddensection_close',),

				)
			);
		}
}			
?>