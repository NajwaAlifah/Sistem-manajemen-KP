<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $roles = $roles ?: ['admin'];
        if (!$request->user() || !in_array($request->user()->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }
        return $next($request);
    }
}
