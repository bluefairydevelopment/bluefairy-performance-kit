# BlueFairy Performance Kit

Removes the front-end weight WordPress adds by default, and lazy-loads iframes.

**Version:** 1.1.1 · **WordPress:** 6.4+ · **PHP:** 8.1+ · **Licence:** GPL-2.0-or-later

This is the free base plugin of a pair. [BlueFairy Performance Kit Pro](https://wordpress.bluefairydevelopment.com/product/bluefairy-performance-kit-pro/) is an add-on that extends the classes here; it does not replace this plugin and will not run without it.

Customer-facing feature list: `FEATURES.md`. The Pro/free comparison, which is easy to get wrong in both directions: `docs/COMPARISON.md`.

---

## Features

- **Emoji removal** — detection script, stylesheet (modern and legacy enqueue paths), feed, RSS and mail filters, TinyMCE plugin, `s.w.org` hint.
- **oEmbed removal** — discovery links and the `wp-embed` script.
- **Front-end heartbeat** — deregistered on `wp_enqueue_scripts`, so wp-admin and the editor keep theirs.
- **Iframe lazy loading** — `loading="lazy"` on iframes in post content.
- **Settings → Performance** — one toggle per feature, stored in `bfpk_features`.
- **Conflict detection** — six caching plugins, evaluated per request.

## File structure

```
bluefairy-performance-kit.php   Header, constants, autoloader, lifecycle hooks
includes/
  Plugin.php                    Boot: fires bfpk_loaded, then binds subsystems
  Installer.php                 Activation checks and defaults
  FeatureRegistry.php           Labels, descriptions, is_enabled(), sanitiser
  ConflictDetector.php          Caching-plugin detection, per request
  HookRemover.php               remove_action() at the priority core really used
  Features/
    LazyLoading.php
    EmojiRemoval.php
    EmbedRemoval.php
    HeartbeatControl.php
  Admin/
    SettingsPage.php            Settings → Performance
    ProUpsell.php               Hidden while Pro is active
uninstall.php                   Deletes its three options by name
```

## Extension points

Pro is built entirely on these. They are public API — changing one is a breaking change.

| Hook | Type | Purpose |
|---|---|---|
| `bfpk_loaded` | action | Fired by `Plugin::boot()` **before** any subsystem binds. This is where an add-on registers. Firing it afterwards would be useless: a feature label added later is not in the list when the settings page reads it. Does not fire when this plugin is inactive, so an add-on must also check the dependency on `plugins_loaded`. |
| `bfpk_feature_labels` | filter | Adds entries to the toggle list. The same array drives the checkboxes, the sanitiser and every `is_enabled()` call, so one added key is a working, persistable toggle. |
| `bfpk_feature_descriptions` | filter | The sentence under each checkbox. |
| `bfpk_register_settings` | action | Passed `$page` and `$group`. Register your own sections and fields onto the base's screen and option group. |
| `bfpk_settings_page_footer` | action | Render below the settings form. |

`FeatureRegistry::is_enabled()` treats an unknown key as enabled, so an add-on's features work the moment it activates without the stored option being rewritten first.

## Why HookRemover exists

`remove_action( $hook, $fn )` defaults to priority 10 and does nothing at all when the real priority differs. WordPress binds `wp_oembed_add_discovery_links` at priority **4**, so the obvious call removed nothing and this plugin printed those links on every singular page for its entire 1.0.0 life while the readme said it did not.

`HookRemover::remove()` asks `has_action()` for the current priority and removes at that, so a core change of priority cannot silently disable a feature again.

## Coding standards

- `declare(strict_types=1)` in every file
- Output escaped at the point of output; never in exception messages
- No output from constructors or `register()` methods — hook registration only

## Support the free plugin

This plugin is free and always will be. If it saves you time, you can [buy us a coffee on Ko-fi](https://ko-fi.com/bluefairydevelopment) ☕ — it helps keep the free plugins tested and maintained.

Found a bug or have an idea? [Open an issue](../../issues/new/choose). Security problems: see [SECURITY.md](SECURITY.md).
