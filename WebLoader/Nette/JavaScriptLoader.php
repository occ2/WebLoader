<?php

namespace WebLoader\Nette;

use Nette\Utils\Html;
use WebLoader\Compiler;

/**
 * JavaScript loader
 *
 * @author Jan Marek
 * @license MIT
 */
class JavaScriptLoader extends WebLoader
{

	public function __construct($tempPath, Compiler ...$compilers)
	{
		if (count($compilers) === 0) {
			throw new \WebLoader\InvalidArgumentException;
		}

		$compiler = array_shift($compilers);
		foreach ($compilers as $otherCompiler) {
			$compiler->getFileCollection()->addFiles($otherCompiler->getFileCollection()->getFiles());
		}

		parent::__construct($compiler, $tempPath);
	}

	/**
	 * Get script element
	 * @param string $source
	 * @return Html
	 */
	public function getElement($source)
	{
		$compiler = $this->getCompiler();
		$el = Html::el("script");
		if ($compiler->isDefer()) {
			$el->defer('');
		} elseif ($compiler->isAsync()) {
			$el->async('');
		}
		return $el->type("text/javascript")->src($source);
	}

}
