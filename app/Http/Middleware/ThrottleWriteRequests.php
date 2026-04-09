<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ThrottleWriteRequests
{
    /**
     * Block all non-GET requests for 30 seconds after the last write request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->method() === 'GET') {
            return $next($request);
        }

        $lastWriteAt = session('last_write_request_at');
        $cooldown = 30;

        if ($lastWriteAt) {
            $elapsed = now()->diffInSeconds($lastWriteAt);
            $remaining = $cooldown - $elapsed;

            if ($remaining > 0) {
                $message = "Please wait {$remaining} seconds before submitting another request.";

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

        session(['last_write_request_at' => now()]);

        return $response;
    }
}
