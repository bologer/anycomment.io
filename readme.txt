=== AnyComment ===
Contributors: ateshabaev
Tags: anycomment, comments, ajax comments, comment, comment moderation, custom comment form, comment email, anycomment.io
Requires at least: 4.7
Tested up to: 4.9.8
Requires PHP: 5.4
Stable tag: 0.0.55
License: GPLv2 or later
License URI: http://www.gnu.org/

AnyComment allows you to have a better commenting experience in WordPress.

== Description ==

AnyComment allows you to have a better commenting experience in WordPress.

It stands for simplicity & speed. We value feedback, so if you have failures or suggestions - please let us know on [support forum](https://wordpress.org/support/plugin/anycomment) or [GitHub](https://github.com/bologer/anycomment.io/issues) about it!

* [See demo](https://anycomment.io/demo/)

In order to start, you need:

* install plugin
* choose social networks you prefer
* configure API keys (we have guides translated in English & Russian to help you)
* you are good to go!

And good new is AnyComment is free & no dependency on third party services, all comments stored in your database.

Here is the list of supported social networks:

* Facebook
* Google
* Twitter
* VK
* Odnoklassniki
* GitHub
* Instagram
* Twitch
* Dribbble
* and more to be added

= Can I contribute? =

Yes! Join us at [GitHub](https://github.com/bologer/anycomment.io)

= Features =
* All comments stored in your own database. We reuse native comment's table, so all of the default WordPress functionality comes out of the box.
* Social network authorization via Facebook, VK, Twitter, Google, Odnoklassniki, GitHub, Instagram, Twitch or Dribble
* Ability to define form type: guests only, with social authorization or both
* Ability to upload files (define allowed extensions, limit, who can upload, etc)
* When user leaves a tweet link in the comment, plugin can embed it as external Twitter widget (can be disabled)
* Plugin is translated into English & Russian
* Suitable for dark & light themes or customize it yourself
* Blazing-fast comments based on React & WordPress REST API
* Alert notification in comment area when new comment was added (by clicking on alert, new comment will be shown)
* Ability to send email on new comment reply
* Ability to send email notification about new comment to administrator
* Ability to specify comma-separated list of words to be used to hold comments for moderators
* Clickable links in comment (control via admin)
* Image and video URLs as attachment (control via admin)
* Provide `Privacy Policy` link, so users know how their data processed and used (when not provided, no checkbox will be shown to users)
* Social avatars shown globally
* Likes (see likes count per comment/user in admin)
* Edit/delete comments when you are the owner or have moderate permission(s) directly in client area
* Assign default group for users who authorize via social network
* See user's social profile URL in admin
* Simple & informative dashboard with graphs to display number of comments over number of users who were engaged in the conversation per certain period & most active users
* Comments do no inherit any of the currently active theme styles. Meaning that comments will not be broken by styles you have
* Get latest plugin update news directly in the dashboard, don't miss a thing
* Integration with [Akismet](https://wordpress.org/plugins/akismet/), [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/) and [WP User Avatar](https://wordpress.org/plugins/wp-user-avatar/), to specify customized avatars

= TODO features =
* Option: ability to specify thanks message and/or page when user left his first comment, #88
* Option: ability to set default sorting, #84
* Option: ability to premoderate comments with links, #84
* Option: when user does not have email after registration via social, ask for it somewhere in the comments again
* Option: notify moderators about new comments, #77
* Option: ability to set default sorting, #85
* Option: embed Instagram photos, #115
* Option: embed Instafeed, #116
* Improve mobile layout, #106
* Option: ability to change email notification template, #97
* Ability to choose comment to make it sticky at the very top of all comments, #76
* Add checkbox to subscribe to new replies, #73 (now it is mandatory)
* Option: Ability to ban user or by IP address, #70
* Support more languages
* Code highlighting
* Markdown support
* Add more guides on how to set-up certain services, Google SMTP, Amazon SES, etc
* Cross sharing of comments, when user posts a comment, duplicate it on their wall
* Integration of: Google's reCaptcha
* Add Yahoo as authorization option
* More widgets to be added on the page (sidebar, custom pages, etc)
* Special moderator panel integrated into custom AnyComment pages (for easy comment moderation)
* More statistics and analytics...
* [And a lot more...](https://github.com/bologer/anycomment.io/issues)

== Screenshots ==

1. Plugin dashboard. Analytics on current month and most active users.
2. Social settings view.
3. Settings view.
4. White theme.
5. Dark theme.

== Frequently Asked Questions ==

= Installation & Instructions =

* Install via WordPress admin panel directly (or [download plugin](https://downloads.wordpress.org/plugin/anycomment.zip) and upload into the `/wp-content/plugins/` directory)
* Activate it from `Plugins` section in admin panel
* Go to preferred social media and register to get API access (API key, secrets, etc)
* Specify required API details in special social network settings
* Go to some post and try to authorize using any of the enabled social networks and leave a comment!

= Comment form does not load or gives an error? =

AnyComment uses WordPress' REST API.

Please confirm two things:

1. You WordPress is 4.7 or higher (this is when WordPress introduced REST API)
2. Going to `http://yourdomain.com/wp-json/` SHOULD NOT redirected to `http://yourdomain.com` or it give 404 Not Found page

For the first step - just upgrade WordPress, it is good.

For the second, check for plugins which make optimization of website. Most of them remove REST API.

For example, [Clearfy](https://wordpress.org/plugins/clearfy/) has option "Disable Rest API". Having this option "On" will fail to load comments in AnyComment.

Not yet working? Please follow the steps:

* Open comments page
* Open "Developer Console" tab (e.g. press F12 in Firefox or in Chrome). It can named a bit different, depends from browser to browser.
* Select "Network" tab, inside find "XHR" click on it (name can also vary). If you do not find "XHR" just ignore it
* Refresh page
* In the list find request which looks something like the following: `https://yourdomain.com/wp-json/anycomment/v1/comments?post=15&parent=0&per_page=15&order=desc&order_by=id`
* Click on it, copy its content and take screenshot of it and report in [support forum](https://wordpress.org/support/plugin/anycomment)

= Why Do Social Media Ask For Privacy Policy link? =

It is now one of the requirements to have Privacy Policy link on your website due to GDRP regulation.

AnyComment is GDRP compliant.

You can read more about General Data Protection Regulation on [Wikipedia](https://en.wikipedia.org/wiki/General_Data_Protection_Regulation).

Read guide on how to create "Privacy Policy" page:
- [English version](https://anycomment.io/how-to-create-privacy-policy-page-in-wordpress/)
- [Russian version](https://anycomment.io/ru/kak-sozdat-stranitsu-politiki-konfidentsialnosti-v-wordpress/)

= Why Does Facebook Require HTTPs? =

From some recent time, Facebook now required website to have HTTPs connection in order to work with their API and we cannot do anything about it.

Most of the hosting providers support single button-like installation of SSL certificate, so it should not be that difficult.

It could only be difficult for website who have HTTP only and hight traffic as transfer to HTTPs can cause some traffic lose.

== Changelog ==

= 0.0.55 – 10.09.2018 =

**Enhancements:**

* Added a Jetpack, Disqus, Disable Comments and a few other plugin to the list of possible problems, #134
* Improved mobile layout, #106
* Improved speed of theming, now x1.5 faster to generate custom styles
* Added logout link to logged in client, #133
* Added ability to customize generated notification email (only for  admin and reply for now), #97
* Added "Shortcodes" tab. It will have list of available shortcodes, #139
* Added a helper notice to admins & moderators about closed comments per post, globally or if post is password protected (comments did not show in this case), #142

**Fixes:**

* Fixed notice message in admin, #132
* Fixed issue when "load on scroll" option was active and comments did not load on short pages because it was not possible to scroll, #135
* Fixed issue when custom styles were ignored as dark theme was selected
* JavaScript & Css assets are not loaded on the page, when comments are disabled or post is password protected


= 0.0.54 – 06.09.2018 =

**Enhancements:**

* Small improvements in the documentation on how to set-up certain social network

**Fixes:**

* Small fixed for cached sidebar news. Added dependency on the website locale
* Fixed issue when some users were unable to authorize using Google caused 500 error, #127
* Fixed issue on comment delete, no more need to add DELETE option in Apache or Nginx
* Fixed typo in Russian translation message when trying to delete a comment
* Fixed issue when comments displayed on the single page could go over the page content as invisible element, #129
* Fixed issue with file input icon (was displaying as black square instead), #128
* Fixed issue when send comment button was too close to the "accept privacy policy" checkbo

= 0.0.53 – 04.09.2018 =

**Fixes:**

* Small fixes to translations regarding options to show/hide user URL
* Fix for main plugin shortcode, now use `[anycomment include="true"]` to include comments on custom place (reported by Ivan)

= 0.0.52 – 03.09.2018 =

**Enhancements:**

* Ability to customize styles of the plugin in the frontend (e.g. color of button, text size, color, avatar sizes, etc). Check out "Settings" -> "Design" tab , #113
* Files: attach files by dragging into the comment area, #68
* Files: attach files via by clicking on small photo icon in the top right of the comment text field, #68
* Files: ability to allow/disallow file upload by guest users, #68
* Files: plugin will add URL of the uploaded file to the comment field (when there is already some comment text, URL will be appended), #68
* Files: option to define comma-separated list of allowed MIME types (e.g. .jpg, .png) or even as image/* for all images and audio/* for audios, #68
* Files: added list of uploaded files in the admin (possible to delete, paginate, etc), #68
* When user logs out from admin top bar or somewhere else, he is going to be redirected back to post comment section instead of a login page, #122
* Added some text above list social icons as some of the users were confused and thought these were sharing buttons, #123
* Added "Possible Problems" to dashboard to help admin to figure out about possible problems or conflicts with other plugins, #117
* When some comment is remembered the comment field will expand automatically after the page has loaded
* Now "Read more", "Show less" link below long comment as some users were a bit confused that it is possible to expand comment by clicking on its text, #118
* Ability to attach Tweets from Twitter directly in the comment by adding link to it, #96

**Fixes:**

* Fixed issue when white theme had white links
* Fixed default options overwrite, before default values were not applied
* Fixed missing Russian translation when user is guest and only has option to authorize using social
* Some themes have hash navigation to comments as "#respond", so it was added, #124
* Removed hash from "Callback URL" as Google does not allow it, #119

= 0.0.51 – 28.08.2018 =

**Enhancements:**

**Fixes:**

* Fixed issue when reply user dialog was in dark color in dark theme (invisible), #114
* Fixed issue when guest inputs (name, email, website) were white in dark theme

= 0.0.50 – 27.08.2018 =

**Enhancements**

* News of plugin in the right sidebar inside console are display per your blog language. For now English and Russian supported
* Likes are now shown to guest users, however they do not have ability to like. When liked by guest, plugin will show alert about requirement to login, #108
* Removed submenu from main menu in admin. Now all of the submenus can be found as tabs inside the dashboard
* "Settings" tab in admin is now split into specific configuration tabs: General, Design, Moderation & Notifications
* Now possible to specify #comments, #to-comments or #load-comments (e.g. https://yourwebsite.com/cool-post/#comments) to move users screen to comment section
* Added proper subject to each type of email (e.g. sent to admin and to user as reply)
* Added option to make video or image link as attachment, #87
* Added option to make links in comment clickable or not, #83

**Fixes:**

* Added [Facebook guide](https://anycomment.io/en/api-facebook/) details regarding "Status" & HTTPs requirement & fixed other guides, also added instruction on how and where to find "Callback URI", #102
* Cosmetic style corrections (fixed height/alignment/decoration of button, make inline guest inputs 100%), #104
* Fixed issue when link in news sidebar lead to 404 page,  #109
* Plugin was not showing comment box until option to show comment was enabled and at least one social was configured. Now this logic is a bit different (plugin allows guest users), so now only required to enable option to show comment box, #112
* Some users were confused with dropdowns in the admin, as they did not have any visuals, such as triangle to see that there is a list of options
* When load on scroll is enabled and user comes from email his screen was not moved directly to the comment, #103
* When user was logged in via social network, he was redirected back to the top of the post. Now he is being moved to comment section
* Fixed issue with `trim()` warning (only some users experienced such problem) near avatars in Dashboard & Comments page in admin
* Fixed issue when emails about comment reply were not send to guest users (as it was not planned to have guest form). Now we have, so should support it
* Fixed dark theme CSS styles as after recent update of styles they got broken

**Other:**

* Added new entry to FAQ about Facebook forcing websites to have HTTPs in order to use API

= 0.0.49 – 24.08.2018 =

* Cosmetic fixes of form: avatar was not shown for logged in user & button was not properly aligned


= 0.0.48 – 23.08.2018 =

> I will try to deliver more fixes and features over next release. Thanks for using AnyComment <3.
> Please give us short review if you like it.

**Fixes:**

* Fixed division by 0 issue, which caused comments not to load, #101
* Fixed some style conflict issues
* Other small fixes & improvements

= 0.0.47 – 23.08.2018 =

**Fixes:**

* Fixed issue when selecting both types did not allow guests to leave a comment

= 0.0.46 – 23.08.2018 =

**Enhancements:**

* Major elements, such as textarea, buttons are now more unified, #90
* Leave comment as guest, via social or both. Ability to define this from admin, #94
* New comment form layout for guest users, social icons, #94
* When guest user entered name, email and/or website, it will be remembered - no need to type every time
* Added warning about [Clearfy](https://wordpress.org/plugins/clearfy/) (only when activated) in the dashboard as some users reported to have problems with it, #95


**Fixes:**

* Fix for missing Gravatar images in the comment section by guest users & now a bit faster on repeating gravatars, #92
* Added FAQ entry about how to fix problem when unable to delete comment (lack of `DELETE` as request option)
* Comment text is now stored safely even when you close tab or switch tabs, so you can continue typing it
* Added user's website to the comment when submitted as guest, #93

= 0.0.45 – 21.08.2018 =

> *IMPORTANT NOTE 1:* Please if you find any bugs report on the [support forum](https://wordpress.org/support/plugin/anycomment) or the [issue tracker](https://github.com/bologer/anycomment.io/issues)


> *IMPORTANT NOTE 2:* this plugin update includes email sending features, which might require SMTP configuration.
> We recommend to install [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/) and follow on the instruction below:
> [English guide on SendPulse example](https://anycomment.io/en/smtp-sendpulse/)
> [Инструкция на русском на примере SendPulse](https://anycomment.io/smtp-sendpulse/)

**Enhancements:**

* Added Instagram, Dribble and Twitch as authorization option, #72
* Alert shown when new comment was added. Comment list will be automatically refreshed once clicked on alert, #63
* Added option to enabled/disable alert notification about new comment, #63
* Now social media avatar shown globally in admin (e.g. in `dashboard`, `user.php`, `comment.php`, etc), #61
* Better layout for plugin news in admin, `New` label is shown for articles which are not older then 2 weeks, #62
* Added caching for news in dashboard (no need to load them every time) and limited to 3
* New design for setting up social networks, now tabbed, #64
* Added guides English & Russian guides for Vkontakte, Facebook, Twitter, Google, GitHub, Odnoklassniki, Instagram, Twitch, Dribbble to help you with configurations, #66
* Added base plugin shortcode - `[anycomment]` to displays comment box, #67
* Now links, images or videos (e.g. YouTube, Rutube) displayed as attachments under comment text, #69
* Long comment text will be limited in height, by clicking on text will allow to expand it, #73
* Adding new comment is now 2x faster, ~500ms
* Loading comments is now 2x faster and there is no more iframe, therefore comments loaded directly
* Plugin is now sending email notification about new reply to the comment, #71
* Clicking on the "Reply" button in the email, will redirect user directly to the reply in the comments section, #81
* Removed iframe, now comments rendered directly on the page = comments can be searched by crawlers = better SEO, #80
* Option to define interval to check for new comments, #82
* Option to define list of comma-separated words. If one of them match comment text, it will be marked for moderation, #86
* Comment text field is now expanding automatically when you start typing new comment/edit existing/replying to someone
* Option to notify administrator by email about new comment, #77

**Fixes:**

* When user did not have social profile URL it lead to clickable name but incorrect URL, #60
* Do not load styles & scrips globally, only in plugin pages
* Plugin icon in admin sidebar was not displaying correctly and was overflowing when menu was opened
* Newlines in comment are now displaying correctly. Previously everything was as a single line
* Fixed issue when limit of number of comments per page was ignored and maximum number of comments displayed
* Fixed overlapping sidebar news in admin on screens smaller then 1000px

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
