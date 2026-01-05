@extends('layouts.app')

@section('title', $article->seo_title ?? $article->title)
@section('description', $article->seo_description ?? Str::limit($article->excerpt, 160))
@section('og_image', $article->image)

@section('content')
<!-- Breadcrumb -->
<section class="py-3 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}">{{ __('nav.blog') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ $article->title }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Article -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Article Content -->
            <div class="col-lg-8">
                <article class="blog-article">
                    <!-- Image -->
                    @if($article->image)
                        <img src="{{ $article->image }}" alt="{{ $article->title }}" class="img-fluid rounded-3 mb-4 w-100">
                    @endif

                    <!-- Meta -->
                    <div class="article-meta mb-4">
                        <span class="text-muted me-3">
                            <i class="fas fa-calendar"></i> {{ $article->created_at->format('d M Y') }}
                        </span>
                        @if($article->category)
                            <span class="text-muted me-3">
                                <i class="fas fa-folder"></i> {{ $article->category->name }}
                            </span>
                        @endif
                        <span class="text-muted">
                            <i class="fas fa-eye"></i> {{ $article->views }} {{ __('blog.views') }}
                        </span>
                    </div>

                    <!-- Title -->
                    <h1 class="fw-bold mb-4">{{ $article->title }}</h1>

                    <!-- Content -->
                    <div class="article-content">
                        {!! $article->content !!}
                    </div>

                    <!-- Share -->
                    <div class="article-share mt-5 pt-4 border-top">
                        <h5 class="fw-bold mb-3">{{ __('blog.share_article') }}</h5>
                        <div class="share-buttons">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                               class="btn btn-primary btn-sm" target="_blank">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}"
                               class="btn btn-info btn-sm text-white" target="_blank">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . url()->current()) }}"
                               class="btn btn-success btn-sm" target="_blank">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(url()->current()) }}&title={{ urlencode($article->title) }}"
                               class="btn btn-secondary btn-sm" target="_blank">
                                <i class="fab fa-linkedin-in"></i> LinkedIn
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Related Articles -->
                @if($relatedArticles->count() > 0)
                    <div class="related-articles mt-5">
                        <h3 class="fw-bold mb-4">{{ __('blog.related_articles') }}</h3>
                        <div class="row g-4">
                            @foreach($relatedArticles as $related)
                                <div class="col-md-6">
                                    <div class="article-card card h-100 border-0 shadow-sm">
                                        <img src="{{ $related->image ?? asset('images/article-placeholder.jpg') }}"
                                             alt="{{ $related->title }}"
                                             class="card-img-top">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'article' => $related->slug]) }}" class="text-dark">
                                                    {{ $related->title }}
                                                </a>
                                            </h6>
                                            <small class="text-muted">{{ $related->created_at->format('d M Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Recent Articles -->
                <div class="sidebar-widget card sticky-top" style="top: 100px;">
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
            </div>
        </div>
    </div>
</section>
@endsection
