<?php
class Vivio_Swift_Cache_Preload
{
	function __construct()
    {
        //add_action('vivio_swift_schedule_preload_cache', array(&$this, 'schedule_create_preload_cache'));
    }

    function enable()
    {
        global $vivio_swift_global;
    	$vivio_swift_global->configs->set_value('vivio_swift_preload_cache_enabled','1');//Checkbox
        $vivio_swift_global->configs->save_config();
    	$vivio_swift_global->util_htaccess->write_to_htaccess();
    	$vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::enable() - Preload Cache enabled.", 1);
    }

	function disable()
    {
        global $vivio_swift_global;
    	$vivio_swift_global->configs->set_value('vivio_swift_preload_cache_enabled','');//Checkbox
        $vivio_swift_global->configs->save_config();
    	$vivio_swift_global->util_htaccess->write_to_htaccess();
    	$vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::enable() - Preload Cache disabled.", 1);
    }

    function clear()
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::clear() - Clearing Preload Cache...", 1);
        $vivio_swift_global->util_file->clear_cache_dir();
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::clear() - Resetting Preload Cache Last Run date...", 1);
        $vivio_swift_global->configs->set_value('vivio_swift_preload_last_run_date','');
        $vivio_swift_global->configs->save_config();
    }

    function create_preload_cache($force=0)
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Create Preload Cache Init.", 0);

        $urls = array();

        // make sure the specified amount of time has passed before we do another run
        if ( $force || ($this->verify_preload_cache_expired()) ) {

            // TODO Add option to select mobile cache as well
            // TODO Add options to select what we want to cache. For now, CACHE ALL THE THINGS

            // home page URI
            $home_url = rtrim(get_option("home"), '/').'/';
            array_push($urls, $home_url);

            // log it
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Added HOME URI: ".get_option("home"), 1);

            // post URI's
            $posts = get_posts(array('post_type'=>'post','numberposts'=>'-1','post_status'=>'publish'));

            if (count($posts) > 0){
                foreach ($posts as $p){
                    if ($post_permalink = get_permalink($p)){
                        // if we're using permalinks, use the permalink uri
                        array_push($urls, $post_permalink);

                        // log it
                        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Added POST URI: ".$post_permalink, 1);
                    } else {
                        // otherwise just log it as incompatible
                        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Failed to get Permalink for Post ID:".$p->ID."[SKIPPING]", 4);
                    }
                } // end foreach
            }

            // attachment URI's
            $posts = get_posts(array('post_type'=>'attachment','numberposts'=>'-1','post_status'=>'publish','post_parent' => null));

            if (count($posts) > 0){
                foreach ($posts as $p){
                    if ($post_permalink = get_permalink($p)){
                        // if we're using permalinks, use the permalink uri
                        array_push($urls, $post_permalink);

                        // log it
                        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Added ATTACHMENT URI: ".$post_permalink, 1);
                    } else {
                        // otherwise just log it as incompatible
                        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Failed to get Permalink for Post ID:".$p->ID."[SKIPPING]", 4);
                    }
                } // end foreach
            }

            // page URI's
            $posts = get_posts(array('post_type'=>'page','numberposts'=>'-1','post_status'=>'publish'));

            if (count($posts) > 0){
                foreach ($posts as $p){
                    if ($post_permalink = get_permalink($p)){
                        // if we're using permalinks, use the permalink uri
                        array_push($urls, $post_permalink);

                        // log it
                        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Added PAGE URI: ".$post_permalink, 1);
                    } else {
                        // otherwise just log it as incompatible
                        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Failed to get Permalink for Post ID:".$p->ID."[SKIPPING]", 4);
                    }
                } // end foreach
            }

            // Category URI's
            $categories = get_categories( array(
                'orderby' => 'id',
                'order'   => 'ASC'
            ) );

            foreach ($categories as $cat){
                $cat_link = get_category_link( $cat->term_id );
                
                // save the link
                array_push($urls, $cat_link);

                // log it
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Added PAGE URI: ".$cat_link, 1);
            }

            // Tag URI's
            $tags = get_tags();

            foreach ($tags as $tag){
                $tag_link = get_tag_link( $tag->term_id );

                // save the link
                array_push($urls, $tag_link);

                // log it
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Added PAGE URI: ".$tag_link, 1);

            }

            foreach ($urls as $url){

                $exclude_response = $vivio_swift_global->cache_obj->cache_excludes->process_cache_excludes($url,$force);
                if($exclude_response){continue;} // a response means url should be excluded.

                // send url off to the caching process
                // $vivio_swift_global->cache_obj->cache_url_with_agent($url, VIVIO_SWIFT_CACHE_BOT_NAME);
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Pushing URL to queue: ".$url, 1);
                $vivio_swift_global->proc_preload->push_to_queue($url);
            }
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Queueing complete. Dispatching preload process queue.", 1);
            $vivio_swift_global->proc_preload->save()->dispatch();

            // caching process finished. record time.
            $date = new DateTime();
            $datetime = $date->format('Y-m-d H:i:s');
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::create_preload_cache() - Recording preload cache runtime: ".$datetime, 1);
            $vivio_swift_global->configs->set_value('vivio_swift_preload_last_run_date',$datetime);
            $vivio_swift_global->configs->save_config();
        }
    
    } // end create_preload_cache

    // checks the current time against the last run date to see if the preload cache has expired
    function verify_preload_cache_expired()
    {
        global $vivio_swift_global;

        // if the last run is null, it means that the preload process has never run
        if ($vivio_swift_global->configs->get_value('vivio_swift_preload_last_run_date')=='') {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::verify_preload_cache_expired() - no preload cache on record.", 2);
            return true;
        }

        // create new datetime objects
        $runtime = new DateTime( $vivio_swift_global->configs->get_value('vivio_swift_preload_last_run_date') );
        $nowtime = new DateTime( date('Y-m-d H:i:s') );

        $expire_hours = $vivio_swift_global->configs->get_value('vivio_swift_preload_cache_expire_hours');
        $elapsed_hours = $nowtime->diff($runtime);

        if ( $elapsed_hours->format('%h') >= $expire_hours) {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::verify_preload_cache_expired() - hour limit reached: ".$elapsed_hours->format('%h'), 2);
            return true;
        } else {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::verify_preload_cache_expired() - Elapsed Hours: ".$elapsed_hours->format('%h'), 2);
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::verify_preload_cache_expired() - Expire Hours: ".$expire_hours, 2);
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::verify_preload_cache_expired() - current cache is still valid. Try forcing a preload if you wish to refresh your cache.", 2);
        }
        
        // default return false (don't run preload cache)
        return false;
    
    }

	// scheduled task for Vivio_Swift_Cron()
    function schedule_create_preload_cache()
    {
        /*
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Preload::schedule_create_preload_cache() - running scheduled preload cache...", 2);
        $preload_enabled = ($vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled')=='1')?true:false;
        if ($preload_enabled){
            $vivio_swift_global->cache_obj->create_preload_cache(1);
        }
        */
    }

}