<?php
class cache_cat_bid extends CacheItem {

	function load() {
		global $db, $scache;
		if ($this->exists() == true) {
		    $this->import();
		}
		else {
			$categories_obj = $scache->load('categories');
			$cat_cache = $categories_obj->get();
		    $result = $db->query("
			SELECT id, name, parent, position, description, topics, replies, opt, optvalue, forumzahl, topiczahl, prefix, invisible, readonly, auto_status, active_topic 
			FROM {$db->pre}forums
			",__LINE__,__FILE__);
		    $this->data = array();
		    while ($row = $db->fetch_assoc($result)) {
		    	$row['bid'] = $cat_cache[$row['parent']]['parent'];
		        $this->data[$row['id']] = $row;
		    }
		    $this->export();
		}
	}

}
?>