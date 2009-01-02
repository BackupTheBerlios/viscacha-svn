<?php
if (defined('VISCACHA_CORE') == false) { die('Error: Hacking Attempt'); }

$lang->group("modules");

$id = $gpc->get('cid', int);
$result = $db->query("
	SELECT p.id AS cid, c.id, p.internal
	FROM {$db->pre}packages AS p
		LEFT JOIN {$db->pre}plugin AS c ON c.module = p.id
	WHERE c.position = CONCAT('admin_component_', p.internal)
");

if ($db->num_rows($result) == 0) {
	echo head();
	error('admin.php?action=cms&job=package',$lang->phrase('admin_requested_page_doesnot_exist'));
}

$cache = $db->fetch_assoc($result);
DEFINE('PACKAGE_ID', $cache[$cid]['cid']);
DEFINE('PACKAGE_INTERNAL', $cache[$cid]['internal']);
DEFINE('PLUGIN_ID', $cache[$cid]['id']);
DEFINE('PLUGIN_DIR', 'modules/'.PACKAGE_ID.'/');
unset($cache);

($code = $plugins->load('admin_component_'.PACKAGE_INTERNAL)) ? eval($code) : null;

?>