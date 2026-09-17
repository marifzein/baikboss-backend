<?php

namespace App\Http\Middleware;

use App\Models\AuthToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $plain = $request->bearerToken();

        if (! $plain) {
            return response()->json(['message' => 'Silakan login terlebih dahulu.'], 401);
        }

        $token = AuthToken::with('user')->where('token', hash('sha256', $plain))->first();

        if (! $token) {
            return response()->json(['message' => 'Sesi tidak valid, silakan login lagi.'], 401);
        }

        $token->forceFill(['last_used_at' => now()])->saveQuietly();
        $request->setUserResolver(fn () => $token->user);

        return $next($request);
    }
}
