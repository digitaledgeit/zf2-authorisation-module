<?php

namespace DeitAuthorisation\View;

use \Zend\EventManager\EventManagerInterface;
use \Zend\EventManager\ListenerAggregateInterface;
use \Zend\Mvc\MvcEvent;

use Zend\View\Model\ViewModel;

class UnauthorisedStrategy implements ListenerAggregateInterface {

	/**
	 * Name of exception template
	 * @var string
	 */
	protected $exceptionTemplate = 'error/401';

	/**
	 * Gets the exception template
	 * @return string
	 */
	public function getExceptionTemplate() {
		return $this->exceptionTemplate;
	}

	/**
	 * Sets the exception template
	 * @param string $template
	 * @return UnauthorisedStrategy
	 */
	public function setExceptionTemplate($template) {
		$this->exceptionTemplate = (string) $template;
		return $this;
	}

	public function attach(EventManagerInterface $events) {
		$this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onUnauthorised'));
	}

	public function detach(EventManagerInterface $events) {
		foreach ($this->listeners as $index => $listener) {
			if ($events->detach($listener)) {
				unset($this->listeners[$index]);
			}
		}
	}

	public function onUnauthorised(MvcEvent $e) {

		// Do nothing if no error in the event
		$error = $e->getError();
		if (empty($error)) {
			return;
		}

		// Do nothing if the result is a response object
		$result = $e->getResult();
		if ($result instanceof Response) {
			return;
		}

		if ($e->getError() == 'error-unauthorized') {
			$model = new ViewModel(array(
				'message' => 'An error occurred during execution; please try again later.'
			));
			$model->setTemplate($this->getExceptionTemplate());
			$e->setResult($model);

			$response = $e->getResponse();
			if (!$response) {
				$response = new HttpResponse();
				$e->setResponse($response);
			}
			$response->setStatusCode(401);
		}

	}

}
