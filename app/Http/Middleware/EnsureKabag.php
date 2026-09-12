<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureKabag
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (
            strtolower(
                trim((string) $user->role)
            ) !== 'kabag'
        ) {
            abort(403);
        }

        if (! $user->is_active) {
            abort(403);
        }

        return $next($request);
    }
}
