<?php
/*
Erstellt ein Array, dass die Differenz zwischen zwei Verzeichnissen mit Language-Dateien erstellt 
und dieses hinterher so formatiert, dass es in dem Viscacha 0.8 Update-Script genutzt werden kann.
*/

/* Konfiguration */
$oldfiles = 'C:/localhost/xampp/htdocs/viscacha/svn/Releases/Viscacha 0.8 RC6/language/'; // Verzeichnis mit den alten Dateien
$newfiles = 'C:/localhost/xampp/htdocs/viscacha/0.8/language/'; // Verzeichnis mit den neuen Dateien

/* Programm-Code */
header('Content-type: text/plain');
define('VISCACHA_CORE', 1);

$files = rscandir($newfiles);
$update = array();
$pattern = '~^'.preg_quote($newfiles).'(\d+)/([\w/]+)\.lng\.php$~i';
$trans = array(
	1 => 'language_de',
	2 => 'language'
);

foreach ($files as $newfile) {
	$oldfile = $oldfiles.str_replace($newfiles, '', $newfile);

	if (!file_exists($oldfile)) {
		continue; // Skip if old file not existant
	}

	include($oldfile);
	$oldlang = $lang;
	include($newfile);
	$newlang = $lang;

	preg_match($pattern, $newfile, $matches);
	$langid = $trans[$matches[1]];
	$langfilename = $matches[2];

	$keys = array_merge(array_keys($oldlang), array_keys($newlang));
	foreach ($keys as $key) {
		if (!isset($oldlang[$key])) { // Phrase existiert nur in neuem Array: Hinzufügen
			$value = $newlang[$key];
		}
		elseif (!isset($newlang[$key])) { // Phrase existiert nur in altem Array: Löschen
			$value = null;
		}
		elseif ($newlang[$key] != $oldlang[$key]) { // Phrase wurde geändert: Hinzufügen (Überschreiben)
			$value = $newlang[$key];
		}
		else {
			continue; // Equal: Do nothing
		}
		$update[$langfilename][$langid][$key] = $value;
	}
}

var_export($update);

function rscandir($base = '', &$data = array()) {
	$array = array_diff(scandir($base), array('.', '..'));
	foreach($array as $value) {
		if (is_dir($base.$value)) {
			$data = rscandir($base.$value.'/', $data);
		}
		elseif (is_file($base.$value)) {
			if (preg_match('/\.lng\.php$/i', $value) && $value != 'custom.lng.php') {
				$data[] = $base.$value;
			}
		}
	}

	return $data;
}
?>