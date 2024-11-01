<?php

class Vivio_Swift_Utility_CSS
{
   	function __construct($html_body)
    {
    	$this->dom = new DomDocument;
    	$this->dom->preserveWhiteSpace=false;
    	$this->dom->loadHTML($html_body);
    	$this->css_content="";
    }
    
    function combine_css_files(){
    	global $vivio_swift_global;

    }

    function create_css_file_list(){
    	$link_tags = $this->dom->getElementsByTagName('link');

    }

    function return_css_from_url(){

    }

    function remove_css_links(){

    }

}