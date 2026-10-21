<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SPA Catch-All Route
|--------------------------------------------------------------------------
|
| Serve the Vue SPA's index.html for any route that doesn't match an API
| endpoint. This lets Vue Router handle client-side routing.
|
*/

Route::get('/{any?}', function () {
    return file_get_contents(public_path('index.html'));
})->where('any', '^(?!api/).*$')->name('spa');
