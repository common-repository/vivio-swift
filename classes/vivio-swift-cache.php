<?php
class Vivio_Swift_Cache
{
	function __construct()
    {
        $this->cache_excludes = new Vivio_Swift_Cache_Excludes();
        $this->cache_extras = new Vivio_Swift_Cache_Extras();
        $this->cache_onaccess = new Vivio_Swift_Cache_OnAccess();
        $this->cache_preload = new Vivio_Swift_Cache_Preload();
    }

    function cache_url_with_agent($url, $agent=VIVIO_SWIFT_CACHE_BOT_NAME)
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::cache_url_with_agent() - init for URL: ".$url, 0);

        $args = array(
            'timeout'       => 10,
            'sslverify'     => false,
            'user-agent'    => $agent,
            'headers'       => array(
                'cache-control' => array(
                    'no-store, no-cache, must-revalidate',
                    'post-check=0, pre-check=0'
                )
            )
        );

        // hit the url we're caching
        $response = wp_remote_get($url, $args);

        // check the response
        if (!$response || is_wp_error($response)){
            // no response from wp_remote_get()
            // log the problem
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::cache_url_with_agent() - No response when attempting to cache URL: ".$url."[SKIPPING]", 4);
            ob_start(); var_dump($url); $url_result = ob_get_clean();
            $vivio_swift_global->debug_logger->log_debug("URL Dump: ".$url_result, 1);
            ob_start(); var_dump($response); $response_result = ob_get_clean();
            $vivio_swift_global->debug_logger->log_debug("Response Dump: ".$response_result, 1);

        } elseif ( is_wp_error($response) ){
            // received error object from wp_remote_get()
            // log the problem
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::cache_url_with_agent() - Received error response object when attempting to cache URL: ".$url."[SKIPPING]", 4);
            $vivio_swift_global->debug_logger->log_debug("Error codes:".$response->get_error_codes(), 4);
            $vivio_swift_global->debug_logger->log_debug("Error message:".$response->get_error_message(), 4);
            ob_start(); var_dump($url); $url_result = ob_get_clean();
            $vivio_swift_global->debug_logger->log_debug("URL Dump: ".$url_result, 1);
            ob_start(); var_dump($response); $response_result = ob_get_clean();
            $vivio_swift_global->debug_logger->log_debug("Response Dump: ".$response_result, 1);
        } elseif ( wp_remote_retrieve_response_code($response) != 200 ){
            // received bad response code
            // log the problem
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::cache_url_with_agent() - Received bad response code when attempting to cache URL: ".$url."[SKIPPING]", 4);
            $vivio_swift_global->debug_logger->log_debug("Response code:".wp_remote_retrieve_response_code($response), 4);
        } else {
            // all good cache the page
            if ( ! $uri = Vivio_Swift_Cache::parse_uri($url) ){
                // failed to parse URL
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::cache_url_with_agent() - Failed to parse: ".$url."[SKIPPING]", 4);
            } else {
                // send page off to save util
                Vivio_Swift_Cache::save_cached_page($uri, wp_remote_retrieve_body($response));
            }
        }
    }

    function parse_uri($url)
    {
        global $vivio_swift_global;
        
        if ($parsed_path=parse_url($url,PHP_URL_PATH)){
            // ensure path ends with '/'
            if(substr($url, -1) === '/'){
                return $parsed_path;
            } else {
                return $parsed_path.'/';
            }
        }
        else {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::parse_uri() - no path found in : ".$url, 1);
            // if it's the front page, set path to /
            if(is_front_page()){
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::parse_uri() - Found front page. Setting path to '/'.", 1);
                $parsed_path = '/';
                return $parsed_path;
            }
            return false;
        }

    }

    function save_cached_page($uri, $body, $return=0)
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - Save Cached Page Init.", 0);

        // remove index.php if it exists in the uri
        //$uri = str_replace("/index.php/", "", $uri);

        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - Original URI: ".$uri, 1);
        $fullpath = Vivio_Swift_Cache::get_cache_path($uri);
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - Looking for: ".$fullpath, 1);

        // create directory structure if doesn't already exist
        if (!file_exists($fullpath)){
            if (!Vivio_Swift_Utility_File::create_dir_recursive($fullpath)){
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - failed to create directory: ".$fullpath, 4);
            } else {
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - created directory: ".$fullpath, 1);
            }
        } else {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - directory exists: ".$fullpath, 1);
        }

        if($vivio_swift_global->configs->get_value('vivio_swift_enable_cache_comment')=='1'){
            // include comment if settings tell us to
            $date = new DateTime();
            $body = $body."<!-- [Vivio Swift] Cached: ".$date->format('Y-m-d H:i:s')." -->";
        }

        // save index.html file
        if (!Vivio_Swift_Utility_File::is_file_writable($fullpath."index.html")){
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - unable to write to: ".$fullpath."index.html", 4);
        } else {
            if (!Vivio_Swift_Utility_File::write_content_to_file($fullpath."index.html",$body)){
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - failed to create index.html in path: ".$fullpath."index.html", 4);
            } else {
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - created index.html in path: ".$fullpath."index.html", 1);
            }
        }

        if ($return){
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::save_cached_page() - Return is true - passing request back to the user.", 2);
            return 1;
        } else {
            return 0;
        }
    }

    function get_cache_path($uri)
    {
        // remove index.php if it exists in the uri
        //$uri = str_replace("/index.php/", "", $uri);

        $fullpath = VIVIO_SWIFT_CACHE_PATH.'d/'.$uri;

        // clean any extra slashes
        $fullpath = str_replace("///", "/", $fullpath);
        $fullpath = str_replace("//", "/", $fullpath);

        return $fullpath;
    }

    // Refresh events for new WP posts
    function process_post_transitions($new_status, $old_status, $post)
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::process_post_transitions() - Processing post transition for post ID: ".$post->ID,1);
        // don't worry about post revisions right now.
        if (wp_is_post_revision($post->ID))return;

        $post_new_rule = $vivio_swift_global->configs->get_value('vivio_swift_refresh_on_post_new');
        $post_update_rule = $vivio_swift_global->configs->get_value('vivio_swift_refresh_on_post_update');
        $preload_cache_enabled = $vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled');

        if ($post_new_rule && $new_status=="publish" && $old_status!="publish"){
            // new post, clear cache
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::process_post_transitions() - New post test resolved true, attempting to clear cache...",2);
            Vivio_Swift_Utility_File::clear_cache_dir();
            if($preload_cache_enabled){$this->cache_preload->create_preload_cache(1);}
        }

        if ($post_update_rule && $old_status=="publish"){
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::process_post_transitions() - Update post test resolved true, attempting to clear cache...",2);
            Vivio_Swift_Utility_File::clear_cache_dir();
            if($preload_cache_enabled){$this->cache_preload->create_preload_cache(1);}
        }
    }

    /*
     * Generic method for clearing cache when a term (tag) changes
     * 
     * @param int      $term_id     Term ID
     * @param int      $tt_id       Taxonomy ID
     * @param string   $taxonomy    Taxonomy Slug
     */
    function process_term_change($term_id, $tt_id, $taxonomy)
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::process_created_terms() - Processing term change for term ID: ".$term_id,1);
        $term_change_rule = $vivio_swift_global->configs->get_value('vivio_swift_refresh_on_tag_change');
        $preload_cache_enabled = $vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled');
        if ($term_change_rule){
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::process_created_terms() - Update term test resolved true, attempting to clear cache...",2);
            Vivio_Swift_Utility_File::clear_cache_dir();
            if($preload_cache_enabled){$this->cache_preload->create_preload_cache(1);}
        }
    }

    function process_taxonomy_change($term_id, $tt_id)
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::process_taxonomy_change() - Processing taxonomy change for taxonomy ID: ".$tt_id,1);
        $taxonomy_change_rule = $vivio_swift_global->configs->get_value('vivio_swift_refresh_on_category_change');
        $preload_cache_enabled = $vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled');
        if ($taxonomy_change_rule){
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache::process_taxonomy_change() - Update taxonomy test resolved true, attempting to clear cache...",2);
            Vivio_Swift_Utility_File::clear_cache_dir();
            if($preload_cache_enabled){$this->cache_preload->create_preload_cache(1);}
        }
    }



}