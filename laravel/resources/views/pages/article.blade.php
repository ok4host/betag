@extends('layouts.app')

@section('title', $article->title)
@section('description', Str::limit(strip_tags($article->content), 160))
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

<!-- Article Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <article>
                    <!-- Featured Image -->
                    @if($article->image)
                        <img src="{{ $article->image }}" alt="{{ $article->title }}" class="img-fluid rounded-3 mb-4 w-100">
                    @endif

                    <!-- Meta -->
                    <div class="article-meta mb-4">
                        <span class="badge bg-primary">{{ $article->category?->name }}</span>
                        <span class="text-muted ms-3">
                            <i class="fas fa-calendar"></i> {{ $article->created_at->format('d/m/Y') }}
                        </span>
                        <span class="text-muted ms-3">
                            <i class="fas fa-eye"></i> {{ $article->views_count }} {{ __('blog.views') }}
                        </span>
                        @if($article->author)
                            <span class="text-muted ms-3">
                                <i class="fas fa-user"></i> {{ $article->author->name }}
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="fw-bold mb-4">{{ $article->title }}</h1>

                    <!-- Content -->
                    <div class="article-content">
                        {!! $article->content !!}
                    </div>

                    <!-- Share -->
                    <div class="article-share mt-5 pt-4 border-top">
                        <h5>{{ __('blog.share') }}</h5>
                        <div class="d-flex gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                               class="btn btn-outline-primary" target="_blank">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($article->title) }}"
                               class="btn btn-outline-info" target="_blank">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . url()->current()) }}"
                               class="btn btn-outline-success" target="_blank">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </article>

                <!-- Related Articles -->
                @if($relatedArticles->count() > 0)
                    <div class="related-articles mt-5">
                        <h4 class="fw-bold mb-4">{{ __('blog.related') }}</h4>
                        <div class="row g-4">
                            @foreach($relatedArticles as $related)
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <img src="{{ $related->image ?? asset('images/article-placeholder.jpg') }}" class="card-img-top" alt="{{ $related->title }}">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $related->slug]) }}">
                                                    {{ $related->title }}
                                                </a>
                                            </h6>
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
                <div class="sticky-top" style="top: 100px;">
                    <!-- CTA -->
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body text-center py-4">
                            <h5>{{ __('blog.looking_for_property') }}</h5>
                            <p class="mb-3">{{ __('blog.browse_properties') }}</p>
                            <a href="{{ route('properties.index', ['locale' => app()->getLocale()]) }}" class="btn btn-light">
                                {{ __('nav.properties') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
