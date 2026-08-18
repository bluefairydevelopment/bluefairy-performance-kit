<?php

declare(strict_types=1);

namespace BlueFairy\PerformanceKitLite;

class Plugin
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    public static function boot(): void
    {
        if (self::$instance !== null) {
            return;
        }

        self::$instance = new self();
        self::$instance->init();
    }

    private function init(): void
    {
        $this->register_lazy_loading();
        $this->register_emoji_removal();
        $this->register_embed_removal();
        $this->register_heartbeat_control();
        $this->register_action_links();
    }

    // -------------------------------------------------------------------------
    // 1. Native lazy loading
    // -------------------------------------------------------------------------

    private function register_lazy_loading(): void
    {
        // Enable the core lazy-loading attribute for images and iframes.
        add_filter('wp_lazy_loading_enabled', '__return_true');

        // Ensure loading="lazy" is applied to iframes in post content.
        add_filter('the_content', [$this, 'add_lazy_loading_to_iframes'], 10, 1);
    }

    /**
     * Add loading="lazy" to any <iframe> tag in post content that does not
     * already carry a loading attribute.
     */
    public function add_lazy_loading_to_iframes(string $content): string
    {
        if (! str_contains($content, '<iframe')) {
            return $content;
        }

        return (string) preg_replace_callback(
            '/<iframe\b([^>]*)>/i',
            static function (array $matches): string {
                $attrs = $matches[1];

                // Skip if a loading attribute is already present.
                if (preg_match('/\bloading\s*=/i', $attrs)) {
                    return $matches[0];
                }

                return '<iframe' . $attrs . ' loading="lazy">';
            },
            $content
        );
    }

    // -------------------------------------------------------------------------
    // 2. Emoji removal
    // -------------------------------------------------------------------------

    private function register_emoji_removal(): void
    {
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

        add_filter('tiny_mce_plugins', [$this, 'remove_tinymce_emoji']);
        add_filter('wp_resource_hints', [$this, 'remove_emoji_dns_prefetch'], 10, 2);
    }

    /**
     * Remove the wpemoji plugin from TinyMCE so the editor does not load the
     * emoji detection script either.
     *
     * @param  array<string> $plugins
     * @return array<string>
     */
    public function remove_tinymce_emoji(array $plugins): array
    {
        return array_values(array_filter($plugins, static fn(string $p): bool => $p !== 'wpemoji'));
    }

    /**
     * Strip the s.w.org DNS-prefetch hint added by the emoji feature.
     *
     * @param  array<array{href:string,crossorigin?:string}> $urls
     * @param  string                                         $relation_type
     * @return array<array{href:string,crossorigin?:string}>
     */
    public function remove_emoji_dns_prefetch(array $urls, string $relation_type): array
    {
        if ($relation_type !== 'dns-prefetch') {
            return $urls;
        }

        return array_values(
            array_filter(
                $urls,
                static fn(array $url): bool => ! str_contains($url['href'] ?? '', 's.w.org') // phpcs:ignore PluginCheck.CodeAnalysis.Offloading.OffloadedContent
            )
        );
    }

    // -------------------------------------------------------------------------
    // 3. Embed script removal
    // -------------------------------------------------------------------------

    private function register_embed_removal(): void
    {
        // Remove oEmbed discovery links and the host JS from <head>.
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'wp_oembed_add_host_js');

        // Dequeue the wp-embed script on the frontend after all scripts are
        // enqueued (priority 1 on wp_footer fires before other footer scripts).
        add_action('wp_footer', [$this, 'dequeue_wp_embed'], 1);
    }

    /**
     * Dequeue the wp-embed script. Runs at wp_footer priority 1 to ensure it
     * fires after WooCommerce and other plugins have had a chance to enqueue.
     */
    public function dequeue_wp_embed(): void
    {
        wp_dequeue_script('wp-embed');
    }

    // -------------------------------------------------------------------------
    // 4. Heartbeat control (frontend only)
    // -------------------------------------------------------------------------

    private function register_heartbeat_control(): void
    {
        // Use wp_enqueue_scripts so this only fires on the frontend — that hook
        // does not run in wp-admin or the block editor context.
        add_action('wp_enqueue_scripts', [$this, 'deregister_heartbeat'], 1);
    }

    /**
     * Remove the heartbeat script from the frontend entirely.
     * The heartbeat is still available in wp-admin and the post editor.
     */
    public function deregister_heartbeat(): void
    {
        wp_deregister_script('heartbeat');
    }

    // -------------------------------------------------------------------------
    // 5. Upgrade to Pro action link
    // -------------------------------------------------------------------------

    private function register_action_links(): void
    {
        add_filter(
            'plugin_action_links_' . plugin_basename(BFPKL_FILE),
            [$this, 'add_upgrade_link']
        );
    }

    /**
     * Append an "Upgrade to Pro" link to the plugin row on the Plugins screen.
     *
     * @param  array<string> $links
     * @return array<string>
     */
    public function add_upgrade_link(array $links): array
    {
        $links[] = '<a href="'
            . esc_url('https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit/')
            . '" target="_blank" rel="noopener noreferrer" style="font-weight:600;color:#2271b1;">'
            . esc_html__('Upgrade to Pro →', 'bluefairy-performance-kit-lite')
            . '</a>';

        return $links;
    }
}
