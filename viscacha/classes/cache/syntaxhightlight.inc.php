<?php
    include_once('classes/class.geshi.php');
	$scache = new scache('syntaxhighlight');
	if ($scache->existsdata() == TRUE) {
	    $clang = $scache->importdata();
	}
	else {
        $clang = array();
        $d = dir("classes/geshi");
        while (false !== ($entry = $d->read())) {
            if (get_extension($entry,TRUE) == 'php' && !is_dir("classes/geshi/".$entry)) {
                include_once("classes/geshi/".$entry);
                $short = str_replace('.php','',$entry);
                $clang[$short]['file'] = $entry;
                $clang[$short]['name'] = $language_data['LANG_NAME'];
                $clang[$short]['short'] = $short;
            }
        }
        $d->close();
        asort($clang);
	    $scache->exportdata($clang);
	}
?>