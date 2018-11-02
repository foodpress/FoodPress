<?php
/**
 * Foodpress software Product class
 * @version   1.4
 */
class FP_product{

	// check purchase code correct format
		public function purchase_key_format($key, $type='main'){
			if(!strpos($key, '-'))
				return false;

			$str = explode('-', $key);
			return (strlen($str[1])==4 && strlen($str[2])==4 && strlen($str[3])==4 )? true: false;
		}

	// Verify license key
		public function verify_product_license($args){

			if($args['slug']=='foodpress'){
				$api_key = 'vzfrb2suklzlq3r339k5t0r3ktemw7zi';
				$api_username ='ashanjay';

				$url = 'http://marketplace.envato.com/api/edge/'.$api_username.'/'.$api_key.'/verify-purchase:'.$args['key'].'.json';
				return $url;
			}else{
				// for addons

				$instance = !empty($args['instance'])?$args['instance']:1;
				$url='http://www.myfoodpress.com/woocommerce/?wc-api=software-api&request=activation&email='.$args['email'].'&licence_key='.$args['key'].'&product_id='.$args['product_id'].'&instance='.$instance;

				//echo $url;
				$request = wp_remote_get($url);

				if (!is_wp_error($request) && $request['response']['code']===200) {
					$result = (!empty($request['body']))? json_decode($request['body']): $request;
					//update_option('test1', json_decode($result));
					return $result;
				}else{
					return $url;
				}
			}
		}

	// activation of foodpress licenses
		function is_activated($slug){
			$fp_licenses = get_option('_fp_licenses');

			if(!empty($fp_licenses[$slug]) && $fp_licenses[$slug]['status']== 'active' && !empty($fp_licenses[$slug]['key']) ){
				return true;
			}else{
				return false;
			}
		}
	// get foodpress license data
		function get_foodpress_license_data(){
			$fp_licenses = get_option('_fp_licenses');
			//var_dump($fp_licenses);
			// running for the first time
			if(empty($fp_licenses)){
				$lice = array(
					'foodpress'=>array(
						'name'=>'foodpress',
						'current_version'=>$foodpress->version,
						'type'=>'plugin',
						'status'=>'inactive',
						'key'=>'',
					));
				update_option('_fp_licenses', $lice);
			}
			return get_option('_fp_licenses');
		}
	// deactivate license
		function deactivate($slug){
			$product_data = get_option('_fp_licenses');
			if(!empty($product_data[$slug])){

				$new_data = $product_data;
				//unset($new_data[$slug]['key']);
				$new_data[$slug]['status']='inactive';

				update_option('_fp_licenses',$new_data);
				return true;
			}else{return false;}
		}

	// save to wp options
		public function save_license_key($slug, $key){
			$licenses =get_option('_fp_licenses');

			if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$slug]) && !empty($key) ){

				$newarray = array();
				$this_license = $licenses[$slug];

				foreach($this_license as $field=>$val){
					if($field=='key')	$val=$key;
					if($field =='status')	$val='active';
					$newarray[$field]=$val;
				}

				$new_ar[$slug] = $newarray;
				$merged=array_merge($licenses,$new_ar);

				update_option('_fp_licenses',$merged);

				return $newarray;
			}else{
				return false;
			}

		}

	// update any given fiels
		public function update_field($slug, $field, $value){
			$product_data = get_option('_fp_licenses');

			if(!empty($product_data[$slug])){
				$new_data = $product_data;
				$new_data[$slug][$field]=$value;
				update_option('_fp_licenses',$new_data);
				return true;
			}else{return false;}
		}

	// error code decipher
		public function error_code_($code=''){
			$code = (!empty($code))? $code: $this->error_code;
			$array = array(
				"00"=>'',
				'01'=>"No data returned from envato API",
				"02"=>'Your license is not a valid one!, please check and try again.',
				"03"=>'envato verification API is busy at moment, please try later.',
				"04"=>'This license is already registered with a different site.',
				"05"=>'Your foodpress version is not updated',
				"06"=>'FoodPress license key not passed correct!',
				"07"=>'Could not deactivate FoodPress license from remote server',
				'08'=>'http request failed, connection time out. Please contact your web provider!',
				'09'=>'wp_remote_post() method did not work to verify licenses, trying a backup method now..',


				'10'=>'License key is not in valid format, please try again.',
				'11'=>'Could not verify. Server might be busy, please try again LATER!',
				'12'=>'Activated successfully and synced w/ FoodPress server!',
				'13'=>'Remote validation did not work, but we have activated your copy within your site!',

				'101'=>'Invalid license key!',
				'102'=>'Addon has been deactivated!',
				'103'=>'You have exceeded maxium number of activations!',
				'104'=>'Invalid instance ID!',
				'105'=>'Invalid security key!',
				'100'=>'Invalid request!',
			);
			return $array[$code];
		}
}
