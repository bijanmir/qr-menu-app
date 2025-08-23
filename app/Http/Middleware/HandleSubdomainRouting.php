<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use Closure;
use Illuminate\Http\Request;

class HandleSubdomainRouting
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        
        // Check if this is a subdomain request
        if (preg_match('/^([^.]+)\.qrmenu\.app$/', $host, $matches)) {
            $subdomain = $matches[1];
            
            // Skip if it's a reserved subdomain
            $reserved = ['www', 'app', 'admin', 'api', 'mail', 'ftp'];
            if (in_array($subdomain, $reserved)) {
                abort(404);
            }

            // Find restaurant by subdomain
            $restaurant = Restaurant::where('subdomain', $subdomain)
                ->where('active', true)
                ->first();

            if (!$restaurant) {
                abort(404, 'Restaurant not found');
            }

            // Add restaurant to request
            $request->merge(['restaurant' => $restaurant]);
            $request->route()->setParameter('restaurant', $restaurant);
        }

        return $next($request);
    }
}
