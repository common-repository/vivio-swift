<?php
class Vivio_Swift_Cache_Extras
{
	function __construct()
    {
    	// remove query strings
    	$this->remove_query_strings();

        add_action('vivio_swift_create_preload_cache', array(&$this, 'schedule_create_preload_cache'));
    }

    function remove_query_strings_qmark($src)
    {
		$rqs = explode('?ver', $src);
		return $rqs[0];
    }

    function remove_query_strings_amp($src)
    {
		$rqs = explode('&ver', $src);
		return $rqs[0];
    }

    function remove_query_strings()
    {
    	global $vivio_swift_global;

    	if( ($vivio_swift_global->configs->get_value('vivio_swift_remove_query_strings')=='1') && (! is_admin()) ){
	    	add_filter( 'script_loader_src', array(&$this,'remove_query_strings_qmark'), 15, 1 );
			add_filter( 'style_loader_src', array(&$this,'remove_query_strings_qmark'), 15, 1 );
	    	add_filter( 'script_loader_src', array(&$this,'remove_query_strings_amp'), 15, 1 );
			add_filter( 'style_loader_src', array(&$this,'remove_query_strings_amp'), 15, 1 );
    	}
    }

}