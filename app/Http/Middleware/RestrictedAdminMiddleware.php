<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestrictedAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Auth::guard('admin')->user();
        
        // Vérifier si c'est l'utilisateur restreint
        if ($admin && $admin->email === 'r.31N3-35Gis@admin.com') {
            // Autoriser seulement l'accès au dashboard simple
            $allowedRoutes = [
                'simpleDashboard',
                'admin.logout'
            ];
            
            $currentRoute = $request->route()->getName();
            
            // Si ce n'est pas une route autorisée, rediriger vers le dashboard simple
            if (!in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('simpleDashboard')
                    ->with('error', 'Accès non autorisé à cette section.');
            }
        }
        
        return $next($request);
    }
}