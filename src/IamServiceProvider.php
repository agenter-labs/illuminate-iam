<?php

namespace AgenterLab\IAM;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class IamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->configure();
        $this->registerIam();
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
}
