<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author'])->published();

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Search
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title_ar', 'like', "%{$request->q}%")
                    ->orWhere('title_en', 'like', "%{$request->q}%")
                    ->orWhere('content_ar', 'like', "%{$request->q}%")
                    ->orWhere('content_en', 'like', "%{$request->q}%");
            });
        }

        $articles = $query->recent()->paginate(12);

        $categories = ArticleCategory::active()
            ->withCount(['articles' => fn($q) => $q->published()])
            ->get();

        $popularArticles = Article::published()
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        return view('pages.blog', compact('articles', 'categories', 'popularArticles'));
    }

    public function show($locale, $slug)
    {
        $article = Article::with(['category', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $article->incrementViews();

        $relatedArticles = Article::with('category')
            ->published()
            ->where('id', '!=', $article->id)
            ->where('category_id', $article->category_id)
            ->recent()
            ->take(3)
            ->get();

        return view('pages.article', compact('article', 'relatedArticles'));
    }
}
