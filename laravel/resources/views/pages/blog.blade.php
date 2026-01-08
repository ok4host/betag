@extends('layouts.app')

@section('title', __('blog.title'))

@section('content')
<!-- Page Header -->
<section class="page-header py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">{{ __('blog.title') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('nav.blog') }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Blog Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Search -->
                <div class="search-box mb-4">
                    <form action="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="{{ __('blog.search_placeholder') }}" value="{{ request('q') }}">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Articles -->
                @if($articles->count() > 0)
                    <div class="row g-4">
                        @foreach($articles as $article)
                            <div class="col-md-6">
                                <div class="card h-100 blog-card">
                                    <img src="{{ $article->image ?? asset('images/article-placeholder.jpg') }}" class="card-img-top" alt="{{ $article->title }}">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <span class="badge bg-primary">{{ $article->category?->name }}</span>
                                            <small class="text-muted ms-2">{{ $article->created_at->format('d/m/Y') }}</small>
                                        </div>
                                        <h5 class="card-title">
                                            <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $article->slug]) }}">
                                                {{ $article->title }}
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted">{{ Str::limit($article->excerpt, 100) }}</p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $article->slug]) }}" class="btn btn-outline-primary btn-sm">
                                            {{ __('blog.read_more') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $articles->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                        <h4>{{ __('blog.no_articles') }}</h4>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('blog.categories') }}</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($categories as $category)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="{{ route('blog.index', ['locale' => app()->getLocale(), 'category' => $category->slug]) }}">
                                    {{ $category->name }}
                                </a>
                                <span class="badge bg-primary rounded-pill">{{ $category->articles_count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Popular Articles -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('blog.popular') }}</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        @foreach($popularArticles as $popular)
                            <li class="list-group-item">
                                <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $popular->slug]) }}">
                                    {{ $popular->title }}
                                </a>
                                <small class="d-block text-muted">{{ $popular->views_count }} {{ __('blog.views') }}</small>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
