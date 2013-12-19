<?php

namespace DeitAuthorisationModule;

use \Zend\EventManager\EventManagerInterface;
use \Zend\EventManager\ListenerAggregateInterface;
use \Zend\Mvc\MvcEvent;

use \Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

/**
 * Guard
 * @author James Newell <james@digitaledgeit.com.au>
 */
class Service implements ListenerAggregateInterface {

	/**
	 * The access control list
	 * @var Acl
	 */
	private $acl;

	/**
	 * The default role used when no authenticated identity is present or the identity's role can't be discovered
	 * @var string
	 */
	private $defaultRole;

	/**
	 * The role resolver used to discover the role of an identity when preset
	 * @var callable
	 */
	private $roleResolver;

	/**
	 * Gets the access control list
	 * @return Acl
	 */
	public function getAcl() {
		return $this->acl;
	}

	/**
	 * Sets the access control list
	 * @param Acl $acl
	 * @return Service
	 */
	public function setAcl(Acl $acl) {
		$this->acl = $acl;
		return $this;
	}

	/**
	 * Gets the default role
	 * @return string
	 */
	public function getDefaultRole() {
		return $this->defaultRole;
	}

	/**
	 * Sets the default role
	 * @param string $role
	 * @return Service
	 */
	public function setDefaultRole($role) {
		$this->defaultRole = (string) $role;
		return $this;
	}

	/**
	 * Gets the role resolver
	 * @return callable
	 */
	public function getRoleResolver() {
		return $this->roleResolver;
	}

	/**
	 * Sets the role resolver
	 * @param callable $resolver
	 * @return Service
	 */
	public function setRoleResolver(callable $resolver) {
		$this->roleResolver = $resolver;
		return $this;
	}

	public function attach(EventManagerInterface $events) {
		$this->listeners[] = $events->attach(MvcEvent::EVENT_ROUTE, array($this, 'onRoute'), -1000);
	}

	public function detach(EventManagerInterface $events) {
		foreach ($this->listeners as $index => $listener) {
			if ($events->detach($listener)) {
				unset($this->listeners[$index]);
			}
		}
	}

	public function onRoute(MvcEvent $e) {

		$rm     = $e->getRouteMatch();
		$app    = $e->getApplication();
		$sm     = $app->getServiceManager();

		if ($rm) {

			$controller = $rm->getParam('controller');
			$action     = $rm->getParam('action');
			$names      = explode('\\', $controller.'\\'.$action);

			$as = $sm->get('Zend\Authentication\AuthenticationService');
			if ($as->hasIdentity()) {
				$identity   = $as->getIdentity();
				$resolver   = $this->getRoleResolver();
				$role       = $resolver($identity);
			} else {
				$identity   = null;
				$role       = $this->getDefaultRole();
			}

			$allowed = false;
			$acl = $this->getAcl();
			while (count($names)) {

				$resource = implode('\\', $names);

				if ($acl->hasResource($resource)) {
					$allowed = $acl->isAllowed($role, $resource);
					break;
				}

				array_pop($names);

			}

			if (!$allowed) {

				$e->setError('error-unauthorized')
					->setParam('identity', $identity)
					->setParam('controller', $controller)
					->setParam('action', $action)
				;

				$e->getApplication()->getEventManager()->trigger('dispatch.error', $e);

			}

		}

	}

}
