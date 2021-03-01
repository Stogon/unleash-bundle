<?php

namespace Stogon\UnleashBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UnleashBundle extends Bundle
{
	public function getPath(): string
	{
		return \dirname(__DIR__);
	}
}
