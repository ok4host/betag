@extends('layouts.app')

@section('title', $compound->name)
@section('description', Str::limit($compound->description, 160))

@section('content')
<!-- Hero -->
<section class="compound-hero position-relative">
    <div class="hero-bg" style="background-image: url('{{ $compound->image ?? asset('images/compound-placeholder.jpg') }}');">
        <div class="overlay"></div>
    </div>
    <div class="container position-relative py-5">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-white">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('compounds.index', ['locale' => app()->getLocale()]) }}" class="text-white">{{ __('nav.compounds') }}</a>
                </li>
                <li class="breadcrumb-item active text-white-50">{{ $compound->name }}</li>
            </ol>
        </nav>
        <h1 class="display-4 fw-bold text-white">{{ $compound->name }}</h1>
        <p class="text-white-50 fs-5">
            <i class="fas fa-map-marker-alt"></i> {{ $compound->parent?->name }}
        </p>
    </div>
</section>

<!-- Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- About -->
                <div class="compound-about mb-5">
                    <h3 class="fw-bold mb-4">{{ __('compounds.about') }}</h3>
                    <div class="content">
                        {!! nl2br(e($compound->description)) !!}
                    </div>
                </div>

                <!-- Features -->
                @if($compound->features && count($compound->features) > 0)
                    <div class="compound-features mb-5">
                        <h3 class="fw-bold mb-4">{{ __('compounds.features') }}</h3>
                        <div class="row g-3">
                            @foreach($compound->features as $feature)
                                <div class="col-6 col-md-4">
                                    <div class="feature-item p-3 bg-light rounded">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ $feature }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Available Properties -->
                <div class="compound-properties">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-bold mb-0">{{ __('compounds.available_properties') }}</h3>
                        <span class="badge bg-primary fs-6">{{ $properties->total() }} {{ __('compounds.units') }}</span>
                    </div>

                    @if($properties->count() > 0)
                        <div class="row g-4">
                            @foreach($properties as $property)
                                <div class="col-md-6">
                                    @include('components.property-card', ['property' => $property])
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $properties->links() }}
                        </div>
                    @else
                        <div class="text-center py-4 bg-light rounded">
                            <p class="text-muted mb-0">{{ __('compounds.no_properties') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Form -->
                <div class="contact-card card sticky-top mb-4" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope"></i> {{ __('compounds.inquire') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('contact.submit', ['locale' => app()->getLocale()]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="compound_id" value="{{ $compound->id }}">

                            <div class="mb-3">
                                <input type="text" name="name" class="form-control" placeholder="{{ __('form.name') }}" required>
                            </div>
                            <div class="mb-3">
                                <input type="email" name="email" class="form-control" placeholder="{{ __('form.email') }}" required>
                            </div>
                            <div class="mb-3">
                                <input type="tel" name="phone" class="form-control" placeholder="{{ __('form.phone') }}" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="message" class="form-control" rows="4" placeholder="{{ __('form.message') }}">{{ __('compounds.interested_in', ['name' => $compound->name]) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane"></i> {{ __('form.send') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="d-grid gap-2">
                    <a href="https://wa.me/201234567890?text={{ urlencode(__('compounds.whatsapp_message', ['name' => $compound->name])) }}"
                       class="btn btn-success" target="_blank">
                        <i class="fab fa-whatsapp"></i> {{ __('compounds.whatsapp') }}
                    </a>
                    <a href="tel:+201234567890" class="btn btn-outline-primary">
                        <i class="fas fa-phone"></i> {{ __('compounds.call_us') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
