<?php
/**
 * Template Loader
 *
 * @class 		fp_Template_Loader
 * @version		1.1.5
 * @package		foodpress/Classes
 * @category	Class
 * @author 		AJDE
 */
class fp_Template_Loader {
	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_loader' ) );
	}


		/**
	 * Load a template.
	 *
	 * Handles template usage so that we can use our own templates instead of the themes.
	 *
	 * Templates are in the 'templates' folder. foodpress looks for theme
	 * overrides in /theme/foodpress/ by default
	 *
	 * For beginners, it also looks for a foodpress.php template first. If the user adds
	 * this to the theme (containing a foodpress() inside) this will be used for all
	 * foodpress templates.
	 *
	 * @access public
	 * @param mixed $template
	 * @return string
	 */
	public function template_loader( $template ) {
		global $foodpress;
		
		$file='';
		$sure_path = FP_PATH . '/templates/';
		
		
		// Paths to check
		$paths = apply_filters('foodpress_template_paths', array(
			0=>TEMPLATEPATH.'/',
			1=>TEMPLATEPATH.'/'.$foodpress->template_url,
		));
		
		
		$menu_page_id = foodpress_get_menu_archive_page_id();
		
		// single and archieve events page
		if( is_single() && get_post_type() == 'menu' ) {
			$file 	= 'single-menu.php';

		// reservations archieve page	
		}elseif ( is_post_type_archive( 'reservation' )){
			
			wp_redirect(get_site_url());
			exit;
			//$file 	= 'all-reservations.php';
			//$paths[] 	= FP_PATH . '/templates/';
		}elseif ( is_post_type_archive( 'menu' ) || ( !empty($menu_page_id) && is_page( $menu_page_id )  )) {
			$file 	= 'archive-menu.php';
			$paths[] 	= FP_PATH . '/templates/';
		}
		
		// FILE Exist
		if ( $file ) {
			
			// each path
			foreach($paths as $path){				
				if(file_exists($path.$file) ){	
					$template = $path.$file;	
					break;
				}
			}		
			
				
			if ( ! $template ) { 
				$template = FP_PATH . '/templates/' . $file;
			
			}
		}
		
		return $template;
	}
}
new fp_Template_Loader();