<?php
class Vivio_Swift_Cache_OnAccess
{
	function __construct()
    {

    }

    function enable()
    {
        global $vivio_swift_global;
    	$vivio_swift_global->configs->set_value('vivio_swift_cache_enabled','1');//Checkbox
        $vivio_swift_global->configs->save_config();
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    	$vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::enable() - OnAccess Cache enabled.", 0);
    }

	function disable()
    {
        global $vivio_swift_global;
    	$vivio_swift_global->configs->set_value('vivio_swift_cache_enabled','');//Checkbox
        $vivio_swift_global->configs->save_config();
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    	$vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::enable() - OnAccess Cache disabled.", 0);
    }

    function clear()
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::clear() - Clearing OnAccess Cache...", 1);
        $vivio_swift_global->util_file->clear_cache_dir();
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::clear() - Resetting Preload Cache Last Run date...", 1);
        $vivio_swift_global->configs->set_value('vivio_swift_preload_last_run_date','');
        $vivio_swift_global->configs->save_config();
    }

    function create_page_cache()
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::create_page_cache() - creating page cache...",0);
        ob_start(array($this, "create_page_cache_closer"));
    }

    function create_page_cache_closer($response)
    {
        global $vivio_swift_global;
        global $wp;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::create_page_cache_closer() - Initializing closer...",0);
        $exclude_response = $vivio_swift_global->cache_obj->cache_excludes->process_cache_excludes($url,$force);
        if ($exclude_response){
            if($vivio_swift_global->configs->get_value('vivio_swift_enable_cache_comment')=='1'){
                return $response."<!-- [Vivio Swift] ".$exclude_response." -->";
            } else {
                return $response;
            }
        }

        // don't cache certain responses
        if((function_exists("http_response_code")) && (http_response_code() != 200))
        {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::create_page_cache_closer() - 200 HTTP response not found. [SKIPPING]",2);
            return $response;
        }
        // TODO add this to page excludes
        if($GLOBALS["pagenow"] == "wp-login.php")
        {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::create_page_cache_closer() - current page is on exclude list. [SKIPPING]",2);
            return $response;
        }

        // TODO add post-processors here (compression, combination, etc)


        // combine css
        if($vivio_swift_global->configs->get_value('vivio_swift_combine_css')=='1'){
            
        }

        // grab the current page URL
        // $url = home_url(add_query_arg(array(),$wp->request)); // doesn't include index.php
        $url = $_SERVER[REQUEST_URI];
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::create_page_cache_closer() - URL pulled as: ".$url,0);

        // parse the URI
        if ( ! $uri = $vivio_swift_global->cache_obj->parse_uri($url) ){
            // failed to parse URI
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::create_page_cache_closer() - Failed to parse: ".$url."[SKIPPING]", 4);
            return $response;
        }

        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_OnAccess::create_page_cache_closer() - Passing off to save_cached_page()...",0);
        // send page off to save util
        if ($vivio_swift_global->cache_obj->save_cached_page($uri,$response,1)){
            if($vivio_swift_global->configs->get_value('vivio_swift_enable_cache_comment')=='1'){
                // include comment if settings tell us to
                return $response."<!-- [Vivio Swift] cached page will be displayed next request. -->";
            } else {
                // otherwise just return to user.
                return $response;
            }
        }
    }

}