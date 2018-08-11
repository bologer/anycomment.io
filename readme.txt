=== AnyComment ===
Contributors: ateshabaev
Tags: anycomment, comments, comment, comment moderation, anycomment.io
Requires at least: 4.7
Tested up to: 4.9.6
Requires PHP: 5.4
Stable tag: 0.0.45
License: GPLv2 or later
License URI: http://www.gnu.org/

AnyComment allows you to have a better commenting experience in WordPress.

== Description ==

> See [demo](https://anycomment.io/demo/)

AnyComment allows you to have a better commenting experience in WordPress.

All you need is:

* choose social networks you prefer
* configure API keys (we have guides translated in English & Russian if you do not know how)
* you are done!

And good new is - no dependency on third party services, all comments stored in your database.

Here is the list of supported social networks:

* Facebook
* Google
* Twitter
* VK
* Odnoklassniki
* GitHub
* and more to be added

AnyComment stands for simplicity & speed. We value feedback, so if you have failures or any suggestions - please let us know about it!

= Can I contribute? =

Yes! Join our [GitHub repository](https://github.com/bologer/anycomment.io) :)

= Features =
* All comments stored in your own database. We do not create extra tables for these reasons. We reuse native comment's table, so all of the default WordPress functionality comes out of the box.
* Social network authorization via Facebook, VK, Twitter, Google, Odnoklassniki or GitHub
* Translated in English & Russian
* Suitable for dark & light themes (on your choice)
* Blazing-fast comments based on React
* Alert notification in comment area when new comment was added (by clicking on alert, new comment will be shown)
* Provide `Privacy Policy` link, so users know how their data processed and used (when not provided, no checkbox will be shown to users)
* Social avatars shown globally
* Likes (see likes count per comment/user in admin)
* Edit/delete comments when you are the owner or have moderate permission(s) directly in client area
* Assign default group for users who authorize via social network
* See user's social profile URL in admin
* Simple & informative dashboard with graphs to display number of comments over number of users who were engaged in the conversation per certain period & most active users
* Comments do no inherit any of the currently active theme styles. Meaning that comments will not be broken by styles you have
* Get latest plugin update news directly in the dashboard, don't miss a thing
* Integration with [Akismet](https://wordpress.org/plugins/akismet/) & [WP User Avatar](https://wordpress.org/plugins/wp-user-avatar/), to specify customized avatars

= TODO features =
* Option: customization of comment styles
* Option: add option to allow guests to leave comments
* Support more languages
* Code highlighting
* Markdown support
* Integration of: Google's reCaptcha
* Integration of more social networks: Dribble, etc
* More widgets to be added on the page (sidebar, custom pages, etc)
* Special moderator panel integrated into custom AnyComment pages (for easy comment moderation)
* More statistics and analytics...
* And a lot more...

== Screenshots ==

1. Plugin dashboard. Analytics on current month and most active users.
2. Settings view.
3. White theme.
4. Dark theme.

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

Read guide on how to create "Privacy Policy" page:
- [English version](https://anycomment.io/how-to-create-privacy-policy-page-in-wordpress/)
- [Russian version](https://anycomment.io/ru/kak-sozdat-stranitsu-politiki-konfidentsialnosti-v-wordpress/)

== Changelog ==

= 0.0.45 – 13.08.2018 =

**Enhancements:**

* Alert shown when new comment was added. Comment list will be automatically refreshed once clicked on alert, #63
* Now social media avatar shown globally in admin (e.g. in `dashboard`, `user.php`, `comment.php`, etc), #61
* Better layout for plugin news in admin, `New` label is shown for articles which are not older then 2 weeks, #62
* Added caching for news in dashboard (no need to load them every time) and limited to 3
* New design for setting up social networks, now tabbed and have (or if don't have yet will have set-up guides) guides on how to set-up each social media translated in English & Russian, #64, #66

**Fixes:**

* When user did not have social profile URL it lead to clickable name but incorrect URL, #60
* Do not load styles & scrips globally, only in plugin pages
* Plugin icon in admin sidebar was not displaying correctly and was overflowing when menu was opened
* Newlines in comment are now displaying correctly. Previously everything was as a single line

= 0.0.41 – 29.07.2018 =

* Fix issue when User Agreement checkbox was not shown

= 0.0.40 – 29.07.2018 =

**Enhancements:**

* Comment send button is now changing text based on action (edit/reply/send)
* Ability to specify User Agreement URL (used to collect consents from users to moderate personal information), text & URL is shown to guest users below list of available authorization options, #56
* Ability to delete personal or any comment if user has moderate permission, #59
* Moved social URL to the name of the user, better user experience (when enabled to show URLs)

**Fixes:**

* Uninstall hook was not properly cleaning-up data after plugin, #42
* Social authorization icon was shown even though it was disabled in admin, #57
* Guest user is not redirected back to post as redirect param is missing in social authentication URL, #58
* Options to enable/disabled show user social profile URL was ignored

= 0.0.35 – 20.07.2018 =
> **Important note:**
> Plugin was completely rewritten to React. It was required as on the very early stage it had a lot of JavaScript, partly merged with HTML).
> Logic behind plugin stays the same, we even added a few improvements and fixes, hope you like the change.

**Enhancements:**

* Comments rewritten to React!
* Post author now has "Author" badge in comments section, #45
* All assets are now minified (css, js) = faster load time
* Now possible to see number of likes per comment (`/wp-admin/edit-comments.php`) & user (`/wp-admin/users.php`), #43
* All settings moved to dashboard tabs (pages are still available, no worries), #38
* Mark new comment to be moderated first or be approved immediately, #50
* Ability to choose whether to show social profile URL in comments (when show is chosen, mini social icon in the bottom right corner will be clickable), #51
* Added new column "Social URL" in `users.php` which displays user's social profile URL

**Fixes:**

* Fixed issue when long texts were overflowing maximum with of the comment
* Fixed issue when it was not possible to disabled footer copyright ("Thanks" option in admin), #46
* Fixed issue when first & last name was not recorded in user profile

= 0.0.33 – 16.07.2018 =
* Fixed problem with array syntax support on PHP version 5.5, #49
* Fixed possible XSS in the comment

= 0.0.32 – 10.07.2018 =
* introducing comment likes, #35
* minified CSS, to save some loading time
* ability to define default user role on creation (registration via plugin), #37
* when user has non-default Gravatar, use it, otherwise use default from plugin, #10
* proper integration with WP User Avatar & Akismet
* load commnets on scroll (new options to load comments when user scroll to it), #36
* and other small bug fixes & improvements

= 0.0.2 – 01.07.2018 =
* admin OR moderator was unable to edit comment as it was too old
* ability to specify number of default comments to load. The same settings applies to number of comment loaded per page, when there are more comments on post/page then specified in settings
* plugin is not enabled until you specify at least one social network, even thought you set plugin to be ON in general settings, #11
* refactoring of comments logic towards native WordPress REST
* ability to update any comment if user has `moderate_comments` or `edit_comment` capability (no time limit)
* ability to update personal comment within 5 minutes
* guest user cannot see comment actions (reply/edit)
* added two new authorization methods: GitHub & Odnoklassniki
* comment text box was overflowing on long texts, #22
* better responsiveness of dashboard layout, #32
* avatars uploaded locally to escape problem when some social medias were blocking access to avatar after token expiration, #14
* display most recent news from plugin, #31
* other small bug fixes and improvements
* moved completely towards REST architecture

= 0.0.1 - 24.06.2018 =
* First Release
* Options to specify API details (secrets, etc) for social authorization: Vk, Twitter, Facebook, Google
* Integrated with [WP User Avatar](https://wordpress.org/plugins/wp-user-avatar/)
* Authorize via VK, Twitter, Facebook, Google
* date when comment is left is based on website's language. List of supported languages can be seen [here](https://github.com/hustcc/timeago.js/tree/master/src/lang)
* comment count at the top updated automatically when new comment added
* add comments with AJAX, no need to refresh the page
* ability to reply to nested comments up to 2 levels
* when all socials disabled, libraries not loaded and they are not shown to end user
