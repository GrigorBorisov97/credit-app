<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyNewCredit
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
        $payload = $request->input();

        if (strlen($payload['userName']) < 3) {
            return response()->json([
                'status' => 'failed',
                'message' => 'The name should be more than 3 characters',
            ]);
        }

        if ($payload['returnPeriod'] < 3 || $payload['returnPeriod'] > 120) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Falsy data for return period',
            ]);
        }

        if ($payload['creditAmount'] < 1 || $payload['creditAmount'] > 80000) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Credit should not exceed 80000',
            ]);
        }
        
        if (!isset($payload['creditAmount']) || !isset($payload['returnPeriod']) || !isset($payload['userName'])) {
            abort(401, 'Unauthorized request');
        }

        return $next($request);
    }
}
