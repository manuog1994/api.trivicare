<?php

namespace App\Listeners;

use Carbon\Carbon;
use App\Events\MyEvent;
use App\Models\EventNot;
use ExponentPhpSDK\Expo;
use Illuminate\Support\Facades\Http;
use ParagonIE\Sodium\Core\Curve25519\H;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendExpoNotification
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
     * @param  \App\Events\MyEvent  $event
     * @return void
     */
    public function handle(MyEvent $event)
    {
        $new = EventNot::all()->last();

        $expo = Expo::normalSetup();
        $channelName = 'my-channel';

        $expoTokens = \App\Models\ExpoToken::all();

        foreach ($expoTokens as $expoToken) {
            $expo->subscribe($channelName, $expoToken->token);

            $notification = ['body' => 
                $new->description,
                'title' => $new->title,
                'sound' => 'default',
                'priority' => 'high',
            ];

            $expo->notify([$channelName], $notification);
        }
    }
}
