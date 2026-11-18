# Code Flows — Wbcom Designs - BuddyPress Ads (1.6.0)

> Bootstrap: `buddypress-ads-rotator.php` → `new Buddypress_Ads_Rotator()` → constructor loads deps, sets locale, registers admin + public hooks via `Buddypress_Ads_Rotator_Loader` → `->run()` calls `add_action/add_filter/add_shortcode`.

## Key files
| Layer | File |
|---|---|
| Bootstrap | `buddypress-ads-rotator.php` |
| Core / DI | `includes/class-buddypress-ads-rotator.php` |
| Hook registry | `includes/class-buddypress-ads-rotator-loader.php` |
| i18n | `includes/class-buddypress-ads-rotator-i18n.php` |
| Admin | `admin/class-buddypress-ads-rotator-admin.php` |
| Public | `public/class-buddypress-ads-rotator-public.php` |
| BuddyPress | `public/class-buddypress-ads-rotator-bp.php` (loaded only if BP active) |
| OLD admin wrapper | `admin/wbcom/wbcom-admin-settings.php` |

## Flow 1 — Create / edit an ad (admin)
```
wp-admin → All Ads / Add New (post_type=wb-ads)
  init → Admin::wb_ads_rotator_add_cpt()                  registers wb-ads CPT
  add_meta_boxes → Admin::wb_ads_rotator_add_metaboxes()  6 metaboxes
  [render] each metabox callback reads wb_ads_rotator_values meta, fires
           wb_ads_rotator_options_before / _after around its panel
  [save]  save_post → Admin::wb_ads_rotator_save_post_meta()
           ├─ verify nonce wb_ads_rotator_render_add_meta_box
           ├─ bail on autosave
           ├─ current_user_can('edit_posts')   ← only cap check in admin write path
           └─ update_post_meta(wb_ads_rotator_values), fire wb_ads_rotator_admin_save_options
  transition_post_status (publish) → set wb_ads_enable = 'enable'
```

## Flow 2 — Toggle enable/disable (admin AJAX)
```
All Ads list → .wb_ads_enable checkbox click
  admin.js → $.post(ajaxurl, {action:wb_ads_rotator_enable, visible, datanotice, nonce:ajax.nonce})
  wp_ajax_wb_ads_rotator_enable → Admin::wb_ads_rotator_ajax_enable_callback()
    ├─ verify nonce ajax-nonce
    ├─ (NO current_user_can)            ← FINDING: any logged-in user can toggle
    └─ update_post_meta(wb_ads_enable, enable|disable)
```

## Flow 3 — Image ad media (admin AJAX)
```
Choose Image → wp.media uploader (admin.js)        sets wb_ads_image_id hidden field
Remove Image → $.post wb_ads_rotator_remove_image  → returns placeholder URL (nonce only)
(wb_ads_rotator_image AJAX returns <img> markup by id, nonce only)
```

## Flow 4 — Render ad in WordPress content (frontend)
```
the_content → Public::wb_ads_display_in_content($content)
  ├─ bail if admin / not singular / is BuddyPress page
  ├─ get_posts(post_type=wb-ads, meta_query LIKE wp_before_content OR wp_after_content)   ← unbounded
  └─ foreach ad: get_post_meta(wb_ads_enable) + get_post_meta(values)  ← N+1
       switch ads_type → wb_ads_rotator_display_{plain_text_and_code|rich_content|image}_ads()
         ├─ visitor (logged in/out) + device (wp_is_mobile) targeting
         ├─ apply_filters wb_ads_rotator_plain_text_code_ads_content / _rich_content_ads_content
         └─ echo container div (inline margin/align styles) + ad markup (raw, phpcs:ignore)
  return before_content . $content . after_content

wp_head  → Public::wb_ads_display_header()   outputs wp_header-position ads' <script>/<style> + global header option
wp_footer → Public::wb_ads_display_footer()  outputs wp_footer-position ads + global footer option
```

## Flow 5 — Shortcode
```
[ads-shortcode ads_id=N] → Public::wb_ads_rotator_shortcode_ads()
  WP_Query(p=N, post_type=wb-ads) → switch ads_type → display_*_ads()
```

## Flow 6 — BuddyPress activity stream (only when BP active)
```
bp_before_activity_entry / bp_activity_entry_content / bp_after_activity_entry
bp_before_activity_entry_comments / bp_activity_entry_comments / bp_after_activity_entry_comments
   → BP::wb_ads_display_*  → BP::wb_ads_rotator_display_ads($position)
       get_posts(wb-ads, meta_query LIKE $position)            ← unbounded
       foreach: match activity_type (sitewide/group/member) + activity_position
         display_ad_at_position() uses did_action() counter vs enject_after (+ repeat)
           → render_ad() → public_class->display_*_ads()
bp_before_directory_activity_content / bp_after_member_activity_post_form /
bp_after_group_activity_post_form → BP::wb_ads_rotator_check_ads()  (Nouveau only)
wp_head/wp_footer/the_content (BP variants) → BP script/style output from scripts option
```

## AJAX chain summary
| JS trigger (admin.js) | action | nonce | PHP handler | writes? |
|---|---|---|---|---|
| `.wb_ads_enable` click | wb_ads_rotator_enable | ajax.nonce | ajax_enable_callback | yes (meta) |
| `#wb_ads_rotator_remove_image` | wb_ads_rotator_remove_image | ajax.nonce | wb_ads_remove_uploaded_image | no |
| (image fetch) | wb_ads_rotator_image | $_GET nonce | wb_ads_rotator_image | no |

## Permissions / roles
- Admin menus + settings: `manage_options`.
- Ad CPT screens: gated by `capability_type=page` (edit_pages family) → editors+ by default.
- Save meta: explicit `edit_posts`.
- AJAX: nonce only, **no capability** (3 admin + 1 wrapper).

## Required for ads to display
- An ad must be published and `wb_ads_enable != 'disable'`.
- A WordPress position (`wp_position`) or BP activity_type+activity_position must be set, OR the ad placed via `[ads-shortcode]`.
- BuddyPress positions require BuddyPress active.
