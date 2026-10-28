# Changelog

All notable changes to BlueFairy Performance Kit are documented here.

The format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).
Versioning: major = architecture change, minor = new feature, patch = bug or security fix.

---

## [1.1.1] — 2026-10-27

### Fixed
- The plugin package no longer includes developer and internal files that were never meant to ship.

1.1.0] — 2026-08-31

Rebuilt as the free base plugin of a free/Pro pair. BlueFairy Performance Kit
Pro is now an add-on that extends these classes rather than a separate plugin
that shares none of their code.

### Added
- Settings page at **Settings → Performance** with a switch for each optimisation, stored in one `bfpk_features` option.
- Conflict detection for WP Rocket, LiteSpeed Cache, W3 Total Cache, Autoptimize, WP Super Cache and SiteGround Optimizer, shown on the plugin's own screen.
- Extension points for the add-on: the `bfpk_loaded` action, and the `bfpk_feature_labels`, `bfpk_feature_descriptions`, `bfpk_register_settings` and `bfpk_settings_page_footer` hooks.
- A "Get Performance Kit Pro" link in the plugins row and a callout beneath the settings form, both hidden while Pro is active.

### Fixed
- **oEmbed discovery links were never removed.** WordPress binds `wp_oembed_add_discovery_links` at `wp_head` priority 4; the plugin called `remove_action()` without a priority, which defaults to 10, so nothing was removed. The links had been printed on every singular page since 1.0.0 while the readme said they were gone. Verified on a test rig: 2 links with the feature off, 0 with it on.
- **The emoji stylesheet was still loading.** Current WordPress enqueues emoji styles through `wp_enqueue_emoji_styles()` on `wp_enqueue_scripts` and unhooks the legacy `print_emoji_styles()` itself, so removing only the legacy callback had no effect. Verified: 4 emoji references with the feature off, 0 with it on.
- Both of the above share one cause — `remove_action()` is silent when the priority does not match — so removals now go through `HookRemover`, which asks `has_action()` for the real priority instead of assuming one.
- Conflict detection ran once at activation and cached its answer in an option, so activating a caching plugin later never warned and deactivating one never cleared the warning. It is evaluated per request now.

### Changed
- The plugin's slug, prefix and namespace. This repository was `bluefairy-performance-kit-lite`; it takes the base name, `BFPK_` and `BlueFairy\PerformanceKit`.
- "Upgrade to Pro" is now "Get Performance Kit Pro". Pro runs alongside this plugin, and "upgrade" reads as a replacement — which would lead a customer to deactivate this plugin and lose everything it does.
- Uninstall deletes its three options by name through `delete_option()`.

---

## [1.0.0] — 2026-08-15

### Added
- Native lazy loading, with `loading="lazy"` applied to iframes in post content.
- Emoji script, style, feed and mail removal.
- oEmbed discovery and `wp-embed` script removal.
- Front-end heartbeat removal, leaving wp-admin and the editor untouched.
