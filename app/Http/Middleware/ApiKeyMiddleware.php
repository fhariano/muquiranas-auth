<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class ApiKeyMiddleware 
{
  public function handle($request, Closure $next)
  {
    dd($request);

    if(!$key = $request->get('apikey') or ($key !== config('acl.api_keys.web') and $key !== config('acl.api_keys.mobile'))){
      throw new AuthenticationException('Wrong api key');
    }
  }
}