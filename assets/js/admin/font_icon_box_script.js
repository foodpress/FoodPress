// ======================================================
// font icons for foodpress popup box controlling and calling


jQuery(document).ready(function($){
	var fa_icon_selection = '';

	// font awesome icons
	$('.faicon').on('click','i', function(){
		var poss = $(this).position();
		$('.fa_icons_selection').css({'top':(poss.top-220)+'px', 'left':(poss.left-74)}).fadeIn('fast');

		fa_icon_selection = $(this);
	});

		//selection of new font icon
		$('.fa_icons_selection').on('click','li', function(){

			var icon = $(this).find('i').data('name');
			console.log(icon)

			fa_icon_selection.attr({'class':'fa '+icon});
			fa_icon_selection.siblings('input').val(icon);

			$('.fa_icons_selection').fadeOut('fast');
		});
		// close with click outside popup box when pop is shown
		$(document).mouseup(function (e){
			var container=$('.fa_icons_selection');
			
				if (!container.is(e.target) // if the target of the click isn't the container...
				&& container.has(e.target).length === 0) // ... nor a descendant of the container
				{
					$('.fa_icons_selection').fadeOut('fast');
				}
			
		});
});