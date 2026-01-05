@extends('layouts.app')

@section('title', __('blog.title'))
@section('description', __('blog.description'))

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

<!-- Blog Grid -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Articles -->
            <div class="col-lg-8">
                @if($articles->count() > 0)
                    <div class="row g-4">
                        @foreach($articles as $article)
                            <div class="col-md-6">
                                <div class="article-card card h-100 border-0 shadow-sm">
                                    <img src="{{ $article->image ?? asset('images/article-placeholder.jpg') }}"
                                         alt="{{ $article->title }}"
                                         class="card-img-top">
                                    <div class="card-body">
                                        <div class="article-meta text-muted small mb-2">
                                            <span><i class="fas fa-calendar"></i> {{ $article->created_at->format('d M Y') }}</span>
                                            @if($article->category)
                                                <span class="ms-3"><i class="fas fa-folder"></i> {{ $article->category->name }}</span>
                                            @endif
                                        </div>
                                        <h5 class="card-title fw-bold">
                                            <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'article' => $article->slug]) }}" class="text-dark">
                                                {{ $article->title }}
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted">{{ Str::limit($article->excerpt, 120) }}</p>
                                    </div>
                                    <div class="card-footer bg-transparent border-0">
                                        <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'article' => $article->slug]) }}"
                                           class="btn btn-link p-0 text-primary">
                                            {{ __('blog.read_more') }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $articles->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                        <h4>{{ __('blog.no_articles') }}</h4>
                        <p class="text-muted">{{ __('blog.no_articles_text') }}</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories -->
                @if($categories->count() > 0)
                    <div class="sidebar-widget card mb-4">
                        <div class="card-header">
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
                @endif

                <!-- Recent Articles -->
                @if($recentArticles->count() > 0)
                    <div class="sidebar-widget card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('blog.recent_articles') }}</h5>
                        </div>
                        <div class="card-body">
                            @foreach($recentArticles as $recent)
                                <div class="recent-article d-flex mb-3">
                                    <img src="{{ $recent->image ?? asset('images/article-placeholder.jpg') }}"
                                         alt="{{ $recent->title }}"
                                         class="rounded me-3" style="width: 80px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'article' => $recent->slug]) }}" class="text-dark">
                                                {{ Str::limit($recent->title, 50) }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $recent->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
