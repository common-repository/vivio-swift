<?php

class Vivio_Swift_Cron {
	function __construct()
	{
		//add_action ('vivio_swift_hourly_cron_event', array(&$this, 'vivio_swift_hourly_cron_event_list'));
		//add_action ('vivio_swift_daily_cron_event', array(&$this, 'vivio_swift_daily_cron_event_list'));
	}

	function vivio_swift_hourly_cron_event_list()
	{
		// run these hourly
		//do_action('vivio_swift_schedule_preload_cache');
	}

	function vivio_swift_daily_cron_event_list()
	{
		// run these daily
	}
}