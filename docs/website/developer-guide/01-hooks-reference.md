# Hooks Reference

## Filters

| Filter | Arguments | Use |
|---|---|---|
| `wb_ads_rotator_plain_text_code_ads_content` | `$content` | Change a plain text and code ad's output before it prints. |
| `wb_ads_rotator_rich_content_ads_content` | `$content` | Change a rich content ad's output before shortcodes run. |
| `wb_ads_rotator_max_ads` | `$max` (default 100) | Maximum number of published ads loaded per request. |
| `buddypress_ads_rotator_admin_capability` | `$cap` (default `manage_options`) | Capability needed to open the plugin settings screen. |
| `buddypress_ads_rotator_admin_tabs` | `$tabs` | Add or remove settings screen tabs. |

## Actions

| Action | Fires |
|---|---|
| `wb_ads_rotator_options_before` | At the start of each ad editor panel. |
| `wb_ads_rotator_options_after` | At the end of each ad editor panel. |
| `wb_ads_rotator_admin_save_options` | After an ad's settings are saved. |

## CSS hooks

| Selector | Element |
|---|---|
| `.wb-bannerads-rotator` | Every ad container. |
| `.wbcomads-{ad_id}` | One specific ad, wherever it appears. |
| `.plain-text-and-code-ads`, `.rich-contentwbads`, `.wbcom-imageads` | Ad type. |
| `li.wb-ads-activity-item` | The list item that holds a before or after entry stream ad. |
| `.wb-ads-site-footer` | The strip that holds site footer ads. |

Inside activity streams, an ad's id is `wbcomads-{ad_id}-{activity_id}` so each copy is unique. Style ads by class, not id.

## Capabilities

Ads are a page-like post type. Anyone who can edit pages (Editors and Administrators) can create, edit and switch ads on or off.
