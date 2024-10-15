=== Footnotes & Content ===
Tags: footnotes, formatting, notes, reference
Requires at least: 6.0
Tested up to: 6.6.1
Requires PHP: 7.4
Stable tag: 3.8.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows post authors to easily add and manage footnotes in posts.

== Description ==

**Footnotes & Content** plugin is a powerful method of adding **footnotes** into your posts and pages. You can have as many **footnotes** as you like pretty easily in every page, post or ACF block, WooCommerce is also supported. That is the fastest footnote plugin which is using extremely low resources - you wont even notice that it is there.

You can visit the [Github page](https://github.com/sdobreff/footnotes/ "Github") for the latest code development, or if you want to report an issue with the code.

## Key features include...

* Simple footnote insertion via markup of choice (default - double parentheses)
* Gutenberg support
* Combine identical **footnotes**
* Paginated posts are supported
* Suppress **Footnotes** on specific page types
* Option to display ‘pretty’ tooltips using jQuery
* Option to display footnotes as tooltips using vanilla JS
* Lots of configuration options
* Different footnotes settings per post - you can use different settings on Post level - changing styles and UI

**Footnotes & Content** plugin is designed to ease the creation of a new footnote. It also gives you the ability to easily switch from most of the existing **footnotes** plugins to this one. Lets face it - almost 100% of them are abandoned or in awful condition. It supports PHP8, it is written using best practices and follows the WordPress standards, give it a try. You can quickly check the plugin [here](https://playground.wordpress.net/?plugin=awesome-footnotes&networking=yes "WP Playground")

## Technical specification...

* Designed for both single and multi-site installations
* PHP8 fully compatible
* PHP multibyte must be installed

== Getting Started ==

Creating a footnote is incredibly simple - you just need to include your **footnote** in double parentheses (default, but you can change that), such as this:

This is a sentence **((and this is your footnote))**.

You can change the markup for the footnote in the settings page of the plugin.

The footnote will then appear at the bottom of your post/page.

Don't put footnotes in short description / excerpts - the plugin won't work there by design.

Or you can use a shortcode for where you want your footnotes to appear. The shortcode is "`awef_show_footnotes`". The shortcode also accepts a parameter of the post id in format of 'post_id=1'. If not presented, the global \WP_Post object will be used. 

You can also use a PHP call in your templates or whatever you like by using the following:
`AWEF\Controllers\Footnotes_Formatter::show_footnotes( array( 'post_id' => 1 ) );`
Note: If you choose this way (above), you have to go to the plugin settings, and set "Do not autodisplay in posts" to true.

**Advanced Custom Fields (ACF)** are also supported out of the box - just read and keep in mind this:

Unfortunately there are limitations with the **ACF** because of its block structure. There is no way to guess how many blocks are there, which is first, second, are there more blocks or not … So every block will show its own footnotes, and shortcodes are not working outside them. Currently there is no way to achieve that functionality. So they are treated more like endnotes if there are multiple blocks using the footnotes tags.

What plugin does is to keep track of the footnotes and keep proper numbering among the blocks (again there is no way to guess which is which, so they are parsed in order of their callings from backend, but they can be shown in entirely different places on the front end.)

**WooCommerce** (including new product editor) is also supported.

Unlike any other plugin, this one gives you the ability to have different settings for different posts. When editing post, you can change the setting for the plugin which will apply for that specific post, and others will keep using the global settings. If you think that this is too much, you can always disable this from advanced settings.

== Other plugins compatibility ==

There are plugins with which that one is 100% compatible - meaning that you can directly jump from them to this one:

- [WP Footnotes](https://github.com/drzax/wp-footnotes "Github - wp-footnotes")
- [Footnotes Made Easy](https://wordpress.org/plugins/footnotes-made-easy "WordPress.org - footnotes-made-easy")

== Options ==

You have a fair few options on how the identifier links, footnotes and back-links look which can be found in the WordPress admin area either on the stand alone page, or under Settings -> Footnotes - that depends on your desired setting in the plugin.

== Shortcode options ==

`[awef_show_footnotes]` Is the shortcode you should use. Inside the post content, there is nothing more that you have to do.
If you want to use the shortcode outside of the post content, then you need to add the post id as a parameter:
`[awef_show_footnotes post_id=1]`
If outside of the post content, and there is no parameter of the post id provided, then the plugin will try to use the global post if presented.

== Paginated Posts ==

Some of you seem to like paginating post, which is kind of problematic. By default each page of your post will have it's own set of footnotes at the bottom and the numbering will start again from 1 for each page.

The only way to get around this is to know how many posts are on each page and tell Awesome Footnotes what number you want the list to start at for each of the pages. So at some point on each page (that is, between each `<!--nextpage-->` tag) you need to add a tag to let the plugin know what number the footnotes on this page should start at. The tag should look like this `<!--startnum=5-->` where "5" is the number you want the footnotes for this page to start at.

== Referencing ==

Sometimes it's useful to be able to refer to a previous footnote a second (or third, or fourth...) time. To do this, you can either simply insert the exact same text as you did the first time and the identifier should simply reference the previous note. Alternatively, if you don't want to do all that typing again, you can construct a footnote like this: `((ref:1))` and the identifier will reference the footnote with the given number.

Even though it's a little more typing, using the exact text method is much more robust. The number referencing will not work across multiple pages in a paged post (but will work within the page). Also, if you use the number referencing system you risk them identifying the incorrect footnote if you go back and insert a new footnote and forget to change the referenced number.

== Installation ==

**Footnotes & Content** can be found and installed via the Plugin menu within WordPress administration (Plugins -> Add New). Alternatively, it can be downloaded from WordPress.org and installed manually...

1. Upload the entire `awesome-footnotes` folder to your `wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress administration.

Voila! It's ready to go.

== Frequently Asked Questions ==
= How do I create a footnote? =
Use a footnote in your post by using the footnote icon in the WordPress editor or by using a formatter character (this is " ((" for opening (beginning) of the footnote and "))" for closing the footnote)

= I've used another plugin, can I switch to this one? =
There probably be implemented some importer in the future version of the plugin, but as far as your current plugin is using opening / closing characters, you can change the opening and closing tags of the Footnotes in the plugin settings to the current ones.
Example:
Lets say currently you are using plugin which marks a footnote like this:
 [[this will be a footnote]]
Then go to settings and change the Open and Close footnote tag to "[[" and "]]" respectively.

= I have multiple sites - can I use the exact same settings for each of them? =
Yes - there is an import/export functionality in the plugin - just make the changes, export them, and then include where you want. Or store them, so you can always get back to them.

= Other than the available options, can the footnotes output be styled? =
Yes it can. The easiest way is to use the CSS editor in your theme customizer. For example, 'ol.footnotes' refers to the footnotes list in general and 'ol.footnotes li' the individual footnotes. You can edit the styling in the plugin settings directly (Options page), or empty the styling and use your own the way it fits your needs best.
CSS classes plugin is using are:
- footnotes - for the <ol>
- footnote - for the <li> elements inside
- awesome-footnotes-header - for the footnotes header wrapper
- awesome-footnotes-footer - for the footnotes footer wrapper

= Is there support for the Block Editor/Gutenberg Editor? =
Yes. You can use the Awesome Footnotes button in the toolbar of the Block Editor to move the selected text into a footnote.

== Screenshots ==

1. An example showing the footnotes in use
2. The settings screen with advanced settings shown
3. Plugin in Gutenberg editor
4. Plugin in standard editor
5. Plugin settings in Gutenberg editor
6. Plugin in standard editor

== Change Log ==
= 3.8.0 =
Speed optimizations and lots of bugs fixed, related to showing the footnotes proper symbol selection. As of this version you can set different options for the footnotes on post level - that means, that you can have different footnotes formatting for every single post. New settings UI - now supporting dark mode as well.

= 3.7.0 =
New AJAX class is introduced for speeding the plugin work. Now separate template file is used to show the footnotes, it can be overwritten from withing the theme. New vanilla JS to show the footnotes is introduced - no external libraries dependencies.

= 3.6.0 =
Fixed bug when trying to save settings and end up with infinity loop. Thanks to [@electrolund](https://wordpress.org/support/users/electrolund/)

= 3.5.0 =
Fixed bug when shortcode is used but no footnotes markup is found in the content. Added hook to check if the call comes from the excerpt hook and removes the footnotes if there are some.

= 3.4.0 =
Bug fixes related to showing posts in loop and shortcodes functionality fixes.

= 3.3.3 =
Bug fixes related to the proper footnotes numbering in the text.

= 3.3.2 =
Removed ACF from options and settings - the ACF (if detected) is automatically enabled.

= 3.3.1 =
Typo fixes

= 3.3.0 =
* Compatibility checks with other plugins, option to put back links before the footnote text, system status improvements, code optimizations.

= 3.2.3 =
* Extracting MySQL version fix.

= 3.2.2 =
* Typo fix. Added check for function existence as WordPress playground has a lack of that functionality.

= 3.2.1 =
* WordPress playground fix.

= 3.2.0 =
* Code fixes and optimizations. Added system info tab.

= 3.1.0 =
* Added support for multiple ACF block in single post.

= 3.0.1 =
* Updated readme file.

= 3.0.0 =
* Plugin is rewritten to use the best practices and improved performance. PHP 8 is fully supported.

= 2.0.0 =
* Improved support and standards.

= 1.0.0 =
* Initial release.
