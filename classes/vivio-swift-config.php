<?php
class Vivio_Swift_Config{
    var $configs;
    static $_this;
    
    function __construct(){
    }

    function load_config(){	
	   $this->configs = get_option('vivio_swift_configs');
    }
	
    function get_value($key){
    	return isset($this->configs[$key])?$this->configs[$key] : '';    	
    }
    
    // HARD set a config value no matter what
    function set_value($key, $value){
    	$this->configs[$key] = $value;
    }
    
    // SOFT set a config value if it doesn't already exist
    function add_value($key, $value){
        if(!is_array($this->configs)){$this->configs = array();}

    	if (array_key_exists($key, $this->configs)){
            //Don't update the value for this key
    	}
        else{//It is safe to update the value for this key
            $this->configs[$key] = $value;
    	}    	
    }

    function delete_value($key){
        //If key exists, remove it.
        if (array_key_exists($key, $this->configs)){
            unset($this->configs[$key]);
        }
    }

    function reset_config(){
        delete_option('vivio_swift_configs');
        $this->load_config();
    }

    function save_config(){
    	update_option('vivio_swift_configs', $this->configs);
    }
    
    function get_instance(){
    	if(empty(self::$_this)){
            self::$_this = new Vivio_Swift_Config();
            self::$_this->load_config();
            return self::$_this;
    	}
    	return self::$_this;
    }
}