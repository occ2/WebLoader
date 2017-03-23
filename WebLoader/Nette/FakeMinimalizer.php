<?php

namespace WebLoader\Nette;

use WebLoader\Compiler;

/**
 * Vypne minimalizovani kodu js a css
 *
 * @author Attreid <attreid@gmail.com>
 */
class FakeMinimalizer
{

	public function __invoke($code, Compiler $compiler, $file = '')
	{
		return $code;
	}

}
