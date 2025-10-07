<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$types): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->is_active) {
            abort(403, 'Votre compte a été désactivé.');
        }

        if (! empty($types) && ! in_array($user->user_type, $types)) {
            abort(403, 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
        }

        return $next($request);
    }
}
