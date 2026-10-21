<?php

use Illuminate\Support\Facades\Route;
use Modules\Client\Http\Controllers\ClientController;

Route::prefix('v1')->group(function () {
    Route::get('clients/search', [ClientController::class, 'search'])->name('clients.search');
    Route::apiResource('clients', ClientController::class)->names('client');
});
