<?php


namespace App\Events;


use App\Audio;
use Illuminate\Broadcasting\PrivateChannel;

trait Audioable
{
	/**
	 * @var Audio
	 */
	public $audio;

	/**
	 * Create a new event instance.
	 *
	 * @param Audio $audio
	 */
	public function __construct(Audio $audio)
	{
		$this->audio = $audio;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return \Illuminate\Broadcasting\Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('user-'.$this->audio->user_id);
	}
}
