# Changelog

All notable changes to BlueFairy Performance Kit Lite are documented in this file.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).
Versioning follows [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] — 2026-08-15

### Added

- Native lazy loading: `wp_lazy_loading_enabled` filter returns `true`; `the_content` filter adds `loading="lazy"` to iframes.
- Emoji removal: removes detection script, print styles, admin scripts/styles, feed/mail staticize filters, TinyMCE `wpemoji` plugin, and `s.w.org` DNS-prefetch hint.
- Embed cleanup: removes `wp_oembed_add_discovery_links` and `wp_oembed_add_host_js` from `wp_head`; dequeues `wp-embed` script on `wp_footer` priority 1.
- Heartbeat control: deregisters `heartbeat` script on `wp_enqueue_scripts` (frontend only).
- "Upgrade to Pro" action link on the Plugins screen row.
