# Plugin: Wbcom Designs - BuddyPress Ads

> **READ FIRST:** [`audit/manifest.json`](audit/manifest.json) is the canonical inventory — 0 REST endpoints, 4 AJAX handlers, 1 CPT (`wb-ads`), 2 shortcodes, 6 admin pages, 0 tables, 0 cron, 0 WP-CLI. Use it before grepping. See also [`audit/FEATURE_AUDIT.md`](audit/FEATURE_AUDIT.md), [`audit/CODE_FLOWS.md`](audit/CODE_FLOWS.md), and the QA baseline [`audit/wppqa-baseline-2026-06-05/SUMMARY.md`](audit/wppqa-baseline-2026-06-05/SUMMARY.md). Refresh via `/wp-plugin-onboard --refresh` after non-trivial changes.

> **Development standards:** Follow the [`/wp-plugin-development`](https://example/skills/wp-plugin-development) skill for ALL code work here — the 16 critical admin rules, Part 6 Admin UI (card-based pattern), the 3-layer design tokens, escaping rules, and dev hygiene (no `alert()`/`confirm()`, no inline SVG where a Lucide icon belongs, no em-dash in i18n strings, 3-breakpoint + 40px tap-target discipline). QA gate via the `wp-plugin-qa` MCP (`wppqa_check_plugin_dev_rules`).

## Quick reference
- **Main file**: `buddypress-ads-rotator.php`
- **Version**: `1.6.0`  ·  **Branch**: `1.6.0`  ·  **Constant**: `BUDDYPRESS_ADS_ROTATOR_VERSION`
- **Namespace**: none — global classes prefixed `Buddypress_Ads_Rotator_`
- **Text domain**: `buddypress-ads-rotator`  ·  **readme**: `readme.txt`
- **Extends**: none (standalone free plugin; BuddyPress optional since 1.6.0)
- **Architecture**: WordPress Plugin Boilerplate (loader-orchestrated)

## Key entry points
- Bootstrap → `buddypress-ads-rotator.php` → `run_buddypress_ads_rotator()`
- Core / hook wiring: `includes/class-buddypress-ads-rotator.php` (`define_admin_hooks()` / `define_public_hooks()`)
- Hook registry: `includes/class-buddypress-ads-rotator-loader.php` (add hooks HERE via `$this->loader->add_*`, not scattered)
- Admin: `admin/class-buddypress-ads-rotator-admin.php` (CPT, metaboxes, settings tabs, AJAX, list columns)
- Public: `public/class-buddypress-ads-rotator-public.php` (content/header/footer/shortcode rendering)
- BuddyPress: `public/class-buddypress-ads-rotator-bp.php` (loaded only if `class_exists('BuddyPress')`)

## Admin UI wrapper classification: **NEW (card-panel shell)**
- **Controller**: `includes/admin/class-buddypress-ads-rotator-settings-admin.php` — class `Buddypress_Ads_Rotator_Settings_Admin` (card-panel pattern, `get_tabs()` registry + `view_map`).
- Renders `includes/admin/views/layout.php` (page header + sidebar nav + body slot; never name a file `shell.php`: host WAFs block zips containing it). Tabs: `overview`, `scripts`, `welcome`, `faq`, `discover`. Views in `includes/admin/views/`.
- Shared WB Plugins hub under `wbcomplugins` (top-level, pos 59). Settings submenu slug `buddypress-ads-rotator-settings`. `takeover_hub_landing()` @999 reclaims the hub landing if a legacy wbcom-wrapper plugin registered `wbcomplugins` first.
- The legacy `admin/wbcom/` wrapper + `Wbcom_Admin_Settings` class + `[wbcom_admin_setting_header]` shortcode + `wbcom_addons_cards` AJAX were removed in the 1.6.0 migration. The `wb-ads` CPT, metaboxes, list columns, save handler and metabox AJAX remain in the retained legacy `Buddypress_Ads_Rotator_Admin` service class.

## Important patterns
- All ad config lives in ONE post-meta array `wb_ads_rotator_values` on the `wb-ads` CPT; on/off in `wb_ads_enable`.
- Frontend rendering has one entry point: `Public::wb_ads_rotator_render_ad( $ad_id )` (returns markup, '' when hidden by To Whom). Content, footer, shortcode and the BP class all call it. No public JS.
- Activity hooks register only when `bp_is_active( 'activity' )`; before/after-entry ads are wrapped in `li.wb-ads-activity-item`; comment-position ads in `div.wb-ads-activity-comments-slot`. The `/wp/v2/wb-ads` REST routes are limited to users who can edit ads. Stream ad ids are `wbcomads-<ad>-<activity>`.
- Site Header/Footer and the global Scripts tab (header/body/footer) are sitewide, BP pages included; before/after content skips BP pages.
- Ad code with script/style tags is kept only when the last editor has `unfiltered_html` (`wb_ads_rotator_ad_code_allowed()`); tag matching uses the one `WB_ADS_ROTATOR_CODE_TAGS` regex in `includes/wb-ads-rotator-functions.php`.
- Ad code (scripts/styles/HTML) is rendered **raw/unescaped by design** (with `phpcs:ignore`) — input is gated to admin; treat ad content as trusted-author input.
- POT: `wp i18n make-pot . languages/buddypress-ads-rotator.pot`.

## CSS selectors (for testing/dev)
- Frontend ad container: `.wb-bannerads-rotator` (+ `.plain-text-and-code-ads` / `.rich-contentwbads` / `.wbcom-imageads`; `.wbbpads-nouveau` under BP Nouveau).
- Admin metabox: `.wb_ads_rotator-panel`, `.wb_ads_rotator-wrapper`, type toggles `.plain-text-and-code` / `.rich-content` / `.image-ad`.
- Enable toggle: `.wb_ads_enable`. Shortcode copy: `.ads-copy` / `.shortcode-copy`.

## Build and CI
- `bin/local-ci.sh`: PHP lint, version triangulation, changelog entry, no em dashes, PHPStan (needs `composer install`).
- `bin/build-release.sh`: runs local CI, then builds `release-zip/buddypress-ads-<version>.zip` from `git archive HEAD` minus `.distignore`, with a dev-file leak check. Commit before building.
- User docs: `docs/website/` (lint with `node ~/.claude/skills/wbcom-documentation/bin/lint-plugin-docs.mjs .`).
