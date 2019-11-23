<?php

namespace App\Scripts;

use Illuminate\Support\Arr;

class Distributor extends Script
{
	/**
	 * @var int
	 */
	public const SPLIT_BY_SECONDS = 120;

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
		$this->output = rtrim($output,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	}

	public function name()
	{
		return 'Distributor';
	}

	/**
	 * @return array
	 */
	public function command(): array
	{
		$output = $this->output.'%03d.mp3';
		return ['ffmpeg','-i', $this->source, '-f', 'segment','-segment_time', self::SPLIT_BY_SECONDS, '-c','copy', $output];
	}
}
