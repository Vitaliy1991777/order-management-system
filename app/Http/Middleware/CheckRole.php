<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Проверяем, совпадает ли роль залогиненного пользователя
        // с той ролью, которая требуется для доступа к странице ($role)
        if ($request->user()->role !== $role) {
            // Если роли не совпадают, просто перенаправляем на главную
            return redirect('/dashboard');
        }

        // Если роли совпали, пропускаем пользователя дальше
        return $next($request);
    }
}