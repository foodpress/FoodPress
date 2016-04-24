jQuery(document).ready(function(){  
    jQuery('#the-list').on('click', '.editinline', function(){  
		
		inlineEditPost.revert();

		var post_id = jQuery(this).closest('tr').attr('id');
		
		post_id = post_id.replace("post-", "");
		
		var $fp_inline_data = jQuery('#foodpress_inline_' + post_id );
		
		var price 		= $fp_inline_data.find('.price').text();
		var spicelevel 	= $fp_inline_data.find('.spicelevel').text();
		var vegetarian 	= $fp_inline_data.find('.vegetarian').text();
		var _featured 	= $fp_inline_data.find('.featured').text();
		
		
		jQuery('input[name="fp_price"]', '.inline-edit-row').val(price);
		
		
		jQuery('select[name="fp_spicy"] option[value="'+ spicelevel+'"]', '.inline-edit-row').attr('selected','selected');

		jQuery('select[name="fp_vege"] option[value="'+ vegetarian+'"]', '.inline-edit-row').attr('selected','selected');

		
		if (_featured=='yes') {
			jQuery('input[name="_featured"]', '.inline-edit-row').attr('checked', 'checked'); 
		} else {
			jQuery('input[name="_featured"]', '.inline-edit-row').removeAttr('checked'); 
		}
		
		
    });  
    
   
    
    
    
});  