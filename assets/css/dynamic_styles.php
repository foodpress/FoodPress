<?php
/**
 * dynamic styles for front end
 *
 * @version		0.1
 * @package		foodpress/Styles
 * @author 		AJDE
 */


	// Load variables
	$opt= get_option('fp_options_food_1');
	$_grad_rgb = (!empty($opt['fp_res_form1']))? $opt['fp_res_form1']: '132,67,114';
	

	// complete styles array
	$style_array = apply_filters('foodpress_inline_styles', array(
		array(
			'item'=>'.fp_menucard_content h3, .fp_popup_option_title, .fp_menucard_content .menu_description, .fp_text, .fp_menucard_content .fp_popup_img_title, .foodpress_menu .fp_menu_sub_section, .fp_box h3, .fp_box .menu_description, .fp_box .fp_price, .fp_box h5.fp_subheader, .fp_box h5.fp_additions, .foodpress_menu.box_cats .foodpress_categories h4, .foodpress_menu.box_cats p.fp_backto_cats',
			'css'=>'font-family:$', 'var'=>'fp_font_fam',	'default'=>"'open sans', 'sans-serif'"
		),array(
			'item'=>'.fp_box .fp_price, .fp_popup_img_price',
			'css'=>'color:#$', 'var'=>'fc_pbfc',	'default'=>'fff'
		),array(
			'item'=>'.fp_popup_option i',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fp__f1',	'default'=>'999'),				
			)			
		),array(
			'item'=>'.fp_price, .fp_pop_inner .fp_popup_img_price',
			'css'=>'background-color:#$',
			'var'=>'fc_pbg',	'default'=>'955181'
		),array(
			'item'=>'.bghighl',
			'css'=>'background-color:#$',
			'var'=>'fc_fbh',	'default'=>'f2f6e8'
		),array(
			'item'=>'.bghighl:hover',
			'css'=>'background-color:#$',
			'var'=>'fc_fbhh',	'default'=>'e4eccd'
		),array(
			'item'=>'.fp_box h3',
			'css'=>'color:#$',
			'var'=>'fc_tt1',	'default'=>'7a2662'
		),array(
			'item'=>'.fp_box h3',
			'css'=>'color:#$',
			'var'=>'fc_tt1',	'default'=>'7a2662'
		),array(
			'item'=>'.fp_box h3',
			'css'=>'font-size:$',
			'var'=>'fs_001',	'default'=>'18px'
		),array(
			'item'=>'.fp_box .menu_description',
			'css'=>'color:#$',
			'var'=>'fc_tt2',	'default'=>'767676'
		),array(
			'item'=>'.fp_box .menu_description',
			'css'=>'font-size:$',
			'var'=>'fs_002',	'default'=>'13px'
		),array(
			'item'=>'.fp_popup h3',
			'css'=>'color:#$',
			'var'=>'fc_tt2',	'default'=>'7a2662'
		),array(
			'item'=>'.fp_popup h3',
			'css'=>'font-size:$',
			'var'=>'fs_003',	'default'=>'24px'
		),array(
			'item'=>'.fp_popup_option_title',
			'css'=>'color:#$',
			'var'=>'fc_tt4',	'default'=>'767676'
		),array(
			'item'=>'.fp_popup_option_title',
			'css'=>'font-size:$',
			'var'=>'fs_004',	'default'=>'22px'
		)

		,array(
			'item'=>'.style_1.fp_box:hover',
			'css'=>'background-color:#$','var'=>'fc_boxH','default'=>'f5f4eb'
		),array(
			'item'=>'.style_2.fp_box .fp_inner_box:hover',
			'css'=>'background-color:#$','var'=>'fc_boxH','default'=>'f5f4eb'
		)


		,array(
			'item'=>'.foodpress_menu .fp_menu_sub_section',
			'css'=>'color:#$',
			'var'=>'fc_tt5',	'default'=>'7a2662'
		),array(
			'item'=>'.foodpress_menu .fp_menu_sub_section',
			'css'=>'font-size:$',
			'var'=>'fs_005',	'default'=>'30px'
		),array(
			'item'=>'.foodpress_menu h3.fp_menu_sub_section',
			'css'=>'color:#$',
			'var'=>'fc_tt6',	'default'=>'7a2662'
		),array(
			'item'=>'.foodpress_menu h3.fp_menu_sub_section',
			'css'=>'font-size:$',
			'var'=>'fs_006',	'default'=>'24px'
		),
		
		/*Added by Mike */

			array(
				'item'=>'.tabbed_menu .foodpress_tabs h4',
				'css'=>'color:#$','var'=>'fc_tab1','default'=>'474747'
			),array(
				'item'=>'.tabbed_menu .foodpress_tabs h4:hover',
				'css'=>'color:#$','var'=>'fc_tab2','default'=>'e1e1e1'
			),
			
			array(
				'item'=>'.tabbed_menu .foodpress_tabs h4.focused',
				'css'=>'color:#$','var'=>'fc_tab3','default'=>'ffffff'
			),array(
				'item'=>'.tabbed_menu .foodpress_tabs h4.focused:hover',
				'css'=>'color:#$','var'=>'fc_tab4','default'=>'ffffff'
			),
			
			array(
				'item'=>'.tabbed_menu .foodpress_tabs h4',
				'css'=>'background-color:#$','var'=>'fc_tab5','default'=>'ffffff'
			),array(
				'item'=>'.tabbed_menu .foodpress_tabs h4:hover',
				'css'=>'background-color:#$','var'=>'fc_tab6','default'=>'f1f1f1'
			),
			
			array(
				'item'=>'.tabbed_menu .foodpress_tabs h4.focused',
				'css'=>'background-color:#$',
				'var'=>'fc_tab7','default'=>'e1e1e1'
			),array(
				'item'=>'.tabbed_menu .foodpress_tabs h4.focused:hover',
				'css'=>'background-color:#$',
				'var'=>'fc_tab8','default'=>'f1f1f1'
			),			
			array(
				'item'=>'.tabbed_menu .foodpress_tabs h4',
				'css'=>'font-size:$',
				'var'=>'fs_007',	'default'=>'20px'
			),
			array(
				'item'=>'.foodpress_menu.box_cats .foodpress_categories h4',
				'css'=>'background-color:#$',
				'var'=>'fc_box1','default'=>'7a2662'
			),			
			array(
				'item'=>'.foodpress_menu.box_cats .foodpress_categories h4 i',
				'css'=>'color:#$',
				'var'=>'fc_009',	'default'=>'ffffff'
			),	
			array(
				'item'=>'.foodpress_menu.box_cats .foodpress_categories h4',
				'css'=>'color:#$',
				'var'=>'fc_010',	'default'=>'ffffff'
			),array(
				'item'=>'.foodpress_menu.box_cats .foodpress_categories h4',
				'css'=>'font-size:$',
				'var'=>'fs_010',	'default'=>'18px'
			),
			array(
				'item'=>'.foodpress_menu.box_cats .foodpress_categories h4 p.fp_meal_type_description',
				'css'=>'color:#$',
				'var'=>'fc_011',	'default'=>'ffffff'
			),array(
				'item'=>'.foodpress_menu.box_cats .foodpress_categories h4 p.fp_meal_type_description',
				'css'=>'font-size:$',
				'var'=>'fs_011',	'default'=>'14px'
			),			
			array(
				'item'=>'.tabbed_menu .foodpress_tabs h4.focused',
				'css'=>'color:#$','var'=>'fc_res','default'=>'ffffff'
			),array(
				'item'=>'.tabbed_menu .foodpress_tabs h4.focused:hover',
				'css'=>'color:#$','var'=>'fc_tab4','default'=>'ffffff'
			),			
			array(
				'item'=>'.fp_res_button, #fp_make_res.onpage .form_section_2 #fp_reservation_submit','css'=>'background-color:#$','var'=>'fp_res_btn_bgc',	'default'=>'8a2945'
			),			
			array(
				'item'=>'.fp_res_button:hover, #fp_make_res.onpage .form_section_2 #fp_reservation_submit:hover','css'=>'background-color:#$','var'=>'fp_res_btn_bgcH',	'default'=>'8f3174'
			),			
			array(
				'item'=>'.fp_res_t1',
				'css'=>'color:#$',
				'var'=>'fc_res3',	'default'=>'ffffff'
			),array(
				'item'=>'.fp_res_t1',
				'css'=>'font-size:$',
				'var'=>'fs_res3',	'default'=>'22px'
			),			
			array(
				'item'=>'.fp_res_t2',
				'css'=>'color:#$',
				'var'=>'fc_res4',	'default'=>'ffffff'
			),array(
				'item'=>'.fp_res_t2',
				'css'=>'font-size:$',
				'var'=>'fs_res4',	'default'=>'14px'
			),array(
				'item'=>'.fpres_bg',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'fp_res_formbd_bgc',	'default'=>'8a2945'),	
				)	
			),array(
				'item'=>'.form_section_2 #fp_reservation_submit',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'fp_res_btn_bgc',	'default'=>'8a2945'),	
					array('css'=>'color:#$', 'var'=>'fp_res_btn_text',	'default'=>'ffffff'),	
				)	
			),array(
				'item'=>'#fp_make_res .reservation_section input, #fp_make_res .reservation_section textarea, #fp_make_res.onpage .reservation_section input, #fp_make_res.onpage .reservation_section textarea, #fp_make_res .reservation_section select, .form_section_1 p select option, .form_section_2 p select option',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'fp_res_formbd_fields',	'default'=>'A54460'),	
					array('css'=>'color:#$', 'var'=>'fp_res_formbd_fieldtext',	'default'=>'FFFFFF'),	
					array('css'=>'border-color:#$', 'var'=>'fp_res_formbd_fieldborder',	'default'=>'8A2945'),	
				)	
			)

			// input placeholder color
			,array(
				'item'=>'.form_section_2 input::-webkit-input-placeholder, .form_section_1 input::-webkit-input-placeholder','css'=>'color:#$','var'=>'fp_res_formbd_fieldplaceholder',	'default'=>'ffffff'
			),array(
				'item'=>'.form_section_2 input:-moz-input-placeholder, .form_section_1 input:-moz-input-placeholder','css'=>'color:#$','var'=>'fp_res_formbd_fieldplaceholder',	'default'=>'ffffff'
			),array(
				'item'=>'.form_section_2 input::-moz-input-placeholder, .form_section_1 input::-moz-input-placeholder','css'=>'color:#$','var'=>'fp_res_formbd_fieldplaceholder',	'default'=>'ffffff'
			),array(
				'item'=>'.form_section_2 input:-ms-input-placeholder, .form_section_1 input:-ms-input-placeholder','css'=>'color:#$','var'=>'fp_res_formbd_fieldplaceholder',	'default'=>'ffffff'
			)

			// permalink color
			,array(
				'item'=>'#fp_make_res .terms a, #fp_make_res.onpage .terms a','css'=>'color:#$','var'=>'fp_res_links',	'default'=>'ffffff'
			)
			,array(
				'item'=>'.fpres_bg.success','css'=>'background-color:#$',
				'var'=>'fp_res_formbd_bgcS',	'default'=>'7A9E6B'
			),array(
				'item'=>'#fp_make_res','css'=>'color:#$',
				'var'=>'fp_res_form_genfont',	'default'=>'ffffff'
			),array(
				'item'=>'#fp_make_res.onpage','css'=>'background-color:#$',
				'var'=>'fp_res_formbd_bgcS_ONP',	'default'=>'ffffff'
			),array(
				'item'=>'#fp_make_res.onpage','css'=>'color:#$',
				'var'=>'fp_res_form_genfontOP',	'default'=>'717171'
			),array(
				'item'=>'#fp_make_res.onpage .terms a','css'=>'color:#$','var'=>'fp_res_linksOP',	'default'=>'C34545'
			)
			


	));
	
	ob_start();

	foreach($style_array as $sa){
		if(!empty($sa['multicss']) && is_array($sa['multicss'])){

			echo $sa['item'].'{';

			foreach($sa['multicss'] as $sin_CSS){
				$css_val  = (!empty($opt[ $sin_CSS['var'] ] ))? $opt[ $sin_CSS['var'] ] : $sin_CSS['default'];
				$css = str_replace('$',$css_val,$sin_CSS['css'] );
				echo $css.';';
			}
			echo '}';
		}else{
			$css_val  = (!empty($opt[ $sa['var'] ] ))? $opt[ $sa['var'] ] : $sa['default'];
			$css = str_replace('$',$css_val,$sa['css'] );
			echo $sa['item'].'{'.$css.'}';
		}
	}



	
	
	echo ob_get_clean();

	// (---) Hook for addons
	if(has_action('foodpress_inline_styles')){
		do_action('foodpress_inline_styles');
	}

	echo get_option('food_styles');

	
