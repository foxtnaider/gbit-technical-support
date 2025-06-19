<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar la verificación del estado de los dispositivos
// withoutOverlapping() evita que se ejecute si la tarea anterior aún está en ejecución
Schedule::command('devices:check-status')->everyThreeMinutes()->withoutOverlapping();
