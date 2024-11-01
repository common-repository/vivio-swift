<?php

require_once(dirname(__FILE__) . '/vivio-swift-config-settings.php');

class Vivio_Swift_Deactivator {
	
	public static function deactivate() {

		global $vivio_swift_global;
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			// hold-off on multi-site support for now.
		}

		// clear out scheduled tasks
		//wp_clear_scheduled_hook('vivio_swift_hourly_cron_event');
		//wp_clear_scheduled_hook('vivio_swift_daily_cron_event');

		// clear out cached files
		Vivio_Swift_Utility_File::clear_cache_dir();

		// clear options config
		delete_option('vivio_swift_configs');

	}

}
