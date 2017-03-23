<?php

namespace WebLoader\Nette;

use Nette\DI\Container;
use Nette\Http\IRequest;
use WebLoader\Compiler;
use WebLoader\InvalidArgumentException;

class LoaderFactory
{

	/** @var IRequest */
	private $httpRequest;

	/** @var Container */
	private $serviceLocator;

	/** @var array */
	private $tempPaths;

	/** @var string */
	private $extensionName;

	/**
	 * @param array $tempPaths
	 * @param string $extensionName
	 * @param IRequest $httpRequest
	 * @param Container $serviceLocator
	 */
	public function __construct(array $tempPaths, $extensionName, IRequest $httpRequest, Container $serviceLocator)
	{
		$this->httpRequest = $httpRequest;
		$this->serviceLocator = $serviceLocator;
		$this->tempPaths = $tempPaths;
		$this->extensionName = $extensionName;
	}

	/**
	 * Paths
	 * @return array
	 */
	public function getTempPaths()
	{
		return array_values(array_unique($this->tempPaths));
	}

	/**
	 * @param string $name
	 * @return \WebLoader\Nette\CssLoader
	 */
	public function createCssLoader($name)
	{
		/* @var $compiler Compiler */
		$compiler = $this->serviceLocator->getService($this->extensionName . '.css' . ucfirst($name) . 'Compiler');
		return new CssLoader($compiler, $this->formatTempPath($name));
	}

	/**
	 * @param array $names
	 * @return JavaScriptLoader
	 * @throws InvalidArgumentException
	 * @internal param string $name
	 */
	public function createJavaScriptLoader(...$names)
	{
		if (empty($names)) {
			throw new InvalidArgumentException;
		}
		$first = array_shift($names);
		$compilers = [$this->serviceLocator->getService($this->extensionName . '.js' . ucfirst($first) . 'Compiler')];

		foreach ($names as $name) {
			try {
				$compilers[] = $this->serviceLocator->getService($this->extensionName . '.js' . ucfirst($name) . 'Compiler');
			} catch (\Nette\DI\MissingServiceException $ex) {

			}
		}

		return new JavaScriptLoader($this->formatTempPath($first), ...$compilers);
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function formatTempPath($name)
	{
		$lName = strtolower($name);
		$tempPath = isset($this->tempPaths[$lName]) ? $this->tempPaths[$lName] : Extension::DEFAULT_TEMP_PATH;
		return rtrim($this->httpRequest->getUrl()->basePath, '/') . '/' . $tempPath;
	}

}
