<?php

namespace App\Listeners;

use App\Events\MyEvent;
use App\Models\EventNot;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use ExponentPhpSDK\Expo;

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

        $recipient1 = 'ExponentPushToken[AGK2XVBEGhwLzEgwLqDokN]';
        $recipient2 = 'ExponentPushToken[kQWzJ0I36PBcuaj381Al0M]';

        $expo->subscribe($channelName, $recipient1);
        $expo->subscribe($channelName, $recipient2);

        $notification = ['body' => 
            $new->description,
            'title' => $new->title,
            'sound' => 'default',
            'priority' => 'high',
        ];
        
        // Aquí deberás obtener los tokens de los dispositivos a los que deseas enviar la notificación
        // $tokens = \App\Models\ExpoToken::pluck('token')->toArray();
    
        $expo->notify([$channelName], $notification);
    }
}
