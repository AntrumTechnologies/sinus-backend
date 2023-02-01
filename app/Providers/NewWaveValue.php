<?php

namespace App\Providers;

use App\Models\SinusValue;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewWaveValue
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The wave value instance
     * 
     * @var \App\Models\SinusValue
     */
    public $waveValue;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\SinusValue $waveValue
     * @return void
     */
    public function __construct(SinusValue $waveValue)
    {
        $this->waveValue = $waveValue;
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
