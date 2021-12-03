<?php

namespace Fearless\Tool\Provider;


use Fearless\Tool\Console\Build;
use Fearless\Tool\Console\Comment;
use Illuminate\Support\ServiceProvider;

class ScaffoldServiceProvider extends ServiceProvider
{
    protected $commands = [
        Build::class,
        Comment::class
    ];
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
