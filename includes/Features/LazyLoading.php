<?php
/**
 * Native lazy loading for iframes.
 *
 * @package BlueFairy\PerformanceKit
 */

declare(strict_types=1);

namespace BlueFairy\PerformanceKit\Features;

final class LazyLoading {

    public function register(): void {
        add_filter( 'wp_lazy_loading_enabled', '__return_true' );
        add_filter( 'the_content', [ $this, 'lazy_load_iframes' ] );
    }

    /**
     * Add loading="lazy" to any <iframe> that does not already carry a
     * loading attribute.
     *
     * WordPress core already handles images; iframes in post content are the
     * gap this closes.
     */
    public function lazy_load_iframes( string $content ): string {
        if ( ! str_contains( $content, '<iframe' ) ) {
            return $content;
        }

        return (string) preg_replace_callback(
            '/<iframe\b([^>]*)>/i',
            static function ( array $matches ): string {
                $attrs = $matches[1];

                if ( preg_match( '/\bloading\s*=/i', $attrs ) ) {
                    return $matches[0];
                }

                return '<iframe' . $attrs . ' loading="lazy">';
            },
            $content
        );
    }
}
