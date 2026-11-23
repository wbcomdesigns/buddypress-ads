=== Wbcom Designs - BuddyPress Ads ===
Contributors: vapvarun, wbcomdesigns
Tags: buddypress, ads, advertising, banner, activity
Requires at least: 6.9
Tested up to: 7.1
Requires PHP: 8.1
Stable tag: 1.6.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display ads in posts, pages, the site header and footer, and the BuddyPress activity stream. Works with or without BuddyPress; the activity stream placement needs BuddyPress 12.0 or higher.

== Description ==

**BuddyPress Ads** places your own banners, rich content and ad network code on a WordPress site. Each ad is a post in its own WB Ads menu, with an on/off switch in the list, so you can prepare ads ahead of time and turn them on when a campaign starts.

BuddyPress is optional. Without it you get the post, header, footer and shortcode placements. With it you also get six positions inside the activity stream.

**Starting a new site?** Look at [WB Ad Manager](https://wordpress.org/plugins/wb-ads-rotator-with-split-test/), our newer free ad plugin, first. It adds ad rotation, A/B split testing, scheduling and 16+ placements, with BuddyPress, bbPress and Jetonomy built in. BuddyPress Ads keeps receiving fixes for the sites that already use it.

= What you get =

**Three ad types**
* Plain Text and Code - paste AdSense, Amazon or any other ad network code, HTML or shortcodes.
* Rich Content - build the ad in the WordPress editor with images, formatting and shortcodes.
* Image Ad - upload an image, set its size and the link it opens.

**Placements**
* Before or after post and page content.
* Site header (wp_head) and site footer (wp_footer).
* Anywhere with the shortcode `[ads-shortcode ads_id="123"]`. Each ad shows its own shortcode in the editor.
* BuddyPress activity stream: before or after an activity entry, inside the entry content, or before, inside or after the comments.
* Choose the sitewide, group or member activity stream, show the ad after a set number of activities, and optionally repeat it.

**Targeting and layout**
* Show an ad to logged-in members, logged-out visitors, or both.
* Show it on desktop, mobile, or both.
* Alignment, margins, a clearfix option, and text and background colors.

**Global scripts and styles**
* Add your own markup to the site header, the start of the body and the footer from the Scripts / Styles tab.

= Documentation =

* **[Plugin page](https://wbcomdesigns.com/downloads/buddypress-ads/)** - Overview and support options

= Links =

* [Plugin Homepage](https://wbcomdesigns.com/downloads/buddypress-ads/)
* [Support](https://wbcomdesigns.com/support/)
* [Request Features](https://wbcomdesigns.com/contact/)

= What's New in 1.6.0 =

BuddyPress is now optional, the settings screen is rebuilt, and activity stream ads appear reliably again. Ad settings are sanitized on save, and ad code keeps script tags only for users allowed to post unfiltered HTML. See the changelog for the full list.

== More from Wbcom Designs ==

* **[WB Ad Manager](https://wbadmanager.com/features/)** - Our newer free ad plugin with rotation, A/B split testing, scheduling and 16+ placements, including BuddyPress, bbPress and Jetonomy.
* **[BuddyX](https://buddyxtheme.com/features/)** - A free community theme on WordPress.org for BuddyPress sites.
* **[Reign](https://reigntheme.com/best-wordpress-community-theme/)** - A community theme for BuddyPress and BuddyBoss sites.
* **[BuddyNext](https://buddynext.com/features/)** - Our standalone community plugin with a feed, spaces, profiles, messaging and moderation, for new sites that do not need BuddyPress.

== Installation ==

1. In your WordPress admin go to Plugins > Add New, search for "Wbcom Designs - BuddyPress Ads", then click Install Now and Activate.
2. Or upload the plugin zip from Plugins > Add New > Upload Plugin and activate it.
3. Go to WB Ads > Add New WB Ads, pick an ad type, add your content, choose where it shows, and publish.
4. Global header, body and footer markup lives under WB Plugins > BuddyPress Ads.

== Frequently Asked Questions ==

= Do I need BuddyPress? =

No. Ads before and after post content, in the site header and footer, and through the shortcode work on any WordPress site. The activity stream positions appear when BuddyPress is active.

= Should I use BuddyPress Ads or WB Ad Manager? =

For a new site, use [WB Ad Manager](https://wordpress.org/plugins/wb-ads-rotator-with-split-test/). It does everything this plugin does plus rotation, split testing and scheduling. Ads you made here do not move across automatically, so recreate them there. The two plugins use separate ad types, so you can run both while you switch.

= Does it work with BuddyNext? =

No. The activity positions hook into the BuddyPress activity stream, and BuddyNext has its own feed. On a BuddyNext site you can still use the post, header, footer and shortcode placements.

= Why were the script tags removed from my ad? =

Ad code keeps script tags only for users with the unfiltered_html capability. On a single site, administrators and editors have it. On a multisite network only super admins do, so everyone else gets the code filtered for safety. Ask a user with that capability to save ads that need scripts.

= Which themes does it work with? =

Activity ads use standard BuddyPress hooks, so they work with themes that support BuddyPress, including BuddyX and Reign. Plain text and rich content ads also work with the BuddyPress Nouveau template pack, where the activity stream loads over AJAX.

= Can I control who can open the settings screen? =

Yes. It needs manage_options by default. Developers can change that with the `buddypress_ads_rotator_admin_capability` filter.

== Screenshots ==

1. Admin overview - published and enabled ad counts, global header/body/footer markup, and quick actions.
2. Discover more free Wbcom Designs community plugins from the admin screen.

== Changelog ==
= 1.6.0 - November 2026 =

Standalone WordPress support plus a rebuilt admin. BuddyPress is now optional.

* New      - Added a Discover tab to the settings screen with more free tools from Wbcom Designs.
* Improve  - Refactored the plugin to run on standalone WordPress; BuddyPress is now optional, not required.
* Improve  - Rebuilt the settings screen on the new Wbcom card-panel admin (Overview, Scripts / Styles, Welcome, FAQ) with a sidebar layout and shared WB Plugins hub.
* Improve  - Raised admin buttons to a 40px tap target and moved the warning-text color onto a design token.
* Fix      - Activity stream ads now appear reliably. The BuddyPress integration was skipped whenever this plugin loaded before BuddyPress, which left every activity ad position dead.
* Fix      - The Scripts / Styles header, body and footer fields no longer strip the style, script and meta tags you paste into them. Saving a style block now keeps it, and the CSS is applied instead of being printed as text.
* Fix      - Header and footer code is no longer printed twice on BuddyPress pages.
* Fix      - An ad with no content can no longer be published. It is kept as a draft with a notice, instead of publishing an ad that renders as an empty space.
* Fix      - Ads created with wp_insert_post() or WP-CLI, with their content in meta_input, are no longer forced to draft.
* Fix      - Two activity ads at different positions now both show. An ad set to show before comments used to hide every activity ad listed after it.
* Fix      - Clearing the activity ad Position field no longer breaks the activity stream. An empty value now falls back to every 3 entries.
* Fix      - Turning off one ad no longer removes the site-wide header scripts from BuddyPress pages.
* Fix      - Sites with more than five ads now show all of them. Older ads were silently skipped once a sixth ad was added.
* Fix      - Ad code now runs exactly once per ad slot on activity pages. It used to run extra times from a hidden copy and the page head, which double-counted ad impressions.
* Fix      - Ad code in activity entries loaded with Load More now runs.
* Fix      - Before and after entry ads in the activity stream now sit in their own list item, so they take the theme's card style in light and dark mode and keep the list valid.
* Fix      - Ads repeated in the activity stream now have unique HTML ids.
* Fix      - The global Body code now loads on every page right after the opening body tag. It used to load only on BuddyPress pages.
* Fix      - Site header and site footer ads now show on BuddyPress pages too, and site header ads follow the To Whom rules.
* Fix      - Site footer ads are no longer hidden behind a theme's fixed side panel.
* Fix      - Shortcodes inside rich content ads no longer render out of order, twice, or not at all.
* Fix      - Image ads keep the width and height you set, scale down on small screens, use the ad title as alt text, and open their link in a new tab marked as sponsored.
* Fix      - Choosing or removing the picture on an image ad no longer replaces or clears its click-through URL.
* Fix      - Rich content ads keep their paragraphs, line breaks, tables, horizontal lines and new-tab links, as shown in the editor.
* Fix      - Updating an ad you switched off no longer switches it back on.
* Fix      - The ad shortcode only shows published ads. A missing or wrong ads_id shows nothing to visitors and a short hint to editors.
* Fix      - The Device option now shows the saved choice when it is set to both.
* Fix      - The ad editor no longer overflows on small screens, and its options have clear labels and help text.
* Fix      - The plain text ad content filter now also runs when shortcodes are allowed.
* Fix      - Choosing a new image for an image ad keeps the width and height you typed. Empty size fields take the image's own size.
* Fix      - Removing an ad image no longer clears the size fields.
* Fix      - The Overview tab now says when the BuddyPress Activity component is off instead of listing activity positions as available.
* Fix      - The FAQ no longer mentions container ID and CSS class fields that do not exist, and the Welcome tab describes the documentation link accurately.
* Fix      - The settings screen no longer scrolls sideways on phones.
* Fix      - Rich content ads with the clearfix option printed the line break twice.
* Fix      - Empty margin or font size fields no longer write broken CSS such as margin-top:px into the ad.
* Fix      - The settings screen no longer hides WordPress core and other plugins' admin notices.
* Fix      - The Documentation links in the settings screen opened a 404. They now open the plugin page.
* Fix      - The three ad type options no longer share one HTML id.
* Fix      - Every admin string, including the new settings screen, is now in the translation template.
* Fix      - Before, inside and after entry comments ads now sit in their own spaced block, so a theme's action bar no longer covers the top of the ad.
* Fix      - Before and after content ads now show only in the page's main content, not in related posts or query blocks on the same page, and only the ads placed there are rendered.
* Fix      - Image ad alt text uses the ad title as typed, without converted characters such as 728&#215;90.
* Fix      - The ad shortcode hints for editors now use the BuddyPress notice style.
* Security - Added capability checks to the ad image, image-removal and enable/disable AJAX handlers. They follow the ad's own edit permission, so Editors can manage ads.
* Security - Ad settings are sanitized field by field on save. Ad code keeps script tags only for users with the unfiltered_html capability.
* Security - Saving an ad or switching it on or off now checks that the user can edit that ad.
* Security - The image preview in the ad editor no longer runs markup from an attachment's title or alt text.
* Security - The ads REST API routes are now limited to users who can edit ads, so visitors can no longer list ad titles.
* Dev      - Added the buddypress_ads_rotator_admin_capability filter for the settings screen capability.
* Dev      - Raised the minimum PHP version to 8.1.
* Dev      - Added bin/build-release.sh and .distignore, which build the release zip from git without development files, and bin/local-ci.sh.
* Dev      - Activity stream hooks register only when the BuddyPress Activity component is on, and group activity options only when Groups is on.
* Dev      - Removed the public JavaScript file and the hidden ad holder it copied from.
* Dev      - Added user documentation under docs/website.
* Dev      - Removed the legacy admin/wbcom/ wrapper and unused settings partials.
* Dev      - Fixed all WordPress Coding Standards violations and mixed boolean operator precedence issues across all PHP files.

= 1.5.5 =
* Fix: (#66,#68,#69)Fixed ads position issue on activity feed
* Fix: Compatibility check with WordPress 6.5.0

= 1.5.4 =
* Fix: BP v12 Fixes
* Fix: Updated class name and remove dynamic class name

= 1.5.3 =
* Fix: PHPCS fixes

= 1.5.2 =
* Fix: PHPCS fixes
* Fix: (#60) Not processing shortcodes
* Fix: Plugin redirect issue when multiple plugins activate at the same time
* Fix: (#59) Hide admin notice error
* Fix: (#51) First position is wrong

= 1.5.1 =
* Fix- Fixed BuddyBoss admin notice issue
* Fix- (#54) Fixed can not create an image ad
* Fix- Fixed copy shortcode option UI managed 

= 1.5.0 =
* Fix- Hide admin notices
* Fix- Fixed string mistakes
* Fix- Updated admin UI code

= 1.4.0 =
* Fix- (#48) Fixed HTML is not rendering
* Fix- (#49) changed admin ads type description
* Fix- (#43) Added Remove image option for image ads
* Fix- (#44) Fixed image ads URL setting issue
* Fix- (#42) Fixed warning issue displayed on the front-end
* Fix- (#47) Fixed warning issue displaying on single group for logout user

= 1.3.2 =
* Fix- (#41)Fixed Google Ad Issue with other ads type
* Fix- (#34)Fixed when not selected option in activity type, ads not showing
* Fix- (#30)Fixed when select image in Rich Content, it is not showing frontend

= 1.3.1 =
* Fix- Removed install plugin button from wrapper
* Fix- Fixed warning issue

= 1.3.0 =
* Fix- (#28)Fixed Ads are showing repeatedly
* Fix- (#29)changed shortcode text
* Fix- (#27)Fixed google ads script issue with rich content ads
* Fix- Update Code for CSS and JS Loading
* Fix- Load Admin CSS and JS on Plugins Settings Pages
* Fix- Changed Plugin documentation link

= 1.2.0 =
* Fix- js and iframe support

= 1.1.0 =
* Fix- PHPCS Fixes

= 1.0.0 =
* first version.
