<?php

class Vivio_Swift_Dashboard_Menu extends Vivio_Swift_Admin_Messages
{
    var $dashboard_menu_page_slug = VIVIO_SWIFT_MAIN_MENU_SLUG;
    var $menu_tabs;
    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1',
        'tab2' => 'render_tab2',
        'tab3' => 'render_tab3',
        'tab4' => 'render_tab4',
        'tab5' => 'render_tab5',
        'tab6' => 'render_tab6'
    );

    function __construct()
    {
        require_once('vivio-swift-admin-links.php');
        $this->links = new Vivio_Swift_Admin_Links;
        $this->render_menu_page();
    }

    function set_menu_tabs()
    {
        $this->menu_tabs = array(
            'tab1' => __('Dashboard', 'vivio-swift'),
            'tab2' => __('System Info', 'vivio-swift'),
            'tab3' => __('Log Viewer', 'vivio-swift'),
            'tab4' => __('Cookie Viewer', 'vivio-swift'),
            'tab5' => __('htaccess Viewer', 'vivio-swift'),
            'tab6' => __('System Reset', 'vivio-swift')
        );
    }

    function get_current_tab()
    {
        $tab_keys = array_keys($this->menu_tabs);
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : $tab_keys[0];
        return $tab;
    }

    /*
     * Renders our tabs of this menu as nav items
     */
    function render_menu_tabs()
    {
        $current_tab = $this->get_current_tab();

        echo '<h2 class="nav-tab-wrapper">';
        foreach ($this->menu_tabs as $tab_key => $tab_caption) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->dashboard_menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }

    function render_notifications()
    {
        global $vivio_swift_global;

        // TODO check for global messages and post them here.
    }

    /*
     * The menu rendering goes here
     */
    function render_menu_page()
    {
        echo '<div class="wrap">';
        echo '<h2>' . __('Vivio Swift Dashboard', 'vivio-swift') . '</h2>';//Interface title
        $this->set_menu_tabs();
        $tab = $this->get_current_tab();
        $this->render_menu_tabs();
        ?>        
        <div id="poststuff"><div id="post-body">
        <?php
        //$tab_keys = array_keys($this->menu_tabs);
        call_user_func(array(&$this, $this->menu_tabs_handler[$tab]));
        ?>
        </div></div>
        </div><!-- end of wrap -->
        <?php
    }

    function render_tab1()
    {
    
        echo '<div class="message_success">';
        echo '<p>' . __('Thank you for trying Vivio Swift (beta). Please report bugs or issues <a href="https://viviotech.net/contactus.html" target="_blank">HERE</a>.', 'vivio-swift') . '</p>';
        echo '<p><a href="https://twitter.com/viviotech/" target="_blank">' . __('Follow us on Twitter', 'vivio-swift') . '</a> ' . __('to stay up to date on new and improved features of this plugin.', 'vivio-swift') . '</p>';
        echo '</div>';

        global $vivio_swift_global;

        $cache_expires_in_txt = '';
        $preload_cache_enabled = $vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled');
        $preload_last_run_date = $vivio_swift_global->configs->get_value('vivio_swift_preload_last_run_date');
        $preload_last_run_datetime = new DateTime( $vivio_swift_global->configs->get_value('vivio_swift_preload_last_run_date') );

        // if preload is enabled, calc the expiration date
        if (($preload_cache_enabled=='1') && ($preload_last_run_date!='')){
            $cache_expires_in_txt = $vivio_swift_global->util_date->format_interval($preload_last_run_datetime);
        } else {
            $cache_expires_in_txt = "No Preload Cache on record.";
        }

        if (isset($_POST['vivio_swift_clear_cache_submit']))
        {
            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_clear_cache_dashboard_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Dashboard_Menu::render_tab1 - Nonce check failed in preload settings.", 4);
                wp_die("Error: Nonce check failed when attempting to clear cache.");
            }

            $vivio_swift_global->cache_obj->cache_preload->clear();
            $this->show_msg_settings_updated();
        }

        if (isset($_POST['vivio_swift_preload_force_refresh'])){
            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_preload_dashboard_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Dashboard_Menu::render_tab1 - Nonce check failed in preload dashboard.", 4);
                wp_die("Error: Nonce check failed when attempting to update cache dashboard.");
            }

            // force a preload cache refresh
            $vivio_swift_global->cache_obj->cache_preload->create_preload_cache(1);
            $this->show_msg_preload_scheduled();
        }

        if (isset($_POST['vivio_swift_cache_dashboard_submit']))
        {
            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_cache_dashboard_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Dashboard_Menu::render_tab1 - Nonce check failed in preload settings.", 4);
                wp_die("Error: Nonce check failed when attempting to update cache settings.");
            }

            // set and save submitted settings
            $vivio_swift_global->configs->set_value('vivio_swift_cache_enabled',isset($_POST["vivio_swift_cache_enabled"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_preload_cache_enabled',isset($_POST["vivio_swift_preload_cache_enabled"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_enable',isset($_POST["vivio_swift_cache_control_headers_enable"])?'1':'');
            $vivio_swift_global->configs->save_config();

            $this->show_msg_settings_updated();
        }

        ?>
        <div id="vivio-swift-container">
            <div class="dashboard_box_small">
                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Quick Settings', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">
                        <form action=""
                            id="vivio_swift_cache_dashboard"
                            method="POST">
                            <?php wp_nonce_field('vivio_swift_cache_dashboard_nonce'); ?>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('On-Access Cache', 'vivio-swift')?>:</th>
                                    <td>
                                        <label class="switch">
                                            <input name="vivio_swift_cache_enabled" type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_enabled')=='1') echo ' checked="checked"'; ?> value="1"/>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Preload Cache', 'vivio-swift')?>:</th>                
                                    <td>
                                        <label class="switch">
                                            <input name="vivio_swift_preload_cache_enabled"
                                                id="vivio_swift_preload_cache_enabled"
                                                type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled')=='1') echo ' checked="checked"'; ?> value="1"/>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Browser Cache', 'vivio-swift')?>:</th>                
                                    <td>
                                        <label class="switch">
                                            <input name="vivio_swift_cache_control_headers_enable"
                                                id="vivio_swift_cache_control_headers_enable"
                                                type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_control_headers_enable')=='1') echo ' checked="checked"'; ?> value="1"/>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                </tr>
                            </table>
                            <input type="submit" name="vivio_swift_cache_dashboard_submit" id="vivio_swift_cache_dashboard_submit" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                        </form>
                    </div>
                </div>
            </div>

            <div class="dashboard_box_small">
                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Quick Clear', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">
                        <form action=""
                            id="vivio_swift_clear_cache_dashboard"
                            method="POST">
                            <?php wp_nonce_field('vivio_swift_clear_cache_dashboard_nonce'); ?>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('Current Cache', 'vivio-swift')?>:</th>
                                    <td>
                                    <span class="description"><?php echo $vivio_swift_global->util_file->get_file_count().' Files ('.$vivio_swift_global->util_file->get_directory_size().')'; ?></span>
                                    </td>
                                </tr>
                            </table>
                            <input type="submit" name="vivio_swift_clear_cache_submit" value="<?php _e('Clear Cache', 'vivio-swift')?>" class="pure-button button-error" />
                        </form>
                    </div>
                </div>
            </div>

            <div class="dashboard_box_small">
                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Preload Cache', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">
                        <form action=""
                            id="vivio_swift_preload_dashboard"
                            method="POST">
                            <?php wp_nonce_field('vivio_swift_preload_dashboard_nonce'); ?>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('Current Preload Cache Age', 'vivio-swift')?>:</th>                
                                    <td>
                                    <span class="description"><?php echo $cache_expires_in_txt; //echo ' ('.$preload_last_run_date.')' ?></span>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Force Preload Refresh', 'vivio-swift')?>:</th>                
                                    <td>
                                        <label>
                                            <input type="submit" name="vivio_swift_preload_force_refresh" value="<?php _e('Force Refresh', 'vivio-swift')?>"
                                                class="pure-button button-small"
                                                <?php if(!$vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled')=='1') echo ' disabled'; ?> />
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>

            <div class="dashboard_box_small">
                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Spread the Word', 'vivio-swift');?></label>
                    </h3>
                    <div class="inside">
                        <p><?php _e('Has this plugin helped you? Let others know!', 'vivio-swift');?></p>
                        <p><a href="https://www.facebook.com/viviotech" target="_blank">Like us on Facebook</a></p>
                        <p><a href="https://twitter.com/intent/tweet?url=https://viviotech.net/&text=Using the Vivio Swift Wordpress plugin from @viviotech has really helped speed up my site! Check it out!" target="_blank">Post to Twitter</a></p>
                        <p><a href="https://wordpress.org/support/plugin/vivio-swift" target="_blank">Give a Good Rating on Wordpress</a></p>
                    </div>
                </div>
            </div>

        </div>
        <!-- Masonry stuff -->
        
        <?php
        echo '<script type="text/javascript" src="' . VIVIO_SWIFT_URL . '/js/masonry.pkgd.min.js"></script>';
        ?>
        <script type="text/javascript">
            window.onload = function () {
                var container = document.querySelector('#vivio-swift-container');
                var msnry = new Masonry(container, {
                    // options
                    columnWidth: 100,
                    itemSelector: '.dashboard_box_small'
                });
            }
        </script>
    <?php
    }

    function render_tab2()
    {
    
        echo '<div class="message_standard">';
        echo '<p>' . __('The following information is useful when reporting bugs or issues regarding Vivio Swift.', 'vivio-swift') . '</p>';
        echo '</div>';

        global $wpdb;
        global $vivio_swift_global;

        ?>
        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('Vivio Swift Environment', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <strong><?php _e('Version', 'vivio-swift');?>
                    : </strong><code><?php echo VIVIO_SWIFT_VERSION;?></code><br/>
                <strong><?php _e('URL', 'vivio-swift');?>
                    : </strong><code><?php echo VIVIO_SWIFT_URL; ?></code><br/>
                <strong><?php _e('PATH', 'vivio-swift');?>
                    : </strong><code><?php echo VIVIO_SWIFT_PATH; ?></code><br/>
                <strong><?php _e('Cache URL', 'vivio-swift');?>
                    : </strong><code><?php echo VIVIO_SWIFT_CACHE_URL; ?></code><br/>
                <strong><?php _e('Cache Path', 'vivio-swift');?>
                    : </strong><code><?php echo VIVIO_SWIFT_CACHE_PATH; ?></code><br/>
                <strong><?php _e('Cache Path Writable', 'vivio-swift');?>
                    : </strong><code><?php echo (is_writable(VIVIO_SWIFT_CACHE_PATH)) ? "Writable</code>" : "Not Writable</code> "."<font color='red'>WARNING</font>" ?><br/>
                <strong><?php _e('Cache Bot Name', 'vivio-swift');?>
                    : </strong><code><?php echo VIVIO_SWIFT_CACHE_BOT_NAME; ?></code><br/>
                <strong><?php _e('Debug File Path', 'vivio-swift');?>
                    : </strong><code><?php echo VIVIO_SWIFT_PATH."logs/".VIVIO_SWIFT_LOG_FILE; ?></code><br/>
                <strong><?php _e('Debug File Writable', 'vivio-swift');?>
                    : </strong><code><?php echo (is_writable(VIVIO_SWIFT_PATH."logs/".VIVIO_SWIFT_LOG_FILE)) ? "Writable</code>" : "Not Writable</code> "."<font color='red'>WARNING</font>" ?><br/>

            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('Apache Environment', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <strong><?php _e('VERSION', 'vivio-swift');?>
                    : </strong><code><?php echo ($vivio_swift_global->util_apache->apache_version()) ? $vivio_swift_global->util_apache->apache_version() : "Unknown"; ?></code>
                    <?php if(!$vivio_swift_global->util_apache->apache_version()){echo '<font color="red">WARNING</font>';} ?><br/>
                <strong><?php _e('mod_rewrite', 'vivio-swift');?>
                    : </strong><code><?php echo ($vivio_swift_global->util_apache->test_mod_rewrite()) ? "Yes" : "Unknown"; ?></code>
                    <?php if(!$vivio_swift_global->util_apache->test_mod_rewrite()){echo '<font color="red">WARNING</font>';} ?><br/>
                <strong><?php _e('mod_headers', 'vivio-swift');?>
                    : </strong><code><?php echo ($vivio_swift_global->util_apache->test_mod_headers()) ? "Yes" : "Unknown"; ?></code>
                    <?php if(!$vivio_swift_global->util_apache->test_mod_headers()){echo '<font color="red">WARNING</font> <a href="'.$this->links->get_link("help_mod_headers").'" target="_blank" style="text-decoration: none;"><span class="dashicons dashicons-editor-help"></span></a>';} ?><br/>

            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('WordPress Environment', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <strong><?php _e('WP Version', 'vivio-swift');?>
                    : </strong><code><?php echo get_bloginfo("version"); ?></code><br/>
                <strong>WPMU: </strong><code><?php echo (!defined('MULTISITE') || !MULTISITE) ? "No" : "Yes"; ?></code><br/>
                <strong>MySQL <?php _e('Version', 'vivio-swift');?>
                    : </strong><code><?php echo $wpdb->db_version();?></code><br/>
                <strong>WP <?php _e('Table Prefix', 'vivio-swift');?>
                    : </strong><code><?php echo $wpdb->prefix; ?></code><br/>
                <strong>PHP <?php _e('Version', 'vivio-swift');?>
                    : </strong><code><?php echo phpversion(); ?></code><br/>
                <strong><?php _e('Session Save Path', 'vivio-swift');?>
                    : </strong><code><?php echo ini_get("session.save_path"); ?></code><br/>
                <strong>WP URL: </strong><code><?php echo get_bloginfo('wpurl'); ?></code><br/>
                <strong><?php _e('Server Name', 'vivio-swift');?>
                    : </strong><code><?php echo $_SERVER['SERVER_NAME']; ?></code><br/>
                <strong><?php _e('Cookie Domain', 'vivio-swift');?>
                    : </strong><code><?php $cookieDomain = parse_url(strtolower(get_bloginfo('wpurl')));
                    echo $cookieDomain['host']; ?></code><br/>
                <strong>CURL <?php _e('Library Present', 'vivio-swift');?>
                    : </strong><code><?php echo (function_exists('curl_init')) ? "Yes" : "No"; ?></code><br/>

            </div>
        </div>

        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('PHP Environment', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <strong><?php _e('PHP Version', 'vivio-swift'); ?>
                    : </strong><code><?php echo PHP_VERSION; ?></code><br/>
                <strong><?php _e('PHP Memory Usage', 'vivio-swift'); ?>:
                </strong><code><?php echo round(memory_get_usage() / 1024 / 1024, 2) . __(' MB', 'vivio-swift'); ?></code>
                <br/>
                <?php
                if (ini_get('memory_limit')) {
                    $memory_limit = filter_var(ini_get('memory_limit'), FILTER_SANITIZE_STRING);
                } else {
                    $memory_limit = __('N/A', 'vivio-swift');
                }
                ?>
                <strong><?php _e('PHP Memory Limit', 'vivio-swift'); ?>
                    : </strong><code><?php echo $memory_limit; ?></code><br/>
                <?php
                if (ini_get('upload_max_filesize')) {
                    $upload_max = filter_var(ini_get('upload_max_filesize'), FILTER_SANITIZE_STRING);
                } else {
                    $upload_max = __('N/A', 'vivio-swift');
                }
                ?>
                <strong><?php _e('PHP Max Upload Size', 'vivio-swift'); ?>
                    : </strong><code><?php echo $upload_max; ?></code><br/>
                <?php
                if (ini_get('post_max_size')) {
                    $post_max = filter_var(ini_get('post_max_size'), FILTER_SANITIZE_STRING);
                } else {
                    $post_max = __('N/A', 'vivio-swift');
                }
                ?>
                <strong><?php _e('PHP Max Post Size', 'vivio-swift'); ?>
                    : </strong><code><?php echo $post_max; ?></code><br/>
                <?php
                if (ini_get('allow_url_fopen')) {
                    $allow_url_fopen = __('On', 'vivio-swift');
                } else {
                    $allow_url_fopen = __('Off', 'vivio-swift');
                }
                ?>
                <strong><?php _e('PHP Allow URL fopen', 'vivio-swift'); ?>
                    : </strong><code><?php echo $allow_url_fopen; ?></code>
                <br/>
                <?php
                if (ini_get('allow_url_include')) {
                    $allow_url_include = __('On', 'vivio-swift');
                } else {
                    $allow_url_include = __('Off', 'vivio-swift');
                }
                ?>
                <strong><?php _e('PHP Allow URL Include'); ?>
                    : </strong><code><?php echo $allow_url_include; ?></code><br/>
                <?php
                if (ini_get('display_errors')) {
                    $display_errors = __('On', 'vivio-swift');
                } else {
                    $display_errors = __('Off', 'vivio-swift');
                }
                ?>
                <strong><?php _e('PHP Display Errors', 'vivio-swift'); ?>
                    : </strong><code><?php echo $display_errors; ?></code>
                <br/>
                <?php
                if (ini_get('max_execution_time')) {
                    $max_execute = filter_var(ini_get('max_execution_time'));
                } else {
                    $max_execute = __('N/A', 'vivio-swift');
                }
                ?>
                <strong><?php _e('PHP Max Script Execution Time', 'vivio-swift'); ?>
                    : </strong><code><?php echo $max_execute; ?> <?php _e('Seconds'); ?></code><br/>
            </div>
        </div><!-- End of PHP Info -->

        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('Active Plugins', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <?php
                $all_plugins = get_plugins();
                $active_plugins = get_option('active_plugins');
                //var_dump($all_plugins);
                ?>
                <table class="widefat margin_10_0">
                    <thead>
                    <tr>
                        <th><?php _e('Name', 'vivio-swift') ?></th>
                        <th><?php _e('Version', 'vivio-swift') ?></th>
                        <th><?php _e('Plugin URL', 'vivio-swift') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($active_plugins as $plugin_key) {
                        $plugin_details = $all_plugins[$plugin_key];
                        echo '<tr><td>' . $plugin_details['Name'] . '</td><td>' . $plugin_details['Version'] . '</td><td>' . $plugin_details['PluginURI'] . '</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div><!-- End of Active Plugins -->
    <?php
    }

    function render_tab3()
    {
        global $vivio_swift_global;

        // $file_selected = filter_input(INPUT_POST, 'vivio_swift_log_file'); // Get the selected file
        $file_selected = VIVIO_SWIFT_LOG_FILE;
        $loglevel_selected = $vivio_swift_global->configs->get_value('vivio_swift_debug_level');

        // process enable/disable debugging logging
        if (isset($_POST['vivio_swift_save_debug_settings']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio-swift-debug-settings-nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Dashboard_Menu::render_tab3 - Nonce check failed in debug logging.", 4);
                wp_die("Error: Nonce check failed while attempting enable/disable debug logging.");
            }

            if (isset($_POST['vivio_swift_log_level'])){
                $loglevel_selected = intval($_POST['vivio_swift_log_level']);
            }

            if ($loglevel_selected >5) {
                $error .= '<br />'.__('You entered an invalid value for the log level field. It has been set to the default value.','vivio-swift');
                $loglevel_selected = 3;
            }
            $vivio_swift_global->configs->save_config();

            // set and save debug settings
            $vivio_swift_global->configs->set_value('vivio_swift_enable_cache_comment',isset($_POST["vivio_swift_enable_cache_comment"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_enable_debug',isset($_POST["vivio_swift_enable_debug"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_debug_level',$loglevel_selected);
            $vivio_swift_global->configs->save_config();

            // process user feedback
            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            $this->show_msg_settings_updated();
        }

        // reset logs
        if (isset($_POST['vivio_swift_reset_logs']))
        {
            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_reset_logs_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed while attempting to reset logs.", 4);
                wp_die("Error: Nonce check failed while attempting to reset logs.");
            }

            $vivio_swift_global->debug_logger->reset_log_file('vivio-swift-log.txt');
            $vivio_swift_global->debug_logger->reset_log_file('vivio-swift-log-cron-job.txt');

            $this->show_msg_settings_updated();
        }

        ?>
        
        <!-- enable debugging option -->
        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('Debug Logging', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <form action=""
                    id="vivio_swift_dashboard_debug_settings"
                    method="POST">
                    <?php wp_nonce_field('vivio-swift-debug-settings-nonce'); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Enable HTML Comment', 'vivio-swift')?>:</th>
                            <td>
                                <label class="switch">
                                    <input name="vivio_swift_enable_cache_comment"
                                        id="vivio_swift_enable_cache_comment"
                                        type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_enable_cache_comment')=='1') echo ' checked="checked"'; ?> value="1"/>
                                    <span class="slider round"></span>
                                </label>
                                <span class="description"><?php _e('Adds a cache timestamp or other information to an HTML comment at the bottom of processed requests.', 'vivio-swift'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Enable Debug Logging', 'vivio-swift')?>:</th>
                            <td>
                                <label class="switch">
                                    <input name="vivio_swift_enable_debug"
                                        id="vivio_swift_enable_debug"
                                        type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_enable_debug')=='1') echo ' checked="checked"'; ?> value="1"/>
                                    <span class="slider round"></span>
                                </label>
                                <span class="description"><?php _e('Enable certain actions to be logged and viewable in the Vivio Swift Log Viewer.', 'vivio-swift'); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Select Debug Log Level', 'vivio-swift')?>:</th>
                            <td>
                                <select
                                    id="vivio_swift_log_level"
                                    name="vivio_swift_log_level"
                                    <?php if(!$vivio_swift_global->configs->get_value('vivio_swift_enable_debug')=='1') echo 'disabled'; ?>
                                    >
                                    <option
                                        value="0"<?php selected($loglevel_selected, '0'); ?>>
                                        <?php _e('0 - DEBUG', 'vivio-swift')?>
                                    </option>
                                    <option
                                        value="1"<?php selected($loglevel_selected, '1'); ?>>
                                        <?php _e('1 - STATUS', 'vivio-swift')?>
                                    </option>
                                    <option
                                        value="2"<?php selected($loglevel_selected, '2'); ?>>
                                        <?php _e('2 - NOTICE', 'vivio-swift')?>
                                    </option>
                                    <option
                                        value="3"<?php selected($loglevel_selected, '3'); ?>>
                                        <?php _e('3 - WARNING', 'vivio-swift')?>
                                    </option>
                                    <option
                                        value="4"<?php selected($loglevel_selected, '4'); ?>>
                                        <?php _e('4 - FAILURE', 'vivio-swift')?>
                                    </option>
                                    <option
                                        value="5"<?php selected($loglevel_selected, '5'); ?>>
                                        <?php _e('5 - CRITICAL', 'vivio-swift')?>
                                    </option>
                                </select>
                                <span class="description"><span class="dashicons dashicons-info info-icon" id="log_level_info"></span> <?php _e('Your selected level and everything higher will be logged. For example, with "WARNING" selected, all warnings, failures, and critical errors will be logged.', 'vivio-swift'); ?></span>
                            </td>
                        </tr>
                        <tr valign="top" id="log_level_info_row">
                            <th></th>
                            <td>
                                <table class="widefat">
                                    <tr valign="top">
                                        <td width="10%">DEBUG</td>
                                        <td width="90%"><?php _e('intended to be used by devs for information needed for development', 'vivio-swift')?></td>
                                    </tr>
                                    <tr valign="top">
                                        <td>STATUS</td>
                                        <td><?php _e('logs when/where things happen within the plugin', 'vivio-swift')?></td>
                                    </tr>
                                    <tr valign="top">
                                        <td>NOTICE</td>
                                        <td><?php _e('logs when things do/don\'t happen for specific reasons', 'vivio-swift')?></td>
                                    </tr>
                                    <tr valign="top">
                                        <td>WARNING</td>
                                        <td><?php _e('logs non-blocking errors (like when a variable isn\'t created yet)', 'vivio-swift')?></td>
                                    </tr>
                                    <tr valign="top">
                                        <td>FAILURE</td>
                                        <td><?php _e('logs function-blocking errors (may or may not be blocking)', 'vivio-swift')?></td>
                                    </tr>
                                    <tr valign="top">
                                        <td>CRITICAL</td>
                                        <td><?php _e('logs blocking errors that prevent functionality', 'vivio-swift')?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <input type="submit" name="vivio_swift_save_debug_settings" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                </form>
            </div>
        </div>

        <?php
            if (!empty($file_selected)) {
                ?>
                <div class="postbox">
                    <h3 class="hndle"><label
                            for="title"><?php echo __('Log File Contents For', 'vivio-swift') . ': ' . $file_selected;?></label>
                    </h3>

                    <div class="inside">
                        <?php
                        $vivio_swift_log_dir = VIVIO_SWIFT_PATH . '/logs';
                        $log_file = $vivio_swift_log_dir . '/' . $file_selected;
                        if (file_exists($log_file)) {
                            $log_contents = $vivio_swift_global->util_file->get_file_contents($log_file);
                        } else {
                            $log_contents = '';
                        }

                        if (empty($log_contents)) {
                            $log_contents = $file_selected . ': ' . __('Log file is empty!', 'vivio-swift');
                        }
                        ?>
                        <textarea class="text_area_file_output width_full margin_10_0" rows="20" readonly><?php echo esc_textarea($log_contents); ?></textarea>
                        <form action=""
                            id="vivio_swift_reset_logs_form"
                            method="POST"
                            onsubmit="return confirm('<?php _e('Are you sure you want to reset the logs? This cannot be undone.', 'vivio-swift')?>');">
                            <?php wp_nonce_field('vivio_swift_reset_logs_nonce'); ?>
                        <input type="submit" name="vivio_swift_reset_logs"
                           value="<?php _e('Reset Logs', 'vivio-swift')?>"
                           class="pure-button button-error" />
                        </form>
                    </div>
                </div>
            <?php
            }
    }

    function render_tab4()
    {
        echo '<div class="message_standard">';
        echo '<p>' . __('This page shows you your current cookies. This can be helpful when testing your \'Exclude Cookie\' rules.', 'vivio-swift') . '</p>';
        echo '</div>';

        global $vivio_swift_global;

        if(isset($_POST['vivio_swift_delete_cookie']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_cookie_viewer_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on cookie viewer.", 4);
                wp_die("Error: Nonce check failed on cookie viewer.");
            }
            
            foreach ((array)$_COOKIE as $cookie_key => $cookie_value){
                if($_REQUEST['vivio_swift_delete_cookie']==sanitize_text_field($cookie_key)){
                    setcookie($cookie_key, "", time()-3600);
                }
            }
        }
        
        ?>

        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('Cookie Viewer', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <form action="" method="POST">
                    <?php wp_nonce_field('vivio_swift_cookie_viewer_nonce'); ?>    
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Cookie Name (sanitized)', 'vivio-swift') ?></th>
                                <!-- <th><?php _e('Delete', 'vivio-swift') ?></th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ((array)$_COOKIE as $cookie_key => $cookie_value){
                                echo '<tr>';
                                echo '<td class="pattern_cell">' . sanitize_text_field($cookie_key) . "</td>";
                                //echo '<td class="action_cell"><button name="vivio_swift_delete_cookie" class="pure-button button-error" type="submit"';
                                //echo 'value="'.sanitize_text_field($cookie_key).'"';
                                //echo '>';
                                //echo '<span class="dashicons dashicons-no"></span></button></td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th><?php _e('Cookie Name (sanitized)', 'vivio-swift') ?></th>
                                <!-- <th><?php _e('Delete', 'vivio-swift') ?></th> -->
                            </tr>
                        </tfoot>
                    </table>
                </form>
            </div>
        </div>

        
    <?php
    }

    function render_tab5()
    {

        global $vivio_swift_global;

        echo '<div class="message_standard">';
        echo '<p>' . __('This page displays what Vivio Swift is writing to your .htaccess file. This can be helpful to ensure your rules are doing what you expect.', 'vivio-swift') . '</p>';
        echo '</div>';

        ?>

        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('.htaccess File Viewer', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <textarea class="text_area_file_output width_full margin_10_0" rows="20" readonly><?php
                $htaccess_contents = $vivio_swift_global->util_htaccess->display_htaccess();

                if (empty($htaccess_contents)){
                    echo __('Vivio Swift has nothing to write to .htaccess', 'vivio-swift');
                } else {
                    echo $htaccess_contents;
                }
                ?></textarea>
                <?php echo ABSPATH . '.htaccess' ?>
            </div>
        </div>
    <?php
    }

    function render_tab6()
    {
    
        echo '<div class="message_error">';
        echo '<p>' . __('WARNING: Performing a system reset on the Vivio Swift plugin will clear all caches, clear all logs, and reset all of Vivio Swift\'s settings back to their defaults.', 'vivio-swift') . '</p>';
        echo '</div>';

        global $vivio_swift_global;

        if (isset($_POST['vivio_swift_system_reset']))//Do form submission tasks
        {
            error_log('[Vivio Swift] System reset called...');
            //Check nonce before doing anything
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_system_reset_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on system reset.", 4);
                wp_die("Error: Nonce check failed on system reset.");
            }

            // reset options
            Vivio_Swift_Config_Settings::reset_to_defaults();
            // clear cache directory
            $vivio_swift_global->util_file->clear_cache_dir();
            // reset .htaccess
            $vivio_swift_global->util_htaccess->delete_from_htaccess();
            $vivio_swift_global->util_htaccess->write_to_htaccess();
            // reset logs
            $vivio_swift_global->debug_logger->reset_log_file('1');
            //$vivio_swift_global->debug_logger->reset_log_file('2');//cron

            $preload_enabled = ($vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled')=='1')?true:false;
            if ($preload_enabled){wp_schedule_single_event(time(), 'vivio_swift_schedule_preload_cache');}
            $this->show_msg_settings_updated();
        }
        ?>

        <div class="postbox">
            <h3 class="hndle">
                <label for="title"><?php _e('Perform System Reset', 'vivio-swift');?></label>
            </h3>

            <div class="inside">
                <form action=""
                    id="vivio_swift_system_reset_form"
                    method="POST"
                    onsubmit="return confirm('<?php _e('Are you absolutely sure you want to perform a system reset?', 'vivio-swift')?>');">
                    <?php wp_nonce_field('vivio_swift_system_reset_nonce'); ?>
                <input type="submit" name="vivio_swift_system_reset"
                   value="<?php _e('System Reset', 'vivio-swift')?>"
                   class="pure-button button-error" />
                </form>
            </div>
        </div>
    <?php
    }

} //end class
