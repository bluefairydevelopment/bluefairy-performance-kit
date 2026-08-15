<?php

/**
 * Plugin Name: BlueFairy Performance Kit Lite
 * Plugin URI: https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit/
 * Description: Essential WordPress performance optimisations — native lazy loading, emoji removal, embed cleanup, and heartbeat control. Zero configuration required.
 * Version: 1.0.0
 * Author: Blue Fairy Development
 * Author URI: https://bluefairydevelopment.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bluefairy-performance-kit-lite
 * Requires at least: 6.4
 * Requires PHP: 8.1
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
    exit;
}

define('BFPKL_VERSION', '1.0.0');
define('BFPKL_FILE', __FILE__);
define('BFPKL_DIR', plugin_dir_path(__FILE__));

require BFPKL_DIR . 'includes/class-plugin.php';

add_action('plugins_loaded', function (): void {
    BlueFairy\PerformanceKitLite\Plugin::boot();
});
