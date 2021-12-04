<?php

namespace AgenterLab\IAM;

class Iam {

    /**
     * Illuminate application.
     *
     * @var \Laravel\Lumen\Application
     */
    public $app;

    /**
     * Create a new confide instance.
     *
     * @param  \Laravel\Lumen\Application $app
     * @return void
     */
    public function __construct(\Laravel\Lumen\Application $app)
    {
        $this->app = $app;
    }

    /**
     * Get the currently authenticated user or null.
     *
     * @return \Illuminate\Auth\UserInterface|null
     */
    public function user()
    {
        return $this->app->auth->user();
    }
}