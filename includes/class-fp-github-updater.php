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
 
    private $test;

    function __construct( $pluginFile, $gitHubProjectName, $accessToken = '' ) {
        add_filter( "pre_set_site_transient_update_plugins", array( $this, "setTransitent" ) );
        add_filter( "plugins_api", array( $this, "setPluginInfo" ), 10, 3 );
        add_filter( "upgrader_post_install", array( $this, "postInstall" ), 10, 3 );
 
        $this->pluginFile = $pluginFile;
        $this->repo = $gitHubProjectName;
        $this->accessToken = $accessToken;
    }
 
    // Get information regarding our plugin from WordPress
    private function initPluginData() {
		$this->slug = plugin_basename($this->pluginFile);
		$this->pluginData = get_plugin_data($this->pluginFile);
    }
 
    // Get information regarding our plugin from GitHub
    private function getRepoReleaseInfo() {
		// Only do this once as WP runs this twice by default
		if (!empty($this->githubAPIResult)) {
		    return;
		}
		// Query the GitHub API
		$url = "https://api.github.com/repos/{$this->repo}/releases";
		 
		// We need the access token for private repos
		if (!empty($this->accessToken)) {
		    $url = add_query_arg(array("access_token" => $this->accessToken), $url);
		}

		// Get the results
		$this->githubAPIResult = wp_remote_retrieve_body(wp_remote_get($url));
		if (!empty($this->githubAPIResult)) {
		    $this->githubAPIResult = @json_decode($this->githubAPIResult);
		}

		// Use only the latest non-draft release
		if (is_array($this->githubAPIResult)) {
			foreach ($this->githubAPIResult as $result) {
				if ($result->draft == false) {
					$this->githubAPIResult = $result;
					break;
				}
			}
		}
    }
 
    // Push in plugin version information to get the update notification
    public function setTransitent($transient) {
        // If we have checked the plugin data before, don't re-check
		if (empty($transient->checked)) {
		    return $transient;
		}

		// Get plugin & GitHub release information
		$this->initPluginData();
		$this->getRepoReleaseInfo();

		// Check the versions if we need to do an update
		$doUpdate = version_compare($this->githubAPIResult->tag_name, $transient->checked[$this->slug]);

		// Update the transient to include our updated plugin data
		if ($doUpdate == 1) {
		    $package = $this->githubAPIResult->zipball_url;
		 
		    // Include the access token for private GitHub repos
		    if (!empty($this->accessToken)) {
		        $package = add_query_arg(array("access_token" => $this->accessToken), $package);
		    }
		 
		    $obj = new stdClass();
		    $obj->slug = $this->slug;
		    $obj->new_version = $this->githubAPIResult->tag_name;
		    $obj->url = $this->pluginData["PluginURI"];
		    $obj->package = $package;
		    $transient->response[$this->slug] = $obj;
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
		    return false;
		}

		// Add our plugin information
		$response->last_updated = $this->githubAPIResult->published_at;
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
			<p>Download, Upgrading, Installation:</p>
			<p><strong>Upgrade</strong></p>
			<ul>
				<li>First deactivate foodpress.</li>
				<li>Remove the <code>foodpress</code> directory.</li>
			</ul>
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
				<li>Login into your Envato account</li>
				<li>Go to Download tab</li>
				<li>Under foodpress click "License Cerificate"</li>
				<li>Open text file and copy the <strong>Item Purchase Code</strong></li>
				<li>Go to myfoodpress in your website admin</li>
				<li>Under "Licenses" tab find the foodpress license and click "Activate Now"</li>
				<li>Paste the copied purchased code from envato, and click "Activate Now"</li>
				<li>Once the license if verified and activated you will be able to download updates automatically</li>
			</ul>
			<a href="http://www.myfoodpress.com/documentation/how-to-find-foodpress-license-key/">Updated Documentation</a>
		';

		// Create tabs in the lightbox
		$response->sections = array(
		    'description' => 'FoodPress is a wordpress restaurant menu management plugin for wordpress.',
		    'changelog' => class_exists("Parsedown")
		        ? Parsedown::instance()->parse($this->githubAPIResult->body)
		        : $this->githubAPIResult->body,
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
 
    // Perform additional actions to successfully install our plugin
    public function postInstall($true, $hook_extra, $result) {
		// Get plugin information
		$this->initPluginData();

		// Remember if our plugin was previously activated
		$wasActivated = is_plugin_active($this->slug);

		// Since we are hosted in GitHub, our plugin folder would have a dirname of
		// reponame-tagname change it to our original one:
		global $wp_filesystem;
		$pluginFolder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . dirname($this->slug);
		$wp_filesystem->move($result['destination'], $pluginFolder);
		$result['destination'] = $pluginFolder;

		// Re-activate plugin if needed
		if ($wasActivated) {
		    $activate = activate_plugin($this->slug);
		}

        return $result;
    }
}