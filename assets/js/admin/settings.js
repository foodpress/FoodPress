/*
	only for foodpress settings page
	ver: 0.1
*/
jQuery(document).ready(function($){
	// language tab
	$('.foodpress_cl_input').focus(function(){
		$(this).parent().addClass('onfocus');
	});
	$('.foodpress_cl_input').blur(function(){
		$(this).parent().removeClass('onfocus');
	});

	
	// change language
	$('#fp_lang_selection').change(function(){
		var val = $(this).val();
		var url = $(this).attr('url');
		window.location.replace(url+'?page=foodpress&tab=food_2&lang='+val);
	});

	// toggeling
	$('.fp_settings_toghead').on('click',function(){
		$(this).next('.fp_settings_togbox').toggle();
		$(this).toggleClass('open');
	});

});