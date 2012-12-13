<?php

namespace DeitAccessControl;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

use \Zend\Permissions\Acl\Acl;
use \Zend\Permissions\Acl\GenericRole as Role;
use \Zend\Permissions\Acl\GenericResource as Resource;

class Module {

	public function onBootstrap(MvcEvent $e) {
		$app    = $e->getApplication();
		$sm     = $app->getServiceManager();
		$em     = $app->getEventManager();

		$cfg = $sm->get('Config');

		if (isset($cfg['deit_access_control'])) {

			//get the service config
			$serviceCfg = $cfg['deit_access_control'];

			//construct the Access Control List
			$acl = new Acl();

			foreach ($serviceCfg['acl']['roles'] as $role) {
				$acl->addRole($role);
			}

			foreach ($serviceCfg['acl']['resources'] as $resource) {
				$acl->addResource($resource);
			}

			foreach ($serviceCfg['acl']['rules']['allow'] as $role => $resource) {
				$acl->allow($role, $resource);
			}

			//create the service
			$service = new \DeitAccessControl\Service();
			$service
				->setAcl($acl)
				->setDefaultRole($serviceCfg['default_role'])
				->setRoleResolver($serviceCfg['role_resolver'])
			;

			//attach the service listeners
			$em->attachAggregate($service);
			$em->attachAggregate(new View\UnauthorisedStrategy());

		}

	}

	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}

	public function getAutoloaderConfig() {
		return array(
			'Zend\Loader\StandardAutoloader' => array(
				'namespaces' => array(
					__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
				),
			),
		);
	}

}
