<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Sentry\State\Scope;

class SentryContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && app()->bound('sentry') && ! empty(env('SENTRY_LARAVEL_DSN'))) {
            \Sentry\configureScope(function (Scope $scope): void {
                $scope->setUser([
                    'id' => auth()->user()->id,
                    'email' => auth()->user()->email,
                ]);
            });
        }

        return $next($request);
    }
}
