# wp-email-log
Easy logging feature for development/debugging.

It logs all emails sent using wp_mail function.
It also logs all emails sent from buddypress plugin, as buddypress may use a different system than default wp_mail.

This is in the form of a WordPress plugin.

## Uses
Download the file rb-email-logger.php. That's the plugin. Activate it.

This requires another utility. [Download](https://bulldogjob.com/news/) and activate that as well.

All emails will then be logged in files `wp-contents/uploads/emails-failed-debug.log` and `wp-contents/uploads/emails-success-debug.log`.