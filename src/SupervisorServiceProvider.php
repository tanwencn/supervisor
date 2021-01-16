<?php

namespace Tanwencn\Supervisor;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SupervisorServiceProvider extends ServiceProvider
{
    public function boot()
    {
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
            SUPERVISOR_PATH . '/public' => public_path('vendor/supervisor'),
        ], 'supervisor-assets');
    }

    /**
     * Register the supervisor routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        \Route::group([
            'domain' => config('supervisor.domain', null),
            'prefix' => config('supervisor.path'),
            'namespace' => 'Tanwencn\Supervisor\Http\Controllers',
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
     * Register the Horizon Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
            if($this->app->isLocal()) {
                $this->commands([
                    Console\TestBuildCommand::class,
                ]);
            }
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!defined('SUPERVISOR_PATH')) {
            define('SUPERVISOR_PATH', realpath(__DIR__ . '/../'));
        }

        $this->configure();
        $this->offerPublishing();
        $this->registerCommands();
    }

    protected function configure()
    {
        $this->mergeConfigFrom(
            SUPERVISOR_PATH.'/config/supervisor.php', 'supervisor'
        );

        // if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
        //     $config = $this->app->make('config');

        //     $config->set('filesystems.disks', array_merge(
        //         $config->get('supervisor.disks', []), $config->get('filesystems.disks', [])
        //     ));
        // }
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
            return app()->environment('local') || Gate::check('viewSupervisor', [$request->user()]);
        });
    }
}
