=== AnyComment ===
Contributors: ateshabaev
Tags: anycomment, comments, comment moderation, comment, anycomment.io
Requires at least: 4.4
Tested up to: 4.9.6
Requires PHP: 5.4
Stable tag: 0.0.33
License: GPLv2 or later
License URI: http://www.gnu.org/

AnyComment allows you to have a better commenting experience in WordPress.

== Description ==

> **Notice!** 0.0.35 will have some nice change regarding frontend & backend, new feature a lot more! Get prepared. Update will be ready within this week (16-21.07.2018)!

> See [demo](https://anycomment.io/demo/)

Are you tired of connecting different third part commenting system to your website or having the default ones?

AnyComment is here to help you - it is a better commenting system for WordPress.

No dependency on third party services, all comments stored in your database.

You may connect your favorite social networks.

Here is the list of the supported ones:

* Facebook
* Google
* Twitter
* VK
* Odnoklassniki
* GitHub
* and more to be added

Dark & light themes available.

AnyComment stands for simplicity, so comments have very simple user interface, it is up to you what other features to add.

Ps. Comment will be actively supported. We would like to have your opinion on what could be improved or fixed. Thank you for using it!

= Can I contribute? =

Yes! Join our [GitHub repository](https://github.com/bologer/anycomment.io) :)

= Features =
* Social network authorization via Facebook, VK, Twitter, Google, Odnoklassniki or GitHub
* Translated in English & Russian
* Suitable for dark & light themes (on your choice)
* Blazing-fast AJAX way of sending comments
* Likes
* Simple & informative dashboard with graphs to display number of comments over number of active user per certain period & most active commenting users
* Comments do no inherit any of the currently active theme styles. Meaning that comments will not be broken by styles you have.
* Get latest plugin update news directly in the dashboard, don't miss a thing
* Integration with [WP User Avatar](https://wordpress.org/plugins/wp-user-avatar/), to specify customized avatars
* All comments stored in your own database. We do not create extra tables for these reasons. We reuse native comment's table, so all of the default WordPress functionality comes out of the box.

= TODO features =
* Option: customization of comment styles
* Option: add option to define whether new comments will be added automatically or will be required to be moderated first
* Option: add option to allow guests to leave comments
* Option: Auto-update comments on new comments
* Option: to choose favorite comment of the post (sticky at the top of the comments)
* Support more languages
* Code highlighting
* Markdown support
* Integration of: Google's reCaptcha
* Integration of more social networks: Dribble, etc
* More widgets to be added on the page (sidebar, custom pages, etc)
* Special moderator panel integrated into custom AnyComment pages (for easy comment moderation)
* More statistics and analytics regarding comments...
* And a lot more...

== Screenshots ==

1. White theme
2. Dark theme
3. Plugin dashboard. Analytics on current month and most active users

== Frequently Asked Questions ==

= Installation & Instructions =

* Install via WordPress admin panel directly (or [download plugin](https://downloads.wordpress.org/plugin/anycomment.zip) and upload into the `/wp-content/plugins/anycomment/` directory)
* Activate from `Plugins` section in admin panel
* Go to preferred social media and register to get API access (API key, secrets, etc)
* Specify required API details in special social network settings
* Go to some post and try to authorize using any of the enabled social networks and leave a comment!

= Why Facebook and Twitter want Privacy Policy URL? =

Facebook and Twitter are now require your website to have Privacy Policy.

After [General Data Protection Regulation](https://en.wikipedia.org/wiki/General_Data_Protection_Regulation) (GDPR) regulation was made it is now required to have a link to Privacy Policy.

Currently known problem is that it is NOT possible to start using Facebook API (only in test mode), until you specify `Privacy Policy URL` in their settings.

Twitter is not that strict, and only does not provide email of the user when he is logged in.
What You Can Do

WordPress currently has default Privacy Policy page create for your. What you can is to add the following text there under "What personal data we collect and why we collect it" header:

> When you authorize via some of the available social networks, we collects the following information about you: first name, last name, login (when available), avatar URL and email (when available or access given).
> Some of the information may vary from social to social. For example, VK.com give access to email only when you accept it while authorizing.
> We record information about only when social network allows us to have it.

== Changelog ==

= 0.0.32 – 16.07.2018
* Fixed problem with array syntax support on PHP version 5.5, #49
* Fixed possible XSS in the comment

= 0.0.32 – 10.07.2018 =
* Enh - introducing comment likes, #35
* Enh - minified CSS, to save some loading time
* Enh - ability to define default user role on creation (registration via plugin), #37
* Enh - when user has non-default Gravatar, use it, otherwise use default from plugin, #10
* Fix - proper integration with WP User Avatar & Akismet
* Enh - load commnets on scroll (new options to load comments when user scroll to it), #36
* and other small bug fixes & improvements


= 0.0.2 – 01.07.2018 =
* Fix - admin OR moderator was unable to edit comment as it was too old
* Enh - ability to specify number of default comments to load. The same settings applies to number of comment loaded per page, when there are more comments on post/page then specified in settings
* Fix - plugin is not enabled until you specify at least one social network, even thought you set plugin to be ON in general settings, #11
* Enh - refactoring of comments logic towards native WordPress REST
* Enh - ability to update any comment if user has `moderate_comments` or `edit_comment` capability (no time limit)
* Enh - ability to update personal comment within 5 minutes
* Fix - guest user cannot see comment actions (reply/edit)
* Enh - added two new authorization methods: GitHub & Odnoklassniki
* Fix - comment text box was overflowing on long texts, #22
* Fix - better responsiveness of dashboard layout, #32
* Fix - avatars uploaded locally to escape problem when some social medias were blocking access to avatar after token expiration, #14
* Enh - display most recent news from plugin, #31
* Enh - other small bug fixes and improvements
* Eng - moved completely towards REST architecture

= 0.0.1 - 24.06.2018 =
* First Release
* Options to specify API details (secrets, etc) for social authorization: Vk, Twitter, Facebook, Google
* Integrated with [WP User Avatar](https://wordpress.org/plugins/wp-user-avatar/)
* Authorize via VK, Twitter, Facebook, Google
* Enh: date when comment is left is based on website's language. List of supported languages can be seen [here](https://github.com/hustcc/timeago.js/tree/master/src/lang)
* Enh: comment count at the top updated automatically when new comment added
* Enh: add comments with AJAX, no need to refresh the page
* Enh: ability to reply to nested comments up to 2 levels
* Enh: when all socials disabled, libraries not loaded and they are not shown to end user
