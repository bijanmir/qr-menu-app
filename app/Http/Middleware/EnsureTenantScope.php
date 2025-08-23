<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTenantScope
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        
        // Role checking completely removed for now
        // Will re-add once Spatie Permission is properly installed

        // Add tenant scope to query builder
        if ($user->tenant_id) {
            app()->instance('current_tenant_id', $user->tenant_id);
            
            // Add global scope to models that have tenant_id
            $this->addTenantScopes($user->tenant_id);
        }

        return $next($request);
    }

    private function addTenantScopes(int $tenantId): void
    {
        // Only add scopes if models exist
        if (class_exists(\App\Models\Restaurant::class)) {
            \App\Models\Restaurant::addGlobalScope('tenant', function ($builder) use ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            });
        }

        if (class_exists(\App\Models\Menu::class)) {
            \App\Models\Menu::addGlobalScope('tenant', function ($builder) use ($tenantId) {
                $builder->where('tenant_id', $tenantId);
            });
        }

        if (class_exists(\App\Models\User::class)) {
            \App\Models\User::addGlobalScope('tenant', function ($builder) use ($tenantId) {
                if (!app()->runningInConsole()) {
                    $builder->where('tenant_id', $tenantId);
                }
            });
        }
    }
}