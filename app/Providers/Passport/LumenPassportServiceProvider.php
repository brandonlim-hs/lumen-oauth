<?php

namespace App\Providers\Passport;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class LumenPassportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Bind Illuminate\Database\Connection to Lumen's database connection.
        $this->app->singleton(Connection::class, function() {
            return $this->app['db.connection'];
        });
    }
}
