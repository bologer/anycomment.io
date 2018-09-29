# Changelog

## 0.0.58 – 26.09.2018

**Enhancements:**

* Added left side highlight of a comment when clicked on "replied to" link and when user comes from email, #170
* Enhanced editor, now possible to use : bold, italics, underscore, strike through, quote, order/unordered list, link and embed image (all customizable), #47

**Fixes:**

* Fixed issue with translations on number of comments in the header in Russian language, #178
* Fixed issue when some websites had broken CSS styles after activating plugin, #177
* Now when comments is deleted, trashed/untrashed, marked as spam, status changed, its cache will be dropped and it will display on frontend accordingly, #162
* Fixed issue when guest users were not able to submit uploaded documents, #175
* Fixed issue when "Login with:" was displayed even thought none of the socials were enabled, #166

## 0.0.57 – 25.09.2018

**Enhancements:**

* Added ability to disable WordPress from login options in "Socials" -> "WordPress", #154

* [Gallery] Ability to preview image in the gallery (can use LEFT-RIGHT arrow keys to iterate through images, ESC to close gallery), #147
* [Gallery] After image or file is uploaded, they will be added as small block below the comment box, #147
* [Gallery] Images are now handled smarter: original source is kept as it is and small thumbnail is cropped from original source as a preview, #147
* [Gallery] Ability to delete file when adding/updating comment (file will be erased from DB and filesystem), #147

* Removed ability to choose predefined themes and no more support for dark theme in favor of customizer. However, white theme is kept as the default one, #155

> The support of dark theme was a bit pain in the ass. So we sat and thought it would be better if give control over the theme to you.
> Give ability to drag & drop some of the elements, change colors, sized, etc. However it will come a bit further, for now a few new design options will be added.

* Added option to premoderate comments with links, #84
* Improved comments caching, they should be working even faster now, #151

**Fixes:**

* Added missing Russian translation for "Sorting"
* Fixed situation when one user with two social networks and same email address was always logged in with the first recorded social


## 0.0.56 – 17.09.2018

**Enhancements:**

* Comments are now nested up to 3 levels, any further replies will be added without further nesting. Two benefits: easier to maintain mobile view + easier to follow conversation
* Child comments are now having "reply to {name}" where {name} is the name of the person to whom reply is made
* Comments are now cached. This will help to limit number of requests to the database, load comments faster & help people who have limited resource environments
* From now on, plugin will crop original avatar from social into smaller version, which will increase loading speed of comment and take less disc space (existing avatar will be enhanced automatically for you), #149
* Converted sorting dropdown to multi dropdown. Now it has sorting option & logout link. When user is guest, it only has sorting options, #145
* When user registered via default WordPress form, and the same user is trying to authenticate using via social (using same email), he will see error message that he needs to use regular login form in this case, #143
* Integration tab now has option to add reCAPTCHA to comment form (for all, guests or authenticated users only, choose theme, etc), #146
* Added guides in Russian & English on how to set-up reCAPTCHA, #146
* Added ability to change border radius of avatars, #148
* Added WordPress icon as authorization option in social list, #131
* Added ability to choose default avatar (when user does not have any avatar). Currently possible to choose default from AnyComment or ones available from [Gravatar](https://en.gravatar.com/site/implement/images/), #138
* Small cosmetic style changes

**Fixes:**

* Added Russian translations for default sorting function
* Fixed list-style issue on some websites
* Logout link does not ask extra confirmation
* Sorting dropdown will close when clicked outside the element. Previously it was always open

## 0.0.55 – 10.09.2018

**Enhancements:**

* Added a Jetpack, Disqus, Disable Comments and a few other plugin to the list of possible problems, #134
* Improved mobile layout, #106
* Improved speed of theming, now x1.5 faster to generate custom styles
* Added logout link to logged in client, #133
* Added ability to customize generated notification email (only for  admin and reply for now), #97
* Added "Shortcodes" tab. It will have list of available shortcodes, #139
* Added a helper notice to admins & moderators about closed comments per post, globally or if post is password protected (comments did not show in this case), #142
* Added ability to rearrange guest form fields or remove unwanted, #125
* Added ability to define default sorting (ascending or descending order), #85

**Fixes:**

* Fixed notice message in admin, #132
* Fixed issue when "load on scroll" option was active and comments did not load on short pages because it was not possible to scroll, #135
* Fixed issue when custom styles were ignored as dark theme was selected
* JavaScript & Css assets are not loaded on the page, when comments are disabled or post is password protected
* Fixed issue when user was trying to login using the social network with same email as one of the existing account. It caused no problem, but redirect to page and user was not logged in, #29


## 0.0.54 – 06.09.2018

**Enhancements:**

* Small improvements in the documentation on how to set-up certain social network

**Fixes:**

* Small fixed for cached sidebar news. Added dependency on the website locale
* Fixed issue when some users were unable to authorize using Google caused 500 error, #127
* Fixed issue on comment delete, no more need to add DELETE option in Apache or Nginx
* Fixed typo in Russian translation message when trying to delete a comment
* Fixed issue when comments displayed on the single page could go over the page content as invisible element, #129
* Fixed issue with file input icon (was displaying as black square instead), #128
* Fixed issue when send comment button was too close to the "accept privacy policy" checkbox

## 0.0.53 – 04.09.2018

**Fixes:**

* Small fixes to translations regarding options to show/hide user URL
* Fix for main plugin shortcode, now use `[anycomment include="true"]` to include comments on custom place (reported by Ivan)

## 0.0.52 – 03.09.2018

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

## 0.0.51 – 28.08.2018

**Enhancements:**

**Fixes:**

* Fixed issue when reply user dialog was in dark color in dark theme (invisible), #114
* Fixed issue when guest inputs (name, email, website) were white in dark theme

## 0.0.50 – 27.08.2018

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

## 0.0.49 – 24.08.2018

* Cosmetic fixes of form: avatar was not shown for logged in user & button was not properly aligned


## 0.0.48 – 23.08.2018

> I will try to deliver more fixes and features over next release. Thanks for using AnyComment <3.
> Please give us short review if you like it.

**Fixes:**

* Fixed division by 0 issue, which caused comments not to load, #101
* Fixed some style conflict issues
* Other small fixes & improvements

## 0.0.47 – 23.08.2018

**Fixes:**

* Fixed issue when selecting both types did not allow guests to leave a comment

## 0.0.46 – 23.08.2018

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

## 0.0.45 – 21.08.2018

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

## 0.0.41 – 29.07.2018

* Fix issue when User Agreement checkbox was not shown

## 0.0.40 – 29.07.2018

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

## 0.0.35 – 20.07.2018
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

## 0.0.33 – 16.07.2018
* Fixed problem with array syntax support on PHP version 5.5, #49
* Fixed possible XSS in the comment

## 0.0.32 – 10.07.2018
* introducing comment likes, #35
* minified CSS, to save some loading time
* ability to define default user role on creation (registration via plugin), #37
* when user has non-default Gravatar, use it, otherwise use default from plugin, #10
* proper integration with WP User Avatar & Akismet
* load commnets on scroll (new options to load comments when user scroll to it), #36
* and other small bug fixes & improvements

## 0.0.2 – 01.07.2018
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

## 0.0.1 - 24.06.2018
* First Release
* Options to specify API details (secrets, etc) for social authorization: Vk, Twitter, Facebook, Google
* Integrated with [WP User Avatar](https://wordpress.org/plugins/wp-user-avatar/)
* Authorize via VK, Twitter, Facebook, Google
* date when comment is left is based on website's language. List of supported languages can be seen [here](https://github.com/hustcc/timeago.js/tree/master/src/lang)
* comment count at the top updated automatically when new comment added
* add comments with AJAX, no need to refresh the page
* ability to reply to nested comments up to 2 levels
* when all socials disabled, libraries not loaded and they are not shown to end user