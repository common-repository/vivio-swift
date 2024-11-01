<?php

class Vivio_Swift_Exclusions_Menu extends Vivio_Swift_Admin_Messages
{
    var $exclusions_menu_page_slug = VIVIO_SWIFT_EXCLUSIONS_MENU_SLUG;

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
        $this->render_menu_page();
    }

    function set_menu_tabs()
    {
        $this->menu_tabs = array(
            'tab1' => __('Settings', 'vivio-swift'),
            'tab2' => __('Cookies', 'vivio-swift'),
            'tab3' => __('User Agents', 'vivio-swift'),
            'tab4' => __('Exact Paths', 'vivio-swift'),
            'tab5' => __('Paths Ending With', 'vivio-swift'),
            'tab6' => __('Paths Containing', 'vivio-swift'),
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
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->exclusions_menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }

    /*
     * The menu rendering goes here
     */
    function render_menu_page()
    {
        echo '<div class="wrap">';
        echo '<h2>' . __('Vivio Swift Exclusions', 'vivio-swift') . '</h2>';//Interface title
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
        global $vivio_swift_global;

        if (isset($_POST['vivio_swift_exclusion_settings_submit']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclusion_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on exclusion settings.", 4);
                wp_die("Error: Nonce check failed on exclusion settings.");
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // set and save debug settings
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_query_strings',isset($_POST["exclude_query_strings_enabled"])?'1':'');
            //$vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_cookie_contains',isset($_POST["exclude_cookie_contains_enabled"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_is',isset($_POST["exclude_path_is_enabled"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_ends_with',isset($_POST["exclude_path_ends_with_enabled"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_contains',isset($_POST["exclude_path_contains_enabled"])?'1':'');
            $vivio_swift_global->configs->save_config();
            
            $this->show_msg_settings_updated();
        }

        ?>
        <div id="vivio-swift-container">

            <form action=""
                id="vivio_swift_exclusion_settings"
                method="POST">
                <?php wp_nonce_field('vivio_swift_exclusion_nonce'); ?>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Exclusions', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">

                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude POST requests', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_posts_enabled"
                                            id="exclude_posts_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_posts')=='1') echo ' checked="checked"'; ?> value="1" disabled/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("(Required) Vivio Swift should never show a cached page to an HTTP POST request.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude Cookie Contains', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_cookie_contains_enabled"
                                            id="exclude_cookie_contains_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_cookie_contains')=='1') echo ' checked="checked"'; ?> value="1" disabled/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("(Required) The existance of some cookies may indicate a user will need a dynamic response.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude User Agents', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_user_agents_enabled"
                                            id="exclude_user_agents_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_user_agent')=='1') echo ' checked="checked"'; ?> value="1" disabled/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("(Required) Some user agents will require a dynamic response.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude Query Strings', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_query_strings_enabled"
                                            id="exclude_query_strings_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_query_strings')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("(Recommended: ON) Requests containing query strings usually require a dynamic response.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude exact paths', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_path_is_enabled"
                                            id="exclude_path_is_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_is')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("Enable this option to exclude specific URL paths.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude paths that end with', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_path_ends_with_enabled"
                                            id="exclude_path_ends_with_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_ends_with')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("Enable this option to exclude paths that end with specific strings.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude paths that contain', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_path_contains_enabled"
                                            id="exclude_path_contains_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_contains')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("Enable this option to exclude paths that contain specific strings.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" id="vivio_swift_exclusion_settings_submit" name="vivio_swift_exclusion_settings_submit" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </div>
                </div>
            </form>

        </div>
        <?php
    }

    function render_tab2()
    {
        global $vivio_swift_global;
        
        /*if (isset($_POST['vivio_swift_exclusion_settings_submit']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclusion_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on exclusion settings.", 4);
                wp_die("Error: Nonce check failed on exclusion settings.");
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // set and save debug settings
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_cookie_contains',isset($_POST["exclude_cookie_contains_enabled"])?'1':'');
            $vivio_swift_global->configs->save_config();
            
            $this->show_msg_settings_updated();
        }*/

        if (isset($_POST['vivio_swift_add_cookie_contains']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_cookies_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on cookie exclusions.", 4);
                wp_die("Error: Nonce check failed on cookie exclusions.");
            }

            $new_cookie_contains_value = sanitize_text_field($_REQUEST['vivio_swift_cookie_contains_value']);
            if($new_cookie_contains_value){
                Vivio_Swift_Cache_Excludes::add_cookie_value($new_cookie_contains_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        if(isset($_POST['vivio_swift_delete_cookie_value']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_cookies_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on cookie exclusions.", 4);
                wp_die("Error: Nonce check failed on cookie exclusions.");
            }
            
            $delete_cookie_value = sanitize_text_field($_REQUEST['vivio_swift_delete_cookie_value']);
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Exclusions_Menu - delete_cookie_value: ".$delete_cookie_value, 1);
            if($delete_cookie_value){
                Vivio_Swift_Cache_Excludes::remove_cookie_value($delete_cookie_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        ?>
        <div id="vivio-swift-container">

            <form action=""
                id="vivio_swift_exclusion_settings"
                method="POST">
                <?php wp_nonce_field('vivio_swift_exclusion_nonce'); ?>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Enable Cookie Exclusions', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">

                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude Cookie Contains', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_cookie_contains_enabled"
                                            id="exclude_cookie_contains_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_cookie_contains')=='1') echo ' checked="checked"'; ?> value="1" disabled/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("(Required) The existance of some cookies may indicate a user will need a dynamic response.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" id="vivio_swift_exclusion_settings_submit" name="vivio_swift_exclusion_settings_submit" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </div>
                </div>
            </form>

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e("Current 'Cookie Contains' Exclusions", 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action="" method="POST">
                        <?php wp_nonce_field('vivio_swift_exclude_cookies_nonce'); ?>   
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Add New', 'vivio-swift')?>:</th>                
                                <td>
                                <input type="text" size="10"
                                    id="vivio_swift_cookie_contains_value"
                                    name="vivio_swift_cookie_contains_value"
                                    value=""
                                    />
                                <button type="submit" name="vivio_swift_add_cookie_contains">Add</button>
                                <span class="description"><?php _e('A visitor with a cookie that contains this value will not use cache.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>

                        </table>         
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e('Cookie Contains', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $arr_cookie_values = Vivio_Swift_Cache_Excludes::get_cookie_values();
                                foreach ($arr_cookie_values as $i => $value) {
                                    echo '<tr>';
                                    echo '<td class="pattern_cell">' . $value . "</td>";
                                    echo '<td class="action_cell"><button name="vivio_swift_delete_cookie_value" class="pure-button button-error" type="submit"';
                                    echo 'value="'.$value.'"';
                                    if(Vivio_Swift_Cache_Excludes::cookie_check_required_value($value)){echo ' disabled';}
                                    echo '>';
                                    echo '<span class="dashicons dashicons-no"></span></button></td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><?php _e('Cookie Contains', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>

            <div class="postbox closed">
                <h3 class="hndle">
                    <label for="title"><?php _e('Cookie Viewer', 'vivio-swift');?></label><small style="margin-left: 20px;"><?php _e('[Show/Hide]', 'vivio-swift');?></small>
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
        </div>
    <?php
    }

    function render_tab3()
    {
        global $vivio_swift_global;

        if (isset($_POST['vivio_swift_add_user_agent']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_user_agent_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on user agent exclusions.", 4);
                wp_die("Error: Nonce check failed on user agent exclusions.");
            }

            $new_user_agent_value = sanitize_text_field($_REQUEST['vivio_swift_user_agent_value']);
            if($new_user_agent_value){
                Vivio_Swift_Cache_Excludes::add_user_agent_value($new_user_agent_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        if(isset($_POST['vivio_swift_delete_user_agent_value']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_user_agent_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on user agent exclusions.", 4);
                wp_die("Error: Nonce check failed on user agent exclusions.");
            }
            
            $delete_user_agent_value = sanitize_text_field($_REQUEST['vivio_swift_delete_user_agent_value']);
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Exclusions_Menu - delete_user_agent_value: ".$delete_user_agent_value, 1);
            if($delete_user_agent_value){
                Vivio_Swift_Cache_Excludes::remove_user_agent_value($delete_user_agent_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        ?>
        <div id="vivio-swift-container">

            <form action=""
                id="vivio_swift_exclusion_settings"
                method="POST">
                <?php wp_nonce_field('vivio_swift_exclusion_nonce'); ?>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Enable User Agent Exclusions', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">

                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude User Agent', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_user_agent_enabled"
                                            id="exclude_user_agent_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_user_agent')=='1') echo ' checked="checked"'; ?> value="1" disabled/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("(Required) Some user agents will require a dynamic response.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" id="vivio_swift_exclusion_settings_submit" name="vivio_swift_exclusion_settings_submit" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </div>
                </div>
            </form>

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e("Current 'User Agent' Exclusions", 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action="" method="POST">
                        <?php wp_nonce_field('vivio_swift_exclude_user_agent_nonce'); ?>   
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Add New', 'vivio-swift')?>:</th>                
                                <td>
                                <input type="text" size="10"
                                    id="vivio_swift_user_agent_value"
                                    name="vivio_swift_user_agent_value"
                                    value=""
                                    />
                                <button type="submit" name="vivio_swift_add_user_agent">Add</button>
                                <span class="description"><?php _e('A visitor with a user agent that contains this value will not use cache.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>

                        </table>         
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e('User Agent', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $arr_user_agent_values = Vivio_Swift_Cache_Excludes::get_user_agent_values();
                                foreach ($arr_user_agent_values as $i => $value) {
                                    echo '<tr>';
                                    echo '<td class="pattern_cell">' . $value . "</td>";
                                    echo '<td class="action_cell"><button name="vivio_swift_delete_user_agent_value" class="pure-button button-error" type="submit"';
                                    echo 'value="'.$value.'"';
                                    if(Vivio_Swift_Cache_Excludes::user_agent_check_required_value($value)){echo ' disabled';}
                                    echo '>';
                                    echo '<span class="dashicons dashicons-no"></span></button></td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><?php _e('User Agent', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    <?php
    }


    function render_tab4()
    {
        global $vivio_swift_global;
        
        if (isset($_POST['vivio_swift_exclusion_settings_submit']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclusion_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on exclusion settings.", 4);
                wp_die("Error: Nonce check failed on exclusion settings.");
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // set and save debug settings
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_is',isset($_POST["exclude_path_is_enabled"])?'1':'');
            $vivio_swift_global->configs->save_config();
            
            $this->show_msg_settings_updated();
        }

        if (isset($_POST['vivio_swift_add_path_is']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_path_is_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on path_is exclusions.", 4);
                wp_die("Error: Nonce check failed on path_is exclusions.");
            }

            $path_is_value = sanitize_text_field($_REQUEST['vivio_swift_path_is_value']);
            if($path_is_value){
                Vivio_Swift_Cache_Excludes::add_path_is_value($path_is_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        if(isset($_POST['vivio_swift_delete_path_is_value']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_path_is_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on path_is exclusions.", 4);
                wp_die("Error: Nonce check failed on path_is exclusions.");
            }

            $delete_path_is_value = sanitize_text_field($_REQUEST['vivio_swift_delete_path_is_value']);
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Exclusions_Menu - delete_path_is_value: ".$delete_path_is_value, 1);
            if($delete_path_is_value){
                Vivio_Swift_Cache_Excludes::remove_path_is_value($delete_path_is_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        ?>
        <div id="vivio-swift-container">

            <form action=""
                id="vivio_swift_exclusion_settings"
                method="POST">
                <?php wp_nonce_field('vivio_swift_exclusion_nonce'); ?>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Enable Exact Path Exclusions', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude exact paths', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_path_is_enabled"
                                            id="exclude_path_is_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_is')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("Enable this option to exclude specific URL paths.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" id="vivio_swift_exclusion_settings_submit" name="vivio_swift_exclusion_settings_submit" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </div>
                </div>
            </form>

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e("Current 'Path Is' Exclusions", 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action="" method="POST">
                    <?php wp_nonce_field('vivio_swift_exclude_path_is_nonce'); ?>   
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Add New', 'vivio-swift')?>:</th>                
                                <td>
                                <?php echo get_bloginfo('wpurl').'/' ?>
                                <input type="text" size="10"
                                    id="vivio_swift_path_is_value"
                                    name="vivio_swift_path_is_value"
                                    value=""
                                    />
                                <button type="submit" name="vivio_swift_add_path_is">Add</button>
                                <span class="description"><?php _e('A URL whos path is this value will not use cache.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>

                        </table>         
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e('Path Is', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $arr_path_is_values = Vivio_Swift_Cache_Excludes::get_path_is_values();
                                if(count($arr_path_is_values)){
                                    foreach ($arr_path_is_values as $i => $value) {
                                        echo '<tr>';
                                        echo '<td class="pattern_cell">' . $value . "</td>";
                                        echo '<td class="action_cell"><button name="vivio_swift_delete_path_is_value" class="pure-button button-error" type="submit"';
                                        echo 'value="'.$value.'"';
                                        echo '>';
                                        echo '<span class="dashicons dashicons-no"></span></button></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr>';
                                    echo '<td colspan="2" class="action_cell">No records.</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><?php _e('Path Is', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    <?php
    }


    function render_tab5()
    {
        global $vivio_swift_global;

        if (isset($_POST['vivio_swift_exclusion_settings_submit']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclusion_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on exclusion settings.", 4);
                wp_die("Error: Nonce check failed on exclusion settings.");
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // set and save debug settings
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_ends_with',isset($_POST["exclude_path_ends_with_enabled"])?'1':'');
            $vivio_swift_global->configs->save_config();
            
            $this->show_msg_settings_updated();
        }
        
        if (isset($_POST['vivio_swift_add_path_ends_with']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_path_ends_with_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on path_ends_with exclusions.", 4);
                wp_die("Error: Nonce check failed on path_ends_with exclusions.");
            }

            $path_ends_with_value = sanitize_text_field($_REQUEST['vivio_swift_path_ends_with_value']);
            if($path_ends_with_value){
                Vivio_Swift_Cache_Excludes::add_path_ends_with_value($path_ends_with_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        if(isset($_POST['vivio_swift_delete_path_ends_with_value']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_path_ends_with_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on path_ends_with exclusions.", 4);
                wp_die("Error: Nonce check failed on path_ends_with exclusions.");
            }

            $delete_path_ends_with_value = sanitize_text_field($_REQUEST['vivio_swift_delete_path_ends_with_value']);
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Exclusions_Menu - delete_path_ends_with_value: ".$delete_path_ends_with_value, 1);
            if($delete_path_ends_with_value){
                Vivio_Swift_Cache_Excludes::remove_path_ends_with_value($delete_path_ends_with_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        ?>
        <div id="vivio-swift-container">

            <form action=""
                id="vivio_swift_exclusion_settings"
                method="POST">
                <?php wp_nonce_field('vivio_swift_exclusion_nonce'); ?>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Enable Paths Ending With Excludes', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">

                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude paths that end with', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_path_ends_with_enabled"
                                            id="exclude_path_ends_with_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_ends_with')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("Enable this option to exclude paths that end with specific strings.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" id="vivio_swift_exclusion_settings_submit" name="vivio_swift_exclusion_settings_submit" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </div>
                </div>
            </form>

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e("Current 'Paths Ending With' Exclusions", 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action="" method="POST">
                    <?php wp_nonce_field('vivio_swift_exclude_path_ends_with_nonce'); ?>   
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Add New', 'vivio-swift')?>:</th>                
                                <td>
                                <?php echo get_bloginfo('wpurl').'/*filename' ?>
                                <input type="text" size="10"
                                    id="vivio_swift_path_ends_with_value"
                                    name="vivio_swift_path_ends_with_value"
                                    value=""
                                    />
                                <button type="submit" name="vivio_swift_add_path_ends_with">Add</button>
                                <span class="description"><?php _e('A URL whos path is this value will not use cache.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>         
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e('Paths Ending With', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $arr_path_ends_with_values = Vivio_Swift_Cache_Excludes::get_path_ends_with_values();
                                if(count($arr_path_ends_with_values)){
                                    foreach ($arr_path_ends_with_values as $i => $value) {
                                        echo '<tr>';
                                        echo '<td class="pattern_cell">' . $value . "</td>";
                                        echo '<td class="action_cell"><button name="vivio_swift_delete_path_ends_with_value" class="pure-button button-error" type="submit"';
                                        echo 'value="'.$value.'"';
                                        echo '>';
                                        echo '<span class="dashicons dashicons-no"></span></button></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr>';
                                    echo '<td colspan="2" class="action_cell">No records.</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><?php _e('Paths Ending With', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    <?php
    }


    function render_tab6()
    {
        global $vivio_swift_global;

        if (isset($_POST['vivio_swift_exclusion_settings_submit']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclusion_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on exclusion settings.", 4);
                wp_die("Error: Nonce check failed on exclusion settings.");
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // set and save debug settings
            $vivio_swift_global->configs->set_value('vivio_swift_cache_exclude_path_contains',isset($_POST["exclude_path_contains_enabled"])?'1':'');
            $vivio_swift_global->configs->save_config();
            
            $this->show_msg_settings_updated();
        }

        if (isset($_POST['vivio_swift_add_path_contains']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_path_contains_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on path_contains exclusions.", 4);
                wp_die("Error: Nonce check failed on path_contains exclusions.");
            }

            $path_contains_value = sanitize_text_field($_REQUEST['vivio_swift_path_contains_value']);
            if($path_contains_value){
                Vivio_Swift_Cache_Excludes::add_path_contains_value($path_contains_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        if(isset($_POST['vivio_swift_delete_path_contains_value']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_exclude_path_contains_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on path_contains exclusions.", 4);
                wp_die("Error: Nonce check failed on path_contains exclusions.");
            }

            $delete_path_contains_value = sanitize_text_field($_REQUEST['vivio_swift_delete_path_contains_value']);
            $vivio_swift_global->debug_logger->log_debug("Vivio_Swift_Exclusions_Menu - delete_path_contains_value: ".$delete_path_contains_value, 1);
            if($delete_path_contains_value){
                Vivio_Swift_Cache_Excludes::remove_path_contains_value($delete_path_contains_value);
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }
        }

        ?>
        <div id="vivio-swift-container">

            <form action=""
                id="vivio_swift_exclusion_settings"
                method="POST">
                <?php wp_nonce_field('vivio_swift_exclusion_nonce'); ?>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Exclusions', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">

                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Exclude paths that contain', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="exclude_path_contains_enabled"
                                            id="exclude_path_contains_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_exclude_path_contains')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("Enable this option to exclude paths that contain specific strings.", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" id="vivio_swift_exclusion_settings_submit" name="vivio_swift_exclusion_settings_submit" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </div>
                </div>
            </form>

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e("Current 'Paths Containing' Exclusions", 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action="" method="POST">
                    <?php wp_nonce_field('vivio_swift_exclude_path_contains_nonce'); ?>   
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Add New', 'vivio-swift')?>:</th>                
                                <td>
                                <?php echo get_bloginfo('wpurl').'/*' ?>
                                <input type="text" size="10"
                                    id="vivio_swift_path_contains_value"
                                    name="vivio_swift_path_contains_value"
                                    value=""
                                    />*/
                                <button type="submit" name="vivio_swift_add_path_contains">Add</button>
                                <span class="description"><?php _e('A URL whos path is this value will not use cache.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>

                        </table>         
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php _e('Paths Containing', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $arr_path_contains_values = Vivio_Swift_Cache_Excludes::get_path_contains_values();
                                if(count($arr_path_contains_values)){
                                    foreach ($arr_path_contains_values as $i => $value) {
                                        echo '<tr>';
                                        echo '<td class="pattern_cell">' . $value . "</td>";
                                        echo '<td class="action_cell"><button name="vivio_swift_delete_path_contains_value" class="pure-button button-error" type="submit"';
                                        echo 'value="'.$value.'"';
                                        echo '>';
                                        echo '<span class="dashicons dashicons-no"></span></button></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr>';
                                    echo '<td colspan="2" class="action_cell">No records.</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th><?php _e('Paths Containing', 'vivio-swift') ?></th>
                                    <th><?php _e('Delete', 'vivio-swift') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    <?php
    }

} //end class
