<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Re-locks the privacy PIN as soon as the user leaves the sensitive sections.
 * Combined with EnsurePrivacyUnlocked this means the PIN is required on *every*
 * fresh entry into Diary/Recovery — the unlock only lasts while you stay inside.
 *
 * We only act on real full-page GET loads (not Livewire component updates, which
 * carry the X-Livewire header) so in-page interactions don't drop the unlock.
 */
class RelockPrivacyWhenLeaving
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $routeName = $request->route()?->getName();

        if ($user
            && $user->hasPin()
            && $request->isMethod('GET')
            && ! $request->hasHeader('X-Livewire')
            && $routeName !== null
            && $routeName !== 'privacy.unlock'
            && ! str_starts_with($routeName, 'diary')
            && ! str_starts_with($routeName, 'recovery')
        ) {
            $request->session()->forget('privacy_unlocked');
        }

        return $next($request);
    }
}
