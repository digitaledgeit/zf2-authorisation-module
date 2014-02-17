<?php

return array(

	'service_manager' => array(
		'factories' => array(

			'deit_authorisation_options' => function($sm) {
				$config = $sm->get('Config');
				return new \DeitAuthorisationModule\Options\Options(
					isset($config['deit_authorisation']) ? $config['deit_authorisation'] : array()
				);
			},

			'DeitAuthorisationModule\View\ViewStrategy' => function($sm) {

				$options    = $sm->get('deit_authorisation_options');
				$strategy   = new DeitAuthorisationModule\View\ViewStrategy();

				if ($options->getTemplate()) {
					$strategy->setExceptionTemplate($options->getTemplate());
				}

				return $strategy;
			},

			'DeitAuthorisationModule\View\RedirectStrategy' => function($sm) {

				$options    = $sm->get('deit_authorisation_options');
				$strategy   = new DeitAuthorisationModule\View\RedirectStrategy();

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

	),

);
