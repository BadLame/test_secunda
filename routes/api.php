<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/')
    ->name('api.')
    ->group(function () {
        Route::name('auth-test')->get('/auth-test', function (): Response {
            return response()->noContent();
        });


    });
