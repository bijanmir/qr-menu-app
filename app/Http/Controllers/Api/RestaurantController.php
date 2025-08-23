<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $restaurants = Restaurant::where('tenant_id', $user->tenant_id)
            ->where('active', true)
            ->with(['menus' => function($query) {
                $query->where('status', 'published');
            }])
            ->get();

        return response()->json([
            'data' => $restaurants,
            'meta' => [
                'total' => $restaurants->count(),
            ]
        ]);
    }
}
