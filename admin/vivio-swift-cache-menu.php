<?php

class Vivio_Swift_Cache_Menu extends Vivio_Swift_Admin_Messages
{
    var $cache_menu_page_slug = VIVIO_SWIFT_CACHE_MENU_SLUG;

    var $menu_tabs;

    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1',
        'tab2' => 'render_tab2',
        'tab3' => 'render_tab3',
        'tab4' => 'render_tab4',
        'tab5' => 'render_tab5'
    );

    function __construct()
    {
        $this->render_menu_page();
    }

    function set_menu_tabs()
    {
        $this->menu_tabs = array(
            'tab1' => __('On-Access Cache', 'vivio-swift'),
            'tab2' => __('Preload Cache', 'vivio-swift'),
            'tab3' => __('Browser Cache', 'vivio-swift'),
            'tab4' => __('Refresh Events', 'vivio-swift'),
            'tab5' => __('Extras', 'vivio-swift')
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
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->cache_menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }

    /*
     * The menu rendering goes here
     */
    function render_menu_page()
    {
        echo '<div class="wrap">';
        echo '<h2>' . __('Vivio Swift Cache', 'vivio-swift') . '</h2>';//Interface title
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
    
        echo '<div class="message_standard">';
        echo '<p>' . __('On-Access Cache creates a cache whenever a cachable PHP page is accessed. It is recommended to keep this on unless there is a conflict with another plugin.', 'vivio-swift') . '</p>';
        echo '</div>';

        // process cache settings
        if (isset($_POST['vivio_swift_save_cache_settings']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_simple_settings_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on cache settings.", 4);
                wp_die("Error: Nonce check failed on cache settings.");
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // set and save settings
            if (isset($_POST["vivio_swift_cache_enabled"])){
                $vivio_swift_global->cache_obj->cache_onaccess->enable();
            } else {
                $vivio_swift_global->cache_obj->cache_onaccess->disable();
            }

            
            $this->show_msg_settings_updated();
        }

        if (isset($_POST['vivio_swift_cache_clear_submit']))
        {
            $vivio_swift_global->cache_obj->cache_onaccess->clear();
            $this->show_msg_settings_updated();
        }

        ?>
        <div id="vivio-swift-container">

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e('On-Access Cache', 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action=""
                        id="vivio_swift_cache_simple_settings"
                        method="POST">
                        <?php wp_nonce_field('vivio_swift_simple_settings_nonce'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Enable On-Access Cache', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_cache_enabled" type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_enabled')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('(Recommended: ON) Enable on-access caching using Vivio Swift.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="vivio_swift_save_cache_settings" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </form>
                </div>
            </div>

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e('Clear Cache', 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action=""
                        id="vivio_swift_cache_clear"
                        method="POST">
                        <?php wp_nonce_field('vivio_swift_cache_clear_nonce'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Current Cache', 'vivio-swift')?>:</th>
                                <td>
                                <span class="description"><?php echo $vivio_swift_global->util_file->get_file_count().' Files ('.$vivio_swift_global->util_file->get_directory_size().')'; ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="vivio_swift_cache_clear_submit" value="<?php _e('Clear Cache', 'vivio-swift')?>" class="pure-button button-error" />
                    </form>
                </div>
            </div>
        </div>
        <?php
    }

    function render_tab2()
    {
        global $vivio_swift_global;
    
        echo '<div class="message_standard">';
        echo '<p>' . __('Preload cache will cache your entire site and refresh that cache when it expires. It is recommended that you run both On-Access and Preload Cache together.', 'vivio-swift') . '</p>';
        echo '</div>';

        if (isset($_POST['vivio_swift_cache_clear_submit']))
        {
            $vivio_swift_global->cache_obj->cache_preload->clear();
            $this->show_msg_settings_updated();
        }

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

        if (isset($_POST['vivio_swift_save_preload_cache_settings']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_preload_cache_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed in preload settings.", 4);
                wp_die("Error: Nonce check failed in preload settings.");
            }

            $vivio_swift_preload_cache_expire_hours_val = sanitize_text_field($_POST['vivio_swift_preload_cache_expire_hours']);
            if(!is_numeric($vivio_swift_preload_cache_expire_hours_val))
            {
                $error .= '<br />'.__('You entered a non numeric value for the cache expires field. It has been set to the default value.','vivio-swift');
                $vivio_swift_preload_cache_expire_hours_val = '1';//Set it to the default value for this field
            } elseif ($vivio_swift_preload_cache_expire_hours_val < 1)
            {
                $error .= '<br />'.__('The Cache Expires field cannot have a value of less than 1. It has been set to the default value.','vivio-swift');
                $vivio_swift_preload_cache_expire_hours_val = '1';//Set it to the default value for this field
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // set and save settings
            if (isset($_POST["vivio_swift_preload_cache_enabled"])){
                $vivio_swift_global->cache_obj->cache_preload->enable();
            } else {
                $vivio_swift_global->cache_obj->cache_preload->disable();
            }

            $vivio_swift_global->configs->set_value('vivio_swift_preload_cache_expire_hours',absint($vivio_swift_preload_cache_expire_hours_val));
            $vivio_swift_global->configs->save_config();

            $this->show_msg_settings_updated();
        }

        if (isset($_POST['vivio_swift_preload_force_refresh']))
        {
            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_preload_cache_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed in preload settings.", 4);
                wp_die("Error: Nonce check failed in preload settings.");
            }

            if (isset($_POST['vivio_swift_preload_force_refresh'])){
                // force a preload cache refresh
                $vivio_swift_global->cache_obj->cache_preload->create_preload_cache(1);
                $this->show_msg_preload_scheduled();
            }
        }

        ?>
        <div id="vivio-swift-container">

            <form action=""
                id="vivio_swift_cache_preload_settings"
                method="POST">
                <?php wp_nonce_field('vivio_swift_preload_cache_nonce'); ?>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Preload Cache', 'vivio-swift');?></label>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Enable Preload Cache', 'vivio-swift')?>:</th>                
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_preload_cache_enabled"
                                            id="vivio_swift_preload_cache_enabled"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_preload_cache_enabled')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e("(Recommended: ON) Enable a process that will cache your site at regular intervals (based on when they're set to expire).", 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Cache Valid Hours', 'vivio-swift')?>:</th>                
                                <td>
                                <input type="text" size="6"
                                    id="vivio_swift_preload_cache_expire_hours"
                                    name="vivio_swift_preload_cache_expire_hours"
                                    value="<?php echo $vivio_swift_global->configs->get_value('vivio_swift_preload_cache_expire_hours'); ?>"
                                    />
                                <span class="description"><?php _e('(Recommended: 1) Number of hours that the Preload Cache will remain valid.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Current Preload Cache Age', 'vivio-swift')?>:</th>                
                                <td>
                                <span class="description"><?php echo $cache_expires_in_txt; //echo ' ('.$preload_last_run_date.')' ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Force Refresh', 'vivio-swift')?>:</th>                
                                <td>
                                    <label>
                                        <input type="submit" name="vivio_swift_preload_force_refresh" value="<?php _e('Force Refresh', 'vivio-swift')?>" class="pure-button button-small" />
                                    </label>
                                <span class="description"><?php _e('Force a refresh of the preload cache.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" id="vivio_swift_save_preload_cache_settings" name="vivio_swift_save_preload_cache_settings" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </div>
                </div>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Clear Cache', 'vivio-swift');?></label>
                    </h3>

                    <div class="inside">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Current Cache', 'vivio-swift')?>:</th>
                                <td>
                                <span class="description"><?php echo $vivio_swift_global->util_file->get_file_count().' Files ('.$vivio_swift_global->util_file->get_directory_size().')'; ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="vivio_swift_cache_clear_submit" value="<?php _e('Clear Cache', 'vivio-swift')?>" class="pure-button button-error" />
                    </div>
                </div>
            </form>
        </div><!-- End of Extra Settings -->
        <?php
    }

    function render_tab3()
    {
        echo '<div class="message_standard">';
        echo '<p>' . __('This feature modifies your HTTP response headers to include a Cache-Control Max-Age value, which gives you control over how your site is cached in a browser.', 'vivio-swift') . '</p>';
        echo '</div>';

        global $vivio_swift_global;
        $cache_control_groups = $vivio_swift_global->configs->get_value('vivio_swift_cache_control_headers_values');
        $error='';

        // process the cache control switch
        if (isset($_POST['vivio_swift_cache_control_save']))
        {
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_control_group_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed in cache-control options.", 4);
                wp_die("Error: Nonce check failed in cache-control options.");
            }

            $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_enable',isset($_POST["vivio_swift_cache_control_headers_enable"])?'1':'');
            $vivio_swift_global->configs->save_config();
            $vivio_swift_global->util_htaccess->write_to_htaccess();

            $this->show_msg_settings_updated();
        }        

        if (isset($_POST['vivio_swift_control_group_add_group_button']))
        {
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_control_group_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed in cache-control options.", 4);
                wp_die("Error: Nonce check failed in cache-control options.");
            }

            $new_group_name = sanitize_text_field($_POST['vivio_swift_control_group_add_group']);

            if ($new_group_name==''){
                $error = 'Group name cannot be empty.';
            }

            // verify group name doesn't already exist
            foreach($cache_control_groups as $index=>$group){
                if ($group['name']==$new_group_name){
                    $error = 'That group name already exists.';
                }
            }

            // create a simple array to house our new group
            if ($error==''){
                $arr_new_group = array(
                    'name'          => $new_group_name,
                    'max-age'       => '0',
                    'enable-cache'  => '0', 
                    'extensions'    => array()
                    );
                // add the new group to the existing $cache_control_groups array
                array_push($cache_control_groups,$arr_new_group);
                $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_values',$cache_control_groups);
                $vivio_swift_global->configs->save_config();
                $vivio_swift_global->util_htaccess->write_to_htaccess();
                // get the new group index
                foreach($cache_control_groups as $index=>$group){
                    if ($group['name']==$new_group_name){
                        $new_group_index = $index;
                    }
                }
                // forcably set post field to new group index so that it will show as selected
                $_POST['vivio_swift_control_group_id'] = $new_group_index;
                // also set the view group button so that it acts as if it were pressed
                $_POST['vivio_swift_cache_control_select_group'] = "View Group";
            } else {
                $this->show_msg_error(__('Error: ','vivio-swift').$error);
            }
        }

        $arr_control = '';

        if (isset($_POST['vivio_swift_control_group_id']) && (is_numeric($_POST['vivio_swift_control_group_id'])) && ($error==''))
        {
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_control_group_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on control group select.", 4);
                wp_die("Error! Nonce check failed on control group select.");
            }

            // validate id being passed
            $post_group = intval(sanitize_text_field($_POST['vivio_swift_control_group_id']));

            if ($post_group < 0){
                $error .= '<br />'.__('The group value cannot have a value of lower than 0.','vivio-swift');
            } elseif (in_array($post_group, $cache_control_groups, true)){
                $error .= '<br />'.__('The group value was not found in the current groups. Please try again.','vivio-swift');
            }

            if(sanitize_text_field($_POST['vivio_swift_cache_control_save'])=="2"){
                $cache_control_groups[$post_group]['max-age'] = intval(sanitize_text_field($_POST['vivio_swift_cache_control_max_age_value']));
                $cache_control_groups[$post_group]['enable-cache'] = isset($_POST["vivio_swift_cache_control_group_enabled"])?'1':'';
                $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_values',$cache_control_groups);
                $vivio_swift_global->configs->save_config();
                $vivio_swift_global->util_htaccess->write_to_htaccess();
            }

            if(isset($_POST['vivio_swift_add_control_group_extension_add'])){
                $new_extension = sanitize_text_field($_POST['vivio_swift_control_group_extension']);
                if($new_extension==""){
                    $error .= '<br />'.__('The New File Extension cannot be blank.','vivio-swift');
                } elseif (!ctype_alnum($new_extension)){
                    $error .= '<br />'.__('The New File Extension must be alphanumeric.','vivio-swift');
                } elseif (in_array($new_extension, $cache_control_groups[$post_group]['extensions'])){
                    // TODO check all extensions in every group
                    $error .= '<br />'.__('The New File Extension you entered already exists in this group.','vivio-swift');
                }
                if($error==""){
                    array_push($cache_control_groups[$post_group]['extensions'], $new_extension);
                    $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_values',$cache_control_groups);
                    $vivio_swift_global->configs->save_config();
                    $vivio_swift_global->util_htaccess->write_to_htaccess();
                }
            }

            if(isset($_POST["vivio_swift_control_group_extension_delete"])){
                $arr_extensions = $cache_control_groups[$post_group]['extensions'];
                $ext_to_remove = sanitize_text_field($_POST["vivio_swift_control_group_extension_delete"]);
                foreach ($arr_extensions as $index=>$extension) {
                    if($extension==$ext_to_remove){
                        unset($cache_control_groups[$post_group]['extensions'][$index]);
                    }
                }
                $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_values',$cache_control_groups);
                $vivio_swift_global->configs->save_config();
                $vivio_swift_global->util_htaccess->write_to_htaccess();
            }
            
            if($error){$this->show_msg_error(__('Attention!','vivio-swift').$error);}

            $arr_control = $cache_control_groups[$post_group];

            // group delete
            if ((sanitize_text_field($_POST['vivio_swift_control_group_delete']))=="1")
            {
                unset($cache_control_groups[$post_group]);
                $cache_control_groups = array_values($cache_control_groups);
                $vivio_swift_global->configs->set_value('vivio_swift_cache_control_headers_values',$cache_control_groups);
                $vivio_swift_global->configs->save_config();
                $vivio_swift_global->util_htaccess->write_to_htaccess();
                unset($_POST['vivio_swift_control_group_id']);
                unset($post_group);
                unset($arr_control);
                $this->show_msg_record_deleted_st();
            }
        }

        ?>
        <div id="vivio-swift-container">
            <form action=""
                id="vivio_swift_control_group_form"
                method="POST">
                <?php wp_nonce_field('vivio_swift_control_group_nonce'); ?>
                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Browser Cache', 'vivio-swift');?></label>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Enable Browser Cache', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_cache_control_headers_enable"
                                            id="vivio_swift_cache_control_headers_enable"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_cache_control_headers_enable')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('Production sites should enable this for best performance. Do not enable cache-control for sites still in development.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <button type="submit" name="vivio_swift_cache_control_save" value="1" class="pure-button"><?php _e('Save Settings', 'vivio-swift')?></button>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Control Groups', 'vivio-swift');?></label>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Add New Control Group', 'vivio-swift')?>:</th>                
                                <td>
                                <input type="text" size="10"
                                    id="vivio_swift_control_group_add_group"
                                    name="vivio_swift_control_group_add_group"
                                    value=""
                                    />
                                <button type="submit" name="vivio_swift_control_group_add_group_button" class="pure-button"><?php _e('Add', 'vivio-swift')?></button>
                                <span class="description"><?php _e('Add a new Control Group. Control Groups enable you to set specific cache times for specific groups of files.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Control Groups', 'vivio-swift')?>:</th>
                                <td>
                                    <select id="vivio_swift_control_group_id" name="vivio_swift_control_group_id">
                                        <option
                                            value=""><?php _e('--Select a Group--', 'vivio-swift')?></option>
                                        <?php
                                            foreach($cache_control_groups as $index=>$group){
                                                echo '<option value="'.$index.'"';
                                                if(isset($post_group)&&($index==$post_group)){echo ' selected';}
                                                echo '>'.$group['name'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <span class="description"><?php _e('Control Groups give you the ability to group specific file types so they can be cached for different lengths of time.', 'all-in-one-wp-security-and-firewall'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <?php if (!is_numeric($_POST['vivio_swift_control_group_id'])){?>
                        <button type="submit" name="vivio_swift_cache_control_save" value="1" class="pure-button"><?php _e('Save Settings', 'vivio-swift')?></button>
                        <?php } ?>
                    </div>
                </div>
                <?php
                if (isset($_POST['vivio_swift_control_group_id']) && is_array($arr_control))
                {
                    ?>
                    <input
                        type="hidden"
                        id="vivio_swift_control_group_delete"
                        name="vivio_swift_control_group_delete"
                        value="0">
                    <div class="postbox">
                        <h3 class="hndle">
                            <label for="title"><?php echo $arr_control['name'];?></label>
                        </h3>
                        <div class="inside">
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('Enable This Group', 'vivio-swift')?>:</th>
                                    <td>
                                        <label class="switch">
                                            <input name="vivio_swift_cache_control_group_enabled"
                                                id="vivio_swift_cache_control_group_enabled"
                                                type="checkbox"<?php if($arr_control['enable-cache']=='1') echo ' checked="checked"'; ?> value="1"/>
                                            <span class="slider round"></span>
                                        </label>
                                    <span class="description"><?php _e('Enable or Disable Cache-Control headers for this group. Browser Cache option (above) must also be enabled.', 'vivio-swift'); ?></span>
                                    </td>
                                </tr>
                                <tr valign="top">
                                    <th scope="row"><?php _e('Max-Age', 'vivio-swift')?>:</th>                
                                    <td>
                                    <input type="text" size="10"
                                        id="vivio_swift_cache_control_max_age_value"
                                        name="vivio_swift_cache_control_max_age_value"
                                        value="<?php echo $arr_control['max-age']; ?>"
                                        />
                                    <span class="description"><?php _e('Number of seconds that the file can be stored in a browser cache.', 'vivio-swift'); ?></span>
                                    </td>
                                </tr>
                            </table>
                            <button type="submit" name="vivio_swift_cache_control_save" value="2" class="pure-button"><?php _e('Save Settings', 'vivio-swift')?></button>
                            <table class="form-table">
                                <tr valign="top">
                                    <th scope="row"><?php _e('Add New File Extension', 'vivio-swift')?>:</th>                
                                    <td>
                                    <input type="text" size="10"
                                        id="vivio_swift_control_group_extension"
                                        name="vivio_swift_control_group_extension"
                                        value=""
                                        />
                                    <button type="submit" name="vivio_swift_add_control_group_extension_add" class="pure-button"><?php _e('Add', 'vivio-swift')?></button>
                                    <span class="description"><?php _e('Add a new file extension to this control group', 'vivio-swift'); ?></span>
                                    </td>
                                </tr>
                            </table>         
                            <table class="widefat">
                                <thead>
                                    <tr>
                                        <th><?php _e('File Extension', 'vivio-swift') ?></th>
                                        <th><?php _e('Delete', 'vivio-swift') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $arr_group_extensions = $arr_control["extensions"];
                                    foreach ($arr_group_extensions as $i => $value) {
                                        echo '<tr>';
                                        echo '<td class="pattern_cell">' . $value . "</td>";
                                        echo '<td class="action_cell"><button name="vivio_swift_control_group_extension_delete" class="pure-button button-error" type="submit"';
                                        echo 'value="'.$value.'">';
                                        echo '<span class="dashicons dashicons-no"></span></button></td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th><?php _e('File Extension', 'vivio-swift') ?></th>
                                        <th><?php _e('Delete', 'vivio-swift') ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                            <input type="button" id="vivio_swift_remove_control_group_submit" value="<?php _e('Delete Group', 'vivio-swift')?>" class="pure-button button-error" />

                            <?php
                        }
                        ?>
                    </div>
                </div>
            </form>
        </div>

        <?php
    }

    function render_tab4()
    {
        global $vivio_swift_global;
    
        echo '<div class="message_standard">';
        echo '<p>' . __('Refresh events clear your existing cache when they occur. If you have pre-load cache enabled, these events will also refresh your preload cache.', 'vivio-swift') . '</p>';
        echo '</div>';

        if (isset($_POST['vivio_swift_refresh_events_submit']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_refresh_events_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed in refresh events.", 4);
                wp_die("Error: Nonce check failed in refresh events.");
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // set and save submitted settings
            $vivio_swift_global->configs->set_value('vivio_swift_refresh_on_post_new',isset($_POST["vivio_swift_refresh_on_post_new_enabled"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_refresh_on_post_update',isset($_POST["vivio_swift_refresh_on_post_update_enabled"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_refresh_on_category_change',isset($_POST["vivio_swift_refresh_on_category_change_enabled"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_refresh_on_tag_change',isset($_POST["vivio_swift_refresh_on_tag_change_enabled"])?'1':'');
            $vivio_swift_global->configs->save_config();

            $this->show_msg_settings_updated();
        }


        ?>
        <div id="vivio-swift-container">

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e('Refresh Events', 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action=""
                        id="vivio_swift_refresh_events_settings"
                        method="POST">
                        <?php wp_nonce_field('vivio_swift_refresh_events_nonce'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Refresh on New Post', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_refresh_on_post_new_enabled" type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_refresh_on_post_new')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('(Recommended: ON) Clear cache whenever a new post or page is made.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Refresh on Post Update', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_refresh_on_post_update_enabled" type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_refresh_on_post_update')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('(Recommended: ON) Clear cache whenever a post or page is updated.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Refresh on Cetegory Change', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_refresh_on_category_change_enabled" type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_refresh_on_category_change')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('(Recommended: ON) Clear cache whenever a category is added, updated, or removed.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Refresh on Tag Change', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_refresh_on_tag_change_enabled" type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_refresh_on_tag_change')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('(Recommended: ON) Clear cache whenever a Tag (term) is added, updated, or removed.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="vivio_swift_refresh_events_submit" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </form>
                </div>
            </div>
            </div>
        </div>

    <?php
    }

    function render_tab5()
    {
    
        echo '<div class="message_standard">';
        echo '<p>' . __('The following options can provide additional speed boosts in some situations.', 'vivio-swift') . '</p>';
        echo '</div>';

        global $vivio_swift_global;

        // process advanced settings
        if (isset($_POST['vivio_swift_save_extra_options']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_extra_options_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed in extra options.", 4);
                wp_die("Error: Nonce check failed in extra options.");
            }

            // set and save submitted settings
            $vivio_swift_global->configs->set_value('vivio_swift_remove_query_strings',isset($_POST["vivio_swift_remove_query_strings"])?'1':'');
            $vivio_swift_global->configs->save_config();

            $this->show_msg_settings_updated();
        }

        ?>
        <div id="vivio-swift-container">

            <form action=""
                id="vivio_swift_cache_extra_settings"
                method="POST">
                <?php wp_nonce_field('vivio_swift_extra_options_nonce'); ?>

                <div class="postbox">
                    <h3 class="hndle">
                        <label for="title"><?php _e('Extras', 'vivio-swift');?></label>
                    </h3>
                    <div class="inside">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Remove Query Strings', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_remove_query_strings"
                                            id="vivio_swift_remove_query_strings"
                                            type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_remove_query_strings')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('(Recommended: ON) Remove version number query strings from static files so they can be cached more easily by 3rd-party services like CDN\'s.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="vivio_swift_save_extra_options" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

} //end class
