<?php

namespace App\Scripts;

class Splitter extends Script
{
	/**
	 * @var string
	 */
	private $source;
	/**
	 * @var string
	 */
	private $output;

	/**
	 * Splitter constructor.
	 * @param string $source
	 * @param string $output
	 */
	public function __construct(string $source, string $output)
	{
		$this->source = $source;
		$this->output = $output;
	}

	public function name()
	{
		return 'Splitter';
	}

	/**
	 * @param string $source
	 * @param string $output
	 * @return array
	 */
	public function command(): array
	{
		return ['spleeter', 'separate', '-i', $this->source,'-c','mp3', '-o', $this->output];
	}
}
