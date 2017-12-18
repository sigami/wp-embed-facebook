=== WP Embed Facebook ===
Contributors: poxtron
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R8Q85GT3Q8Q26
Tags: Facebook, facebook, Social Plugins, embed facebook, facebook video, facebook posts, facebook publication, facebook publications, facebook event, facebook events, facebook pages, facebook page, facebook profiles, facebook album, facebook albums, facebook photos, facebook photo, social,
Requires at least: 4.6
Tested up to: 4.8
Stable tag: 2.2.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Embed any public Facebook video, page, comment, event, album, photo, profile. Add Facebook comments to all your site or embed any Social Plugin.

== Description ==

Automatically embed any type of content from Facebook directly into your site by simply copying the url into the editor.

There are two types of embeds: Custom Embeds that are entirely native to this plugin and social plugins which are pieces of code created by Facebook developers

[All custom embeds examples](http://www.wpembedfb.com/demo-site/category/custom-embeds/)

[All social plugins examples](http://www.wpembedfb.com/demo-site/category/social-plugins/)

As an alternative to automatically embed your content you can use the `[facebook url]` shortcode instead and pass on some parameters to change each embed [examples](http://www.wpembedfb.com/demo-site/category/custom-embeds/).

You can also use the native `[embed]` [shortcode](https://codex.wordpress.org/Embeds).

= Supported Embeds =
* Facebook Live Video
* Facebook Videos
* Facebook Albums
* Facebook Events
* Facebook Photos
* Facebook Fan pages
* Facebook Profiles
* Facebook Posts
* Facebook Comment

= Facebook Social Plugins =
Pieces of code crafted by Facebook developers for us mortals ( [official documentation](https://developers.facebook.com/docs/plugins/) ). Create a default instance with the shortcode: `[fb_plugin type]`

Examples:

A like and share button for the current page

`[fb_plugin like share=true]`

Send a message to a Facebook fan page.

`[fb_plugin page href=https://www.facebook.com/wpemf/ small-header=true height=350 tabs=messages ]`

See a lot more examples [here](http://www.wpembedfb.com/demo-site/category/social-plugins/).

= Facebook Comments =
Automatically replace the WordPress comments system with Facebook comments or manually using the shortcode `[fb_plugin comments]`.

To enable moderation you have to set up a Facebook App ID and Secret also scrape the urls on Facebook more details inside the settings page.

= The Quote Social Plugin =
The last social plugin to come to life allows your visitors to share any part of your site just by selecting text. Activate it automatically on selected post types or by using the shortcode `[fb_plugin quote]`. [Demo](http://www.wpembedfb.com/demo-site/social-plugins/quote-plugin/).

= Requirements =
* Nothing to embed posts, pages, videos and comments.
* For custom embeds and comments moderation a Facebook App Id and Secret are required more details inside settings.

**[One page live demo](http://www.wpembedfb.com/demo/)**

**[Demo Site](http://www.wpembedfb.com/demo-site/)**

= Contributing =
If you found a bug or want to add an extra feature create a pull request on [github](https://github.com/sigami/wp-embed-facebook).

= Premium extension =
* "Elegant" custom embeds theme
* Embed full event shortcode
* Embed full fan page shortcode
* Embed events with address and admins
* Embed albums with more that 100 photos
* Embed all upcoming events of a fan page
* mbed personal data
* Features cooking
    * Shortcode creator
    * Special templates for albums and pages

== Installation ==

1. Download wp embed facebook plugin from [WordPress](http://wordpress.org/plugins/wp-embed-facebook)
1. Extract to /wp-content/plugins/ folder, and activate the plugin in /wp-admin/.
1. Create a Facebook App follow the [step by step guide](http://www.wpembedfb.com/creating-a-facebook-app-the-step-by-step-guide/).
1. Copy the App Id and App Secret to the “Embed Facebook” page under the Settings section.
1. Change settings to your liking.
1. Enjoy and tell someone !

== Frequently Asked Questions ==

= How can I change the way an embed looks? =

You can overwrite the embed template with a custom one.

1. Create a folder on **your theme** named "plugins".
1. Inside that folder create a new one named "wp-embed-facebook".
1. Inside that folder create a new one named "default".
1. Copy the contents of “wp-embed-facebook/templates/default/” to “your-theme/plugins/wp-embed-facebook/default”
1. Change the template files to what you want. Inside each file you can access the `$fb_data` array that contains the information retrieved from facebook.

= I moved from another Facebook comments/like plugin and my comments don't show =

Go to advanced section on settings and tick the option "Use permalinks on social plugins urls" if it does not work please create a support ticket mentioning the old comments plugin.

= How can I make my page load faster ? =

Social plugins will load at its own time via JavaScript so you just have to wait on them, for custom embeds a cache plugin will greatly increase performance.

= Why can't I embed a certain fan page getting error code 100 ? =

The Facebook page your are trying to embed is not available to users logged out from Facebook.

= I cannot embed my photo stream =

This plugin only works for embedding **albums**. Alternatives are being tested and they will be available very soon.

= Is there a way to embed an album with more than 100 photos ? =

Change the number of embedded photos on settings or use the shortcode like this [facebook album_url photos=200 ] This can only be achieved using the premium version.

= How I can guarantee that this software is kept up to date? =

Buying the premium extensions helps to keep this project alive.

== Screenshots ==

1. Fan Page Social Plugin
2. Fan Page Custom Embed
3. Video Social Plugin
4. Video Custom Embed
5. Album
6. Album with more than 100 photos  (Premium only)
7. Event
8. Full Event Shortcode (Premium only)
9. Full Page Shortcode (Premium only)
10. Fan page upcoming events (Premium only)
11. Post Social Plugin
12. Post Custom Embed
13. Photo Custom embed
14. Settings
15. Profile


== Changelog ==

= 2.2.3 =
* Added: filter for $fbsdk
* Added: Facebook SDK v2.11

= 2.2.2 =
* Added: Facebook SDK v2.10

= 2.2.1 =
* Fixed: Album thumbnails
* Fixed: Default sdk locale
* Fixed: Tiny MCE CSS not updating correctly

= 2.2.0 =
* Improved: fb.js no longer depends on jQuery
* Improved: Templates CSS (completely rewritten)
* Improved: Custom embed responsiveness
* Improved: Album thumbnails look
* Fixed: Adaptive embed script
* Added: [embedfb] shortcode just in case [facebook] shortcode is being used by another plugin
* Updated: Deprecate Facebook SDK version 2.3 force to 2.4

= 2.1.14 =
* Added: Compatibility for PHP 5.3 0..o

= 2.1.13 =
* Fixed: website page url @sabrina_b
* Added: Shortcode widget
* Updated: Facebook locales


= 2.1.12 =
* Added: shortcode widget
* Added: api version 2.9
* Added: filter wef_embedded_with
* Added: advanced option to use permalinks on social plugins urls
* Improved: updated wp_get_sites for get_sites

= 2.1.10 =
* Improved: Deprecated Facebook sdk 2.1 and 2.2 automatically updates to 2.3
* Improved: admin navigation

= 2.1.9 =
* Fixed: page likes count
* Fixed: css not found no custom templates

= 2.1.8 =
* Fixed: Removed download video link
* Fixed: Prevent wrong file load on theme templates @pierreg_

= 2.1.7 =
* Fixed: url understanding on page embeds when they have the format page-name-8798798
* Fixed: website on custom embed fail on edge cases
* Fixed: download video link
* Added: spanish translations ES, MX, AR, CL, GT, PE, VE

= 2.1.6 =
* Fixed: missing posts from page embeds
* Updated: readme file

= 2.1.5 =
* Added: new filter wef_lightbox_title
* Added: event time format option
* Added: single post time format option
* Added: single post now triggers photos in lightbox
* Fixed: single post caption link error
* Added: compatibility for custom embed templates css (pierreg_)
* Fixed: typos on readme file
* Improved: Lightbox css and option section
* Added: Facebook API v2.7
* Added: option to use lightbox on [gallery] shortcode (experimental)

= 2.1.4 =
* Added: new action 'wp_embed_fb' on WP_Embed_FB class
* Added: compatibility for using href=, url=, uri=, link=, on [facebook] shortcode
* Fixed: shortcode not running when text changed for htmlentities
* Added: warning when shortcode in badly used.

= 2.1.3 =
* Fixed: includes giving problems in some sites.

= 2.1.2 =
* Added: [fb_plugin] shortcode to embed any type of social plugin
* Added: Auto embed comments plugin to certain post types
* Added: Auto embed quote plugin to certain post types
* Added: Default options for all social plugins
* Added: Advanced option to integrate other lightbox scripts to the album embeds
* Improved: Admin area is more comprehensive with tons of examples
* Improved: url recognition
* Deprecated: Old functions on Wef_Social_Plugins class

= 2.1.1 =
* Updated: social plugins embed

= 2.1.0 =
* Fixed: Shortcode [facebook object_id]
* Fixed: Error for uls https://www.facebook.com/something-3423223
* Fixed: Readme typo
* Added: Uglyfied scripts
* Added: Extra FAQ
* Added: Github for development https://github.com/sigami/wp-embed-facebook

= 2.1 =
* Removed: all options and moved them to a single one 'wpemfb_options'
* Removed: resize cover javascript it is now done with css
* Fixed: timezone bug custom post and events
* Added option to only load scripts when an embed is present
* Added option to reset all options
* Added JetPack Photon compatibility
* Added compatibility with some drag and drop themes
* Added lightbox.sass for theme developers
* Changed: Lightbox script and style
* Added Lightbox Option Album Label
* Added Lightbox Option Always Show Nav On Touch Devices
* Added Lightbox Option Show Image Number Label
* Added Lightbox Option Wrap Around
* Added Lightbox Option Disable Scrolling
* Added Lightbox Option Fit Images In Viewport
* Added Lightbox Option Max Width
* Added Lightbox Option Max Height
* Added Lightbox Option Position From Top
* Added Lightbox Option Resize Duration
* Added Lightbox Option Fade Duration
* Changed css on classic theme

= 2.0.9.1 =
* Fixed: Admin notice bug
* Lightbox css improved
* Fixed: cover css


= 2.0.9 =
* Fixed: css on footer when using different themes
* Changed: d all.js to sdk.js (bryant1410)
* Optimization for sites with no Facebook App
* Added error messages for special cases
* Added advanced option for selecting Facebook SDK version
* Fixed: locale error inside editor
* Fixed: link underline in some themes
* Fixed: several css and html structure nothing critical

= 2.0.8 =
* Fix Event title css

= 2.0.7 =
* Settings translation link
* Improved object id identification for fan pages and posts
* Video download option

= 2.0.6 =
* Added new filter "wpemfb_embed_fb_id"
* Added Download Video option
* Added Settings link on plugin description
* Improved type and fb_id recognition

= 2.0.5 =
* Improved [embed] shortcode compatibility !
* Added new 'photos' attribute for shortcode used only on albums
* Added 'type' parameter to wpemfb_template filter
* Fixed: https on all templates
* Fixed: like and comment links on single post raw
* Fixed: forced app token only if it has app
* Fixed: admin shortcode references
* Fixed: removed unused options on uninstall
* Fixed: translations strings
* Fixed: notice on installations with no FB App

= 2.0.4 =
* changed shortcode tag from [facebook=url] to [facebook url]
* force app access token

= 2.0.3 =
* Fixed: notice on pages and events with no cover
* Moved admin scripts to footer

= 2.0.2 =
* Added options for page social plugins
* Changed admin layout
* Does not need facebook app for simple embeds
* More human friendly

= 2.0.1 =
* Fixed: message on photo single post

= 2.0 =
* Fixed: language issue when embedding social plugins in admin
* Fixed: time on events
* Changed:  Facebook API to 2.4
* Added a new parameters for shortcode 'social_plugin' and 'theme'
* Fixed: shortcode use [facebook FB_Object_ID ]
* Improved CSS and themes
* New Embed Post Raw

= 1.9.6.7 =
* Fixed: delete of options on uninstall

= 1.9.6.6 =
* Fixed: Embed Video Error
* Fixed: like and follow button html

= 1.9.6.5 =
* Fixed: more things on multisite
* Fixed: Page Template HTML

= 1.9.6.4 =
* Fixed: translation files
* Fixed: bug on event template

= 1.9.6.3 =
* Fixed: MultiSite error
* New Shortcode use [facebook FB_Object_ID ] solution for fb permalinks
* Fixed: raw attribute on shortcode when url is video

= 1.9.6.2 =
* Local Release

= 1.9.6.1 =
* Fixed: headers already sent notice.
* Added Links to Facebook Apps and plugin settings
* Removed: deprecated is_date_only field on event template

= 1.9.6 =
* Fix Fatal Error on non object

= 1.9.5 =
* Fixed: event templates
* Fixed: album thumbnails
* Fixed: jQuery UI error when admin is in https

= 1.9.4 =
* Added option to embed raw videos with facebook code
* Added poster on raw embed videos
* Changed:  to FB API v2.3
* Changed:  raw photo template

= 1.9.3 =
* Fixed: error on older versions of PHP

= 1.9.2 =
* Line breaks fix

= 1.9.1 =
* Line breaks fix

= 1.9 =
* Facebook video embed code in case video type is not supported
* Fix: Compatibility with other facebook plugins thanks to ozzWANTED
* New filter: 'wpemfb_api_string' and 'wpemfb_2nd_api_string'
* Show embedded posts on admin
* Fix undefined variable on js
* Fix languages on event time

= 1.8.3 =
* Better Video Embeds

= 1.8.2 =
* Fix: Error on some systems nothing critic.

= 1.8.1 =
* Fix: Warning on Dashboard
* Changed: : Readme.txt

= 1.8 =
* Compatibility with twenty 15 theme
* New css for embeds
* Compatibility with premium plugin

= 1.7.1 =
* Documentation Changed: 
* New advanced option

= 1.7 =
* Better detection of video urls
* FB js now loaded via jquery
* More comprehensive admin section
* Fix album pictures not showing on chrome

= 1.6.2 =
* minor bugs

= 1.6.1 =
* fix website url
* fix embed post width

= 1.6 =
* Responsive Template
* Posts on Page Embeds
* Album Photo Count
* Fixes on Admin Page
* Remove of unnecessary code

= 1.5.3 =
* fixed Warning in admin

= 1.5 =
* Support for raw videos and photos
* Support for albums
* Spanish translations

= 1.4 =
* Support for Video url's
* Support for filter 'wpemfb_category_template'
* Follow buttons
* Better photo embeds
* New website www.wpembedfb.com !

= 1.3.1 =
* Documentation and screenshots.

= 1.3 =
* Shortcode [facebook url width=600] width is optional
* Themes
* Multilingual Like Buttons

= 1.2.3 =
* Bugs and documentation

= 1.2.1 =
* Changed: d Instructions
* Change theme template directory

= 1.2 =
* Embed posts
* Embed photos
* Like buttons

= 1.1.1 =
* Corrected links on events.

= 1.1 =
* Making the plugin public.

= 1.0 =
* Making the plugin.

== Upgrade Notice ==

= 2.2.2 =
Added Facebook SDK v2.10
