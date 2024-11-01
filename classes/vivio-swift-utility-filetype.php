<?php

class Vivio_Swift_Utility_FileType
{
    function __construct()
    {

    }

	function isJSON($str)
	{
		$json = json_decode($str);
		return $json && $str != $json;
	}

	function isXML($str)
	{
		// no good way to check for valid XML in PHP? Using this for now...
		if(!(substr($output, 0, 5) == "<?xml")) {
			return false;
		}
	}

}