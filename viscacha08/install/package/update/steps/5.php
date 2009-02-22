<div class="bbody">
<?php
echo "<strong>Starting Update:</strong><br />";

require('data/config.inc.php');
require_once('install/classes/class.phpconfig.php');

echo "- Source files loaded<br />";

if (!class_exists('filesystem')) {
	require_once('install/classes/class.filesystem.php');
	$filesystem = new filesystem($config['ftp_server'], $config['ftp_user'], $config['ftp_pw'], $config['ftp_port']);
	$filesystem->set_wd($config['ftp_path'], $config['fpath']);
}
if (!class_exists('DB')) {
	require_once('install/classes/database/'.$config['dbsystem'].'.inc.php');
	$db = new DB($config['host'], $config['dbuser'], $config['dbpw'], $config['database'], $config['dbprefix']);
	$db->setPersistence($config['pconnect']);
}

echo "- FTP class loaded, Database connection started.<br />";

// Hooks
$hooks = array_map('trim', file('admin/data/hooks.txt'));
removeHook($hooks, 'components_');
$hooks[] = '';
$hooks[] = 'admin/packages_admin.php';
$filesystem->file_put_contents('admin/data/hooks.txt', implode("\r\n", $hooks));
echo "- Hooks updated.<br />";

// Config
$c = new manageconfig();
$c->getdata('data/config.inc.php');
$c->updateconfig('version', str, VISCACHA_VERSION);
$c->updateconfig('spider_logvisits', int, 2);
$c->delete('always_send_js');
$c->savedata();
echo "- Configuration updated.<br />";

// Old files
$filesystem->unlink('templates/lang2js.php');
$filesystem->unlink('classes/feedcreator/mbox.inc.php');
$feeds = file_get_contents('data/feedcreator.inc.php');
$feeds = preg_replace('~[\r\n]+MBOX\|mbox\.inc\.php\|MBox\|\d|\d~i', '', $feeds);
$filesystem->file_put_contents('data/feedcreator.inc.php', $feeds);
$dir = dir('language');
while (false !== ($entry = $dir->read())) {
	$path = "{$dir->path}/{$entry}";
	if (is_dir($path) && is_id($entry)) {
		$filesystem->rmdirr("{$path}/modules/");
	}
}
echo "- Old files deleted.<br />";

// Languages
$ini = array (
  'admin/bbcodes' =>
  array (
    'language_de' =>
    array (
      'admin_bbc_replacement_desc' => 'Dies ist der HTML-Code f�r die BB-Code-Ersetzung. Stellen Sie sicher, dass sie \'{param}\' (ohne Anf�hrungszeichen) verwenden um den Text, der zwischen dem �ffnenden und dem schlie�enden BB-Code-Tag steht, einzuf�gen. Wenn Sie dem BB-Code einen Parameter mitgeben (muss vorher aktiviert werden), so f�gen Sie auch \'{option}\' (ohne Anf�hrungszeichen) an der gew�nschten Stelle ein. Zur Absicherung k�nnen Sie den Platzhaltern einen Typ zuweisen. Daf�r h�ngen Sie hinter "param" bzw. "option" ein Doppelpunkt und danach erg�nzen Sie den Typ. Folgende Typen sind m�glich: hexcolor, int, float, hex, simpletext, url, email, alnum, alpha. Beispiel: {param:hexcolor}',
    ),
    'language' =>
    array (
      'admin_bbc_replacement_desc' => 'This is the HTML code for the BB code replacement. Make sure that you include \'{param}\' (without the quotes) to insert the text between the opening and closing BB code tags, and \'{option}\' for the parameter within the BB code tag. You can only use {option} if \'Use Option\' is set to yes. For a better security you can specify a type for each placeholder. To specify the placeholder simply add a colon and the type. The following types can be used: hexcolor, int, float, hex, simpletext, url, email, alnum, alpha. Example: {param:hexcolor}',
    ),
  ),
  'admin/cms' =>
  array (
    'language_de' =>
    array (
      'admin_cms_nav_package' => 'Paket:',
    ),
    'language' =>
    array (
      'admin_cms_nav_package' => 'Package:',
    ),
  ),
  'admin/frames' =>
  array (
    'language_de' =>
    array (
      'admin_component_manager' => NULL,
      'admin_sqlerror_log' => 'Protokoll der Systemfehler',
    ),
    'language' =>
    array (
      'admin_component_manager' => NULL,
      'admin_sqlerror_log' => 'System Error Log',
    ),
  ),
  'admin/language' =>
  array (
    'language_de' =>
    array (
      'admin_lang_component' => NULL,
      'admin_lang_components' => NULL,
      'admin_lang_component_without_langfile' => NULL,
      'admin_lang_edit_langfile_package_id' => NULL,
      'admin_lang_files' => NULL,
      'admin_lang_packages_phrases' => 'Pakete',
    ),
    'language' =>
    array (
      'admin_lang_component' => NULL,
      'admin_lang_components' => NULL,
      'admin_lang_component_without_langfile' => NULL,
      'admin_lang_edit_langfile_package_id' => NULL,
      'admin_lang_files' => NULL,
      'admin_lang_packages_phrases' => 'Packages',
    ),
  ),
  'admin/misc' =>
  array (
    'language_de' =>
    array (
      'admin_misc_dictionary' => NULL,
      'admin_misc_save' => NULL,
    ),
    'language' =>
    array (
      'admin_misc_dictionary' => NULL,
      'admin_misc_save' => NULL,
    ),
  ),
  'admin/packages' =>
  array (
    'language_de' =>
    array (
      'admin_packages_component_is_active' => NULL,
      'admin_packages_component_is_active_but_package_is_not_active' => NULL,
      'admin_packages_component_is_not_active' => NULL,
      'admin_packages_component_manager' => NULL,
      'admin_packages_com_activate' => NULL,
      'admin_packages_com_component_is_required' => NULL,
      'admin_packages_com_deactivate' => NULL,
      'admin_packages_com_delete_do_you_really_want_to_delete_this_component' => NULL,
      'admin_packages_com_delete_head_delete_component' => NULL,
      'admin_packages_com_th_component' => NULL,
      'admin_packages_com_th_package' => NULL,
      'admin_packages_err_no_package_with_this_id' => NULL,
      'admin_packages_err_section_not_found' => NULL,
      'admin_packages_err_specified_component_is_required' => NULL,
      'admin_packages_err_specified_component_not_found' => NULL,
      'admin_packages_err_this_package_is_required_you_cannot_change_the_status' => NULL,
      'admin_packages_info_component' => NULL,
      'admin_packages_info_for_this_package_is_no_component_specified' => NULL,
      'admin_packages_info_required' => NULL,
      'admin_packages_ok_component_successfully_removed' => NULL,
      'admin_packages_plugins_add_file_for_code_text' => NULL,
      'admin_packages_plugins_delete_head_delete_package' => NULL,
      'admin_packages_plugins_edit_add_edit_phrases' => NULL,
      'admin_packages_plugins_edit_file_for_code_text' => NULL,
      'admin_packages_plugins_template_file_for_code_text' => NULL,
    ),
    'language' =>
    array (
      'admin_packages_component_is_active' => NULL,
      'admin_packages_component_is_active_but_package_is_not_active' => NULL,
      'admin_packages_component_is_not_active' => NULL,
      'admin_packages_component_manager' => NULL,
      'admin_packages_com_activate' => NULL,
      'admin_packages_com_component_is_required' => NULL,
      'admin_packages_com_deactivate' => NULL,
      'admin_packages_com_delete_do_you_really_want_to_delete_this_component' => NULL,
      'admin_packages_com_delete_head_delete_component' => NULL,
      'admin_packages_com_th_component' => NULL,
      'admin_packages_com_th_package' => NULL,
      'admin_packages_err_no_package_with_this_id' => NULL,
      'admin_packages_err_section_not_found' => NULL,
      'admin_packages_err_specified_component_is_required' => NULL,
      'admin_packages_err_specified_component_not_found' => NULL,
      'admin_packages_err_this_package_is_required_you_cannot_change_the_status' => NULL,
      'admin_packages_info_component' => NULL,
      'admin_packages_info_for_this_package_is_no_component_specified' => NULL,
      'admin_packages_ok_component_successfully_removed' => NULL,
      'admin_packages_plugins_add_file_for_code_text' => NULL,
      'admin_packages_plugins_delete_head_delete_package' => NULL,
      'admin_packages_plugins_edit_add_edit_phrases' => NULL,
      'admin_packages_plugins_edit_file_for_code_text' => NULL,
      'admin_packages_plugins_template_file_for_code_text' => NULL,
    ),
  ),
  'admin/settings' =>
  array (
    'language_de' =>
    array (
      'admin_ignor_words_less_chackters' => NULL,
      'admin_ignor_words_less_chackters_info' => NULL,
      'admin_mode_suggestions' => NULL,
      'admin_save_php_errors' => 'Speichere Fehler (PHP und MySQL) in Protokolldatei:',
      'admin_select_setting_group' => NULL,
      'admin_select_slq_erroe_log' => NULL,
      'admin_show_text_captcha' => NULL,
      'admin_show_text_captcha_info' => NULL,
      'admin_suggestions_fast_mode' => NULL,
      'admin_suggestions_normal_mode' => NULL,
      'admin_suggestions_slow_mode' => NULL,
      'admin_logvisits_count_logging' => 'Nur Anzahl der Besuche protokollieren',
      'admin_logvisits_full_logging' => 'Zeit und Anzahl der Besuche protokollieren',
      'admin_logvisits_no_logging' => 'Keine Protokollierung',
      'admin_select_sys_error_log' => 'Protokoll der Systemfehler',
    ),
    'language' =>
    array (
      'admin_ignor_words_less_chackters' => NULL,
      'admin_ignor_words_less_chackters_info' => NULL,
      'admin_mode_suggestions' => NULL,
      'admin_select_setting_group' => NULL,
      'admin_select_slq_erroe_log' => NULL,
      'admin_show_text_captcha' => NULL,
      'admin_show_text_captcha_info' => NULL,
      'admin_suggestions_fast_mode' => NULL,
      'admin_suggestions_normal_mode' => NULL,
      'admin_suggestions_slow_mode' => NULL,
      'admin_logvisits_count_logging' => 'Log only number of visits',
      'admin_logvisits_full_logging' => 'Log time and number of visits',
      'admin_logvisits_no_logging' => 'No logging',
      'admin_select_sys_error_log' => 'System Error Log',
    ),
  ),
  'admin/slog' =>
  array (
    'language_de' =>
    array (
      'admin_slog_sql_error_logfile' => 'Protokoll der Systemfehler',
      'admin_slog_backtrace' => 'Laufzeitinformationen',
      'admin_slog_error_num' => 'Nr.',
    ),
    'language' =>
    array (
      'admin_slog_sql_error_logfile' => 'System Error Logfile',
      'admin_slog_backtrace' => 'Runtime information',
      'admin_slog_error_num' => 'No.',
    ),
  ),
  'global' =>
  array (
    'language_de' =>
    array (
      'box_newtopic' => NULL,
      'editprofile_signature_longdesc' => NULL,
      'editprofile_standard' => NULL,
      'forum_options_search_reset' => NULL,
      'im_msgtitle' => NULL,
      'index_headline' => NULL,
      'no_board_given' => NULL,
      'pm_index_dir' => NULL,
      'post_sent' => NULL,
      'print_title_page' => NULL,
      'section_closed' => NULL,
      'section_not_available' => NULL,
      'thumb_error' => 'Konnte Miniaturansicht nicht erstellen',
      'x_article' => NULL,
      'benchmark_bbc_smileys' => 'BB-Codes + Smileys:',
      'benchmark_failed' => 'fehlerhaft',
      'benchmark_gzip' => 'GZIP:',
      'benchmark_load_time' => 'Generierungszeit:',
      'benchmark_queries' => 'DB-Abfragen:',
      'benchmark_queries_time' => 'Zeit f�r DB-Abfragen:',
      'benchmark_sec' => 'Sek.',
      'benchmark_smileys' => 'Nur Smileys:',
      'benchmark_templates' => 'Templates:',
      'benchmark_templates_time' => 'Zeit f�r Templates:',
      'img_captcha_session_expired_error' => 'Sitzung beendet<br>Aktualisiere die Seite',
      'page_gzip_off' => 'Aus',
      'page_gzip_on' => 'An<br />Komprimierungsrate: ',
      'post_info_postcount' => 'Beitr�ge: ',
    ),
    'language' =>
    array (
      'box_newtopic' => NULL,
      'editprofile_signature_longdesc' => NULL,
      'editprofile_standard' => NULL,
      'forum_options_search_reset' => NULL,
      'im_msgtitle' => NULL,
      'index_headline' => NULL,
      'no_board_given' => NULL,
      'pm_index_dir' => NULL,
      'post_sent' => NULL,
      'print_title_page' => NULL,
      'register_veriword' => 'Please enter the chars in the image. This should help to avoid spam.',
      'section_closed' => NULL,
      'section_not_available' => NULL,
      'x_article' => NULL,
      'benchmark_bbc_smileys' => 'BB-Codes + Smileys:',
      'benchmark_failed' => 'failed',
      'benchmark_gzip' => 'GZIP:',
      'benchmark_load_time' => 'Load Time:',
      'benchmark_queries' => 'Queries:',
      'benchmark_queries_time' => 'Time for Queries:',
      'benchmark_sec' => 'sec.',
      'benchmark_smileys' => 'Only Smileys:',
      'benchmark_templates' => 'Templates:',
      'benchmark_templates_time' => 'Templates Time:',
      'img_captcha_session_expired_error' => 'Session expired<br>Refresh the Page',
      'page_gzip_off' => 'Off',
      'page_gzip_on' => 'On<br />Compression Rate: ',
      'post_info_postcount' => 'Posts: ',
    ),
  ),
  'modules' =>
  array (
    'language_de' =>
    array (
      'wiki_add_new_page' => 'Neue Seite erstellen',
      'wiki_article_form' => 'Artikel',
      'wiki_char_error' => 'Der Titel enth�lt nicht erlaubt Zeichen!',
      'wiki_creator' => 'Erstellung',
      'wiki_edit' => 'Editieren',
      'wiki_editor' => '�nderung',
      'wiki_edit_page' => 'Bestehende Seite �ndern',
      'wiki_locked' => 'Diese Seite ist gesperrt. Wenn Sie glauben, dass dies ein Irrtum ist, senden Sie uns bitte eine E-Mail.',
      'wiki_name' => 'Wiki',
      'wiki_new_pages' => 'Neue Seiten',
      'wiki_no_results' => 'Es wurden leider keine Suchergebnisse gefunden!',
      'wiki_page' => 'Seite',
      'wiki_page_data' => 'Seiteninhalt:',
      'wiki_page_format' => 'Formatierung:',
      'wiki_recent_changes' => 'Letzte �nderungen',
      'wiki_saved' => 'Seite wurde gespeichert!',
      'wiki_search' => 'Suche',
      'wiki_search_form' => 'Suchen',
      'wiki_search_result' => 'Suchergebnisse: ',
      'wiki_title' => 'Wiki',
      'wiki_view_info' => 'Letzte �nderung am {$date} von {$author}',
    ),
    'language' =>
    array (
      'wiki_add_new_page' => 'Create new page',
      'wiki_article_form' => 'Article',
      'wiki_char_error' => 'The title contains invalid chars!',
      'wiki_creator' => 'Creation',
      'wiki_edit' => 'Edit',
      'wiki_editor' => 'Modification',
      'wiki_edit_page' => 'Edit existing page',
      'wiki_locked' => 'This page is locked. If you think this is a mistake, please email the admin.',
      'wiki_name' => 'Wiki',
      'wiki_new_pages' => 'New pages',
      'wiki_no_results' => 'Sorry, no search results found!',
      'wiki_page' => 'Page',
      'wiki_page_data' => 'Page Content:',
      'wiki_page_format' => 'Formatting:',
      'wiki_recent_changes' => 'Last changes',
      'wiki_saved' => 'Site has been saved!',
      'wiki_search' => 'Search',
      'wiki_search_form' => 'Search',
      'wiki_search_result' => 'Search results: ',
      'wiki_title' => 'Wiki',
      'wiki_view_info' => 'Last change at {$date} by {$author}',
    ),
  ),
  'settings' =>
  array (
    'language_de' =>
    array (
      'compatible_version' => '0.8 RC7',
    ),
    'language' =>
    array (
      'compatible_version' => '0.8 RC7',
    ),
  )
);
updateLanguageFiles($ini);
echo "- Language files updated.<br />";

// Stylesheets
$dir = dir('designs');
while (false !== ($entry = $dir->read())) {
	$path = "{$dir->path}/{$entry}";
	if (is_dir($path) && is_id($entry)) {
		$css = file_get_contents("{$path}/standard.css");
		$css .= "\r\ntt {\r\n\tfont-family: 'Courier New', monospace;\r\n}";
		$filesystem->file_get_contents("{$path}/standard.css", $css);
	}
}
echo "- Stylesheets updated.<br />";

// MySQL
$file = 'install/package/'.$package.'/db/db_changes.sql';
$sql = file_get_contents($file);
$sql = str_ireplace('{:=DBPREFIX=:}', $db->prefix(), $sql);
$db->multi_query($sql);
echo "- Database tables updated.<br />";

// Set incompatible packages inactive
$db->query("UPDATE {$db->pre}packages SET active = '0' WHERE internal = 'viscacha_quick_reply'");
// TODO: Delete/disable components
setPackagesInactive();
echo "- Incompatible Packages set as 'inactive'.<br />";

// Refresh Cache
$dirs = array('cache/', 'cache/modules/');
foreach ($dirs as $dir) {
	if ($dh = @opendir($dir)) {
		while (($file = readdir($dh)) !== false) {
			if (strpos($file, '.php') !== false) {
				$filesystem->unlink($dir.$file);
			}
	    }
		closedir($dh);
	}
}
echo "- Cache cleared.<br />";
echo "<br /><strong>Finished Update!</strong>";
?>
</div>
<div class="bfoot center"><input type="submit" value="Continue" /></div>