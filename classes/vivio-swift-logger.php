<?php
/* 
 * Logs debug data to a file with the following usage:
 *
 * global $vivio_swift_global;
 * $vivio_swift_global->debug_logger->log_debug("Log messaged goes here");
 */
class Vivio_Swift_Logger
{
    var $log_folder_path;
    var $log_level;
    var $default_log_file = 'vivio-swift-log.txt';
    var $default_log_file_cron = 'vivio-swift-log-cron-job.txt';
    var $debug_enabled = false;
    var $debug_status = array('DEBUG','STATUS','NOTICE','WARNING','FAILURE','CRITICAL');
    var $section_break_marker = "\n----------------------------------------------------------\n\n";
    var $log_reset_marker = "-------- Log File Reset --------\n";

    function __construct($debug_enabled,$level=3)
    {
        $this->debug_enabled = $debug_enabled;
        $this->log_level = $level;
        $this->log_folder_path = VIVIO_SWIFT_PATH . '/logs';
    }
    
    function get_debug_timestamp()
    {
        return '['.date('m/d/Y g:i A').'] - ';
    }
    
    function get_debug_status($level)
    {
        return isset($this->debug_status[$level]) ? $this->debug_status[$level] : 'UNKNOWN';
    }
    
    function get_section_break($section_break)
    {
        if ($section_break) {
            return $this->section_break_marker;
        }
        return "";
    }

    /*
     * Anything higher than the selected log level will be logged. 
     *
     * Log level guidelines:
     *
     * DEBUG - intended to be used by devs for information needed during development
     * STATUS - logs when/where things happen within the plugin during normal function
     * NOTICE - logs when things do/don't happen for specific reasons
     * WARNING - logs non-blocking errors (like when a variable isn't created yet)
     * FAILURE - logs function-blocking errors (may or may not be blocking)
     * CRITICAL - logs blocking errors that prevent functionality
     */
    function set_log_level($level=3)
    {
        $this->log_level = $level;
    }

    function log_level_valid($level)
    {
        // boolean if the level passed is greater than the current log level
        $loglevel = $this->log_level;
        if ($level >= $loglevel){
            return true;
        } else {
            return false;
        }
    }
    
    function append_to_file($content,$file_name)
    {
        if(empty($file_name))$file_name = $this->default_log_file;
        $debug_log_file = $this->log_folder_path.'/'.$file_name;
        if (is_writeable($debug_log_file)){
            $fp=fopen($debug_log_file,'a');
            fwrite($fp, $content);
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }
    
    function reset_log_file($file_name='')
    {
        if(empty($file_name))$file_name = $this->default_log_file;
        if($file_name=='1')$file_name = $this->default_log_file;
        if($file_name=='2')$file_name = $this->default_log_file_cron;
        $debug_log_file = $this->log_folder_path.'/'.$file_name;
        $content = $this->get_debug_timestamp().$this->log_reset_marker;
        if (is_writeable($debug_log_file)){
            $fp=fopen($debug_log_file,'w');
            fwrite($fp, $content);
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }

    function log_debug($message,$level=0,$section_break=false,$file_name='')
    {
        if (!$this->debug_enabled) return;
        if (!$this->log_level_valid($level)) return;
        $content = $this->get_debug_timestamp();//Timestamp
        $content .= $this->get_debug_status($level);//Debug status
        $content .= ' : ';
        $content .= $message . "\n";
        $content .= $this->get_section_break($section_break);
        $this->append_to_file($content, $file_name);
    }

    function log_debug_cron($message,$level=0,$section_break=false)
    {
        $this->log_debug($message, $level, $section_break, $this->default_log_file_cron);
    }

}