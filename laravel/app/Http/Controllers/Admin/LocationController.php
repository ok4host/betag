<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('properties')->orderBy('type')->orderBy('name_ar')->paginate(20);
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        $cities = Location::where('type', 'city')->get();
        $areas = Location::where('type', 'area')->get();
        return view('admin.locations.create', compact('cities', 'areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:city,area,compound',
            'parent_id' => 'nullable|exists:locations,id',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name_ar']) . '-' . time();
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('locations', 'public');
        }

        Location::create($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'تم إضافة الموقع بنجاح');
    }

    public function edit(Location $location)
    {
        $cities = Location::where('type', 'city')->where('id', '!=', $location->id)->get();
        $areas = Location::where('type', 'area')->where('id', '!=', $location->id)->get();
        return view('admin.locations.edit', compact('location', 'cities', 'areas'));
    }

    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'type' => 'required|in:city,area,compound',
            'parent_id' => 'nullable|exists:locations,id',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('locations', 'public');
        }

        $location->update($validated);

        return redirect()->route('admin.locations.index')
            ->with('success', 'تم تحديث الموقع بنجاح');
    }

    public function destroy(Location $location)
    {
        if ($location->properties()->count() > 0) {
            return back()->with('error', 'لا يمكن حذف موقع يحتوي على عقارات');
        }

        $location->delete();
        return redirect()->route('admin.locations.index')
            ->with('success', 'تم حذف الموقع بنجاح');
    }
}
