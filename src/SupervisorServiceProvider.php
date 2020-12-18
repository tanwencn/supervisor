<?php

namespace Tanwencn\Supervisor;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SupervisorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config' => config_path(),
                __DIR__ . '/../resources/assets' => public_path('vendor/laravel-elfinder'),
            ], 'supervisor');
        }

        $this->registerRoutes();
        $this->registerResources();
        $this->defineAssetPublishing();
        $this->authorization();
    }

    /**
     * Define the asset publishing configuration.
     *
     * @return void
     */
    public function defineAssetPublishing()
    {
        $this->publishes([
            __DIR__ . '/public' => public_path('vendor/supervisor'),
        ], 'supervisor');
    }

    /**
     * Register the supervisor routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'domain' => config('supervisor.domain', null),
            'prefix' => config('supervisor.path'),
            'namespace' => 'Tanwencn\supervisor\Http\Controllers',
            'middleware' => config('supervisor.middleware', 'web'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    /**
     * Register the supervisor resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'supervisor');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->offerPublishing();
    }

    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/supervisor.php', 'supervisor'
        );
    }

    /**
     * Setup the resource publishing groups for Horizon.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/supervisor.php' => config_path('supervisor.php'),
            ], 'supervisor-config');
        }
    }

    /**
     * Configure the Supervisor authorization services.
     *
     * @return void
     */
    protected function authorization()
    {
        Supervisor::auth(function ($request) {
            return Gate::check('viewSupervisor', [$request->user()]);
        });
    }
}
