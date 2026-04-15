<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSimpleApiAuthenticated
{
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        if (! $request->session()->has('auth_user')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Silakan login terlebih dahulu.',
                'data' => null,
            ], 401);
        }

        return $next($request);
    }
}
