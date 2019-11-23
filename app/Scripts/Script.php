<?php

namespace App\Scripts;

use RuntimeException;

abstract class Script
{
	public function command() : array {
		throw new RuntimeException('Script::command() is not implemented');
	}

	public function name()
	{
		throw new RuntimeException('Script::name() is not implemented');
	}

	public function __toString()
	{
		return implode(' ', $this->command());
	}
}
