# Feature Audit — Wbcom Designs - BuddyPress Ads (1.6.0)

> Derived from [`manifest.json`](manifest.json). Branch `1.6.0`, main file `buddypress-ads-rotator.php`, text domain `buddypress-ads-rotator`.
> Architecture: WordPress Plugin Boilerplate (loader-orchestrated, global classes prefixed `Buddypress_Ads_Rotator_`). No namespace, no Composer autoload.

## What the plugin does
Lets an admin create "ads" (as a private `wb-ads` CPT) and place them in WordPress positions (before/after post content, site header, site footer, via shortcode) and — when BuddyPress is active — in activity-stream positions (before/after entry, entry content, before/after comments, etc.). Each ad supports three types (plain text/code, rich content, image), visitor/device targeting, margins, alignment, and an enable/disable toggle. As of 1.6.0 BuddyPress is optional.

## 1. Admin pages & tabs

| Page | Slug | Parent | Cap | Source | Notes |
|---|---|---|---|---|---|
| WB Plugins (top menu) | `wbcomplugins` | — | manage_options | admin class:151 | Only registered if global `wbcomplugins` menu absent |
| Welcome (submenu) | `wbcomplugins` | wbcomplugins | manage_options | admin class:152 | Shares slug w/ top menu |
| BuddyPress Ads (settings) | `buddypress-ads-rotator-settings` | wbcomplugins | manage_options | admin class:155 | Main page; tabbed (Welcome / FAQ / Add Scripts/Styles) |
| Our Plugins | `wbcom-plugins-page` | wbcomplugins | manage_options | wrapper:211 | OLD wrapper |
| Our Themes | `wbcom-themes-page` | wbcomplugins | manage_options | wrapper:219 | OLD wrapper — **template file missing on disk** |
| Support | `wbcom-support-page` | wbcomplugins | manage_options | wrapper:227 | OLD wrapper |

Settings page tabs are rendered via the Settings API (`settings_fields` / `do_settings_sections`) keyed by `?tab=`. Tabs: `buddypress-ads-rotator-welcome`, `buddypress-ads-rotator-faq`, `buddypress-ads-rotator-scripts`, plus direct links to All Ads / Add New Ads (CPT screens).

## 2. AJAX handlers

| Action | Handler | Nonce | Cap | Purpose |
|---|---|---|---|---|
| `wb_ads_rotator_image` | Admin::wb_ads_rotator_image | ajax-nonce ($_GET) | **none** | Return `<img>` markup for selected image ad |
| `wb_ads_rotator_remove_image` | Admin::wb_ads_remove_uploaded_image | ajax-nonce ($_POST) | **none** | Return placeholder image URL |
| `wb_ads_rotator_enable` | Admin::wb_ads_rotator_ajax_enable_callback | ajax-nonce ($_POST) | **none** | Toggle `wb_ads_enable` meta (**writes data**) |
| `wbcom_addons_cards` | Wbcom_Admin_Settings::wbcom_addons_cards_links | wbcom_admin_setting_nonce | **none** | Echo extension card label (OLD wrapper) |

All four are `wp_ajax_` (auth-required) and lack `current_user_can` — see CODE_FLOWS.md and the wppqa baseline. JS caller for the first three: `admin/js/buddypress-ads-rotator-admin.js`.

## 3. REST endpoints
_None._ (`wb-ads` CPT has `show_in_rest=true`, so the default `/wp/v2/wb-ads` core controller exists but is not authenticated for the public since the CPT is `public=false`.)

## 4. Settings / options

| Option | Group | Sanitize | Read where |
|---|---|---|---|
| `wb_ads_rotator_admin_welcome_options` | self | sanitize_text_field | never read (placeholder) |
| `buddypress-ads-rotator_admin_faq_option` | `..._options` (mismatch) | sanitize_text_field | never read (placeholder) |
| `buddypress-ads-rotator_admin_scripts_options` | self | wp_kses_post per header/body/footer | public + BP header/body/footer output |
| `wb_ads_image_id` | (never written) | — | admin metabox preview only (dead read) |

## 5. Content types

- **CPT `wb-ads`** — `public=false`, `show_ui=true`, `show_in_rest=true`, `capability_type=page`, supports `title` only. Registered on `init`.
- **Meta boxes** (6): WB Ads Type, Ads Parameter, Layout / Output, Display Conditions, To Whom, Shortcode (side). All saved into a single `wb_ads_rotator_values` array via `save_post`.
- **Post meta:** `wb_ads_rotator_values` (config array), `wb_ads_enable` (enable|disable).
- **Custom list columns** on the `wb-ads` table: Ads ID, Ads Type, Enable/Disable toggle, Shortcode.

## 6. Shortcodes

| Tag | Handler | Attrs |
|---|---|---|
| `ads-shortcode` | Public::wb_ads_rotator_shortcode_ads | `ads_id` |
| `wbcom_admin_setting_header` | Wbcom_Admin_Settings::wbcom_admin_setting_header_html | — (OLD wrapper) |

## 7. JS modules
- `public/js/...public.js` — localizes `bp_ads_check.is_shortcode_page`.
- `admin/js/...admin.js` — selectize init, ad-type radio show/hide, AJAX enable toggle, image remove, wp.media uploader, CodeMirror init, shortcode copy.
- `admin/js/selectize.min.js`, `admin/wbcom/assets/js/wbcom-admin-setting.js` (OLD wrapper).

## 8. CSS modules
- `public/css/...public.css`; `admin/css/...admin.css`; `admin/css/selectize.css`; `admin/wbcom/assets/css/wbcom-admin-setting.css` (OLD wrapper).

## 9. Email templates
_None._

## 10. Cron jobs
_None._

## 11. DB tables
_None._ (all data in CPT posts + post meta + a few wp_options entries).

## 12. Integrations
- **BuddyPress** (optional) — `Buddypress_Ads_Rotator_BP` only loaded/hooked when `class_exists('BuddyPress')`. Activity-stream positions + Nouveau-specific output. Detection: `class_exists('BuddyPress')`, `bp_get_theme_compat_id()`, `bp_is_*()`.
- **Wbcom shared admin wrapper** (OLD) — `admin/wbcom/wbcom-admin-settings.php` adds the shared WB Plugins menu, Our Plugins/Themes/Support pages, and the header nav shortcode.

## 13. Capabilities
| Cap | Used for | Where |
|---|---|---|
| `manage_options` | admin menu/page gating | menu registrations |
| `edit_posts` | save-meta guard | admin class:914 |

No custom capabilities registered.

## Top findings (carried from wppqa baseline + manual read)
1. **4× nonce-without-capability AJAX handlers** (1 writes data) — high.
2. **Unbounded `get_posts` + N+1 meta on every front-end request** — scale risk.
3. **Missing wrapper template** `wbcom-themes-page.php` (Our Themes page would warn/fatal).
