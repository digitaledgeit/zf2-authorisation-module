<?php

namespace DeitAuthorisationModule\Options;

/**
 * Options
 * @author James Newell <james@digitaledgeit.com.au>
 */
class Options {

	/**
	 * The service name of the unauthorised strategy
	 * @var     string
	 */
	private $strategy;

	/**
	 * The name of the unauthorised template
	 * @var     string
	 */
	private $template;

	/**
	 * The name of the unauthorised route
	 * @var     string
	 */
	private $route;

	public function __construct(array $config = array()) {

		if (isset($config['strategy'])) {
			$this->setStrategy($config['strategy']);
		}

		if (isset($config['template'])) {
			$this->setTemplate($config['template']);
		}

		if (isset($config['route'])) {
			$this->setRoute($config['route']);
		}

	}

	public function getStrategy() {
		return $this->strategy;
	}

	public function setStrategy($strategy) {
		$this->strategy = $strategy;
		return $this;
	}

	public function getTemplate() {
		return $this->template;
	}

	public function setTemplate($template) {
		$this->template = $template;
		return $this;
	}

	public function getRoute() {
		return $this->route;
	}

	public function setRoute($route) {
		$this->route = $route;
		return $this;
	}

}
 