@extends('layouts.app')

@section('title', $compound->name)
@section('description', Str::limit($compound->description, 160))

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
                    <a href="{{ route('compounds.index', ['locale' => app()->getLocale()]) }}">{{ __('nav.compounds') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ $compound->name }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Compound Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Image -->
                @if($compound->image)
                    <img src="{{ $compound->image }}" alt="{{ $compound->name }}" class="img-fluid rounded-3 mb-4 w-100">
                @endif

                <!-- Title -->
                <h1 class="fw-bold mb-3">{{ $compound->name }}</h1>

                <!-- Info -->
                <div class="compound-info d-flex flex-wrap gap-4 mb-4 text-muted">
                    <span><i class="fas fa-map-marker-alt text-primary"></i> {{ $compound->parent?->name }}</span>
                    @if($compound->developer)
                        <span><i class="fas fa-building text-primary"></i> {{ $compound->developer }}</span>
                    @endif
                </div>

                <!-- Description -->
                @if($compound->description)
                    <div class="compound-description mb-5">
                        <h4 class="fw-bold mb-3">{{ __('compounds.about') }}</h4>
                        <div class="content">
                            {!! nl2br(e($compound->description)) !!}
                        </div>
                    </div>
                @endif

                <!-- Properties in Compound -->
                <div class="compound-properties">
                    <h4 class="fw-bold mb-4">{{ __('compounds.available_properties') }} ({{ $properties->total() }})</h4>

                    @if($properties->count() > 0)
                        <div class="row g-4">
                            @foreach($properties as $property)
                                <div class="col-md-6">
                                    @include('components.property-card', ['property' => $property])
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-5 d-flex justify-content-center">
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
                <div class="sticky-top" style="top: 100px;">
                    <!-- Contact Card -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ __('compounds.interested') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('contact.submit', ['locale' => app()->getLocale()]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="compound_id" value="{{ $compound->id }}">
                                <div class="mb-3">
                                    <input type="text" name="name" class="form-control" placeholder="{{ __('form.name') }}" required>
                                </div>
                                <div class="mb-3">
                                    <input type="tel" name="phone" class="form-control" placeholder="{{ __('form.phone') }}" required>
                                </div>
                                <div class="mb-3">
                                    <input type="email" name="email" class="form-control" placeholder="{{ __('form.email') }}">
                                </div>
                                <div class="mb-3">
                                    <textarea name="message" class="form-control" rows="3" placeholder="{{ __('form.message') }}">{{ __('compounds.inquiry_message', ['name' => $compound->name]) }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    {{ __('form.send') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ __('compounds.share') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                   class="btn btn-outline-primary" target="_blank">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($compound->name . ' - ' . url()->current()) }}"
                                   class="btn btn-outline-success" target="_blank">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <button class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(window.location.href); alert('{{ __('compounds.link_copied') }}');">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
