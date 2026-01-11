<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $query = Property::with(['category', 'location', 'user']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title_ar', 'like', "%{$request->search}%")
                  ->orWhere('title_en', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $properties = $query->latest()->paginate(15);
        $categories = Category::all();

        return view('admin.properties.index', compact('properties', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        $locations = Location::all();
        return view('admin.properties.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'nullable|string',
            'type' => 'required|in:sale,rent',
            'price' => 'required|numeric|min:0',
            'area' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:pending,active,sold,rented,rejected',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['title_ar']) . '-' . time();
        $validated['user_id'] = auth()->id();
        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('properties', 'public');
        }

        Property::create($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'تم إضافة العقار بنجاح');
    }

    public function edit(Property $property)
    {
        $categories = Category::all();
        $locations = Location::all();
        return view('admin.properties.edit', compact('property', 'categories', 'locations'));
    }

    public function update(Request $request, Property $property)
    {
        $validated = $request->validate([
            'title_ar' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'nullable|string',
            'type' => 'required|in:sale,rent',
            'price' => 'required|numeric|min:0',
            'area' => 'nullable|numeric|min:0',
            'bedrooms' => 'nullable|integer|min:0',
            'bathrooms' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'required|exists:locations,id',
            'featured_image' => 'nullable|image|max:2048',
            'status' => 'required|in:pending,active,sold,rented,rejected',
            'is_featured' => 'boolean',
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('properties', 'public');
        }

        $property->update($validated);

        return redirect()->route('admin.properties.index')
            ->with('success', 'تم تحديث العقار بنجاح');
    }

    public function destroy(Property $property)
    {
        $property->delete();
        return redirect()->route('admin.properties.index')
            ->with('success', 'تم حذف العقار بنجاح');
    }

    public function toggleFeatured(Property $property)
    {
        $property->update(['is_featured' => !$property->is_featured]);
        return back()->with('success', 'تم تحديث حالة التمييز');
    }

    public function updateStatus(Request $request, Property $property)
    {
        $property->update(['status' => $request->status]);
        return back()->with('success', 'تم تحديث الحالة');
    }
}
