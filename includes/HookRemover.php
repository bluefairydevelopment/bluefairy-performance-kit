<?php
/**
 * Removes a core callback whatever priority it happens to be bound at.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit;

final class HookRemover {

    /**
     * Remove a named function from a hook, at its real priority.
     *
     * `remove_action( $hook, $fn )` silently does nothing unless the priority
     * matches, and it defaults to 10. WordPress binds
     * `wp_oembed_add_discovery_links` at priority **4**, so the obvious call
     * removes nothing and the discovery links keep being printed — which is
     * exactly what the previous release shipped, while its readme said the
     * links were removed.
     *
     * Asking `has_action()` for the current priority instead of hardcoding one
     * keeps this working if core ever moves the callback again.
     *
     * @param string $hook     Hook name.
     * @param string $function Callback function name.
     * @return bool True when something was removed.
     */
    public static function remove( string $hook, string $function ): bool {
        $removed  = false;
        $priority = has_action( $hook, $function );

        // has_action() returns false when absent, or the integer priority —
        // which can legitimately be 0, so compare types explicitly.
        while ( false !== $priority ) {
            remove_action( $hook, $function, (int) $priority );
            $removed = true;

            // A callback can be bound at more than one priority.
            $next = has_action( $hook, $function );
            if ( $next === $priority ) {
                // Defensive: removal did not take, so stop rather than spin.
                break;
            }
            $priority = $next;
        }

        return $removed;
    }

    /**
     * The filter equivalent. Actions and filters share storage in WordPress,
     * but keeping the intent explicit at the call site is worth the wrapper.
     */
    public static function remove_filter_callback( string $hook, string $function ): bool {
        return self::remove( $hook, $function );
    }
}
