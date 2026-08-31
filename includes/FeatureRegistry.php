<?php
/**
 * The feature registry.
 *
 * One array option holds every toggle, base and add-on alike, and one filter
 * defines what those toggles are. The same list drives the settings checkboxes,
 * the sanitiser and every runtime `is_enabled()` check — so an add-on that adds
 * a label gets a working, persistable, sanitised toggle with no further work.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit;

final class FeatureRegistry {

    public const OPTION = 'bfpk_features';

    /**
     * Labels for every toggleable feature, keyed by feature key.
     *
     * @return array<string, string>
     */
    public static function labels(): array {
        $labels = [
            'lazy_loading' => __( 'Native lazy loading', 'bluefairy-performance-kit' ),
            'emoji'        => __( 'Remove emoji scripts', 'bluefairy-performance-kit' ),
            'embeds'       => __( 'Remove oEmbed scripts', 'bluefairy-performance-kit' ),
            'heartbeat'    => __( 'Disable the front-end heartbeat', 'bluefairy-performance-kit' ),
        ];

        /**
         * Filter the list of toggleable features.
         *
         * Adding a key here is all an add-on needs to do to gain a toggle:
         * the checkbox, the sanitiser and is_enabled() all read this list.
         *
         * @since 1.1.0
         *
         * @param array<string, string> $labels Feature key => human label.
         */
        return (array) apply_filters( 'bfpk_feature_labels', $labels );
    }

    /**
     * Longer descriptions, shown beneath each checkbox. Optional per key.
     *
     * @return array<string, string>
     */
    public static function descriptions(): array {
        $descriptions = [
            'lazy_loading' => __( 'Adds loading="lazy" to iframes in post content. WordPress already does this for images.', 'bluefairy-performance-kit' ),
            'emoji'        => __( 'Removes the emoji detection script, its styles and the s.w.org DNS prefetch.', 'bluefairy-performance-kit' ),
            'embeds'       => __( 'Removes oEmbed discovery links and the wp-embed script from the front end.', 'bluefairy-performance-kit' ),
            'heartbeat'    => __( 'Stops the heartbeat polling on the front end only. It keeps running in wp-admin and the editor.', 'bluefairy-performance-kit' ),
        ];

        /**
         * Filter the per-feature descriptions.
         *
         * @since 1.1.0
         *
         * @param array<string, string> $descriptions Feature key => description.
         */
        return (array) apply_filters( 'bfpk_feature_descriptions', $descriptions );
    }

    /**
     * Is a feature switched on?
     *
     * A key that is absent from the stored option counts as ENABLED. That
     * matters for the add-on: its four features appear the moment it activates,
     * without needing the option rewritten, and without a migration that would
     * have to run before its own toggles were registered.
     */
    public static function is_enabled( string $key ): bool {
        $features = self::all();

        return array_key_exists( $key, $features ) ? (bool) $features[ $key ] : true;
    }

    /**
     * The stored toggle state.
     *
     * @return array<string, bool>
     */
    public static function all(): array {
        $stored = get_option( self::OPTION, [] );

        return is_array( $stored ) ? $stored : [];
    }

    /**
     * Sanitise the submitted toggle array.
     *
     * Driven by labels(), so a key an add-on registered survives a save and a
     * key nobody registered is discarded. Anything missing from the submission
     * is an unticked checkbox, which is false — this is why the whole set is
     * rebuilt from the label list rather than merged into what was there.
     *
     * @param mixed $input
     * @return array<string, bool>
     */
    public static function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $clean = [];

        foreach ( array_keys( self::labels() ) as $key ) {
            $clean[ $key ] = ! empty( $input[ $key ] );
        }

        return $clean;
    }
}
