<?php

namespace App\Console\Commands;

use App\Models\EventNot;
use Illuminate\Console\Command;

class SendEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:event {event}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $event = $this->argument('event');
        // Crear un evento
        $notification = EventNot::create([
            'title' => $event,
            'description' => 'Evento creado desde consola',
        ]);
        
        event(new \App\Events\MyEvent($notification));
    }
}
