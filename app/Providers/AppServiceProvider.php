<?php

namespace App\Providers;

use App\View\Composers\NavigationComposer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        View::composer('components.navigation-menu', NavigationComposer::class);

        Route::macro('softDeletes', function ($uri, $controller, $parameterName = null) {
            $parameterName = $parameterName ?: Str::singular($uri);

            Route::patch("{$uri}/{{$parameterName}}/restore", [$controller, 'restore'])
                ->name("{$uri}.restore")
                ->withTrashed();

            Route::delete("{$uri}/{{$parameterName}}/force", [$controller, 'forceDelete'])
                ->name("{$uri}.forceDelete")
                ->withTrashed();
        });
    }
}