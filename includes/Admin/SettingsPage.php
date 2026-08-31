<?php
/**
 * Settings → Performance.
 *
 * The add-on registers into this page and this option group rather than adding
 * a second screen, so a customer running both sees one set of settings.
 *
 * @package BlueFairy\PerformanceKit\Admin
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit\Admin;

use BlueFairy\PerformanceKit\ConflictDetector;
use BlueFairy\PerformanceKit\FeatureRegistry;

final class SettingsPage {

    public const PAGE  = 'bfpk-settings';
    public const GROUP = 'bfpk_settings';

    public static function register(): void {
        add_action( 'admin_menu', [ self::class, 'add_menu' ] );
        add_action( 'admin_init', [ self::class, 'register_settings' ] );
        add_action( 'admin_notices', [ self::class, 'conflict_notice' ] );
        add_filter(
            'plugin_action_links_' . plugin_basename( BFPK_PLUGIN_FILE ),
            [ self::class, 'action_links' ]
        );
    }

    public static function add_menu(): void {
        add_options_page(
            __( 'Performance', 'bluefairy-performance-kit' ),
            __( 'Performance', 'bluefairy-performance-kit' ),
            'manage_options',
            self::PAGE,
            [ self::class, 'render' ]
        );
    }

    public static function register_settings(): void {
        register_setting(
            self::GROUP,
            FeatureRegistry::OPTION,
            [
                'type'              => 'array',
                'sanitize_callback' => [ FeatureRegistry::class, 'sanitize' ],
                'default'           => [],
            ]
        );

        add_settings_section(
            'bfpk_features_section',
            __( 'Features', 'bluefairy-performance-kit' ),
            '__return_null',
            self::PAGE
        );

        add_settings_field(
            'bfpk_features_field',
            __( 'Active Features', 'bluefairy-performance-kit' ),
            [ self::class, 'render_features_field' ],
            self::PAGE,
            'bfpk_features_section'
        );

        /**
         * Register additional settings on this page and in this group.
         *
         * The add-on hooks this to add its own section and fields. Registering
         * into the base's group is what keeps everything on one Save button.
         *
         * @since 1.1.0
         *
         * @param string $page  Settings page slug.
         * @param string $group Option group.
         */
        do_action( 'bfpk_register_settings', self::PAGE, self::GROUP );
    }

    /**
     * One checkbox per registered feature, base and add-on alike.
     */
    public static function render_features_field(): void {
        $labels       = FeatureRegistry::labels();
        $descriptions = FeatureRegistry::descriptions();

        foreach ( $labels as $key => $label ) {
            printf(
                '<label style="display:block;margin-bottom:0.6rem;"><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s> %4$s',
                esc_attr( FeatureRegistry::OPTION ),
                esc_attr( $key ),
                checked( FeatureRegistry::is_enabled( $key ), true, false ),
                esc_html( $label )
            );

            if ( isset( $descriptions[ $key ] ) && '' !== $descriptions[ $key ] ) {
                printf(
                    '<span class="description" style="display:block;margin-left:1.7rem;">%s</span>',
                    esc_html( $descriptions[ $key ] )
                );
            }

            echo '</label>';
        }
    }

    public static function render(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'bluefairy-performance-kit' ) );
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields( self::GROUP );
                do_settings_sections( self::PAGE );
                submit_button();
                ?>
            </form>

            <?php
            /**
             * Render below the settings form.
             *
             * @since 1.1.0
             */
            do_action( 'bfpk_settings_page_footer' );
            ?>
        </div>
        <?php
    }

    /**
     * Warn about overlapping caching plugins, on our own screen only.
     *
     * The previous version rendered this on every admin page, from an option
     * written once at activation — so it never appeared for a plugin activated
     * later, and never went away for one deactivated later.
     */
    public static function conflict_notice(): void {
        $screen = get_current_screen();

        if ( ! $screen || 'settings_page_' . self::PAGE !== $screen->id ) {
            return;
        }

        $conflicts = ConflictDetector::conflicts();

        if ( [] === $conflicts ) {
            return;
        }
        ?>
        <div class="notice notice-warning">
            <p>
                <strong><?php esc_html_e( 'Another optimisation plugin is active', 'bluefairy-performance-kit' ); ?></strong>
            </p>
            <ul style="list-style:disc;margin-left:1.5rem;">
                <?php foreach ( $conflicts as $conflict ) : ?>
                    <li><?php echo esc_html( $conflict ); ?></li>
                <?php endforeach; ?>
            </ul>
            <p>
                <?php esc_html_e( 'These plugins may already handle some of the optimisations below. Switching a feature off here is safe — nothing breaks if both are on, but the work is done twice.', 'bluefairy-performance-kit' ); ?>
            </p>
        </div>
        <?php
    }

    /**
     * @param string[] $links
     * @return string[]
     */
    public static function action_links( array $links ): array {
        array_unshift(
            $links,
            sprintf(
                '<a href="%s">%s</a>',
                esc_url( admin_url( 'options-general.php?page=' . self::PAGE ) ),
                esc_html__( 'Settings', 'bluefairy-performance-kit' )
            )
        );

        return $links;
    }
}
