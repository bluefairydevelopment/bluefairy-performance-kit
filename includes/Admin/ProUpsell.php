<?php
/**
 * Points free users at the Pro add-on.
 *
 * Every feature named here must ship in Pro and must actually work. This copy
 * is the first place a prospective buyer reads what Pro does, and a claim that
 * fails on contact with the product is worse than no claim.
 *
 * @package BlueFairy\PerformanceKit\Admin
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit\Admin;

final class ProUpsell {

    private const PRODUCT_URL = 'https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit-pro/';

    public static function register(): void {
        add_filter(
            'plugin_action_links_' . plugin_basename( BFPK_PLUGIN_FILE ),
            [ self::class, 'add_action_link' ]
        );
        add_action( 'bfpk_settings_page_footer', [ self::class, 'render_callout' ] );
    }

    /**
     * Is the add-on already running?
     */
    private static function pro_active(): bool {
        return defined( 'BFPKP_VERSION' );
    }

    /**
     * "Get Performance Kit Pro" in the plugins-list row.
     *
     * "Get", not "Upgrade": Pro runs alongside this plugin rather than
     * replacing it, and "upgrade" reads as a replacement — which is exactly
     * the misunderstanding that would make a customer deactivate this plugin
     * and lose everything it does.
     *
     * @param string[] $links
     * @return string[]
     */
    public static function add_action_link( array $links ): array {
        if ( self::pro_active() ) {
            return $links;
        }

        array_unshift(
            $links,
            sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" style="font-weight:600;color:#1d4ed8;">%s</a>',
                esc_url( self::PRODUCT_URL ),
                esc_html__( 'Get Performance Kit Pro', 'bluefairy-performance-kit' )
            )
        );

        return $links;
    }

    public static function render_callout(): void {
        if ( self::pro_active() ) {
            return;
        }
        ?>
        <div class="bfpk-pro-callout" style="margin-top:2rem;padding:1rem 1.25rem;background:#eff6ff;border:1px solid #bfdbfe;border-radius:4px;max-width:46rem;">
            <h2 style="margin-top:0;font-size:1rem;">
                <?php esc_html_e( 'BlueFairy Performance Kit Pro', 'bluefairy-performance-kit' ); ?>
            </h2>
            <p style="margin-bottom:0.5rem;">
                <?php esc_html_e( 'This plugin removes weight WordPress adds by default. Pro goes after the Core Web Vitals themselves:', 'bluefairy-performance-kit' ); ?>
            </p>
            <ul style="margin:0 0 0.75rem 1.25rem;list-style:disc;">
                <li><?php esc_html_e( 'Critical CSS extracted per page template and inlined, with the rest of your stylesheets deferred', 'bluefairy-performance-kit' ); ?></li>
                <li><?php esc_html_e( 'Self-hosted Google Fonts — the font files are downloaded to your uploads folder and the request to fonts.googleapis.com disappears', 'bluefairy-performance-kit' ); ?></li>
                <li><?php esc_html_e( 'Resource hints: preconnect for third-party origins and a preload for the likely LCP image', 'bluefairy-performance-kit' ); ?></li>
                <li><?php esc_html_e( 'Blurred placeholders for WooCommerce product images, swapped in as they enter the viewport', 'bluefairy-performance-kit' ); ?></li>
            </ul>
            <p style="margin-bottom:0;">
                <a href="<?php echo esc_url( self::PRODUCT_URL ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary">
                    <?php esc_html_e( 'See Performance Kit Pro', 'bluefairy-performance-kit' ); ?>
                </a>
                <span style="margin-left:0.5rem;color:#4a6080;">
                    <?php esc_html_e( 'Installs alongside this plugin. Your settings carry straight over.', 'bluefairy-performance-kit' ); ?>
                </span>
            </p>
        </div>
        <?php
    }
}
