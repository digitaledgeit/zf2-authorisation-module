<?php

return array(

	'service_manager' => array(
		'factories' => array(

			'deit_authorisation_options' => function($sm) {
				$config = $sm->get('Config');
				return new \DeitAuthorisation\Options\Options(
					isset($config['deit_authorisation']) ? $config['deit_authorisation'] : array()
				);
			},

			'DeitAuthorisation\View\UnauthorisedStrategy' => function($sm) {

				$options    = $sm->get('deit_authorisation_options');
				$strategy   = new DeitAuthorisation\View\ViewStrategy();

				if ($options->getTemplate()) {
					$strategy->setExceptionTemplate($options->getTemplate());
				}

				return $strategy;
			},

			'DeitAuthorisation\View\RedirectStrategy' => function($sm) {

				$options    = $sm->get('deit_authorisation_options');
				$strategy   = new DeitAuthorisation\View\RedirectStrategy();

				if ($options->getRoute()) {
					$strategy->setRedirectRoute($options->getRoute());
				}

				return $strategy;
			},

		),
	),

	'view_manager' => array(
		'template_map' => array(
			'error/401' => __DIR__ . '/../view/error/401.phtml',
		),
	),

	'deit_authorisation' => array(

		/**
		 * The unauthorised strategy
		 * @type    string
		 */
		'strategy'  => 'DeitAuthorisation\View\ViewStrategy',

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

	),

);
