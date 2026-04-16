<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authUser = $request->session()->get('auth_user');

        if ($authUser && $authUser['role'] === 'participant') {
            $participantQuery = \App\Models\User::query()->where('role', 'peserta');

            if (!empty($authUser['id'])) {
                $participantQuery->where('id', (int) $authUser['id']);
            } else {
                $participantQuery->where('username', (string) ($authUser['username'] ?? ''));
            }

            $dbUser = $participantQuery->first();

            if ($dbUser && !$dbUser->password_changed) {
                $currentRoute = $request->route()->getName();

                if ($currentRoute !== 'dashboard.participant.profile' && $currentRoute !== 'dashboard.participant.profile.update' && $currentRoute !== 'logout') {
                    return redirect()->route('dashboard.participant.profile')
                        ->with('force_password_change', true);
                }
            }
        }

        return $next($request);
    }
}
