/*
	Script that runs on all over the backend pages
	ver: 0.1
*/
jQuery(document).ready(function($){

	// ----------
	// Sitewide POPUP
	// ----------
	// hide

	$('#foodpress_popup').on('click','.foodpress_close_pop_btn', function(){
		var obj = $(this);
		hide_popupwindowbox( obj);

	});

	$('.foodpress_popup_text').on('click',' .fp_close_pop_trig',function(){
		var obj = $(this).parent();
		hide_popupwindowbox( obj);
	});

	$(document).mouseup(function (e){
		var container=$('#foodpress_popup');

		if(container.hasClass('active')){
			if (!container.is(e.target) // if the target of the click isn't the container...
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
			{
				container.animate({'margin-top':'70px','opacity':0}).fadeOut().removeClass('active');
				$('#fp_popup_bg').fadeOut();
			}
		}
	});

	// function to hide popup that can be assign to click actions
		function hide_popupwindowbox(){

			var container=$('#foodpress_popup');
			var clearcontent = container.attr('clear');

			if(container.hasClass('active')){
				container.animate({'margin-top':'70px','opacity':0},300).fadeOut().
					removeClass('active')
					.delay(300)
					.queue(function(n){
						if(clearcontent=='true'){
							$(this).find('.foodpress_popup_text').html('');
						}
						n();
					});
				$('#fp_popup_bg').fadeOut();
			}
		}

	/*
		DISPLAY foodpress in-window popup box
		Usage: <a class='button fp_popup_trig' content_id='is_for_content' dynamic_c='yes'>Click</a>
	*/
		$('.fp_popup_trig').click(function(){

			// dynamic content within the site
			var dynamic_c = $(this).attr('dynamic_c');
			if(typeof dynamic_c !== 'undefined' && dynamic_c !== false){

				var content_id = $(this).attr('content_id');
				var content = $('#'+content_id).html();

				$('#foodpress_popup').find('.foodpress_popup_text').html( content);
			}

			// if content coming from a AJAX file
			var attr_ajax_url = $(this).attr('ajax_url');

			if(typeof attr_ajax_url !== 'undefined' && attr_ajax_url !== false){

				$.ajax({
					beforeSend: function(){
						show_pop_loading();
					},
					url:attr_ajax_url,
					success:function(data){
						$('#foodpress_popup').find('.foodpress_popup_text').html( data);
					},complete:function(){
						hide_pop_loading();
					}
				});
			}

			// change title if present
			var poptitle = $(this).attr('poptitle');
			if(typeof poptitle !== 'undefined' && poptitle !== false){
				$('#fpPOP_title').html(poptitle);
			}


			$('#foodpress_popup').find('.message').removeClass('bad good').hide();
			$('#foodpress_popup').addClass('active').show().animate({'margin-top':'0px','opacity':1}).fadeIn();
			$('html, body').animate({scrollTop:0}, 700);
			$('#fp_popup_bg').show();
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



// POPUP
// -	SHORTCODE generator
	fpPOSH_go_back();

	var shortcode_vars = [];
	var shortcode_keys = new Array();
	var ss_shortcode_vars = new Array();

	var shortcode;

	$('#fpPOSH_outter').on('click','.fpPOSH_btn',function(){
		var obj = $(this);
		var section = $(this).data('step2');
		var code = $(this).data('code');

		// no 2nd step
		if($(this).hasClass('nostep') ){
			$('#fpPOSH_code').html('['+code+']').attr({'data-curcode':code});
		}else{
			var pare = obj.parent().parent();
			pare.find('.step2').show();
			pare.find('#'+section).show();
			$('.fpPOSH_inner').animate({'margin-left':'-470px'});

			fpPOSH_show_back_btn();

			$('#fpPOSH_code').html('['+code+']').attr({'data-curcode':code});
		}

		var title = $(this).html();
		$('h3.notifications span').html(title);
	});


	// each option for menu shortcode options
	$('#foodpress_popup').on('click','.fpPop_option',function(){

		// make sure correct one is highlighted
		$(this).siblings('.fpPop_option').removeClass('selected');
		$(this).addClass('selected');

		var value = $(this).data('value');
		var codevar = $(this).data('codevar');

		if(value!='' && value!='undefined'){
			fpPOSH_update_codevars(codevar,value);
		}else if(!value){
			fpPOSH_remove_codevars(codevar);
		}
	});



	// show back button
	function fpPOSH_show_back_btn(){
		$('#fpPOSH_back').animate({'left':'0px'});
		$('h3.notifications').addClass('back');
	}
	// go back button on the shortcode popup
	function fpPOSH_go_back(){
		$('#fpPOSH_back').click(function(){
			$(this).animate({'left':'-20px'},'fast');

			$('h3.notifications').removeClass('back');

			$('.fpPOSH_inner').animate({'margin-left':'0px'}).find('.step2_in').fadeOut();


			// hide step 2
			$(this).closest('#fpPOSH_outter').find('.step2').fadeOut();

			// clear varianles
			shortcode_vars=[];
			shortcode_vars.length=0;

			var code_to_show = $('#fpPOSH_code').data('defsc');
			$('#fpPOSH_code')
				.html('['+code_to_show+']')
				.attr({'data-curcode':code_to_show});

			// change subtitle
			$('h3.notifications span').html( $('h3.notifications span').data('bf') );
		});
	}


	// width change buttons
	$('#foodpress_popup').on('click','.fpopt_box_size p', function(){
		var obj = $(this);
		var codevar = obj.parent().data('codevar');
		var value = obj.data('size');

		obj.parent().find('p').removeClass('selected');
		obj.addClass('selected');

		fpPOSH_update_codevars(codevar,value);
	});

	//yes no buttons
	$('#foodpress_popup').on('click','.fp_yn_btn', function(){
		var obj = $(this);
		var codevar = obj.data('codevar');
		var value;

		if(obj.hasClass('NO')){
			obj.removeClass('NO');
			value = 'yes';
		}else{
			obj.addClass('NO');	value = 'no';
		}

		fpPOSH_update_codevars(codevar,value);
		report_select_steps_( obj, codevar );

	});
	// input and select fields
	$('.fpPOSH_inner').on('change','.fpPOSH_input, .fpPOSH_select', function(){
		var obj = $(this);
		var value = obj.val();
		var codevar = obj.data('codevar');

		if(value!='' && value!='undefined'){
			fpPOSH_update_codevars(codevar,value);
			report_select_steps_( obj, codevar );
		}else if(!value){
			fpPOSH_remove_codevars(codevar);
		}
	});


	// afterstatements within shortcode gen
	$('#foodpress_popup').on('click', '.trig_afterst .fp_yn_btn',function(){
		$(this).closest('.trig_afterst').next('.fp_afterst').toggle();
	});


	// SELECT STEP within 2ns step field
	$('.fpPOSH_inner').on('change','.fpPOSH_select_step', function(){
		var value = $(this).val();
		var codevar = $(this).data('codevar');
		var this_id = '#'+value;

		$(this_id).siblings('.fp_open_ss').hide();
		$(this_id).delay(300).show();

		// first time selecting
		if(!$(this).hasClass('touched') ){
			$(this).attr({'data-cur_sc': $('#fpPOSH_code').html() })
				.addClass('touched');
		}else{
			var send_code = $(this).data('cur_sc'); // send the code before selecting select step
			remove_select_step_vals();
			$(this).removeClass('touched');
		}


		// update the current shortcode based on selection
		if(value!='' && value!='undefined'){
			fpPOSH_update_codevars(codevar,value);
		}else if(!value){
			fpPOSH_remove_codevars(codevar);
		}

		if(value=='ss_1'){
			fpPOSH_remove_codevars(codevar);
		}


	});

	// toggling sections
		$('.section_selection').on('click','p', function(){
			var val = $(this).data('value');
			console.log(val);
			var par = $(this).closest('.fp_open_ss');
			par.find('.fp_section').hide();
			par.find('.fp_section.'+val).show();
		});

	// RECORD step codevar for each select steps
	function report_select_steps_(obj, codevar){
		// ONLY SELECT STEP
		if( obj.closest('.fp_fieldline').hasClass('ss_in')){
			if(ss_shortcode_vars.indexOf(codevar)==-1){
				ss_shortcode_vars.push(codevar);
			}
		}

	}

	function remove_select_step_vals(){

		if(ss_shortcode_vars.length>0){
			for (var i=0;i<ss_shortcode_vars.length;i++){
				var this_code = ss_shortcode_vars[i];
				fpPOSH_remove_codevars(this_code);
				//delete ss_shortcode_vars[i];
			}
		}
		ss_shortcode_vars=[];

	}

	const copyToClipboard = str => {
		const el = document.createElement('textarea');
		el.value = str;
		el.setAttribute('readonly', '');
		el.style.position = 'absolute';
		el.style.left = '-9999px';
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
	};

	// INSERT shortcode to WYSIWYG editor
	$('.fpPOSH_footer').on('click','.fpPOSH_insert',function(){
		//console.log(shortcode_keys);
		//fpPOSH_update_shortcode();

		var shortcode = $('#fpPOSH_code').html();
		copyToClipboard(shortcode);
		tinymce.activeEditor.execCommand('mceInsertContent', false, shortcode);

		hide_popupwindowbox();


	});

	// update shortcode based on new selections
	function fpPOSH_update_shortcode(){

		var el = $('#fpPOSH_code');
		var string = el.data('curcode')+' ';

		if(shortcode_vars.length==0){
			string=string;
		}else{
			$.each( shortcode_vars, function( key, value ) {
				string += value.code+'="'+value.val+'" ';
			});
		}

		// update the shortcode attr on insert button
		var stringx = '['+string+']';
		el.html(stringx).attr({'data-curcode': string});

	}

	// UPDATE or ADD new shortcode variable to obj
	function fpPOSH_update_codevars(codevar,value){

		if(shortcode_keys.indexOf(codevar)>-1
			&& shortcode_vars.length>0){
			$.each( shortcode_vars, function( key, arr ) {
				if(arr && arr.code==codevar){
					shortcode_vars[key].val=value;
				}
			});
		}else{
			var obj = {'code': codevar,'val':value};
			//shortcode_vars[codevar] = obj;
			shortcode_vars.push(obj);
			shortcode_keys.push(codevar);
		}
		fpPOSH_update_shortcode();
	}

	// REMOVE a shortcode variable to object
	function fpPOSH_remove_codevars(codevar){

		// remove from main object
		$.each( shortcode_vars, function( key, arr ) {
			if(arr.code==codevar){
				shortcode_vars.splice(key, 1);
			}
		});

		//remove from keys
		var index = shortcode_keys.indexOf(codevar);
		if(index>-1){
			shortcode_keys.splice(index, 1);
		}
		fpPOSH_update_shortcode();
	}

});
