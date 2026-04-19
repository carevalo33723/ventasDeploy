<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // 🔔 Ejecutar verificación de vencimientos automáticamente
        $schedule->command('productos:vencimiento')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Registrar comandos manualmente
     */
    protected $commands = [
        \App\Console\Commands\VerificarVencimientos::class,
    ];
}