<?php
if (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) == "modules.lng.php") die('Error: Hacking Attempt');
$lang = array();
$lang['birthdaybox_module'] = 'Today\'s Birthdays';
$lang['last_posts_info_reply'] = 'This topic contains more than {$num} replies. Click <a href="showtopic.php?id={@info->id} "target="_blank">here</a> in order to read the whole topic.';
$lang['last_posts_reply'] = 'The last {$num} posts in this topic';
$lang['last_x_forumposts'] = 'Last {$topicnum} active topics';
$lang['legend_cat_hidden'] = 'Forum is closed.';
$lang['legend_cat_new_post'] = 'Forum contains new posts.';
$lang['legend_cat_old_post'] = 'Forum contains no new posts.';
$lang['legend_cat_re'] = 'Forwarding onto another web page.';
$lang['legend_pm_new'] = 'New Private Message';
$lang['legend_pm_old'] = 'Old Private Message';
$lang['legend_title'] = 'Legend';
$lang['legend_topic_new_closed'] = 'Topic is closed - New Posts';
$lang['legend_topic_new_post'] = 'New Posts';
$lang['legend_topic_old_closed'] = 'Topic is closed - No new Posts';
$lang['legend_topic_old_post'] = 'No new Posts';
$lang['mymenu'] = 'Personal menu';
$lang['mymenu_newpm_1'] = 'You have';
$lang['mymenu_newpm_2'] = 'new PM!';
$lang['mymenu_send'] = 'Log in';
$lang['new_pms'] = 'New Private Message(s)';
$lang['new_pms_since_last_visit'] = 'Since your last visit, you have {%my->cnpms} new Private Message(s):';
$lang['related_no_results'] = 'No related topics found.';
$lang['related_topics'] = 'Related Topics';
$lang['x_comments'] = 'Comments ({@row->posts})';
?>