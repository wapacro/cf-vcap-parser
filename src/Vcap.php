<?php

namespace wapacro\VcapParser;


class Vcap {
	protected $vcapServices;
	protected $vcapApplication;
	protected $serviceProperties;
	protected $defaultValue = null;

	/**
	 * Vcap constructor
	 */
	public function __construct() {
		$this->vcapApplication = json_decode($_ENV['VCAP_APPLICATION'] ?? '{}');
		$this->vcapServices = json_decode($_ENV['VCAP_SERVICES'] ?? '{}');

		$this->parseServiceProperties();
	}

	/**
	 * Reorganises the VCAP Service structure
	 * to a more easy useable array structure
	 */
	protected function parseServiceProperties() {
		foreach ($this->vcapServices as $serviceInstances) {
			foreach ($serviceInstances as $service) {
				$this->serviceProperties[$service->name] = $service;
			}
		}
	}

	/**
	 * Returns the application's name
	 * @return string
	 */
	public function getAppName() {
		return $this->vcapApplication->application_name ?? $this->defaultValue;
	}

	/**
	 * Returns the application's space name
	 * @return string
	 */
	public function getSpaceName() {
		return $this->vcapApplication->space_name ?? $this->defaultValue;
	}

	/**
	 * Returns the environment based on the space name and
	 * default to "local", if no value was returned
	 * @return string
	 */
	public function getEnvironment() {
		return $this->getSpaceName() !== $this->defaultValue ? strtolower($this->getSpaceName()) : 'local';
	}

	/**
	 * Returns the application's routes (all of thme)
	 * @return array
	 */
	public function getAppRoutes() {
		return $this->vcapApplication->application_uris ?? $this->defaultValue;
	}

	/**
	 * Return the application's route (only the first one)
	 * @return string
	 */
	public function getAppRoute() {
		return !empty($this->getAppRoutes()) ? 'https://' . $this->getAppRoutes()[0] : $this->defaultValue;
	}

	/**
	 * Returns the application's memory limit
	 * @return string
	 */
	public function getMemoryLimit() {
		return $this->vcapApplication->limits->mem ?? $this->defaultValue;
	}

	/**
	 * Returns the application's disk quota
	 * @return string
	 */
	public function getDiskLimit() {
		return $this->vcapApplication->limits->disk ?? $this->defaultValue;
	}

	/**
	 * Return the properties of given service
	 * @param string $serviceName
	 * @return array
	 */
	public function getServiceProperties(string $serviceName) {
		return $this->serviceProperties[$serviceName] ?? $this->defaultValue;
	}

	/**
	 * Returns the credentials for given service
	 * @param string $serviceName
	 * @return array
	 */
	public function getServiceCredentials(string $serviceName) {
		return !empty($this->serviceProperties[$serviceName]) ? $this->serviceProperties[$serviceName]->credentials : $this->defaultValue;
	}

}