<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restricts the personal app to the owner. Arena participants are bounced to
 * the shared-challenges area, so friends can never reach the owner's data.
 */
class EnsureOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->isOwner()) {
            return redirect()->route('arena.index');
        }

        return $next($request);
    }
}
