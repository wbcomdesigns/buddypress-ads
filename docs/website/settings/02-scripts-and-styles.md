# Scripts and Styles

Go to **WB Plugins > BuddyPress Ads > Scripts / Styles**. Use these fields for code that should load on every page, such as ad-network verification tags or shared ad CSS.

| Field | Where the code goes |
|---|---|
| Header | Inside the page head (wp_head). |
| Body | Right after the opening body tag (wp_body_open). Your theme must call wp_body_open, which current themes do. |
| Footer | At the end of the page (wp_footer). |

Click **Save Settings**. A **Settings saved** notice confirms it. The code loads on every page, BuddyPress pages included.

Script, style and meta tags are kept for users who can post unfiltered HTML. For other users they are removed on save.
