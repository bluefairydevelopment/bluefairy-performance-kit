<?php
/**
 * Removes oEmbed discovery and the wp-embed script from the front end.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit\Features;

use BlueFairy\PerformanceKit\HookRemover;

final class EmbedRemoval {

    public function register(): void {
        // Bound by core at priority 4, not the 10 that remove_action() assumes.
        HookRemover::remove( 'wp_head', 'wp_oembed_add_discovery_links' );
        HookRemover::remove( 'wp_head', 'wp_oembed_add_host_js' );

        // Late, so anything that enqueues wp-embed during the page has already
        // done so by the time we dequeue it.
        add_action( 'wp_footer', [ $this, 'dequeue_embed_script' ], 1 );
    }

    public function dequeue_embed_script(): void {
        wp_dequeue_script( 'wp-embed' );
    }
}
