<?php
class cache_birthday_module extends CacheItem {

	function load($today = null) {
		global $db, $gpc;
		if ($this->exists($today) == true) {
		    $this->import();
		}
		else {
		    $result = $db->query("SELECT id, name, birthday FROM {$db->pre}user WHERE RIGHT( birthday, 5 ) = '".gmdate('m-d',times())."' ORDER BY name",__LINE__,__FILE__);
		    $this->data = array();
		    while ($e = $db->fetch_assoc($result)) {
		    	$e['name'] = $gpc->prepare($e['name']);
		    	$e['birthday'] = explode('-',$e['birthday']);
   			 	$e['age'] = getAge($e['birthday']);
        		$this->data[] = $e;
    		}
    		$this->export();
		}
	}
	
	function get($max_age = null) {
		if ($this->data == null) {
			$this->load($max_age);
		}
		return $this->data;
	}

}

global $gpc;

$stime = times();
$today = $stime - gmmktime (0, 0, 0, gmdate('m',$stime), gmdate('d',$stime), gmdate('Y',$stime), date('I',$stime)) - 60;

$birthday_module = $scache->load('birthday_module');
$data = $birthday_module->get($today);

if (count($data) > 0) {
	$tpl->globalvars(compact("data"));
	echo $tpl->parse($dir."birthday_box");
}
?>
