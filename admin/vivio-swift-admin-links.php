<?php

class Vivio_Swift_Admin_Links
{
    var $arr_links = array(
        "help_mod_headers" => "https://kb.viviotech.net/display/KB/How+to+Install+mod_headers",
    );

    function get_link($name='')
    {
        return $this->arr_links[$name];
    }
}