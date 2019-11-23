<?php

namespace App\Scripts;

class Merger extends Script
{
	/**
	 * @var array
	 */
	private $sources;
	/**
	 * @var string
	 */
	private $output;

	/**
	 * Splitter constructor.
	 * @param array $sources
	 * @param string $output
	 */
	public function __construct(array $sources, string $output)
	{
		$this->sources = $sources;
		$this->output = $output;
	}

	public function name()
	{
		return 'Merger';
	}

	/**
	 * @return array
	 */
	public function command(): array
	{
		$concat = "concat:".implode('|',$this->sources);
		return ['ffmpeg','-i',$concat,'-acodec','copy',$this->output];
	}
}
