$db->query("INSERT INTO {$db->pre}settings_groups (title, name, description) VALUES ('{$ini['info']['title']}', 'module_{$pluginid}', 'Configuration for plugin {$pluginid}')", __LINE__, __FILE__);
$group = $db->insert_id();

$db->query("
INSERT INTO {$db->pre}settings (
	name, title, description, type, optionscode, value, sgroup
)
VALUES (
	'items',
	'Number of news',
	'Number of news shown on the frontpage',
	'text',
	'',
	'5',
	'{$group}')
", __LINE__, __FILE__);
$db->query("
INSERT INTO {$db->pre}settings (
	name, title, description, type, optionscode, value, sgroup
)
VALUES (
	'teaserlength',
	'Shortening news',
	'Determine onto how many signs the preview of the articles is shortened, if no code to cut the text is specified.',
	'text',
	'',
	'300',
	'{$group}')
", __LINE__, __FILE__);
$db->query("
INSERT INTO {$db->pre}settings (
	name, title, description, type, optionscode, value, sgroup
)
VALUES (
	'cutat',
	'Code to cut after',
	'You can cut the preview (teaser) manually by placing the specified code in the text. All text after this code will be removed from the preview.',
	'select',
	'teaser=[teaser]',
	'teaser',
	'{$group}')
", __LINE__, __FILE__);

$db->query("INSERT INTO {$db->pre}textparser (`search`,`replace`,`type`,`desc`) VALUES ('[teaser]','','censor','')",__LINE__,__FILE__);
$delobj = $scache->load('bbcode');
$delobj->delete();

$c->getdata();
$c->updateconfig(array("module_{$pluginid}", "cutat"), str, "teaser");
$c->updateconfig(array("module_{$pluginid}", "items"), int, 5);
$c->updateconfig(array("module_{$pluginid}", "teaserlength"), int, 300);
$c->savedata();