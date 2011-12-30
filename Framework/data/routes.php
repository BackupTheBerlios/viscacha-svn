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
			'!' => 'AirlinePages',
			'airlines' => 'AirlinePages',
			'admin' => array(
				'!' => 'AdminFlightPages',
				'flights' => 'AdminFlightPages',
				'categories' => 'AdminAirlinesPages',
				'airports' => 'AdminAirportPages',
				'cfields' => 'AdminAirlinesFieldPages',
				'efields' => 'AdminFlightFieldPages'
			)
		),
		'Core' => array() // Empty packages are NOT routable
	)
);
?>