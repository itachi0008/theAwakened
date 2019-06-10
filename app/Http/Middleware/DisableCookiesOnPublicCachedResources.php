<?php

namespace App\Http\Middleware;

use Closure;

class DisableCookiesOnPublicCachedResources
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $result = $next($request);
        $headers = $result->headers;
        if($headers->hasCacheControlDirective("public")) {
          foreach($headers->getCookies() as $cookie) {
            $headers->removeCookie($cookie->getName());
          }
        }
        return $result;
    }
}
