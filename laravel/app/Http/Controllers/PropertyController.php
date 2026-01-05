<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Category;
use App\Models\Location;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['category', 'location'])->active();

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->whereHas('location', fn($q) => $q->where('slug', $request->location));
        }

        // Filter by price range
        $query->priceRange($request->min_price, $request->max_price);

        // Filter by area range
        $query->areaRange($request->min_area, $request->max_area);

        // Filter by bedrooms
        if ($request->filled('bedrooms')) {
            $query->withBedrooms($request->bedrooms);
        }

        // Search by keyword
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->q}%")
                    ->orWhere('description', 'like', "%{$request->q}%")
                    ->orWhere('address', 'like', "%{$request->q}%");
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'area_low' => $query->orderBy('area', 'asc'),
            'area_high' => $query->orderBy('area', 'desc'),
            default => $query->latest(),
        };

        $properties = $query->paginate(12)->withQueryString();

        $categories = Category::active()->ordered()->get();
        $locations = Location::active()->whereIn('type', ['city', 'area'])->get();

        return view('pages.search', compact('properties', 'categories', 'locations'));
    }

    public function show($slug)
    {
        $property = Property::with(['category', 'location', 'user'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $property->incrementViews();

        $similarProperties = Property::with(['category', 'location'])
            ->active()
            ->where('id', '!=', $property->id)
            ->where('category_id', $property->category_id)
            ->take(4)
            ->get();

        return view('pages.property', compact('property', 'similarProperties'));
    }

    public function create()
    {
        $this->authorize('create', Property::class);

        $categories = Category::active()->ordered()->get();
        $locations = Location::active()->whereIn('type', ['city', 'area'])->get();

        return view('pages.add-property', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Property::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:sale,rent',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'floor' => 'nullable|integer|min:0',
            'address' => 'nullable|string|max:255',
            'features' => 'nullable|array',
            'owner_phone' => 'required|string|max:20',
            'owner_whatsapp' => 'nullable|string|max:20',
            'images.*' => 'nullable|image|max:5120',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . uniqid();
        $validated['status'] = 'pending';

        // Handle images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('properties', 'public');
                if ($index === 0) {
                    $validated['featured_image'] = $path;
                }
                $images[] = $path;
            }
            $validated['gallery'] = $images;
        }

        $property = Property::create($validated);

        return redirect()
            ->route('my-properties')
            ->with('success', __('messages.property_added'));
    }

    public function inquiry(Request $request, Property $property)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'message' => 'nullable|string|max:1000',
        ]);

        $validated['property_id'] = $property->id;
        $validated['user_id'] = auth()->id();
        $validated['source'] = 'property_page';

        Lead::create($validated);

        return back()->with('success', __('messages.inquiry_sent'));
    }

    public function myProperties()
    {
        $properties = auth()->user()
            ->properties()
            ->with(['category', 'location'])
            ->withCount('leads')
            ->latest()
            ->paginate(10);

        return view('pages.my-properties', compact('properties'));
    }
}
