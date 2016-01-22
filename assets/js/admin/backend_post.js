/*
	backend menu post scripts
*/
jQuery(document).ready(function($){
	

	// meta box sections
	// click hide and show
		$('#fp_meta_fields').on('click','h4',function(){
			
			var box = $(this).siblings('.foodpress_metafield');
			
			if(box.hasClass('closed')){
				box.slideDown('fast').removeClass('closed');
			}else{
				box.slideUp('fast').addClass('closed');
			}
			update_eventEdit_meta_boxes_values();
		});
		
		function update_eventEdit_meta_boxes_values(){
			var box_ids ='';
			
			$('#fp_meta_fields').find('.foodpress_metafield').each(function(){
				
				if($(this).hasClass('closed'))
					box_ids+=$(this).data('box-id')+',';
				
			});
			
			$('#fp_collapse_meta_boxes').val(box_ids);
		}
	
	//yes no buttons in event edit page
		$('.foodpress').on('click','.fp_yn_btn', function(){
			
			if($(this).hasClass('disable')){
			
			}else{
				// yes
				if($(this).hasClass('NO')){
					$(this).removeClass('NO');
					$(this).siblings('input').val('yes');
					
					$('#'+$(this).attr('afterstatement')).slideDown('fast');
					
				}else{//no
					$(this).addClass('NO');
					$(this).siblings('input').val('no');
					
					$('#'+$(this).attr('afterstatement')).slideUp('fast');
				}
			}		
		});

	// selecting menu icons
		$('.menu_item_icons').on('click',function(){
			var new_val='';		
			var obj = $(this);

			// add or remove selected class
			if(obj.hasClass('selected')){
				obj.removeClass('selected');
			}else{
				obj.addClass('selected');
			}



			$(this).parent().find('p.selected').each(function(){
				new_val += $(this).data('val')+',';
			});
			
			obj.siblings('input').attr('value',new_val);
		});

	// reservation post type
		$( "#fp_res_date" ).datepicker({
			minDate:0
		});
	
	// date and time fields in the reservation form
		$( ".fp_res_input_icon_clock").each(function(){
			var obj = $(this);
			var step = obj.closest('.step');
			var timeFormat = ( step.data('format')=='24')? 'H:i':'h:i A';

			if(step.attr('data-restrict')=='yes'){
				obj.timepicker({
					'minTime': step.data('start'),
						'maxTime': step.data('end'),
						'timeFormat': timeFormat,
				});
			}else{
				obj.timepicker({
					'timeFormat': timeFormat,
					'showDuration': true,
				});
			}

			// when start time is set in start end time pairing change end time
			if(obj.parent().attr('data-type')=='multi'){
				obj.on('changeTime', function(){
					if($(this).attr('name')=='time'){
						changeEnd($(this), timeFormat);
					}					
				});
			}
		});

		// change end time based on start time for reservations for multi 
			function changeEnd(obj, timeFormat){
				var starttime = obj.val();
				var endtime = obj.siblings('input');

				endtime.timepicker({
					'minTime':starttime,
					'timeFormat': timeFormat,
				}).attr({'value':starttime});
			}

	// checkin reservations
		$('#reservation_table').on('click','.reservation_status p',function(){
			var obj = $(this);
			var status = obj.attr('data-status');

			status = (status=='' || status=='check-in')? 'checked':'check-in';

			var data_arg = {
				action: 'the_ajax_res01x',
				res_id: obj.attr('data-res_id'),
				status:  status
			};
			$.ajax({
				beforeSend: function(){
					obj.html( obj.html()+'...');
				},
				type: 'POST',
				url:the_ajax_script.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					obj.attr({'data-status':status}).html(data.new_status_lang).parent().removeAttr('class').addClass(status+' reservation_status');

				}
			});
		});
});