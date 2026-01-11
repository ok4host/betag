<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use App\Models\Lead;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'properties' => Property::count(),
            'active_properties' => Property::where('status', 'active')->count(),
            'featured_properties' => Property::where('is_featured', true)->count(),
            'categories' => Category::count(),
            'locations' => Location::count(),
            'users' => User::count(),
            'leads' => Lead::count(),
            'new_leads' => Lead::where('status', 'new')->count(),
        ];

        $latestProperties = Property::with(['category', 'location'])
            ->latest()
            ->take(5)
            ->get();

        $latestLeads = Lead::with('property')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestProperties', 'latestLeads'));
    }
}
