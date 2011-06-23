=== Spam Free WordPress ===
Contributors: toddlahman
Donate link: http://www.toddlahman.com/spam-free-wordpress/
Tags: spam, antispam, anti-spam, comments, comment, comment spam, rbl, remote proxy, blacklist, blocklist, spam free wordpress
Tested up to: 3.1.3
Stable tag: 1.3.9
Requires at least: 2.8

Todd Lahman's comment spam blocking plugin that blocks 100% of the automated spam with zero false positives.

== Description ==

Spam Free WordPress is a comment spam blocking plugin that blocks 100% of the automated spam with zero false positives. There is no other plugin, or service, available for WordPress that can claim 100% accuracy with zero false positives, not even Akismet.

This plugin was born out of necessity in September of 2007 when [HollywoodGrind](http://www.hollywoodgrind.com/) was getting a lot a traffic, and with it a lot of spam that multiple plugins could not stop, but instead increased the load on the server fighting the spam. Since its birth, Spam Free WordPress has been tested successfully under real world heavy traffic, and heavy comment spam, conditions. Once Spam Free WordPress is installed, no other comment spam plugins are needed, and it is recommended that all other plugins be disabled since they will cause undesirable false positives.

It is my goal for Spam Free WordPress to help WordPress become the world's first and only comment spam free blogging platform.

= Spam Free WordPress Features =

1. Automatically blocks 100% of automated comment spam
2. Local manual spam and ban policy set with local IP address blocklist
3. Global manual spam and ban policy set with remote IP address blocklist
4. Significantly reduces database load compared to other spam plugins
5, Zero false positives
6. Option to strip HTML from comments
7. Saves time and money by eliminating the need to empty the comment spam folder

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

See the [Spam Free WordPress](http://www.toddlahman.com/spam-free-wordpress/) homepage for updates, and to read comments related to the plugin.


== Installation ==

= Proper Installation Example =

If Spam Free Wordpress is installed correctly there will be a "Password:" field on the comment form. Each time a reader leaves a comment they type in that password, or copy and paste it, to leave a comment. An example can be viewed using the Screenshots tab above, or for a live example visit [HollywoodGrind](http://www.hollywoodgrind.com/).

Need help? Then visit [HollywoodGrind](http://wordpress.org/tags/spam-free-wordpress).

= Wordpress 3.0 and up - Using the comment_form function =

If you're running Wordpress 3.0 and up, and ARE using the [comment_form()](http://codex.wordpress.org/Function_Reference/comment_form) function to output (create) the comment form for use within a theme template in the comments.php file

1. Upload to the /wp-content/plugins directory
2. Activate
3. You're done

= Wordpress 3.0 and up - NOT using the comment_form function =

If you're running Wordpress 3.0 and up, but are NOT using the [comment_form()](http://codex.wordpress.org/Function_Reference/comment_form) function to output (create) the comment form for use within a theme template in the comments.php file, then when you activate the plugin, you will not see the password field in the comment form. This means the comment_form() function is not outputting the comment form on the post page, so you will need to follow step 3 below.

1. Upload to the /wp-content/plugins directory
2. Activate
3. Copy and paste the following line into your comments.php file right after the last form field for either the email address or the URL (web site).

`<?php if(function_exists ('tl_spam_free_wordpress_comments_form')) { tl_spam_free_wordpress_comments_form(); } ?>`

4. You're done

= Wordpress 2.8 or 2.9 = 

If you're running Wordpress 2.8 or 2.9.

1. Upload to the /wp-content/plugins directory
2. Activate
3. Copy and paste the following line into your comments.php (comes with your theme files) file right after the last form field for either the email address or the URL (web site).

`<?php if(function_exists ('tl_spam_free_wordpress_comments_form')) { tl_spam_free_wordpress_comments_form(); } ?>`

4. You're done

= Thesis Theme =

1. Go to Thesis -> Custom File Editor, choose custom_functions.php, then click Edit selected file. Add the following line of code to that file.

2. `add_action('thesis_hook_comment_field', 'tl_spam_free_wordpress_comments_form');`

3. Save changes.


== Frequently Asked Questions ==

= Is Spam Free Wordpress compatible with other comment spam plugins? =

Yes, however, other comment spam plugins will cause false positives, so it is best to disable all of them, including Akismet.

= Will the password update on cached post pages? =

This has been tested on many platforms, and in many different caching scenarios. So far every configuration tried has been successful. If a new comment will cause the page to be refreshed, then the password will be refreshed.

= Does this plugin work in Multi-User mode? =

Yes.

== Screenshots ==

1. If Spam Free Wordpress is installed correctly there will be a Password field on the comment form. Each time a reader leaves a comment they type in that password, or copy and paste it, to leave a comment.

2. Spam Free Wordpress has very powerful features that are optional, but most of the work blocking spam is automated upon plugin activation.

== Upgrade Notice ==

= 1.3.9 =

Upgrade immediately to keep your blog comment spam free.

== Changelog ==

= 1.3.9 =

* Added screen shot of comment form password field

= 1.3.8 =

* Added screen shot of comment form password field

= 1.3.7 =

* readme.txt file edit for proper formatting for WordPress SVN

= 1.3.6 =

* Forgot to increment version

= 1.3.5 =

* readme.txt file edit for proper formatting

= 1.3.4 =

* Initial release. In development since September 2007.