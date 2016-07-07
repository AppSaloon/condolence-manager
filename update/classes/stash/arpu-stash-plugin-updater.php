<?php
class Arpu_Stash_Plugin_Updater {

    private $slug; // plugin slug
    private $real_slug; // plugin real slug
    private $plugin_data; // plugin data
    private $host;
    private $username; // Stash username
    private $password;
    private $repo; // Stash repo name
    private $project_name; // Stash project name
    private $plugin_file; // __FILE__ of our plugin
    private $stash_api_result; // holds data from Stash
    private $version;
    private $commit;
    private $plugin_activated;
    private $download_link;
    private $download_path;
    private $author;
    private $author_timestamp;
    private $author_message;

    /**
     * Add filters to check plugin version
     *
     * Arpu_Stash_Plugin_Updater constructor.
     * @param $stash_plugin
     */
    function __construct( $stash_plugin ) {
        $this->plugin_file = $stash_plugin['plugin_file'];
        $this->host = $stash_plugin['stash_host'];
        $this->username = $stash_plugin['stash_owner'];
        $this->password = $stash_plugin['stash_password'];
        $this->project_name = $stash_plugin['stash_project_name'];
        $this->repo = $stash_plugin['stash_repo_name'];
        $this->init_plugin_data();

        add_filter( "pre_set_site_transient_update_plugins", array( $this, "stash_set_transient" ) );
        add_filter( "plugins_api", array( $this, "stash_set_plugin_info" ), 10, 3 );
        add_filter( "upgrader_post_install", array( $this, "stash_post_install" ), 10, 3 );
        add_filter( "upgrader_pre_install", array( $this, "stash_pre_install" ), 10, 3 );
        add_filter( "http_request_args", array($this, "stash_request_args"), 10, 2);
    }

    public function stash_request_args($r, $url){
        $hasAt = strpos($url, 'at=');

        if( $hasAt !== false ){
            $url = substr($url, 0, $hasAt + 3);
        }

        if( $url == $this->get_download_url() ){
            $r['headers'] = array( 'Authorization' => 'Basic ' . base64_encode( "$this->username:$this->password" ) );
        }

        return $r;
    }

    /**
     * Returns slug, real slug and plugin data
     */
    private function init_plugin_data() {
        $this->slug = plugin_basename( $this->plugin_file );
        $this->real_slug = $this->get_slug_name( $this->slug );
        $this->plugin_data = get_plugin_data( $this->plugin_file );
    }

    /**
     * Returns real slug name
     * @param $slug plugin slug
     * @return string real plugin slug
     */
    public function get_slug_name($slug){
        $pos = strpos($slug, '/');
        return substr($slug, 0, $pos);
    }

    /**
     * Check if plugin is activated
     * @param $true
     * @param $args
     */
    public function stash_pre_install( $true, $args ) {
        $this->plugin_activated = is_plugin_active( $this->slug );
    }

    /**
     * Get information regarding our plugin from Stash
     */
    private function get_repo_release_info() {
        // Only do this once
        if ( ! empty( $this->stash_api_result ) ) {
            return;
        }

        // Query the Stash API
        $url = $this->get_tag_url();

        $result = $this->get_stash_data($url);

        if( $result['response']['code'] == 200 ){
            $this->stash_api_result = @json_decode($result['body']);

            $this->stash_api_result = $this->stash_api_result->values[0];
            $this->version = $this->stash_api_result->displayId;
            $this->commit = $this->stash_api_result->latestCommit;
        }
    }

    /**
     * Get information regarding commit from Stash
     */
    private function get_commit_info() {
        // Only do this once
        if ( empty( $this->stash_api_result ) ) {
            return;
        }
        $url = "http://{$this->host}/rest/api/1.0/projects/{$this->project_name}/repos/{$this->repo}/commits/{$this->commit}";
        $result = $this->get_stash_data($url);

        if( $result['response']['code'] == 200 ){
            $this->author = @json_decode($result['body']);

            $this->author_timestamp = substr($this->author->authorTimestamp, 0, -3);
            $this->author_message = $this->author->message;
        }
    }

    private function get_stash_data($url){
        $headers = array( 'Authorization' => 'Basic ' . base64_encode( "$this->username:$this->password" ) );
        $result = wp_remote_get( $url, array( 'headers' => $headers ) );
        return $result;
    }

    /**
     * Push in plugin version information to get the update notification
     */
    public function stash_set_transient( $transient ) {
        // If we have checked the plugin data before, don't re-check
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        // Get plugin & Stash release information
        $this->get_repo_release_info();

        // Check the versions if we need to do an update
        $do_update = version_compare( $this->check_version_name($this->version), $transient->checked[$this->slug] );

        // Update the transient to include our updated plugin data
        if ( $do_update == 1 ) {
            $package = $this->get_download_url();
            $this->download_link = $package;

            //$this->download_package($package);

            $obj = new stdClass();
            $obj->plugin = $this->slug;
            $obj->slug = $this->real_slug;
            $obj->new_version = $this->version;
            $obj->url = $this->plugin_data["PluginURI"];
            $obj->package = $this->download_link;
            $transient->response[$this->slug] = $obj;
        }

        return $transient;
    }

    /**
     * Push in plugin version information to display in the details lightbox
     * + pass update plugin data to wordpress
     */
    public function stash_set_plugin_info( $false, $action, $response ) {
        // Get plugin & Stash release information

        $this->init_plugin_data();
        $this->get_repo_release_info();
        $this->get_commit_info();

        // If nothing is found, do nothing
        if ( empty( $response->slug ) || $response->slug != $this->real_slug ) {
            return false;
        }

        // Add our plugin information
        $response->last_updated = date('Y-m-d', $this->author_timestamp);
        $response->slug = $this->real_slug;
        $response->plugin_name  = $this->plugin_data["Name"];
        $response->version = $this->version;
        $response->author = $this->plugin_data["AuthorName"];
        $response->homepage = $this->plugin_data["PluginURI"];
        $response->name = $this->plugin_data['Name'];

        // This is our release download zip file
        $response->download_link = $this->download_link;

        // We're going to parse the GitHub markdown release notes, include the parser
        require_once( ARPU_DIR . 'classes/parsedown/parsedown.php' );

        // Create tabs in the lightbox
        $response->sections = array(
            'description' => $this->plugin_data["Description"],
            'changelog' => class_exists( "Parsedown" )
                ? Parsedown::instance()->parse( $this->author_message )
                : $this->author_message
        );

        // Gets the required version of WP if available
        $matches = null;
        preg_match( "/requires:\s([\d\.]+)/i", $this->stash_api_result->message, $matches );
        if ( ! empty( $matches ) ) {
            if ( is_array( $matches ) ) {
                if ( count( $matches ) > 1 ) {
                    $response->requires = $matches[1];
                }
            }
        }

        // Gets the tested version of WP if available
        $matches = null;
        preg_match( "/tested:\s([\d\.]+)/i", $this->stash_api_result->message, $matches );
        if ( ! empty( $matches ) ) {
            if ( is_array( $matches ) ) {
                if ( count( $matches ) > 1 ) {
                    $response->tested = $matches[1];
                }
            }
        }

        return $response;
    }

    /**
     * Perform additional actions to successfully install our plugin
     */
    public function stash_post_install( $true, $hook_extra, $result ) {
        // Since we are hosted in Stash, our plugin folder would have a dirname of
        // reponame-tagname change it to our original one:

        global $wp_filesystem;

        $plugin_folder = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $this->real_slug . DIRECTORY_SEPARATOR;
        $wp_filesystem->move( $result['destination'], $plugin_folder );
        $result['destination'] = $plugin_folder;

        // Re-activate plugin if needed
        if ( $this->plugin_activated ) {
            activate_plugin( $this->real_slug );
        }

        return $result;
    }

    /**
     * Control plugin version
     *
     * @param $name version name
     * @return mixed controlled name
     */
    public function check_version_name($name){
        if( strpos($name, 'v' ) !== false ){
            $name = str_replace('v', '', $name);
        }
        return $name;
    }

    public function get_download_url(){
        return "http://{$this->host}/plugins/servlet/archive/projects/{$this->project_name}/repos/{$this->repo}?at={$this->version}";
    }

    public function get_tag_url(){
        return "http://{$this->host}/rest/api/1.0/projects/{$this->project_name}/repos/{$this->repo}/tags?limit=1";
    }
}