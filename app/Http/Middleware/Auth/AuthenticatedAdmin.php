<?php

namespace App\Http\Middleware\Auth;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthenticatedAdmin
{

    public function handle($request, Closure $next) 
    {
        $user = auth()->user()->user_type_id;
        if($user != 1){
            $response['access'] = "denied";
            return response()->json([$response], 403);
        }

        return $next($request);
    }
}

