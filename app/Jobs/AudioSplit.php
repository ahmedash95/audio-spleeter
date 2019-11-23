<?php

namespace App\Jobs;

use App\Audio;
use App\Events\AudioStatusUpdate;
use App\Scripts\Distributor;
use App\Scripts\Merger;
use App\Scripts\Splitter;
use App\Shell\ShellOutput;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class AudioSplit implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	/**
	 * @var Audio
	 */
	public $audio;

	/**
	 * Create a new job instance.
	 *
	 * @param Audio $audio
	 */
	public function __construct(Audio $audio)
	{
		$this->audio = $audio;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->statusUpdate('in progress');
		$this->ensureDirectoriesAreReady();

		$source = public_path($this->audio->source);
		$output = public_path(sprintf('/output/%s/distributor/', $this->audio->getSourceNameWithoutExtension()));
		if (!$this->distributor($source, $output)) {
			$this->statusUpdate('Error');

			return;
		}

		$files = array_diff(scandir($output), ['.', '..']);
		$filesToBeProcessed = array_map(
			function ($file) use ($output) {
				return $output.$file;
			},
			$files
		);

		$processOutput = public_path(sprintf('/output/%s/result/', $this->audio->getSourceNameWithoutExtension()));
		foreach ($filesToBeProcessed as $file) {
			if (!$this->process($file, $processOutput)) {
				$this->statusUpdate('Error');

				return;
			}
		}

		// files as directories (remove extension)
		foreach (['vocals', 'accompaniment'] as $source) {
			$filesToMerge = array_map(
				function ($file) use ($processOutput, $source) {
					return $processOutput.explode('.', $file)[0].DIRECTORY_SEPARATOR.$source.'.mp3';
				},
				$files
			);
			$output = $processOutput.$source.'.mp3';
			if (!$this->merge($filesToMerge, $output)) {
				$this->statusUpdate('Error');
				return;
			}
			$this->audio->update([$source => str_replace(public_path(),'',$output)]);
		}

		$this->statusUpdate('Done');
	}

	private function statusUpdate($status)
	{
		$this->audio->update(['status' => $status]);
		broadcast(new AudioStatusUpdate($this->audio->fresh()));
	}

	private function ensureDirectoriesAreReady(): void
	{
		$fileName = $this->audio->getSourceNameWithoutExtension();
		collect(
			[
				public_path(sprintf('%s/%s/%s', 'output', $fileName, 'distributor')),
				public_path(sprintf('%s/%s/%s', 'output', $fileName, 'result')),
			]
		)->each(
			function ($dir) {
				(new Process(['mkdir', '-p', $dir]))->run();
			}
		);

	}

	private function distributor($source, $output): bool
	{
		$script = new Distributor($source, $output);
		$task = $this->audio->tasks()->create(
			[
				'name' => $script->name(),
				'script' => $script,
			]
		)
		;

		$process = new Process($script->command());
		try {
			$process = tap($process)->run($output = new ShellOutput);
		} catch (ProcessTimedOutException $e) {
			$timedOut = true;
		}

		$task->update(
			[
				'exit_code' => $process->getExitCode(),
				'output' => (string) ($output ?? ''),
				'timed_out' => $timedOut ?? false,
			]
		);

		if ($process->getExitCode() !== 0) {
			return false;
		}

		return true;
	}

	private function process($source, $output): bool
	{
		$script = new Splitter($source, $output);
		$task = $this->audio->tasks()->create(
			[
				'name' => $script->name(),
				'script' => $script,
			]
		)
		;

		$process = new Process($script->command());
		try {
			$process = tap($process)->run($output = new ShellOutput);
		} catch (ProcessTimedOutException $e) {
			$timedOut = true;
		}

		$task->update(
			[
				'exit_code' => $process->getExitCode(),
				'output' => (string) ($output ?? ''),
				'timed_out' => $timedOut ?? false,
			]
		);

		if ($process->getExitCode() !== 0) {
			return false;
		}

		return true;
	}

	private function merge(array $files, string $output)
	{
		$script = new Merger($files, $output);
		$task = $this->audio->tasks()->create(
			[
				'name' => $script->name(),
				'script' => $script,
			]
		)
		;

		$process = new Process($script->command());
		try {
			$process = tap($process)->run($output = new ShellOutput);
		} catch (ProcessTimedOutException $e) {
			$timedOut = true;
		}

		$task->update(
			[
				'exit_code' => $process->getExitCode(),
				'output' => (string) ($output ?? ''),
				'timed_out' => $timedOut ?? false,
			]
		);

		if ($process->getExitCode() !== 0) {
			return false;
		}

		return true;
	}

}
