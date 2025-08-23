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
        
        // Skip for admin users (with error handling)
        try {
            if ($user->hasRole('admin')) {
                return $next($request);
            }
        } catch (\Exception $e) {
            // If role checking fails, log the error and continue
            \Log::warning('Role checking failed in EnsureTenantScope middleware', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }

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
        // Add global scopes for multi-tenant models
        \App\Models\Restaurant::addGlobalScope('tenant', function ($builder) use ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        });

        \App\Models\Menu::addGlobalScope('tenant', function ($builder) use ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        });

        \App\Models\User::addGlobalScope('tenant', function ($builder) use ($tenantId) {
            if (!app()->runningInConsole()) {
                $builder->where('tenant_id', $tenantId);
            }
        });
    }
}
