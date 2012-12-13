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

		$config = $this->getConfig();
		$service = new \DeitAccessControl\Service();

		$acl = new Acl();

		foreach ($config['deit_access_control']['acl']['roles'] as $role) {
			$acl->addRole($role);
		}

		foreach ($config['deit_access_control']['acl']['resources'] as $resource) {
			$acl->addResource($resource);
		}

		foreach ($config['deit_access_control']['acl']['rules']['allow'] as $role => $resource) {
			$acl->allow($role, $resource);
		}

		$service
			->setAcl($acl)
			->setDefaultRole($config['deit_access_control']['default_role'])
			->setRoleResolver($config['deit_access_control']['role_resolver'])
		;

		$em->attachAggregate($service);
		$em->attachAggregate(new View\UnauthorisedStrategy());
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
