<?php

/*
  Plugin Name: WPI Custom Plugin
  Plugin URI: http://ignizeo.com/
  Description: This plugin is used to create a form and create dashboard for Admin user with functionality of google login
  Version: 1.0.0
  Author: Kuldip Makadiya
  Author URI: https://profiles.wordpress.org/kuldip_raghu
 */

/**
 * Basic plugin definitions 
 * 
 * @package WPI Custom Plugin
 * @since 1.0.0
 */
if (!defined('WPI_CUSTOM_DIR')) {
    define('WPI_CUSTOM_DIR', dirname(__FILE__));      // Plugin dir
}
if (!defined('WPI_CUSTOM_URL')) {
    define('WPI_CUSTOM_URL', plugin_dir_url(__FILE__));   // Plugin url
}
if (!defined('WPI_CUSTOM_INC_DIR')) {
    define('WPI_CUSTOM_INC_DIR', WPI_CUSTOM_DIR . '/includes');   // Plugin include dir
}
if (!defined('WPI_CUSTOM_INC_URL')) {
    define('WPI_CUSTOM_INC_URL', WPI_CUSTOM_URL . 'includes');    // Plugin include url
}
if (!defined('WPI_CUSTOM_ADMIN_DIR')) {
    define('WPI_CUSTOM_ADMIN_DIR', WPI_CUSTOM_INC_DIR . '/admin');  // Plugin admin dir
}
if (!defined('WPI_CUSTOM_PREFIX')) {
    define('WPI_CUSTOM_PREFIX', 'wpi_custom'); // Plugin Prefix
}
if (!defined('WPI_CUSTOM_VAR_PREFIX')) {
    define('WPI_CUSTOM_VAR_PREFIX', '_wpi_custom_'); // Variable Prefix
}
if (!defined('WPI_CUSTOM_POST_TYPE_MY_ECKERDS')) {
    define('WPI_CUSTOM_POST_TYPE_MY_ECKERDS', 'my_eckerds'); // Post Type for My Eckerds
}

if (!defined('WPI_CUSTOM_CLIENT_ID')) {
    define('WPI_CUSTOM_CLIENT_ID', '549757786099-gk5on2io4c34qp3rnv0c76ab7688i9fm.apps.googleusercontent.com'); // Post Type for My Eckerds
}

if (!defined('WPI_CUSTOM_CLIENT_SECRET')) {
    define('WPI_CUSTOM_CLIENT_SECRET', 'cA0n1L9FEGF8CAaM7wfa63n4'); // Post Type for My Eckerds
}

if (!defined('WPI_CUSTOM_REDIRECT_URI')) {
    define('WPI_CUSTOM_REDIRECT_URI', 'http://localhost/wordpress777/?dashboard=user'); // Post Type for My Eckerds
}

if (!defined('WPI_CUSTOM_BASENAME')) {
    define('WPI_CUSTOM_BASENAME', basename(WPI_CUSTOM_DIR)); // base name
}

/**
 * Load Text Domain
 *
 * This gets the plugin ready for translation.
 *
 * @package WPI Custom Plugin
 * @since 1.0.0
 */
function wpi_custom_plugins_loaded() {
    // Set filter for plugin's languages directory
    $wpi_custom_lang_dir = dirname(plugin_basename(__FILE__)) . '/languages/';
    $wpi_custom_lang_dir = $wpi_custom_lang_dir;
    // Traditional WordPress plugin locale filter
    $locale = apply_filters('plugin_locale', get_locale(), 'wpicustomplugin');
    $mofile = sprintf('%1$s-%2$s.mo', 'wpicustomplugin', $locale);
    // Setup paths to current locale file
    $mofile_local = $wpi_custom_lang_dir . $mofile;
    $mofile_global = WP_LANG_DIR . '/' . WPI_CUSTOM_BASENAME . '/' . $mofile;
    if (file_exists($mofile_global)) {
        load_textdomain('wpicustomplugin', $mofile_global);
    } elseif (file_exists($mofile_local)) {
        load_textdomain('wpicustomplugin', $mofile_local);
    } else { // Load the default language files
        load_plugin_textdomain('wpicustomplugin', false, $wpi_custom_lang_dir);
    }
}

add_action('plugins_loaded', 'wpi_custom_plugins_loaded');


/**
 * Activation Hook
 *
 * Register plugin activation hook.
 *
 * @package WPI Custom Plugin
 * @since 1.0.0
 */
register_activation_hook(__FILE__, 'wpi_custom_install');

function wpi_custom_install() {
    add_role('patient', 'Patient', array('read' => true, 'level_0' => true));
    add_role('admin', 'Admin', array('read' => true, 'level_0' => true));
    $login_page = get_option('wpi_custom_login_page');
    if (isset($login_page) && empty($login_page)) {
        $curr_page = array(
            'post_title' => __('Login Page', 'wpicustomplugin'),
            'post_content' => '[wpi_custom_login]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_category' => array(1),
            'post_parent' => 0);
        $curr_created = wp_insert_post($curr_page);
        update_option('wpi_custom_login_page', $curr_created);
    }

    $curr_page = array(
        'post_title' => __('Price Check', 'wpicustomplugin'),
        'post_content' => '[wpi_custom_form]',
        'post_status' => 'publish',
        'post_type' => 'page',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_category' => array(1),
        'post_parent' => 0);
    wp_insert_post($curr_page);
}

/**
 * Deactivation Hook
 *
 * Register plugin deactivation hook.
 *
 * @package WPI Custom Plugin
 * @since 1.0.0
 */
register_deactivation_hook(__FILE__, 'wpi_custom_uninstall');

function wpi_custom_uninstall() {
    global $wpdb;
    $wpi_custom_login_page = get_option('wpi_custom_login_page');
    
    delete_option('wpi_custom_login_page');
}

// Global variables
global $wpi_custom_scripts, $wpi_custom_model, $wpi_custom_admin;

// Script class handles most of script functionalities of plugin
include_once( WPI_CUSTOM_INC_DIR . '/class-wpi-custom-plugin-scripts.php' );
$wpi_custom_scripts = new Wpi_Custom_Scripts();
$wpi_custom_scripts->add_hooks();

// Model class handles most of model functionalities of plugin
include_once( WPI_CUSTOM_INC_DIR . '/class-wpi-custom-plugin-model.php' );
$wpi_custom_model = new Wpi_Custom_Model();

// Admin class handles most of admin panel functionalities of plugin
include_once( WPI_CUSTOM_ADMIN_DIR . '/class-wpi-custom-plugin-admin.php' );
$wpi_custom_admin = new Wpi_Custom_Admin();
$wpi_custom_admin->add_hooks();

// Public class handles most of public panel functionalities of plugin
include_once( WPI_CUSTOM_INC_DIR . '/class-wpi-custom-plugin-public-pages.php' );
$wpi_custom_public_pages = new Wpi_Custom_Public_Pages();
$wpi_custom_public_pages->add_hooks();

// Shortocode file handle to display shortocode in front side
include_once( WPI_CUSTOM_INC_DIR . '/class-wpi-custom-plugin-shortcodes.php' );
$wpi_custom_shortcode = new Wpi_Custom_Shortcode();
$wpi_custom_shortcode->add_hooks();

// Registring Post type functionality
require_once( WPI_CUSTOM_INC_DIR . '/wpi-custom-plugin-post-type.php' );
?>