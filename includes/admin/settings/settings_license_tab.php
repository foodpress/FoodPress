<?php
/**
 * FoodPress Addons and Licenses Page
 * @version 1.4
 */

global $foodpress;
?>
<div id="food_4" class="foodpress_admin_meta">
	<?php	/*	LICENSES Section	*/		?>
	<div class='licenses_list' id='foodpress_licenses'>
		<?php
			$admin_url = admin_url();
			$show_license_msg = true;

			$fp_licenses = $foodpress->admin->product->get_foodpress_license_data();

			// render existing licenses
			if(!empty($fp_licenses) && count($fp_licenses) > 0) {
				foreach($fp_licenses as $slug => $fpl) {

					if($slug != 'foodpress') continue;

					// new version text
					//echo '<pre>';
					//var_dump($foodpress->fp_updater->getInfoFromGitHub(true));
					//echo '</pre>';
					$latest_release_info = $foodpress->fp_updater->getInfoFromGitHub(true)->tag_name;
					//var_dump($latest_release_info);
					$new_update_text = (!empty($fpl['has_new_update']) && $fpl['has_new_update'])?
						"<span class='version remote' title='There is a newer version of foodpress available now!'>".$latest_release_info."<em>Latest version</em></span>":null;


					// if activated already
					if($foodpress->admin->product->is_activated('foodpress')) {
						echo "<div class='fp_product fp_product_main active_product'>";

						echo "<h2 class='heading'>FoodPress <span>License</span> <em>activated</em></h2>";

						$new_update_details_btn = (!empty($fpl['has_new_update']) && $fpl['has_new_update'])?
							"<a class='fp_admin_btn btn_prime' href='".$admin_url."update-core.php'>Update Now</a>  <a class='fp_admin_btn btn_prime thickbox' href='".$admin_url."plugin-install.php?tab=plugin-information&plugin=foodpress&section=changelog&TB_iframe=true&width=600&height=400'>Version Details</a> ":null;

						echo "
						<p class='versions'>
							<span class='version'>{$foodpress->version}<em>Your Version</em></span>".$new_update_text."
						</p>
						<p style='font-style:italic; display:block'>INFO: You have successfully activated this license on this website. You will need a seperate license to activate FoodPress for another site.</p>
						<p class='clear padb10'></p>

						<p>".$new_update_details_btn." <a class='fp_deactivate_license fp_admin_btn btn_noBG' data-slug='foodpress'>Deactivate License</a></p>";

						$show_license_msg = false;

					// NOT Activated yet
					}else{
						echo "<div class='fp_product fp_product_main'>";

						echo "<h2 class='heading'>FoodPress <span>License</span> <em>not activated</em></h2>";

						echo "
						<p class='versions'>
							<span class='version'>{$foodpress->version}<em>Your version</em></span>".$new_update_text."
						</p>
						<p style='font-style:italic; display:block'>Activate your copy of foodpress to get free automatic plugin updates direct to your site!</p>
						<p class='clear padb10'></p>

						<p><a class='fp_popup_trig fp_admin_btn btn_prime' dynamic_c='1' content_id='foodpress_pop_content_001' poptitle='Activate foodpress License'>Activate License</a></p>

						<div id='foodpress_pop_content_001' class='fp_hide_this'>
							<p>Email Address <span class='fpGuideCall'>?<em>This needs to be the email of the account you purchased FoodPress with.</a></em></span>
							<input class='foodpress_license_email_val' type='email' style='width:100%'/></p>
							<p>License Key <span class='fpGuideCall'>?<em>Read: <a href='http://www.myfoodpress.com/documentation/how-to-find-foodpress-license-key/' target='_blank'>How to find foodpress license key</a></em></span><br/>
							<input class='foodpress_license_key_val' type='text' style='width:100%'/>
							<input class='foodpress_slug' type='hidden' value='{$slug}' /></p>
							<input class='foodpress_license_div' type='hidden' value='license_{$fpl['name']}' /></p>
							<input class='foodpress_license_product_id' type='hidden' value='".$foodpress->admin->product->getProductLicenseInfoById('foodpress')['id']."' />
							<p><a class='foodpress_submit_license fp_admin_btn btn_prime'>Activate Now</a></p>
						</div>";

					}
				}
			}
		?>

		<div class='clear'></div>
		</div>
	<?php

		$fp_installed_addons ='';
		$count =1;
		$foodpress_addons_opt = get_option('foodpress_addons');
		$activePlugins = get_option( 'active_plugins' );

		$addons_list = foodpress_addon_list();
		$installed_plugins =  get_option( 'active_plugins' );

		// EACH ADDON
		foreach($addons_list as $slug => $addons) {
			// Icon Image for the addon
			$img = ($addons['iconty'] == 'local')? FP_URL.'/'.$addons['icon']: $addons['icon'];

			$_has_addon = false;

			// check if the product is activated within wordpress
				if(!empty($activePlugins)){
					foreach($activePlugins as $plugin) {
						// check if foodpress is in activated plugins list
						if(strpos( $plugin, $slug.'.php') !== false) {
							$_has_addon = true;
						}
					}
				}

				$guide = null;
				$_this_version = null;

				if($_has_addon) {
					$slugger = $slug . '/' . $slug . '.php';
					$_addon_options_array = (isset($foodpress_addons_opt[$slugger]) && !empty($foodpress_addons_opt[$slugger])) ? $foodpress_addons_opt[$slugger] : false;
					//var_dump($_addon_options_array);
					$guide = (!empty($_addon_options_array['guide_file'])) ? "<span class='fp_admin_btn btn_prime foodpress_guide_btn fp_popup_trig' ajax_url='{$_addon_options_array['guide_file']}' poptitle='How to use {$addons['name']}'>Guide</span>" : null;
					$_this_version = "<span class='fpa_ver' title='My Version'>".$_addon_options_array['version']."</span>";
					//var_dump($_addon_options_array);
					$varSlug = str_replace('-', '_', $slug) . '_updater';
					if(isset($foodpress->{$varSlug})) {
						if(isset($fp_licenses[$slug]['has_new_update']) && !empty($fp_licenses[$slug]['has_new_update']) && $fp_licenses[$slug]['has_new_update']) {
							//$latestVersion = $foodpress->{$varSlug}->getInfoFromGitHub(true)->tag_name;
							$_addon_options_array['latestVersion'] = $fp_licenses[$slug]['remote_version'];
						}
					}
				}

			?>
			<div class='fp_product fp_product_<?php echo $slug;?> fpaddon_box <?php echo ($_has_addon?'have_product':'');?>'>
				<div class='fpa_boxe'>
					<div class='fpa_content'>
						<h5 ><?php echo $addons['name'].' '.$_this_version;?><?= (isset($_addon_options_array['latestVersion'])) ? ' - Latest: '. $_addon_options_array['latestVersion'] : ''; ?></h5>
						<p><?php echo $addons['desc'];?></p>
					</div>
					<div class='clear'></div>
					<a class='fp_admin_btn btn_prime' target='_blank' href='<?php echo $addons['link'];?>'>Learn more</a>  <?php echo $guide;?>

					<?php if(!$_has_addon) { ?>
						<a class='fp_admin_btn btn_secondary' target='_blank' href='<?php echo $addons['download'];?>'>Download</a>
					<?php } elseif(!$foodpress->admin->product->is_activated($slug)) { ?>
						<p><a class='fp_popup_trig fp_admin_btn btn_prime' dynamic_c='2' content_id='foodpress_pop_content_<?= $slug ?>' poptitle='Activate <?= $foodpress->admin->product->getProductLicenseInfoById($slug)['name'] ?> License'>Activate License</a></p>

						<div id='foodpress_pop_content_<?= $slug ?>' class='fp_hide_this'>
							<p>Email Address <span class='fpGuideCall'>?<em>This needs to be the email of the account you purchased FoodPress with.</a></em></span>
							<input class='foodpress_license_email_val' type='email' style='width:100%'/></p>
							<p>License Key <span class='fpGuideCall'>?<em>Read: <a href='http://www.myfoodpress.com/documentation/how-to-find-foodpress-license-key/' target='_blank'>How to find foodpress license key</a></em></span><br/>
							<input class='foodpress_license_key_val' type='text' style='width:100%'/>
							<input class='foodpress_slug' type='hidden' value='<?= $slug ?>' /></p>
							<input class='foodpress_license_product_id' type='hidden' value='<?= $foodpress->admin->product->getProductLicenseInfoById($slug)['id'] ?>' />
							<p><a class='foodpress_submit_license fp_admin_btn btn_prime'>Activate Now</a></p>
						</div>
					<?php } ?>

					<?php if($foodpress->admin->product->is_activated($slug)) { ?>
						<br>
						<a class='fp_deactivate_license fp_admin_btn btn_noBG' data-slug='<?= $slug ?>'>Deactivate License</a>
					<?php } ?>
				</div>
			</div>
			<?php

			$count++;
		}


		?>
	</div>
	<?php
		// Throw the output popup box html into this page
		echo $foodpress->output_foodpress_pop_window(array('content'=>'Loading...', 'type'=>'padded'));
	?>
</div>
