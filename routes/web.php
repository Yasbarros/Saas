<?php

use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        // As rotas centrais são registradas aqui
    });
}

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
