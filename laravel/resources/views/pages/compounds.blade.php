@extends('layouts.app')

@section('title', __('compounds.title'))

@section('content')
<!-- Page Header -->
<section class="page-header py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">{{ __('compounds.title') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('nav.compounds') }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Filters & Compounds -->
<section class="py-5">
    <div class="container">
        <!-- Filter Bar -->
        <div class="filter-bar mb-4 p-3 bg-light rounded">
            <form action="{{ route('compounds.index', ['locale' => app()->getLocale()]) }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <select name="location" class="form-select">
                        <option value="">{{ __('compounds.all_locations') }}</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->slug }}" {{ request('location') === $location->slug ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="{{ __('compounds.search') }}" value="{{ request('q') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> {{ __('compounds.filter') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Compounds Grid -->
        @if($compounds->count() > 0)
            <div class="row g-4">
                @foreach($compounds as $compound)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 compound-card">
                            <img src="{{ $compound->image ?? asset('images/compound-placeholder.jpg') }}"
                                 class="card-img-top" alt="{{ $compound->name }}">
                            @if($compound->is_featured)
                                <span class="badge bg-warning position-absolute top-0 start-0 m-3">{{ __('compounds.featured') }}</span>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $compound->name }}</h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt"></i> {{ $compound->parent?->name }}
                                </p>
                                @if($compound->developer)
                                    <p class="text-muted mb-2">
                                        <i class="fas fa-building"></i> {{ $compound->developer }}
                                    </p>
                                @endif
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark">
                                        {{ $compound->properties_count }} {{ __('compounds.properties') }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <a href="{{ route('compounds.show', ['locale' => app()->getLocale(), 'slug' => $compound->slug]) }}"
                                   class="btn btn-outline-primary btn-sm w-100">
                                    {{ __('compounds.view') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $compounds->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-city fa-4x text-muted mb-3"></i>
                <h4>{{ __('compounds.no_results') }}</h4>
            </div>
        @endif
    </div>
</section>
@endsection
