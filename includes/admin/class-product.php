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

	// Get foodpress product license id by slug
		function getProductLicenseInfoById($slug) {
			switch($slug) {
				case 'foodpress':
					$return['id'] = 'FPMAIN';
					$return['name'] = 'FoodPress';
					break;
				case 'foodpress-onlineorder':
					$return['id'] = 'FPOO';
					$return['name'] = 'FoodPress Online Ordering';
					break;
				case 'foodpress-single-menu':
					$return['id'] = 'FPSMI';
					$return['name'] = 'FoodPress Single Menu Item';
					break;
				case 'foodpress-importexport':
					$return['id'] = 'FPIE';
					$return['name'] = 'FoodPress Import Export';
					break;
				default:
					$return['id'] = 'FPMAIN';
					$return['name'] = 'FoodPress';
			}
			return $return;
		}


	// Verify license key
		public function verify_product_license($args) {

			// Domains
			$domain = 'http://myfoodpress.com/';
			$wc_domain = 'woocommerce/';

			// $result vars
			$result['result'] = NULL;
			$result['envatoExists'] = false;
			$result['envatoVerified'] = false;
			$result['envatoExpired'] = false;
			$result['fpVerified'] = false;
			$result['fpExpired'] = false;
			//var_dump($args['slug']);
			// For foodpress
			if($args['slug'] == 'foodpress') {

				// Envato information restricted to only checking license
				$envato = [
				    'key' => 'Ce6hA2xb1JkdC0jaMbzymBE2z9BIu7KN',
				    'username' => 'ashanjay'
				];

				//$url = 'http://marketplace.envato.com/api/edge/'.$envato['username'].'/'.$envato['key'].'/verify-purchase:'.$args['key'].'.json';
				$url = 'https://api.envato.com/v3/market/author/sale?code=' . $args['key'];
				$headers = [
		            'Content-Type' => 'application/json',
		            'Authorization' => 'Bearer ' . $envato['key'],
		            'Cache-Control' => 'no-cache',
					'User-Agent' => 'request'
		        ];

				$envatoRequest = wp_remote_get($url, ['headers' => $headers]);
				if(!is_wp_error($envatoRequest)) {

					// Check if license exists or not
					if($envatoRequest['response']['code'] == 200 && $envatoRequest['response']['message'] == "OK") {
						$result['envatoExists'] = true;
						//$result['result'] = (!empty($envatoRequest['body'])) ? json_decode($envatoRequest['body']) : $envatoRequest;

						$soldAt = strtotime($result['result']->sold_at);
						$expireDate = date('Y-m-d', strtotime('+1 year', $soldAt));
						$result['envatoExpired'] = (strtotime(date('Y-m-d')) > strtotime($expireDate)) ? true : false;

						// If not expired
						if(!$result['envatoExpired']) {
							$result['envatoVerified'] = true;
							$result['result'] = 'Envato license verified.';
						} else {
							$result['result'] = 'Envato License Expired on ' . $expireDate . '. We are no longer listed on the Envato market. Please purchase a new license from <a href="http://myfoodpress.com">http://myfoodpress.com</a>.';
							$result['error_code'] = '106';
						}
						//return $result;

					} else {
						$result['envatoVerified'] = false;
						$result['result'] = 'data';
						//update_option('test1', json_decode($result));
					}
				}

				// If envato license does not exist, check myfoodpress software license
				if(!$result['envatoExists']) {
					$result = $this->processLicenseVerification($result, $args);
				}
				//var_dump($result);
				return $result;

			// For foodpress addons
			} else {

				$result = $this->processLicenseVerification($result, $args);

				return $result;
			}
		}

		private function processLicenseVerification($result, $args) {

			// Domains
			$domain = 'http://myfoodpress.com/';
			$wc_domain = 'woocommerce/';

			$instance = !empty($args['instance']) ? $args['instance'] : 1;

			// Check if email or key exists and is activated before trying to activate.
			// This is needed if the user has to incidentally reroll a new database
			// Running a check first ensures the user can reactivate FoodPress once having already registered an activation
			$fpCheckUrl = $domain . $wc_domain . '?wc-api=software-api&request=check&email='.$args['email'].'&license_key='.$args['key'].'&product_id='.$args['product_id'].'&instance='.$instance;
			$fpCheckRequest = wp_remote_get($fpCheckUrl);
			if (!is_wp_error($fpCheckRequest)) {
				//var_dump($fpCheckRequest);
				if($fpCheckRequest['response']['code'] == 200) {
					//var_dump($fpCheckRequest);
					$body = (!empty($fpCheckRequest['body'])) ? json_decode($fpCheckRequest['body']) : $fpCheckRequest;

						// Temporarily bypassing the api license software checking the order for "completed" orders only.
						// If we don't do this, the customer has to wait for their card to process
						// There's got to be a better way ... maybe activate with a temporary notice scheduled to check back?
						if(isset($body->{'additional info'}) && $body->{'additional info'} === "The purchase matching this product is not complete") {
							$body->success = true;
						}
						// Already activated
						if($body->success) {
							$result['fpVerified'] = true;
							$result['result'] = 'Your ' . $this->getProductLicenseInfoById($args['slug'])['name'] . ' license has been successfully checked for validation and is active.';

						// Not activated, so check if email and key exists
						} else {
							//var_dump($body);

							$fpLicenseUrl = $domain . $wc_domain . '?wc-api=software-api&request=activation&email='.$args['email'].'&license_key='.$args['key'].'&product_id='.$args['product_id'].'&instance='.$instance;
							// Request myfoodpress software-api
							$fpRequest = wp_remote_get($fpLicenseUrl);
							if (!is_wp_error($fpRequest)) {
								if($fpRequest['response']['code'] == 200) {
									$body = (!empty($fpRequest['body'])) ? json_decode($fpRequest['body']) : $fpRequest;

									// Temporarily bypassing the api license software checking the order for "completed" orders only.
									// If we don't do this, the customer has to wait for their card to process
									// There's got to be a better way ... maybe activate with a temporary notice scheduled to check back?
									if(isset($body->{'additional info'}) && $body->{'additional info'} === "The purchase matching this product is not complete") {
										//var_dump($body);
										$body->activated = true;
									}
									// If error in a complete/200 request
									if(isset($body->error) && !empty($body->error) && !$body->activated) {
										$result['error_code'] = $body->code;
										$result['result'] = $body->error;
									} else {
										if($body->activated) {
											$result['fpVerified'] = true;
										}
										$result['result'] = 'Your ' . $this->getProductLicenseInfoById($args['slug'])['name'] . ' license is valid and has been activated.';
									}

								} else {
									$result['fpVerified'] = false;
									$result['result'] = (!empty($fpRequest['body'])) ? json_decode($fpRequest['body']) : $fpRequest;
								}
							}

						}
					//}
				}
			}

			return $result;
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
		function get_foodpress_license_data() {
			$fp_licenses = get_option('_fp_licenses');
			global $foodpress;
			//var_dump($fp_licenses);
			// running for the first time
			if(!isset($fp_licenses['foodpress'])) {
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
		function deactivate($slug) {
			$product_data = get_option('_fp_licenses');
			if(!empty($product_data[$slug])){

				$new_data = $product_data;
				//unset($new_data[$slug]['key']);
				$new_data[$slug]['status'] = 'inactive';

				update_option('_fp_licenses', $new_data);
				return true;
			} else {
				return false;
			}
		}

	// save to wp options
		public function save_license_key($slug, $key) {
			//var_dump($slug);
			$licenses = get_option('_fp_licenses');

			if(!isset($licenses[$slug])) {
				$lice = array(
					$slug => array(
						'name'=>$slug,
						'type'=>'plugin',
						'status'=>'active',
						'key'=>$key,
					));

				$merged = array_merge($licenses, $lice);

				update_option('_fp_licenses', $merged);
			}

			if(!empty($licenses) && count($licenses) > 0 && !empty($licenses[$slug]) && !empty($key) ) {

				$newarray = array();
				$this_license = $licenses[$slug];

				foreach($this_license as $field => $val) {
					if($field == 'key')	$val = $key;
					if($field == 'status')	$val = 'active';
					$newarray[$field] = $val;
				}

				$new_ar[$slug] = $newarray;
				$merged = array_merge($licenses, $new_ar);

				update_option('_fp_licenses', $merged);

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
		public function error_code_($code = '') {
			$code = (!empty($code)) ? $code: $this->error_code;
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
				'11'=>'Could not verify. Server might be busy, please try again later or contact us.',
				'12'=>'Activated successfully and synced w/ FoodPress server!',
				'13'=>'Remote validation did not work, but we have activated your copy within your site!',

				'100'=>'Invalid request!',
				'101'=>'Invalid license key for this product. Please verify the license key exists in your <a style="color:cyan" href="http://myfoodpress.com/my-account">account</a>.',
				'102'=>'Software has been deactivated!',
				'103'=>'You have exceeded maxium number of activations!',
				'104'=>'Invalid instance ID!',
				'105'=>'Invalid security key!',

				// Expiration messages
				'106'=>'Your Envato CodeCanyon license has expired. Please purchase a new license from our website.'
			);
			return $array[$code];
		}
}
