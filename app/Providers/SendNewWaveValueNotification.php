<?php

namespace App\Providers;

use App\Providers\NewWaveValue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewWaveValueNotification
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
     * @param  \App\Providers\NewWaveValue  $event
     * @return void
     */
    public function handle(NewWaveValue $event)
    {
        // Firebase logic: $event->waveValue
    }
}
