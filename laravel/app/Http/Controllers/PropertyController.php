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
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        // Filter by bathrooms
        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', '>=', $request->bathrooms);
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $properties = $query->paginate(12)->withQueryString();

        $categories = Category::active()->ordered()->get();
        $locations = Location::active()->whereIn('type', ['city', 'area'])->get();

        return view('properties.index', compact('properties', 'categories', 'locations'));
    }

    public function sale(Request $request)
    {
        $query = Property::with(['category', 'location'])
            ->active()
            ->where('type', 'sale');

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('location')) {
            $query->whereHas('location', fn($q) => $q->where('slug', $request->location));
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $properties = $query->paginate(12)->withQueryString();
        $categories = Category::active()->ordered()->get();
        $locations = Location::active()->whereIn('type', ['city', 'area'])->get();

        return view('properties.index', compact('properties', 'categories', 'locations'));
    }

    public function rent(Request $request)
    {
        $query = Property::with(['category', 'location'])
            ->active()
            ->where('type', 'rent');

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('location')) {
            $query->whereHas('location', fn($q) => $q->where('slug', $request->location));
        }
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            default => $query->latest(),
        };

        $properties = $query->paginate(12)->withQueryString();
        $categories = Category::active()->ordered()->get();
        $locations = Location::active()->whereIn('type', ['city', 'area'])->get();

        return view('properties.index', compact('properties', 'categories', 'locations'));
    }

    public function show($locale, $slug)
    {
        $property = Property::with(['category', 'location', 'user'])
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        // Increment views
        $property->increment('views');

        $relatedProperties = Property::with(['category', 'location'])
            ->active()
            ->where('id', '!=', $property->id)
            ->where('category_id', $property->category_id)
            ->take(4)
            ->get();

        return view('properties.show', compact('property', 'relatedProperties'));
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
