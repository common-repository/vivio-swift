<?php

class Vivio_Swift_Utility
{
    function __construct()
    {

    }

    /**
     * Explode $string with $delimiter, trim all lines and filter out empty ones.
     * @param string $string
     * @param string $delimiter
     * @return array
     */
    static function explode_trim_filter_empty($string, $delimiter = PHP_EOL) {
        return array_filter(array_map('trim', explode($delimiter, $string)), 'strlen');
    }

    /**
     * Returns the current URL
     * 
     * @return string
     */
    static function get_current_page_url()
    {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    /**
     * Redirects to specified URL
     * 
     * @param type $url
     * @param type $delay
     * @param type $exit
     */
    static function redirect_to_url($url, $delay = '0', $exit = '1')
    {
        if (empty($url)) {
            echo "<br /><strong>Error: URL value is empty.</strong>";
            exit;
        }
        if (!headers_sent()) {
            header('Location: ' . $url);
        } else {
            echo '<meta http-equiv="refresh" content="' . $delay . ';url=' . $url . '" />';
        }
        if ($exit == '1') {
            exit;
        }
    }

    /**
     * Checks if a particular username exists in the WP Users table
     * @global type $wpdb
     * @param type $username
     * @return boolean
     */
    static function check_user_exists($username)
    {
        global $wpdb;

        //if username is empty just return false
        if ($username == '') {
            return false;
        }

        //If multisite 
        if (Vivio_Swift_Utility::is_multisite_install()) {
            $blog_id = get_current_blog_id();
            $admin_users = get_users('blog_id=' . $blog_id . '&orderby=login&role=administrator');
            foreach ($admin_users as $user) {
                if ($user->user_login == $username) {
                    return true;
                }
            }
            return false;
        }

        //check users table
        $sanitized_username = sanitize_text_field($username);
        $sql_1 = $wpdb->prepare("SELECT user_login FROM $wpdb->users WHERE user_login=%s", $sanitized_username);
        $user_login = $wpdb->get_var($sql_1);
        if ($user_login == $sanitized_username) {
            return true;
        } else {
            //make sure that the sanitized username is an integer before comparing it to the users table's ID column
            $sanitized_username_is_an_integer = (1 === preg_match('/^\d+$/', $sanitized_username));
            if ($sanitized_username_is_an_integer) {
                $sql_2 = $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE ID=%d", intval($sanitized_username));
                $userid = $wpdb->get_var($sql_2);
                return ($userid == $sanitized_username);
            } else {
                return false;
            }
        }
    }


    /**
     * Generates a random alpha-numeric string
     * @param type $string_length
     * @return string
     */
    static function generate_alpha_numeric_random_string($string_length)
    {
        //Charecters present in table prefix
        $allowed_chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $string = '';
        //Generate random string
        for ($i = 0; $i < $string_length; $i++) {
            $string .= $allowed_chars[rand(0, strlen($allowed_chars) - 1)];
        }
        return $string;
    }


    /**
     * Generates a random string using a-z characters
     * @param type $string_length
     * @return string
     */
    static function generate_alpha_random_string($string_length)
    {
        //Charecters present in table prefix
        $allowed_chars = 'abcdefghijklmnopqrstuvwxyz';
        $string = '';
        //Generate random string
        for ($i = 0; $i < $string_length; $i++) {
            $string .= $allowed_chars[rand(0, strlen($allowed_chars) - 1)];
        }
        return $string;
    }

    /**
     * Sets cookie
     * @param type $cookie_name
     * @param type $cookie_value
     * @param type $expiry_seconds
     * @param type $path
     * @param string $cookie_domain
     */
    static function set_cookie_value($cookie_name, $cookie_value, $expiry_seconds = 86400, $path = '/', $cookie_domain = '')
    {
        $expiry_time = time() + intval($expiry_seconds);
        if (empty($cookie_domain)) {
            $cookie_domain = COOKIE_DOMAIN;
        }
        setcookie($cookie_name, $cookie_value, $expiry_time, $path, $cookie_domain);
    }
    
    /**
     * Gets cookie
     * @param type $cookie_name
     * @return string
     */
    static function get_cookie_value($cookie_name)
    {
        if (isset($_COOKIE[$cookie_name])) {
            return $_COOKIE[$cookie_name];
        }
        return "";
    }

    /**
     * Checks if installation is multisite
     * @return type
     */
    static function is_multisite_install()
    {
        return function_exists('is_multisite') && is_multisite();
    }

    /**
     * This is a general yellow box message for when we want to suppress a feature's config items because site is subsite of multi-site
     */
    static function display_multisite_message()
    {
        echo '<div class="message_error">';
        echo '<p>' . __('Vivio Swift has detected that you are using a Multi-Site WordPress installation.', 'vivio-swift') . '</p>
              <p>' . __('This feature can only be configured by the "superadmin" on the main site.', 'vivio-swift') . '</p>';
        echo '</div>';
    }

    static function display_sysconfig_message()
    {
        echo '<div class="message_error">';
        echo '<p>' . __('Vivio Swift has detected that your server may require additional configuration in order to be fully functional.', 'vivio-swift') . '</p>
              <p>' . __('Please review the "System Information" tab from your Vivio Swift Dashboard to review your system configuration.', 'vivio-swift') . '</p>';
        echo '</div>';
    }


    /**
     * Returns an array of blog_ids for a multisite install
     * 
     * @global type $wpdb
     * @global type $wpdb
     * @return array or empty array if not multisite
     */
    static function get_blog_ids()
    {
        global $wpdb;
        if (Vivio_Swift_Utility::is_multisite_install()) {
            global $wpdb;
            $blog_ids = $wpdb->get_col("SELECT blog_id FROM " . $wpdb->prefix . "blogs");
        } else {
            $blog_ids = array();
        }
        return $blog_ids;
    }

    /**
     * Gets server type. 
     *  
     * @return string or -1 if server is not supported
     */
    static function get_server_type()
    {
        //figure out what server they're using
        if (strstr(strtolower(filter_var($_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING)), 'apache')) {
            return 'apache';
        } else if (strstr(strtolower(filter_var($_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING)), 'nginx')) {
            return 'nginx';
        } else if (strstr(strtolower(filter_var($_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING)), 'litespeed')) {
            return 'litespeed';
        } else if (strstr(strtolower(filter_var($_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING)), 'iis')) {
            return 'iis';
        } else { //unsupported server
            return -1;
        }

    }

}
