# Troubleshooting

## The ad does not show

1. Check the ad is published and switched on in the **WB Ads** list.
2. Check **To Whom**: a logged-out-only ad is hidden while you are logged in, and a desktop-only ad is hidden on phones.
3. Check the position: before and after content positions show only on single posts and pages, not on BuddyPress activity pages.
4. For activity ads, check an activity type is selected and the stream has at least N entries.

## Activity stream options are missing

The BuddyPress options appear only when BuddyPress is active with the Activity Streams component on. Turn it on in **Settings > BuddyPress > Components**. Group activity also needs the Groups component.

## Device rules show the wrong ad

A full-page cache can serve the desktop copy to phones. Configure the cache to keep separate mobile pages, or set the ad's device to both.

## The shortcode shows nothing

The shortcode needs the ID of a published, switched-on ad, for example `[ads-shortcode ads_id=12]`. When you are logged in as an Editor or Administrator, a hint explains what is wrong.

## Site header ad shows nothing on the page

The site header position only outputs script and style tags, because the page head cannot display content. Use a content, footer or activity position for visible ads.
