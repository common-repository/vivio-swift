<?php

class Vivio_Swift_Compress_Menu extends Vivio_Swift_Admin_Messages
{
    var $compress_menu_page_slug = VIVIO_SWIFT_COMPRESS_MENU_SLUG;

    var $menu_tabs;

    var $menu_tabs_handler = array(
        'tab1' => 'render_tab1',
        'tab2' => 'render_tab2'
    );

    function __construct()
    {
        $this->render_menu_page();
    }

    function set_menu_tabs()
    {
        $this->menu_tabs = array(
            'tab1' => __('Settings', 'vivio-swift'),
            'tab2' => __('Reports', 'vivio-swift'),
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
            echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->compress_menu_page_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }

    /*
     * The menu rendering goes here
     */
    function render_menu_page()
    {
        echo '<div class="wrap">';
        echo '<h2>' . __('Vivio Swift Compression', 'vivio-swift') . '</h2>';//Interface title
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
    
        echo '<div class="message_standard">';
        echo '<p>' . __('Vivio Swift Compression options reduce the size and number of requests needed by your visitors to access your site.', 'vivio-swift') . '</p>';
        echo '</div>';
        global $vivio_swift_global;

        if (isset($_POST['vivio_swift_save_compression_settings']))
        {
            $error = '';

            // verify nonce
            $nonce = $_REQUEST['_wpnonce'];
            if (!wp_verify_nonce($nonce, 'vivio_swift_compression_settings_nonce')) {
                $vivio_swift_global->debug_logger->log_debug("Nonce check failed on compression settings.", 4);
                wp_die("Error: Nonce check failed on compression settings.");
            }

            if($error)
            {
                $this->show_msg_error(__('Attention!','vivio-swift').$error);
            }

            // save settings
            $vivio_swift_global->configs->set_value('vivio_swift_combine_css',isset($_POST["vivio_swift_combine_css"])?'1':'');
            $vivio_swift_global->configs->set_value('vivio_swift_combine_js',isset($_POST["vivio_swift_combine_js"])?'1':'');
            $vivio_swift_global->configs->save_config();

            
            $this->show_msg_settings_updated();
        }

        ?>
        <div id="vivio-swift-container">

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e('Compression Settings', 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <form action=""
                        id="vivio_swift_compression_settings"
                        method="POST">
                        <?php wp_nonce_field('vivio_swift_compression_settings_nonce'); ?>
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?php _e('Combine Multiple CSS Files', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_combine_css" type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_combine_css')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('(Recommended: ON) Combining CSS files reduces the number of requests sent to your server and speeds up load time.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?php _e('Combine Multiple JS Files', 'vivio-swift')?>:</th>
                                <td>
                                    <label class="switch">
                                        <input name="vivio_swift_combine_js" type="checkbox"<?php if($vivio_swift_global->configs->get_value('vivio_swift_combine_js')=='1') echo ' checked="checked"'; ?> value="1"/>
                                        <span class="slider round"></span>
                                    </label>
                                <span class="description"><?php _e('(Recommended: ON) Combining JavaScript files reduces the number of requests sent to your server and speeds up load time.', 'vivio-swift'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="submit" name="vivio_swift_save_compression_settings" value="<?php _e('Save Settings', 'vivio-swift')?>" class="pure-button" />
                    </form>
                </div>
            </div>

        </div>

    <?php
    }


    function render_tab2()
    {
        global $vivio_swift_global;

        ?>
        <div id="vivio-swift-container">

            <div class="postbox">
                <h3 class="hndle">
                    <label for="title"><?php _e('Reports', 'vivio-swift');?></label>
                </h3>

                <div class="inside">
                    <pre>
                    <?php global $wp_scripts; var_dump($wp_scripts); ?>
                    </pre>
                </div>
            </div>
        </div>
    <?php
    }

} //end class
