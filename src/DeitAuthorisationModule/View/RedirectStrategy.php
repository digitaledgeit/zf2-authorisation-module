<?php

namespace DeitAuthorisationModule\View;
use \Zend\EventManager\EventManagerInterface;
use \Zend\EventManager\ListenerAggregateInterface;
use \Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Redirect strategy
 * @author James Newell <james@digitaledgeit.com.au>
 */
class RedirectStrategy implements ListenerAggregateInterface {

	/**
	 * The name of the redirect route
	 * @var     string
	 */
	private $redirectRoute;

	/**
	 * Gets the name of the redirect route
	 * @return  string
	 */
	public function getRedirectRoute() {
		return $this->route;
	}

	/**
	 * Sets the name of the redirect route
	 * @param   string $name
	 * @return  $this
	 */
	public function setRedirectRoute($name) {
		$this->redirectRoute = (string) $name;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function attach(EventManagerInterface $eventManager) {
		$this->listeners[] = $eventManager->attach(
			MvcEvent::EVENT_DISPATCH_ERROR,
			array($this, 'onUnauthorised'),
			-5
		);
	}

	/**
	 * @inheritdoc
	 */
	public function detach(EventManagerInterface $events) {
		foreach ($this->listeners as $index => $listener) {
			if ($events->detach($listener)) {
				unset($this->listeners[$index]);
			}
		}
	}

	/**
	 * Handles the unauthorised event
	 * @param   MvcEvent $event
	 */
	public function onUnauthorised(MvcEvent $event) {

		// do nothing if no error in the event
		$error = $event->getError();
		if (empty($error)) {
			return;
		}

		// do nothing if the result is a response object
		$result = $event->getResult();
		if ($result instanceof Response) {
			return;
		}

		$router = $event->getRouter();

		if ($event->getError() == 'error-unauthorized') {

			//todo: add return URL
			$url = $router->assemble(array(), array('name' => $this->redirectRoute));

			$response = $event->getResponse() ?: new Response();
			$response->getHeaders()->addHeaderLine('Location', $url);
			$response->setStatusCode(302);
			$event->setResponse($response);

		}

	}

}
