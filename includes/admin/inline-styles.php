<?php
/**
 * inline dynamic styles for front end
 *
 * @version		0.1
 * @package		foodPress/Styles
 * @author 		AJDE
 */

// define the CSS file type
header('Content-type: text/css');
	

$opt= get_option('fp_options_food_1');

	// complete styles array
	$style_array = apply_filters('foodpress_inline_styles', array(
		array(
			'item'=>'.fp_box .fp_price, .fp_popup_img_price',
			'css'=>'color:#$', 'var'=>'fc_pbfc',	'default'=>'fff'
		),array(
			'item'=>'.fp_popup_option i',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fp__f1',	'default'=>'999'),
				array('css'=>'font-size:$', 'var'=>'fp__f1b',	'default'=>'22px')
			)			
		),array(
			'item'=>'.fp_price, .fp_popup_img_price',
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
			'var'=>'fs_004',	'default'=>'18px'
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
	
?>