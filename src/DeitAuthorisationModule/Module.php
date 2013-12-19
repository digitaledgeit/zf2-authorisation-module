<?php

namespace DeitAuthorisationModule;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use \Zend\Permissions\Acl\Acl;
use \Zend\Permissions\Acl\GenericRole as Role;
use \Zend\Permissions\Acl\GenericResource as Resource;

class Module {

	public function onBootstrap(MvcEvent $event) {
		$app    = $event->getApplication();
		$sm     = $app->getServiceManager();
		$em     = $app->getEventManager();

		$cfg = $sm->get('Config');

		if (isset($cfg['deit_authorisation'])) {

			//get the service config
			$serviceCfg = $cfg['deit_authorisation'];

			//construct the Access Control List
			$acl = new Acl();

			if (isset($serviceCfg['acl']['roles'])) {
				foreach ($serviceCfg['acl']['roles'] as $key => $value) {

					if (is_string($key)) {
						$acl->addRole($key, $value);
					} else {
						$acl->addRole($value);
					}

				}
			}

			if (isset($serviceCfg['acl']['resources'])) {
				foreach ($serviceCfg['acl']['resources'] as $resource) {
					$acl->addResource($resource);
				}
			}

			if (isset($serviceCfg['acl']['rules']['allow'])) {
				foreach ($serviceCfg['acl']['rules']['allow'] as $resource => $role) {
					$acl->allow($role, $resource);
				}
			}

			//create the service
			$service = new \DeitAuthorisationModule\Service();
			$service
				->setAcl($acl)
			;

			if (isset($serviceCfg['default_role'])) {
				$service->setDefaultRole($serviceCfg['default_role']);
			}

			if (isset($serviceCfg['role_resolver'])) {
				$service->setRoleResolver($serviceCfg['role_resolver']);
			}

			//attach the service listeners
			$options    = $sm->get('deit_authorisation_options');
			$strategy   = $sm->get($options->getStrategy());
			$em->attachAggregate($strategy);
			$em->attachAggregate($service);
			//TODO: specify the view

		}

	}

	public function getConfig() {
		return include __DIR__ . '/../../config/module.config.php';
	}

	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__,
				),
			),
		);
	}

}
