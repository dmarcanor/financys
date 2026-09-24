<?php

namespace App\Listeners;

use App\Events\Account\AccountCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\attributes\Listener;
use Illuminate\Support\Facades\Log;

#[Listener(AccountCreated::class)]
class SaveHistoryOnAccountCreated implements ShouldQueue
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
    public function handle(AccountCreated $event): void
    {
        Log::info('SaveHistoryOnAccountCreated', json_decode(json_encode($event->account), true));
    }
}
