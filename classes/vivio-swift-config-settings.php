<?php

class Vivio_Swift_Config_Settings
{
    // environment vars
    private $has_apache;
    private $has_modrewrite;
    private $has_modheader;

    // functional vars
    private $can_cache;
    private $can_header;

    function __construct()
    {
        // NOP
    }
    
    static function add_value_configs()
    {
        global $vivio_swift_global;
        Vivio_Swift_Config_Settings::default_environment_tests();

        // Debug
        $vivio_swift_global->configs->add_value('vivio_swift_enable_debug','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_debug_level','3');//0-5 3=warn

        // Cache Response HTML comment
        $vivio_swift_global->configs->add_value('vivio_swift_enable_cache_comment','');//Checkbox

        // Query Strings
        $vivio_swift_global->configs->add_value('vivio_swift_remove_query_strings','1');//Checkbox

        // On-Access Cache
        $vivio_swift_global->configs->add_value('vivio_swift_cache_enabled',$can_cache?'1':'');//Checkbox

        // Preload Cache
        $vivio_swift_global->configs->add_value('vivio_swift_preload_cache_enabled',$can_cache?'1':'');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_preload_cache_expire_hours','1');
        $vivio_swift_global->configs->add_value('vivio_swift_preload_last_run_date','');

        // htaccess rules
        $vivio_swift_global->configs->add_value('vivio_swift_cache_custom_rules_at_top','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_posts','1');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_query_strings','1');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_cookie_contains','1');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_cookie_values',array('comment_author_','wordpress_logged_in','wp-postpass_'));//array list
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_user_agent','1');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_user_agent_values',array(VIVIO_SWIFT_CACHE_BOT_NAME));//array list
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_path_is','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_path_is_values',array());//array list
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_path_ends_with','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_path_ends_with_values',array('.html','.png','.jpg','.jpeg','.gif','.ico','.svg','.xml','.json'));//array list
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_path_contains','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_exclude_path_contains_values',array());//array list
        $vivio_swift_global->configs->add_value('vivio_swift_cache_enable_custom_rules','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_custom_rules_values',array());//array list

        // htaccess cache-control
        $vivio_swift_global->configs->add_value('vivio_swift_cache_control_headers_enable','0');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_cache_control_headers_values',Vivio_Swift_Config_Settings::default_cache_control_groups());

        // Compression options
        $vivio_swift_global->configs->add_value('vivio_swift_compress_minify_css','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_compress_minify_js','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_compress_minify_html','');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_compress_combine_css','1');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_compress_combine_js','1');//Checkbox

        // refresh events
        $vivio_swift_global->configs->add_value('vivio_swift_refresh_on_post_new','1');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_refresh_on_post_update','1');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_refresh_on_category_change','1');//Checkbox
        $vivio_swift_global->configs->add_value('vivio_swift_refresh_on_tag_change','1');//Checkbox

	    //Done. Save it.
        $vivio_swift_global->configs->save_config();
    }
    
    static function reset_to_defaults()
    {
        global $vivio_swift_global;
        Vivio_Swift_Config_Settings::default_environment_tests();

        $vivio_swift_global->configs->reset_config();

        // Debug
        $vivio_swift_global->configs->set_value('vivio_swift_enable_debug','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_debug_level','3');//0-5 3=warn

        // Cache Response HTML comment
        $vivio_swift_global->configs->set_value('vivio_swift_enable_cache_comment','');//Checkbox

        // Query Strings
        $vivio_swift_global->configs->set_value('vivio_swift_remove_query_strings','1');//Checkbox

        // On-Access Cache
        $vivio_swift_global->configs->set_value('vivio_swift_cache_enabled',$can_cache?'1':'');//Checkbox

        // Preload Cache
        $vivio_swift_global->configs->set_value('vivio_swift_preload_cache_enabled',$can_cache?'1':'');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_preload_cache_expire_hours','1');
        $vivio_swift_global->configs->set_value('vivio_swift_preload_last_run_date','');

        // htaccess rules
        $vivio_swift_global->configs->set_value('vivio_swift_cache_custom_rules_at_top','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_posts','1');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_query_strings','1');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_cookie_contains','1');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_cookie_values',array('comment_author_','wordpress_logged_in','wp-postpass_'));//array list
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_user_agent','1');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_user_agent_values',array(VIVIO_SWIFT_CACHE_BOT_NAME));//array list
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_is','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_is_values',array());//array list
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_ends_with','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_ends_with_values',array('.html','.png','.jpg','.jpeg','.gif','.ico','.svg','.xml','.json'));//array list
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_contains','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_contains_values',array());//array list
        $vivio_swift_global->configs->set_value('vivio_swift_cache_enable_custom_rules','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_custom_rules_values',array());//array list

        // htaccess cache-control
        $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_enable','0');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_values',Vivio_Swift_Config_Settings::default_cache_control_groups());

        // Compression options
        $vivio_swift_global->configs->set_value('vivio_swift_compress_minify_css','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_compress_minify_js','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_compress_minify_html','');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_compress_combine_css','1');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_compress_combine_js','1');//Checkbox

        // refresh events
        $vivio_swift_global->configs->set_value('vivio_swift_refresh_on_post_new','1');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_refresh_on_post_update','1');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_refresh_on_category_change','1');//Checkbox
        $vivio_swift_global->configs->set_value('vivio_swift_refresh_on_tag_change','1');//Checkbox

        // Done. Save it.
        $vivio_swift_global->configs->save_config();
    }

    static function default_cache_control_groups()
    {
        $groups = array(
            0 => [  'name'          => 'Image Files',
                    'max-age'       => '2419200', // 1 month
                    'enable-cache'  => '1', 
                    'extensions'    => array('png','jpg','jpeg','gif','ico','svg')
            ],
            1 => [  'name'          => 'Static Files',
                    'max-age'       => '604800', // 1 week
                    'enable-cache'  => '1', 
                    'extensions'    => array('css','js','pdf','txt','csv')
            ],
            2 => [  'name'          => 'HTML Files',
                    'max-age'       => '86400', // 1 day
                    'enable-cache'  => '1', 
                    'extensions'    => array('html','htm')
            ],
            3 => [  'name'          => 'Dynamic Files',
                    'max-age'       => '0', // no cache
                    'enable-cache'  => '0', 
                    'extensions'    => array('php')
            ]
        );
        return $groups;
    }

    static function default_environment_tests()
    {
        global $vivio_swift_global;

        // perform environment tests to determine ability
        $has_apache = boolval($vivio_swift_global->util_apache->apache_version());
        $has_modrewrite = boolval($vivio_swift_global->util_apache->test_mod_rewrite());
        $has_modheader = boolval($vivio_swift_global->util_apache->test_mod_headers());

        // combine tests to determine functionality
        $can_cache = (($has_apache && $has_modrewrite)?true:false);
        $can_header = ($has_modheader?true:false);

    }

}