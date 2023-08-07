<?php

namespace App\Console;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //ejecutar cada minuto
        $schedule->command('send:emails')->everyMinute();
        //ejecutar cada 4 horas		
        $schedule->command('cancel:order')->everyFourHours();
        //ejecutar cada 30 minutos
        $schedule->command('reserve:agent')->everyThirtyMinutes();
        //ejecutar comando cada dia a las 00:00
        $schedule->command('discount:agent')->daily();
        //ejecutar comando cada dia a las 12:00
        $schedule->command('stock:listen')->dailyAt('12:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
