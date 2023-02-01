<?php

namespace App\Providers;

use App\Providers\NewWave;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewWaveNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Providers\NewWave  $event
     * @return void
     */
    public function handle(NewWave $event)
    {
        // Firebase logic: $event->wave
    }
}
