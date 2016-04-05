=== Auto-Schedule Posts ===
Contributors: davidjmillerorg
Tags: posts, scheduling, multi-author, auto-schedule
Requires at least: 2.3
Tested up to: 3.3
Stable tag: trunk

Auto-Schedule Posts allows users to separate their writing schedule from their publishing schedule - write when you want and have posts publish at the time you think best. This is also useful for multi-author sites because it can be used to prevent different authors from publishing too close together preventing one article from spending any time as with top placement because another was posted immediately afterward.

== Description ==
Auto-Schedule posts catches posts as they are published and holding them until the previously set criteria are met for the proper publication time.

You can set publication between certain hours, limit publication to certain days, and specify a minimum time period between posts.

== Installation ==

To install it simply unzip the file linked above and save it in your plugins directory under wp-content. In the plugin manager activate the plugin. Settings for the plugin may be altered under the Auto-Schedule Posts page of the Options menu (version 2.3) or Settings menu (version 2.5 or later).

== Frequently Asked Questions ==

= Can I publish overnight? =

You can set a daily start time that is later than the daily end time to have posts publish overnight while still limiting when posts are published.

= Why limit what times I publish? =

It all depends on the purpose for your writing. Some people would want to publish at any time, but if you want to set criteria this allows it. If your target is a business audience you might want to publish during business hours (or days) - if you have a thought outside of business hours it will be held until the next business hours. If you are publishing for late night gamers you might want to publish after regular business hours and late into the early morning.

= How can this help with multiple authors? =

There are two ways it can help - one, different authors cannot accidentally publish over top of each other; two, you can ensure that one author does not dominate all the others by selecting posts from the least recently published author that has a post ready.

= How does randomized publishing work? =

Selecting the option to randomize makes it so that posts will not publish every time it would be time to publish. Instead it publishes whatever percentage of the time you specify in the posting probability field while still honoring the settings for when to publish and th eminimum interval between posting. For example, if you wished to post 4 times per day during an 8 hour window of time you could set the publishing window between 9AM and 5PM then set the interval to 60 and the probablity to 50. This would guarantee that posts are at least 60 minutes apart with a 50% probability that it would post within each 60 minute span after the 60 minute window expired on the previous post. You could also set the interval to 30 and the probablity to 25. This would guarantee that posts are at least 30 minutes apart with a 25% probability that it would post within each 30 minute span after the 30 minute window expired on the previous post.

== Screenshots ==

1. This is a sample options page displayed in Wordpress 2.8

== Changelog ==

= 3.6 =
* The custom post status is registered with Wordpress so that posts queued for automatic publishing can be seen and edited in the Wordpress Posts admin area.
* This version updates the deactivation function so that scheduled posts functionally become orphans. Previously deactivation would result in scheduled posts staying in the database but with a post status that would not show up anywhere in the Wordpress Admin interface. The new deactivation function changes the status of scheduled posts to "draft" so that they can be handled as the author sees fit after the plugin has been deactivated.
* New function to "Publish All" on the settings page - each queued post is published.
* New function to "Delete This" on the settings page. Individual posts may be deleted without having to go to the post administration page.
* This has been tested in Wordpress 3.5.1
= 3.5 =
* This version adds compatibility for network activation on multisite installs of Wordpress. Special thanks to Franck for finding the problem and helping me test the fix. It also removes an unnecessary restriction where the interval was not allowed to be more than a single day - I decided to leave a restrictions of 100 days. This has been tested in Wordpress 3.2.1
= 3.0 =
* This version adds a feature that I had long hoped to offer - the ability to publish posts at random intervals with some control about how many posts are published in a day. Publication still takes place within the specified publication window but it only publishes a specified percentage of the time within that window. I have also added options to publish a random post rather than the next post in the queue or a random post by the least recent author.
= 2.3 =
* Special thanks to Chris Bell for finally tracking down the root cause of the timezone bug and providing a fix. This version integrates that fix. This has been tested in Wordpress 3.2.1
= 2.2 =
* There were some bugs in version 2.1 that interfered with the publishing operations. There was also a bug in the force publish code that posted the wrong timestamp on the posts. This version resolves those issues. This has been tested in Wordpress 3.2.1
= 2.1 =
* As of Wordpress 3.0 the publishing function was misinterpreting the system time and failing to publish or delay publication based on user settings. This version resolves those issues.
= 2.0 =
* Instead of setting posts to the distant future the plugin now sets them to a new status - "auto-schedule" This removes them from the display of posts to edit. To compensate I have added a feature on the options page to edit posts waiting to be published as well as an option to force publication of a particular post without regard to the settings of the plugin. As a result of the bug in WP 2.9 with internal cron jobs I also added a button to the options page that will manually publish a post in the event that automatic publishing is not happening (I hope that Wordpress does not have the problem again, but I noticed a similar feature on another plugin that proved handy before the 2.9.1 upgrade so I am leaving the feature in this plugin as well.
= 1.0 =
* After using this for quite some time I discovered one wrinkle to iron out - in version 0.9 the last published post time was not handled correctly so when updating options new posts could be published before the scheduled time. Now updating options will not open the gate for an extra post to be published.
= 0.9 =
* A virtually beta release - the posts are caught and scheduled, but I would like to add a feature such as only scheduling on the hour (as opposed to at least an hour apart).