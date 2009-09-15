<div class="bbody">
<?php
echo "<strong>Starting Update:</strong><br />";

require('data/config.inc.php');
require_once('install/classes/class.phpconfig.php');

function loadSettingArray($path) {
	include("{$path}/settings.lng.php");
	if (isset($lang['lang_code'])) {
		return $lang;
	}
	else {
		return array('lang_code' => 'en');
	}
}

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

// Hooks (ToDo: this sould be done in one step!)
$hooks = array_map('trim', file('admin/data/hooks.txt'));
removeHook($hooks, 'pdf');
removeHook($hooks, 'components_');
$hooks[] = '';
$hooks[] = 'admin/packages_admin.php';
$filesystem->file_put_contents('admin/data/hooks.txt', implode("\r\n", $hooks));

$hooks = file_get_contents('admin/data/hooks.txt');
$add_com = array('components.php');
$add_acom = array('admin/packages_admin.php');
$result = $db->query("SELECT internal FROM {$db->pre}packages");
while ($row = $db->fetch_assoc($result)) {
	$internal = preg_quote($row['internal'], "~");
	if (!preg_match("~^-component_{$internal}$~im")) {
		$add_com[] = "-component_{$row['internal']}";
	}
	if (!preg_match("~^-admin_component_{$internal}$~im")) {
		$add_acom[] = "-admin_component_{$row['internal']}";
	}
}
if (count($add_com) > 1) {
	$hooks = preg_replace("~^components.php$~im", implode("\r\n", $add_com), $hooks);
}
if (count($add_acom) > 1) {
	$hooks = preg_replace("~^admin/packages_admin.php$~im", implode("\r\n", $add_acom), $hooks);
}
$filesystem->file_put_contents('admin/data/hooks.txt', $hooks);
echo "- Hooks updated.<br />";

// Config
$c = new manageconfig();
$c->getdata('data/config.inc.php');
$c->updateconfig('version', str, VISCACHA_VERSION);
$c->updateconfig('fname', html_enc);
$c->updateconfig('fdesc', html_enc);
$c->updateconfig('spider_logvisits', int, 2);
$c->updateconfig('vote_change', int, 0);
$c->updateconfig('botgfxtest_width', int, 150);
$c->updateconfig('botgfxtest_height', int, 40);
$c->updateconfig('botgfxtest_recaptcha_private', str, '');
$c->updateconfig('botgfxtest_recaptcha_public', str, '');
$c->updateconfig('local_mode', int, 0);
$c->updateconfig('multiple_instant_notifications', int, 0);
$c->delete('always_send_js');
$c->delete('pdfcompress');
$c->delete('pdfdownload');
$c->delete('allow_http_auth');
$c->delete('botgfxtest_text_verification');
$c->delete('botgfxtest_posts_width');
$c->delete('botgfxtest_posts_height');
$c->savedata();

$c = new manageconfig();
$c->getdata('admin/data/config.inc.php', 'admconfig');
$c->updateconfig('checked_package_updates', int, 0);
$c->savedata();
echo "- Configuration updated.<br />";

// Old files
$filesystem->unlink('templates/lang2js.php');
$filesystem->unlink('classes/feedcreator/mbox.inc.php');
$filesystem->unlink('admin/html/images/captcha.jpg');
$filesystem->unlink('admin/html/images/captcha2.jpg');
$filesystem->unlink("pdf.php");
$filesystem->unlink('templates/editor/wysiwyg-color.js');
$filesystem->rmdirr("classes/fpdf/");
$filesystem->rmdirr("temp/pdfimages");
$filesystem->rmdirr("templates/editor/popups");
$feeds = file_get_contents('data/feedcreator.inc.php');
$feeds = preg_replace('~[\r\n]+MBOX\|mbox\.inc\.php\|MBox\|\d\|\d~i', '', $feeds);
$filesystem->file_put_contents('data/feedcreator.inc.php', $feeds);
$dir = dir('images');
while (false !== ($entry = $dir->read())) {
	$path = "{$dir->path}/{$entry}";
	if (is_dir($path) && is_id($entry)) {
		$filesystem->unlink("{$path}/pdf.gif");
	}
}
$dir = dir('language');
while (false !== ($entry = $dir->read())) {
	$path = "{$dir->path}/{$entry}";
	if (is_dir($path) && is_id($entry)) {
		$filesystem->rmdirr("{$path}/modules/");
	}
}
$filesystem->file_put_contents('data/errlog_php.inc.php', '');
$filesystem->file_put_contents("data/errlog_{$db->system}.inc.php", '');
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
      'admin_wysiwyg_alignment' => 'Ausrichtung:',
      'admin_wysiwyg_alignment_bottom' => 'Unten',
      'admin_wysiwyg_alignment_center' => 'Zentriert',
      'admin_wysiwyg_alignment_left' => 'Links',
      'admin_wysiwyg_alignment_middle' => 'Mitte',
      'admin_wysiwyg_alignment_not_set' => 'Nicht gesetzt',
      'admin_wysiwyg_alignment_right' => 'Rechts',
      'admin_wysiwyg_alignment_top' => 'Oben',
      'admin_wysiwyg_alt_text' => 'Alternativtext:',
      'admin_wysiwyg_bgcolor' => 'Hintergrundfarbe',
      'admin_wysiwyg_border_collapse' => 'Rahmen vereinigen:',
      'admin_wysiwyg_border_color' => 'Rahmenfarbe',
      'admin_wysiwyg_border_style' => 'Rahmenstil:',
      'admin_wysiwyg_border_width' => 'Rahmenst�rke:',
      'admin_wysiwyg_choose' => 'Ausw�hlen',
      'admin_wysiwyg_color' => 'Farbe:',
      'admin_wysiwyg_color_preview' => 'Vorschau der Farbe',
      'admin_wysiwyg_custom_target' => 'Kein Ziel / Benutzerdefiniert',
      'admin_wysiwyg_file' => 'Datei:',
      'admin_wysiwyg_folder' => 'Verzeichnis:',
      'admin_wysiwyg_folder_restrictions' => 'Das Verzeichnis darf nur Buchstaben, Zahlen, Unterstriche und Bindestriche enthalten.',
      'admin_wysiwyg_form_cancel' => 'Abbrechen',
      'admin_wysiwyg_form_submit' => 'Einf�gen',
      'admin_wysiwyg_form_upload' => 'Hochladen',
      'admin_wysiwyg_height' => 'H�he:',
      'admin_wysiwyg_hey_code' => 'Hex-Code:',
      'admin_wysiwyg_hspace' => 'Horizontaler Zwischenraum:',
      'admin_wysiwyg_image_url' => 'Bild-Adresse:',
      'admin_wysiwyg_insert_hr' => 'Horizontale Linie einf�gen',
      'admin_wysiwyg_insert_img' => 'Bild einf�gen',
      'admin_wysiwyg_insert_link' => 'Link einf�gen',
      'admin_wysiwyg_layout' => 'Layout',
      'admin_wysiwyg_max_filesize' => 'Maximale Dateigr��e: {$filesize}',
      'admin_wysiwyg_name' => 'Titel:',
      'admin_wysiwyg_no_files_found' => 'Es wurden leider keine Dateien gefunden.',
      'admin_wysiwyg_no_shade' => 'Kein Schatten:',
      'admin_wysiwyg_padding' => 'Innenabstand:',
      'admin_wysiwyg_predefined_colors' => 'Vordefinierte Farben',
      'admin_wysiwyg_preview' => 'Vorschau',
      'admin_wysiwyg_prev_dir' => 'Vorheriges Verzeichnis',
      'admin_wysiwyg_select_color' => 'Farbe w�hlen',
      'admin_wysiwyg_select_img' => 'Bild ausw�hlen',
      'admin_wysiwyg_table_cols' => 'Spalten:',
      'admin_wysiwyg_table_properties' => 'Tabellen-Einstellungen',
      'admin_wysiwyg_table_rows' => 'Zeilen:',
      'admin_wysiwyg_table_width' => 'Breite:',
      'admin_wysiwyg_target' => 'Ziel-Fenster:',
      'admin_wysiwyg_upload_x' => 'Bild hochladen',
      'admin_wysiwyg_url' => 'Adresse:',
      'admin_wysiwyg_vspace' => 'Vertikaler Zwischenraum:',
      'admin_wysiwyg_width' => 'Breite:',
      'admin_wysiwyg_width_full' => 'Voll',
    ),
    'language' =>
    array (
      'admin_cms_nav_package' => 'Package:',
      'admin_wysiwyg_alignment' => 'Alignment:',
      'admin_wysiwyg_alignment_bottom' => 'Bottom',
      'admin_wysiwyg_alignment_center' => 'Center',
      'admin_wysiwyg_alignment_left' => 'Left',
      'admin_wysiwyg_alignment_middle' => 'Middle',
      'admin_wysiwyg_alignment_not_set' => 'Not set',
      'admin_wysiwyg_alignment_right' => 'Right',
      'admin_wysiwyg_alignment_top' => 'Top',
      'admin_wysiwyg_alt_text' => 'Alternate Text:',
      'admin_wysiwyg_bgcolor' => 'Background-Color:',
      'admin_wysiwyg_border_collapse' => 'Border-Collapse:',
      'admin_wysiwyg_border_color' => 'Border-Color:',
      'admin_wysiwyg_border_style' => 'Border-Style:',
      'admin_wysiwyg_border_width' => 'Border-Width:',
      'admin_wysiwyg_choose' => 'Choose',
      'admin_wysiwyg_color' => 'Color:',
      'admin_wysiwyg_color_preview' => 'Preview of the color',
      'admin_wysiwyg_custom_target' => 'No target / Custom',
      'admin_wysiwyg_file' => 'File:',
      'admin_wysiwyg_folder' => 'Folder:',
      'admin_wysiwyg_folder_restrictions' => 'The folder should only contain letters, numbers, underscores or hyphen.',
      'admin_wysiwyg_form_cancel' => 'Cancel',
      'admin_wysiwyg_form_submit' => 'Insert',
      'admin_wysiwyg_form_upload' => 'Upload',
      'admin_wysiwyg_height' => 'Height:',
      'admin_wysiwyg_hey_code' => 'Hex-Code:',
      'admin_wysiwyg_hspace' => 'Horizontal Space:',
      'admin_wysiwyg_image_url' => 'Image URL:',
      'admin_wysiwyg_insert_hr' => 'Insert Horizontal Ruler',
      'admin_wysiwyg_insert_img' => 'Insert Image',
      'admin_wysiwyg_insert_link' => 'Insert Hyperlink',
      'admin_wysiwyg_layout' => 'Layout',
      'admin_wysiwyg_max_filesize' => 'Max Filesize: {$filesize}',
      'admin_wysiwyg_name' => 'Name:',
      'admin_wysiwyg_no_files_found' => 'Sorry, no files found.',
      'admin_wysiwyg_no_shade' => 'No Shade:',
      'admin_wysiwyg_padding' => 'Padding:',
      'admin_wysiwyg_predefined_colors' => 'Predefined colors',
      'admin_wysiwyg_preview' => 'Preview',
      'admin_wysiwyg_prev_dir' => 'Previous Directory',
      'admin_wysiwyg_select_color' => 'Select Color',
      'admin_wysiwyg_select_img' => 'Select Image',
      'admin_wysiwyg_table_cols' => 'Columns:',
      'admin_wysiwyg_table_properties' => 'Table Properties',
      'admin_wysiwyg_table_rows' => 'Rows:',
      'admin_wysiwyg_table_width' => 'Width:',
      'admin_wysiwyg_target' => 'Target Window:',
      'admin_wysiwyg_upload_x' => 'Upload image',
      'admin_wysiwyg_url' => 'URL:',
      'admin_wysiwyg_vspace' => 'Vertical Space:',
      'admin_wysiwyg_width' => 'Width:',
      'admin_wysiwyg_width_full' => 'Full',
    ),
  ),
  'admin/db' =>
  array (
    'language_de' =>
    array (
      'admin_db_backup_options_invalid' => 'Die Wahl der Optionen war leider nicht korrekt. Sie m�ssen entweder die Struktur und/oder die Daten exportieren.',
    ),
    'language' =>
    array (
      'admin_db_backup_options_invalid' => 'The chosen options are not correct. You need to export the structure and/or the data.',
    ),
  ),
  'admin/designs' =>
  array (
    'language_de' =>
    array (
      'admin_design_copy_standard_css' => 'Erstelle ein neues Stylesheet-Verzeichnis und benutze die Stylesheets des Standard-Designs als Grundlage.',
      'admin_design_create_new_images_directory' => 'Erstelle ein neues Bilder-Verzeichnis und benutze die Bilder des Standard-Designs als Grundlage.',
      'admin_design_create_new_template_directory' => 'Erstelle ein neues Template-Verzeichnis und benutze die Templates des Standard-Designs als Grundlage.',
    ),
    'language' =>
    array (
      'admin_design_copy_standard_css' => 'Create a new directory for stylesheets and use the stylesheets from the standard design as base',
      'admin_design_create_new_images_directory' => 'Create a new directory for images and use the images from the standard design as base.',
      'admin_design_create_new_template_directory' => 'Create a new directory for templates and use the templates from the standard design as base.',
    ),
  ),
  'admin/forums' =>
  array (
    'language_de' =>
    array (
      'admin_forum_until' => 'bis ',
    ),
    'language' =>
    array (
      'admin_forum_until' => 'until ',
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
  'admin/global' =>
  array (
    'language_de' =>
    array (
      'admin_gll_docs' => NULL,
      'admin_gll_pdf' => NULL,
      'admin_gls_docs' => NULL,
      'admin_gls_pdf' => NULL,
      'gmt' => 'GMT',
    ),
    'language' =>
    array (
      'admin_gll_docs' => NULL,
      'admin_gll_pdf' => NULL,
      'admin_gls_docs' => NULL,
      'admin_gls_pdf' => NULL,
      'gmt' => 'GMT',
    ),
  ),
  'admin/javascript' =>
  array (
    'language_de' =>
    array (
      'wysiwyg_backcolor' => 'Hintergrundfarbe',
      'wysiwyg_bold' => 'Fett',
      'wysiwyg_center' => 'Zentriert ausrichten',
      'wysiwyg_clean_word' => 'HTML-Code von MS Word s�ubern?',
      'wysiwyg_copy' => 'Kopieren',
      'wysiwyg_cut' => 'Ausscheiden',
      'wysiwyg_error_text_mode' => 'Sie sind im Text-Modus. Dieses Feature ist deswegen zur Zeit nicht verf�gbar.',
      'wysiwyg_font_face' => 'Schriftart',
      'wysiwyg_font_size' => 'Schriftgr��e',
      'wysiwyg_forecolor' => 'Vordergrundfarbe',
      'wysiwyg_headings' => '�berschrift',
      'wysiwyg_hr' => 'Horizontale Linie',
      'wysiwyg_image' => 'Bild',
      'wysiwyg_indent' => 'Einr�cken',
      'wysiwyg_italic' => 'Kursiv',
      'wysiwyg_justify' => 'Blocksatz',
      'wysiwyg_left' => 'Linksb�ndig ausrichten',
      'wysiwyg_link' => 'Link',
      'wysiwyg_maximize' => 'Editor maximieren',
      'wysiwyg_not_compatible' => 'Der WYSIWYG-Editor wird von Ihrem Browser leider nicht (ausreichend) unterst�tzt.',
      'wysiwyg_ordered_list' => 'Geordnete Liste',
      'wysiwyg_outdent' => 'Ausr�cken',
      'wysiwyg_paste' => 'Einf�gen',
      'wysiwyg_redo' => 'Wiederherstellen',
      'wysiwyg_remove_formatting' => 'Formatierung entfernen',
      'wysiwyg_right' => 'Rechtsb�ndig ausrichten',
      'wysiwyg_strikethrough' => 'Durchgestrichen',
      'wysiwyg_strip_word' => 'Word HTML entfernen',
      'wysiwyg_subscript' => 'Tiefgestellt',
      'wysiwyg_superscript' => 'Hochgestellt',
      'wysiwyg_table' => 'Tabelle',
      'wysiwyg_underline' => 'Unterstrichen',
      'wysiwyg_undo' => 'R�ckg�ngig',
      'wysiwyg_unordered_list' => 'Ungeordnete Liste',
      'wysiwyg_view_source' => 'Quelltext ansehen',
      'wysiwyg_view_text' => 'Design ansehen',
    ),
    'language' =>
    array (
      'wysiwyg_backcolor' => 'Back Color',
      'wysiwyg_bold' => 'Bold',
      'wysiwyg_center' => 'Justify Center',
      'wysiwyg_clean_word' => 'Clean HTML inserted by MS Word?',
      'wysiwyg_copy' => 'Copy',
      'wysiwyg_cut' => 'Cut',
      'wysiwyg_error_text_mode' => 'You are in TEXT Mode. This feature has been disabled.',
      'wysiwyg_font_face' => 'Font face',
      'wysiwyg_font_size' => 'Font Size',
      'wysiwyg_forecolor' => 'Fore Color',
      'wysiwyg_headings' => 'Headings',
      'wysiwyg_hr' => 'Horizontal Ruler',
      'wysiwyg_image' => 'Image',
      'wysiwyg_indent' => 'Indent',
      'wysiwyg_italic' => 'Italic',
      'wysiwyg_justify' => 'Justify',
      'wysiwyg_left' => 'Justify Left',
      'wysiwyg_link' => 'Link',
      'wysiwyg_maximize' => 'Maximize the editor',
      'wysiwyg_not_compatible' => 'The WYSIWYG-Editor is not (completely) supported by your browser.',
      'wysiwyg_ordered_list' => 'Ordered List',
      'wysiwyg_outdent' => 'Outdent',
      'wysiwyg_paste' => 'Paste',
      'wysiwyg_redo' => 'Redo',
      'wysiwyg_remove_formatting' => 'Remove Formatting',
      'wysiwyg_right' => 'Justify Right',
      'wysiwyg_strikethrough' => 'Strikethrough',
      'wysiwyg_strip_word' => 'Strip Word HTML',
      'wysiwyg_subscript' => 'Subscript',
      'wysiwyg_superscript' => 'Superscript',
      'wysiwyg_table' => 'Table',
      'wysiwyg_underline' => 'Underline',
      'wysiwyg_undo' => 'Undo',
      'wysiwyg_unordered_list' => 'Unordered List',
      'wysiwyg_view_source' => 'View Source',
      'wysiwyg_view_text' => 'View Design',
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
      'admin_lang_imported_successfully' => 'Sprachpaket wurde erfolgreich importiert.',
    ),
    'language' =>
    array (
      'admin_lang_component' => NULL,
      'admin_lang_components' => NULL,
      'admin_lang_component_without_langfile' => NULL,
      'admin_lang_edit_langfile_package_id' => NULL,
      'admin_lang_files' => NULL,
      'admin_lang_packages_phrases' => 'Packages',
      'admin_lang_imported_successfully' => 'Languagepack imported successfully.',
    ),
  ),
  'admin/members' =>
  array (
    'language_de' =>
    array (
      'admin_member_activate_by_admin' => NULL,
      'admin_member_activate_via_mail' => NULL,
      'admin_member_at_least_one_match' => 'oder',
      'admin_member_keep_time_zone' => NULL,
      'admin_member_not_activated' => NULL,
      'admin_member_whole_match' => 'und',
      'admin_member_at_least_one_match_desc' => 'Nur eine der Angaben muss passen, um zu einem Treffer zu f�hren',
      'admin_member_whole_match_desc' => 'Alle Angaben m�ssen passen, um zu einem Treffer zu f�hren',
    ),
    'language' =>
    array (
      'admin_member_activate_by_admin' => NULL,
      'admin_member_activate_via_mail' => NULL,
      'admin_member_at_least_one_match' => 'or',
      'admin_member_keep_time_zone' => NULL,
      'admin_member_not_activated' => NULL,
      'admin_member_whole_match' => 'and',
      'admin_member_at_least_one_match_desc' => 'at least one of the input have to lead to a match',
      'admin_member_whole_match_desc' => 'the whole input have to lead to a match',
    ),
  ),
  'admin/misc' =>
  array (
    'language_de' =>
    array (
      'admin_misc_dictionary' => NULL,
      'admin_misc_license_not_forun' => NULL,
      'admin_misc_save' => NULL,
      'admin_misc_license_not_found' => 'Lizenztext wurde leider nicht gefunden.',
      'admin_misc_mysql_version' => 'Datenbank-Version:',
    ),
    'language' =>
    array (
      'admin_misc_dictionary' => NULL,
      'admin_misc_license_not_forun' => NULL,
      'admin_misc_save' => NULL,
      'admin_misc_license_not_found' => 'License not found.',
      'admin_misc_mysql_version' => 'Database version:',
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
      'admin_packages_conf_add_a_new_group_for_settings' => 'Gruppe f�r Einstellungen hinzuf�gen',
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
      'admin_packages_plugins_template_manage_templates_for_package' => ' Template-Verwaltung des Pakets ',
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
      'admin_packages_plugins_template_manage_templates_for_package' => 'Template Management for Package ',
    ),
  ),
  'admin/settings' =>
  array (
    'language_de' =>
    array (
      'admin_activate_logging_missing_ip' => 'Aktiviert die Protokollierung von IPs und User-Agents:',
      'admin_activate_pdf_topics' => NULL,
      'admin_activate_pdf_topics_info' => NULL,
      'admin_activate_spambot_at_guests' => 'Spam-Bot-Schutz bei Gastbeitr�gen',
      'admin_activate_spambot_registration' => 'Spam-Bot-Schutz bei der Registration:',
      'admin_compress_pdf' => NULL,
      'admin_compress_pdf_info' => NULL,
      'admin_dyeing_letters_captcha' => 'Eingef�rbte Buchstaben:',
      'admin_examples_captcha' => NULL,
      'admin_examples_captchaimg_textcodes' => NULL,
      'admin_examples_textcodes' => NULL,
      'admin_e_all' => 'Alle Fehler, Warnungen und Hinweise',
      'admin_e_error' => 'Nur schwerewiegende Fehler',
      'admin_e_notice' => NULL,
      'admin_e_strict' => NULL,
      'admin_e_warning' => NULL,
      'admin_file_typ_captcha' => 'Dateityp:',
      'admin_ftp_directory_does_not_exist' => 'Verzeichnis "{$ftp_path}" existiert leider nicht!',
      'admin_htaccess_error_doc_info' => 'Bei einem Server-Fehler (400, 403, 404, 500) wird die benutzerdefinierte Fehlerseite angezeigt. Beispiel: <a href="misc.php?action=error&id=404" target="_blank">Fehler 404</a>',
      'admin_ignor_words_less_chackters' => NULL,
      'admin_ignor_words_less_chackters_info' => NULL,
      'admin_image_height_captcha' => 'Standard Bildh�he:',
      'admin_image_width_captcha' => 'Standard Bildbreite:',
      'admin_mode_suggestions' => NULL,
      'admin_php_standard' => 'Standardeinstellung von PHP nutzen',
      'admin_pic_quality_captcha' => 'Qualit�t der Bilder:',
      'admin_save_php_errors' => 'Speichere Fehler (PHP und MySQL) in Protokolldatei:',
      'admin_save_php_errors_info' => 'Diese Option sollte nur zu Debugging-Zwecken aktiviert werden.',
      'admin_select_setting_group' => NULL,
      'admin_select_slq_erroe_log' => NULL,
      'admin_setting_posts_topics_info' => 'Minimale und Maximale L�ngen, Beitrags�nderungen und andere Einstellungen zu Beitr�gen.',
      'admin_show_text_captcha' => NULL,
      'admin_show_text_captcha_info' => NULL,
      'admin_spambot_posting' => NULL,
      'admin_spambot_registration' => NULL,
      'admin_suggestions_fast_mode' => NULL,
      'admin_suggestions_normal_mode' => NULL,
      'admin_suggestions_slow_mode' => NULL,
      'admin_test_ftp_connection' => 'Daten speichern und FTP-Verbindung ggf. testen',
      'admin_timezone_maintain' => NULL,
      'admin_topics_posts_pdf' => NULL,
      'admin_wave_filter_captcha' => 'Wende den "Wellen"-Filter auf das Spamschutz-Bild an:',
      'admin_wordwrap_character_html_tag_long_words' => 'Wordwrap: Text (HTML erlaubt) der f�r die Trennung von zu langen W�rtern genutzt wird:',
      'admin_captcha_type0' => 'Nicht aktiviert',
      'admin_captcha_type1' => 'Standard (VeriWord)',
      'admin_captcha_type2' => 'reCaptcha',
      'admin_enable_change_vote' => 'Erlaubt sich bei einer Umfrage umzuentscheiden',
      'admin_enable_change_vote_info' => 'Diese Option erm�glicht es Mitgliedern, sich, nach ihrer Stimmabgabe, bei einer Umfrage nochmal umzuentscheiden.',
      'admin_e_none' => 'Keine Fehlermeldungen ausgeben',
      'admin_ftp_php_extension_error' => 'Viscacha ben�tigt mindestens fsockopen, die Sockets-Erweiterung oder die FTP-Erweiterung f�r die FTP-Funktionalit�t. Bitte aktiviere eines dieser Features oder deaktiviere FTP.',
      'admin_logvisits_count_logging' => 'Nur Anzahl der Besuche protokollieren',
      'admin_logvisits_full_logging' => 'Zeit und Anzahl der Besuche protokollieren',
      'admin_logvisits_no_logging' => 'Keine Protokollierung',
      'admin_multiple_instant_notifications' => 'Pro Antwort eine E-Mail-Benachrichtigungen schicken:',
      'admin_multiple_instant_notifications_info' => 'Bei der sofortigen E-Mail-Benachrichtigung (Abonnements) von Themen wird, wenn diese Option aktiviert ist, pro Antwort eine Benachrichtigung geschickt. Andernfalls wird nur bei der ersten Antwort seit dem letzten Besuch eine Benachrichtigung verschickt.',
      'admin_recaptcha_private_key' => 'Interner Schl�ssel:',
      'admin_recaptcha_private_key_info' => '\'Private Key\', der Ihnen von {$re_link} zur Verf�gung gestellt wurde.',
      'admin_recaptcha_public_key' => '�ffentlicher Schl�ssel:',
      'admin_recaptcha_public_key_info' => '\'Public Key\', der Ihnen von {$re_link} zur Verf�gung gestellt wurde.',
      'admin_select_sys_error_log' => 'Protokoll der Systemfehler',
      'admin_spambot_recaptcha' => 'reCaptcha-Einstellungen',
      'admin_spambot_recaptcha_info' => 'reCaptcha ist ein Online-Service zur Spam-Abwehr. Sie brauchen einen pers�nlichen Schl�ssel um diesen Service in Anspruch zu nehmen (siehe unten). Ein Bild mit zwei W�rtern wird den Nutzern angezeigt. Diese �berpr�fung unterst�tzt Audio und erlaubt blinden Benutzern sich ebenfalls zu registrieren.',
      'admin_spambot_veriword' => 'VeriWord-Einstellungen',
      'admin_spambot_veriword_info' => 'VeriWord ist der Standard-Spam-Schutz von Viscacha. Ein Bild, bestehend aus mehreren Zeichen in variierenden Schriften/Farben, wird dem Nutzer angezeigt. Das Verhalten und Aussehen des Bildes wird von diversen Optionen bestimmt, die unten angepasst werden k�nnen.',
      'admin_topics_subscriptions' => 'Themen & Beitr�ge � Abonnements',
    ),
    'language' =>
    array (
      'admin_activate_pdf_topics' => NULL,
      'admin_activate_pdf_topics_info' => NULL,
      'admin_activate_spambot_at_guests' => 'Spam-Bot-Protection at Posting of guests',
      'admin_activate_spambot_registration' => 'Spam-Bot-Protection at Registration:',
      'admin_compress_pdf' => NULL,
      'admin_compress_pdf_info' => NULL,
      'admin_dyeing_letters_captcha' => 'Dyeing letters:',
      'admin_examples_captcha' => NULL,
      'admin_examples_captchaimg_textcodes' => NULL,
      'admin_examples_textcodes' => NULL,
      'admin_e_all' => 'All errors, warnings and notices',
      'admin_e_error' => 'Only fatal error messages',
      'admin_e_notice' => NULL,
      'admin_e_strict' => NULL,
      'admin_e_warning' => NULL,
      'admin_file_typ_captcha' => 'File type:',
      'admin_ftp_directory_does_not_exist' => 'Directory "{$ftp_path}" does not exist!',
      'admin_htaccess_error_doc_info' => 'On Server-Errors (400, 403, 404, 500) the custom Error-sites will be shown. Example: <a href="misc.php?action=error&id=404" target="_blank">Error 404</a>',
      'admin_ignor_words_less_chackters' => NULL,
      'admin_ignor_words_less_chackters_info' => NULL,
      'admin_image_height_captcha' => 'Standard image height:',
      'admin_image_width_captcha' => 'Standard image width:',
      'admin_mode_suggestions' => NULL,
      'admin_php_standard' => 'Standardeinstellung von PHP nutzen',
      'admin_pic_quality_captcha' => 'Quality of the picture:',
      'admin_save_php_errors_info' => 'This option should be activated only for debugging purposes!',
      'admin_select_setting_group' => NULL,
      'admin_select_slq_erroe_log' => NULL,
      'admin_setting_posts_topics_info' => 'Minimum lengths and maximum lengths, editing and other settings on posts and topics.',
      'admin_show_text_captcha' => NULL,
      'admin_show_text_captcha_info' => NULL,
      'admin_spambot_posting' => NULL,
      'admin_spambot_registration' => NULL,
      'admin_suggestions_fast_mode' => NULL,
      'admin_suggestions_normal_mode' => NULL,
      'admin_suggestions_slow_mode' => NULL,
      'admin_test_ftp_connection' => 'Save data and if so test FTP connection',
      'admin_timezone_maintain' => NULL,
      'admin_topics_posts_pdf' => NULL,
      'admin_wave_filter_captcha' => 'Use "wave"-filter on Spam-Bot-Protection-Picture:',
      'admin_wordwrap_character_html_tag_long_words' => 'Wordwrap: Text (HTML allowed) which will be used for separation of too long words:',
      'admin_captcha_type0' => 'Not active',
      'admin_captcha_type1' => 'Standard (VeriWord)',
      'admin_captcha_type2' => 'reCaptcha',
      'admin_enable_change_vote' => 'Allow to change one\'s mind of a survey',
      'admin_enable_change_vote_info' => 'This option allows members to change their vote in surveys again.',
      'admin_e_none' => 'Keine Fehlermeldungen ausgeben',
      'admin_ftp_php_extension_error' => 'Viscacha needs at least fsockopen, sockets extension or ftp extension to work! Please enable one of this features or disable ftp.',
      'admin_logvisits_count_logging' => 'Log only number of visits',
      'admin_logvisits_full_logging' => 'Log time and number of visits',
      'admin_logvisits_no_logging' => 'No logging',
      'admin_multiple_instant_notifications' => 'Send one e-mail subscriptions per reply:',
      'admin_multiple_instant_notifications_info' => 'For instant e-mail notifications (subscriptions) of replies you can receive one notification per reply, if this option is turned on. In the other case, you only get one notification for the first reply since your last visit.',
      'admin_recaptcha_private_key' => 'Private Key:',
      'admin_recaptcha_private_key_info' => 'Private key provided to you by {$re_link}.',
      'admin_recaptcha_public_key' => 'Public Key:',
      'admin_recaptcha_public_key_info' => 'Public key provided to you by {$re_link}.',
      'admin_select_sys_error_log' => 'System Error Log',
      'admin_spambot_recaptcha' => 'reCaptcha Settings',
      'admin_spambot_recaptcha_info' => 'reCaptcha is an online service to protect against spam. You\'ll need to get your personal keys to use this service (see below). An image containing two words will be shown to the user. This verification supports audio, allowing blind users to register.',
      'admin_spambot_veriword' => 'VeriWord Settings',
      'admin_spambot_veriword_info' => 'VeriWord is the default spam protection of Viscacha. An image consisting of letters in varying fonts/colors will be shown to the user. The appearance of this image is dictated by several options that you may control below.',
      'admin_topics_subscriptions' => 'Topics & Posts � Subscriptions',
    ),
  ),
  'admin/slog' =>
  array (
    'language_de' =>
    array (
      'admin_slog_sql_error_logfile' => 'Protokoll der Systemfehler',
      'admin_slog_backtrace' => 'Laufzeitinformationen',
      'admin_slog_day' => 'Tag',
      'admin_slog_error_num' => 'Nr.',
      'admin_slog_month' => 'Monat',
      'admin_slog_week' => 'Woche',
    ),
    'language' =>
    array (
      'admin_slog_sql_error_logfile' => 'System Error Logfile',
      'admin_slog_backtrace' => 'Runtime information',
      'admin_slog_day' => 'Day',
      'admin_slog_error_num' => 'No.',
      'admin_slog_month' => 'Month',
      'admin_slog_week' => 'Week',
    ),
  ),
  'bbcodes' =>
  array (
    'language_de' =>
    array (
      'bb_edit_author' => 'Nachtr�gliche Anmerkung des Autors:',
      'bb_edit_mod' => 'Nachtr�gliche Anmerkung von',
      'bb_hidden_content' => 'Versteckter Inhalt:',
      'bb_offtopic' => 'Off-Topic:',
      'bb_quote' => 'Zitat:',
      'bb_quote_by' => 'Zitat von',
      'bb_sourcecode' => 'Quelltext:',
      'geshi_hlcode_title' => '{$lang_name}-Quelltext:',
      'geshi_hlcode_txtdownload' => 'Download',
    ),
    'language' =>
    array (
      'bb_edit_author' => 'Additional note by the author:',
      'bb_edit_mod' => 'Additional note by',
      'bb_hidden_content' => 'Hidden Content:',
      'bb_offtopic' => 'Off Topic:',
      'bb_quote' => 'Quote:',
      'bb_quote_by' => 'Quote by',
      'bb_sourcecode' => 'Source Code:',
      'geshi_hlcode_title' => 'Source code ({$lang_name}):',
      'geshi_hlcode_txtdownload' => 'Download',
    ),
  ),
  'classes' =>
  array (
    'language_de' =>
    array (
      'mailer_signing' => 'Signierungsfehler: ',
    ),
    'language' =>
    array (
      'mailer_signing' => 'Signing Error: ',
    ),
  ),
  'global' =>
  array (
    'language_de' =>
    array (
      'bb_edit_author' => NULL,
      'bb_edit_mod' => NULL,
      'bb_hidden_content' => NULL,
      'bb_offtopic' => NULL,
      'bb_quote' => NULL,
      'bb_quote_by' => NULL,
      'bb_sourcecode' => NULL,
      'box_newtopic' => NULL,
      'editprofile_signature_longdesc' => NULL,
      'editprofile_standard' => NULL,
      'forum_options_search_reset' => NULL,
      'geshi_hlcode_title' => NULL,
      'geshi_hlcode_txtdownload' => NULL,
      'htaccess_errdesc_401' => NULL,
      'htaccess_error_401' => NULL,
      'im_msgtitle' => NULL,
      'index_headline' => NULL,
      'no_board_given' => NULL,
      'pdf_attachments' => NULL,
      'pdf_attachments_filesize' => NULL,
      'pdf_footer' => NULL,
      'pdf_postinfo' => NULL,
      'pdf_vote' => NULL,
      'pdf_vote_result' => NULL,
      'pdf_vote_voters' => NULL,
      'pm_index_dir' => NULL,
      'post_sent' => NULL,
      'print_title_page' => NULL,
      'register_veriword' => 'Bitte geben Sie zum Spamschutz die Zeichenfolge aus dem Bild ein.',
      'section_closed' => NULL,
      'section_not_available' => NULL,
      'showtopic_options_pdf' => NULL,
      'thumb_error' => 'Konnte Miniaturansicht nicht erstellen',
      'timestamps_gmt_diff' => 'Alle Zeitangaben in {%my->timezone_str}.',
      'timezone_current' => NULL,
      'timezone_desc' => 'Aktuelle Uhrzeit: {%my->current_time}.',
      'timezone_summer' => NULL,
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
      'error_no_forum_permissions' => 'Sie haben leider keine Berechtigung die versteckten Foren anzusehen. Bitte melden Sie sich mit den n�tigen Rechten an!',
      'gmt' => 'GMT',
      'img_captcha_session_expired_error' => 'Sitzung beendet<br>Aktualisiere die Seite',
      'page_gzip_off' => 'Aus',
      'page_gzip_on' => 'An<br />Komprimierungsrate: ',
      'post_info_postcount' => 'Beitr�ge: ',
      'showtopic_options_fav_remove' => 'Aus den Favoriten entfernen',
      'vote_change_option' => 'Votum �ndern',
      'vote_go_form' => 'Votum abgeben',
      'vote_reply_too_long' => 'Die Antwortm�glichkeit {$i} ist zu lang.',
    ),
    'language' =>
    array (
      'bb_edit_author' => NULL,
      'bb_edit_mod' => NULL,
      'bb_hidden_content' => NULL,
      'bb_offtopic' => NULL,
      'bb_quote' => NULL,
      'bb_quote_by' => NULL,
      'bb_sourcecode' => NULL,
      'box_newtopic' => NULL,
      'editprofile_signature_longdesc' => NULL,
      'editprofile_standard' => NULL,
      'forum_options_search_reset' => NULL,
      'geshi_hlcode_title' => NULL,
      'geshi_hlcode_txtdownload' => NULL,
      'htaccess_errdesc_401' => NULL,
      'htaccess_error_401' => NULL,
      'im_msgtitle' => NULL,
      'index_headline' => NULL,
      'no_board_given' => NULL,
      'pdf_attachments' => NULL,
      'pdf_attachments_filesize' => NULL,
      'pdf_footer' => NULL,
      'pdf_postinfo' => NULL,
      'pdf_vote' => NULL,
      'pdf_vote_result' => NULL,
      'pdf_vote_voters' => NULL,
      'pm_index_dir' => NULL,
      'post_sent' => NULL,
      'print_title_page' => NULL,
      'register_veriword' => 'Please enter the chars in the image. This should help to avoid spam.',
      'section_closed' => NULL,
      'section_not_available' => NULL,
      'showtopic_options_pdf' => NULL,
      'timestamps_gmt_diff' => 'All times are {%my->timezone_str}.',
      'timezone_current' => NULL,
      'timezone_desc' => 'Current time: {%my->current_time}.',
      'timezone_summer' => NULL,
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
      'error_no_forum_permissions' => 'Sorry, you haven\'t got the permission to view the hidden forums. Please log in with the necessary permissions!',
      'gmt' => 'GMT',
      'img_captcha_session_expired_error' => 'Session expired<br>Refresh the Page',
      'page_gzip_off' => 'Off',
      'page_gzip_on' => 'On<br />Compression Rate: ',
      'post_info_postcount' => 'Posts: ',
      'showtopic_options_fav_remove' => 'Remove from favorites',
      'vote_change_option' => 'Change vote',
      'vote_go_form' => 'Cast your vote',
      'vote_reply_too_long' => 'Option {$i} of your vote is too long.',
    ),
  ),
  'modules' =>
  array (
    'language_de' =>
    array (
      'ps_nav_package_overview' => NULL,
      'ps_nav_title' => NULL,
    ),
    'language' =>
    array (
      'ps_nav_package_overview' => NULL,
      'ps_nav_title' => NULL,
    ),
  ),
  'settings' =>
  array (
    'language_de' =>
    array (
      'compatible_version' => '0.8',
    ),
    'language' =>
    array (
      'compatible_version' => '0.8',
    ),
  ),
  'wwo' =>
  array (
    'language_de' =>
    array (
      'wwo_pdf' => NULL,
      'wwo_pdf_fallback' => NULL,
    ),
    'language' =>
    array (
      'wwo_pdf' => NULL,
      'wwo_pdf_fallback' => NULL,
      'wwo_showforum' => 'is viewing the following board: <a href="showforum.php?id={$id}">{$title}</a>',
      'wwo_showforum_fallback' => 'is viewing a board',
    ),
  ),
);
updateLanguageFiles($ini);

$dir = dir('language');
while (false !== ($entry = $dir->read())) {
	$path = "{$dir->path}/{$entry}";
	if (is_dir($path) && is_id($entry)) {
		$lng_settings = loadSettingArray($path);
		if ($lng_settings['lang_code'] != 'de') {
			$lng_settings['lang_code'] = 'en';
		}
		$filesystem->file_put_contents(
			"{$path}/mails/digest_d.php",
			file_get_contents('install/package/update/language/'.$lng_settings['lang_code'].'/digest_d.php')
		);
		$filesystem->file_put_contents(
			"{$path}/mails/digest_w.php",
			file_get_contents('install/package/update/language/'.$lng_settings['lang_code'].'/digest_w.php')
		);
		$filesystem->file_put_contents(
			"{$path}/mails/digest_s.php",
			file_get_contents('install/package/update/language/'.$lng_settings['lang_code'].'/digest_s.php')
		);
	}
}

echo "- Language files updated.<br />";

// Stylesheets
$dir = dir('designs');
while (false !== ($entry = $dir->read())) {
	$path = "{$dir->path}/{$entry}";
	if (is_dir($path) && is_id($entry)) {
		if (file_exists("{$path}/standard.css")) {
			$css = file_get_contents("{$path}/standard.css");
			$css = preg_replace("~\.popup\s+\{~i", ".popup {\r\n\toverflow: hidden;", $css);
			$css .= "\r\ntt {\r\n\tfont-family: 'Courier New', monospace;\r\n}";
			$filesystem->file_put_contents("{$path}/standard.css", $css);
		}

		if (file_exists("{$path}/ie.css")) {
			$css = file_get_contents("{$path}/ie.css");
			$css .= "\r\n* html .editor_textarea_outer .popup {\r\n\theight: expression( this.scrollHeight > 249 ? \"250px\" : \"auto\" );\r\n\toverflow-x: expression( this.scrollHeight > 249 && this.scrollWidth <= 200 ? \"hidden\" : \"auto\" );\r\n}";
			$css .= "\r\n* html .editor_textarea_outer .popup strong {\r\n\twidth: 196px;\r\n}";
			$css .= "\r\n* html .editor_textarea_outer .popup li {\r\n\twidth: 194px;\r\n}";
			$css .= "\r\n.bb_blockcode li {\r\n\twhite-space: normal;\r\n}";
			$filesystem->file_put_contents("{$path}/ie.css", $css);
		}
	}
}
echo "- Stylesheets updated.<br />";

// Set incompatible packages inactive
$db->query("UPDATE {$db->pre}packages SET active = '0' WHERE internal = 'viscacha_quick_reply'");
$result = $db->query("SELECT package FROM {$db->pre}component");
while ($row = $db->fetch_assoc($result)) {
	$db->query("UPDATE {$db->pre}packages SET active = '0' WHERE id = '{$row['package']}'");
}
setPackagesInactive();
echo "- Incompatible Packages set as 'inactive'.<br />";

// MySQL
$file = 'install/package/'.$package.'/db/db_changes.sql';
$sql = file_get_contents($file);
$sql = str_ireplace('{:=DBPREFIX=:}', $db->prefix(), $sql);
$db->multi_query($sql);
echo "- Database tables updated.<br />";

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