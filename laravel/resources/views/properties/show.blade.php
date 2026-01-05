@extends('layouts.app')

@section('title', $property->title)
@section('description', Str::limit($property->description, 160))
@section('og_image', $property->image)

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
                    <a href="{{ route('properties.index', ['locale' => app()->getLocale()]) }}">{{ __('nav.properties') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ $property->title }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Property Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Gallery -->
                <div class="property-gallery mb-4">
                    <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner rounded-3">
                            @forelse($property->gallery ?? [$property->image] as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $image }}" class="d-block w-100" alt="{{ $property->title }}">
                                </div>
                            @empty
                                <div class="carousel-item active">
                                    <img src="{{ asset('images/property-placeholder.jpg') }}" class="d-block w-100" alt="{{ $property->title }}">
                                </div>
                            @endforelse
                        </div>
                        @if(count($property->gallery ?? []) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Title & Price -->
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <span class="badge bg-{{ $property->type === 'sale' ? 'success' : 'info' }} mb-2">
                            {{ $property->type === 'sale' ? __('property.for_sale') : __('property.for_rent') }}
                        </span>
                        <h1 class="fw-bold">{{ $property->title }}</h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            {{ $property->location?->name }}
                            @if($property->location?->parent)
                                , {{ $property->location->parent->name }}
                            @endif
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="property-price text-primary fw-bold fs-3">
                            {{ $property->formatted_price }}
                        </div>
                        @if($property->type === 'rent')
                            <small class="text-muted">/ {{ __('property.per_month') }}</small>
                        @endif
                    </div>
                </div>

                <!-- Features -->
                <div class="property-features-grid mb-4">
                    <div class="row g-3">
                        @if($property->bedrooms)
                            <div class="col-6 col-md-3">
                                <div class="feature-item text-center p-3 bg-light rounded">
                                    <i class="fas fa-bed fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold">{{ $property->bedrooms }}</div>
                                    <small class="text-muted">{{ __('property.bedrooms') }}</small>
                                </div>
                            </div>
                        @endif
                        @if($property->bathrooms)
                            <div class="col-6 col-md-3">
                                <div class="feature-item text-center p-3 bg-light rounded">
                                    <i class="fas fa-bath fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold">{{ $property->bathrooms }}</div>
                                    <small class="text-muted">{{ __('property.bathrooms') }}</small>
                                </div>
                            </div>
                        @endif
                        @if($property->area)
                            <div class="col-6 col-md-3">
                                <div class="feature-item text-center p-3 bg-light rounded">
                                    <i class="fas fa-ruler-combined fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold">{{ $property->area }}</div>
                                    <small class="text-muted">{{ __('property.sqm') }}</small>
                                </div>
                            </div>
                        @endif
                        @if($property->floor)
                            <div class="col-6 col-md-3">
                                <div class="feature-item text-center p-3 bg-light rounded">
                                    <i class="fas fa-building fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold">{{ $property->floor }}</div>
                                    <small class="text-muted">{{ __('property.floor') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div class="property-description mb-4">
                    <h4 class="fw-bold mb-3">{{ __('property.description') }}</h4>
                    <div class="content">
                        {!! nl2br(e($property->description)) !!}
                    </div>
                </div>

                <!-- Amenities -->
                @if($property->features && count($property->features) > 0)
                    <div class="property-amenities mb-4">
                        <h4 class="fw-bold mb-3">{{ __('property.amenities') }}</h4>
                        <div class="row g-2">
                            @foreach($property->features as $feature)
                                <div class="col-6 col-md-4">
                                    <div class="amenity-item p-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ $feature }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Location Map -->
                @if($property->latitude && $property->longitude)
                    <div class="property-map mb-4">
                        <h4 class="fw-bold mb-3">{{ __('property.location_map') }}</h4>
                        <div id="propertyMap" class="rounded-3" style="height: 400px;"></div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Form -->
                <div class="contact-card card sticky-top mb-4" style="top: 100px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope"></i> {{ __('property.contact_agent') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('contact.submit', ['locale' => app()->getLocale()]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="property_id" value="{{ $property->id }}">

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
                                <textarea name="message" class="form-control" rows="4" placeholder="{{ __('form.message') }}">{{ __('property.interested_in', ['title' => $property->title]) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane"></i> {{ __('form.send') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Actions -->
                <div class="property-actions d-grid gap-2 mb-4">
                    @auth
                        <button class="btn btn-outline-danger" onclick="toggleFavorite({{ $property->id }})">
                            <i class="fas fa-heart"></i> {{ __('property.add_to_favorites') }}
                        </button>
                    @endauth
                    <a href="https://wa.me/201234567890?text={{ urlencode(__('property.whatsapp_message', ['title' => $property->title, 'url' => url()->current()])) }}"
                       class="btn btn-success" target="_blank">
                        <i class="fab fa-whatsapp"></i> {{ __('property.whatsapp') }}
                    </a>
                    <button class="btn btn-outline-secondary" onclick="shareProperty()">
                        <i class="fas fa-share-alt"></i> {{ __('property.share') }}
                    </button>
                </div>

                <!-- Property Details -->
                <div class="property-details-card card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('property.details') }}</h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('property.id') }}</span>
                            <strong>#{{ $property->id }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('property.type') }}</span>
                            <strong>{{ $property->type === 'sale' ? __('property.for_sale') : __('property.for_rent') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('property.category') }}</span>
                            <strong>{{ $property->category?->name }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ __('property.posted_date') }}</span>
                            <strong>{{ $property->created_at->format('d/m/Y') }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Related Properties -->
        @if($relatedProperties->count() > 0)
            <div class="related-properties mt-5">
                <h3 class="fw-bold mb-4">{{ __('property.related_properties') }}</h3>
                <div class="row g-4">
                    @foreach($relatedProperties as $related)
                        <div class="col-md-6 col-lg-3">
                            @include('components.property-card', ['property' => $related])
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
function shareProperty() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $property->title }}',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('{{ __("property.link_copied") }}');
    }
}
</script>
@endpush
@endsection
