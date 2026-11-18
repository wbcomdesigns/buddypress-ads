# Where Ads Appear

## WordPress positions

| Position | Where it shows |
|---|---|
| None (use shortcode only) | Only where you paste the ad shortcode. |
| Before post/page content | Above the content of single posts and pages. |
| After post/page content | Below the content of single posts and pages. |
| Site header | In the page head on every page, BuddyPress pages included. Only script and style tags are output, so use it for ad-network loader code. |
| Site footer | At the end of every page, BuddyPress pages included, in a centred strip below the theme footer. |

Before and after content positions do not show on BuddyPress activity pages. Use the activity positions there.

## BuddyPress activity streams

Shown when BuddyPress is active with the Activity Streams component on.

- **Activity type** picks the streams: sitewide activity, group activity (needs the Groups component) and member profile activity. Leave it empty to keep the ad out of streams.
- **Activity position** picks the slot: before or after an activity entry, inside the entry content, or before, inside or after the entry comments.
- **Show after entry** sets N. The ad shows at entry N. With **Repeat** on, it shows at every Nth entry, and keeps counting on entries loaded with **Load More**.

Ads placed before or after an entry sit in their own list item, so they take the theme's card style in light and dark mode and never break the stream on mobile.

## Shortcode

Every ad has a shortcode, shown in the **Shortcode** box on the ad editor and in the ads list:

```
[ads-shortcode ads_id=123]
```

The shortcode only shows published, switched-on ads. If the ID is missing or wrong, visitors see nothing. Users who can edit ads see a short hint instead.
