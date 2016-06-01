<?php
/**
 * Functions for the settings page in admin.
 *
 * The settings page contains options for the foodpress plugin - this file contains functions to display
 * and save the list of options.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	foodpress/Admin/Settings
 * @version     1.2.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Store settings in this array */
global $foodpress_settings;

if ( ! function_exists( 'foodpress_settings' ) ) {
	/**
	 * Settings page.
	 */
	function foodpress_settings() {
		global $foodpress;		
		
		// hook for settings beginning
		do_action('foodpress_settings_start');		
		
		// Settings Tabs array
		$food_tabs = apply_filters('foodpress_settings_tabs',array(
			'food_1'=>__('Settings','foodpress'), 			
			'food_2'=>__('Language','foodpress'),
			'food_3'=>__('Styles','foodpress'),
			'food_6'=>__('Reservations','foodpress'),
			'food_5'=>__('Addons & Licenses','foodpress'),
			'food_4'=>__('Support','foodpress'),
		));
		
		
		// Get current tab/section
		$focus_tab = (isset($_GET['tab']) )? sanitize_text_field( urldecode($_GET['tab'])):'food_1';	
		$current_section = (isset($_GET['section']) )? sanitize_text_field( urldecode($_GET['section'])):'';	
		
		
		// Update or add options
		if( isset($_POST['food_noncename']) && isset( $_POST ) ){
			if ( wp_verify_nonce( $_POST['food_noncename'], plugin_basename( __FILE__ ) ) ){
				
				$food_options=array();

				// FOREACH post values
				foreach($_POST as $pf=>$pv){
					if( $pf!='food_styles' ){
						
						$pv = (is_array($pv))? $pv: ($pv);
						$food_options[$pf] = $pv;
					}					
				}

				// General settings page - write styles to head option
				if($focus_tab=='food_1' && isset($_POST['fp_css_head']) && $_POST['fp_css_head']=='yes'){

					ob_start();
					include(FP_PATH.'/assets/css/dynamic_styles.php');

					$fp_dyn_css = ob_get_clean();
					
					update_option('fp_dyn_css', $fp_dyn_css);

					do_action('foodpress_save_settings_food_1');
				}

				// language tab
				if($focus_tab=='food_2'){
					$new_lang_opt ='';
					$_lang_version = (!empty($_GET['lang']))? $_GET['lang']: 'L1';

					$lang_opt = get_option('fp_options_food_2');
					if(!empty($lang_opt) ){
						$new_lang_opt[$_lang_version] = $food_options;
						$new_lang_opt = array_merge($lang_opt, $new_lang_opt);

					}else{
						$new_lang_opt[$_lang_version] =$food_options;
					}

					//print_r($new_lang_opt);
					update_option('fp_options_food_2', $new_lang_opt);	

				}else{
					update_option('fp_options_'.$focus_tab, $food_options);
				}				
				
				// STYLES
				if( isset($_POST['food_styles']) )
					update_option('food_styles', strip_tags(stripslashes($_POST['food_styles'])) );
				
				$_POST['settings-updated']='true';

				foodpress_generate_options_css();
							
			}else{
				die( __( 'Action failed. Please refresh the page and retry.', 'foodpress' ) );
			}	
		}
		
		// Load foodpress settings values for current tab
		$current_tab_number = substr($focus_tab, -1);
		if(!is_numeric($current_tab_number)){ // if the tab last character is not numeric then get the whole tab name as the variable name for the options 
			$current_tab_number = $focus_tab;
		}


		$food_opt[$current_tab_number] = get_option('fp_options_'.$focus_tab);
		
?>
<div class="wrap" id='fp_settings'>
	<div id='foodpress'><div id="icon-themes" class="icon32"></div></div>
	<h2>foodPress Settings (ver <?php echo get_option('foodpress_plugin_version');?>) <?php do_action('foodpress_updates_in_settings');?></h2>
	<h2 class='nav-tab-wrapper' id='meta_tabs'>
		<?php					
			foreach($food_tabs as $nt=>$ntv){
				$food_notification='';

				echo "<a href='?page=foodpress&tab=".$nt."' class='nav-tab ".( ($focus_tab == $nt)? 'nav-tab-active':null)."' food_meta='food_1'>".$ntv.$food_notification."</a>";	
			}			
		?>		
	</h2>	
<div class='metabox-holder'>
	<div class="update-nag notice is-dismissible"><p><?php _e('Important: <b>Reservation</b> feature will be <b>discontinued</b> in the next foodpress update while we re-build it better as an addon!','foodpress');?></p></div>	
<?php
	$updated_code = (isset($_POST['settings-updated']) && $_POST['settings-updated']=='true')? '<div class="updated fade"><p>'.__('Settings Saved','foodpress').'</p></div>':null;
	echo $updated_code;

// TABS	
switch ($focus_tab):	

// General settings tab
	case "food_1":
	
		// Event type custom taxonomy
		$evt_name = (!empty($food_opt[1]['food_eventt']))?$food_opt[1]['food_eventt']:'Event Type';
		$evt_name2 = (!empty($food_opt[1]['food_eventt2']))?$food_opt[1]['food_eventt2']:'Event Type 2';
	?>
	<form method="post" action=""><?php settings_fields('food_field_group'); 
		wp_nonce_field( plugin_basename( __FILE__ ), 'food_noncename' );
	?>
	<div id="food_1" class=" foodpress_admin_meta food_focus">		
		<div class="inside">
			<?php
				// include settings tab content
				require_once(FP_PATH.'/includes/admin/settings/class-settings-settings.php');				
				$settings = new foodpress_settings_settings($food_opt);	
				$foodpress->load_ajde_backender();	
				print_ajde_customization_form($settings->content(), $food_opt[1]);				
			?>			
		</div>

		<p class='fp_settings_bottom'><input type="submit" class="btn_prime fp_admin_btn" value="<?php _e('Save Changes') ?>" /> <a id='resetColor' style='display:none' class='fp_admin_btn btn_secondary'><?php _e('Reset to default colors','foodpress')?></a><br/><i id='resetcolornote' style='display:none'><?php _e('NOTE: If you want to reset colors to default, click','foodpress')?> "<?php _e('Reset to default colors','foodpress')?>" <?php _e('button and click save changes.','foodpress');?></i></p>
	</div>	
	</form>
<?php  
	break;	

	// STYLES TAB
		case "food_3":
			
			echo '<form method="post" action="">';			
			//settings_fields('food_field_group'); 
			wp_nonce_field( plugin_basename( __FILE__ ), 'food_noncename' );
					
			// styles settings tab content
			require_once('settings_styles_tab.php');
		
		break;
	
	// Languages TAB
		case "food_2":

			//print_r( get_option('fp_options_food_2') );
			//delete_option('fp_options_food_2') ;

			$__lang_version = (!empty($_GET['lang']))? $_GET['lang']: 'L1';
			
			$lang_options = (!empty($food_opt[2][$__lang_version]) )? $food_opt[2][$__lang_version]:null;
			
			// Language variations
			$lang_variations = apply_filters('foodpress_lang_variation', array('L1','L2', 'L3'));
			
			$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);

		?>
		<form method="post" action=""><?php settings_fields('food_field_group'); 
				wp_nonce_field( plugin_basename( __FILE__ ), 'food_noncename' );
		?>
		<div id="food_2" class="postbox foodpress_admin_meta">	
			<div class="inside">
				<h2><?php _e('Type in custom language text','foodpress');?></h2>
				<h4><?php _e('Select your language','foodpress');?> <select id='fp_lang_selection' url=<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $uri_parts[0];;?>>		
				<?php
					foreach($lang_variations as $lang){
						echo "<option value='{$lang}' ".(($__lang_version==$lang)? 'selected="select"':null).">{$lang}</option>";
					}
				?></select><span class='fpGuideCall'>?<em><?php _e("You can use this to save upto 2 different languages for customized text. Once saved use the shortcode to show calendar text in that customized language. eg. [add_foodpress lang='L2']",'foodpress');?></em></span></h4>
				<p><i><?php _e('Please use the below fields to type in custom language text that will be used to replace the default language text on the front-end of the calendar.','foodpress')?></i></p>
				
				
				<div class='foodpress_custom_lang_lines'>
				<?php
					
					require_once('settings_language_tab.php');
					
					// hook into addons
					if(has_filter('foodpress_settings_lang_tab_content')){
						$foodpress_custom_language_array = apply_filters('foodpress_settings_lang_tab_content', $foodpress_custom_language_array);			
					}
					
					//print_r($lang_options);

					// FOR EACH language options
					foreach($foodpress_custom_language_array as $cl){

						if(!empty($cl['type']) && $cl['type']=='togheader'){
							echo "<div class='fpLANG_section_header fp_settings_toghead'>{$cl['name']}</div>
								<div class='fp_settings_togbox'>";
						}else if(!empty($cl['type']) && $cl['type']=='togend'){
							echo '</div>';
						}else if(!empty($cl['type']) && $cl['type']=='subheader'){
							echo '<div class="fpLANG_subheader">'.$cl['label'].'</div><div class="fpLANG_subsec">';
						}else if(!empty($cl['type']) && $cl['type']=='parent' ){

							// language style with children
							if(!empty($cl['children'])){
								foreach($cl['children'] as $child){
									$val = (!empty($lang_options[$child['name']]))?  $lang_options[$child['name']]: '';
									echo "<div class='foodpress_custom_lang_line'>
											<div class='foodpress_cl_label_out'>
												<p class='foodpress_cl_label'>{$child['label']}</p>
											</div>";
									echo '<input class="foodpress_cl_input" type="text" name="'.$child['name'].'" placeholder="'.$placeholder.'" value="'.stripslashes($val).'"/>';
									echo "<div class='clear'></div>
										</div>";
								}
							}else{}
						}else{

							$val = (!empty($lang_options[$cl['name']]))?  $lang_options[$cl['name']]: '';

							$placeholder = (!empty($cl['placeholder']))?  $cl['placeholder']: '';

							echo "
								<div class='foodpress_custom_lang_line'>
									<div class='foodpress_cl_label_out'>
										<p class='foodpress_cl_label'>{$cl['label']}</p>
									</div>";
							echo '<input class="foodpress_cl_input" type="text" name="'.$cl['name'].'" placeholder="'.$placeholder.'" value="'.stripslashes($val).'"/>';
							echo "<div class='clear'></div>
								</div>";
							echo (!empty($cl['legend']))? "<p class='foodpress_cl_legend'>{$cl['legend']}</p>":null;	
						}			
					}
				?>		
				</div><!-- .foodpress_custom_lang_lines -->
			</div>
		</div>
		<input type="submit" class="btn_prime fp_admin_btn" value="<?php _e('Save Changes', 'foodpress') ?>" />
		</form>
		<?php
		
		break;

	// ADDON TAB
		case "food_5":
			
			// Licenses settings tab content
			require_once('settings_license_tab.php');
		break;

	// Reservations TAB
		case "food_6":	
			?>
			<form method="post" action=""><?php settings_fields('food_field_group'); 
				wp_nonce_field( plugin_basename( __FILE__ ), 'food_noncename' );
			?>
			<div id="food_1" class=" foodpress_admin_meta food_focus">		
				<div class="inside">
					<?php
						
						$opt6 = $food_opt[6];
						require_once('settings_reservations_tab.php');
						
						// hook into addons
						if(has_filter('foodpress_settings_tab1_arr_content')){
							$cutomization_pg_array = apply_filters('foodpress_settings_tab1_arr_content', $cutomization_pg_array);					
						}						
						
						$foodpress->load_ajde_backender();
						print_ajde_customization_form($cutomization_pg_array, $food_opt[6]);
					?>					
				</div>
				<p class='fp_settings_bottom'><input type="submit" class="btn_prime fp_admin_btn" value="<?php _e('Save Changes') ?>" /></p>
			</div>	
			</form>
		<?php  
		
		break;

	// support TAB
		case "food_4":
			
			// Addons settings tab content
			require_once('settings_support_tab.php');
		
		break;

	// Other pluggable
		default:
			do_action('foodpress_settings_tabs_'.$focus_tab);
		break;
		endswitch;
		
		echo "</div>";
	}
} // * function exists 

?>