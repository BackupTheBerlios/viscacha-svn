<?php
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "modules.lng.php") die('Error: Hacking Attempt');
$lang = array();
$lang['birthdaybox_module'] = 'We congratulate...';
$lang['last_posts_info_reply'] = 'This topic contains more than {$num} replies. Click <a href="showtopic.php?id={$tid}" target="_blank">here</a>, to read the whole topic.';
$lang['last_posts_reply'] = 'The last {$num} postings in this topic:';
$lang['last_x_forumposts'] = 'Last {$topicnum} active topics';
$lang['legend_cat_hidden'] = 'Board is closed.';
$lang['legend_cat_new_post'] = 'Board has new postings.';
$lang['legend_cat_old_post'] = 'Board has no new postings.';
$lang['legend_cat_re'] = 'Redirect to a URL.';
$lang['legend_pm_new'] = 'New messages';
$lang['legend_pm_old'] = 'Old messages';
$lang['legend_title'] = 'Legend';
$lang['legend_topic_new_closed'] = 'Topic closed - New postings';
$lang['legend_topic_new_post'] = 'New postings';
$lang['legend_topic_old_closed'] = 'Topic closed - No new postings';
$lang['legend_topic_old_post'] = 'No new postings';
$lang['mymenu'] = 'Personal Menu';
$lang['mymenu_newpm_1'] = 'You have';
$lang['mymenu_newpm_2'] = 'new PM!';
$lang['mymenu_send'] = 'Log in';
$lang['new_pms'] = 'New Private Message(s)';
$lang['new_pms_since_last_visit'] = 'You have {%my->cnpms} new private message(s) since your last visit:';
$lang['related_no_results'] = 'No related topics found';
$lang['related_topics'] = 'Related Topics';
$lang['x_comments'] = 'Comments ({$posts})';
?>