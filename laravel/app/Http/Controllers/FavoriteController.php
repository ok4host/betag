<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $favorites = auth()->user()
            ->favorites()
            ->with(['category', 'location'])
            ->active()
            ->latest('favorites.created_at')
            ->paginate(12);

        return view('pages.favorites', compact('favorites'));
    }

    public function toggle(Property $property)
    {
        $user = auth()->user();

        if ($user->favorites()->where('property_id', $property->id)->exists()) {
            $user->favorites()->detach($property->id);
            $message = __('messages.removed_from_favorites');
            $status = false;
        } else {
            $user->favorites()->attach($property->id);
            $message = __('messages.added_to_favorites');
            $status = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'favorited' => $status,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    public function count()
    {
        return response()->json([
            'count' => auth()->user()->favorites()->count(),
        ]);
    }
}
