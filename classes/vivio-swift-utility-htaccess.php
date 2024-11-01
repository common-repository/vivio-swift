<?php

class Vivio_Swift_Utility_Htaccess
{
    //The following variables hold the markers for each of features added to the .htacces file
    //This enables us to locate blocks of code when a feature needs to be removed.
    private static $vivio_swift_excludes_marker_start = '# BEGIN Excludes';
    private static $vivio_swift_excludes_marker_end = '# END Excludes';

    private static $vivio_swift_basics_marker_start = '# BEGIN Basics';
    private static $vivio_swift_basics_marker_end = '# END Basics';

    private static $vivio_swift_excludes_posts_start = '# BEGIN Exclude POST Requests';
    private static $vivio_swift_excludes_posts_end = '# END Exclude POST Requests';

    private static $vivio_swift_exclude_query_strings_start = '# BEGIN Exclude Query Strings';
    private static $vivio_swift_exclude_query_strings_end = '# END Exclude Query Strings';

    private static $vivio_swift_exclude_cookie_contains_start = '# BEGIN Exclude Cookie Contains';
    private static $vivio_swift_exclude_cookie_contains_end = '# END Exclude Cookie Contains';

    private static $vivio_swift_exclude_user_agent_start = '# BEGIN Exclude User Agents';
    private static $vivio_swift_exclude_user_agent_end = '# END Exclude User Agents';

    private static $vivio_swift_exclude_path_is_start = '# BEGIN Exclude Path is';
    private static $vivio_swift_exclude_path_is_end = '# END Exclude Path is';

    private static $vivio_swift_exclude_path_ends_with_start = '# BEGIN Exclude Path ends with';
    private static $vivio_swift_exclude_path_ends_with_end = '# END Exclude Path ends with';

    private static $vivio_swift_exclude_path_contains_start = '# BEGIN Exclude Path contains';
    private static $vivio_swift_exclude_path_contains_end = '# END Exclude Path contains';

    private static $vivio_swift_custom_rules_start = '# BEGIN Custom Rules';
    private static $vivio_swift_custom_rules_end = '# END Custom Rules';

    private static $vivio_swift_cachecontrol_marker_start = '# BEGIN Cache-Control';
    private static $vivio_swift_cachecontrol_marker_end = '# END Cache-Control';

    function __construct()
    {

    }

    static function write_to_htaccess()
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_Htaccess::write_to_htaccess() - Write to .htaccess Init.", 0);
        //figure out what server is being used
        if ($vivio_swift_global->util->get_server_type() == -1) {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_Htaccess::write_to_htaccess() - Unable to write to .htaccess - unrecognized server type.", 4);
            return false; //unable to write to the file
        }

        //clean up old rules first
        if ($vivio_swift_global->util_htaccess->delete_from_htaccess() == -1) {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_Htaccess::write_to_htaccess() - Unable to delete from .htaccess file.", 4);
            return false; //unable to write to the file
        }

        $htaccess = ABSPATH . '.htaccess';

        if (!$f = @fopen($htaccess, 'a+')) {
            @chmod($htaccess, 0644);
            if (!$f = @fopen($htaccess, 'a+')) {
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_Htaccess::write_to_htaccess() - Unable to chmod .htaccess file.", 4);
                return false;
            }
        }
        $vivio_swift_global->util_file->backup_and_rename_htaccess($htaccess); //TODO - we dont want to continually be backing up the htaccess file
        @ini_set('auto_detect_line_endings', true);
        $ht = explode(PHP_EOL, implode('', file($htaccess))); //parse each line of file into array
        
        $rules = $vivio_swift_global->util_htaccess->getrules();

        $rulesarray = explode(PHP_EOL, $rules);
        $contents = array_merge($rulesarray, $ht);

        if (!$f = @fopen($htaccess, 'w+')) {
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_Htaccess::write_to_htaccess() - Unable to write to .htaccess file.", 4);
            return false; //we can't write to the file
        }

        $blank = false;

        //write each line to file
        foreach ($contents as $insertline) {
            if (trim($insertline) == '') {
                if ($blank == false) {
                    fwrite($f, PHP_EOL . trim($insertline));
                }
                $blank = true;
            } else {
                $blank = false;
                fwrite($f, PHP_EOL . trim($insertline));
            }
        }
        @fclose($f);
        return true; //success
    }

    /*
     * This function writes the vivio-swift htaccess content to a variable in order to be displayed to the admin
     */
    static function display_htaccess()
    {
        global $vivio_swift_global;
        $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Utility_Htaccess::display_htaccess() - Display .htaccess Init.", 0);
        $rules = $vivio_swift_global->util_htaccess->getrules();
        return $rules;
    }

    /*
     * This function will delete the code which has been added to the .htaccess file by this plugin
     * It will try to find the comment markers "# BEGIN Vivio Swift" and "# END Vivio Swift" and delete contents in between
     */
    static function delete_from_htaccess($section = 'Vivio Swift')
    {
        //TODO
        $htaccess = ABSPATH . '.htaccess';

        @ini_set('auto_detect_line_endings', true);
        if (!file_exists($htaccess)) {
            $ht = @fopen($htaccess, 'a+');
            @fclose($ht);
        }
        /* 
         * 
         Bug Fix: On some environments such as windows (xampp) this function was clobbering the non-aiowps-related .htaccess contents for certain cases.
         In some cases when WordPress saves the .htaccess file (eg, when saving permalink settings), 
         the line endings differ from the expected PHP_EOL endings. (WordPress saves with "\n" (UNIX style) but PHP_EOL may be set as "\r\n" (WIN/DOS))
         In this case exploding via PHP_EOL may not yield the result we expect.
         Therefore we need to do the following extra checks.
         */
        $ht_contents_imploded = implode('', file($htaccess));
        if(empty($ht_contents_imploded)){
            return 1;
        }else if(strstr($ht_contents_imploded, PHP_EOL)) {
            $ht_contents = explode(PHP_EOL, $ht_contents_imploded); //parse each line of file into array
        }else if(strstr($ht_contents_imploded, "\n")){
            $ht_contents = explode("\n", $ht_contents_imploded); //parse each line of file into array
        }else if(strstr($ht_contents_imploded, "\r")){
            $ht_contents = explode("\r", $ht_contents_imploded); //parse each line of file into array
        }else if(strstr($ht_contents_imploded, "\r\n")){
            $ht_contents = explode("\r\n", $ht_contents_imploded); //parse each line of file into array
        }
        
        if ($ht_contents) { //as long as there are lines in the file
            $state = true;
            if (!$f = @fopen($htaccess, 'w+')) {
                @chmod($htaccess, 0644);
                if (!$f = @fopen($htaccess, 'w+')) {
                    return -1;
                }
            }

            foreach ($ht_contents as $n => $markerline) { //for each line in the file
                if (strpos($markerline, '# BEGIN ' . $section) !== false) { //if we're at the beginning of the section
                    $state = false;
                }
                if ($state == true) { //as long as we're not in the section keep writing
                    fwrite($f, trim($markerline) . PHP_EOL);
                }
                if (strpos($markerline, '# END ' . $section) !== false) { //see if we're at the end of the section
                    $state = true;
                }
            }
            @fclose($f);
            return 1;
        }
        return 1;
    }

    static function getrules()
    {
        global $vivio_swift_global;

        $rules = '';
        $rules .= $vivio_swift_global->util_htaccess->getrules_excludes();
        $rules .= $vivio_swift_global->util_htaccess->getrules_cachecontrol();

        // Add opener and closer
        if ($rules != '') {
            $rules = $vivio_swift_global->util_htaccess->getrules_opener() . $rules . $vivio_swift_global->util_htaccess->getrules_closer();
        }

        return $rules;
    }

    static function getrules_opener()
    {
        $date = new DateTime();

        $rules = '';
        $rules .= "# BEGIN Vivio Swift [" . $date->format('Y-m-d H:i:s') . "]" . PHP_EOL;

        return $rules;
    }

    static function getrules_closer()
    {
        $rules = '';
        $rules .= "# END Vivio Swift" . PHP_EOL;
        
        return $rules;
    }

    static function getrules_excludes()
    {
        global $vivio_swift_global;

        $rules = '';
        $rules .= $vivio_swift_global->util_htaccess->getrules_exclude_basics();
        $rules .= $vivio_swift_global->util_htaccess->getrules_exclude_posts();
        $rules .= $vivio_swift_global->util_htaccess->getrules_exclude_query_strings();
        $rules .= $vivio_swift_global->util_htaccess->getrules_exclude_cookie_contains();
        $rules .= $vivio_swift_global->util_htaccess->getrules_exclude_user_agents();
        $rules .= $vivio_swift_global->util_htaccess->getrules_exclude_path_is();
        $rules .= $vivio_swift_global->util_htaccess->getrules_exclude_path_ends_with();
        $rules .= $vivio_swift_global->util_htaccess->getrules_exclude_path_contains();

        $custom_rules .= $vivio_swift_global->util_htaccess->getrules_custom_rules();
        if($vivio_swift_global->configs->get_value('vivio_swift_cache_custom_rules_at_top')=='1'){
            $rules = $custom_rules . $rules;
        }else{
            $rules .= $custom_rules;
        }

        // Add opener and closer
        if ($rules != '') {
            $rules = $vivio_swift_global->util_htaccess->getrules_excludes_opener() . $rules . $vivio_swift_global->util_htaccess->getrules_excludes_closer();
        }

        return $rules;
    }

    static function getrules_cachecontrol()
    {
        global $vivio_swift_global;

        $rules = '';

        if($vivio_swift_global->configs->get_value('vivio_swift_cache_control_headers_enable')){
            $cache_control_groups = $vivio_swift_global->configs->get_value('vivio_swift_cache_control_headers_values');
            $str_rule = '';
            foreach($cache_control_groups as $index=>$group){
                // make sure this group is active
                if(!$group['enable-cache']){continue;}

                $arr_extensions = $group['extensions'];

                // start building the FilesMatch rule string
                $str_rule = '<FilesMatch "\.(';

                // add the extensions for this group
                foreach ($arr_extensions as $i=>$extension) {
                    $str_rule .= $extension;
                    if($i!=(count($arr_extensions)-1)){$str_rule .='|';}
                }

                // finish the string for the rule
                $str_rule .= ')$">' . PHP_EOL;
                
                // set the cache-control header
                if(!$group['max-age']){
                    $str_rule .= '    Header unset Cache-Control' . PHP_EOL;
                } else {
                    $str_rule .= '    Header set Cache-Control "max-age='.$group['max-age'].'"' . PHP_EOL;
                }
                $str_rule .= '</FilesMatch>' . PHP_EOL;
                $rules .= $str_rule;
            }
        }
        
        if ($rules != '') {
            $rules = "<IfModule mod_headers.c>" . PHP_EOL . $rules . "</IfModule>" . PHP_EOL;
            $rules = Vivio_Swift_Utility_Htaccess::$vivio_swift_cachecontrol_marker_start . PHP_EOL . $rules . Vivio_Swift_Utility_Htaccess::$vivio_swift_cachecontrol_marker_end . PHP_EOL;
        }

        return $rules;
    }

    static function getrules_excludes_opener()
    {
        $rules = '';
        $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_excludes_marker_start . PHP_EOL; //Add feature marker start
        $rules .= '<IfModule mod_rewrite.c>' . PHP_EOL;
        $rules .= 'RewriteEngine On' . PHP_EOL;
        $rules .= 'RewriteBase /' . PHP_EOL;
        $rules .= 'AddDefaultCharset UTF-8' . PHP_EOL;

        return $rules;
    }

    static function getrules_excludes_closer()
    {
        // to support subdirectory installs, we need to get the subdirectory
        $parsed_url = parse_url(VIVIO_SWIFT_URL);

        $rules = '';
        
        // ensure the request URI ends in a /
        $rules .= 'RewriteCond %{REQUEST_URI} \/$' . PHP_EOL;

        // ensure a cache file exists
        $rules .= 'RewriteCond %{DOCUMENT_ROOT}'.$parsed_url['path'].VIVIO_SWIFT_CACHE_URL.'$1/d/index.html -f [or]' . PHP_EOL;
        $rules .= 'RewriteCond '.VIVIO_SWIFT_CACHE_PATH.'d/$1/index.html -f' . PHP_EOL;

        // send to cache file
        $rules .= 'RewriteRule ^(.*) "'.$parsed_url['path'].VIVIO_SWIFT_CACHE_URL.'d/$1/index.html" [L]' . PHP_EOL;

        // when we need more rules
        // $rules .= '' . PHP_EOL;
        $rules .= '</IfModule>' . PHP_EOL;
        $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_excludes_marker_end . PHP_EOL;

        return $rules;
    }

    static function getrules_exclude_basics()
    {
        global $vivio_swift_global;

        // basic rules are always added
        $rules = '';
        $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_basics_marker_start . PHP_EOL; //Add feature marker start
        
        // ensure the WAP profile is alphanumeric
        $rules .= 'RewriteCond %{HTTP:X-Wap-Profile} !^[a-z0-9\"]+ [NC]' . PHP_EOL;
        
        // ensure the HTTP profile is alphanumeric
        $rules .= 'RewriteCond %{HTTP:Profile} !^[a-z0-9\"]+ [NC]' . PHP_EOL;

        // when we need more rules
        // $rules .= '' . PHP_EOL;

        //add caching rules here when we're ready

        $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_basics_marker_end . PHP_EOL; //Add feature marker end

        return $rules;
    }

    static function getrules_exclude_posts()
    {
        global $vivio_swift_global;

        $rules = '';

        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_posts')=='1')
        {
            $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_excludes_posts_start . PHP_EOL; //Add feature marker start
            
            // don't use cache on POST hits
            $rules .= 'RewriteCond %{REQUEST_METHOD} !POST' . PHP_EOL;

            $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_excludes_posts_end . PHP_EOL; //Add feature marker end
        }

        return $rules;
    }

    static function getrules_exclude_query_strings()
    {
        global $vivio_swift_global;

        $rules = '';

        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_query_strings')=='1')
        {
            $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_query_strings_start . PHP_EOL; //Add feature marker start
            
            // don't use cache on requests with query strings
            $rules .= 'RewriteCond %{QUERY_STRING} ^$' . PHP_EOL;

            $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_query_strings_end . PHP_EOL; //Add feature marker end
        }

        return $rules;
    }

    static function getrules_exclude_cookie_contains()
    {
        global $vivio_swift_global;
        $rules = '';
        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_cookie_contains')=='1')
        {
            $arr_cookie_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_cookie_values');
            if(!is_array($arr_cookie_values)){$arr_cookie_values = array();}
            if(count($arr_cookie_values)){
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_cookie_contains_start . PHP_EOL; //Add feature marker start
                foreach ($arr_cookie_values as $i => $value) {
                    $rules .= 'RewriteCond %{HTTP:Cookie} !^.*('.$value.').*$' . PHP_EOL;
                }
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_cookie_contains_end . PHP_EOL; //Add feature marker end
            }
        }
        return $rules;
    }

    static function getrules_exclude_user_agents()
    {
        global $vivio_swift_global;
        $rules = '';
        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_user_agent')=='1')
        {
            $arr_user_agents = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_user_agent_values');
            if(!is_array($arr_user_agents)){$arr_user_agents = array();}
            if(count($arr_user_agents)){
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_user_agent_start . PHP_EOL; //Add feature marker start
                foreach ($arr_user_agents as $i => $value) {
                    $rules .= 'RewriteCond %{HTTP_USER_AGENT} !^.*('.$value.').*$' . PHP_EOL;
                }
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_user_agent_end . PHP_EOL; //Add feature marker end
            }
        }
        return $rules;
    }

    static function getrules_exclude_path_is()
    {
        global $vivio_swift_global;
        $rules = '';
        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_is')=='1')
        {
            $arr_path_is_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_is_values');
            if(!is_array($arr_path_is_values)){$arr_path_is_values = array();}
            if(count($arr_path_is_values)){
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_path_is_start . PHP_EOL; //Add feature marker start
                foreach ($arr_path_is_values as $i => $value) {
                    $rules .= 'RewriteCond %{REQUEST_URI} !^/'.$value.'/$' . PHP_EOL;
                }
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_path_is_end . PHP_EOL; //Add feature marker end
            }

        }

        return $rules;
    }

    static function getrules_exclude_path_ends_with()
    {
        global $vivio_swift_global;
        $rules = '';
        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_ends_with')=='1')
        {
            $arr_path_ends_with_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_ends_with_values');
            if(!is_array($arr_path_ends_with_values)){$arr_path_ends_with_values = array();}
            if(count($arr_path_ends_with_values)){
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_path_ends_with_start . PHP_EOL; //Add feature marker start
                foreach ($arr_path_ends_with_values as $i => $value) {
                    $rules .= 'RewriteCond %{REQUEST_URI} !\.('.$value.')$' . PHP_EOL;
                    // don't rewrite static content
                    //$rules .= 'RewriteCond %{REQUEST_URI} !\.(html|jpg|gif|css|js)$' . PHP_EOL;
                }
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_path_ends_with_end . PHP_EOL; //Add feature marker end
            }
        }
        return $rules;
    }

    static function getrules_exclude_path_contains()
    {
        global $vivio_swift_global;
        $rules = '';
        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_contains')=='1')
        {
            $arr_path_contains_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_contains_values');
            if(!is_array($arr_path_contains_values)){$arr_path_contains_values = array();}
            if(count($arr_path_contains_values)){
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_path_contains_start . PHP_EOL; //Add feature marker start
                foreach ($arr_path_contains_values as $i => $value) {
                    $rules .= 'RewriteCond %{REQUEST_URI} !^.*('.$value.').*$' . PHP_EOL;
                }
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_exclude_path_contains_end . PHP_EOL; //Add feature marker end
            }
        }
        return $rules;
    }

    static function getrules_custom_rules()
    {
        global $vivio_swift_global;
        $rules = '';
        if ($vivio_swift_global->configs->get_value('vivio_swift_cache_enable_custom_rules')=='1')
        {
            $arr_custom_rules_values = $vivio_swift_global->configs->get_value('vivio_swift_cache_custom_rules_values');
            if(!is_array($arr_custom_rules_values)){$arr_custom_rules_values = array();}
            if(count($arr_custom_rules_values)){
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_custom_rules_start . PHP_EOL; //Add feature marker start
                foreach ($arr_custom_rules_values as $i => $value) {
                    $rules .= 'RewriteCond '.$value . PHP_EOL;
                }
                $rules .= Vivio_Swift_Utility_Htaccess::$vivio_swift_custom_rules_end . PHP_EOL; //Add feature marker end
            }
        }
        return $rules;
    }

}