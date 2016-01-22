<?php
	/*
		Styles tab for foodpress settings
	*/
?>

<div id="food_2" class="postbox foodpress_admin_meta">	
	
	<div class="inside">
		<h2><?php _e('Add your own custom styles','foodpress');?></h2>
		<p><i><?php _e('Please use text area below to write your own CSS styles to override or fix style/layout changes in your menu pages. <br/>These styles will be appended into the dynamic styles sheet loaded on the front-end.','foodpress')?></i></p>
		<table width='100%'>
			<tr><td colspan='2'>
				<textarea style='width:100%; height:350px' name='food_styles'><?php echo get_option('food_styles');?></textarea>				
			</tr>		
		</table>

		<h2 style='padding-top:30px'><?php _e('Auto generated Dynamic Styles','foodpress');?></h2>
		<p><i><?php _e('If your dynamic styles (appearance changes in foodpress settings) do not reflect on front-end, it could be that your website is blocking foodpress from using wp_filesystems() to write these dynamic styles to "dynamic_styles.css". <br/>In this case please <b>copy</b> the below CSS styles and paste it on your theme styles (style.css).','foodpress')?></i></p>
		<table width='100%'>
			<tr><td colspan='2'>
				<textarea readonly style='width:100%; height:350px; opacity:0.5' name='fp_styles_dynamic'><?php
					ob_start();
					include(FP_PATH.'/assets/css/dynamic_styles.php');

					$content = ob_get_clean();
					echo $content;
				?></textarea>				
			</tr>		
		</table>
		<p><i>NOTE: These styles will update everytime you make changes in foodpress appearance settings</i></p>			
	</div>
</div>
<input type="submit" class="btn_prime fp_admin_btn" value="<?php _e('Save Changes') ?>" />
</form>