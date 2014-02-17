
# Zend Framework 2 module: DeitAuthorisationModule #

This module is a simple reusable access control module which restricts access to 
your controllers based on the user's role.

To restrict access to your controllers, add a new config entry in your module:

	'deit_authorisation' => array(

		/**
		 * The service name of the unauthorised strategy
		 * @type    string
		 */
		'strategy'  => 'DeitAuthorisationModule\View\ViewStrategy',

		/**
		 * The view template to display when the user is unauthorised
		 * @type    string
		 */
		'template'  => 'error/401',

		/**
		 * The route to redirect to when the user is unauthorised
		 * @type    string
		 */
		'route'     => 'log-in',

		/**
		 * The access control list
		 * @var array
		 */
		'acl'       => array(
			'roles'     => array(
				'guest',
				'admin' => 'guest'                                              //the admin role inherits guest permissions
			),
			'resources' => array(
				'DeitAuthenticationModule\\Controller\\Authentication\\log-in',
				'DeitAuthenticationModule\\Controller\\Authentication\\log-out',
				'DeitAuthenticationModule\\Controller\\Authentication',
				'DeitAuthenticationModule',
				'Application',
			),
			'rules'     => array(
				'allow'     => array(
					'DeitAuthenticationModule\\Controller\\Authentication::log-in'  => 'guest',  //restrict access to a specific action
					'DeitAuthenticationModule\\Controller\\Authentication::log-out' => 'admin' ,
					//'DeitAuthenticationModule\\Controller\\Authentication'          => 'admin',  //restrict access to a specific controller
					'Application'                                                   => 'admin',  //restrict access to a specific module
				),
			),
		),

		/**
		 * The default role used when no authenticated identity is present or the identity's role can't be discovered
		 * @var string
		 */
		'default_role'  => 'guest',

		/**
		 * The role resolver used to discover the role of an identity when preset
		 * @var callable
		 */
		'role_resolver' => function($identity) {
			if ($identity) {                                                     //this will be different if you have multiple roles which your authenticated users can be
				return 'admin';
			} else {
				return 'guest';
			}
		},

	),