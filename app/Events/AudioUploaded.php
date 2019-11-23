<?php

namespace App\Events;

use App\Audio;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AudioUploaded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels,Audioable;

    public function broadcastAs() : string {
    	return 'audio.uploaded';
	}
}
