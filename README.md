
= Zend Framework 2 module: DeitAuthorisation =

This module is a simple reusable access control module which restricts access to 
your controllers based on the user's role.

To restrict access to your controllers, add a new config entry in your module: 
<code>
'deit_authorisation' => array(

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
		),
		'rules'     => array(
			'allow'     => array(
				'DeitAuthenticationModule\\Controller\\Authentication\\log-in'  => 'guest',  //specific action
				'DeitAuthenticationModule\\Controller\\Authentication\\log-out' => 'admin' ,
				
				'DeitAuthenticationModule\\Controller\\Authentication'          => 'admin',  //specific controller
				'DeitAuthenticationModule'                                      => 'admin',  //specific module
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
</code>