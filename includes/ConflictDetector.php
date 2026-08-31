<?php
/**
 * Detects caching and optimisation plugins that overlap with this one.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit;

final class ConflictDetector {

    /**
     * Plugin file => display name.
     *
     * @var array<string, string>
     */
    private const KNOWN = [
        'wp-rocket/wp-rocket.php'           => 'WP Rocket',
        'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache',
        'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
        'autoptimize/autoptimize.php'       => 'Autoptimize',
        'wp-super-cache/wp-cache.php'       => 'WP Super Cache',
        'sg-cachepress/sg-cachepress.php'   => 'SiteGround Optimizer',
    ];

    /** @var string[]|null */
    private static ?array $found = null;

    /**
     * Scan the active plugin list.
     *
     * Runs per request and caches only for the life of that request.
     *
     * The previous implementation wrote the result to an option during
     * activation and never looked again: activating WP Rocket afterwards never
     * produced a warning, and deactivating it left the warning up for ever.
     * A conflict is a property of what is running now, so it is computed now.
     */
    public static function scan(): void {
        if ( null !== self::$found ) {
            return;
        }

        $active = self::active_plugin_files();
        $found  = [];

        foreach ( self::KNOWN as $file => $name ) {
            if ( in_array( $file, $active, true ) ) {
                $found[] = $name;
            }
        }

        self::$found = $found;
    }

    /**
     * @return string[]
     */
    public static function conflicts(): array {
        self::scan();

        return self::$found ?? [];
    }

    public static function has_conflicts(): bool {
        return [] !== self::conflicts();
    }

    /**
     * Active plugins, including network-activated ones on multisite.
     *
     * Read from the option directly rather than through is_plugin_active(),
     * which lives in a wp-admin include that is not loaded on the front end.
     *
     * @return string[]
     */
    private static function active_plugin_files(): array {
        $active = (array) get_option( 'active_plugins', [] );

        if ( is_multisite() ) {
            $network = get_site_option( 'active_sitewide_plugins', [] );
            if ( is_array( $network ) ) {
                $active = array_merge( $active, array_keys( $network ) );
            }
        }

        return array_values( array_filter( array_map( 'strval', $active ) ) );
    }

    /**
     * Reset the per-request cache. Test seam.
     */
    public static function reset(): void {
        self::$found = null;
    }
}
