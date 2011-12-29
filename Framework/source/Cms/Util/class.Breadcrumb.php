<?php
/**
 * Manages the breadcrumb menues.
 *
 * @package		Cms
 * @subpackage	Util
 * @author		Matthias Mohr
 * @since 		1.0
 */
class Breadcrumb {

    private $content;

    public function __construct() {
    	$this->content = array();
    }

    public function add($title, $url = NULL) {
    	$this->content[] = array(
    	    'title' => $title,
    	    'url' => $url
    	);
    }

    public function addUrl($url) {
		$last = array_pop($this->content);
		if ($last != null) {
	    	$this->content[] = array(
	    	    'title' => $last['title'],
	    	    'url' => $url
	    	);
		}
    }

    public function resetUrl() {
		$last = array_pop($this->content);
		if ($last != null) {
	    	$this->content[] = array(
	    	    'title' => $last['title'],
	    	    'url' => null
	    	);
		}
    }

    public function outputHTML($seperator = ' &raquo; ') {
        $cache = array();
        foreach ($this->content as $key => $row) {
        	$row['title'] = Sanitize::saveHtml($row['title']);
            if (!empty($row['url'])) {
                $cache[$key] = '<a href="'.$row['url'].'">'.$row['title'].'</a>';
            }
            else {
                $cache[$key] = $row['title'];
            }
        }
        return implode($seperator, $cache);
    }

    public function outputPlain($seperator = ' > ', $entities = true) {
        $cache = array();
        foreach ($this->content as $key => $row) {
        	$row['title'] = strip_tags($row['title']);
        	if ($entities) {
        		$row['title'] = Sanitize::saveHtml($row['title']);
        	}
            $cache[$key] = $row['title'];
        }
        return implode($seperator, $cache);
    }

    public function getCurrentTitle() {
    	$last = array_pop($this->content);
    	if ($last != null) {
    		return Sanitize::saveHtml($last['title']);
    	}
    	else {
    		return '';
    	}
    }

    public function getArray() {
        return $this->content;
    }
}
?>
