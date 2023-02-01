<?php

namespace App\Providers;

use App\Models\Sinus;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewWave
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The wave instance
     * 
     * @var \App\Models\Sinus
     */
    public $wave;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Sinus $wave
     * @return void
     */
    public function __construct(Sinus $wave)
    {
        $this->wave = $wave;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
