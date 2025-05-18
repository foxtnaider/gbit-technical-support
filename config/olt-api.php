<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de la API OLT
    |--------------------------------------------------------------------------
    |
    | Esta configuración define los parámetros de conexión a la API externa
    | que gestiona las conexiones Telnet a dispositivos OLT.
    |
    */

    'host' => env('API_OLT_HOST', '127.0.0.1'),
    'port' => env('API_OLT_PORT_API', '3000'),
];
