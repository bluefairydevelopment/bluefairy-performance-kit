<?php
/**
 * Activation and deactivation lifecycle.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit;

final class Installer {

    private const MIN_PHP = '8.1';
    private const MIN_WP  = '6.4';

    private const SCHEMA_VERSION = '1.1.0';

    /**
     * Run on activation.
     *
     * The advertised minimums and the enforced minimums are the same numbers,
     * read from the constants above. The previous release advertised WordPress
     * 6.4 in both the plugin header and readme.txt while the activation check
     * refused anything below 7.0, so the plugin could not be activated on any
     * version it claimed to support.
     */
    public static function activate(): void {
        self::check_requirements();
        self::set_defaults();

        update_option( 'bfpk_schema_version', self::SCHEMA_VERSION, false );
        update_option( 'bfpk_activated_at', gmdate( 'Y-m-d H:i:s' ), false );
    }

    public static function deactivate(): void {
        // Nothing scheduled and nothing cached by the base plugin.
    }

    private static function check_requirements(): void {
        $errors = [];

        if ( version_compare( PHP_VERSION, self::MIN_PHP, '<' ) ) {
            $errors[] = sprintf(
                /* translators: 1: required PHP version, 2: running PHP version */
                __( 'BlueFairy Performance Kit requires PHP %1$s or higher. You are running PHP %2$s.', 'bluefairy-performance-kit' ),
                self::MIN_PHP,
                PHP_VERSION
            );
        }

        global $wp_version;
        if ( version_compare( (string) $wp_version, self::MIN_WP, '<' ) ) {
            $errors[] = sprintf(
                /* translators: 1: required WordPress version, 2: running WordPress version */
                __( 'BlueFairy Performance Kit requires WordPress %1$s or higher. You are running WordPress %2$s.', 'bluefairy-performance-kit' ),
                self::MIN_WP,
                (string) $wp_version
            );
        }

        if ( [] === $errors ) {
            return;
        }

        if ( ! function_exists( 'deactivate_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        deactivate_plugins( plugin_basename( BFPK_PLUGIN_FILE ) );

        wp_die(
            '<p>' . implode( '</p><p>', array_map( 'esc_html', $errors ) ) . '</p>',
            esc_html__( 'Plugin Activation Error', 'bluefairy-performance-kit' ),
            [ 'back_link' => true ]
        );
    }

    /**
     * Seed the toggle option, every feature on.
     *
     * add_option() only writes when the option is absent, so this is safe to
     * call on every activation and will not undo a customer's choices.
     */
    private static function set_defaults(): void {
        $defaults = [];

        foreach ( array_keys( FeatureRegistry::labels() ) as $key ) {
            $defaults[ $key ] = true;
        }

        add_option( FeatureRegistry::OPTION, $defaults, '', true );
    }
}
