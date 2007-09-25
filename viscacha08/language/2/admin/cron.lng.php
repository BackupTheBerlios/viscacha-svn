<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }
$lang = array();

$lang['admin_button_add'] = 'Add';
$lang['admin_button_send'] = 'Send';

$lang['admin_err_no_file_specified'] = 'No file specified. Either upload a file or specify a file in the select-box.';
$lang['admin_err_no_title_specified'] = 'You have not specified a title.';
$lang['admin_err_upload_failed'] = 'File could not be uploaded.';
$lang['admin_err_entry_already_exists'] = 'This entry already exists.';

$lang['admin_job_successfully_added'] = 'Cron Job successfully added';

$lang['admin_add_a_new_task'] = 'Add a new task';
$lang['admin_cron_status'] = 'Status: ';
$lang['admin_status_enabled'] = 'Simulated Cron Jobs enabled';
$lang['admin_status_disabled'] = 'Simulated Cron Jobs disabled';
$lang['admin_status_enabled_info'] = 'Because Cron Jobs are often not availible, Viscacha can simulate Cron Jobs. This works as follows: On every page call, it will be checked if there should have been a Cron Job executed. If the time limit of a Cron Job is exceeded, it will be executed in the background.';
$lang['admin_status_disabled_info'] = 'Cron Jobs are not simuleted by Viscacha. You have to set up a Cron Job that starts the installed Cron Jobs automatically.';
$lang['admin_specify_a_file'] = 'Specify a file and enter a title';
$lang['admin_title_description'] = 'Title / Description:';
$lang['admin_enter_a_filename'] = '<em>Either</em> enter a filename:';
$lang['admin_enter_a_filename'] = 'Specify a file in the directory "classes/cron/jobs/".';
$lang['admin_option_choose_a_file'] = '-- Please choose a file --';
$lang['admin_upload_a_file'] = '<em>or</em> upload a file:';
$lang['admin_upload_a_file2'] = 'Allowed file types: .php<br />Maximum file size: 100 KB';
$lang['admin_time_to_execute'] = 'Time to execute';
$lang['admin_minute'] = 'Minute:';
$lang['admin_every_minute'] = 'Every Minute (*)';
$lang['admin_every_5_minutes'] = 'Every Five Minutes (*/5)';
$lang['admin_every_10_minutes'] = 'Every Ten Minutes (*/10)';
$lang['admin_every_15_minutes'] = 'Every Fifteen Minutes (*/15)';
$lang['admin_every_30_minutes'] = 'Every Thirty Minutes (*/30)';
$lang['admin_hour'] = 'Hour:';
$lang['admin_every_hour'] = 'Every Hour (*)';
$lang['admin_every_2_hours'] = 'Every Two Hours (*/2)';
$lang['admin_every_3_hours'] = 'Every Three Hours (*/3)';
$lang['admin_every_4_hours'] = 'Every Four Hours (*/4)';
$lang['admin_every_6_hours'] = 'Every Six Hours (*/6)';
$lang['admin_every_12_hours'] = 'Every Twelve Hours (*/12)';
$lang['admin_day'] = 'Day:';
$lang['admin_every_day'] = 'Every Day (*)';
$lang['admin_every_2_days'] = 'Every Two Days (*/2)';
$lang['admin_every_14_days'] = 'Every Fourteen Days (*/14)';
$lang['admin_weekday'] = 'Weekday:';
$lang['admin_every_weekday'] = 'Every Weekday (*)';
$lang['admin_month'] = 'Month:';
$lang['admin_every_month'] = 'Every Month (*)';
$lang['admin_th_delete'] = 'Delete';
$lang['admin_th_file'] = 'File';
$lang['admin_th_minutes'] = 'Minute(s)';
$lang['admin_th_hours'] = 'Hour(s)';
$lang['admin_th_days'] = 'Day(s)';
$lang['admin_th_month'] = 'Month';
$lang['admin_th_weekday'] = 'Weekday';
$lang['admin_cron_jobs_deleted'] = '{$anz} cron jobs deleted.';
$lang['admin_add_new_task'] = 'Add new Task';
$lang['admin_tasks_logfile'] = 'Tasks Log File';
$lang['admin_manage_tasks'] = 'Manage Tasks';
$lang['admin_cron_change'] = 'Change';
?>
