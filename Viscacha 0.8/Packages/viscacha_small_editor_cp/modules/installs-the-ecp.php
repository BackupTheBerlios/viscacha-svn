$db->query("ALTER TABLE {$db->pre}groups ADD `editor` ENUM('0', '1') NOT NULL DEFAULT '0' AFTER `admin`");
$fields = unserialize(file_get_contents('data/group_fields.php'));
$fields['gFields'][] = 'editor'; 
$filesystem->file_put_contents('data/group_fields.php', serialize($fields));

$filesystem->copy('modules/'.$packageid.'/editorcp.php', 'editorcp.php');
$filesystem->copyr('modules/'.$packageid.'/editorcp/', 'editorcp/');

$delobj = $scache->load('groups');
$delobj->delete();