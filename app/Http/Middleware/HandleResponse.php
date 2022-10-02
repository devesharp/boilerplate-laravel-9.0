<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HandleResponse
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
        /** @var JsonResponse $newRequest */
        $newRequest = $next($request);

        if ($request->is('v1/*')) {
            if ($newRequest instanceof \Illuminate\Http\Response) {
                return response()->json([
                    'success' => true,
                    'data' => $newRequest->getContent(),
                ]);
            } elseif ($newRequest instanceof \Illuminate\Http\JsonResponse) {
                $newRequest->setData([
                    'success' => ! isset($newRequest->getData()->error),
                    'data' => $newRequest->getData(),
                ]);
            }
        }

        return $newRequest;
    }
}
