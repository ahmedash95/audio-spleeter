<?php

namespace App\Events;

use App\Audio;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AudioStatusUpdate  implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels,Audioable;

	public function broadcastAs() : string {
		return 'audio.status_update';
	}
}
