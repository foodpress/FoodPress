<?php
/** 
 * Foodpress helper calss
 * @version 0.1
 */

class fp_helper{

	// tooltips
		public function tooltips($content, $position='', $echo=true){
			$L = (!empty($position) && $position=='L')? ' L':null;
			
			$content = "<span class='fpGuideCall{$L}'>?<em>{$content}</em></span>";
			
			if(!$echo)
				return $content;			
			
			echo $content;
		}
		function echo_tooltips($content, $position=''){
			$this->tooltips($content, $position='',true);
		}

	// emailing
		public function get_email_part($part){
			global $foodpress;
			$file_name = 'email_'.$part.'.php';
			$paths = array(
				0=> TEMPLATEPATH.'/'.$foodpress->template_url.'templates/email/',
				1=> FP_PATH.'/templates/email/',
			);
			foreach($paths as $path){
				if(file_exists($path.$file_name) ){
					$template = $path.$file_name;
					break;
				}
			}
			ob_start();
			include($template);
			return ob_get_clean();
		}

		// body part of the email template loading
			public function get_email_body($part, $def_location, $args){
				global $foodpress;
				$file_name = $part.'.php';
				$paths = array(
					0=> TEMPLATEPATH.'/'.$foodpress->template_url.'templates/email/',
					1=> $def_location,
				);

				foreach($paths as $path){
					if(file_exists($path.$file_name) ){
						$template = $path.$file_name;
						break;
					}					//echo($path.$file_name.'<br/>');
				}

				ob_start();
				include($template);
				return ob_get_clean();
			}

		// Send email
		public function send_email($args){
			$defaults = array(
				'html'=>'yes',
				'preview'=>'no',
				'to'=>'',
				'from'=>'',
				'from_name'=>'','from_email'=>'',
				'header'=>'',
				'subject'=>'',
				'message'=>'',
				'type'=>'',
			);
			$args = array_merge($defaults, $args);

			if($args['html']=='yes'){
				add_filter( 'wp_mail_content_type',array($this,'set_html_content_type'));
			}

			if(!empty($args['header'])){
				$headers = $args['header'];
			}else{
				$headers = array();
				if(empty($args['from_email'])){
					$headers[] = 'From: '.$args['from'];
				}else{
					$headers[] = 'From: '.(!empty($args['from_name'])? $args['from_name']:'') .' <'.
						$args['from_email'] . '>';
				}
			}	

			$return = '';		

			if($args['preview']=='yes'){
				$return = array(
					'to'=>$args['to'],
					'subject'=>$args['subject'],
					'message'=>$args['message'],
					'headers'=>$headers
				);
			// bcc version of things
			}else if(!empty($args['type']) && $args['type']=='bcc' ){
				$bcc = (is_array($args['to']))? implode(',', $args['to']): $args['to'];
				$headers[] = "Bcc: ".$bcc;
				$return = wp_mail($args['from'], $args['subject'], $args['message'], $headers);	
			}else{
				$return = wp_mail($args['to'], $args['subject'], $args['message'], $headers);
			}

			if($args['html']=='yes'){
				remove_filter( 'wp_mail_content_type', array($this,'set_html_content_type') );
			} 

			return $return;
		}
		function set_html_content_type() {
			return 'text/html';
		}
		function set_charset_type() {
			return 'utf8';
		}

		// GET email body with eventon header and footer for email included
		public function get_email_body_content($message=''){
			ob_start();
			echo $this->get_email_part('header');
			echo !empty($message)? $message:'';
			echo $this->get_email_part('footer');
			return ob_get_clean();
		}


}