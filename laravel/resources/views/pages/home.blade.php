@extends('layouts.app')

@section('title', __('site.home_title'))
@section('description', __('site.description'))

@section('content')
<!-- Hero Section -->
<section class="hero-section position-relative">
    <div class="hero-bg"></div>
    <div class="container">
        <div class="row align-items-center min-vh-75">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold text-white mb-4">{{ __('home.hero_title') }}</h1>
                <p class="lead text-white-50 mb-5">{{ __('home.hero_subtitle') }}</p>

                <!-- Search Box -->
                <div class="search-box bg-white rounded-4 p-4 shadow">
                    <form action="{{ route('properties.index', ['locale' => app()->getLocale()]) }}" method="GET">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <select name="type" class="form-select">
                                    <option value="">{{ __('search.property_type') }}</option>
                                    <option value="sale">{{ __('search.for_sale') }}</option>
                                    <option value="rent">{{ __('search.for_rent') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="category" class="form-select">
                                    <option value="">{{ __('search.category') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="location" class="form-select">
                                    <option value="">{{ __('search.location') }}</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->slug }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> {{ __('search.search') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties -->
<section class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">{{ __('home.featured_properties') }}</h2>
            <p class="text-muted">{{ __('home.featured_subtitle') }}</p>
        </div>

        <div class="row g-4">
            @foreach($featuredProperties as $property)
                <div class="col-lg-4 col-md-6">
                    @include('components.property-card', ['property' => $property])
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('properties.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-primary btn-lg">
                {{ __('home.view_all_properties') }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            </a>
        </div>
    </div>
</section>

<!-- Compounds Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">{{ __('home.featured_compounds') }}</h2>
            <p class="text-muted">{{ __('home.compounds_subtitle') }}</p>
        </div>

        <div class="row g-4">
            @foreach($featuredCompounds as $compound)
                <div class="col-lg-4 col-md-6">
                    <div class="compound-card card h-100">
                        <img src="{{ $compound->image ?? asset('images/compound-placeholder.jpg') }}"
                             alt="{{ $compound->name }}"
                             class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">{{ $compound->name }}</h5>
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt"></i> {{ $compound->parent?->name }}
                            </p>
                            <p class="card-text">{{ Str::limit($compound->description, 100) }}</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('compounds.show', ['locale' => app()->getLocale(), 'compound' => $compound->slug]) }}"
                               class="btn btn-outline-primary btn-sm w-100">
                                {{ __('home.explore_compound') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('compounds.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-primary btn-lg">
                {{ __('home.view_all_compounds') }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            </a>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">{{ __('home.why_choose_us') }}</h2>
            <p class="text-muted">{{ __('home.why_subtitle') }}</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="feature-box text-center p-4">
                    <div class="feature-icon bg-primary-soft rounded-circle mb-3 mx-auto">
                        <i class="fas fa-home fa-2x text-primary"></i>
                    </div>
                    <h5>{{ __('home.feature_1_title') }}</h5>
                    <p class="text-muted mb-0">{{ __('home.feature_1_text') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box text-center p-4">
                    <div class="feature-icon bg-primary-soft rounded-circle mb-3 mx-auto">
                        <i class="fas fa-shield-alt fa-2x text-primary"></i>
                    </div>
                    <h5>{{ __('home.feature_2_title') }}</h5>
                    <p class="text-muted mb-0">{{ __('home.feature_2_text') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box text-center p-4">
                    <div class="feature-icon bg-primary-soft rounded-circle mb-3 mx-auto">
                        <i class="fas fa-headset fa-2x text-primary"></i>
                    </div>
                    <h5>{{ __('home.feature_3_title') }}</h5>
                    <p class="text-muted mb-0">{{ __('home.feature_3_text') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="feature-box text-center p-4">
                    <div class="feature-icon bg-primary-soft rounded-circle mb-3 mx-auto">
                        <i class="fas fa-tags fa-2x text-primary"></i>
                    </div>
                    <h5>{{ __('home.feature_4_title') }}</h5>
                    <p class="text-muted mb-0">{{ __('home.feature_4_text') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Articles -->
@if($latestArticles->count() > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="fw-bold">{{ __('home.latest_articles') }}</h2>
            <p class="text-muted">{{ __('home.articles_subtitle') }}</p>
        </div>

        <div class="row g-4">
            @foreach($latestArticles as $article)
                <div class="col-lg-4 col-md-6">
                    <div class="article-card card h-100">
                        <img src="{{ $article->image ?? asset('images/article-placeholder.jpg') }}"
                             alt="{{ $article->title }}"
                             class="card-img-top">
                        <div class="card-body">
                            <div class="article-meta text-muted small mb-2">
                                <span><i class="fas fa-calendar"></i> {{ $article->created_at->format('d M Y') }}</span>
                            </div>
                            <h5 class="card-title">
                                <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'article' => $article->slug]) }}">
                                    {{ $article->title }}
                                </a>
                            </h5>
                            <p class="card-text text-muted">{{ Str::limit($article->excerpt, 100) }}</p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'article' => $article->slug]) }}"
                               class="btn btn-link p-0">
                                {{ __('home.read_more') }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-primary btn-lg">
                {{ __('home.view_all_articles') }} <i class="fas fa-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">{{ __('home.cta_title') }}</h3>
                <p class="mb-0 opacity-75">{{ __('home.cta_subtitle') }}</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('my-properties.create', ['locale' => app()->getLocale()]) }}" class="btn btn-light btn-lg">
                    <i class="fas fa-plus"></i> {{ __('home.add_property_btn') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
