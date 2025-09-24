<?php

use App\Http\Controllers\BuildingsController;
use App\Http\Controllers\CompaniesController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::prefix('/v1')->name('api.')->group(function () {
    Route::name('auth-test')->get('/auth-test', fn (): Response => response()->noContent());

    Route::name('company.')->prefix('/company')->group(function () {
        Route::get('/{company}', [CompaniesController::class, 'find'])->name('find');
    });

    Route::name('building.')->prefix('/building')->group(function () {
        Route::get('/', [BuildingsController::class, 'list'])->name('list');
    });
});
