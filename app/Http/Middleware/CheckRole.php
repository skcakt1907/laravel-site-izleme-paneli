<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Kullanıcının belirtilen rollerden birine sahip olup olmadığını kontrol eder.
     * Kullanım: middleware('role:super_admin,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole(...$roles)) {
            abort(403, 'Bu sayfaya erişim yetkiniz bulunmamaktadır.');
        }

        return $next($request);
    }
}
