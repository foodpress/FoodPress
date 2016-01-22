/*
	Script that runs on widget page only
	ver: 0.1
*/
jQuery(document).ready(function($){

	

	// select option
		$('.widgets-sortables').on('click','h3.fpw_step_btns', function(){

			$('.fpw_step1').find('h3').removeClass('selected');
			$(this).addClass('selected');

			//$('.fpwi').hide();
			var option = $(this).data('option');
			var par_section = $(this).parent().siblings('.fpw_step2');

			//console.log(option);
			par_section.find('.fpwi').hide();
			par_section.find('#'+option).show();

			$(this).closest('.foodpressW_inner').find('.fpw_selection_type').attr({'value':option});

		});

	// style selection
		$('.widgets-sortables').on('click','.fpw_style_btn',function(){
			var optob = $(this).siblings('.fpPop_options');

			if(optob.hasClass('show')){
				hide_style_selection_box(optob);
			}else{
				var posit = $(this).position();
				
				optob.css({left: posit.left});
				optob.addClass('show');
				optob.show();

				//console.log(posit);
			}
		});
	// select a style value
		$('.widgets-sortables').on('click', '.fpw_style_selection p', function(){
			var style = $(this).data('value');
			var optob = $(this).parent();

			optob.siblings('.fpw_item_style').attr({'value':style});

			optob.find('p').removeClass('selected');
			$(this).addClass('selected');

			// set new value
			optob.siblings('span').html( $(this).data('name') );
			$(this).closest('.fpw_item_box').find('.fpw_item_style').attr({'value':style});

			// hide the options
			hide_style_selection_box(optob );
		});


	// clonning content
	$('.widgets-sortables').on('click','.fpw_add_item', function(){
		var section = $(this).closest('.fpwi').find('.fpwi_boxes');
		var count = $(this).parent().attr('data-numitems');

		if(count!=5){
			console.log(count);
			var next_count = parseInt(count)+1;

			section.find('.fpwb'+next_count).show();

			// change to new count
			if(next_count==5){
				$(this).parent().hide();
			}else{
				$(this).parent().attr({'data-numitems':next_count});
			}
		}
	});


	// supporter functinos
		function hide_style_selection_box(obj){
			obj.hide();
			obj.removeClass('show');
		}

});