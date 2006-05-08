$result = $db->query("SELECT id, name FROM {$db->pre}settings_groups WHERE name = 'module_{$pluginid}' LIMIT 1");
$row = $db->fetch_assoc($result);

$c->getdata();
$result = $db->query("SELECT id, name FROM {$db->pre}settings WHERE sgroup = '{$row['id']}' LIMIT 1");
while ($row2 = $db->fetch_assoc($result)) {
	$c->delete(array($row['name'], $row2['name']));
}
$c->savedata();

$db->query("DELETE FROM {$db->pre}settings WHERE sgroup = '{$row['id']}'", __LINE__, __FILE__);
$db->query("DELETE FROM {$db->pre}settings_groups WHERE id = '{$row['id']}'", __LINE__, __FILE__);