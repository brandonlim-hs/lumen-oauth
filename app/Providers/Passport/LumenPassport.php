<?php

namespace App\Providers\Passport;

use Laravel\Lumen\Application;
use Laravel\Lumen\Routing\Router;

class LumenPassport
{
    /**
     * Get a Passport route registrar.
     *
     * @param  callable|Router|Application  $callback
     * @param  array  $options
     * @return RouteRegistrar
     */
    public static function routes($callback = null, array $options = [])
    {
        if ($callback instanceof Application && preg_match('/5\.5\..*/', $callback->version())) $callback = $callback->router;

        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'prefix' => 'oauth',
            'namespace' => '\Laravel\Passport\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        $callback->group(array_except($options, ['namespace']), function ($router) use ($callback, $options) {
            $routes = new RouteRegistrar($router, $options);
            $routes->all();
        });
    }
}
