@extends('layouts.app')

@section('title', __('compounds.title'))
@section('description', __('compounds.description'))

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

<!-- Compounds Grid -->
<section class="py-5">
    <div class="container">
        @if($compounds->count() > 0)
            <div class="row g-4">
                @foreach($compounds as $compound)
                    <div class="col-lg-4 col-md-6">
                        <div class="compound-card card h-100 border-0 shadow-sm">
                            <div class="position-relative">
                                <img src="{{ $compound->image ?? asset('images/compound-placeholder.jpg') }}"
                                     alt="{{ $compound->name }}"
                                     class="card-img-top">
                                <div class="compound-badge position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary">{{ $compound->properties_count ?? 0 }} {{ __('compounds.units') }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title fw-bold">{{ $compound->name }}</h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                    {{ $compound->parent?->name }}
                                </p>
                                <p class="card-text text-muted">{{ Str::limit($compound->description, 100) }}</p>

                                @if($compound->min_price)
                                    <div class="price-range text-primary fw-bold">
                                        {{ __('compounds.starting_from') }}
                                        {{ number_format($compound->min_price) }}
                                        {{ app()->getLocale() === 'ar' ? 'ج.م' : 'EGP' }}
                                    </div>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="{{ route('compounds.show', ['locale' => app()->getLocale(), 'compound' => $compound->slug]) }}"
                                   class="btn btn-outline-primary w-100">
                                    {{ __('compounds.view_details') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $compounds->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-4x text-muted mb-3"></i>
                <h4>{{ __('compounds.no_results') }}</h4>
                <p class="text-muted">{{ __('compounds.no_results_text') }}</p>
            </div>
        @endif
    </div>
</section>
@endsection
