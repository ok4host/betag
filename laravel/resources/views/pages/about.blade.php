@extends('layouts.app')

@section('title', __('pages.about_title'))
@section('description', __('pages.about_description'))

@section('content')
<!-- Page Header -->
<section class="page-header py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">{{ __('pages.about_title') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('nav.about') }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- About Content -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="{{ asset('images/about-image.jpg') }}" alt="{{ __('pages.about_title') }}" class="img-fluid rounded-3 shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">{{ __('pages.about_heading') }}</h2>
                <p class="lead text-muted mb-4">{{ __('pages.about_lead') }}</p>
                <p class="mb-4">{{ __('pages.about_text_1') }}</p>
                <p class="mb-4">{{ __('pages.about_text_2') }}</p>

                <div class="row g-4 mt-4">
                    <div class="col-6">
                        <div class="counter-box text-center p-3 bg-light rounded">
                            <h3 class="text-primary fw-bold mb-0">500+</h3>
                            <small class="text-muted">{{ __('pages.properties_sold') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="counter-box text-center p-3 bg-light rounded">
                            <h3 class="text-primary fw-bold mb-0">1000+</h3>
                            <small class="text-muted">{{ __('pages.happy_clients') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vision & Mission -->
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-box mb-3">
                            <i class="fas fa-eye fa-2x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">{{ __('pages.our_vision') }}</h4>
                        <p class="text-muted mb-0">{{ __('pages.vision_text') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="icon-box mb-3">
                            <i class="fas fa-bullseye fa-2x text-primary"></i>
                        </div>
                        <h4 class="fw-bold">{{ __('pages.our_mission') }}</h4>
                        <p class="text-muted mb-0">{{ __('pages.mission_text') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values -->
        <div class="values-section">
            <h3 class="fw-bold text-center mb-5">{{ __('pages.our_values') }}</h3>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="value-box text-center p-4">
                        <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">{{ __('pages.value_1_title') }}</h5>
                        <p class="text-muted mb-0">{{ __('pages.value_1_text') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-box text-center p-4">
                        <i class="fas fa-gem fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">{{ __('pages.value_2_title') }}</h5>
                        <p class="text-muted mb-0">{{ __('pages.value_2_text') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value-box text-center p-4">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">{{ __('pages.value_3_title') }}</h5>
                        <p class="text-muted mb-0">{{ __('pages.value_3_text') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
