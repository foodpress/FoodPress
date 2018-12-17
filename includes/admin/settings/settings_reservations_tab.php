<?php
/**
 * reservation settings
 *
 * @version 	3.1
 * @updated 	2015-3-2
 */

class fp_settings_reservation{

	public function __construct($food_opt){
		$this->food_opt = $food_opt;
	}

	function get_content(){
		global $foodpress;
		?>
			<form method="post" action=""><?php settings_fields('food_field_group');
				wp_nonce_field( 'food_nonce', 'food_noncename' );
			?>
			<div id="food_1" class=" foodpress_admin_meta food_focus">
				<div class="inside">
					<?php
						//var_dump($this->food_opt);
						//$opt6 = $food_opt[6];

						$cutomization_pg_array = $this->settings();

						$foodpress->load_ajde_backender();
						print_ajde_customization_form($cutomization_pg_array, $this->food_opt[6]);
					?>
				</div>
				<p class='fp_settings_bottom'><input type="submit" class="btn_prime fp_admin_btn" value="<?php _e('Save Changes') ?>" /></p>
			</div>
			</form>
		<?php
	}

	function settings(){
		$__fp_admin_email = get_option('admin_email');

		return apply_filters('foodpress_reservation_setting',
			array(
				array(
					'id'=>'food_001',
					'name'=>__('General Reservation Settings','foodpress'),
					'display'=>'show',
					'tab_name'=>__('General','foodpress'),
					'fields'=>array(
						array('id'=>'fpr_timesl','type'=>'yesno','name'=>__('Set time restriction for reservations'),'afterstatement'=>'fpr_timesl', 'legend'=>'This will allow you to restrict the time slots available for reservations to between certain times only. Both start and end times are required.'),
							array('id'=>'fpr_timesl','type'=>'begin_afterstatement'),

								array('id'=>'reservation','type'=>'subheader','name'=>'Everyday Reservable Hours'),
								array('id'=>'fpr_start_time',
									'type'=>'dropdown',
									'name'=>__('Reservation Start Time Range','foodpress'),
									'options'=>foodpress_get_times(2)
								),
								array('id'=>'fpr_end_time',
									'type'=>'dropdown',
									'name'=>__('Reservation End Time Range','foodpress'),
									'options'=>foodpress_get_times(2)
									),
							array('id'=>'fpr_timesl','type'=>'end_afterstatement'),
						//array('id'=>'fpr_24hr','type'=>'yesno','name'=>__('Show time in 24 hour format','foodpress'), 'legend'=>'This will show the time slots available for selection in 24 hour format'),

						//array('id'=>'fpr_univ_date_format','type'=>'yesno','name'=>__('Use WordPress date format','foodpress'), 'legend'=>'Use date format saved in wordpress general settings instead of default date format for reservation form'),

						array('id'=>'fpr_time_incer','type'=>'dropdown','name'=>__('Time slot increment by','foodpress'), 'options'=>array( '1'=>'1 hour','2'=>'30 min', '4'=>'15 min','12'=>'5 min'),'default'=>'12', 'legend'=>'Reservation form time field available values for selection be incremented by this amount for each time interval'),

						array('id'=>'fpr_startend','type'=>'yesno','name'=>__('Show start and end time for reservations','foodpress'), 'legend'=>'This will show start and end time as two time selection boxes in reservation form to select from'),

						array('id'=>'fpr_partysize','type'=>'yesno','name'=>__('Set party size capacity limit'),'afterstatement'=>'fpr_partysize', 'legend'=>'This will allow you to restrict the time slots available for reservations to between certain times only'),
							array('id'=>'fpr_partysize','type'=>'begin_afterstatement'),
								array('id'=>'fpr_partysz_num','type'=>'text','name'=>__('Type maximum party size allowed','foodpress'), 'default'=>'eg. 8'),
							array('id'=>'fpr_partysize','type'=>'end_afterstatement'),

						array('id'=>'fpr_draft','type'=>'yesno','name'=>__('Reservations must be approved by admin','foodpress'), 'legend'=>'If this is activated, all reservations will need to be approved by admin before they are confirmed'),

						array('id'=>'fpr_24_block','type'=>'yesno','name'=>__('Start reservations from tomorrow','foodpress'), 'legend'=>'Customers will only be able to select reservation days from tomorrow on wards. This will make the customers not select todays day for reservations.'),

						array('id'=>'fpr_univ_date_format','type'=>'yesno','name'=>__('Use WP default Date format in reservation','foodpress'),'legend'=>__('Select this option to use the default WP Date format through out foodPress. Default format: yyyy/mm/dd','foodpress'),),

						array('id'=>'fpr_privacy','type'=>'text','name'=>__('URL Link to Privacy Policy','foodpress'), 'legend'=>'This will add privacy policy statement to footer of reservation form. Text can be edited from language'),
						array('id'=>'fpr_terms','type'=>'text','name'=>__('URL Link to Terms of Use','foodpress'), 'legend'=>'This will add terms of use statement to footer of reservation form. Text can be edited from language'),

						array('id'=>'fpr_redire','type'=>'yesno','name'=>__('Redirect after form submission'),'afterstatement'=>'fpr_redire', 'legend'=>'This will redirect the web page to a page you set upon successful form submission.'),
							array('id'=>'fpr_redire','type'=>'begin_afterstatement'),
								array('id'=>'fpr_redire_url','type'=>'text','name'=>'Redirect Page URL'),
							array('id'=>'fpr_redire','type'=>'end_afterstatement'),
				)),
				array(
					'id'=>'food_002a',
					'name'=>__('Reservation Form Additional Fields','foodpress'),
					'tab_name'=>__('Form Fields','foodpress'),
					'fields'=>$this->foodpress_reservation_settings_003()
				),
				array(
					'id'=>'food_002',
					'name'=>__('Reservation Notifications & Emails','foodpress'),
					'tab_name'=>__('Notifications & Emails','foodpress'),
					'fields'=>array(
						array('id'=>'fpr_notif','type'=>'yesno','name'=>__('Notify admin upon new reservation','foodpress'),'afterstatement'=>'fpr_notif'),
							array('id'=>'fpr_notif','type'=>'begin_afterstatement'),

								array('id'=>'fpr_ntf_admin_to','type'=>'text','name'=>__('Email address to send notification. (eg. you@domain.com)','foodpress'), 'legend'=>__('You can add multiple email addresses seperated by commas to receive notifications of event submissions.','foodpress'),'default'=>$__fp_admin_email),
								array('id'=>'fpr_ntf_admin_from','type'=>'text','name'=>__('From eg. My Name &lt;myname@domain.com&gt; - Default will use admin email from this website','foodpress'),),
								array('id'=>'fpr_ntf_admin_subject','type'=>'note','name'=>'Subject for emails can be changed via language'),

							array('id'=>'fpr_notif','type'=>'end_afterstatement'),


							array('id'=>'fpr_notsubmitter','type'=>'subheader','name'=>__('Notify submitter upon receipt of the reservation','foodpress'), ),

							array('id'=>'fpr_ntf_user_from','type'=>'text','name'=>__('From eg. My Name &lt;myname@domain.com&gt; - Default will use admin email from this website','foodpress'), 'default'=>$__fp_admin_email),
							array('id'=>'fpr_ntf_drf_link','type'=>'text','name'=>__('Contact for help link','foodpress'),'default'=>site_url(), 'legend'=>__('This will be added to the bottom of reservation confirmation email sent to customer','foodpress'),),


							array('id'=>'fpr_ntf_admin_subject','type'=>'note','name'=>'Subject for emails can be changed via language'),


							array('id'=>'fpr_fcx','type'=>'note','name'=>__('To override and edit the confirmation email to customer, COPY php file from "../foodpress/templates/email/reservation-confirmation.php" to  "../wp-content/yourtheme/foodpress/templates/email/reservation-confirmation.php.','foodpress'),),

							array('id'=>'fpr_3_000','type'=>'code','name'=>'Preview Emails','value'=>$this->foodpress_reservation_settings_001()
										),

				)),array(
					'id'=>'food_003',
					'name'=>__('Reservation Submissions','foodpress'),
					'tab_name'=>__('Reservations','foodpress'),
					'fields'=>array(
						array('id'=>'fp_note',
							'type'=>'code',
							'value'=>$this->foodpress_reservation_settings_002()
						),

				))
			)
		);
	}


	// reservations section
	function foodpress_reservation_customcode($opt6){
		ob_start();
			echo "<div class='fp_reservation_time_settings'>";

			/*
			echo "<p class='subheader_fp'>Reservable Time Periods</p>";
			echo "<div class='fp_reservable_times fp_reservable'>";
			echo "<p>Sunday 1:00am - 4:00pm <span>X</span><input type='hidden' name='fp_res[]' value=''/></p>";
			echo "</div>";

			echo "<div class='add_reservation_time'>";
			echo "<p>".__('Add a time slot for reservable times','foodpress')."</p>";
			echo "<p class='add_reservation_time_row'>DATE <select name='' class='reservation_date'>
				<option value='0'>Everyday</option>
				<option value='1'>Monday</option>
				<option value='2'>Tuesday</option>
				<option value='3'>Wednesday</option>
				<option value='4'>Thursday</option>
				<option value='5'>Friday</option>
				<option value='6'>Saturday</option>
				<option value='7'>Sunday</option>
				</select>";
			echo " FROM <select class='from'>";
			foreach(foodpress_get_times(1) as $opt){
				echo "<option value='{$opt}'>{$opt}</option>";
			}

			echo "</select>";
			echo " TO <select class='to'>";
			foreach(foodpress_get_times(1) as $opt){
				echo "<option value='{$opt}'>{$opt}</option>";
			}
			echo "</select> <span id='add_reservation_time_slot' class='add_reservation_time_slot'>".__('ADD TIME SLOT','foodpress')."</span> <em></em></p>";
			echo "</div>";
			*/

			// unreservanles
			echo "<p class='subheader_fp' style='padding-top:25px;'>UN-Reservable Days</p>";
			echo "<div class='fp_unreservable_dates fp_reservable'>";

			if(!empty($opt6['fp_unres'])){
				foreach($opt6['fp_unres'] as $dates){
					echo "<p>{$dates}<span>X</span><input type='hidden' name='fp_unres[]' value='{$dates}'/></p>";
				}
			}

			echo "</div>";
			echo "<div class='add_reservation_time'>";
			echo "<p>".__('Add date that is NOT reservable','foodpress')."</p>";
			echo "<p class='add_reservation_time_row'>DATE <input id='fp_input_unreserve' class='unreservable_date'/>";
			echo "<span id='add_unreserve_date' class='add_reservation_time_slot' >".__('ADD DATE','foodpress')."</span> <em></em>";
			echo "</p></div>";

			echo "</div>";

		return ob_get_clean();
	}

	//preview emails
	function foodpress_reservation_settings_001(){
		global $foodpress;
		ob_start();
		echo "<a href='".get_admin_url()."admin.php?page=foodpress&tab=food_6&action=nf#food_002' class='fp_admin_btn btn_triad'>Preview Notification Email</a>";
		echo "<a href='".get_admin_url()."admin.php?page=foodpress&tab=food_6&action=cf#food_002' class='fp_admin_btn btn_triad'>Preview Confirmation Email</a>";
		echo "<a href='".get_admin_url()."admin.php?page=foodpress&tab=food_6&action=cancelf#food_002' class='fp_admin_btn btn_triad'>Preview Cancellation Email</a>";
		if(!empty($_GET['action'])){

			//$type = ($_GET['action']=='nf')?'notification':'confirmation';
			switch($_GET['action']) {
				case 'nf':
					$type = 'notification';
				break;
				case 'cf':
					$type = 'confirmation';
				break;
				case 'cancelf':
					$type = 'cancellation';
				break;
				default:
					$type = 'notification';
			}
			echo $foodpress->reservations->get_email_preview('jboune@blackbriar.com',$type);
		}
		return ob_get_clean();
	}

	// reservations submission entries settings
	function foodpress_reservation_settings_002(){
		global $foodpress;
		return $foodpress->admin->reservation_UI();
	}

	// additional fields for reservation format
	function foodpress_reservation_settings_003(){
		$cmf_add_res[] = array('id'=>'fpr_phonenumber','type'=>'yesno','name'=>__('Phone Number','foodpress'), 'legend'=>'Add phone number field to the form', 'afterstatement'=>'fp_res_phone_req');
			$cmf_add_res[] = array('id'=>'fp_res_phone_req','type'=>'begin_afterstatement');
			$cmf_add_res[] = array('id'=>'fp_res_phone_req','type'=>'yesno','name'=>__('Required field','foodpress'));
			//$cmf_add_res[] = array('id'=>'fp_res_phone_pattern','type'=>'text','name'=>__('Input pattern for phone number','foodpress'), 'legend'=>'You can type custom phone number format masks. Use this guide.');
			$cmf_add_res[] = array('id'=>'fp_res_phone_req','type'=>'end_afterstatement');

		$cmf_add_res[] = array('id'=>'fpr_location','type'=>'yesno','name'=>__('Restaurant Location','foodpress'), 'legend'=>'Locations of restaurants pulled from Menu Items > Locations');

		$cmf_add_res[] = array('id'=>'fpr_validation','type'=>'yesno','name'=>__('Validate form before submission','foodpress'), 'legend'=>'Add form validation to make sure it is an intended reservation before submission');

		$cmf_add_res[] =array('id'=>'fpr_notif','type'=>'note','name'=>__('Activate additional form fields the reservation form.<br/>NOTE: Each additional field names can also be changed under language settings, which will override the field names set in here.','foodpress') ) ;

		$resevation_for_fields = foodpress_get_reservation_form_fields();
		for($x=1; $x<=$resevation_for_fields; $x++){
			$cmf_add_res_ = array(
				array('id'=>'fp_af_'.$x,'type'=>'yesno','name'=>__('Activate Additional Field #'.$x,'foodpress'),'legend'=>__('This will activate additional menu item field.','foodpress'),'afterstatement'=>'fp_af_'.$x),
					array('id'=>'fp_af_'.$x,'type'=>'begin_afterstatement'),
					array('id'=>'fp_ec_f'.$x,'type'=>'text','name'=>__('Field Name*','foodpress'),),
					array('id'=>'fp_ec_fv'.$x,'type'=>'text','name'=>__('Field Values (comma separated)','foodpress'),'legend'=>'If content type is "select": seperate each select value by comma and enter in here, otherwise this will work as placeholder text for field. Do not leave spaces between values.'),
					array('id'=>'fp_ec_fb'.$x,'type'=>'dropdown','name'=>__('Content Type','foodpress'), 'options'=>array(
						'text'=>__('Single Line Text Field','foodpress'),
						'select'=>__('Select Field','foodpress'),
						'checkbox'=>__('Checkbox Field','foodpress'),
						'multiline'=>__('Multiple Lines of Text Field','foodpress'),
						)),
					array('id'=>'fp_ec_req_'.$x,'type'=>'yesno','name'=>__('Required field'),'legend'=>'This will make this field required on front-end form except select and checkbox fields.'),
					array('id'=>'fp_af_'.$x,'type'=>'end_afterstatement'),
			);
			$cmf_add_res = array_merge($cmf_add_res, $cmf_add_res_);
		}

		return $cmf_add_res;
	}









}






?>
