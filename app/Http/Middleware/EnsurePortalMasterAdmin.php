<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalMasterAdmin
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        $email = strtolower(
            trim((string) $user->email)
        );

        if (
            $email !==
            'sandyramdani65@gmail.com'
        ) {
            abort(403);
        }

        return $next($request);
    }
}
