<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Property;
use Illuminate\Http\Request;

class CompoundController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::compounds()->active()->with('parent');

        // Filter by parent location
        if ($request->filled('location')) {
            $query->whereHas('parent', fn($q) => $q->where('slug', $request->location));
        }

        // Filter by developer
        if ($request->filled('developer')) {
            $query->where('developer', 'like', "%{$request->developer}%");
        }

        // Search
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name_ar', 'like', "%{$request->q}%")
                    ->orWhere('name_en', 'like', "%{$request->q}%");
            });
        }

        $compounds = $query->withCount('properties')
            ->orderBy('is_featured', 'desc')
            ->orderBy('name_ar')
            ->paginate(12);

        $locations = Location::active()
            ->whereIn('type', ['city', 'area'])
            ->orderBy('name_ar')
            ->get();

        return view('pages.compounds', compact('compounds', 'locations'));
    }

    public function show($slug)
    {
        $compound = Location::compounds()
            ->active()
            ->where('slug', $slug)
            ->with('parent')
            ->firstOrFail();

        $properties = Property::with(['category'])
            ->active()
            ->where('location_id', $compound->id)
            ->latest()
            ->paginate(12);

        return view('pages.compound', compact('compound', 'properties'));
    }
}
