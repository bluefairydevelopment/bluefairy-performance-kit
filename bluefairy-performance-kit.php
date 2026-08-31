<?php
/**
 * Plugin Name: BlueFairy Performance Kit
 * Plugin URI:  https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit/
 * Description: The WordPress front-end weight you never asked for — emoji scripts, oEmbed JS, the heartbeat — removed, plus native lazy loading. No configuration required. Add BlueFairy Performance Kit Pro for critical CSS, LQIP images, resource hints and self-hosted Google Fonts.
 * Version:     1.1.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author:      Blue Fairy Development
 * Author URI:  https://bluefairydevelopment.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: bluefairy-performance-kit
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'BFPK_PLUGIN_FILE', __FILE__ );
define( 'BFPK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BFPK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BFPK_VERSION', '1.1.0' );

/**
 * Autoloader — no Composer required.
 *
 * The add-on registers its own autoloader for its own namespace. Neither
 * plugin loads the other's classes directly; they meet at the hooks in
 * Plugin::boot().
 */
spl_autoload_register(
    static function ( string $class ): void {
        $prefix = 'BlueFairy\\PerformanceKit\\';
        if ( ! str_starts_with( $class, $prefix ) ) {
            return;
        }
        $relative = substr( $class, strlen( $prefix ) );
        $file     = __DIR__ . '/includes/' . str_replace( '\\', '/', $relative ) . '.php';
        if ( is_readable( $file ) ) {
            require $file;
        }
    }
);

/**
 * Marker function the add-on checks for.
 *
 * An add-on cannot rely on a constant alone: constants survive a fatal during
 * boot, so a half-loaded plugin would still look present.
 */
function bfpk_plugin(): \BlueFairy\PerformanceKit\Plugin {
    return \BlueFairy\PerformanceKit\Plugin::instance();
}

// Lifecycle hooks must be registered at file scope, not inside another hook.
register_activation_hook( __FILE__, [ \BlueFairy\PerformanceKit\Installer::class, 'activate' ] );
register_deactivation_hook( __FILE__, [ \BlueFairy\PerformanceKit\Installer::class, 'deactivate' ] );

add_action( 'plugins_loaded', [ \BlueFairy\PerformanceKit\Plugin::class, 'boot' ] );
