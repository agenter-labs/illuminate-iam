<?php

namespace AgenterLab\IAM;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Authorizable;

class IamServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {;
        $this->registerPermissionsToGate();
        
        $root = dirname(__DIR__);
        $this->loadMigrationsFrom($root . '/database/migrations');
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
        $this->registerIam();
        $this->registerCommands();
    }

    /**
     * Setup the configuration for iam.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/iam.php', 'iam');
    }

    /**
     * Setup the resource publishing group for Laratrust.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../resources/permissions/' => resource_path('permissions'),
            ]);
        }
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    protected function registerIam()
    {
        $this->app->singleton('iam', function ($app) {
            return new Iam($app);
        });
    }

    /**
     * Register the Laratrusts commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \AgenterLab\IAM\Console\PermissionSeed::class
            ]);
        }
    }

    /**
     * Register permissions to Laravel Gate
     *
     * @return void
     */
    protected function registerPermissionsToGate()
    {
        if (!$this->app['config']->get('iam.permissions_as_gates')) {
            return;
        }

        app(Gate::class)->before(function (Authorizable $user, string $ability) {
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability) ?: null;
            }
        });
    }
}
