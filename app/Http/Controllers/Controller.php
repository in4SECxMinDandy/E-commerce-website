<?php

namespace App\Http\Controllers;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

abstract class Controller
{
    protected function broadcastSafely(object $event, bool $toOthers = true): void
    {
        if ($toOthers && method_exists($event, 'dontBroadcastToCurrentUser')) {
            $event->dontBroadcastToCurrentUser();
        }

        try {
            app(Dispatcher::class)->dispatch($event);
        } catch (BroadcastException $exception) {
            Log::warning('Broadcast delivery failed.', [
                'event' => $event::class,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
