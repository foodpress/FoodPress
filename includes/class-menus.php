<?php
/**
 * foodpress menu main class.
 *
 * @class 		foodpress_menus
 * @version		1.3.2
 * @package		foodpress/Classes
 * @category	Class
 * @author 		AJDE
 */

class foodpress_menus {
	
	public $fopt1, 
		$fopt2,
		$lang;
	public $shortcode_args = array();
	private $_menu_call_type ='';
	
	public function __construct(){
		global $foodpress;
		/** set class wide variables **/
		$this->fopt1= get_option('fp_options_food_1');		
		$this->fopt2= get_option('fp_options_food_2');		
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_default_styles' ) );
		
		$this->functions = $foodpress->functions;
	}	
	
	// Register/queue frontend scripts.
		public function frontend_default_styles() {
			global $foodpress;				
			
			$foodpress->load_default_fp_styles();		
			$foodpress->load_default_fp_scripts();	
			
			if(has_action('foodpress_enqueue_scripts')){
				do_action('foodpress_enqueue_scripts');
			}
		}	
	
	// SHORT CODE variables
		function get_acceptable_shortcode_atts(){
			return apply_filters('foodpress_default_args', array(
				'id'=>'1',
				'meal_type'=>'all',
				'dish_type'=>'all',
				'menu_location'=>'all',
				'menu_type_3'=>'all',
				'menu_type'=>'ss_1', // ss_2 - categorized or uncategorized
				'primary'=>'meal_type', // primary categorizeation by
				'cat_by_dish'=>'no', // sub categorize by dish 
				'all_cat_items'=>'no',// yes no
				'collapsable'=>false,
				'collapsable_dt'=>'no',	// collapsable dish type
				'collapsed'=>'no',
				'collapsed_dt'=>'no', // dish type collapsed on load
				'style'=>'one',
				'ft_style'=>'none',
				'cat_sty'=>'de',	// category style
				'focused_tab'=>'', 	// focused tab for tabbed view
				'box_width'=>'100', // 33 50 100
				'fbox_width'=>'100', // 33 50 100
				'ft_dif'=>'no',	// differentiate featured items
				'orderby'=>'title',
				'order'=>'ASC',
				'tac'=>'no', // center align menu content - only for normal list menu style
				'ux'=>'lightbox', // diable menu clicking
				'mt_des'=>'no', // show meal type description
				'dt_des'=>'no', // show dish type description
				'boxhei'=>'0',		// minimum height
				'wordcount'=>'20',
				'show_menu_updated'=>'no',// show menu last updated date
				'link'=>'',
				'lang'=>'L1', 		// language
				'wpml_l1'=>'',		// WPML lanuage L1 = en
				'wpml_l2'=>'',		// WPML lanuage L2 = nl
				'wpml_l3'=>'',		// WPML lanuage L3 = es
			));
		}
	
	// PROCESS shortcode arguments
		function process_arguments($args='', $type='', $own_defaults=false){
			$default_arguments = $this->get_acceptable_shortcode_atts();

			if(!empty($args)){
			
				// merge default values of shortcode
				if(!$own_defaults)
					$args = shortcode_atts($default_arguments, $args);

				$this->shortcode_args=$args; // set global arguments
			
			// empty args
			}else{
				
				if($type=='usedefault'){
					$args = (!empty($this->shortcode_args))? $this->shortcode_args:null;
					
				}else{
					$this->shortcode_args=$default_arguments; // set global arguments
					$args = $default_arguments;
				}
			}

			// process WPML
				if(defined('ICL_LANGUAGE_CODE')){
					for($x=1; $x<4; $x++){
						if(!empty($args['wpml_l'.$x]) && $args['wpml_l'.$x]==ICL_LANGUAGE_CODE){
							$args['lang']='L'.$x;
						}
					}
				}
			// set processed argument values to class var
			$this->shortcode_args=$args;
			
			return $args;
		}

	// process arguments for include in the menu
		public function args_for_menu(){
			$arg = $this->shortcode_args;
			$_cd='';
			//print_r($arg);
			
			$cdata = apply_filters('foodpress_menu_shortcode_args', $arg);
			$includes = apply_filters('foodpress_menu_sh_includes', array('meal_type','ux','id','tac','wordcount','lang','wpml_l1','wpml_l2','wpml_l3'));

			foreach ($cdata as $f=>$v){
				if(!empty($v) && in_array($f, $includes))
					$_cd .='data-'.$f.'="'.$v.'" ';
			}
			return "<div class='menu_arguments' style='display:none' {$_cd}></div>";			
		}

	// ============================
	// PRIMARY menu function
		public function generate_content($args, $type=''){

			// call type update
			$this->_menu_call_type = $type;

			// initial values
				$args__ = $this->process_arguments($args);
				extract($args__);	

				$__menu_classes = array('foodpress_menu');
				if(!empty($tabbed) && $tabbed=='yes') {$__menu_classes[]='tabbed_menu'; };
				if(!empty($cat_sty) && $cat_sty=='tb') {$__menu_classes[]='tabbed_menu'; $tabbed='yes';}else{$tabbed='no';};				
				if(!empty($cat_sty) && $cat_sty=='bx') {$__menu_classes[]='box_cats'; };
				if(!empty($collapsable_dt) && $collapsable_dt=='yes') {$__menu_classes[]='clps_dt'; };

				// centerlized menu
				if($cat_sty=='de' && $tac=='yes') {$__menu_classes[]='centermenu'; };


				$__menu_classes = implode(' ', $__menu_classes);

			ob_start();
			echo "<div class='{$__menu_classes}' id='fp_{$id}' data-tabbed='{$tabbed}'>";

				// menu arguments
				echo $this->args_for_menu();

				do_action('foodpress_before_menu_header');

				// menu type
				switch ($menu_type){
					case 'ss_2':// categorized menu
						// menu style
						
						$primary_term = ($primary == 'meal_type')? $meal_type: $dish_type;
						$primary_terms = $this->functions->get_tax_terms($primary, $primary_term);

						switch ($cat_sty) {
							// TABBED MENU
							case 'tb':
								if(!empty($primary_terms)){

									// focused tab
										$focused_tab = !empty($focused_tab)? (int)$focused_tab: false;

									echo "<div class='foodpress_tabs'>";
									$count = $cnt =0;
									foreach($primary_terms as $term){

										// focused tab
										$focsued = (
											($focused_tab && $focused_tab==$term->term_id) || 
											(!$focused_tab && $count==0)
										)? 'focused':'';

										// icon
										$term_meta = get_option( "fp_taxonomy_$term->term_id" );
										$icon = $this->functions->get_term_icon($term->term_id, $term_meta, true);

										echo "<h4 class='".$focsued."' data-i='{$count}' data-tid='{$term->term_id}' data-termslug='".$term->slug."'>".$icon.$term->name."</h4>";
										$count++;
									}
									echo "</div><!-- foodpress_tabs-->";

									// each term for tab content
									echo "<div class='foodpress_tab_body'>"; 
									foreach($primary_terms as $term){

										$focused = (($focused_tab && $focused_tab==$term->term_id) || (!$focused_tab &&$cnt==0))? 'block':'none';

										echo "<div data-i='{$cnt}' class='foodpress_tab_content fp_{$term->slug}' style='display:".$focused."'>";

										// description
											echo (!empty($term->description))? "<p class='fp_meal_type_description'>".$term->description."</p>":'';
										
										$content = $this->get_menu_items( $term->term_id, false, false);
										echo ($content)? $content : 'No Menu Items';

										$cnt++;
										echo "</div>";
									}
									echo "</div>";
									
								}else{	echo __("No meal type tabs found");	}
							break;

							// box menu
							case 'bx':
								if(!empty($primary_terms)){

									echo "<div class='fp_categories_holder'>";

									echo "<div class='foodpress_categories'>";
									$count = $cnt =0;
									foreach($primary_terms as $term){
										$term_meta = get_option( "fp_taxonomy_$term->term_id" );
										$image_src = $this->functions->get_term_image($term->term_id, $term_meta);
										$icon = $this->functions->get_term_icon($term->term_id, $term_meta, true);

										$description = (!empty($term->description))? "<p class='fp_meal_type_description'>".$term->description."</p>":'';

										echo "<h4 class='".($count==0? 'focused':'')."' data-i='{$count}' data-termslug='".$term->slug."' data-name=\"".($term->name)."\"><span class='mtimg' style='background-image:url({$image_src}'></span>".$icon.$term->name.$description."</h4>";
										$count++;
									}

									echo "</div><!-- foodpress_categories -->
									<div class='fp_content' style='display:none'><span class='fp_backto_cats'><i></i><em>".$this->functions->fp_get_language('Back to Menu', $this->fopt2)."</em></span>
										<span class='fp_category_subtitle'></span>"; 
									foreach($primary_terms as $term){
										echo "<div class='foodpress_tab_content {$term->slug} fp_{$cnt}' style='display:".($cnt==0?'block':'none')."'>";
										
										$content = $this->get_menu_items($term->term_id, false, false);
										echo ($content)? $content : 'No Menu Items';

										$cnt++;
										echo "</div>";
									}
									echo "</div><!-- fp_content-->";

									echo "</div><!-- fp_categories_holder-->";
								}else{	echo __("No meal types found");	}
							break;

							// SCROLL MENU
							case 'sc':
								if(!empty($primary_terms)){

									// focused tab
										$focused_tab = !empty($focused_tab)? (int)$focused_tab: false;

									echo "<div class='foodpress_scroll_sections'>";
									$count = $cnt =0;
									foreach($primary_terms as $term){
										// focused tab
										$focsued = (($focused_tab && $focused_tab==$term->term_id) || (!$focused_tab && $count==0))? 'focused':'';

										echo "<h4 class='".$focsued."' data-i='{$count}' data-tid='{$term->term_id}' data-termslug='".$term->slug."'>".$term->name."</h4>";
										$count++;
									}
									echo "</div><!-- foodpress_sections-->";

									// each term for tab content
									echo "<div class='foodpress_scroll_section_body'>"; 
									foreach($primary_terms as $term){
										
										echo "<div data-i='{$cnt}' class='foodpress_section_content fp_{$term->slug}' >";

										// header
											$term_meta = get_option( "fp_taxonomy_$term->term_id" );
												$icon = $this->functions->get_term_icon($term->term_id, $term_meta, true);
											echo "<h4 class='".$focsued."' data-i='{$count}' data-tid='{$term->term_id}' data-termslug='".$term->slug."'>".$icon.$term->name."</h4>";
										// description
											echo (!empty($term->description))? "<p class='fp_meal_type_description'>".$term->description."</p>":'';
										
										$content = $this->get_menu_items( $term->term_id, false, false);
										echo ($content)? $content : 'No Menu Items';

										$cnt++;
										echo "</div>";
									}
									echo "</div>";
									
								}else{	echo __("No meal type tabs found");	}
							break;			
							default: // normal list
								echo $this->get_menu_items();
							break;
						}
					break;

					case 'ss_5': // specific meal and dish type
					case 'ss_4':

						if(!empty($meal_type) && !empty($dish_type)){
							if($meal_type!='all' && $dish_type=='all'){
								$this->shortcode_args['primary'] = 'meal_type';
								echo $this->get_menu_items($meal_type);
							}
							if($dish_type!='all' && $meal_type=='all'){
								$this->shortcode_args['primary'] = 'dish_type';
								echo $this->get_menu_items($dish_type);
							}
							if($dish_type!='all' && $meal_type!='all'){
								$this->shortcode_args['primary'] = 'meal_type';
								$this->shortcode_args['cat_by_dish'] = 'yes';
								echo $this->get_menu_items($meal_type);
							}
						}else{
							echo __('Shortcode is missing required variables', 'foodpress');
						}

					break;
					
					default:// uncategorize case
						echo $this->generate_un_categoried_menu();
					break;
				}

			do_action('foodpress_menu_footer');

			echo "<div class='clear'></div>";

			// if show last menu items updated date
				if($show_menu_updated == 'yes'){
					$lastU = $this->functions->last_updated_date();
					if($lastU)
						echo "<p class='menu_last_updated'>".$this->functions->fp_get_language('Menu Last Updated', $this->fopt2).': '.$lastU."</p>";
				}

			echo "</div>";
			
			// return validated content
			$content = ob_get_clean();
			return $this->validate_content($content);

		}

		function _each_type_outter($meal_type, $dish_type, $slug, $mealTypeName='', $DTterm_name=''){
			$dishTypeText = $mealTypeText = '';

			// if meal type is all
			if($meal_type=='all' && $dish_type!='all'){
				$this->shortcode_args['primary'] = 'dish_type';
				$dishTypeText = $DTterm_name;
			}elseif($meal_type!='all' && $dish_type=='all'){
				$mealTypeText = $mealTypeName;
			}elseif($meal_type!='all' && $dish_type!='all'){
				$mealTypeText = $mealTypeName;
				$dishTypeText = ' > '.$DTterm_name;
			}

			
			echo "<h2 class='meal_type fp_menu_sub_section tint_menu dish_type_{$dish_type} meal_type_{$meal_type}'>{$mealTypeText}{$dishTypeText}</h2>";							
			echo "<div class='fp_container fp_{$slug}'>";
			echo "<div class='food_items_container' >";

			echo $this->get_menu_items('', false);
			//echo $this->get_dishtype_menuitems($dish_type, $meal_type);
			echo "</div><div class='clear'></div>";
			echo "</div>";
		}

	// get menu items for specific section
	// termids are a string of ids separated by commas
		function get_menu_items($terms='', $headers= true, $container = true){

			$shortc = $this->shortcode_args;
			$primary = $shortc['primary'];
			$terms = !empty($terms)? $terms:$shortc[$primary];

			switch ($primary) {
				case 'meal_type':		
					return $this->general_categorized_menu('meal_type', $terms, $headers, $container);
				break;
				case 'dish_type':
					return $this->general_categorized_menu('dish_type', $terms, $headers, $container);
				break;
			}
		}
	
	// GENERAL categorized menu
		function general_categorized_menu($tax, $terms, $header=false, $container=true){
			// initial
				$args__ = $this->shortcode_args;
				extract($args__);

				// collapsable and collapsed options
					if($collapsed=='yes'){
						$__colps = 'style="display:none"';
						$_collapsable = 'collapsable collapsed';			
					}else{
						$__colps = null;
						$_collapsable = ($collapsable=='yes')? 'collapsable':null;
					}

					// if the primary categorization is by DT
					if( $tax=='dish_type'){
						if($collapsed_dt=='yes'){
							$__colps = 'style="display:none"';
							$_collapsable = 'collapsable collapsed';			
						}else{
							$__colps = null;
							$_collapsable = ($collapsable_dt=='yes')? 'collapsable':null;
						}
					}else{
						if($collapsed_dt=='yes'){
							$__colps_dt = 'style="display:none"';
							$_collapsable_dt = 'collapsable collapsed';			
						}else{
							$__colps_dt = null;
							$_collapsable_dt = ($collapsable_dt=='yes')? 'collapsable':null;
						}
					}					
									
				// get tax terms
					$tax_terms = $this->functions->get_tax_terms($tax, $terms);

				// DISH TYPE terms - is sub categorization by DT - while primary category is MT
					if($cat_by_dish=='yes' && $tax=='meal_type'){
						$dish_tax_terms = $this->functions->get_tax_terms('dish_type', $dish_type);
					}else{$dish_type_terms=array();}

			ob_start();
			
			// For each primary tax term
				if(count($tax_terms)>0 && is_array($tax_terms)):

					// EACH primary tax terms
					foreach($tax_terms as $term): 

						// intials
							$section = array();
							$term_meta = get_option( "fp_taxonomy_$term->term_id" );
							$__des_class = (!empty($__mt_des))? 'data-desc="1"':null;

							// meal type menu icon
								$__menuicons = $this->functions->get_term_icon($term->term_id, $term_meta, true);

							// menu category image
								$__mt_img_src = '';
								if($cat_sty=='bx'){
									$__mt_img_src = $this->functions->get_term_image($term->term_id, $term_meta);
								}

								$__mt_img_src = (!empty($__mt_img_src))? $__mt_img_src[0]:null;

							$term_name = $this->get_lang('fp_lang_tax_meal_type_'.$term->term_id,$term->name );
							

						// header content for primary tax term
							if($header){
								// description
									$term_description = $this->functions->get_term_desc($term->description, $tax);

								$section['header'] = "<h2 class='primary_type meal_type fp_menu_sub_section tint_menu menu_term_{$term->term_id} {$_collapsable} ". ( $__menuicons? 'with_icons':'') ."' {$__des_class} data-name='{$term_name}' data-slug='{$term->slug}' data-src='{$__mt_img_src}'>{$__menuicons}{$term_name}<span class='fp_menu_expand'></span>".( $term_description? $term_description:'')."</h2>";
							}

						// IF dish type subcategory
						if($cat_by_dish=='yes' &&  $tax=='meal_type' && count($dish_tax_terms)>0){

							// EACH DISH type terms
							$DT_content = $output='';
										
							// for EACH DISH TYPE term
								foreach($dish_tax_terms as $dish_term){

									// icon
									$DT_icon = $this->functions->get_term_icon($dish_term->term_id, '', true);

									// description
									$term_description = $this->functions->get_term_desc($dish_term->description, 'dish_type');

									$dishtype_items = $this->get_dishtype_menuitems($dish_term->term_id, $term->term_id);
									if(empty($dishtype_items)) continue;

									$output .= "<h3 class='secondary_type dish_type fp_menu_sub_section tint_menu menu_term_{$dish_term->term_id} {$_collapsable_dt} ". ( $DT_icon? 'with_icons':'') ."'>{$DT_icon}{$dish_term->name}<span class='fp_menu_expand'></span>{$term_description}</h3>";

									$output .= "<div class='food_items_container secondary_section fp_{$dish_term->slug}' {$__colps_dt} >";
									$output .= $dishtype_items;
									$output .= "</div><div class='clear'></div>";
									
								}

							if(!empty($output)){
								// Main category header
								$DT_content .= ($container)? "<div class='secondary_container fp_container fp_mt fp_{$term->slug}' ". $__colps.">":'';

								$DT_content .= $output;
								$DT_content .=	'<div class="clear"></div>';
								$DT_content .=	($container)? '</div>':'';

								$section['content'] = $DT_content;
							}

						// NO sub categorization
						}else{
							$content =  ($container)? "<div class='fp_container fp_{$term->slug}' {$__colps}>":'';
							$content .=  "<div class='food_items_container' >";

							$inside_content = ($tax=='dish_type')? 
								$this->get_dishtype_menuitems($term->term_id)
								:$this->get_primary_menuitems($term->term_id);

							$content .= $inside_content;							
							$content .= "</div><div class='clear'></div>";
							$content .= ($container)? "</div>":'';

							if(!empty($inside_content)) $section['content']= $content;
						}

						// check if section content is provided to create section
						if(!empty($section['content']))
							echo implode('',$section);
						
					endforeach; // each primary term
				endif;

			return ob_get_clean();
		}

		function get_tax_terms($tax, $terms){
			return $this->functions->get_tax_terms($tax, $terms);
		}

	// return meal type and dish type menu item content
		function get_primary_menuitems($termid){
			$wpQ = $this->get_wp_arguments(array('mealids'=>$termid));

			// pass the WP argument to get content
			$output= $this->run_wp_query($wpQ);
			return $output;
		}
		function get_dishtype_menuitems($termid, $parenttermID=''){
			$wpQ = $this->get_wp_arguments(array('mealids'=>$parenttermID, 'dishids'=>$termid));

			// pass the WP argument to get content
			$output= $this->run_wp_query($wpQ);
			return $output;
		}

	// get wp arguments with tax arguments added
	// array('mealids','dishids','locationids')
	// @version 1.3
		function get_wp_arguments($args){
			$shortC = $this->shortcode_args;
			if(!empty($args['mealids'])){
				$l_id = (is_array($args['mealids'])? $args['mealids']: explode(',', $args['mealids']));

				$tax = 'meal_type';

				$meal_type= array(
			     'taxonomy' => $tax,
			     'field' => 'id', 
			     'terms' => $args['mealids']
			    );
			}else{	$meal_type=''; }

			if(!empty($args['dishids'])){
				$l_id = (is_array($args['dishids'])? $args['dishids']: explode(',', $args['dishids']));
				$dish_type= array(
			     'taxonomy' => 'dish_type',
			     'field' => 'id', 
			     'terms' => $args['dishids']
			    );
			}else{	$dish_type=''; }

			// get menu location from shortcodes
				if(!empty($shortC['menu_location']) && $shortC['menu_location']!='all'){
					$l_id = explode(',', $shortC['menu_location']);
					$menu_location= array(
				     'taxonomy' => 'menu_location',
				     'field' => 'id', 
				     'terms' => $l_id
				    );
				}else{ $menu_location='';}

			// Meal type 3 taxonomy filtering
				if(!empty($shortC['menu_type_3']) && $shortC['menu_type_3']!='all'){
					$l_id = explode(',', $shortC['menu_type_3']);
					$menu_type_3= array(
				     'taxonomy' => 'menu_type_3',
				     'field' => 'id', 
				     'terms' => $l_id
				    );
				}else{ $menu_type_3='';}

			// WP QUERY
				$wp_argument = apply_filters('foodpress_cat_wp_arg', array(
					'post_type'=>'menu',
					'posts_per_page'=>-1,
					'tax_query' => array(
						 $meal_type, $dish_type,$menu_location, $menu_type_3
					)
				));
			return $wp_argument;
		}

	// show all menu items W/O categorising
		function generate_un_categoried_menu(){

			// extract the variable values 
			$args__ = $this->shortcode_args;
			extract($args__);


			// WE BUILD the wp argument here
			// except special items
			$wp_argument = apply_filters('foodpress_uncat_wp_arg', array(
				'post_type'=>'menu',
				'posts_per_page'=>-1,
				'meta_query' => array(
					'relation' => 'OR',
				    array(
				     'key' => '_special',
				     'compare' => 'NOT EXISTS', // works!
				     'value' => '' // This is ignored, but is necessary...
				    ),
				    array(
			           'key' => '_special',
			           'value' => 'yes',
			           'compare' => 'NOT IN',
			        )
				)
			));

			if(!empty($menu_location) && $menu_location!='all'){
				$l_id = explode(',', $menu_location);
				$menu_location= array(
			     'taxonomy' => 'menu_location',
			     'field' => 'id', 
			     'terms' => $l_id
			    );

			    $wp_argument['tax_query'][] =$menu_location;
			}


			// pass the WP argument to get content
			return $this->run_wp_query($wp_argument);

		}
	
	// RUN WP_Query for all arguments and return data or false
		function run_wp_query($wp_argument){
			// extract the variable values 
			$args__ = $this->shortcode_args;
			extract($args__);

			// order arguments
			$wp_argument['orderby']=$orderby;
			$wp_argument['order']=$order;

			$menu = new WP_Query($wp_argument);
			
			if($menu->have_posts()):
				$output='';	
				
				// create an array of items for the menu 
				$featured = $items = array();

				while($menu->have_posts()): $menu->the_post();
					$post_id = get_the_ID();
					$ft = get_post_meta( $post_id, '_featured', true );

					// if featured item
					if(!empty($ft) && $ft=='yes' && $ft_dif=='yes'){
						$featured[] = $post_id;
					}else{
						$items[] = $post_id;
					}

				endwhile;


				// Make an array of items with featured on top
				if(is_array($featured) && is_array($items)){
					$items = array_merge($featured, $items);
				}

				// send the array of menu items to get HTML content
				$output = $this->get_individual_item_content($items, '','',$featured);
				wp_reset_postdata();
				return $output;
			else:
				wp_reset_postdata();
				return false;

			endif;
		}

	// GET HTML for each menu item
		public function get_individual_item_content($items_array, $args='', $type='', $ft_items_array=''){
			

			if(!empty($items_array) && is_array($items_array)){

				// extract the shortcode argument values
				if(!empty($args)){ // if shortcode arguments sent to function 
					$args__ = $this->process_arguments($args);
				}else{
					$args__ = $this->shortcode_args;}			
				extract($args__);

				ob_start();

				//print_r($args__);

				// container element
				echo (!empty($type) && $type=='single')? "<div class='foodpress_menu fp_single_item_box'>":null;

				$_ft_div_start = false;
				$_ft_div_end_html ='';
				$_ft_item_count = (!empty($ft_items_array) && is_array($ft_items_array))? count($ft_items_array):0;
				$_items_ran =1;

				// variable values
				$vars = $this->_get_menu_variables();

				// RUN through each item in the passed on array of menu items
				foreach($items_array as $menu_id){

					// Intial values
					$pmv = get_post_custom($menu_id);		
					
					// for featured div
					if( $_ft_item_count!=0 && in_array($menu_id, $ft_items_array) && $ft_dif=='yes'){

						// if we are showing ft items and this is the first ft item
						if(!$_ft_div_start && $_items_ran==1){
							//echo "<div class='fp_features_items' data='5'>";
							$_ft_div_start = true;
						}elseif($_items_ran == $_ft_item_count && $_ft_div_start){
							$_ft_div_end_html = '<div class="clear" ></div><!--clear-->';
							//$_ft_div_end_html .= '</div>';
						}
					}else{
						$_ft_div_end_html='';
					}


					// get HTML 
					if(!empty($type) && $type=='single'){
						echo  $this->intepret_menu_item_html($pmv, $menu_id,'singleITM','',$vars);
					}else{ echo  $this->intepret_menu_item_html($pmv, $menu_id,'','',$vars); }
					

					// closing of featured items div if printed
					echo $_ft_div_end_html;

					$_items_ran++;

				}// end foreach

				// container element - CLOSE
				echo (!empty($type) && $type=='single')? "</div>":null;

				return ob_get_clean();

			}// end if
		}
	
	// return an array of values needed for each menu item via shortcode variables
		function _get_menu_variables(){
			$args__ = $this->shortcode_args;
			extract($args__);


			// min-box height
				$_box_instyles = apply_filters('foodpress_mi_styles',array(''));
				if(!empty($boxhei) && $boxhei!= '0'){
					$_box_instyles[] = 'min-height:'.$boxhei.'px;';
				}
			// individual items
				$featured_item_style = $regular_item_style ='';
				if(!empty($ind_style)){
					if($ind_style=='one'){
						$regular_item_style = 'one';
						$featured_item_style = 'none';
					}if($ind_style=='two'){
						$regular_item_style = 'none';
						$featured_item_style = 'two';
					}
					if($ind_style=='three'){
						$regular_item_style = 'none';
						$featured_item_style = 'three';
					}
					if($ind_style=='four'){
						$regular_item_style = 'none';
						$featured_item_style = 'four';
					}
				}


			// UX
				$__openpop = (empty($ux)  || $ux=='lightbox')? 'fp_popTrig':null;

			// menu icons
				$__active_menu_icons = array();
				for($x=1; $x<= $this->functions->icon_symols_cnt(); $x++){
					$icon_name = !empty($this->fopt1['fp_m_00'.$x])?
						$this->fopt1['fp_m_00'.$x]:null;
					$icon_class = !empty($this->fopt1['fp_m_00'.$x.'i'])?
						$this->fopt1['fp_m_00'.$x.'i']:null;

					if(!empty($icon_name) && !empty($icon_class)){
						$__active_menu_icons['fp_m_00'.$x.'i'] = array($icon_class, $icon_name);
					}
				}

			return array(
				'_box_instyles'=>$_box_instyles,
				'_box_classes'=>apply_filters('foodpress_mi_classnames',array('fp_box','menuItem')),
				'regular_item_style'=>$regular_item_style,
				'featured_item_style'=>$featured_item_style,
				'__openpop'=>$__openpop,
				'__active_menu_icons'=>$__active_menu_icons,

			);

		}

	// RETURN HTML for menu item
		function intepret_menu_item_html($pmv='', $menu_id, $type='', $style='', $vars){

			$pmv = (!empty($pmv))? $pmv : get_post_custom($menu_id);

			// extract the variable values 
			$args__ = $this->shortcode_args;
			extract($args__);
			//print_r($args__);



			// initial variables
				$_box_classes = $vars['_box_classes'];
				$_box_instyles = $vars['_box_instyles'];
				$regular_item_style = $vars['regular_item_style'];
				$featured_item_style = $vars['featured_item_style'];
				$__openpop = $vars['__openpop'];
				$__active_menu_icons = $vars['__active_menu_icons'];

				
				// item featured and def style set
					if(!empty($pmv['_featured']) && $pmv['_featured'][0]=='yes' && $ft_dif=='yes'){
						if($type!='singleITM'){
							$featured_item_style = $ft_style;
							$regular_item_style = ($ft_style=='none')? $style: 'none';
						}
						$_box_classes[]='c_'.$fbox_width; // ft box width
						$_box_classes[] = 'ft_item';

					}else{
						if($type!='singleITM'){
							$regular_item_style = $style;
							$featured_item_style = 'none';
						}
						$_box_classes[]='c_'.$box_width; // regular box with
						$_box_classes[] = 'normal_item';
					}	
				
				// THumbnail
					$img_id =get_post_thumbnail_id($menu_id);
					if($img_id!=''){
						$img_src = wp_get_attachment_image_src($img_id,'medium');
						$img_src_full = wp_get_attachment_image_src($img_id,'large');
					}else{
						if(!empty($this->fopt1['fp_def_thumb_url']))
							$img_src = $img_src_full = array($this->fopt1['fp_def_thumb_url']);
					}
				
				// menu item title
					$_title = get_the_title($menu_id);
					$_excerpt_length = (!empty($args__['wordcount'])? $args__['wordcount']: 20);
					$_excerpt = $this->get_excerpt($_excerpt_length, $menu_id);

				// MENU ICONS
					$_menu_item_icons = '';
					// check if menu icons are active
					if(count($__active_menu_icons)>0){
						$__MI_value = (!empty($pmv['fp_menuicons']) )? $pmv['fp_menuicons'][0]: null;

						if($__MI_value){
							// each activated menu icon
							foreach($__active_menu_icons as $f=>$v){
								$pos = strpos($__MI_value, $f);
								if($pos!== false){
									$_menu_item_icons .= "<i title='{$v[1]}' class='fa {$v[0]}'></i>";
								}
							}							
						}
					}
					$_menu_item_icons = (!empty($_menu_item_icons))? "<p class='fp_icons'>".$_menu_item_icons.'</p>':null;

				// more link
					$_more_link = ( empty($ux)  || $ux =='lightbox' )? "<a class='".$__openpop." fp_inline_btn' data-menuitem_id='{$menu_id}'> ".$this->functions->fp_get_language('Read More', $this->fopt2, $lang)."</a>": null;

				
				// box data variables
					$_box_data = apply_filters('foodpress_mi_box_data_vars', array(
						'ux'=>$ux,
						'menuitem_id'=>$menu_id,					
					), $menu_id);
					$_box_data_ ='';
					foreach($_box_data as $f=>$v){
						$_box_data_ .= "data-".$f."='".$v."' ";
					}
				

				// menu price
					$___price = (!empty($pmv['fp_price']))? apply_filters('foodpress_price_value',$pmv['fp_price'][0], $pmv):null;
					$__price_html = (!empty($pmv['fp_price']) )? "<span class='fp_price'>".$___price."</span>":null;
					$__price_html_2 = (!empty($pmv['fp_price']) )? "<p class='fp_price'>".$___price."</p>":null;

				
				// MENU TOP fields - v 1.1.5
					$foodpress_menutop = (!empty($this->fopt1['fs_menutop']))?$this->fopt1['fs_menutop']: null;
					$foodpress_fields_ = (is_array($foodpress_menutop) )? true:false;

					
					$___mt_subheader = $___mt_additions='';
					// elements
						if($foodpress_fields_ && in_array('subheader', $foodpress_menutop) && !empty($pmv['fp_subheader'])){
						 	$___mt_subheader = '<h5 class="fp_subheader">'.$pmv['fp_subheader'][0].'</h5>';
						}
						if($foodpress_fields_ && in_array('addtext', $foodpress_menutop) && !empty($pmv['fp_addition'])){	
						 	$___mt_additions = '<h5 class="fp_additions">'.$pmv['fp_addition'][0].'</h5>';
						}
			
			ob_start();

				/* <p class='fp_icons'><?php echo $_vegetarian;?></p>*/

				$_box_classes[] = 'fp_popTrig';

				// Featured item styles
				switch ($featured_item_style):
					case 'none': break;	
					// FT: highlighted
					case "one":
						$_box_classes[]='style_ft1';
						$_box_classes[]='bghighl';
						//$_box_classes[]=$__openpop;
						$_class_nm = implode(' ', $_box_classes); // process box class names
						?>
							<div class='<?php echo $_class_nm;?>' <?php echo $_box_data_;?>>
								<div class='fp_inner_box'>
									<h3 class='' title='<?php echo $_title;?>'><?php echo $__price_html;?><?php echo $_title;?></h3><?php echo $___mt_subheader;?>
									<div class='menu_description'><?php echo $_excerpt;?> <?php echo $_more_link;?><?php echo $___mt_additions;?></div>
									<?php do_action('fp_menu_box_end',$menu_id, $pmv, $featured_item_style, $args__);?>
								</div>
							</div>						
						<?php
					break;

					// FT: info over image
					case "two":
						$_box_classes[]='style_ft2';
						//$_box_classes[]=$__openpop;
						$_class_nm = implode(' ', $_box_classes); // process box class names
						?>
							<div class='new_ft2_layout <?php echo $_class_nm;?>' <?php echo $_box_data_;?>>
								<div class='new_ft_img2 '><img class='' src='<?php echo $img_src_full[0];?>'></div>
								<div class='menu_info '>
									<div class='fp_inner_box' >
										<?php echo $__price_html;?>
										<h3 class=''><?php echo $_title;?></h3><?php echo $___mt_subheader;?>
										<p><?php echo $_more_link;?></p><?php echo $___mt_additions;?>
										<?php do_action('fp_menu_box_end',$menu_id, $pmv, $featured_item_style, $args__);?>
									</div>
								</div>							
							</div>					
						<?php
					break;

					// FT: info under image
					case "three":
						$_box_classes[]='style_ft3';
						$_box_classes[]='bghighl';
						//$_box_classes[]=$__openpop;
						$_box_classes[]= (empty($img_src_full))? 'no_img':null;
						$_class_nm = implode(' ', $_box_classes); // process box class names
						?>
							<div class='new_ft3_layout <?php echo $_class_nm;?>' data-type='ftone'<?php echo $_box_data_;?>>
								<div class='new_ft_img3'>
									<img class='' src='<?php echo $img_src_full[0];?>'>
									<?php echo $__price_html;?>
								</div>
								<div class='menu_info '>
									<div class='fp_inner_box' >									
										<h3 class=''><?php echo $_title;?></h3><?php echo $___mt_subheader;?>
										<div class='menu_description'><?php echo $_excerpt;?><?php echo $_more_link;?><?php echo $___mt_additions;?></div>
										<?php do_action('fp_menu_box_end',$menu_id, $pmv, $featured_item_style, $args__);?>
									</div>
								</div>							
							</div>					
						<?php
					break;

					// FT: info next to image
					case "four":
						$_box_classes[]='style_ft4';
						$_box_classes[]='bghighl';
						//$_box_classes[]=$__openpop;
						$_box_classes[]= (empty($img_src_full))? 'no_img':null;
						$_class_nm = implode(' ', $_box_classes); // process box class names
						?>
							<div class='<?php echo $_class_nm;?>' data-type='ftone'<?php echo $_box_data_;?>>
								<div class='menu_image ' style='background-image: url(<?php echo $img_src_full[0];?>)'></div>
								<div class='menu_info '>
									<div class='fp_inner_box' >
										<?php echo $__price_html;?>
										<h3 class=''><?php echo $_title;?></h3><?php echo $___mt_subheader;?>
										<div class='menu_description'><?php echo $_excerpt;?><?php echo $_more_link;?><?php echo $___mt_additions;?></div>
										<?php do_action('fp_menu_box_end',$menu_id, $pmv, $featured_item_style, $args__);?>
									</div>
								</div>							
							</div>					
						<?php
					break;
				endswitch;

				// ----------------------------
				// Regular item styles
				switch ($regular_item_style):	
					case 'none': break;	
					// REG: text based
					case "one":
						$_box_classes[]='style_1';
						//$_box_classes[]=$__openpop;
						$_class_nm = implode(' ', $_box_classes); // process box class names
						$_styles = 'style="'. implode('', $_box_instyles). '"'; // process box class names

						?>
							<div class='<?php echo $_class_nm;?>' <?php echo $_box_data_;?> <?php echo $_styles;?>>
								<div class='fp_inner_box'>
								<?php echo ($box_width!='100')? $__price_html:null;?>
									<h3 class='' title='<?php echo $_title;?>'><?php echo $_title;?><?php echo ($box_width=='100')? $__price_html:null;?></h3><?php echo $___mt_subheader;?>
									<div class='menu_description'><?php echo $_excerpt;?><?php echo $_more_link;?><?php echo $___mt_additions;?><?php echo $_menu_item_icons;?></div>
									<?php do_action('fp_menu_box_end',$menu_id, $pmv, $featured_item_style, $args__);?>
								</div>
							</div>
						
						<?php
					break;

					// REG: thumb and text
					case "two":
						$_box_classes[]='style_2';
						//$_box_classes[]=$__openpop;
						$_box_classes[]= (empty($img_src))? 'no_img':null;
						$_class_nm = implode(' ', $_box_classes); // process box class names
						?>
							<div class='<?php echo $_class_nm;?>' <?php echo $_box_data_;?>>
								<div class='fp_inner_box'>
									<?php if(!empty($img_src)):?>
										<div class='fp_thumbnail new_fp_thumbnail'><img class='new_fp_thumb ' src='<?php echo $img_src[0];?>'></div>
									<?php endif;?>
									
									<div class='menu_info'>
										<?php echo $__price_html;?>
										<h3 class='' title='<?php echo $_title;?>'><?php echo $_title;?></h3><?php echo $___mt_subheader;?>
										<div class='menu_description'><?php echo $_excerpt;?><?php echo $_more_link;?><?php echo $___mt_additions;?><?php echo $_menu_item_icons;?></div>
										<?php do_action('fp_menu_box_end',$menu_id, $pmv, $featured_item_style, $args__);?>
									</div>
									<div class='clear'></div>
								</div>
							</div>
						<?php
					break;

				endswitch;
				
				return ob_get_clean();

		}

	// AJAX - related
	
	// INDIVIDUAL MENU ITEM
		function get_detailed_menu_item_content($item_id, $__type='', $args=''){
			global $foodpress;

			// initias
				$lang = !empty($args['lang'])? $args['lang']:'L1';
				
				$menuItem = get_post($item_id );
				$pmv = get_post_custom($item_id);
				
				$img_id =get_post_thumbnail_id($item_id);
				if($img_id!='')
					$img_src_full = wp_get_attachment_image_src($img_id,'full');
				
				// image styles 
				$in_style =($img_id!='')? 
					"<img src=".$img_src_full[0].">":
					"style='min-height:55px;'";
				$mi_img_url = ($img_id!='')?$img_src_full[0]:null;
			
			// get tax 1 terms for this item
				$this_terms= array();
				$terms = wp_get_object_terms($item_id, 'meal_type');
				if($terms && count($terms)>0){
					foreach($terms as $term){
						$term_name = $this->get_lang('fp_lang_tax_meal_type_'.$term->term_id,$term->name , $lang);
						$this_terms[] =$term_name;
					}
				}

			$this_terms = '<span>'.implode('</span><span>', $this_terms).'</span>';
			
			// menu card HTML
			require_once(FP_PATH.'/includes/fp_menu-card.php');


				// image
				$__menucard['header'] =array(
					'imgurl'=>$mi_img_url,
					'instyle'=>$in_style,
					'price'=>(!empty($pmv['fp_price']))? $pmv['fp_price'][0]:null,
					'title'=>$menuItem->post_title,
					'subtitle'=> (!empty($pmv['fp_subheader'])? $pmv['fp_subheader'][0]:false)
				);

				// description
				$___description = (!empty($pmv['fp_description']))? $pmv['fp_description'][0]:null;


				$__menucard['details'] =array(
					'terms'=>$this_terms,
					'title'=>$menuItem->post_title,
					'description'=>$___description,
					'additionaltext'=> (!empty($pmv['fp_addition'])? $pmv['fp_addition'][0]:false)
				);

				// ingredients
					if(!empty($pmv['fp_ingredients'])):

						$_txt_ingredients = $this->functions->fp_get_language('Ingredients', $this->fopt2, $lang);
						$__menucard['ingredients'] =array(
							'title'=>$_txt_ingredients,
							'content'=>(!empty($pmv['fp_ingredients']))? $pmv['fp_ingredients'][0]:null,
						);
					endif;

				// nutritions
					$count =1;
					$left = $right ='';
					
					$nutrition_items = $this->functions->get_nutrition_items();

					foreach($nutrition_items as $val){

						$slug = $val['slug'];
						if(!empty($pmv[$slug])){
							$data = "<b>".$this->functions->fp_get_language($slug, $this->fopt2, $lang)."</b> ".$pmv[$slug][0]."<br/>";
							if($count%2==0){	$right .= $data;	}else{	$left .= $data;	}
							$count++;
						}
					}

					if($count>1):
						$_txt_nutritions = $this->functions->fp_get_language('Nutritions', $this->fopt2, $lang);
						$__menucard['nutritions'] =array(
							'title'=>$_txt_nutritions,
							'left'=>$left,
							'right'=>$right,
						);
					endif;

				// custom fields
					for($x =1; $x<= $foodpress->functions->custom_fields_cnt(); $x++){
						if(!empty($this->fopt1['fp_af_'.$x]) && $this->fopt1['fp_af_'.$x]=='yes'  && !empty($pmv['fp_ec_f'.$x]) ){

							// field name
							$_fieldName = (!empty($this->fopt1['fp_ec_f'.$x]))? stripslashes($this->fopt1['fp_ec_f'.$x]) : 'Custom field '.$x;

							// icon name
							$_iconName = ( !empty($this->fopt1['fp_ec_f'.$x.'a']) )? $this->fopt1['fp_ec_f'.$x.'a']: 'fa-asterisk';

							if(!empty($pmv['fp_ec_f'.$x]) && $pmv['fp_ec_f'.$x][0]!='--'){
								$__menucard['customfield'.$x] =array(
									'x'=>$x,
									'title'=>foodpress_get_custom_language($this->fopt2, 'fp_ec_f'.$x, $_fieldName, $lang),
									'iconname'=>$_iconName,
									'content'=> (!empty($pmv['fp_ec_f'.$x]) )? $pmv['fp_ec_f'.$x][0]:null,
									'content_type'=>$this->fopt1['fp_ec_f'.$x.'b']
								);
							}
						}// endif
					}

				// spice level
					if(!empty($pmv['fp_spicy']) && $pmv['fp_spicy'][0]!='0'):
						
						$spicelevel = (int)$pmv['fp_spicy'][0];
						$__menucard['spicelevel'] =array(
							'level'=>$spicelevel,						
						);

					endif;

				// construction menu card
				if(!empty($__menucard)  && count($__menucard)>0){

					// filter hook for menu card content array - 1.3
					$__menucard = apply_filters('foodpress_menucard_array', $__menucard, $this->fopt1, $this->fopt2, $item_id);

					// if an order is set for re-ordering
						if(!empty($this->fopt1['fpCard_order']))
							$__menucard = $this->functions->menucard_sort($__menucard, $this->fopt1 );

					ob_start();
					echo "<div class='fp_menucard_content ". (($__type=='page')? 'fp_page':'fp_lightbox') ."'>";

					echo foodpress_menucard_print($__menucard, $this->fopt1, $this->fopt2,  $pmv , $args);

					// pluggable hook
					do_action('foodpress_menu_itemcard_additions', $item_id, $pmv, $___description, $menuItem->post_title, $mi_img_url, $args);

					echo "</div>";

					return  ob_get_clean();

				}else{
					return " ";
				}

		}

	// POPUP FUNCTIONS
	
		// MENU popup HTML framework
		function get_popup_info_html(){
			ob_start();
			?>
			<div id='fp_popup' class='fp_popup_section fp_popup_html' style='display:none'>
				<div class='fp_popup'>
					<a id='fp_close'><i class="fa fa-times"></i></a>
					<div class='fp_pop_inner'>
						
					</div>				
				</div>
			</div>
			<div id='fp_popup_bg' class='fp_popup_html' style='display:none'>
				<div class="spinner">
				  <div class="bounce1"></div>
				  <div class="bounce2"></div>
				  <div class="bounce3"></div>
				</div>
			</div>
			
			<?php		
			return ob_get_clean();
		}
			
	// FEATURE IMAGE
		function get_image($post_id=''){
			global $post;
			
			$p_id = (!empty($post_id))? $post_id: $post->ID;

			$img_id =get_post_thumbnail_id($p_id);
			
			if(!empty($img_id)){
				$img_src = wp_get_attachment_image_src($img_id,'thumbnail');
				if($img_src){
					return "<img class='fp_thumb_postedit' src='{$img_src[0]}'/>";
				}
			}else{
				$url = FP_URL.'/assets/images/placeholder.png';
				return "<img class='fp_thumb_postedit def' src='{$url}' />";
			}
			
		}
	
	// SUPPORTING
		// check to make sure there are content passing through
			function validate_content($content){
				return (!empty($content))?
					$content:
					"<p>".$this->functions->fp_get_language('No Menu Items', $this->fopt2)."</p>";
			}

		// return FP transated language text
			function get_lang($var, $default, $lang=''){
				$lang = !empty($lang)? $lang: (!empty($this->shortcode_args['lang'])? $this->shortcode_args['lang']:'L1');
				return foodpress_get_custom_language($this->fopt2, $var, $default, $lang);
			}		

		// excerpt
			function get_excerpt($limit, $post_id=''){
				global $post;
				
				$p_id = (!empty($post_id))? $post_id: $post->ID;

				$description = get_post_meta($p_id, 'fp_description', true);
				
				if(empty($description) )
					return false;	
				
				$excerpt = explode(' ', $description, $limit);
				
				if (count($excerpt)>=$limit) {
					array_pop($excerpt);
					$excerpt = implode(" ",$excerpt).' ...';
				} else {
					$excerpt = implode(" ",$excerpt);
				} 
				$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
				
				return $excerpt;
				
				
			}
	
}