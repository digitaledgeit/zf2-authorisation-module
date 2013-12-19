<?php

namespace DeitAuthorisation;

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

			foreach ($serviceCfg['acl']['roles'] as $key => $value) {

				if (is_string($key)) {
					$acl->addRole($key, $value);
				} else {
					$acl->addRole($value);
				}

			}

			foreach ($serviceCfg['acl']['resources'] as $resource) {
				$acl->addResource($resource);
			}

			foreach ($serviceCfg['acl']['rules']['allow'] as $resource => $role) {
				$acl->allow($role, $resource);
			}

			//create the service
			$service = new \DeitAuthorisation\Service();
			$service
				->setAcl($acl)
				->setDefaultRole($serviceCfg['default_role'])
				->setRoleResolver($serviceCfg['role_resolver'])
			;

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
