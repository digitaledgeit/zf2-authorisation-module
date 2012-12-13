<?php

return array(

	/*
	 * EXAMPLE CONFIGURATION

	'deit_access_control' => array(

		'acl' => array(

			'roles' => array(
				'Guest',
				'Student',
				'Supervisor',
				'Admin',
			),

			'resources' => array(
				'UonFebeFyp',
				'UonFebeFypStudent',
				'UonFebeFypSupervisor',
				'UonFebeFypAdmin',
			),

			'rules' => array(

				'allow' => array(
					'Guest'         => array('UonFebeFyp'),
					'Student'       => array('UonFebeFypStudent'),
					'Supervisor'    => array('UonFebeFypSupervisor'),
					'Admin'         => array('UonFebeFypAdmin'),
				),

				'deny' => array(
				),

			),

		),

		'default_role' => 'Guest',

		'role_resolver' => function($identity) {
			switch ($identity->getType()) {
				case 'student':
					return 'Student';
				case 'supervisor':
					return 'Supervisor';
				default:
					return 'Guest';
			}

		},

	),
	*/
	
	'view_manager' => array(
		'template_map' => array(
			'error/401' => __DIR__ . '/../view/error/401.phtml',
		),
	),

);
