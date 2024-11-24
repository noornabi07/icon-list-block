<?php

/**
 * Plugin Name: Icon List Block
 * Description: Show your icon list in web.
 * Version: 1.0.9
 * Author: bPlugins
 * Author URI: https://bplugins.com
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: icon-list
 * @fs_premium_only /freemius
 * @fs_free_only, /bplugins_sdk
 */

// ABS PATH
if (!defined('ABSPATH')) {
    exit;
}

if (function_exists('ilb_fs')) {
    // This for .. if free plugin is installed, and when we will install pro plugin then uninstall free plugin
    register_activation_hook(__FILE__, function () {
        if (is_plugin_active('icon-list-block/index.php')) {
            deactivate_plugins('icon-list-block/index.php');
        }
        if (is_plugin_active('icon-list-block-pro/index.php')) {
            deactivate_plugins('icon-list-block-pro/index.php');
        }
    });
} else {
    // Constant
    define('ILB_VERSION', isset($_SERVER['HTTP_HOST']) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.9');
    define('ILB_DIR_URL', plugin_dir_url(__FILE__));
    define('ILB_DIR_PATH', plugin_dir_path(__FILE__));
    define('ILB_HAS_FREE', 'icon-list-block/index.php' === plugin_basename(__FILE__));
    define('ILB_HAS_PRO', 'icon-list-block-pro/index.php' === plugin_basename(__FILE__));


    if (! function_exists('ctb_fs')) {
        function ctb_fs()
        {
            global $ctb_fs;

            if (! isset($ctb_fs)) {
                $fsStartPath = dirname(__FILE__) . '/freemius/start.php';
                $bSDKInitPath = dirname(__FILE__) . '/bplugins_sdk/init.php';

                if (ILB_HAS_PRO && file_exists($fsStartPath)) {
                    require_once $fsStartPath;
                } else if (ILB_HAS_FREE && file_exists($bSDKInitPath)) {
                    require_once $bSDKInitPath;
                }

                $ctbConfig = [
                    'id'                  => '17159',
                    'slug'                => 'icon-list-block',
                    'premium_slug'        => 'icon-list-block-pro',
                    'type'                => 'plugin',
                    'public_key'          => 'pk_230e85e0630d1d7e5dfc8fa3297d2',
                    'is_premium'          => true,
                    'premium_suffix'      => 'Pro',
                    // If your plugin is a serviceware, set this option to false.
                    'has_premium_version' => true,
                    'has_addons'          => false,
                    'has_paid_plans'      => true,
                    'menu'                => array(
                        'slug'           => 'icon-list-block',
                        'contact'        => false,
                        'support'        => false,
                        'parent'         => array(
                            'slug' => 'tools.php',
                        ),
                    ),
                ];

                $ctb_fs = (ILB_HAS_PRO && file_exists($fsStartPath)) ? fs_dynamic_init($ctbConfig) : fs_lite_dynamic_init($ctbConfig);
            }

            return $ctb_fs;
        }

        ctb_fs();
        do_action('ctb_fs_loaded');
    }

    // ... Your plugin's main file logic ...

    function libIsPremium()
    {
        return false;
        // return ILB_HAS_PRO ? ilb_fs()->can_use_premium_code() : false;
    }


    if (!class_exists('ILBPlugin')) {
        class ILBPlugin
        {
            public function __construct()
            {
                add_action('enqueue_block_assets', [$this, 'enqueueBlockAssets']);
                add_action('init', [$this, 'onInit']);

                // sub menu function hooks
                add_action('admin_menu', [$this, 'addToolsSubmenu']);

                // Premium checker
                add_action('wp_ajax_libPipeChecker', [$this, 'libPipeChecker']);
                add_action('wp_ajax_nopriv_libPipeChecker', [$this, 'libPipeChecker']);
            }

            function libPipeChecker()
            {
                $nonce = $_POST['_wpnonce'] ?? null;

                if (!wp_verify_nonce($nonce, 'wp_ajax')) {
                    wp_send_json_error('Invalid Request');
                }

                wp_send_json_success([
                    'isPipe' => libIsPremium()
                ]);
            }
            function enqueueBlockAssets()
            {
                wp_register_style('fontAwesome', ILB_DIR_URL . 'assets/css/font-awesome.min.css', [], '6.4.2'); // Icon
            }

            function onInit()
            {
                register_block_type(__DIR__ . '/build');
            }

            function addToolsSubmenu()
            {
                add_submenu_page(
                    'tools.php',                   // Parent slug (Tools menu)
                    __('Icon List Block', 'icon-list'), // Page title
                    __('Icon List Block', 'icon-list'), // Menu title
                    'manage_options',              // Capability required to access this menu
                    'icon-list-block',             // Menu slug
                    [$this, 'renderToolsPage']     // Callback function to render the page
                );
            }

            function renderToolsPage()
            {
?>
                <div class="wrap">
                    <h1><?php esc_html_e('Icon List Tools', 'icon-list'); ?></h1>
                    <p><?php esc_html_e('Welcome to the Icon List Tools submenu!', 'icon-list'); ?></p>
                </div>
<?php
            }
        }

        new ILBPlugin();
    }
}
