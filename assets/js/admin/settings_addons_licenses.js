/**
 * Addons and license page script
 * @version  0.1
 */

jQuery(document).ready(function($){
// Licenses verification and saving
	$('body').on('click','.foodpress_submit_license',function(){
		
		$('#foodpress_popup').find('.message').removeClass('bad good');
		
		var parent_pop_form = $(this).parent().parent();
		var license_key = parent_pop_form.find('.foodpress_license_key_val').val();
		
		if(license_key==''){
			show_pop_bad_msg('License key can not be blank! Please try again.');
		}else{
			
			var slug = parent_pop_form.find('.foodpress_slug').val();	
			var error = false;			
			
			// validate key format
				var data_arg = {
					action:'fp_validate_license',
					key:license_key,
				};
				$.ajax({
					beforeSend: function(){	show_pop_loading();	},
					type: 'POST',
					url:the_ajax_script.ajaxurl,
					data: data_arg,
					dataType:'json',
					success:function(data){	
						// if valid license format
						if(data.status=='good'){
							var data_arg_2 = {
								action:'foodpress_verify_lic',
								key:license_key,
								slug:'foodpress',
							};
							$.ajax({
								beforeSend: function(){},
								type: 'POST',
								url:the_ajax_script.ajaxurl,
								data: data_arg_2,
								dataType:'json',
								success:function(data2){
									// GET json license information
									// /console.log(data2.json_url);
									if(data2.json_url != 'data'){
										$.getJSON(data2.json_url, function(dataJ){
											// if validated remotely			
											$.each( dataJ, function( key, val ) {
											    if(val.created_at!=''){	
											    	// update remote validity			
													var data_arg_3 = {
														action:'fp_remote_validity',
														remote_validity:'valid',
														slug:'foodpress',
													};
													$.ajax({
														beforeSend: function(){},
														type: 'POST',
														url:the_ajax_script.ajaxurl,
														data: data_arg_3,
														dataType:'json',
														success:function(data3){}
													});
											    }
											});
										});
									}

									// update front-end with activated info
										box = $('#foodpress_licenses');
										box.find('h2.heading em').html('Activated');
										box.find('.fp_popup_trig').fadeOut();
										
										show_pop_good_msg('License key verified and saved.');
										$('#foodpress_popup').delay(3000).queue(function(n){
											$(this).animate({'margin-top':'70px','opacity':0}).fadeOut();
											$('#fp_popup_bg').fadeOut();
											n();
										});

								},complete:function(){	hide_pop_loading();	}
							});
						}else{	show_pop_bad_msg(data.error_msg);	}
					},complete:function(){ hide_pop_loading();	}
				});	






			var slug = parent_pop_form.find('.foodpress_slug').val();
			
			var data_arg = {
				action:'foodpress_verify_lic',
				key:license_key,
				slug:slug
			};					
			
			$.ajax({
				beforeSend: function(){
					show_pop_loading();
				},
				type: 'POST',
				url:the_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					if(data.status=='success'){
						
						
					}else{
						show_pop_bad_msg(data.error_msg);
					}					
					
				},complete:function(){
					hide_pop_loading();
				}
			});
		}
	});


	function show_pop_bad_msg(msg){
		$('#foodpress_popup').find('.message').removeClass('bad good').addClass('bad').html(msg).fadeIn();
	}
	function show_pop_good_msg(msg){
		$('#foodpress_popup').find('.message').removeClass('bad good').addClass('good').html(msg).fadeIn();
	}
	
	function show_pop_loading(){
		$('.foodpress_popup_text').css({'opacity':0.3});
		$('#fp_loading').fadeIn();
	}
	function hide_pop_loading(){
		$('.foodpress_popup_text').css({'opacity':1});
		$('#fp_loading').fadeOut(20);
	}
	
});