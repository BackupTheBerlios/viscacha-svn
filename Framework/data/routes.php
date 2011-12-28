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
			'evaluate' => 'AddEvaluationPages',
			'admin' => 'AdminAirlinesPages'
		),
		'Restaurants' => array(
			'evaluate' => 'AddEvaluationPages',
			'admin' => 'AdminRestaurantsPages'
		),
		'Core' => array(), // Empty packages are NOT routable
		'Evaluation' => array()
	)
);
?>