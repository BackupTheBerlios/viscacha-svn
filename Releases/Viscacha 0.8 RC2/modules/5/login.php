$request_uri = getRefererURL();
if (!$my->vlogin) {
	$request_uri = htmlspecialchars($request_uri);
	$tpl->globalvars(compact("request_uri"));
	echo $tpl->parse("modules/{$pluginid}/login_guest");
} 
else {
	$result = $db->query("SELECT COUNT(*) FROM {$db->pre}pm AS p WHERE pm_to = '{$my->id}' AND status = '0'",__LINE__,__FILE__);
	$newpms = $db->fetch_num($result);
	$my->pms = $newpms[0];
	$request_uri = rawurlencode($request_uri);
	$tpl->globalvars(compact("request_uri", "my"));
	echo $tpl->parse("modules/{$pluginid}/login_member");
}
