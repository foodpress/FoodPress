/**
 * FoodPress front-end javascript
 *
 * @version  1.3.2
 * @author AJDE & Michael Gamble
 */
jQuery(document).ready(function($){

	init();

	// initiate everything
	function init(){
		alternate_categorized_menu();
	}

	// scroll sections
		$(window).scroll(function(){
			if( $('.foodpress_scroll_section_body').length==0) return;
			$('.foodpress_scroll_section_body').each(function(){
				sib = $(this).siblings('.foodpress_scroll_sections');
				topmenu = $(this).offset().top;
				bottommenu = topmenu+ $(this).height();
				sibheight = sib.height();
				if($(window).scrollTop() > topmenu ) {
					if($(window).scrollTop() > (bottommenu-sibheight) ){
						sib.attr('class', 'foodpress_scroll_sections bottom');
					}else{
						sib.attr('class', 'foodpress_scroll_sections top');
					}
		        }else{
		        	sib.removeClass('top');
		        }
			});
		});
		// clicking on each section on scroll menu
			$('.foodpress_scroll_sections').on('click','h4', function(){
				index = $(this).data('i');
				menusection = $(this).parent().siblings('.foodpress_scroll_section_body').find('.foodpress_section_content[data-i='+index+']');
				sectiontop = parseInt(menusection.offset().top);

				$('html, body').animate({
				    scrollTop: sectiontop-30
				}, 2000);

			});

	// Tabbed menu
		// switching tabbed menu tabs
			$('.foodpress_tabs h4').on('click',function(){
				// tab focus
				$(this).parent().find('h4').removeClass('focused');
				$(this).addClass('focused');

				var index = $(this).data('i');
				var slug = $(this).data('termslug');
				var fp_menu = $(this).closest('.foodpress_menu');

				fp_menu.find('.foodpress_tab_content').hide();
				fp_menu.find('.foodpress_tab_content.fp_'+slug).show();
			});

	// Categorize box menu
		function alternate_categorized_menu(){
			$('.foodpress_menu.box_cats').each(function(){
				var width = $(this).width();
				$(this).find('.foodpress_categories').width(width);
				$(this).find('.fp_content').width(width);
				$(this).find('.fp_categories_holder').width(width*2);
				//$(this).find('h2.fp_menu_sub_section').hide();
			});
		}
		// click on each category
			$('.box_cats').on('click', '.foodpress_categories h4' , function(){
				var obj = $(this);
				var section = obj.attr('data-i');
				var width = obj.parent().width();

				var fp_content = obj.parent().siblings('.fp_content');

				fp_content.find('.foodpress_tab_content').hide();
				fp_content.show().find('.fp_'+section).show();
				fp_content.show().find('.fp_'+section+' .fp_container').show();
				obj.parent().parent().animate({'margin-left':'-'+width}, 200);

				// set go back title
				fp_content.find('.fp_category_subtitle').html( obj.attr('data-name') );
			});

		// going back to categories
			$('.box_cats').on('click', '.fp_backto_cats' , function(){
				$(this).closest('.fp_categories_holder').animate({'margin-left':0}, function(){
					$(this).parent().find('.fp_container').hide();
				});

			});

		// responsive boxes
			$( window ).resize(function() {
				$('.foodpress_menu.box_cats').each(function(){
					var width = $(this).parent().width();

					var cath =$(this).find('.fp_categories_holder');
					cath.width(width*2);
					cath.find('.foodpress_categories').width(width);
					cath.find('.fp_content').width(width);
				});
			});

	// Featured menu item hover effect
		$('.fp_featured_content').hover(function(){
			$(this).find('.fp_featured_description').stop().slideDown('fast');
		},function(){
			$(this).find('.fp_featured_description').stop().slideUp('fast');
		});

	// collapsable menu
		$('.foodpress_menu').on('click', 'h2.collapsable', function(){
			$(this).toggleClass('collapsed').next('.fp_container').slideToggle('fast');
		});
		$('.foodpress_menu.clps_dt').on('click', 'h3.collapsable', function(){
			$(this).toggleClass('collapsed').next('.food_items_container').slideToggle('fast');
		});

	// close menu popup
		$('body').on('click', '.fplbclose', function(){
			closePopup();			
		});

	// close with click outside popup box when pop is shown
		$(document).mouseup(function (e){
			var container=$('body').find('.fp_pop_body');

			if (!container.is(e.target) // if the target of the click isn't the container...
			&& e.pageX < ($(window).width() - 30)
			&& container.has(e.target).length === 0) // ... nor a descendant of the container
			{
				closePopup();
			}
		});

	// open popup
		$('.foodpress_menu').on('click', '.fp_popTrig', function(e){
			menuarg = $(this).closest('.foodpress_menu').find('.menu_arguments');
			var xx = e.target;

			if(menuarg.attr('data-ux') !== 'none'){// if ux none is not set
				var menuitem_id = $(this).closest('.menuItem').data('menuitem_id');
				var menu_id = $(this).closest('.foodpress_menu').attr('id');
				//console.log(menuitem_id);
				ajax_menuitem_content(menuitem_id, menu_id);
			}
		});

	// load content for the popup item box
		function ajax_menuitem_content(item_id, menu_id){
			var data_arg = {
				action:'fp_ajax_content',
				menuitem_id:item_id,
				args:get_arguments(menu_id)
			};

			$.ajax({
				beforeSend: function(){
					get_popup_html();
					$('body').find('.fp_lightbox').addClass('view');
					$('html').addClass('fp_overflow');
				},
				type: 'POST',
				url:fp_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					$('body').find('.fp_pop_inner').html(data.content);
					$('body').find('.fp_lightbox').removeClass('loading');
					setTimeout(function(){
						$('body').find('.fp_lightbox').addClass('show');
					}, 100);

				},complete:function(){

				}
			});
		}

		// prepare the HTML for lightbox
			function get_popup_html(type){
				if(!$('body').hasClass('fp_overflow')){
					type = (type!='')? 'menu':type;
					var popupcode = "<div class='fp_lightbox fp_popup loading "+type+"' >";
							popupcode += "<div class='fp_content_in'>";
								popupcode += "<div class='fp_content_inin'>";
									popupcode += "<div class='fp_lightbox_content'>";
										popupcode += "<a class='fplbclose'>X</a>";
										popupcode += "<div class='fp_lightbox_body fp_pop_body fp_pop_inner'></div>";
									popupcode += "</div>";
								popupcode += "</div>";
							popupcode += "</div>";
						popupcode += "</div>";
					$('body').addClass('fp_overflow').append(popupcode);

					// var thisis = "<div id='fp_popup' class='fp_popup_section fp_popup_html' style='display:none'><div class='fp_popup'><a id='fp_close'><i class='fa fa-times'></i></a><div class='fp_pop_inner'></div></div></div><div id='fp_popup_bg' class='fp_popup_html' style='display:none'><div class='spinner'><div class='bounce1'></div><div class='bounce2'></div><div class='bounce3'></div></div></div>";
					// $('body').addClass('fppop').append(thisis);

					// $('body').find('#fp_popup_bg').fadeIn(300);
				}
			}

	// Close the lightbox
		function closePopup(){
			// if the lightbox is open
			if($('body').hasClass('fp_overflow')){
				$('body').find('.fp_lightbox').removeClass('show');
				setTimeout(function(){ $('body').find('.fp_lightbox').remove(); }, 170);
				$('html').removeClass('fp_overflow');
				$('body').removeClass('fp_overflow');
			}
		}

	// RESERVATION FORM
		// phone number field
			/*
			$('body').find('.fp_resform_phone').each(function(){
				TID = $(this).attr('id');
				$('#'+TID).intlTelInput({
					allowExtensions:false,
				});
			});
			*/

		// trigger the reservation FORM
			$('.fp_res_button').on('click',function(){
				var link = $(this).attr('data-link');
				if(link!==''){
					if($(this).data('new')=='1'){
						window.open(link);
					}else{
						window.location.href=link;
					}
					return;
				}

				$('body').find('.fp_make_res.popup').fadeIn().addClass('open');
				$('.fpres_bg').fadeIn();
				$('html, body').animate({scrollTop:0});
				$('.fp_res_success').hide();
			});

		// date and time fields in the reservation form

		// reservation time
			$('body').find('.fpres_time_range').each(function(){
				var OBJ = $(this);
				var step = OBJ.closest('.step');
				if(step.attr('data-restrict')=='yes'){
					START = step.data('start');
					OBJ.find('option[value="'+START+'"]').prevAll().wrap( '<span style="display: none;" />' );
					END = step.data('end');
					OBJ.find('option[value="'+END+'"]').nextAll().wrap( '<span style="display: none;" />' );

					OBJ.val(START);
				}

				if( OBJ.attr('id')=='fp_res_time_start'){
					OBJ.on('change',function(){
						//console.log('t');
						VAL = OBJ.val();
						OBJ.next().find('option').show();
						OBJ.next().find('option[value="'+VAL+'"]').prevAll().wrap( '<span style="display: none;" />' );
						OBJ.next().val(VAL);
					});
				}
			});

			// for each reservation lightbox
				$('body').find('.fp_make_res').each(function(){
					lightbox = $(this);
					topsection = lightbox.find('.fpres_form_datetime');

					// date picker
					var dateformat__ = topsection.attr('data-dateformat');
					date_format = (typeof dateformat__ !== 'undefined' && dateformat__ !== false)?
						dateformat__: 'dd/mm/yy';

					// start date range
					var blk24 = lightbox.attr('data-blk24')
					minDate = (blk24=='1')? '+1':'0';

					// reserverable dates
					unres_ar = [];
					lightbox.find('#fp_unres i').each(function(){
						unres_ar.push( $(this).html() );
					});

					lightbox.find( "#fp_res_date" ).datepicker({
						dateFormat: date_format,
						minDate:minDate,
						beforeShowDay: function(date){
					        var string = $.datepicker.formatDate('yy-mm-dd', date);
					        return [ unres_ar.indexOf(string) == -1 ]
					    }
					});
				});



		// hide form clicking outside of it and resetting to beginning
			$('.fpres_bg').on('click',function(){
				var container=$('body').find('#fp_make_res');
				if(container.hasClass('open')){
					//$('#fp_res_modal').animate({'margin-top':'200px'},500);
					$('#fp_make_res').fadeOut(200).removeClass('open');
					$('.fpres_bg').fadeOut(500, function(){
						$('#fp_make_res').find('.reservation_section').show();
					});
					$('body').find('.fpres_bg').removeClass('success');
				}
			});

		// close button hide reservation form
			$('body').on('click','#fp_close', function(){
				$('#fp_make_res').fadeOut(200).removeClass('open');
				$('.fpres_bg').fadeOut(500,function(){
					$('#fp_make_res').find('.reservation_section').show();
				});
				$('body').find('.fpres_bg').removeClass('success');
			});

		// submission of reservation form
			$('body').on('click','.fp_reservation_submit', function(e){

				e.preventDefault();
				var error = 0;
			    var form = $(this).closest('.fp_make_res');

			    // form type
			    var onpage = form.hasClass('onpage')?true: false;

			    // if the form is processing right now
			    if(form.hasClass('loading'))
			    	return;

			   	var form_msg = JSON.parse(form.find('#fpres_form_msg').html());
			   	var form_msg_el = form.find('.form_message .error');

			   	//reset error-ed fields
			   		form.find('.error.resinput').removeClass('error');
			   		form_msg_el.hide();

			    // VALIDATIONS
			       	// verify email
					if(error==0){
						if(!is_email(form.find('input[name=email]').val())){
							form.find('input[name=email]').addClass('error');
							error=2;
						}
					}

			        // check for blank required fields
				    var ajaxdataa = { };
				    form.find('.req').each(function(){
				    	var thisO = $(this);
						var val = thisO.val();
				    	if(!val ){
				    		error=1;
				    		thisO.addClass('error');
				    	}
				    });

				    // party size
				    	party= $('#fp_res_people');
				    	if(!$.isNumeric(party.val())){
				    		error=1;
				    		party.addClass('error');
				    	}
				    // validate phone number
				    /*
				    	phone = form.find('.fp_resform_phone');
				    	if(phone.length>0){
				    		pattern = phone.attr('pattern');
				    		if(pattern !='no'){
				    			pattern = new RegExp(pattern);
				    			phonenumber = phone.val();

				    			result = pattern.test(phonenumber);
				    			console.log(pattern+' '+result);
				    			if(result ){
				    				error=1;
				    				phone.addClass('error');
				    			}
				    		}
				    	}*/

				// all field values
					form.find('.resinput').each(function(){
				    	var thisO = $(this);
						var val = thisO.val();

				    	// build ajax data string
				    	if(thisO.hasClass('check')){
				    		ajaxdataa[ thisO.attr('name')] = (thisO.is(':checked'))?'yes':'no';
				    	}else{
				    		ajaxdataa[ thisO.attr('name')] = encodeURIComponent(thisO.val());
				    	}
				    });

				// check validation of capcha
					if(form.attr('data-vald')=='1'){
						OBJvalidation = form.find('.validation');
						val_num = parseInt(OBJvalidation.data('val'))-1;

						var c_codes = ['F8RJS9','Q9D7D','DJ93KC','LR32C','JD93C1','D93J8A','J38DY2'];

						if(OBJvalidation.find('input').val() != c_codes[val_num ]){
							error=5;
							OBJvalidation.find('input').addClass('error');
						}
					}

			   	if(error==0){
			   		ajaxdataa['action']= 'fp_ajax_popup';

			   		// get all shortcode arguments for the form
			   		var shortcode_array ={};
			   		$.each(form.get(0).attributes, function (i, attrib){
			   			if(attrib.name!='class' && attrib.name!='id' && attrib.value !=''){
			   				name__ = attrib.name.split('-');
			   				shortcode_array[name__[1]]= attrib.value;
			   			}
			   		});
			   		ajaxdataa['args']= shortcode_array;

				    $.ajax({
				    	beforeSend: function(){
							form.addClass('loading');
						},
				        data: ajaxdataa,
				        type: 'post',dataType:'json',
				        url: fp_ajax_script.ajaxurl,
				        success: function(data) {
				        	form.removeClass('loading');
				        	if(data.status=='0'){
				        		// update success message with
				        		form.find('.fp_res_success_title .name').html( form.find('.form_section_2 input[name=name]').val());
				        		form.find('.fp_res_success').find('.reservation_info span').html( '<em>'+form_msg.res2+'</em>: '+decodeURIComponent(ajaxdataa.date)+ ' <em>'+form_msg.res3+'</em>: ' +decodeURIComponent(ajaxdataa.time)+ ' <em>'+form_msg.res4+'</em>: '+decodeURIComponent(ajaxdataa.party));

				        		form.find('.reservation_section').slideUp(function(){
				        			$('.fp_res_success').slideDown(400);
				        			$('html, body').animate({scrollTop:0});
				        			if(onpage)
				        				form.addClass('success');
				        		});

				        		$('body').find('.fpres_bg').addClass('success');


				        		// if redirect to another page after submission
				        			redir = $('#fp_make_res').data('redirect');
				        			if(redir != 'no'){
				        				window.location.replace($('#fp_make_res').data('redirect'));
				        			}

				        	}else{
				        		if(data.status=='01'){
				        			// could not create reseration
				        			form_msg_el.addClass('err').html( form_msg.err4).show();
				        		}
				        	}
				        }
				    });
				}else{
					switch(error){
						case 1: 	form_msg_el.addClass('err').html( form_msg.err).fadeIn();	break;
						case 2: 	form_msg_el.addClass('err').html( form_msg.err2).fadeIn();	break;
						case 3: 	form_msg_el.addClass('err').html( form_msg.err3).fadeIn();	break;
						case 5: 	form_msg_el.addClass('err').html( form_msg.err5).fadeIn();	break;
					}
				}

			});

	// form validation
		function is_email(email){
			var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	  		return regex.test(email);
		}

	// get menu shortcode arguments
		function get_arguments(menu_id){
			var shortcode_array ={};

			$('#'+menu_id).find('.menu_arguments').each(function(){
				$.each(this.attributes, function(i, attrib){
					var name = attrib.name;
					if(attrib.name!='class' && attrib.name!='style' && attrib.value !=''){
						name__ = attrib.name.split('-');
						shortcode_array[name__[1]] = attrib.value;
					}
				});
			});
			return shortcode_array;
		}

	// Added script for Boxed Menu to scroll to top of the Menu
		$(".foodpress_menu.box_cats h4").on("click", function(event) {
	        event.preventDefault();

	        //var $parent = $(this).parent();
	        var $parent = $(this).closest('div.foodpress_menu');

	        var topToScrollTo = $parent.offset().top -100;

	        $("html, body").stop().animate({ scrollTop: topToScrollTo}, 1000);
	    });

	// Initiate reservation phone number
		var telInput = $("#fp_phone_");
		var errorMsg = $("#phone-error-msg");
		var validMsg = $("#phone-valid-msg");

		telInput.intlTelInput({
	        // allowExtensions: true,
	        autoFormat: true,
	        // autoHideDialCode: false,
	        autoPlaceholder: true,
	        // dropdownContainer: "body",
	        // excludeCountries: ["us"],
	       geoIpLookup: function(callback) {
	       $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
	        var countryCode = (resp && resp.country) ? resp.country : "";
	        callback(countryCode);
	        });
	        },
	        // initialCountry: "auto",
	        // nationalMode: false,
	        numberType: "MOBILE",
	        // onlyCountries: ['us', 'gb', 'ch', 'ca', 'do'],
	        // preferredCountries: ['cn', 'jp']
	      });

		var reset = function() {
			telInput.removeClass("error");
			errorMsg.addClass("hide");
		    validMsg.addClass("hide");
		};

		// on blur: validate
		telInput.blur(function() {
		    reset();
		    if ($.trim(telInput.val())) {
		        if (telInput.intlTelInput("isValidNumber")) {
			        validMsg.removeClass("hide");
			    } else {
			        telInput.addClass("error");
			        errorMsg.removeClass("hide");
			    }
		    }
		});

		// on keyup / change flag: reset
		telInput.on("keyup change", reset);
});
