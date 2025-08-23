<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request, Menu $menu)
    {
        $user = $request->user();
        
        if ($menu->tenant_id !== $user->tenant_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $items = $menu->items()
            ->where('visible', true)
            ->where('available', true)
            ->with(['category', 'modifierGroups.modifiers'])
            ->get();

        return response()->json([
            'data' => $items,
            'meta' => [
                'menu' => $menu->name,
                'total' => $items->count(),
            ]
        ]);
    }
}
