<?php

namespace App\Listeners;

use App\Events\AccountCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\attributes\Listener;

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
        // create a file
        $filePath = storage_path('app/history.txt');
        $fileContent = sprintf(
            "Account created: %s, User ID: %s, Code: %s, Name: %s, Balance: %s, Currency: %s\n",
            $event->account->id,
            $event->account->userId,
            $event->account->code,
            $event->account->name,
            $event->account->balance,
            $event->account->currency
        );
        file_put_contents($filePath, $fileContent, FILE_APPEND);
    }
}
