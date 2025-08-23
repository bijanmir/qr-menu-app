<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        // Check if user can rate this item
        if (auth()->check() && !auth()->user()->canRateItem($item->id)) {
            return response()->json(['error' => 'You cannot rate this item'], 422);
        }

        $rating = Rating::updateOrCreate(
            [
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'ip_address' => $request->ip(),
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        if (request()->headers->get('HX-Request')) {
            return view('partials.rating-success', compact('rating', 'item'));
        }

        return response()->json(['success' => true, 'rating' => $rating]);
    }
}
