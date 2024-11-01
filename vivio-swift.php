<?php
/*
Plugin Name:  Vivio Swift
Plugin URI:   https://code.viviotech.net/wp/vivio-swift
Description:  Make your WordPress sites fly with simple yet powerful acceleration tools like caching, compression, and simplification.
Version:      2020040301
Author:       Vivio Technologies
Author URI:   https://viviotech.net
License:      GPL3
Text Domain:  vivio-swift
Domain Path:  /languages

Copyright (C)2018-2020 Vivio Technologies

Additional Contributers, Copyrights, & Inspiration:
Tips and Tricks HQ, wpsolutions, Peter Petreski, Ruhul Amin, mbrsolution, chesio

*/

if(!defined('ABSPATH')){
    exit;//Exit if accessed directly
}

if (!class_exists('Vivio_Swift')){

	class Vivio_Swift{

		var $version = '2020040301';
	    var $plugin_url;
	    var $plugin_path;
	    var $configs;
	    var $admin_init;
	    var $debug_logger;
	    var $cron_handler;

	    function __construct()
	    {
	    	$this->load_configs();
	    	$this->define_constants();
	    	$this->classes();
        	$this->loader_operations();

        	add_action('init', array(&$this, 'vivio_swift_plugin_init'), 0);
        	// post refresh events
        	add_action('transition_post_status', array(&$this, 'vivio_swift_post_transitions'), 10, 3 );
        	// tag (term) refresh events
        	add_action('created_term', array(&$this, 'vivio_swift_created_term'), 10, 3 );
			add_action('edited_term', array(&$this, 'vivio_swift_edited_term'), 10, 3 );
			add_action('delete_term', array(&$this, 'vivio_swift_delete_term'), 10, 5 );
        	// category (taxonomy) refresh events
        	add_action('create_{$taxonomy}', array(&$this, 'vivio_swift_create_taxonomy'), 10, 3 );
			add_action('edit_{$taxonomy}', array(&$this, 'vivio_swift_edit_taxonomy'), 10, 3 );
			add_action('delete_{$taxonomy}', array(&$this, 'vivio_swift_delete_taxonomy'), 10, 5 );
	    }
    
	    function load_configs()
	    {
			require_once(plugin_dir_path( __FILE__ ) . 'classes/vivio-swift-config.php');
			$this->configs = Vivio_Swift_Config::get_instance();
	    }

	    function activate_handler()
	    {
			require_once(plugin_dir_path( __FILE__ ) . 'classes/vivio-swift-activator.php');
			Vivio_Swift_Activator::activate();
		}

		function deactivate_handler()
		{
			require_once(plugin_dir_path( __FILE__ ) . 'classes/vivio-swift-deactivator.php');
			Vivio_Swift_Deactivator::deactivate();
		}

		function define_constants()
	    {
	        define('VIVIO_SWIFT_VERSION', $this->version);
	        define('VIVIO_SWIFT_URL', plugin_dir_url( __FILE__ ));
	        define('VIVIO_SWIFT_PATH', plugin_dir_path( __FILE__ ));
	        define('VIVIO_SWIFT_CACHE_URL', 'cache/'); // must inc trailing slash
	        define('VIVIO_SWIFT_CACHE_PATH', VIVIO_SWIFT_PATH.'cache/'); // must inc trailing slash
	        define('VIVIO_SWIFT_CACHE_BOT_NAME', 'vivio_swift_cache_bot');
	        define('VIVIO_SWIFT_BACKUPS_PATH', VIVIO_SWIFT_PATH.'backups/');
	        define('VIVIO_SWIFT_MENU_SLUG_PREFIX', 'vivio-swift');
	        define('VIVIO_SWIFT_MAIN_MENU_SLUG', 'vivio-swift');
	        define('VIVIO_SWIFT_CACHE_MENU_SLUG', 'vivio-swift-cache');
	        define('VIVIO_SWIFT_COMPRESS_MENU_SLUG', 'vivio-swift-compress');
	        define('VIVIO_SWIFT_IMAGES_MENU_SLUG', 'vivio-swift-images');
	        define('VIVIO_SWIFT_EXCLUSIONS_MENU_SLUG', 'vivio-swift-exclusions');
	        define('VIVIO_SWIFT_CAPABILITY', 'manage_options');
	        define('VIVIO_SWIFT_LOG_FILE', 'vivio-swift-log.txt');

	    }

	    function classes()
	    {
	        require_once('classes/vivio-swift-logger.php');
	        require_once('classes/vivio-swift-utility.php');
	        require_once('classes/vivio-swift-utility-apache.php');
	        require_once('classes/vivio-swift-utility-async.php');
	        require_once('classes/vivio-swift-utility-bgproc.php');
	        require_once('classes/vivio-swift-utility-htaccess.php');
	        require_once('classes/vivio-swift-utility-ip-address.php');
	        require_once('classes/vivio-swift-utility-css.php');
	        require_once('classes/vivio-swift-utility-file.php');
	        require_once('classes/vivio-swift-utility-date.php');
	        require_once('classes/vivio-swift-utility-filetype.php');
	        require_once('classes/vivio-swift-utility-minify-tovic.php');
	        require_once('classes/vivio-swift-compress.php');
	        require_once('classes/vivio-swift-cache.php');
	        require_once('classes/vivio-swift-cache-excludes.php');
	        require_once('classes/vivio-swift-cache-extras.php');
	        require_once('classes/vivio-swift-cache-onaccess.php');
	        require_once('classes/vivio-swift-cache-preload.php');
	        require_once('classes/vivio-swift-proc-preload.php');
	        require_once('classes/vivio-swift-cron.php');
	        
	        if (is_admin()){ // admin files
	            require_once('classes/vivio-swift-config-settings.php');
	            require_once('admin/vivio-swift-admin.php');
	            require_once('admin/vivio-swift-admin-table.php');
	        }
	        else{ // front-end files
	        }
	    }

		function loader_operations()
		{
			// set debugging
			$debug_config = $this->configs->get_value('vivio_swift_enable_debug');
			$debug_enabled = empty($debug_config)?false:true;
			$debug_level = $this->configs->get_value('vivio_swift_debug_level');

			// init objects
			$this->debug_logger = new Vivio_Swift_Logger($debug_enabled,$debug_level);
			$this->cron_obj = new Vivio_Swift_Cron();
			$this->util = new Vivio_Swift_Utility();
	        $this->util_apache = new Vivio_Swift_Utility_Apache();
	    	$this->util_htaccess = new Vivio_Swift_Utility_Htaccess();
	        $this->util_file = new Vivio_Swift_Utility_File();
	        $this->util_date = new Vivio_Swift_Utility_Date();

	        // define non-blocking processes
	        $this->proc_preload = new Vivio_Swift_Process_Preload();

			if(is_admin()){
			    $this->admin_init = new Vivio_Swift_Admin();
			}
		}

		function vivio_swift_plugin_init()
		{
			// localization
			$locale = apply_filters( 'plugin_locale', get_locale(), 'vivio-swift' );
			load_textdomain( 'vivio-swift', WP_LANG_DIR . "/vivio-swift-$locale.mo" );
			load_plugin_textdomain('vivio-swift', false, dirname(plugin_basename(__FILE__ )) . '/languages/');

			$this->cache_obj = new Vivio_Swift_Cache();

			$cache_enabled = ($this->configs->get_value('vivio_swift_cache_enabled')=='1')?true:false;
			$preload_enabled = ($this->configs->get_value('vivio_swift_preload_cache_enabled')=='1')?true:false;
			$preload_last_run = $this->configs->get_value('vivio_swift_preload_last_run_date');

			// if preload_last_run is blank and preload is on, this is likely an activation or reset, refresh preload
			if ($preload_enabled && ($preload_last_run=='')){
				$this->cache_obj->cache_preload->create_preload_cache(1);
			}
			
			// don't cache the admin user
			if (!is_admin()){
				$is_bot = preg_match("/".VIVIO_SWIFT_CACHE_BOT_NAME."/i", $_SERVER['HTTP_USER_AGENT'])?true:false;
				
				// don't cache our bot
				if (!$is_bot){
					if ($preload_enabled){ // preload
						$this->debug_logger->log_debug("Vivio_Swift_Cache::vivio_swift_plugin_init() - Preload Cache Init.",0);
						$this->cache_obj->cache_preload->create_preload_cache();
					}

					if ($cache_enabled){ // on-access
						$this->debug_logger->log_debug("Vivio_Swift_Cache::vivio_swift_plugin_init() - On-Access Cache Init.",0);
						$this->cache_obj->cache_onaccess->create_page_cache();
					}
				}
			}
		}

		// refresh event functions
	    function vivio_swift_post_transitions($new_status, $old_status, $post){
	        $this->cache_obj->process_post_transitions($new_status, $old_status, $post);
	    }

	    function vivio_swift_created_term($term_id, $tt_id, $taxonomy){
	    	$this->cache_obj->process_term_change($term_id, $tt_id, $taxonomy);
	    }

	    function vivio_swift_edited_term($term_id, $tt_id, $taxonomy){
	    	$this->cache_obj->process_term_change($term_id, $tt_id, $taxonomy);
	    }

	    function vivio_swift_delete_term($term, $tt_id, $taxonomy, $deleted_term, $object_ids){
	    	$this->cache_obj->process_term_change($term, $tt_id, $taxonomy);
	    }

	    function vivio_swift_create_taxonomy($term_id, $tt_id){
	    	$this->cache_obj->process_taxonomy_change($term_id, $tt_id);
	    }

	    function vivio_swift_edit_taxonomy($term_id, $tt_id){
	    	$this->cache_obj->process_taxonomy_change($term_id, $tt_id);
	    }

	    function vivio_swift_delete_taxonomy($term, $tt_id, $deleted_term, $object_ids){
	    	$this->cache_obj->process_taxonomy_change($term, $tt_id);
	    }

	} // close class

} // close class exists check

function vivio_swift_add_dashboard_link($links, $file) 
{
    if ($file == plugin_basename(__FILE__)){
            $dashboard_link = '<a href="admin.php?page='.VIVIO_SWIFT_MAIN_MENU_SLUG.'">Dashboard</a>';
            array_unshift($links, $dashboard_link);
    }
    return $links;
}

add_filter('plugin_action_links', 'vivio_swift_add_dashboard_link', 10, 2 );

$GLOBALS['vivio_swift_global'] = new Vivio_Swift();

register_activation_hook(__FILE__,array('Vivio_Swift','activate_handler'));
register_deactivation_hook(__FILE__,array('Vivio_Swift','deactivate_handler'));