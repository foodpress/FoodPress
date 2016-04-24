<?php
/**
 * FoodPress Widget
 *
 * @author 		AJDE
 * @category 	Widget
 * @package 	FoodPress/Classes
 * @version     1.0
 */
class foodpress_Widget extends WP_Widget{

	private $item_count_max = 5;
	private $style_selection_array = array(
		'one'=>'Text Only',
		'two'=>'Info over Image',
		'three'=>'Info under Image',
		'four'=>'Info next to Image',
	);
	
	function foodpress_Widget(){
		$widget_ops = array('classname' => 'fp_Widget', 
			'description' => 'Add FoodPress Menu widget.' );
		parent::__construct('foodpress_Widget', 'FoodPress Menu', $widget_ops);
	}
	
	function widget_default(){
		global $foodpress;

		return $defaults = array(
			'fpw_shortcode'=>'',
			'fpw_item_001m'=>'','fpw_item_001s'=>'',
			'fpw_item_002m'=>'','fpw_item_002s'=>'',
			'fpw_item_003m'=>'','fpw_item_003s'=>'',
			'fpw_item_004m'=>'','fpw_item_004s'=>'',
			'fpw_item_005m'=>'','fpw_item_005s'=>'',
			'fpw_type'=>'',
			'fpw_title'=>''
		);
	}
	function widget_values($instance){		
		$defaults = $this->widget_default();		
		return wp_parse_args( (array) $instance, $defaults);
	}
	
	function process_values($inst){
		$defaults = $this->widget_default();
		
		$send_values = array();
		
		foreach($defaults as $f=>$v){			
			$send_values[$f] = (!empty($inst[$f])) ?$inst[$f] : $v;			
		}
		
		return $send_values;
	}
	
	function form($instance) {
		global $foodpress;
				
		$instance = $this->widget_values($instance); 
		extract($instance);
		// HTML
		
		?>
		<div id='foodpress_widget_settings' class='foodpress_widget_settings'>

			<?php
				// selection value
				$__fpw_type = (!empty($fpw_type))? esc_attr($fpw_type):null;
			?>
			<div class='foodpress_row_item'>					
				<input id="<?php echo $this->get_field_id('fpw_title'); ?>" name="<?php echo $this->get_field_name('fpw_title'); ?>" type="text" 
				value="<?php echo esc_attr($fpw_title); ?>" placeholder='Widget Title' title='Widget Title'/>					
			</div>
			<div class='foodpress_widget_top'><p></p></div>

			<div class='foodpressW_inner'>
				<input class='fpw_selection_type' type="hidden" id='<?php echo $this->get_field_id('fpw_type'); ?>' name='<?php echo $this->get_field_name('fpw_type'); ?>' value="<?php echo $__fpw_type; ?>"/>
				<div class="fpw_step1 fpw_steps">					
					<h3 class='fpw_step_btns <?php echo ($__fpw_type=='fp_wi_001')? 'selected':'';?>' data-option='fp_wi_001'>Add menu items<?php $foodpress->throw_guide('With this option you can add one or more menu items to this widget and select its styles.','L');?></h3>
					<h3 class='fpw_step_btns <?php echo ($__fpw_type=='fp_wi_002')? 'selected':'';?>' data-option='fp_wi_002'>Add a shortcode<?php $foodpress->throw_guide('Use this option to use foodPress shortcode directly in this widget.','L');?></h3>
				</div>
				<div class="fpw_step2 fpw_steps">
					<div id="fp_wi_002" class='fpwi' style='display:<?php echo ($__fpw_type=='fp_wi_002')? 'block':'none';?>'>						
						<textarea id='<?php echo $this->get_field_id('fpw_shortcode'); ?>' name='<?php echo $this->get_field_name('fpw_shortcode'); ?>' ><?php echo esc_attr($fpw_shortcode); ?></textarea>
						<legend><i>Type the shortcode to use for the widget. eg [add_foodpress]</i></legend>
					</div>


					<div id="fp_wi_001" class='fpwi' style='display:<?php echo ($__fpw_type=='fp_wi_001')? 'block':'none';?>'>						
						<?php

							$menuitems = get_posts(array('post_type'=>'menu', 'posts_per_page'=>-1));
							if(!empty($menuitems) && count($menuitems)>0){
								foreach($menuitems as $mm){
									$__menuitems[$mm->ID]= $mm->post_title;
								}
							}else{ $__menuitems=null;}

							$__style_options = "<div class='fpPop_options' style='display:none'>
									<p class='fpPop_option selected' data-value='one' data-name='Text Only'><img src='".FP_URL."/assets/images/backend/ft_i_0.jpg'/>Text only</p>			
									<p class='fpPop_option' data-value='two' data-name='Info over Image'><img src='".FP_URL."/assets/images/backend/ft_i_2.jpg'/>Info over<br/>Image</p>
									<p class='fpPop_option' data-value='three' data-name='Info under Image'><img src='".FP_URL."/assets/images/backend/ft_i_3.jpg'/>Info under<br/>Image</p>
									<p class='fpPop_option'data-value='four' data-name='Info next to Image'><img src='".FP_URL."/assets/images/backend/info_thumb_opp.jpg'/>Info next to<br/>Image</p>
									<div class='clear'></div>
								</div>";

							?><div class='fpwi_boxes' data-numitems=''><?php

							$count=0;
							// FOR Statement
							for($x=1; $x<($this->item_count_max +1); $x++){

								$value_m = $instance['fpw_item_00'.$x.'m'];
								$value_s = $instance['fpw_item_00'.$x.'s'];
								$value_s = (!empty($value_s))? $value_s: 'one';


								$__style_display = ($x==1 || (!empty($value_m) && $value_m!='none') )? 'display:block':'display:none';
								if(!empty($value_m) && $value_m!='none'){ $count++;}
								//echo $value_m;
							echo"							
								<div class='fpw_item_box fpwb{$x}' data-count=' {$x}' style='". $__style_display."'>
									<p class='fpw_num'>{$x}</p>							
									<h4>Menu Item: <select id='".$this->get_field_id('fpw_item_00'.$x.'m')."' name='".$this->get_field_name('fpw_item_00'.$x.'m')."'>
										<option value='none'>None</option>";
										foreach($__menuitems as $mm=>$mv){
											echo "<option value='{$mm}'".( ($value_m==$mm)?"selected='selected'":null ).">{$mv}</option>";
										}
										echo "</select></h4>										
									<input class='fpw_item_style' type='hidden' id='". $this->get_field_id('fpw_item_00'.$x.'s')."' name='".$this->get_field_name('fpw_item_00'.$x.'s')."' value='".$value_s."'/>
									<h4 class='fpw_style_selection'>Style <span class='fpw_style_btn'>". $this->style_selection_array[$value_s]."</span>". $__style_options."</h4>
								</div>";								

								}
							?>
							</div>
						<?php if($count<($this->item_count_max +1)):?>
						<p data-numitems='<?php echo ($count==0)? 1: $count;?>'><a class='fpw_add_item btn_prime fp_admin_btn'>+</a><?php $foodpress->throw_guide('Click here to add another menu item and set styles. To remove an item, select Menu item to None.','L');?></p>
						<?php endif;?>
					</div>
				</div>
			</div>

		</div>
		<?php
	}
	
	// update the new values for widget
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		foreach($this->widget_default() as $defv=>$def){
			$instance[$defv] = strip_tags($new_instance[$defv]);
		}
		
		return $instance;
	}

	
	/**
	 * The actuval widget
	 */
	public function widget($args, $instance) {
		global $foodpress;
				
		// extract widget specific variables
		extract($args, EXTR_SKIP);			
		
		$values = $this->process_values($instance);		
		extract($values);
		

		/* WIDGET	*/				
		if(has_action('foodpress_before_widget')){
			do_action('foodpress_before_widget');
		}else{
			echo $before_widget;
		}
			
			
		// widget title
		if(!empty($instance['fpw_title']) ){
			echo "<h3 class='widget-title'>".$instance['fpw_title']."</h3>";
		}

		echo "<div class='foodpress_widget'>";
		
		// shortcode
		if(!empty($fpw_type) && $fpw_type=='fp_wi_002'){
			echo do_shortcode($fpw_shortcode);			
		}
		


		// single items
		if(!empty($fpw_type) && $fpw_type=='fp_wi_001'){

			add_filter('foodpress_default_args', array($this,'shortcode_defaults'), 10, 1);
			$foodpress->foodpress_menus->frontend_default_styles();
			
			for($x=1; $x<($this->item_count_max +1); $x++){

				$value_m = $values['fpw_item_00'.$x.'m'];
				$value_s = $values['fpw_item_00'.$x.'s'];
				$value_s = (!empty($value_s))? $value_s: 'one';

				if(!empty($value_m) && $value_m!='none'){
					
					$atts = array('ind_style'=>$value_s);
					$supported_defaults = $foodpress->foodpress_menus->get_acceptable_shortcode_atts();
					$args = shortcode_atts( $supported_defaults, $atts ) ;

					echo $foodpress->foodpress_menus->get_individual_item_content(
						array($value_m),$args , 'single');
					
				}

			}
		}

		echo "</div>";
		
		
		if(has_action('foodpress_after_widget')){
			do_action('foodpress_after_widget');
		}else{
			echo $after_widget;
		}
		
	}

	// shortcode defaults for menu single item
		function shortcode_defaults($arr){
			return array_merge($arr, array(
				'item_id'=>'all',
				'ind_style'=>'one',
			));
		}


}
