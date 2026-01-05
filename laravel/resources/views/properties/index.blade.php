@extends('layouts.app')

@section('title', __('properties.title'))
@section('description', __('properties.description'))

@section('content')
<!-- Page Header -->
<section class="page-header py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">{{ __('properties.title') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('nav.properties') }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Filters & Properties -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="filter-card card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-filter"></i> {{ __('properties.filters') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('properties.index', ['locale' => app()->getLocale()]) }}" method="GET">
                            <!-- Type -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('properties.type') }}</label>
                                <select name="type" class="form-select">
                                    <option value="">{{ __('properties.all_types') }}</option>
                                    <option value="sale" {{ request('type') === 'sale' ? 'selected' : '' }}>{{ __('properties.for_sale') }}</option>
                                    <option value="rent" {{ request('type') === 'rent' ? 'selected' : '' }}>{{ __('properties.for_rent') }}</option>
                                </select>
                            </div>

                            <!-- Category -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('properties.category') }}</label>
                                <select name="category" class="form-select">
                                    <option value="">{{ __('properties.all_categories') }}</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Location -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('properties.location') }}</label>
                                <select name="location" class="form-select">
                                    <option value="">{{ __('properties.all_locations') }}</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->slug }}" {{ request('location') === $location->slug ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('properties.price_range') }}</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <input type="number" name="min_price" class="form-control" placeholder="{{ __('properties.min') }}" value="{{ request('min_price') }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" name="max_price" class="form-control" placeholder="{{ __('properties.max') }}" value="{{ request('max_price') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Bedrooms -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('properties.bedrooms') }}</label>
                                <select name="bedrooms" class="form-select">
                                    <option value="">{{ __('properties.any') }}</option>
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Bathrooms -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('properties.bathrooms') }}</label>
                                <select name="bathrooms" class="form-select">
                                    <option value="">{{ __('properties.any') }}</option>
                                    @for($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ request('bathrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                                    @endfor
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> {{ __('properties.search') }}
                            </button>
                            <a href="{{ route('properties.index', ['locale' => app()->getLocale()]) }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-redo"></i> {{ __('properties.reset') }}
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Properties Grid -->
            <div class="col-lg-9">
                <!-- Sort Bar -->
                <div class="sort-bar d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded">
                    <span class="text-muted">
                        {{ __('properties.showing') }} {{ $properties->firstItem() ?? 0 }}-{{ $properties->lastItem() ?? 0 }}
                        {{ __('properties.of') }} {{ $properties->total() }} {{ __('properties.results') }}
                    </span>
                    <div class="d-flex gap-2 align-items-center">
                        <label class="text-muted">{{ __('properties.sort_by') }}:</label>
                        <select class="form-select form-select-sm w-auto" onchange="window.location.href=this.value">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest']) }}" {{ request('sort') === 'latest' ? 'selected' : '' }}>
                                {{ __('properties.latest') }}
                            </option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') === 'price_low' ? 'selected' : '' }}>
                                {{ __('properties.price_low') }}
                            </option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') === 'price_high' ? 'selected' : '' }}>
                                {{ __('properties.price_high') }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Properties -->
                @if($properties->count() > 0)
                    <div class="row g-4">
                        @foreach($properties as $property)
                            <div class="col-md-6 col-lg-4">
                                @include('components.property-card', ['property' => $property])
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-5 d-flex justify-content-center">
                        {{ $properties->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-home fa-4x text-muted mb-3"></i>
                        <h4>{{ __('properties.no_results') }}</h4>
                        <p class="text-muted">{{ __('properties.no_results_text') }}</p>
                        <a href="{{ route('properties.index', ['locale' => app()->getLocale()]) }}" class="btn btn-primary">
                            {{ __('properties.view_all') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
