jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.foodpress_shortcode_button', {
         init : function(ed, url) {
             ed.addButton('foodpress_shortcode_button', {
                title : 'Add Foodpress Menu',
                onclick : function() {
                   $('#foodpress_popup').addClass('active').show().animate({'margin-top':'0px','opacity':1}).fadeIn();

                  $('html, body').animate({scrollTop:0}, 700);
                  $('#fp_popup_bg').show();
                }
             });
          },
          createControl : function(n, cm) {
             return null;
          },
          getInfo : function() {
             return {
                longname : "Foodpress Shortcode",
                author : 'Ashan Jay',
                authorurl : 'http://www.ashanjay.com',
                version : "1.0"
             };
          }  
    });

    tinymce.PluginManager.add('foodpress_shortcode_button', tinymce.plugins.foodpress_shortcode_button);

    // click on the foodpress shortcode button
      $('body').on('click','#fp_shortcode_btn', function(e){
          e.preventDefault();
      });

});