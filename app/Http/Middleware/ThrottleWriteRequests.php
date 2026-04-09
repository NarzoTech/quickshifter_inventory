<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleWriteRequests
{
    /**
     * Block duplicate non-GET requests to the same route within 30 seconds.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->method() === 'GET') {
            return $next($request);
        }

        $routeKey = $request->method() . '|' . $request->path();
        $sessionKey = 'throttle_write_' . md5($routeKey);
        $cooldown = 30;

        $lastRequestAt = session($sessionKey);

        if ($lastRequestAt) {
            $elapsed = now()->diffInSeconds($lastRequestAt);
            $remaining = $cooldown - $elapsed;

            if ($remaining > 0) {
                $message = "Please wait {$remaining} seconds before submitting this request again.";

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'message' => $message,
                        'alert-type' => 'error',
                        'throttled' => true,
                        'remaining_seconds' => $remaining,
                    ], 429);
                }

                return redirect()->back()->with([
                    'messege' => $message,
                    'alert-type' => 'error',
                ]);
            }
        }

        $response = $next($request);

        session([$sessionKey => now()]);

        return $response;
    }
}
