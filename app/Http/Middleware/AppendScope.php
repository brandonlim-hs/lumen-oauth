<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class AppendScope
{
    /**
     * Handle an incoming request. Ignore any scope within the request payload provided by the user.
     * Pre-defined default scope value is attached to the request instead.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $scope = Input::get('client_id') ? Config::get('scopes.clients.'.Input::get('client_id')) : '';
        $request->merge(array("scope" => $scope));

        return $next($request);
    }
}
