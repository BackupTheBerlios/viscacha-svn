<?php
$config = array(
	'DefaultPackage' => 'Cms',
	'Routable' => array(
		'Cms' => array(
			'!' => 'ContentPages',
			'page' => 'ContentPages',
			'contact' => 'ContactPages',
			'user' => 'UserPages',
			'admin' => array(
				'!' => 'AdminDefaultPages',
				'sys' => 'AdminDefaultPages',
				'members' => 'AdminMemberPages',
				'documents' => 'AdminDocPages'
			)
		),
		'Airlines' => array(
			'admin' => array(
				'!' => 'AdminAirlinesPages',
				'default' => 'AdminAirlinesPages',
				'cfields' => 'AdminAirlinesFieldPages'
			)
		),
		'Core' => array() // Empty packages are NOT routable
	)
);
?>