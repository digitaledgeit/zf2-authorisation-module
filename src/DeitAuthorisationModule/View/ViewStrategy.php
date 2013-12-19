<?php

namespace DeitAuthorisationModule\View;
use \Zend\EventManager\EventManagerInterface;
use \Zend\EventManager\ListenerAggregateInterface;
use \Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * View strategy
 * @author James Newell <james@digitaledgeit.com.au>
 */
class ViewStrategy implements ListenerAggregateInterface {

	/**
	 * Name of exception template
	 * @var     string
	 */
	protected $exceptionTemplate = 'error/401';

	/**
	 * Gets the exception template
	 * @return  string
	 */
	public function getExceptionTemplate() {
		return $this->exceptionTemplate;
	}

	/**
	 * Sets the exception template
	 * @param   string $template
	 * @return  ViewStrategy
	 */
	public function setExceptionTemplate($template) {
		$this->exceptionTemplate = (string) $template;
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function attach(EventManagerInterface $eventManager) {
		$this->listeners[] = $eventManager->attach(
			MvcEvent::EVENT_DISPATCH_ERROR,
			array($this, 'onUnauthorised'),
			-10
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

		if ($event->getError() == 'error-unauthorized') {

			$model = new ViewModel(array(
				'message' => 'An error occurred during execution; please try again later.'
			));
			$model->setTemplate($this->getExceptionTemplate());
			$event->setResult($model);

			$response = $event->getResponse() ?: new Response();
			$response->setStatusCode(401);
			$event->setResponse($response);

		}

	}

}
