=== Spam Free WordPress ===
Contributors: toddlahman
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SFVH6PCCC6TLG
Tags: spam, antispam, anti-spam, comments, comment, comment spam, rbl, remote proxy, blacklist, blocklist, spam free wordpress, Akismet, WP-SpamFree, Mollom, AVH First Defense, CAPTCHA, Defensio, block spam, spam free, Growmap, spambot, bot, NoSpamNX, Spammer Blocker, recaptcha, Bad Behavior, Antispam Bee, Block Spam By Math Reloaded, block spam, Sabre, W3 Total Cache, WP Super Cache, pingbacks, trackbacks, security, SI CAPTCHA Anti-Spam, comment love, comment luv, commentlove, commentluv
Tested up to: 3.6
Stable tag: 1.6.2
Requires at least: 3.0

Todd Lahman's comment spam blocking plugin that blocks 100% of the automated spam with zero false positives.

== Description ==

Update 3-26-2012: Support requests on the WordPress.org forum will no longer be answered. All requests for support should be made at the [Spam Free WordPress](http://www.toddlahman.com/spam-free-wordpress/) homepage.

Spam Free WordPress is a comment spam blocking plugin that blocks 100% of the automated spam with zero false positives. There is no other plugin, or service, available for WordPress that can claim 100% accuracy with zero false positives, not even Akismet. Manual spam is blocked with an IP address blocklist.

This plugin was born out of necessity in September of 2007 when [HollywoodGrind](http://www.hollywoodgrind.com/) was getting a lot a traffic, and with it a lot of spam that multiple plugins could not stop, but instead increased the load on the server fighting the spam. Since its birth, Spam Free WordPress has been tested successfully under real world heavy traffic, and heavy comment spam, conditions. Once Spam Free WordPress is installed, no other comment spam plugins are needed, and it is recommended that all other plugins be disabled since they will cause undesirable false positives.

It is my goal for Spam Free WordPress to help WordPress become the world's first and only comment spam free blogging platform.

See the [Spam Free WordPress](http://www.toddlahman.com/spam-free-wordpress/) homepage for updates, and to read comments related to the plugin.

= Spam Free WordPress Features =

1. Automatically blocks 100% of automated comment spam
2. Local manual spam and ban policy set with local IP address blocklist
3. Global manual spam and ban policy set with remote IP address blocklist
4. Significantly reduces database load compared to other spam plugins
5. Zero false positives
6. Option to strip HTML from comments
7. No CAPTCHA, cookies, or Javascript needed
8. Saves time and money by eliminating the need to empty the comment spam folder
9. Over 100,000 spam free blogs and counting.

= Automatically Blocks Automated Comment Spam =

Spam Free WordPress uses anonymous password authentication to block 100% of all comment spam with zero false positives. Either the password is submitted with the comment form, or it's spam. Each post is a assigned a password. The password is generated only after it is visited for the first time, and the password only changes when a comment is left. The password is only generated and changed when necessary to eliminate unnecessary load on the database. The reader leaving a comment copies and pastes the password into the comment field to authenticate while remaining anonymous, thus eliminating the need to login to an different account on each blog. Logged in readers will not be required to use the comment form password.

CAPTCHA is not used because it is hard to read, unnecessary, easily cracked, and reduces the number of real comments substantially. There is an interesting article about CAPTCHA here.

Automated spam bots use the wp-comments-post.php core WordPress file to submit comment spam even if the comment form doesn't exist like when DISQUS is used to handle comments. Spam Free WordPress hooks into wp-comments-post.php to block automated spam by requiring the same password authentication used on the comment form. Spam Free WordPress eliminates the spam DISQUS users continue to experience.

= Local and Remote Blocklist =

Spam Free WordPress uses an IP address blocklist to block comment spam that is manually submitted by a real person. The blocklist can also be used to ban readers that leave offensive comments. The local blocklist is stored in the database, so it can be used to set policy for a local blog. The remote blocklist allows a global policy to be set for many blogs that remotely access a file that contains the IP address list.

If someone has their IP address listed in the blocklist that person can still read the blog, but will not be able to leave a comment. This approach is used for several reasons. Spam bots may spoof an IP address, or another person may have been using the IP address when they were banned. No one owns an IP address for life, so the IP address is blocked from leaving comments, but not from reading the blog.

= Reduces Database Load =

As mentioned above, the password is set and changed only when necessary to reduce load on the database. Other plugins filter comments in an effort to determine if they are spam.

Since it is not possible for any filter to ever identify spam accurately, their success at blocking spam is marginal. Those other plugins allow spam to be written to the database most of the time, and stored in the comment spam queue, where the blogger must manually delete the spam. Akismet will prevent some comments it believes is spam from being written to the database, and that results in complaints at times when people realize it was a real person commenting.

Spam Free WordPress knows if comments are real or not, because a password must be entered into the form manually. Anything that is submitted without the password is considered spam. Unlike a filter approach that has many variables, password authentication is 100% accurate, since the password is submitted or not.

Comments that are blocked are never written to the database, which eliminates all the load on the database that spam creates, and other plugins allow.

= Option to Strip HTML from Comments =

It is very common for manual and automated comment spam to include a URL that links to a web site. Spam Free WordPress has an optional feature that will automatically strip out HTML from comments so that links will show up as plain text, and will then also remove the allowed HTML tags from below the comment text box.

= Cached Pages Will Work =

Comment form passwords will properly refresh on cached pages, provided the cache program is set to refresh the page on changes to the page, or if a comment has been submitted. Spam Free WordPress has been tested with WP Super Cache, Batcahe, W3 Total Cache using APC, Memcache, and Xcache, and with the super fast Nginx web server using its core NCache module, and PHP served with PHP-FPM, with Apache serving PHP, and with other caching programs, all of which worked properly.

= Cookies and Javascript Not Required =

Readers do not need to accept cookies or to have Javascript enabled for Spam Free Wordpress to work.


== Installation ==

Update 3-26-2012: Support requests on the WordPress.org forum will no longer be answered. All requests for support should be made at the [Spam Free WordPress](http://www.toddlahman.com/spam-free-wordpress/) homepage.

= Proper Installation Example =

If Spam Free Wordpress is installed correctly there will be a "Password:" field on the comment form. An example can be viewed using the Screenshots tab above, or for a live example visit [Spam Free Wordpress](http://www.toddlahman.com/spam-free-wordpress/).

To see the password field you must be logged out of your WordPress blog account.

*NOTE: Clear the blog cache, like [WP Super Cache](http://wordpress.org/extend/plugins/wp-super-cache/), after installation.*

= WordPress 3.0 and Above =

1. Upload to the /wp-content/plugins directory
2. Activate
3. If there is a password field on the comment form when you are logged out as Admin, then nothing else needs to be done. Otherwise...
4. If there is no password field on the comment form, then replace the form code with `<?php comment_form(); ?>`.
5. If you need help editing your comments.php file I will help you on the [Spam Free Wordpress page](http://www.toddlahman.com/spam-free-wordpress/).

= Thesis Theme =

1. Go to Thesis -> Custom File Editor, choose custom_functions.php, then click Edit selected file. Add the following line of code to that file: `add_action('thesis_hook_comment_field', 'tl_spam_free_wordpress_comments_form');`
2. Save changes.


== Frequently Asked Questions ==

Update 3-26-2012: Support requests on the WordPress.org forum will no longer be answered. All requests for support should be made at the [Spam Free WordPress](http://www.toddlahman.com/spam-free-wordpress/) homepage.

= Is Spam Free Wordpress compatible with other comment spam plugins? =

Yes, however, other comment spam plugins will cause false positives, so it is best to disable all of them, including Akismet.

= Will the password update on cached post pages? =

Yes.

= Does this plugin work on WordPress Multisite? =

Yes.

= Why can't I see the password field on the comment form? =

Log out and clear you browser, and blog cache. If there is still no password field then follow the installation instructions to edit your comments.php file, or ask me to do it for you.

= Do readers need to accept cookies or to have Javascript enabled? =

No. Spam Free Wordpress uses anonymous password authentication the reader types into the comment form, which does not require cookies or Javascript.

= Will having a password requirement stop readers from commenting? =

No. If you've ever logged into a blog or forum, like Wordpress.org, with a username and password to leave a comment then you'll know security doesn't stop someone from leaving a comment. Spam Free Wordpress eliminates the need for an account on the blog, and instead uses anonymous password authentication. Readers expect some security on the Internet, and all are aware that spam is a problem.

== Screenshots ==

1. If Spam Free Wordpress is installed correctly there will be a Password field on the comment form. Each time a reader leaves a comment they type in that password, or copy and paste it, to leave a comment.

2. Spam Free Wordpress in Action

== Upgrade Notice ==

= 1.6.2 =

Upgrade immediately to keep your blog comment spam free.

== Changelog ==

= 1.6.2 =

* Arrrg. Forgot to bump the version.

= 1.6.1 =

* Upgrade procedure didn't work as expected first time

= 1.6 =

* Added disable pingback/trackback
* Added disable user registration
* Added settings link to plugin row menu
* Added remove url form field
* Added remove comment author clickable link
* Admin page has a new look
* Fixed donation link
* Added uninstall to cleanup database on deletion
* Made invalid or empty password error more clear
* Added automatic theme support for popular themes
* Minor code changes

= 1.5.1 =

* Fix for number_format error
* Ability to turn blocklists on or off
* Easier Admin page
* Backend improvements

= 1.5.0 =

* Number localization fix.

= 1.4.9 =

* Added global remote blocklist after code rewrite
* Added more plugin security features
* Added plugin to close pingbacks and trackbacks
* Cleaned up code and admin page a bit

= 1.4.8 =

* Initial release. In development since September 2007.
* Global remote blocklist left out pending code rewrite.