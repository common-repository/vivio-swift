<?php
class Vivio_Swift_Cache_Excludes
{
	
    function __construct()
    {
        
    }

    function get_cookie_values()
    {
        global $vivio_swift_global;
        $arr_cookie_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_cookie_values');
        if(!is_array($arr_cookie_values)){$arr_cookie_values = array();}
        return array_values($arr_cookie_values);
    }

    function add_cookie_value($val)
    {
        global $vivio_swift_global;
        $arr_cookie_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_cookie_values');
        if(!is_array($arr_cookie_values)){$arr_cookie_values = array();}
        if(!in_array($val,$arr_cookie_values)){
            array_push($arr_cookie_values, $val);
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_cookie_values',array_values($arr_cookie_values));
            $vivio_swift_global->configs->save_config();
        }
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    function remove_cookie_value($val)
    {
        global $vivio_swift_global;
        $arr_cookie_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_cookie_values');
        if(!is_array($arr_cookie_values)){$arr_cookie_values = array();}
        foreach ($arr_cookie_values as $i => $value) {
            if ($value==$val){unset($arr_cookie_values[$i]);}
        }
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_cookie_values',array_values($arr_cookie_values));
        $vivio_swift_global->configs->save_config();
        $vivio_swift_global->util_htaccess->write_to_htaccess();    }

    //returns true if passed cookie value is listed in required cookie exclusions
    function cookie_check_required_value($val)
    {
        $required_cookie_exclusions = array("comment_author_", "wordpress_logged_in", "wp-postpass_");
        foreach ($required_cookie_exclusions as $i => $value) {
            if($value==$val){return true;}
        }
        return false;
    }

    function get_user_agent_values()
    {
        global $vivio_swift_global;
        $arr_user_agent_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_user_agent_values');
        if(!is_array($arr_user_agent_values)){$arr_user_agent_values = array();}
        return array_values($arr_user_agent_values);
    }

    function add_user_agent_value($val)
    {
        global $vivio_swift_global;
        $arr_user_agent_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_user_agent_values');
        if(!is_array($arr_user_agent_values)){$arr_user_agent_values = array();}
        if(!in_array($val,$arr_user_agent_values)){
            array_push($arr_user_agent_values, $val);
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_user_agent_values',array_values($arr_user_agent_values));
            $vivio_swift_global->configs->save_config();
        }
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    function remove_user_agent_value($val)
    {
        global $vivio_swift_global;
        $arr_user_agent_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_user_agent_values');
        if(!is_array($arr_user_agent_values)){$arr_user_agent_values = array();}
        foreach ($arr_user_agent_values as $i => $value) {
            if ($value==$val){unset($arr_user_agent_values[$i]);}
        }
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_user_agent_values',array_values($arr_user_agent_values));
        $vivio_swift_global->configs->save_config();
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    //returns true if passed user_agent value is listed in required user_agent exclusions
    function user_agent_check_required_value($val)
    {
        $required_user_agent_exclusions = array(VIVIO_SWIFT_CACHE_BOT_NAME);
        foreach ($required_user_agent_exclusions as $i => $value) {
            if($value==$val){return true;}
        }
        return false;
    }

    function get_path_is_values()
    {
        global $vivio_swift_global;
        $arr_path_is_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_is_values');
        if(!is_array($arr_path_is_values)){$arr_path_is_values = array();}
        return array_values($arr_path_is_values);
    }

    function add_path_is_value($val)
    {
        global $vivio_swift_global;
        $arr_path_is_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_is_values');
        if(!is_array($arr_path_is_values)){$arr_path_is_values = array();}
        if(!in_array($val,$arr_path_is_values)){
            array_push($arr_path_is_values, $val);
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::add_path_is_value() - saving $val to array: ".$val, 1);
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_is_values',array_values($arr_path_is_values));
            $vivio_swift_global->configs->save_config();
        }
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::add_path_is_value() - writing .htaccess file...", 1);
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    function remove_path_is_value($val)
    {
        global $vivio_swift_global;
        $arr_path_is_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_is_values');
        if(!is_array($arr_path_is_values)){$arr_path_is_values = array();}
        foreach ($arr_path_is_values as $i => $value) {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::remove_path_is_value() - value=val: ".$value."=".$val, 0);
            if ($value==$val){
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::remove_path_is_value() - unset i: ".$i, 0);
                unset($arr_path_is_values[$i]);
            }
        }
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_is_values',array_values($arr_path_is_values));
        $vivio_swift_global->configs->save_config();
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    function get_path_ends_with_values()
    {
        global $vivio_swift_global;
        $arr_path_ends_with_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_ends_with_values');
        if(!is_array($arr_path_ends_with_values)){$arr_path_ends_with_values = array();}
        return array_values($arr_path_ends_with_values);
    }

    function add_path_ends_with_value($val)
    {
        global $vivio_swift_global;
        $arr_path_ends_with_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_ends_with_values');
        if(!is_array($arr_path_ends_with_values)){$arr_path_ends_with_values = array();}
        if(!in_array($val,$arr_path_ends_with_values)){
            array_push($arr_path_ends_with_values, $val);
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_ends_with_values',array_values($arr_path_ends_with_values));
            $vivio_swift_global->configs->save_config();
        }
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    function remove_path_ends_with_value($val)
    {
        global $vivio_swift_global;
        $arr_path_ends_with_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_ends_with_values');
        if(!is_array($arr_path_ends_with_values)){$arr_path_ends_with_values = array();}
        foreach ($arr_path_ends_with_values as $i => $value) {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::remove_path_ends_with_value() - value=val: ".$value."=".$val, 0);
            if ($value==$val){
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::remove_path_ends_with_value() - unset i: ".$i, 0);
                unset($arr_path_ends_with_values[$i]);
            }
        }
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_ends_with_values',array_values($arr_path_ends_with_values));
        $vivio_swift_global->configs->save_config();
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    function get_path_contains_values()
    {
        global $vivio_swift_global;
        $arr_path_contains_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_contains_values');
        if(!is_array($arr_path_contains_values)){$arr_path_contains_values = array();}
        return array_values($arr_path_contains_values);
    }

    function add_path_contains_value($val)
    {
        global $vivio_swift_global;
        $arr_path_contains_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_contains_values');
        if(!is_array($arr_path_contains_values)){$arr_path_contains_values = array();}
        if(!in_array($val,$arr_path_contains_values)){
            array_push($arr_path_contains_values, $val);
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_contains_values',array_values($arr_path_contains_values));
            $vivio_swift_global->configs->save_config();
        }
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    function remove_path_contains_value($val)
    {
        global $vivio_swift_global;
        $arr_path_contains_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_contains_values');
        if(!is_array($arr_path_contains_values)){$arr_path_contains_values = array();}
        foreach ($arr_path_contains_values as $i => $value) {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::remove_path_contains_value() - value=val: ".$value."=".$val, 0);
            if ($value==$val){
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::remove_path_contains_value() - unset i: ".$i, 0);
                unset($arr_path_contains_values[$i]);
            }
        }
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_contains_values',array_values($arr_path_contains_values));
        $vivio_swift_global->configs->save_config();
        $vivio_swift_global->util_htaccess->write_to_htaccess();
    }

    // returns error message is condition is hit. otherwise simply returns false.
    function process_cache_excludes($url='',$force=0)
    {
        global $vivio_swift_global;

        if(!$url){$url=$_SERVER[REQUEST_URI];}
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - processing excludes for: ".$url, 1);

        // user agent excludes
        /*
        if(strpos($_SERVER['HTTP_USER_AGENT'],VIVIO_SWIFT_CACHE_BOT_NAME)!==false){
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - bot rule hit [SKIPPING]", 2);
            return false;
        }
        */
        
        // POST requests exclude
        if(!is_admin() && $_SERVER['REQUEST_METHOD']=="POST"){
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - HTTP POST rule hit [SKIPPING]", 2);
            return "HTTP POST rule hit [SKIPPING]";
        }

        // query string excludes
        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_query_strings')=='1')
        {
            if(parse_url($url, PHP_URL_QUERY)){
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - URL query stings rule hit [SKIPPING]", 2);
                return "URL query stings rule hit [SKIPPING]";
            }
        }
        
        // cookie excludes
        // forced refreshes do not need to process cookies
        if(!$force){
            $arr_cookie_values=$vivio_swift_global->cache_obj->cache_excludes->get_cookie_values();
            foreach ($arr_cookie_values as $i => $value) {
                foreach ((array)$_COOKIE as $cookie_key => $cookie_value){
                    $searchval='/'.$value.'/i';
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Testing Cookie: ".$cookie_key, 0);
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Against Cookie Exclude: ".$value, 0);
                    if (preg_match($searchval,$cookie_key)){
                        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Exclude Cookie rule hit for cookie contains value: '".$value."' [SKIPPING]", 2);
                        return "Exclude Cookie rule hit for cookie contains value: '".$value."' [SKIPPING]";
                    } else {
                        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - NO MATCH", 0);
                    }
                }
            }
        } else {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Forced refresh, skipping cookie checks.", 1);
        }

        // user agent excludes
        if ($vivio_swift_global->configs->get_value('vivio_swift_enable_cache_comment')=='1')
        {
            $arr_user_agent_values=$vivio_swift_global->cache_obj->cache_excludes->get_user_agent_values();
            foreach ($arr_user_agent_values as $i => $value) {
                if(strpos($_SERVER['HTTP_USER_AGENT'],$value)!==false){
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Exclude 'user agent' rule hit for user agent value: '".$value."' [SKIPPING]", 2);
                    return "Exclude 'user agent' rule hit for user agent value: '".$value."' [SKIPPING]";
                }
            }
        }

        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_is')=='1')
        {
            $arr_path_is_values=$vivio_swift_global->cache_obj->cache_excludes->get_path_is_values();
            foreach ($arr_path_is_values as $i => $value) {
                $value = '/'.$value.'/';
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Testing URL: ".$url, 0);
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Against Path-Is Exclude: ".$value, 0);
                if ($url==$value){
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Exclude 'Path Is' rule hit for path value: '".$value."' [SKIPPING]", 2);
                    return "Exclude 'Path Is' rule hit for path value: '".$value."' [SKIPPING]";
                } else {
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - NO MATCH", 0);
                }
            }
        }

        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_ends_with')=='1')
        {
            $arr_path_ends_with_values=$vivio_swift_global->cache_obj->cache_excludes->get_path_ends_with_values();
            foreach ($arr_path_ends_with_values as $i => $value) {
                $searchval='/\.('.$value.')$/';
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Testing URL: ".$url, 0);
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Against Ends-With Exclude: ".$searchval, 0);
                if (preg_match($searchval,$url)){
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Exclude 'Path Ends With' rule hit for value: '".$value."' [SKIPPING]", 2);
                    return "Exclude 'Path Ends With' rule hit for value: '".$value."' [SKIPPING]";
                } else {
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - NO MATCH", 0);
                }
            }
        }

        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_contains')=='1')
        {
            $arr_path_contains_values=$vivio_swift_global->cache_obj->cache_excludes->get_path_contains_values();
            foreach ($arr_path_contains_values as $i => $value) {
                $searchval='/^.*('.$value.').*$/';
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Testing URL: ".$url, 0);
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Against Path-Contains Exclude: ".$searchval, 0);
                if (preg_match($searchval,$url)){
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Exclude 'Path Contains' rule hit for value: '".$value."' [SKIPPING]", 2);
                    return "Exclude 'Path Contains' rule hit for value: '".$value."' [SKIPPING]";
                } else {
                    $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - NO MATCH", 0);
                }
            }
        }

        // tests passed.
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Cache_Excludes::process_cache_excludes() - Exclude tests passed.", 1);
        return false;

    }

}