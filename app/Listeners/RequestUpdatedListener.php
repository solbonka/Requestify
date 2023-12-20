<?php

namespace App\Listeners;

use App\Events\RequestUpdatedEvent;
use App\Jobs\SendResolvedMessageJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RequestUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RequestUpdatedEvent $event): void
    {
        $request = $event->request;

        SendResolvedMessageJob::dispatch($request)->onQueue('resolved');
    }
}
