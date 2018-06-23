=== AnyComment ===
Contributors: ateshabaev
Tags: anycomment, comments, comment moderation, anycomment.io
Requires at least: 4.7
Tested up to: 4.9.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/

AnyComment allows you to have a better commenting experience in WordPress.

== Description ==

Are you tired of connecting different third part commenting system to your website or having the default ones?

AnyComment is here to help you - it is a better commenting system in WordPress.

No dependency on third party services, all comments store in your own database.

You may connect your favorite social networks.

Dark for light background websites, where as light for dark background ones.

AnyComment stands for simplicity, so default comment have very simple user interface, it is up to you what features to have.

Ps. Comment will be actively supported and maintained. We would like to have your opinions on what is good and what could be improved. Thank you for sharing love with us!

= Can I contribute? =

Yes! Join our [GitHub repository](https://github.com/bologer/anycomment.io) :)


Major features of AnyComment include:
* Social network authorization via Facebook, VK, Twitter or Google
* Currently translated in two languages: English, Russian
* Suitable for dark & light themes (on your choice)
* Blazing-fast AJAX way of sending comments
* Simple & informative dashboard with graphs to display number of comments over number of active user per certain period & most active commenting users
* Comments do no inherit any of the currently active theme styles. Meaning that comments will not be broken by styles you have.
* Integration with [WP User Avatar](https://wordpress.org/plugins/wp-user-avatar/), to specify customized avatars

Todo features:
* Option: customization of comment styles
* Option: add option to specify default number of comments loaded
* Option: add option to specify number of comments loaded per page
* Option: add option to load comments when user scrolls to it, save some loading time
* Option: add option to define whether new comments will be added automatically or will be required to be moderated first
* Option: add option to allow guests to leave comments
* Option: Auto-update comments on new comments
* Support more languages
* Code highlighting
* Markdown support
* Integration of: Akismet Anti-Spam and Google's reCaptcha
* Integration of more social networks: Dribble, Github, etc
* More widgets to be added on the page (sidebar, custom pages, etc)
* Special moderator panel integrated into custom AnyComment pages (for easy comment moderation)
* More statistics and analytics regarding comments...
* And a lot more...

== Screenshots ==

1. For light-colored websites.
2. For dark-colored websites.
3. Main plugin dashboard.

== Frequently Asked Questions ==

= Installation & Instructions =
* Upload files into the `/wp-content/plugins/anycomment/` directory OR install via WordPress directly
* Install & activate from `Plugins` section in admin panel
* Register you website in the API of social networks to get API secrets
* Specify required API details in special social network settings
* Try it out!
* Go to some post and try to authorize using any of the enabled social networks and leave a comment!

= Why Facebook wants Privacy Policy URL? =

This is now only facebook, but Twitter and Facebook.

After [General Data Protection Regulation](https://en.wikipedia.org/wiki/General_Data_Protection_Regulation) (GDPR) regulation was made it is not required to have a link to Privacy Policy.

Currently known problem is that it is NOT possible to start using Facebook API (only in test mode), until you specify `Privacy Policy URL` in their settings.

Twitter is not that strict, and only does not provide email of the user when he is logged in.

Current workaround is to copy some of the Privacy Policy from another website, correct it to you own, have public URL to it and specify it on Facebook.

On the next release, special options will be enabled on plugin to have Privacy Policy URL out of the box! :)

== Changelog ==

= 0.0.2 =
* Added authorization via GitHub & Odnoklassniki
* Fix - comment text box was overflowing on long texts, #22

= 0.0.1 - 24.06.2018 =
* First Release
* Options to specify API details (secrets, etc) for social authorization: Vk, Twitter, Facebook, Google
* Integrated with [WP User Avatar](https://wordpress.org/plugins/wp-user-avatar/)
* Authorize via VK, Twitter, Facebook, Google
* enh: date when comment is left is based on website's language. List of supported languages can be seen [here](https://github.com/hustcc/timeago.js/tree/master/src/lang)
* enh: comment count at the top updated automatically when new comment added
* enh: add comments with AJAX, no need to refresh the page
* enh: ability to reply to nested comments up to 2 levels
* enh: when all socials disabled, libraries not loaded and they are not shown to end user
