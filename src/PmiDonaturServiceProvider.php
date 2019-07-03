<?php

namespace BajakLautMalaka\PmiDonatur;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Routing\RouteRegistrar;

class PmiDonaturServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Factory $factory,RouteRegistrar $routeRegistrar)
    {
        $this->mergeAuthConfig();
        $this->loadConfig();
        $this->loadMigrationsAndFactories($factory);
        $this->loadRoutes($routeRegistrar);
    }

    /**
     * Merger any auth config from package donator.
     *
     * @return void
     */
    private function mergeAuthConfig()
    {
        /** @var \Illuminate\Config\Repository */
        $config = $this->app['config'];

        $original = $config->get('auth', []);
        $toMerge = require __DIR__ . '/../config/auth.php';

        $auth = [];
        foreach ($original as $key => $value) {
            $auth[$key] = $value;
            if (isset($toMerge[$key])) {
                $auth[$key] = array_merge($value, $toMerge[$key]);
            }
        }

        $config->set('auth', $auth);
    }

    /**
     * Register any load config.
     *
     * @return void
     */
    private function loadConfig()
    {
        $path = __DIR__ . '/../config/donator.php';
        $this->mergeConfigFrom($path, 'donator');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $path => config_path('donator.php'),
            ], 'donator:config');
        }
    }

    /**
     * Register any load migrations & factories from package donators.
     *
     * @return void
     */
    private function loadMigrationsAndFactories(Factory $factory): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

            $factory->load(__DIR__ . '/../database/factories');
        }
    }
    /**
     * Register any load routes
     *
     * @param RouteRegistrar $routeRegistrar
     * @return void
     */
    private function loadRoutes(RouteRegistrar $routeRegistrar): void
    {
        $routeRegistrar->prefix('api')
            ->namespace('BajakLautMalaka\PmiDonatur\Http\Controllers\Api')
            ->middleware(['api'])
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            });
    }
}
