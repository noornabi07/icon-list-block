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
 */

// ABS PATH
if (!defined('ABSPATH')) {
    exit;
}

if (function_exists('ilb_fs')) {
    // This for .. if free plugin is installed, and when we will install pro plugin then uninstall free plugin
    register_activation_hook(__FILE__, function () {
        if (is_plugin_active('icon-list-block/icon-list-block.php')) {
            deactivate_plugins('icon-list-block/icon-list-block.php');
        }
        if (is_plugin_active('icon-list-block-pro/icon-list-block.php')) {
            deactivate_plugins('icon-list-block-pro/icon-list-block.php');
        }
    });
} else {
    // Constant
    define('ILB_VERSION', isset($_SERVER['HTTP_HOST']) && 'localhost' === $_SERVER['HTTP_HOST'] ? time() : '1.0.9');
    define('ILB_DIR_URL', plugin_dir_url(__FILE__));
    define('ILB_DIR_PATH', plugin_dir_path(__FILE__));
    define('ILB_HAS_FREE', 'icon-list-block/icon-list-block.php' === plugin_basename(__FILE__));
    define('ILB_HAS_PRO', 'icon-list-block-pro/icon-list-block.php' === plugin_basename(__FILE__));


    if (! function_exists('ilb_fs')) {
        if (! function_exists('ilb_fs')) {
            // Create a helper function for easy SDK access.
            function ilb_fs()
            {
                global $ilb_fs;

                if (! isset($ilb_fs)) {
                    $fsStartPath = dirname(__FILE__) . '/freemius/start.php';
                    $bSDKInitPath = dirname(__FILE__) . '/bplugins_sdk/init.php';

                    // Include Freemius SDK.
                    if (ILB_HAS_PRO && file_exists($fsStartPath)) {
                        require_once $fsStartPath;
                    } else if (ILB_HAS_FREE && file_exists($bSDKInitPath)) {
                        require_once $bSDKInitPath;
                    }


                    $libConfig = fs_dynamic_init(array(
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
                            'first-path'     => 'plugins.php',
                            'support'        => false,
                        ),
                    ));

                    $ilb_fs = (ILB_HAS_PRO && file_exists($fsStartPath)) ? fs_dynamic_init($libConfig) : fs_lite_dynamic_init($libConfig);
                }

                return $ilb_fs;
            }

            // Init Freemius.
            ilb_fs();
            // Signal that SDK was initiated.
            do_action('ilb_fs_loaded');
        }
    }

    // ... Your plugin's main file logic ...

    function libIsPremium()
    {
        return ILB_HAS_PRO ? ilb_fs()->can_use_premium_code() : false;
    }


    if (!class_exists('ILBPlugin')) {
        class ILBPlugin
        {
            public function __construct()
            {
                add_action('enqueue_block_assets', [$this, 'enqueueBlockAssets']);
                add_action('init', [$this, 'onInit']);

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
        }

        new ILBPlugin();
    }
}
