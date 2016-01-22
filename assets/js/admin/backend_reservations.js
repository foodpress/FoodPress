/**
 * Backend Reservation script
 * @version  1.3
 */
jQuery(document).ready(function($){

	// Reservation time slot addition section
		$('#add_reservation_time_slot').on('click',function(){
			row = $(this).parent();
			date = row.find('select.reservation_date').val();
			date_text = row.find('select.reservation_date option:selected').text();
			from = row.find('select.from').val();
			to = row.find('select.to').val();
			string = date+'-'+from+'-'+to;

			// check if it exists
			if( $('.fp_reservable_times p[data-d="'+string+'"]').length==0 ){
				html = "<p data-d='"+string+"'>"+date_text+" "+from+' - '+to+" <span>X</span><input type='hidden' name='fp_res[]' value='"+string+"'/></p>";
				$('body').find('.fp_reservable_times').append(html);
				msg = 'ADDED';
			}else{
				msg = 'Already Exists';
			} 

			$(this).siblings('em').html(msg).fadeIn(300).delay(1000).fadeOut(300);
		});

	// unreservables
        // verify the datepicker element exists before trying to use it on pages that the datepicker.js is not being loaded
        if($('#fp_input_unreserve').length) {
            $('#fp_input_unreserve').datepicker({
                'dateFormat': 'yy-mm-dd'
            });  
        }
		
		$('#add_unreserve_date').on('click',function(){
			date = $(this).siblings('input').val();
			if(!date) return false;

			// check if it exists
			if( $('.fp_reservable_times p[data-d="'+date+'"]').length==0 ){
				html = "<p data-d='"+date+"'>"+date+"<span>X</span><input type='hidden' name='fp_unres[]' value='"+date+"'/></p>";
				$('body').find('.fp_unreservable_dates').append(html);
				msg = 'Added';
			}else{
				msg = 'Already Exists';
			}
			
			$(this).siblings('em').html(msg).fadeIn(300).delay(1000).fadeOut(300);
		});
		// delete unreservable
			$('.fp_unreservable_dates p').on('click','span',function(){
				$(this).parent().fadeOut().remove();
			})


	// reservations
		$('.fpr_sections').on('click','.fpr_view_res', function(){

			var data_arg = {
				action: 		'fp_ajax_set_res',
				type: 		$(this).data('type'),
			};
			$.ajax({
				beforeSend: function(){},
				type: 'POST',
				url:fp_reservations.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					if(data.status=='0'){					
						$('.fp_set_res').find('.foodpress_popup_text').html(data.content);
					}else{
						$('.fp_set_res').find('.foodpress_popup_text').html('Could not load reservations');
					}
				},complete:function(){}
			});
		});

	// delete reservation
		$('.foodpress_popup_text').on('click','.fp_res_list i.Dres', function(){
			var obj = $(this);
			var rid = obj.data('rid');
			
			var data_arg = {
				action: 		'fp_ajax_delete_res',
				rid: rid,
				postnonce: fp_reservations.postnonce, 
			};
			$.ajax({
				beforeSend: function(){
					obj.closest('.fp_reservation').animate({'opacity':'0.5'});
				},
				type: 'POST',
				url:fp_reservations.ajaxurl,
				data: data_arg,
				dataType:'json',
				success:function(data){
					//alert(data);
					if(data.status=='success'){					
						obj.closest('.fp_reservation').fadeOut();
					}
				},complete:function(){}
			});
		});

});