<?php
/**
 * FoodPress Addons and Licenses Page
 * @version 1.3.2
 */
?>
<div id="food_4" class="foodpress_admin_meta">		
	<?php	/*	LICENSES Section	*/		?>
	<div class='licenses_list' id='foodpress_licenses'>
		<?php
			//echo $foodpress->fp_updater->getRemote_version();

			$admin_url = admin_url();
			$show_license_msg = true;

			// REMOVE license
			if(isset($_GET['lic']) && $_GET['lic']=='remove')
				delete_option('_fp_licenses');

			$fp_licenses = get_option('_fp_licenses');

			// running for the first time
			if(empty($fp_licenses)){
				
				$lice = array(
					'foodpress'=>array(
						'name'=>'foodpress',
						'current_version'=>$foodpress->version,
						'type'=>'plugin',
						'status'=>'inactive',
						'key'=>'',
					));
				update_option('_fp_licenses', $lice);
				
				$fp_licenses = get_option('_fp_licenses');				
			}
			
			// render existing licenses
			if(!empty($fp_licenses) && count($fp_licenses)>0){
				foreach($fp_licenses as $slug=>$fpl){
					
					// new version text
					$latest_release_info = $foodpress->fp_updater->getInfoFromGitHub(true);
					$new_update_text = (!empty($fpl['has_new_update']) && $fpl['has_new_update'])?
						"<span class='version remote' title='There is a newer version of foodpress available now!'>".$latest_release_info->tag_name."<em>Latest version</em></span>":null;
					
					// if activated already
					if($fpl['status']=='active'){
						
						echo "<h2 class='heading'>FoodPress <span>License</span> <em>activated</em></h2>";
						
						$new_update_details_btn = (!empty($fpl['has_new_update']) && $fpl['has_new_update'])?
							"<a class='fp_admin_btn btn_prime' href='".$admin_url."update-core.php'>Update Now</a>  <a class='fp_admin_btn btn_prime thickbox' href='".$admin_url."plugin-install.php?tab=plugin-information&plugin=foodpress&section=changelog&TB_iframe=true&width=600&height=400'>Version Details</a> ":null;
						
						echo "
						<p class='versions'>
							<span class='version'>{$foodpress->version}<em>Your Version</em></span>".$new_update_text."	
						</p>
						<p style='font-style:italic'>INTO: You have successfully activated this license on this website. You will need a seperate license to activate FoodPress for another site. With foodpress 1.3.2 you should be able to auto update foodpress from here on out :)</p>
						<p class='clear padb10'></p>
							
						<p>".$new_update_details_btn." <a href='". $admin_url."admin.php?page=foodpress&tab=food_5&lic=remove' class='fp_admin_btn btn_noBG'>Remove License</a></p>";
						
						$show_license_msg = false;
					
					// NOT Activated yet
					}else{
						
						echo "<h2 class='heading'>foodpress <span>License</span> <em>not activated</em></h2>";
													
						echo "
						<p class='versions'>
							<span class='version'>{$foodpress->version}<em>Your version</em></span>".$new_update_text."	
						</p>
						<p class='clear padb10'></p>
						
						<p><a class='fp_popup_trig fp_admin_btn btn_prime' dynamic_c='1' content_id='foodpress_pop_content_001' poptitle='Activate foodpress License'>Activate Now</a></p>						
						
						<div id='foodpress_pop_content_001' class='fp_hide_this'>
							<p>License Key <span class='fpGuideCall'>?<em>Read: <a href='http://www.myfoodpress.com/documentation/how-to-find-foodpress-license-key/' target='_blank'>How to find foodpress license key</a></em></span><br/>
							<input class='foodpress_license_key_val' type='text' style='width:100%'/>
							<input class='foodpress_slug' type='hidden' value='{$slug}' /></p>
							<input class='foodpress_license_div' type='hidden' value='license_{$fpl['name']}' /></p>
							<p><a class='foodpress_submit_license fp_admin_btn btn_prime'>Activate Now</a></p>
						</div>";
						
					}
				}
			}
		?>
		
	
		<div class='clear'></div>
		
		<?php if($show_license_msg):?>
		<p><?php _e('Activate your copy of foodpress to get free automatic plugin updates direct to your site!'); ?></p>
		<?php endif;?>
	</div>
	
	

	<div class="inside foodpress_addons">
		
	<?php		
		
		$fp_installed_addons ='';
		$count =1;
		$foodpress_addons_opt = get_option('foodpress_addons');
		
		global $wp_version; 
		
		echo "<h2 class='heading'><a href='http://www.myfoodpress.com/addons/' target='_blank'>FOODPRESS ADDONS</a></h2>";
		
		$url = 'http://update.myfoodpress.com/addons.php';
		$response = wp_remote_post(
            $url,
            array(
                'body' => array(
                    'action'     => 'fp_get_addons',
                    'api-key' => md5(get_bloginfo('url'))
                ),
                'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
            )
        );

        if ( !is_wp_error( $response )  && isset($response['body']))  {
        	
        	if(is_serialized($response['body']))
        		$request = unserialize($response['body']);

        	if(!empty($request)){
				
				// installed addons
				if(!empty($foodpress_addons_opt) and count($foodpress_addons_opt)>0 ){
					foreach($foodpress_addons_opt as $tt=>$yy){
						$fp_installed_addons[]=$tt;
					}
				}else{	$fp_installed_addons=false;	}
				
				
				echo "<div class='fp_addons_list'>";

				// get installed plugins array
				$installed_plugins =  get_option( 'active_plugins' );
				
				// EACH ADDON
				foreach($request as $slug=>$addons){
					// Icon Image for the addon
					$img = ($addons['iconty'] == 'local')? FP_URL.'/'.$addons['icon']: $addons['icon'];
					
					// Check if addon is installed in the website
					$_has_addon = ($installed_plugins && in_array($slug.'/'.$slug.'.php', $installed_plugins))?true:false;
					

					if($_has_addon){
						$_addon_options_array = !empty($foodpress_addons_opt[(string)$slug])? $foodpress_addons_opt[(string)$slug]:false;				
					}
					
					
					$guide = ($_has_addon && !empty($_addon_options_array['guide_file']) )? "<span class='fp_admin_btn btn_prime foodpress_guide_btn fp_popup_trig' ajax_url='{$_addon_options_array['guide_file']}' poptitle='How to use {$addons['name']}'>Guide</span>":null;
					
					$_this_version = ($_has_addon)? "<span class='fpa_ver' title='My Version'>".$_addon_options_array['version']."</span>": null;
					
					$_hasthis_btn = ($_has_addon)? "  <span class='fp_admin_btn btn_triad'>You have this</span>":null;
					
					?>
					<div class='fpaddon_box'>
						<div class='fpa_boxe'>
						<div class='fpaddon_box_in'>	
							<div class='fpa_content'>
								<h5 style='background-image:url(<?php echo $img;?>)'><?php echo $addons['name'].' '.$_this_version;?></h5>
								<p><?php echo $addons['desc'];?></p>						
							</div>
							<div class='clear'></div>
							<a class='fp_admin_btn btn_prime' target='_blank' href='<?php echo $addons['link'];?>'>Learn more</a>  <?php echo $guide;?><?php echo $_hasthis_btn;?>
							<?php if(!$_has_addon):?> <a class='fp_admin_btn btn_secondary' target='_blank' href='<?php echo $addons['download'];?>'>Download</a><?php endif;?>
						</div>
						</div>
					</div>
					<?php			
						echo ($count%2==0)?"<div class='clear'></div>":null;
					$count++;
				}
				
				echo "<div class='clear'></div></div>";

			}

        }else{
        	echo "<p>WordPress is unable to access remote server to get content. Connection time out. Please check your server for cURL accessibility.</p>";
        	$error_string = $response->get_error_message();
  			echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
        }


	?>
		
	</div>
	
	
	
	
	<?php
		// Throw the output popup box html into this page
		echo $foodpress->output_foodpress_pop_window(array('content'=>'Loading...', 'type'=>'padded'));
	?>
</div>