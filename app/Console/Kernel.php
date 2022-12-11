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

        $schedule->call(function () {
            if(DB::table('products')->where('created_at', '<', now()->addDays(30))->count() > 0) {
                DB::table('products')->update(['new' => 1]);
            }
        })->everyFourHours();

        // $schedule->call(function () {
        //     if(DB::table('orders')->where('created_at', '<', now()->addMinutes(15))->count() > 0 && DB::table('orders')->where('paid', 2)->count() > 0) {
        //         DB::table('orders')->where('paid', 2)->update(['paid' => 4]);
        //         DB::table('orders')->where('status', 1)->update(['status' => 5]);
        //         DB::table('orders')->where('token_id')->update(['token_id' => null]);
        //     }
        // })->everyFifteenMinutes();
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
