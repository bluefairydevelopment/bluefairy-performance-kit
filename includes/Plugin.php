<?php
/**
 * Plugin bootstrap — wires the base subsystems and opens the seams the add-on uses.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit;

use BlueFairy\PerformanceKit\Admin\ProUpsell;
use BlueFairy\PerformanceKit\Admin\SettingsPage;
use BlueFairy\PerformanceKit\Features\EmbedRemoval;
use BlueFairy\PerformanceKit\Features\EmojiRemoval;
use BlueFairy\PerformanceKit\Features\HeartbeatControl;
use BlueFairy\PerformanceKit\Features\LazyLoading;

final class Plugin {

    private static ?self $instance = null;

    private bool $booted = false;

    private function __construct() {}

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Boot on plugins_loaded.
     */
    public static function boot(): void {
        $plugin = self::instance();

        // Guard against a double bind. Two calls would register every hook
        // twice, and the emoji/embed removals are idempotent while the
        // the_content filter is not.
        if ( $plugin->booted ) {
            return;
        }
        $plugin->booted = true;

        // Run conflict detection once per request; the result is cached in the
        // detector, not in an option, so it reflects the CURRENT set of active
        // plugins rather than whatever was active at activation time.
        ConflictDetector::scan();

        /**
         * The base plugin is ready to be extended.
         *
         * Fired BEFORE the subsystems register, which is the only ordering
         * that works. An add-on cannot add a feature toggle after the settings
         * page has already read the label list, and cannot register a renderer
         * after the hooks that would call it are bound.
         *
         * This does not fire at all when the base plugin is inactive, so an
         * add-on must ALSO check the dependency on plugins_loaded if it wants
         * to explain itself to the user.
         *
         * @since 1.1.0
         */
        do_action( 'bfpk_loaded' );

        $plugin->register_features();

        if ( is_admin() ) {
            SettingsPage::register();
            ProUpsell::register();
        }
    }

    /**
     * Bind the base plugin's own optimisations, each behind its toggle.
     */
    private function register_features(): void {
        if ( FeatureRegistry::is_enabled( 'lazy_loading' ) ) {
            ( new LazyLoading() )->register();
        }

        if ( FeatureRegistry::is_enabled( 'emoji' ) ) {
            ( new EmojiRemoval() )->register();
        }

        if ( FeatureRegistry::is_enabled( 'embeds' ) ) {
            ( new EmbedRemoval() )->register();
        }

        if ( FeatureRegistry::is_enabled( 'heartbeat' ) ) {
            ( new HeartbeatControl() )->register();
        }
    }
}
