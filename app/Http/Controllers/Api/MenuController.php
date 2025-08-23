<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request, Restaurant $restaurant)
    {
        $user = $request->user();
        
        if ($restaurant->tenant_id !== $user->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $menus = $restaurant->menus()
            ->where('status', 'published')
            ->with(['categories.items'])
            ->get();

        return response()->json([
            'data' => $menus,
            'meta' => [
                'restaurant' => $restaurant->name,
                'total' => $menus->count(),
            ]
        ]);
    }
}
