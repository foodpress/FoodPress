<?php
/**
 * foodpress reservations main class.
 *
 * @class 		foodpress_reservations
 * @version		1.0.0
 * @package		foodpress/Classes
 * @category	Class
 * @author 		AJDE
 */

class foodpress_reservations {

	private $resOPT;

	public $shortcode_args = array();

	public $lang = 'L1';

	public function __construct(){
		//add_action('admin_menu', array( $this,'menu'));
		$this->opt2 = get_option('fp_options_food_2');
		$this->resOPT = get_option('fp_options_food_6');

		// send email upon reservation confirmation
		//add_action('future_to_publish',array($this,'send_new_event_email'), 10, 1);
		//add_action('new_to_publish',array($this,'send_new_event_email'), 10, 1);
		add_action('draft_to_publish',array($this,'send_confirmatiion_from_post'), 10, 1);
	}

	// Send emails
		// SEND notification and confirmation emails
			public function successful_reservation_emails($reservation_id, $args){
				$resOPT = $this->resOPT;
				$siteemail = get_bloginfo('admin_email');

				// language
				$this->lang = (!empty($args['lang']))? $args['lang']:'L1';

				// confirmation email
				// if admin approval need for reservation dont send confirmation email
				if(!empty($resOPT['fpr_draft']) && $resOPT['fpr_draft']=='yes'){
				}else{
					$this->send_confirmation_email($reservation_id, $args);
				}

				// notification email
					if(!empty($resOPT['fpr_notif']) && $resOPT['fpr_notif']=='yes'){
						$n_to = (!empty($resOPT['fpr_ntf_admin_to']))? $resOPT['fpr_ntf_admin_to']: $siteemail;
						$n_from = (!empty($resOPT['fpr_ntf_admin_from']))? $resOPT['fpr_ntf_admin_from']: $siteemail;
						$n_subject = $this->get_lang('','fprsvp_004','New Reservation Submission!');

						$this->send_email($n_to, $n_from, $n_subject, 'notification', $reservation_id, $args);
					}
			}
		// SEND confirmation email when reservation is confirmed
			public function send_confirmatiion_from_post($post){
				$reservation_id = $post->ID;
				if($post->post_type!='reservation')
					return;

				if(!empty($reservation_id))
					$this->send_confirmation_email($reservation_id);
			}
		// SEND confirmation email for reservation
			public function send_confirmation_email($reservation_id, $args=''){

				$res_pmv = get_post_custom($reservation_id);
				$lang = !empty($args['lang'])? $args['lang']:
					( !empty($res_pmv['lang'])? $res_pmv['lang'][0]:'L1');

				$resOPT = $this->resOPT;
				$siteemail = get_bloginfo('admin_email');

				$c_to = (!empty($res_pmv['email_address']))?
					$res_pmv['email_address'][0]:(!empty($res_pmv['email'])? $res_pmv['email'][0]:false);

				// stop if reserver email is missing
				if(!$c_to) return;

				$c_from = (!empty($resOPT['fpr_ntf_user_from']))? $resOPT['fpr_ntf_user_from']: $siteemail;
				$c_subject = $this->get_lang('','fprsvp_003','We have received your reservation!');

				$this->send_email($c_to, $c_from, $c_subject, 'confirmation', $reservation_id, array('lang'=>$lang));

				update_post_meta($reservation_id, 'aaa','done');

			}

		// this will use foodpress default email templates to send emails
			public function send_email($to, $from, $subject, $email_type='notification', $reservation_id ='', $sc_args, $echo = false){

				global $foodpress;

				$resOPT = $this->resOPT;

				$email_template_file_name = ($email_type=='notification')? 'reservation-notification':'reservation-confirmation';

				$path = FP_PATH2.'templates/email/';

				$headers = 'From: '.$from;

				// output or process
					if($echo){
						$args = array(
							'type'=>$email_type,
							'temp'=>true,
							'lang'=>$this->lang,
							'args'=>array(
								'name'=>array("Jason Bourne"),
								'reservation_id'=>'123',
								'time'=>array('12:45 AM'),
								'date'=>array('03/18/2015'),
								'email_address'=>array($to),
								'people'=>array('4'),
							),
						);
						$message_ = $foodpress->get_email_body($email_template_file_name,$path, $args);
						return array(
							'headers'=>$headers,
							'to'=>$to,
							'subject'=>$subject,
							'message'=>$message_
						);
					}else{
						$args = array(
							'reservation_id'=>$reservation_id,
							'type'=>$email_type, // notification email or confirmation email
							'lang'=>(!empty($sc_args['lang'])? $sc_args['lang']:'L1'),
						);
						$message_ = $foodpress->get_email_body($email_template_file_name,$path, $args);
						add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
						return wp_mail($to, $subject, $message_, $headers);
					}

			}
		// preview of emails that are sent out
			function get_email_preview($to, $type){
				$resOPT = $this->resOPT;
				$siteemail = get_bloginfo('admin_email');

				//$this->lang = 'L2';

				if($type=='confirmation'){
					$from = (!empty($resOPT['fpr_ntf_user_from']))? $resOPT['fpr_ntf_user_from']: $siteemail;
					$subject = $this->get_lang('','fprsvp_003','We have received your reservation!');
				}else{
					$from = (!empty($resOPT['fpr_ntf_admin_from']))? $resOPT['fpr_ntf_admin_from']: $siteemail;
					$subject =  $this->get_lang('','fprsvp_004','New Reservation Submission!');
				}

				$email = $this->send_email($to, $from, $subject, $type, '', '', true);

				ob_start();
				echo '<div class="foodpress_email_preview"><p>'.$email['headers'].'</p>';
				echo '<p>To: '.$email['to'].'</p>';
				echo '<p>Subject: '.$email['subject'].'</p>';
				echo '<div class="foodp_email_preview_body">'.$email['message'].'</div></div>';
				return ob_get_clean();
			}

	// RETURN values to be used in other parts of the site from reservation
		public function get_rsvp_count(){
			$rsvp = new WP_Query( array(
				'post_type'=>'reservation',
				'posts_per_page'=>-1,
				'post_status'=>'any'
			));

			$current_time = current_time('timestamp');

			$upcoming = $past = $pending = 0;

			if($rsvp->have_posts()):
				while($rsvp->have_posts()): $rsvp->the_post();

					if($rsvp->post->post_status!='publish'){
						$pending++;
						continue;
					}

					$_id = get_the_ID();

					$rmeta = get_post_custom($_id);

					$rsvp_time = strtotime($rmeta['date'][0].' '.$rmeta['time'][0]);

					// compare to current time to find if the rsvp is in past or future
					if($current_time< $rsvp_time){
						$upcoming++;
					}else{
						$past++;
					}

				endwhile;

				return array('upcoming'=>$upcoming, 'past'=>$past, 'pending'=>$pending);
			endif;
		}

		// send out list of rsvp for foodpress settings page
			public function get_rsvp_list($type='upcoming'){
				$rsvp = new WP_Query( array(
					'post_type'=>'reservation',
					'posts_per_page'=>-1,
					'post_status'=>'publish'
				));

				$current_time = current_time('timestamp');

				$upcoming = $past = 0;


				if($rsvp->have_posts()):
					ob_start();

					while($rsvp->have_posts()): $rsvp->the_post();

						$_id = get_the_ID();
						$rmeta = get_post_custom($_id);
						$_status = (!empty($rmeta['status']))? $rmeta['status'][0]:'none';

						$rsvp_time = strtotime($rmeta['date'][0].' '.$rmeta['time'][0]);

						// compare to current time to find if the rsvp is in past or future
						if($type=='upcoming' && $current_time<=$rsvp_time || $type=='past' && $current_time> $rsvp_time){

							$name = (!empty($rmeta['name']))? $rmeta['name'][0]:
								( !empty($rmeta['first_name'])?
									$rmeta['last_name'][0].', '.$rmeta['first_name'][0] : '-name-');

							$email = (!empty($rmeta['email']))?
								$rmeta['email'][0]: (!empty($rmeta['email_address'])? $rmeta['email_address'][0]:null);

							?>
								<p class='fp_reservation'><span class='party'><?php echo $rmeta['people'][0];?></span>
								<br /><b><?php echo 'Reservation #'.$_id.' '. $name.' <br /><i>('.$email.')</i>';?></b>
								<br/>Time: <?php echo $rmeta['date'][0];?> @ <?php echo $rmeta['time'][0]; echo !empty($rmeta['end_time'])? ' - '.$rmeta['end_time'][0]:null?>
								<br />Phone: <?php echo (!empty($rmeta['phone_number'])? $rmeta['phone_number'][0]: '-');?>
								<br/><i class='Dres'data-rid='<?php echo $_id;?>'>Delete Reservation</i></p>
							<?php
						}

					endwhile;

					$content = ob_get_clean();

					if(!empty($content)){
						$content = "<p class='header'>Reservations <span>Size of the party</span></p>".$content;
					}else{ $content=false;}


					return $content;
				else:
					return false;
				endif;
			}

		// delete a reservation
			public function delete_reservation($reservation_id){
				$reservation = get_post($reservation_id, 'ARRAY_A');
				$reservation['post_status']= 'trash';
				wp_update_post($reservation);

				return 'success';
			}

	// reservation BUTTON for front-end
	// calls from class-fp-shortcodes.php
		public function output_reservation_button($args=''){

			// shortcode default values
			$this->lang = (!empty($args['lang']))? $args['lang']: 'L1';
			$opt = $this->opt2;
			$defaults = array(
				'id'=>'1',
				'header'=>$this->get_lang($opt, 'fp_lang_res_btn_h1', 'Make A Reservation Now'),
				'subheader'=>$this->get_lang($opt, 'fp_lang_res_btn_h2', 'Click here to reserve a spot at our restaurant for tonights dinner'),
				'type'=>'popup',
				'lang'=>'L1',
				'res_link'=>'',
				'res_link_new'=>'no'
			);

			// update with values passed
			$args = (!empty($args))? shortcode_atts($defaults, $args): $defaults;
			$this->shortcode_args = $args;

			ob_start();

			if(!empty($args['type']) && $args['type']=='onpage'){
				$this->output_reservation_form($args);
			}else{

				$link = (!empty($args['res_link']))? $args['res_link']:false;
				$new_window = (!empty($args['res_link_new']) && $args['res_link_new']=='yes')? '1':'0';

				?>
				<div class='fp_res_button' data-link='<?php echo $link;?>' data-new='<?php echo $new_window;?>'>
					<p class='fp_res_t1 text'><?php echo $args['header'];?></p>
					<p class='fp_res_t2'><?php echo $args['subheader'];?></p>
				</div>
				<?php
				add_action('wp_footer', array($this, 'output_reservation_form'));
			}

			return ob_get_clean();
		}

	// reservation form from template file
		function output_reservation_form($args=''){
			global $foodpress;

			// shortcode arguments for the form
			$args = !empty($args)? $args: $this->shortcode_args;

			// re-call the option values
			$opt = $this->opt2;
			$opt6 = $this->resOPT;

			$_onpage = (!empty($args['type']) && $args['type']=='onpage')?true:false;

			$multi_times_boxes = ( !empty($opt6['fpr_startend']) && $opt6['fpr_startend'] =='yes')? true:false;

			ob_start();

			// set language
			$this->lang = (!empty($args['lang']))? $args['lang']:'L1';

			?>

		<!-- Form Html -->
		<?php if(!$_onpage):?><div class="fp_res_overlay"></div><?php endif;?>

		<?php
			$__24block = (!empty($opt6['fpr_24_block']) && $opt6['fpr_24_block']=='yes')? '1':'0';
			$redirect = $foodpress->functions->redirect($opt6);
		?>

		<div id='fp_make_res' class='fp_make_res <?php echo ($_onpage)?'onpage':'popup'; ?>' data-lang='<?php echo $args['lang'];?>' data-type='<?php echo $args['type'];?>' data-vald='<?php echo (!empty($opt6['fpr_validation']) && $opt6['fpr_validation']=='yes')?1:0;?>' data-blk24='<?php echo $__24block;?>' data-redirect='<?php echo $redirect? $redirect:'no';?>'>
			<div class='inside'>
				<a id='fp_close'><i class="fa fa-times"></i></a>
				<h2 class='title'><?php echo $this->get_lang($opt, 'fp_lang_resform_001', 'Make a Reservation'); ?></h2>
				<p class='subtitle'><?php echo $this->get_lang($opt, 'fp_lang_resform_A2', 'For further questions, please call'); ?></p>
				<p class="divider"></p>

				<div class='reservation_section'>
				<?php
					$__time_incre = (!empty($opt6['fpr_time_incer']))?
						( (in_array($opt6['fpr_time_incer'], array('1','2','4','12')))?
							$opt6['fpr_time_incer']: 2): 2;
					$__time_format = (!empty($opt6['fpr_24hr']) &&  $opt6['fpr_24hr']=='yes')? '24':'12';
					$__time_restrict = (!empty($opt6['fpr_timesl']) &&  $opt6['fpr_timesl']=='yes')? 'yes':'no';
					$__time_start = (!empty($opt6['fpr_start_time']) )? $opt6['fpr_start_time']:'-';
					$__time_end = (!empty($opt6['fpr_end_time']) )? $opt6['fpr_end_time']:'-';

					// date format
					$date_format = foodpress_get_timeNdate_format($opt6);
					$placeholder_dateformat = (!empty($opt6['fpr_univ_date_format']) && $opt6['fpr_univ_date_format']=='yes')?get_option('date_format'): 'Y/m/d';

					// unreservable dates
						$unres = (!empty($opt6['fp_unres']) && is_array($opt6['fp_unres'])) ? 'yes':'no';
						$str = '';
						if($unres=='yes'){
							foreach($opt6['fp_unres'] as $date){
								$str .= '<i>'.$date.'</i>';
							}
						}
				?>
				<div class="fpres_form_datetime form_section_1 step" data-format='<?php echo $__time_format;?>' data-restrict='<?php echo $__time_restrict;?>' data-start='<?php echo $__time_start;?>' data-end='<?php echo $__time_end;?>' data-incre='<?php echo $__time_incre;?>' data-dateformat='<?php echo $date_format[0];?>' >
					<p class='date'><span><?php echo $this->get_lang($opt, 'fp_lang_resform_A5', 'Date'); ?></span><input class='resinput req fp_res_short_input fp_res_input_icon_calendar' type="text" name="date" size="30" value="" id="fp_res_date" placeholder="<?php echo $this->get_lang($opt, 'fp_lang_resform_A5A', 'Select Date'); ?>" autocomplete="off" data-unres='<?php echo $unres;?>' readonly='true'/><em id='fp_unres' style='display:none'><?php echo $str;?></em></p>

					<p class='time' data-type='<?php echo ($multi_times_boxes)? 'multi':'';?>'><span><?php echo $this->get_lang($opt, 'fp_lang_resform_A4', 'Time'); ?></span>
						<?php
							// if show both start and end time slots
							if( $multi_times_boxes):	?>

								<select name="time" class="fpres_time_range resinput req fp_res_short_input fp_res_input_icon_clock"  id="fp_res_time_start"><?php
								foreach(foodpress_get_times($__time_incre) as $time){
									echo "<option value='{$time}'>{$time}</option>";
								}
								?></select>
								<select name="end_time" class="fpres_time_range resinput req fp_res_short_input fp_res_input_icon_clock"  id="fp_res_time_end"><?php
								foreach(foodpress_get_times($__time_incre) as $time){
									echo "<option value='{$time}'>{$time}</option>";
								}
								?></select>

								<?php /*
								<input class='resinput req fp_res_short_input fp_res_input_icon_clock' type="text" name="time" size="30" value="" id="fp_res_time" placeholder="<?php echo (!empty($opt6['fpr_24hr']) &&  $opt6['fpr_24hr']=='yes')? 'eg. 13:00':'eg. 1:00pm';?>" autocomplete="off" readonly='true'>
								<input class='resinput req fp_res_short_input fp_res_input_icon_clock' type="text" name="end_time" size="30" value="" id="fp_res_time" placeholder="<?php echo (!empty($opt6['fpr_24hr']) &&  $opt6['fpr_24hr']=='yes')? 'eg. 13:00':'eg. 1:00pm';?>" autocomplete="off">
								*/?>
						<?php	else:	?>

							<select name="time" class="fpres_time_range resinput req fp_res_short_input fp_res_input_icon_clock"  id="fp_res_time"><?php
							foreach(foodpress_get_times($__time_incre) as $time){
								echo "<option value='{$time}'>{$time}</option>";
							}
							?></select>
							<?php /*
							<input class='resinput req fp_res_short_input fp_res_input_icon_clock' type="text" name="time" size="30" value="" id="fp_res_time" placeholder="<?php echo (!empty($opt6['fpr_24hr']) &&  $opt6['fpr_24hr']=='yes')? 'eg. 13:00':'eg. 1:00pm';?>" autocomplete="off" />
							*/?>

						<?php endif;?>
					</p>

					<p class='size'><span><?php echo $this->get_lang($opt, 'fp_lang_resform_A3', 'Party Size'); ?></span>
						<?php
							$_partysize_cap = ( !empty($opt6['fpr_partysize']) && $opt6['fpr_partysize'] =='yes' && !empty($opt6['fpr_partysz_num']))? $opt6['fpr_partysz_num']: false;

							// if party size cap set
							if($_partysize_cap){
								echo "<select name='party' class='resinput req fp_res_short_input' value='' id='fp_res_people' autocomplete='off'>";
								for($x=1; $x<=(int)$_partysize_cap; $x++){
									echo "<option value='{$x}'>{$x}</option>";
								}
								echo "</select>";
							}else{
								echo '<input class="resinput req fp_res_short_input" type="text" name="party" placeholder="'.$this->get_lang($opt, 'fp_lang_resform_004dB', 'Qty').'" value="1" id="fp_res_people" autocomplete="off">';
							}
						?>
					</p>
					<div class="clear"></div>
				</div>
				<p class="divider"></p>
				<div class="form_section_2">
					<p>
						<label for=""><?php echo $this->get_lang($opt, 'fp_lang_resform_004a', 'Your Name'); ?></label>
						<input type="text" name='name' class='resinput req' placeholder='<?php echo $this->get_lang($opt, 'fp_lang_resform_004a', 'Your Name'); ?>'/>
					</p>
					<p>
						<label for=""><?php echo $this->get_lang($opt, 'fp_lang_resform_004c', 'Email Address'); ?></label>
						<input type="text" name='email' class='resinput req' placeholder='<?php echo $this->get_lang($opt, 'fp_lang_resform_004c', 'Email Address'); ?>'/>
					</p>
					<?php
					// phone number field
					if(!empty($opt6['fpr_phonenumber']) && $opt6['fpr_phonenumber']=='yes'):
					?>
						<p>
							<label for="phone"><?php echo $this->get_lang($opt, 'fp_lang_resform_004d', 'Phone Number'); ?></label>
							<input id="fp_phone_" type="tel" name="phone" />
							<br>
							<span id="phone-valid-msg" class="hide">✓ Valid</span>
							<span id="phone-error-msg" class="hide">Invalid number</span>
					<?php endif;

					// Restaurant Location
					if(!empty($opt6['fpr_location']) && $opt6['fpr_location']=='yes'):
						$locations = get_terms('menu_location');
						if(!empty($locations)):
					?>
						<p>
							<label for="location"><?php echo $this->get_lang($opt, 'fp_lang_resform_004e', 'Restaurant Location'); ?></label>
							<select id='fp_location_<?php echo rand(100,113);?>' class='resinput' name='location'>
							<?php foreach($locations as $location){
								echo "<option value='{$location->slug}'>{$location->name}</option>";
								}?>
							</select>
						</p>
					<?php endif; endif; ?>
					<?php

						// additional form fields
						for($x=1; $x<=foodpress_get_reservation_form_fields(); $x++){
							// check if fields are good
							if( !empty($opt6['fp_af_'.$x]) && $opt6['fp_af_'.$x]=='yes' && !empty($opt6['fp_ec_f'.$x]) ){

								$field_name = $this->get_lang($opt, 'fp_ec_f'.$x, $opt6['fp_ec_f'.$x]);
								$required = (!empty($opt6['fp_ec_req_'.$x]) && $opt6['fp_ec_req_'.$x]=='yes')? 'req':'';

								switch ($opt6['fp_ec_fb'.$x]) {
									case 'text':
										echo "<p><input title='".$field_name."' class='resinput {$required}' type='text' name='".'fp_af_'.$x."' placeholder='".$field_name."'/></p>";
										break;
									case 'select':
										$values = $opt6['fp_ec_fv'.$x];
										if(!empty($values)){ // if field values present
											//$values = str_replace(' ', '', $values);
											$vals = explode(',', $values);
											echo "<p><label>".$field_name."</label>";
											echo "<select class='resinput' name='".'fp_af_'.$x."'>";
											foreach($vals as $values){
												$values = ltrim($values);
												echo "<option value='{$values}'>{$values}</option>";
											}
											echo "</select></p>";
										}
										break;
									case 'checkbox':
										echo "<p><input class='resinput check' name='".'fp_af_'.$x."' type='checkbox' /> ".$field_name."</p>";
										break;
									case 'multiline':
										echo "<p><label>".$field_name."</label><textarea class='resinput multiline {$required}' name='".'fp_af_'.$x."'></textarea></p>";
										break;
									default:	break;
								}
							}
						}
					?>

					<?php
						// form validation
						if(!empty($opt6['fpr_validation']) && $opt6['fpr_validation']=='yes'){
							$rand = (int)rand(1,7);
							echo "<p class='validation code_{$rand}' data-val='{$rand}'><span></span><input class='resinput' type='text' placeholder='".$this->get_lang($opt, 'fp_lang_resform_si008','Type the code to validate')."'/></p>";
						}
					?>

					<a id="fp_reservation_submit" class="reserve-submit fp_reservation_submit" ><?php echo $this->get_lang($opt, 'fp_lang_resform_A1', 'Reserve Now'); ?></a>

					<?php
					// privacy and terms of use statement
						if(!empty($opt6['fpr_privacy']) || !empty($opt6['fpr_terms'])):

							$_privacy = !empty($opt6['fpr_privacy'])?
								"<a href='".$opt6['fpr_privacy']."'/>".$this->get_lang($opt, 'fp_lang_resform_si006', 'Privacy Policy')."</a>":null;
							$_terms = !empty($opt6['fpr_terms'])?
								"<a href='".$opt6['fpr_terms']."'/>".$this->get_lang($opt, 'fp_lang_resform_si007', 'Terms of Use')."</a>":null;
						?>
						<p class='terms'><?php echo $this->get_lang($opt, 'fp_lang_resform_si005', 'By clicking you agree with our ');?><?php echo $_terms;?> <?php echo $_privacy;?></p>
						<?php endif;?>
				</div>
				</div>

				<!-- form messages -->
				<div class="form_message">
					<p class='error' style='display:none'>Error Message</p>
					<div class='fp_res_success'>
						<div class='fp_res_success_icon'></div>
						<div class='fp_res_success_title'><span class='name'></span> <?php echo $this->get_lang($opt, 'fp_lang_resform_s001', 'Awesome, we got your reservation!'); ?></div>
						<p class='reservation_info'><?php echo $this->get_lang($opt, 'fp_lang_resform_si001', 'Reservation Information');?><span></span></p>
						<div class='fp_res_success_message'><?php echo $this->get_lang($opt, 'fp_lang_resform_s002', 'If there are any issues with this reservation we will give you a call. Otherwise, we look forward to seeing you soon!');?></div>
					</div>
					<?php
						$notifications = array(
							'succ_m'=> $this->get_lang($opt, 'fp_lang_resform_n001', 'Posted successfully'),
							'err'=> $this->get_lang($opt, 'fp_lang_resform_n002', 'Required fields missing'),
							'err2'=> $this->get_lang($opt, 'fp_lang_resform_n003', 'Invalid email address'),
							'err3'=> $this->get_lang($opt, 'fp_lang_resform_n004', 'Invalid phone number'),
							'err4'=> $this->get_lang($opt, 'fp_lang_resform_n005', 'Could not create new reservation. Please try again later'),
							'err5'=> $this->get_lang($opt, 'fp_lang_resform_n006', 'Validation code does not match, please try again!'),
							'res2'=> $this->get_lang($opt, 'fp_lang_resform_si002', 'Date'),
							'res3'=> $this->get_lang($opt, 'fp_lang_resform_si003', 'Time'),
							'res4'=> $this->get_lang($opt, 'fp_lang_resform_si004', 'Party Size'),
						);
					?>
					<div id='fpres_form_msg' class='fpres_form_msg' style='display:none'><?php echo json_encode($notifications);?></div>
				</div>
			</div>
		</div>
		<?php if(!$_onpage):?><div class='fpres_bg'></div><?php endif;?>

			<?php
			echo ob_get_clean();

		}

	// for language
		function get_lang($opt='', $var, $default){
			$opt = !empty($opt)? $opt: $this->opt2;
			$lang = !empty($this->lang)? $this->lang:'L1';
			return foodpress_get_custom_language($opt, $var, $default, $lang);
		}

	// get checkin status
		function get_checkin_status($status='', $lang=''){

			if(empty($status)){
				return $this->def_status();
			}else{
				$opt2 = $this->opt2;
				$lang = (!empty($lang))? $lang : 'L1';

				if($status=='checked'){
					return (!empty($opt2[$lang]['fp_lang_resform_R2']))? $opt2[$lang]['fp_lang_resform_R2']: 'checked';
				}else{
					return (!empty($opt2[$lang]['fp_lang_resform_R1']))? $opt2[$lang]['fp_lang_resform_R1']: $this->def_status();
				}
			}
		}
		function def_status(){
			return 'check-in';
		}

}
