<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LinkAccountMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only redirect if the portal is configured to allow matching accounts by invoice
        if (config('portal.allow-invoice-match')){
            $user = $request->user();
            if ($user && $user->client_id === null){
                return redirect()->route('user.link-account');
            }
        }

        return $next($request);
    }
}
