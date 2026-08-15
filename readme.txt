=== BlueFairy Performance Kit Lite ===
Contributors: bluefairydevelopment
Tags: performance, lazy loading, speed, emoji, optimization
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 8.1
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Essential WordPress performance optimisations — native lazy loading, emoji removal, embed cleanup, and heartbeat control. Zero configuration required.

== Description ==

BlueFairy Performance Kit Lite applies four proven, zero-configuration optimisations the moment it is activated:

**Native lazy loading**
Enables WordPress core's lazy-loading attribute for images and also adds `loading="lazy"` to any `<iframe>` tags in post content that do not already carry the attribute. Reduces initial page weight without any JavaScript.

**Emoji removal**
Removes the WordPress emoji detection script, inline emoji styles, and the `s.w.org` DNS-prefetch hint from every page — including the TinyMCE editor. If you are not using emoji in your content these assets are pure overhead.

**Embed script removal**
Dequeues the `wp-embed` JavaScript bundle and removes the oEmbed discovery `<link>` tags from `<head>`. If your site does not embed WordPress posts from other sites, this script is unused weight loaded on every page.

**Heartbeat control**
Removes the WordPress Heartbeat API script from the frontend. The Heartbeat API is still active in wp-admin and the post/page editor where it is needed for autosave and post-locking — only the superfluous frontend polling is stopped.

= Pro Version =

[BlueFairy Performance Kit Pro](https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit/) adds:

* LQIP blur-up image placeholders (no Intersection Observer JavaScript required)
* Critical CSS extraction and inline delivery
* Preconnect and LCP preload resource hints
* Local Google Fonts subsetting and self-hosting

== Installation ==

1. Upload the `bluefairy-performance-kit-lite` folder to the `/wp-content/plugins/` directory, or install directly via the WordPress Plugins screen.
2. Activate the plugin through the **Plugins** screen in WordPress.
3. That's it — all four optimisations are active immediately with no configuration needed.

== Frequently Asked Questions ==

= Does this slow down my site? =

No. Every optimisation is implemented via lightweight WordPress hooks. There are no database reads or writes, no external HTTP requests, and no JavaScript or CSS assets added to the frontend.

= Does it conflict with caching plugins? =

No. BlueFairy Performance Kit Lite only removes or filters existing WordPress output. It does not alter cached pages or interfere with how caching plugins store or serve responses.

= Does it conflict with WooCommerce or other plugins? =

Generally no. The embed dequeue fires late (at `wp_footer` priority 1) to give other plugins time to enqueue first. The heartbeat is only removed from the frontend, so WooCommerce admin features are unaffected.

= Can I disable individual features? =

This lite version is always-on with no settings page, keeping it as lightweight as possible. If you need per-feature control, upgrade to the [Pro version](https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit/).

= What does the Pro version add? =

[BlueFairy Performance Kit Pro](https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit/) adds LQIP blur-up image placeholders, critical CSS extraction with inline delivery, preconnect and LCP preload resource hints, and local Google Fonts subsetting and self-hosting.

== Changelog ==

= 1.0.0 — 2026-08-15 =
* Initial release.
* Native lazy loading enabled for images and iframes.
* Emoji detection script, styles, and DNS-prefetch hint removed.
* oEmbed discovery links and wp-embed script removed on frontend.
* Heartbeat script removed on frontend only.
* "Upgrade to Pro" link added to Plugins screen row.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
