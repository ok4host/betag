<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Category;
use App\Models\Location;
use App\Models\Article;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProperties = Property::with(['category', 'location'])
            ->active()
            ->featured()
            ->latest()
            ->take(8)
            ->get();

        $latestProperties = Property::with(['category', 'location'])
            ->active()
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::active()
            ->withCount(['properties' => fn($q) => $q->active()])
            ->ordered()
            ->get();

        $popularLocations = Location::active()
            ->whereIn('type', ['city', 'area'])
            ->withCount(['properties' => fn($q) => $q->active()])
            ->orderByDesc('properties_count')
            ->take(6)
            ->get();

        $latestArticles = Article::with('category')
            ->published()
            ->recent()
            ->take(3)
            ->get();

        return view('pages.home', compact(
            'featuredProperties',
            'latestProperties',
            'categories',
            'popularLocations',
            'latestArticles'
        ));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }
}
