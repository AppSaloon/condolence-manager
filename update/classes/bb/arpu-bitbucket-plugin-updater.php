<?php
namespace cm\update\classes\bb;

use cm\update\classes\parsedown\Parsedown;

class Arpu_Bitbucket_Plugin_Updater {

    private $slug; // plugin slug
    private $real_slug; // plugin real slug
    private $plugin_data; // plugin data
    private $plugin_slug;
    private $host;
    private $download_host;
    private $username; // Bitbucket username
    private $password;
    private $repo; // Bitbucket repo name
    private $project_name; // Bitbucket project name
    private $plugin_file; // __FILE__ of our plugin
    private $bb_api_result; // holds data from Bitbucket
    private $version;
    private $commit_date;
    private $change_log;
    private $plugin_activated;
    private $download_link;

    /**
     * Add filters to check plugin version
     *
     * Arpu_Bitbucket_Plugin_Updater constructor.
     *
     * @param $bb_plugin
     */
    function __construct( $bb_plugin ) {
        $this->plugin_file   = $bb_plugin['plugin_file'];
        $this->plugin_slug   = $bb_plugin['plugin_slug'];
        $this->host          = $bb_plugin['bb_host'];
        $this->download_host = $bb_plugin['bb_download_host'];
        $this->username      = $bb_plugin['bb_owner'];
        $this->password      = $bb_plugin['bb_password'];
        $this->project_name  = $bb_plugin['bb_project_name'];
        $this->repo          = $bb_plugin['bb_repo_name'];
        $this->init_plugin_data();

        add_filter( "pre_set_site_transient_update_plugins", array( $this, "bb_set_transient" ) );
        add_filter( "plugins_api", array( $this, "bb_set_plugin_info" ), 10, 3 );
        add_filter( "upgrader_post_install", array( $this, "bb_post_install" ), 10, 3 );
        add_filter( "upgrader_pre_install", array( $this, "bb_pre_install" ), 10, 3 );
        add_filter( "http_request_args", array( $this, "bb_request_args" ), 10, 2 );
    }

    /**
     * Add bitbucket credentials to request url
     *
     * @param $r
     * @param $url
     *
     * @return mixed
     */
    public function bb_request_args( $r, $url ) {

        if ( strpos( $url, $this->check_download_url() ) !== false ) {
            $r['headers'] = array( 'Authorization' => 'Basic ' . base64_encode( "$this->username:$this->password" ) );
        }

        return $r;
    }

    /**
     * Returns slug, real slug and plugin data
     */
    private function init_plugin_data() {
        $this->slug        = plugin_basename( $this->plugin_file );
        $this->real_slug   = $this->get_slug_name( $this->slug );
        $this->plugin_data = get_plugin_data( $this->plugin_file );
    }

    /**
     * Returns real slug name
     *
     * @param $slug plugin slug
     *
     * @return string real plugin slug
     */
    public function get_slug_name( $slug ) {
        $pos = strpos( $slug, '/' );

        return substr( $slug, 0, $pos );
    }

    /**
     * Check if plugin is activated
     *
     * @param $true
     * @param $args
     */
    public function bb_pre_install( $true, $args ) {
        $this->plugin_activated = is_plugin_active( $this->slug );
    }

    /**
     * Get information regarding our plugin from Bitbucket
     */
    private function get_repo_release_info() {
        // Only do this once
        if ( ! empty( $this->bb_api_result ) ) {
            return;
        }

        // Query the Bitbucket API
        $url = $this->get_tag_url();

        $result = $this->get_bb_data( $url );

        if ( $result['response']['code'] == 200 ) {
            $decoded_result = json_decode( $result['body'] );

            $this->bb_api_result = $decoded_result;

            // first one is correct
            $latest_tag = current( $decoded_result->values );

            $changelog = $this->get_changelog_content( $latest_tag->target->hash );

            if ( $changelog !== false ) {
                $this->change_log = $changelog;
            } else {
                $this->change_log = $latest_tag->target->message;
            }

            $this->version     = $latest_tag->name;
            $this->commit_date = date( 'Y-m-d H:i:s', strtotime( $latest_tag->target->date ) );
        }
    }

    private function get_bb_data( $url ) {
        $headers = array( 'Authorization' => 'Basic ' . base64_encode( "$this->username:$this->password" ) );
        $result  = wp_remote_get( $url, array( 'headers' => $headers ) );

        return $result;
    }

    /**
     * Push in plugin version information to get the update notification
     */
    public function bb_set_transient( $transient ) {
        // If we have checked the plugin data before, don't re-check
        if ( empty( $transient->checked ) || ! isset( $transient->checked[ $this->slug ] ) ) {
            return $transient;
        }

        // default - don't update the plugin
        $do_update = 0;

        // if bitbucket live
        if ( $this->git_repository_is_live() ) {
            // Get plugin & Bitbucket release information
            $this->get_repo_release_info();

            // Check the versions if we need to do an update
            $do_update = version_compare( $this->check_version_name( $this->version ),
                $transient->checked[ $this->slug ] );
        }

        // Update the transient to include our updated plugin data
        if ( $do_update == 1 ) {
            $package             = $this->get_download_url();
            $this->download_link = $package;

            $obj                                = new \stdClass();
            $obj->plugin                        = $this->slug;
            $obj->slug                          = $this->real_slug;
            $obj->new_version                   = $this->version;
            $obj->url                           = "https://www.appsaloon.be";
            $obj->package                       = $this->download_link;
            $transient->response[ $this->slug ] = $obj;
        }

        return $transient;
    }

    /**
     * Push in plugin version information to display in the details lightbox
     * + pass update plugin data to wordpress
     */
    public function bb_set_plugin_info( $false, $action, $response ) {
        if ( 'plugin_information' == $action && $response->slug == $this->plugin_slug ) {
            // Get plugin & Bitbucket release information
            $this->init_plugin_data();

            if ( $this->git_repository_is_live() ) {
                $this->get_repo_release_info();

                // Add our plugin information
                $response->last_updated = $this->commit_date;
                $response->slug         = $this->real_slug;
                $response->plugin_name  = $this->plugin_data["Name"];
                $response->version      = $this->version;
                $response->author       = $this->plugin_data["AuthorName"];
                $response->homepage     = "https://www.appsaloon.be";
                $response->name         = $this->plugin_data['Name'];

                // This is our release download zip file
                $response->download_link = $this->get_download_url();

                $change_log = $this->change_log;

                $matches = null;
                preg_match_all( "/[##|-].*/", $this->change_log, $matches );
                if ( ! empty( $matches ) ) {
                    if ( is_array( $matches ) ) {
                        if ( count( $matches ) > 0 ) {
                            $change_log = '<p>';
                            foreach ( $matches[0] as $match ) {
                                if ( strpos( $match, '##' ) !== false ) {
                                    $change_log .= '<br>';
                                }
                                $change_log .= $match . '<br>';
                            }
                            $change_log .= '</p>';
                        }
                    }
                }


                // Create tabs in the lightbox
                $response->sections = array(
                    'description' => $this->plugin_data["Description"],
                    'changelog'   => Parsedown::instance()->parse( $change_log )
                );

                // Gets the required version of WP if available
                $matches = null;
                preg_match( "/requires:\s([\d\.]+)/i", $this->change_log, $matches );
                if ( ! empty( $matches ) ) {
                    if ( is_array( $matches ) ) {
                        if ( count( $matches ) > 1 ) {
                            $response->requires = $matches[1];
                        }
                    }
                }

                // Gets the tested version of WP if available
                $matches = null;
                preg_match( "/tested:\s([\d\.]+)/i", $this->change_log, $matches );
                if ( ! empty( $matches ) ) {
                    if ( is_array( $matches ) ) {
                        if ( count( $matches ) > 1 ) {
                            $response->tested = $matches[1];
                        }
                    }
                }

                return $response;
            }
        }


        return $false;
    }

    /**
     * Perform additional actions to successfully install our plugin
     */
    public function bb_post_install( $true, $hook_extra, $result ) {
        // Since we are hosted in Bitbucket, our plugin folder would have a dirname of
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
     *
     * @return mixed controlled name
     */
    public function check_version_name( $name ) {
        if ( strpos( $name, 'v' ) !== false ) {
            $name = str_replace( 'v', '', $name );
        }

        return $name;
    }

    public function get_download_url() {
        return "{$this->download_host}/{$this->project_name}/{$this->repo}/get/{$this->version}.zip";
    }

    public function check_download_url() {
        return "{$this->download_host}/{$this->project_name}/{$this->repo}/get/";
    }

    public function get_tag_url() {
        return "{$this->host}/2.0/repositories/{$this->project_name}/{$this->repo}/refs/tags?sort=-target.date";
    }

    public function git_repository_is_live() {
        $new_url = $this->host . "/2.0/repositories/" . $this->project_name . "/" . $this->repo;

        $request = wp_remote_get( $new_url, array( 'headers' => $this->get_headers() ) );

        if ( ! is_wp_error( $request ) && $request['response']['code'] == 200 ) {
            return true;
        }

        return false;
    }

    protected function get_headers() {
        return array( 'Authorization' => 'Basic ' . base64_encode( $this->username . ":" . $this->password ) );
    }

    /**
     * Get content of changelog.md file from bitbucket
     *
     * @param $commit_hash
     *
     * @return string content of changelog
     *          bool    false if wp errors
     */
    protected function get_changelog_content( $commit_hash ) {
        $changelog = wp_remote_get( 'https://bitbucket.org/' . $this->project_name . '/' . $this->repo . '/raw/' . $commit_hash . '/CHANGELOG.md',
            array( 'headers' => $this->get_headers() ) );

        if ( is_wp_error( $changelog ) ) {
            return false;
        }

        return $changelog['body'];
    }
}