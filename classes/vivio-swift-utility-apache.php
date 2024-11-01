<?php

class Vivio_Swift_Utility_Apache {
	function __construct()
	{

	}

	function apache_version()
	{
		return apache_get_version();
	}

	function apache_module_exists($module)
	{
		return in_array($module, apache_get_modules());
	}

	function test_mod_rewrite()
	{
		if($this->apache_version()){
			return $this->apache_module_exists('mod_rewrite');
		}else{
			return false;
		};
	}

	function test_mod_headers()
	{
		if($this->apache_version()){
			return $this->apache_module_exists('mod_headers');
		}else{
			return false;
		};
	}
}