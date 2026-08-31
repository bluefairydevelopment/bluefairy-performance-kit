=== BlueFairy Performance Kit ===
Contributors: bluefairydevelopment
Tags: performance, speed, emoji, oembed, heartbeat
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Removes the front-end weight WordPress adds by default — emoji scripts, oEmbed JS, the heartbeat — and lazy-loads iframes. Nothing to configure.

== Description ==

Every WordPress site ships JavaScript and CSS that most sites never use. The emoji detection script and its stylesheet load on every page. oEmbed discovery links and `wp-embed.js` load whether or not you embed anything. The heartbeat polls admin-ajax from the front end for visitors who will never see wp-admin.

BlueFairy Performance Kit takes them out. Four optimisations, all on by default, each with a switch if you want it back.

**What it removes:**

* **Emoji** — the detection script, the emoji stylesheet, the feed and email filters, the TinyMCE plugin, and the `s.w.org` DNS-prefetch hint. WordPress changed how it enqueues emoji styles; this removes both the modern and the legacy path.
* **oEmbed** — the discovery `<link>` tags in your `<head>` and the `wp-embed` script. Embedding still works; only the discovery overhead goes.
* **The heartbeat, on the front end only.** It keeps running in wp-admin and the block editor, so autosave and post locking are untouched.

**What it adds:**

* **`loading="lazy"` on iframes** in post content. WordPress already does this for images; iframes are the gap.

**Settings → Performance** has one checkbox per feature and nothing else. Everything is on after activation, so there is nothing you must configure.

**Conflict detection.** If WP Rocket, LiteSpeed Cache, W3 Total Cache, Autoptimize, WP Super Cache or SiteGround Optimizer is active, the settings page says so. Nothing breaks if both are on — the work is simply done twice — and you can switch off whichever half you prefer.

**Add Performance Kit Pro** for critical CSS, self-hosted Google Fonts, resource hints and WooCommerce image placeholders. Pro installs alongside this plugin rather than replacing it, and your settings carry over.

== Installation ==

1. Upload the `bluefairy-performance-kit` folder to `/wp-content/plugins/`, or install the zip from **Plugins → Add New → Upload Plugin**.
2. Activate it from the **Plugins** screen.

That is the whole setup. All four optimisations are active immediately. Visit **Settings → Performance** only if you want to turn one off.

== Frequently Asked Questions ==

= Will removing emoji support break anything? =

No. Emoji characters still display — they are ordinary Unicode and your visitors' devices render them. What is removed is WordPress's detection script, which exists to swap emoji for images on browsers that have not needed the help in years.

= Does disabling the heartbeat break autosave? =

No. The heartbeat is only removed on the front end, where nothing uses it. It keeps running in wp-admin and the block editor, so autosave, post locking and session expiry warnings all work as normal.

= Will oEmbed still work? =

Yes. Pasting a YouTube or Twitter URL still produces an embed. What is removed is the discovery metadata that lets *other* sites embed *your* posts, plus the `wp-embed` script that powers those external embeds.

= Can I use this alongside WP Rocket or LiteSpeed Cache? =

Yes. There is overlap — most caching plugins offer some of these switches too — so the settings page warns you when it finds one. Turn a feature off here if the other plugin already handles it. Nothing breaks either way.

= Does it work with a page cache? =

Yes. Everything happens while the page is generated, so a cached page is cached without the removed assets.

= What does Performance Kit Pro add? =

Pro is an add-on that runs alongside this plugin — it does not replace it, and your settings carry over.

* **Critical CSS** extracted per page template and inlined, with the rest of your stylesheets deferred
* **Self-hosted Google Fonts** — the font files are downloaded to your uploads folder and the request to `fonts.googleapis.com` disappears
* **Resource hints** — `preconnect` for the third-party origins your page actually loads, and a `preload` for the likely LCP image
* **Blurred placeholders for WooCommerce product images**, swapped in as they enter the viewport

[Get Performance Kit Pro →](https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit-pro/)

= What happens to my data if I uninstall it? =

Deleting the plugin removes the three options it created. Nothing else is stored.

== Screenshots ==

1. Settings → Performance — one checkbox per optimisation, all on by default.
2. The conflict notice, shown when another optimisation plugin is active.

== Changelog ==

= 1.1.0 =
* Rebuilt as the free base plugin of a pair. BlueFairy Performance Kit Pro is now an add-on that extends this plugin instead of a separate plugin that shares none of its code.
* Added a settings page at Settings → Performance with a switch for each optimisation.
* Added conflict detection for WP Rocket, LiteSpeed Cache, W3 Total Cache, Autoptimize, WP Super Cache and SiteGround Optimizer, evaluated on every request rather than once at activation.
* Added documented extension points so the add-on can register: `bfpk_loaded`, `bfpk_feature_labels`, `bfpk_feature_descriptions`, `bfpk_register_settings` and `bfpk_settings_page_footer`.
* Fixed: the oEmbed discovery links were never removed. WordPress binds that callback at priority 4 and the plugin asked to remove it at the default priority 10, so the links have been printed on every singular page since 1.0.0 while the readme said otherwise.
* Fixed: the emoji stylesheet was still loading. Current WordPress enqueues it through `wp_enqueue_emoji_styles()` and unhooks the older callback itself, so removing only the old one had no effect on the styles.
* Removed the "Upgrade to Pro" wording. Pro runs alongside this plugin; "upgrade" reads as a replacement, and deactivating this plugin would lose everything it does.

= 1.0.0 =
* Initial release: native lazy loading, emoji removal, embed cleanup and heartbeat control.

== Upgrade Notice ==

= 1.1.0 =
Two optimisations this plugin claimed to perform were silently doing nothing — oEmbed discovery links and the emoji stylesheet are now genuinely removed. Adds a settings page so you can switch any of them off.
