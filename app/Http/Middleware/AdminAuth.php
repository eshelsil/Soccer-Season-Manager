<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\Authenticate;
use Closure;

class AdminAuth extends Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    protected function authenticate($request, array $guards)
    {
        parent::authenticate($request, $guards);
        if (!$this->auth->user()->isAdmin() ){
            $this->unauthenticated($request, $guards);
        }

    }

    protected function redirectTo($request)
    {
        return 'table';
    }
}
