<?php
/**
 * Removes the WordPress emoji detection script and everything it drags along.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit\Features;

use BlueFairy\PerformanceKit\HookRemover;

final class EmojiRemoval {

    /**
     * hook => callback, removed at whatever priority core bound them at.
     *
     * @var array<int, array{0:string,1:string}>
     */
    private const REMOVALS = [
        // Detection script.
        [ 'wp_head', 'print_emoji_detection_script' ],
        [ 'admin_print_scripts', 'print_emoji_detection_script' ],
        [ 'embed_head', 'print_emoji_detection_script' ],

        // Styles. Current WordPress enqueues them through
        // wp_enqueue_emoji_styles(); print_emoji_styles() is still bound but
        // core unhooks it from inside wp_enqueue_emoji_styles(), so removing
        // only the old callback — which is what the previous release did —
        // leaves the emoji stylesheet loading on every page.
        [ 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' ],
        [ 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' ],
        [ 'enqueue_embed_scripts', 'wp_enqueue_emoji_styles' ],
        [ 'wp_print_styles', 'print_emoji_styles' ],
        [ 'admin_print_styles', 'print_emoji_styles' ],

        // Feeds and mail.
        [ 'the_content_feed', 'wp_staticize_emoji' ],
        [ 'comment_text_rss', 'wp_staticize_emoji' ],
        [ 'wp_mail', 'wp_staticize_emoji_for_email' ],
    ];

    public function register(): void {
        foreach ( self::REMOVALS as [ $hook, $callback ] ) {
            HookRemover::remove( $hook, $callback );
        }

        add_filter( 'tiny_mce_plugins', [ $this, 'remove_tinymce_plugin' ] );
        add_filter( 'wp_resource_hints', [ $this, 'remove_dns_prefetch' ], 10, 2 );
    }

    /**
     * Drop wpemoji from TinyMCE so the editor stops loading the script too.
     *
     * @param mixed $plugins
     * @return array<int, string>
     */
    public function remove_tinymce_plugin( $plugins ): array {
        if ( ! is_array( $plugins ) ) {
            return [];
        }

        return array_values(
            array_filter( $plugins, static fn( $p ): bool => 'wpemoji' !== $p )
        );
    }

    /**
     * Strip the s.w.org DNS-prefetch hint the emoji feature adds.
     *
     * @param mixed  $urls
     * @param string $relation_type
     * @return array<int, mixed>
     */
    public function remove_dns_prefetch( $urls, string $relation_type ): array {
        if ( ! is_array( $urls ) || 'dns-prefetch' !== $relation_type ) {
            return is_array( $urls ) ? $urls : [];
        }

        return array_values(
            array_filter(
                $urls,
                static function ( $url ): bool {
                    // The hint arrives as a bare string or as an array with an
                    // href key, depending on which core path added it.
                    $href = is_array( $url ) ? ( $url['href'] ?? '' ) : $url;

                    // phpcs:ignore PluginCheck.CodeAnalysis.Offloading.OffloadedContent
                    return ! is_string( $href ) || ! str_contains( $href, 's.w.org' );
                }
            )
        );
    }
}
