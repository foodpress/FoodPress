<?php
/**
 * Language settings for foodpress
 * @version   1.4
 */

$foodpress_custom_language_array = array(

	array('type'=>'togheader','name'=>'General Menu'),

		array(
			'label'=>__('no menu items','foodpress'),'name'=>__('no_menu_items','foodpress'),
		),array(
			'label'=>__('featured','foodpress'),'name'=>__('featured','foodpress'),
		),array(
			'label'=>__('more info','foodpress'),'name'=>__('more_info','foodpress'),
		),array(
			'label'=>__('read more','foodpress'),'name'=>__('read_more','foodpress'),
		),array(
			'label'=>__('vegetarian','foodpress'),'name'=>__('vegetarian','foodpress'),
		),array(
			'label'=>__('ingredients','foodpress'),'name'=>__('ingredients','foodpress'),
		),array(
			'label'=>__('description','foodpress'),'name'=>__('description','foodpress'),
		),array(
			'label'=>__('short description','foodpress'),'name'=>__('short_description','foodpress'),
		),array(
			'label'=>__('price','foodpress'),'name'=>__('price','foodpress'),
		),array(
			'label'=>__('spicy level','foodpress'),'name'=>__('spicy_level','foodpress'),
		),array(
			'label'=>__('nutritional information','foodpress'),'name'=>__('nutritional_information','foodpress'),
		),array(
			'label'=>__('nutritions','foodpress'),'name'=>__('nutritions','foodpress'),
		),array(
			'label'=>__('calories','foodpress'),'name'=>__('calories','foodpress'),
		),array(
			'label'=>__('cholesterol','foodpress'),'name'=>__('cholesterol','foodpress'),
		),array(
			'label'=>__('fiber','foodpress'),'name'=>__('fiber','foodpress'),
		),array(
			'label'=>__('sodium','foodpress'),'name'=>__('sodium','foodpress'),
		),array(
			'label'=>__('carbohydrates','foodpress'),'name'=>__('carbohydrates','foodpress'),
		),array(
			'label'=>__('fat','foodpress'),'name'=>__('fat','foodpress'),
		),
		array('label'=>__('protein','foodpress'),'name'=>__('protein','foodpress')),

		// custom meta fields
		array('type'=>'parent', 'children'=>foodpress_lang_get_custom_meta_field()),

	array('type'=>'togend'),

	array('type'=>'togheader','name'=>__('Other General','foodpress'),),
		array('label'=>__('Location','foodpress'),'name'=>__('location','foodpress')),
		array('label'=>__('Menu Last Updated','foodpress'),'name'=>__('menu_last_updated','foodpress')),
		array('label'=>__('Back to Menu','foodpress'),'name'=>'back_to_menu'),
		array('type'=>'togend'),

	array('type'=>'togheader','name'=>__('Categories','foodpress'),),
		array('label'=>__('Category: Meal Type','foodpress'),'name'=>'fp_lang_tax_1','legend'=>''),
		array('type'=>'parent', 'children'=>foodpress_lang_get_tax_terms('meal_type')),

		array('label'=>__('Category: Dish Type','foodpress'),'name'=>'fp_lang_tax_2','legend'=>''),
		array('type'=>'parent', 'children'=>foodpress_lang_get_tax_terms('dish_type')),

		array('type'=>'togend'),

	array('type'=>'togheader','name'=>__('Reservation Status','foodpress'),),
		array('label'=>__('check-in','foodpress'),'name'=>'fp_lang_resform_R1'),
		array('label'=>__('checked','foodpress'),'name'=>'fp_lang_resform_R2'),
		array('type'=>'togend'),

	array('type'=>'togheader','name'=>__('Reservation Form','foodpress'),),
		array('label'=>__('Reservation Button Text (Default)','foodpress'),
			'name'=>'fp_lang_res_btn_h1','placeholder'=>__('Make a Reservation now','foodpress'),),
		array('label'=>__('Reservation Button Subtext (Default)'),
			'name'=>'fp_lang_res_btn_h2', 'placeholder'=>'Click here to reserve a spot at our restaurant for tonights dinner'),

		array('label'=>__('Form Header Text','foodpress'),'name'=>'fp_lang_resform_001','placeholder'=>__('Make a Reservation','foodpress'),),
		array('label'=>__('Form Subheader Text'),'name'=>'fp_lang_resform_A2', 'placeholder'=>'For further questions, please call'),

		array('label'=>__('Date','foodpress'),'name'=>'fp_lang_resform_A5'),
		array('label'=>__('Date Placeholder','foodpress'),'name'=>'fp_lang_resform_A5A','placeholder'=>__('Select Date','foodpress'),),
		array('label'=>__('Time','foodpress'),'name'=>'fp_lang_resform_A4'),
		array('label'=>__('Party Size','foodpress'),'name'=>'fp_lang_resform_A3'),
			array('label'=>__('Party Size Placeholder','foodpress'),'name'=>'fp_lang_resform_004dB'),
		array('label'=>__('Your Name','foodpress'),'name'=>'fp_lang_resform_004a'),
		array('label'=>__('Email Address','foodpress'),'name'=>'fp_lang_resform_004c'),
		array('label'=>__('Phone Number','foodpress'),'name'=>'fp_lang_resform_004d','placeholder'=>__('(555) 235-2020','foodpress'),),
		array('label'=>__('Restaurant Location','foodpress'),'name'=>'fp_lang_resform_004e'),
		array('label'=>__('Phone Number Placeholder','foodpress'),'name'=>'fp_lang_resform_004dA'),

		array('type'=>'parent', 'children'=>foodpress_lang_get_custom_fields('dish_type')),
		array('label'=>__('Type the code to validate','foodpress'),'name'=>'fp_lang_resform_si008'),
		array('label'=>__('Reserve Now','foodpress'),'name'=>'fp_lang_resform_A1'),

		array('label'=>__('By clicking you agree with our ','foodpress'),'name'=>'fp_lang_resform_si005'),
		array('label'=>__('Privacy Policy','foodpress'),'name'=>'fp_lang_resform_si006'),
		array('label'=>__('Terms of Use','foodpress'),'name'=>'fp_lang_resform_si007'),
		array('type'=>'togend'),


	array('type'=>'togheader','name'=>__('Reservation Form Success Step','foodpress'),),
		array('label'=>__('Awesome, we got your reservation!','foodpress'),'name'=>'fp_lang_resform_s001'),
		array('label'=>__('Success Text','foodpress'),'name'=>'fp_lang_resform_s002', 'legend'=>__('Default Text: If there are any issues with this reservation we will give you a call. Otherwise, we look forward to seeing you soon!','foodpress'),),
		array('label'=>__('Reservation Information','foodpress'),'name'=>'fp_lang_resform_si001'),
		array('label'=>__('Date','foodpress'),'name'=>'fp_lang_resform_si002'),
		array('label'=>__('Time','foodpress'),'name'=>'fp_lang_resform_si003'),
		array('label'=>__('Party Size','foodpress'),'name'=>'fp_lang_resform_si004'),
		array('type'=>'togend'),

	array('type'=>'togheader','name'=>__('Reservation Form Notification Messages','foodpress'),),
		array('label'=>__('Posted successfully','foodpress'),'name'=>'fp_lang_resform_n001'),
		array('label'=>__('Required fields missing','foodpress'),'name'=>'fp_lang_resform_n002'),
		array('label'=>__('Invalid email address','foodpress'),'name'=>'fp_lang_resform_n003'),
		array('label'=>__('Invalid phone number','foodpress'),'name'=>'fp_lang_resform_n004'),
		array('label'=>__('Could not create new reservation. Please try again later','foodpress'),'name'=>'fp_lang_resform_n005'),
		array('label'=>__('Validation code does not match, please try again!','foodpress'),'name'=>'fp_lang_resform_n006'),
		array('type'=>'togend'),

	array('type'=>'togheader','name'=>__('Reservation Emails','foodpress'),),
		array('label'=>__('Subject: for notification','foodpress'),'name'=>'fprsvp_003','placeholder'=>'We have received your reservation!'),
		array('label'=>__('Subject: for confirmation','foodpress'),'name'=>'fprsvp_004','placeholder'=>'New Reservation Submission!'),
		array('label'=>__('Subject: for cancellation','foodpress'),'name'=>'fprsvp_cancellation_text','placeholder'=>'Reservation cancellation!'),

		array('label'=>__('Reservation confirmation','foodpress'),'name'=>'reservation_confirmation'),
		array('label'=>__('Reservation ID','foodpress'),'name'=>'fprsvp_001','legend'=>''),
		array('label'=>__('Reservation Time','foodpress'),'name'=>'reservation_time','legend'=>''),
		array('label'=>__('Primary Contact','foodpress'),'name'=>'primary_content','legend'=>''),
		array('label'=>__('Email Address','foodpress'),'name'=>'fprsvp_005','legend'=>''),
		array('label'=>__('Phone Number','foodpress'),'name'=>'fprsvp_006','legend'=>''),
		array('label'=>__('Party Size','foodpress'),'name'=>'party_size','legend'=>''),
		array('label'=>__('We look forward to seeing you!','foodpress'),'name'=>'we_look_forward_to_seeing_you','legend'=>''),
		array('label'=>__('Contact Us for questions and concerns','foodpress'),'name'=>'contact_us_for_questions_and_concerns','legend'=>''),
		array('label'=>__('You have received a new Reservation!','foodpress'),'name'=>'you_have_received_a_new_reservation','legend'=>''),
		array('label'=>__('View RSVP in wp-admin','foodpress'),'name'=>'fprsvp_002','legend'=>''),
		array('type'=>'togend'),
);

// language for foodpress menu custom taxonomy terms
	function foodpress_lang_get_tax_terms($tax){
		$terms = get_terms($tax, array('orderby'=>'name'));
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
		    foreach($terms as $term){
		    	$output[]= array('label'=>$term->name,'name'=>'fp_lang_tax_'.$tax.'_'.$term->term_id);
		    }
		    //print_r($output);
		    return $output;
		}else{
			return false;
		}
	}

// language for custom fields on reservation form
	function foodpress_lang_get_custom_fields(){
		$output = '';
		$resOPT = get_option('fp_options_food_6');
		for($x=1; $x<=foodpress_get_reservation_form_fields(); $x++){
			if( !empty($resOPT['fp_af_'.$x]) && $resOPT['fp_af_'.$x]=='yes' && !empty($resOPT['fp_ec_f'.$x]) ){
				$output[]= array('label'=>$resOPT['fp_ec_f'.$x],'name'=>'fp_ec_f'.$x);
			}
		}
		return $output;
	}

// language for foodpress menu custom taxonomy terms
	function foodpress_lang_get_custom_meta_field(){
		global $foodpress;
		$output = '';
		$fpOPT = get_option('fp_options_food_1');
		for($x=1; $x<= $foodpress->functions->custom_fields_cnt(); $x++){
			if( !empty($fpOPT['fp_af_'.$x]) && $fpOPT['fp_af_'.$x]=='yes' && !empty($fpOPT['fp_ec_f'.$x]) ){
				$output[]= array('label'=>stripslashes($fpOPT['fp_ec_f'.$x]),
					'name'=>'fp_ec_f'.$x );
			}
		}

		return $output;
	}
?>
