<?php

require_once(dirname(__FILE__) . '/vivio-swift-config-settings.php');

class Vivio_Swift_Activator{

	public static function activate()
    {

		// hold-off on multi-site support for now.
        /*
        global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
            $current_blog = $wpdb->blogid;
            // loop over each blog in the multisite
            $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
            foreach ($blogids as $blog_id){
                switch_to_blog($blog_id);
                Vivio_Swift_Config_Settings::add_value_configs();
            }
            switch_to_blog($current_blog);
            return;
        }
        */
        
        // set default configs
        Vivio_Swift_Config_Settings::add_value_configs();


        // configure cron handlers
        /*
        if ( !wp_next_scheduled('vivio_swift_hourly_cron_event') ) {
            wp_schedule_event(time(), 'hourly', 'vivio_swift_hourly_cron_event'); //schedule an hourly cron event
        }
        if ( !wp_next_scheduled('vivio_swift_daily_cron_event') ) {
            wp_schedule_event(time(), 'daily', 'vivio_swift_daily_cron_event'); //schedule an daily cron event
        }
        */
	}

}
