$db->query("ALTER TABLE {$db->pre}groups DROP `editor`");
$fields = unserialize(file_get_contents('data/group_fields.php'));
$key = array_search('editor', $fields['gFields']);
if ($key !== false) {
  unset($fields['gFields'][$key]);
}
$filesystem->file_put_contents('data/group_fields.php', serialize($fields));

$filesystem->unlink('editorcp.php');
$filesystem->rmdirr('editorcp/');