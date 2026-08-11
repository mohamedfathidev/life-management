<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates sensitive sections (Diary + Recovery) behind the privacy PIN.
 * If the user set a PIN and hasn't unlocked this session, redirect to unlock.
 */
class EnsurePrivacyUnlocked
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasPin() && ! $request->session()->get('privacy_unlocked')) {
            $request->session()->put('url.intended', $request->fullUrl());

            return redirect()->route('privacy.unlock');
        }

        return $next($request);
    }
}
