<?php

abstract class Vivio_Swift_Admin_Messages
{
    function show_msg_settings_updated()
    {
        echo '<div id="message" class="updated fade"><p><strong>';
        _e('Settings successfully updated.','vivio-swift');
        echo '</strong></p></div>';
    }
    
    static function show_msg_preload_scheduled()
    {
        echo '<div id="message" class="updated fade"><p><strong>';
        _e('New preload cache process has been scheduled...','vivio-swift');
        echo '</strong></p></div>';
    }
    
    static function show_msg_record_deleted_st()
    {
        echo '<div id="message" class="updated fade"><p><strong>';
        _e('The selected record(s) deleted successfully!','vivio-swift');
        echo '</strong></p></div>';
    }
    
    function show_msg_updated($msg)
    {
        echo '<div id="message" class="updated fade"><p><strong>';
        echo $msg;
        echo '</strong></p></div>';
    }
    
    static function show_msg_updated_st($msg)
    {
        echo '<div id="message" class="updated fade"><p><strong>';
        echo $msg;
        echo '</strong></p></div>';
    }
    
    function show_msg_error($error_msg)
    {
        echo '<div id="message" class="error"><p><strong>';
        echo $error_msg;
        echo '</strong></p></div>';
    }
    
    static function show_msg_error_st($error_msg)
    {
        echo '<div id="message" class="error"><p><strong>';
        echo $error_msg;
        echo '</strong></p></div>';
    }
}