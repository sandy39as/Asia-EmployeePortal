<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyFaceLogToken
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $expectedToken = (string) config(
            'services.facelog.token'
        );

        if ($expectedToken === '') {
            return response()->json([
                'success' => false,
                'message' => 'FaceLog API token belum dikonfigurasi.',
            ], 500);
        }

        $receivedToken = (string) $request->bearerToken();

        if (
            $receivedToken === ''
            ||
            ! hash_equals(
                $expectedToken,
                $receivedToken
            )
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
