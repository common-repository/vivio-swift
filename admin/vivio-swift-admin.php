<?php

class Vivio_Swift_Admin {

	var $main_menu_page;
	var $dashboard_menu;
	var $cache_menu;
	var $compress_menu;
    var $exclusions_menu;

	public function __construct() {
		require_once('vivio-swift-admin-messages.php');

		//create admin menus
        add_action('admin_menu', array(&$this, 'create_admin_menus'));

        //init scripts/styles if user is on vivio_swift plugin pages
        if (isset($_GET['page']) && strpos($_GET['page'], VIVIO_SWIFT_MENU_SLUG_PREFIX) !== false) {
            add_action('admin_print_scripts', array(&$this, 'admin_menu_page_scripts'));
            add_action('admin_print_styles', array(&$this, 'admin_menu_page_styles'));
        }
	}

    function admin_menu_page_scripts() 
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('postbox');
        wp_enqueue_script('dashboard');
        wp_enqueue_script('thickbox');
        wp_register_script('vivio-swift-admin-js', VIVIO_SWIFT_URL. '/js/vivio-swift-admin.js', array('jquery'));
        wp_enqueue_script('vivio-swift-admin-js');
    }
    
    function admin_menu_page_styles() 
    {
        wp_enqueue_style('dashboard');
        wp_enqueue_style('thickbox');
        wp_enqueue_style('global');
        wp_enqueue_style('wp-admin');
        wp_enqueue_style('vivio-swift-admin-css', VIVIO_SWIFT_URL. '/css/vivio-swift-admin.css');
    }
    
    function create_admin_menus()
    {
        $vivio_swift_icon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAFcSURBVDiNjZPPSlVRGMV/31URJBFNsSuikIo0CEMEBzUIctQgcJgKPkMvEL6AQ0eizhrYMATBWVOf4Oo1JALDQQimIdn9NbjnyOm0D7gmm/19a6299r8gAbUDeAk8BbqABvA5Ii5S/CTUQXVF3VVv1Wt1Q+2vEkyr0xW9Z2rTNprqZJkwrH5TW+q++jxhMqpeZiYnal+xueP/WE+YzKtv1Lfqu7w4pP5OGKjWK7b0JEvci7qcELbUtQrxQ/U0473uBCZKnF/AakR8TBkAP4CvwDjwuAa0Cs0G8CIXq93qnBo5ISIEPmXTWg04Bf4A68AscKQuqbvAOXAIvC+l+HI3qnX1g7qgbheuqogr9UHhHBbzWmdEnKk3wEHFngF6gDpwnM1ngJ2I+Jk7PlK/V1xljqlCgk114J8lbD/Xs3sajCRzZuexZfvzlDGa0kSqmMV7BYxlpSawFxG3Ze5fNDWhFVEs0JAAAAAASUVORK5CYII=';
        $this->main_menu_page = add_menu_page(__('Vivio Swift', 'vivio-swift'), __('Vivio Swift', 'vivio-swift'), VIVIO_SWIFT_CAPABILITY, VIVIO_SWIFT_MAIN_MENU_SLUG , array(&$this, 'handle_dashboard_menu_rendering'), $vivio_swift_icon);
        add_submenu_page(VIVIO_SWIFT_MAIN_MENU_SLUG, __('Dashboard', 'vivio-swift'),  __('Dashboard', 'vivio-swift') , VIVIO_SWIFT_CAPABILITY, VIVIO_SWIFT_MAIN_MENU_SLUG, array(&$this, 'handle_dashboard_menu_rendering'));
        add_submenu_page(VIVIO_SWIFT_MAIN_MENU_SLUG, __('Cache', 'vivio-swift'),  __('Cache', 'vivio-swift') , VIVIO_SWIFT_CAPABILITY, VIVIO_SWIFT_CACHE_MENU_SLUG, array(&$this, 'handle_cache_menu_rendering'));
        //add_submenu_page(VIVIO_SWIFT_MAIN_MENU_SLUG, __('Compression', 'vivio-swift'),  __('Compression', 'vivio-swift') , VIVIO_SWIFT_CAPABILITY, VIVIO_SWIFT_COMPRESS_MENU_SLUG, array(&$this, 'handle_compress_menu_rendering'));
        add_submenu_page(VIVIO_SWIFT_MAIN_MENU_SLUG, __('Exclusions', 'vivio-swift'),  __('Exclusions', 'vivio-swift') , VIVIO_SWIFT_CAPABILITY, VIVIO_SWIFT_EXCLUSIONS_MENU_SLUG, array(&$this, 'handle_exclusions_menu_rendering'));
    }
        
    function handle_dashboard_menu_rendering()
    {
        include_once('vivio-swift-dashboard-menu.php');
        $this->dashboard_menu = new Vivio_Swift_Dashboard_Menu();
    }
    
    function handle_cache_menu_rendering()
    {
        include_once('vivio-swift-cache-menu.php');
        $this->cache_menu = new Vivio_Swift_Cache_Menu();
    }
    
    function handle_compress_menu_rendering()
    {
        include_once('vivio-swift-compress-menu.php');
        $this->compress_menu = new Vivio_Swift_Compress_Menu();
    }
    
    function handle_exclusions_menu_rendering()
    {
        include_once('vivio-swift-exclusions-menu.php');
        $this->exclusions_menu = new Vivio_Swift_Exclusions_Menu();
    }

}
