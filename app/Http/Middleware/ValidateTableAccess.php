<?php

namespace App\Http\Middleware;

use App\Models\Table;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ValidateTableAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Only validate for customer routes that require table context
        if (!$request->routeIs('customer.*')) {
            return $next($request);
        }

        $tableInfo = Session::get('current_table');
        
        // If no table info and this is an order-related action, require table
        if (!$tableInfo && in_array($request->route()->getActionMethod(), ['checkout', 'add', 'updateLine'])) {
            return response()->json(['error' => 'Table information required'], 422);
        }

        // Validate table exists and is active
        if ($tableInfo) {
            $table = Table::where('id', $tableInfo['table_id'])
                ->where('restaurant_id', $tableInfo['restaurant_id'])
                ->where('active', true)
                ->first();

            if (!$table) {
                Session::forget('current_table');
                return response()->json(['error' => 'Invalid table'], 422);
            }
        }

        return $next($request);
    }
}
