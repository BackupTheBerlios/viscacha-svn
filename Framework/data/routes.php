<?php
$config = array(
	'DefaultPackage' => 'Cms',
	'DefaultModule' => 'ContentPages',
	'Routable' => array(
		'Cms' => array(
			'page' => 'ContentPages',
			'contact' => 'ContactPages',
			'user' => 'UserPages',
			'admin' => array(
				'' => 'AdminDefaultPages',
				'members' => 'AdminMemberPages',
				'documents' => 'AdminDocPages'
			)
		),
		'Core' => array() // Empty packages are NOT routable
	)
);
?>