<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    function boot(): void
    {
        Scramble::configure()
            ->withDocumentTransformers(
                fn (OpenApi $openApi) => $openApi->secure(SecurityScheme::http('bearer'))
            );
    }
}
