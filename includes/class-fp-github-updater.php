<?php
/**
 * FoodPress GitHub Automatic Updater
 *
 * @author 		Ian Shephard
 * @category 	Class
 * @package 	FoodPress/Classes
 * @version     1.0
 */

class foodpress_github_updater {

    private $slug; // plugin slug
    private $pluginData; // plugin data
    private $repo; // GitHub repo name
    private $pluginFile; // __FILE__ of our plugin
    private $githubAPIResult; // holds data from GitHub
    private $accessToken; // GitHub private repo token
    private $pluginActivated; // is plugin activated
    private $changeLog; // store for all git change logs
    private $url; // url for github repo
    private $current_version;
    private $test;

    function __construct($pluginFile, $gitHubProjectName, $accessToken = '', $version) {
		$this->pluginFile = $pluginFile;
		$this->accessToken = $accessToken;
        $this->current_version = $version;
        add_filter("pre_set_site_transient_update_plugins", array($this, "setTransitent"));
        add_filter("plugins_api", array($this, "setPluginInfo"), 10, 3);
        add_filter("upgrader_pre_install", array($this, "preInstall"));
        add_filter("upgrader_post_install", array($this, "postInstall"), 10, 3);

        $this->repo = $gitHubProjectName;
        $this->url = "https://api.github.com/repos/{$this->repo}/releases";

    }

    // Get information regarding our plugin from WordPress
    private function initPluginData() {
		$this->slug = plugin_basename($this->pluginFile);
        //$this->slug = 'foodpress';
		$this->pluginData = get_plugin_data($this->pluginFile);
        //var_dump($this->pluginData);
        // update to current version
        //global $foodpress;
		$this->save_new_license_field_values('current_version', $this->current_version, $this->slug);
    }

    // Get information regarding our plugin from GitHub
    private function getRepoReleaseInfo() {
		// Only do this once as WP runs this twice by default
		if (!empty($this->githubAPIResult)) {
		    return;
		}

		$this->getInfoFromGitHub();
    }

    public function getInfoFromGitHub($latestOnly = false){
		// We need the access token for private repos
		if (!empty($this->accessToken)) {
		    $url = add_query_arg(array("access_token" => $this->accessToken), $this->url);
		}

		// Get the results
		$this->githubAPIResult = wp_remote_retrieve_body(wp_remote_get($url));
        //var_dump($this->githubAPIResult);
		if (!empty($this->githubAPIResult)) {
		    $this->githubAPIResult = @json_decode($this->githubAPIResult);
		}
        //$this->initPluginData();
        //var_dump($this->slug);

		// Use only the latest non-release and append older releases to the change log
		$latest_result = null;
		$this->changeLog = "# Latest Update";
		if (is_array($this->githubAPIResult)) {
			foreach ($this->githubAPIResult as $result) {
				if ($result->draft == false) {
					if ($latest_result == null) {
						$this->changeLog .= " - " . $result->tag_name . " - " . $result->name . "\n" . $result->body . "\n";
						$latest_result = $result;
						if ($latestOnly) {
							return $result;
						}
						$this->changeLog .= "# Previous Updates\n";
					} else {
						$this->changeLog .= "## " . $result->tag_name . " - " . $result->name . "\n" . $result->body . "\n";
					}
				}
			}
		}
		if ($latest_result != null) { $this->githubAPIResult = $latest_result; }

		// Return the info incase it is being called outside of the class
		return $this->githubAPIResult;
    }

    //	Update field values to licenses
		public function save_new_update_details($remote_version, $has_new_update, $current_version){
			$licenses = get_option('_fp_licenses');

			if(!empty($licenses) && count($licenses) > 0 && !empty($licenses[$this->slug]) ){

				$newarray = array();

				$this_license = $licenses[$this->slug];

				foreach($this_license as $field=>$val) {
					if($field != 'remote_version' || $field != 'has_new_update') {
						$newarray[$field] = $val;
					}
				}
				$newarray['remote_version'] = $remote_version;
				$newarray['has_new_update'] = $has_new_update;

				$new_ar[$this->slug] = $newarray;

				$merged=array_merge($licenses,$new_ar);

				update_option('_fp_licenses', $merged);

				return $newarray;
			}else{
				return false;
			}
		}

    // Save new license data
        function save_new_license_field_values($license_field, $new_value, $license_slug){
    		$licenses = get_option('_fp_licenses');

    		if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$license_slug]) ){

    			$newarray = array();

    			$this_license = $licenses[$license_slug];

    			foreach($this_license as $field=>$val){
    				if($field !=$license_field){
    					$newarray[$field]=$val;
    				}
    			}
    			$newarray[$license_field]=$new_value;

    			$new_ar[$license_slug] = $newarray;

    			$merged=array_merge($licenses,$new_ar);

    			update_option('_fp_licenses',$merged);
    		}
    	}

    // Push in plugin version information to get the update notification
    public function setTransitent($transient) {
        // If we have checked the plugin data before, don't re-check
		if (empty($transient->checked)) {
		    return $transient;
		}
        //global $foodpress;
		// Get plugin & GitHub release information
		$this->initPluginData();
		$this->getRepoReleaseInfo();
        //var_dump($this->githubAPIResult);
        $latestGithubVersion = (isset($this->githubAPIResult->tag_name) && !empty($this->githubAPIResult->tag_name)) ? $this->githubAPIResult->tag_name : null;
        //var_dump($latestGithubVersion);
		// Check the versions if we need to do an update
		$doUpdate = version_compare($latestGithubVersion, $transient->checked[$this->slug]);
        //var_dump($doUpdate);
		// Update the transient to include our updated plugin data
		if ($doUpdate == 1) {

		    $package = $this->githubAPIResult->zipball_url;

		    // Include the access token for private GitHub repos
		    if (!empty($this->accessToken)) {
		        $package = add_query_arg(array("access_token" => $this->accessToken), $package);
		    }

		    $obj = new stdClass();
		    $obj->slug = $this->slug;
		    $obj->new_version = $latestGithubVersion;
		    $obj->url = $this->pluginData["PluginURI"];
		    $obj->package = $package;
		    $transient->response[$this->slug] = $obj;

            $this->save_new_update_details($latestGithubVersion, $doUpdate, $this->current_version);
		}



        return $transient;
    }

    // Push in plugin version information to display in the details lightbox
    public function setPluginInfo( $false, $action, $response ) {
		// Get plugin & GitHub release information
		$this->initPluginData();
		$this->getRepoReleaseInfo();

		// If nothing is found, do nothing
		if (empty($response->slug) || $response->slug != $this->slug) {
		    return $false;
		}

		// Add our plugin information
		$response->last_updated = $this->githubAPIResult->published_at;
        $response->name = $this->pluginData["Name"];
		$response->slug = $this->slug;
		$response->plugin_name  = $this->pluginData["Name"];
		$response->version = $this->githubAPIResult->tag_name;
		$response->author = $this->pluginData["AuthorName"];
		$response->homepage = $this->pluginData["PluginURI"];

		// This is our release download zip file
		$downloadLink = $this->githubAPIResult->zipball_url;

		// Include the access token for private GitHub repos
		if (!empty( $this->accessToken)) {
		    $downloadLink = add_query_arg(
		        array("access_token" => $this->accessToken),
		        $downloadLink
		    );
		}
		$response->download_link = $downloadLink;

		// We're going to parse the GitHub markdown release notes, include the parser
		require_once(plugin_dir_path( __FILE__ ) . "parsedown.php");

		// Set basic info
		$install_instructions = '
			<p>Installation:</p>

			<p><strong>Install</strong></p>
			<ul>
				<li>Unzip the <code>foodpress.zip</code> file.</li>
				<li>Upload the <code>foodpress</code> folder (not just the files in it) to your <code>wp-contents/plugins</code> folder. If you are using FTP, use binary mode.</li>
			</ul>
			<p><strong>Activate</strong></p>
			<ul>
				<li>In your WordPress admin, go to Plugins Page</li>
				<li>Activate the foodpress plugin which will take you to the new welcome screen</li>
			</ul>
		';
		$license_info = '
			<p><strong>Get free updates</strong></p>
			<p>In order to get free foodpress updates and download them directly in here activate your copy of foodpress with proper license.</p>
			<p><strong>How to get your license key</strong></p>
			<ul>
				<li>Login to your myFoodpress.com account</li>
				<li>Go to License Keys</li>
				<li>Copy the license key matching your FoodPress or addon purchase</li>
				<li>Go to your website admin where you have the plugins installed</li>
				<li>Under "Addons & Licenses" tab find the foodpress license and click "Activate Now"</li>
				<li>Paste the copied purchased code from envato, and click "Activate Now"</li>
				<li>Once the license if verified and activated you will start getting updates automatically</li>
			</ul>
			<a href="http://www.myfoodpress.com/documentation/how-to-find-foodpress-license-key/">Updated Documentation</a>
		';

		// Create tabs in the lightbox
		$response->sections = array(
		    'description' => 'FoodPress is a wordpress restaurant menu management plugin for wordpress.',
		    'changelog' => class_exists("Parsedown")
		        ? Parsedown::instance()->parse($this->changeLog)
		        : $this->changeLog,
		    'installation' => $install_instructions,
		    'register_license' => $license_info,
		    'FAQ' => 'For support & frequently asked questions, visit <a href="http://support.ashanjay.com">the FoodPress forums</a>.',
		    'latest_news' => 'Make sure to follow us via twitter <code>@myfoodpress</code> for updates.'
		);

		// Gets the required version of WP if available
		$matches = null;
		preg_match("/requires:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches);
		if (!empty($matches)) {
		    if (is_array($matches)) {
		        if (count($matches) > 1) {
		            $response->requires = $matches[1];
		        }
		    }
		}

		// Gets the tested version of WP if available
		$matches = null;
		preg_match("/tested:\s([\d\.]+)/i", $this->githubAPIResult->body, $matches);
		if (!empty($matches)) {
		    if (is_array($matches)) {
		        if (count($matches) > 1) {
		            $response->tested = $matches[1];
		        }
		    }
		}

        return $response;
    }

 	// Perform check before install
    public function preInstall($true, $args = null) {
		// Get the plugin info
		$this->initPluginData();
		// Check to see if the plugin was previously installed
		$this->pluginActivated = is_plugin_active($this->slug);

	    return $true;
    }

    // Perform additional actions to successfully install our plugin
    public function postInstall($true, $hook_extra, $result) {
		// Since we are hosted in GitHub, our plugin folder would have a dirname of
		// reponame-tagname change it to our original one:
		global $wp_filesystem;
		$pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname($this->slug);
		$wp_filesystem->move($result['destination'], $pluginFolder);
		$result['destination'] = $pluginFolder;

		// Re-activate plugin if needed
		if ($this->pluginActivated) {
		    $activate = activate_plugin($this->slug);
		}

        return $result;
    }




}
