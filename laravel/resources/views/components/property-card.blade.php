@props(['property'])

<div class="property-card card h-100">
    <div class="property-image position-relative">
        <img src="{{ $property->image ?? asset('images/property-placeholder.jpg') }}"
             alt="{{ $property->title }}"
             class="card-img-top">

        <!-- Badges -->
        <div class="property-badges">
            <span class="badge bg-{{ $property->type === 'sale' ? 'success' : 'info' }}">
                {{ $property->type === 'sale' ? __('property.for_sale') : __('property.for_rent') }}
            </span>
            @if($property->is_featured)
                <span class="badge bg-warning text-dark">{{ __('property.featured') }}</span>
            @endif
        </div>

        <!-- Favorite Button -->
        @auth
            <button class="btn-favorite {{ $property->isFavorited ? 'active' : '' }}"
                    onclick="toggleFavorite({{ $property->id }})"
                    title="{{ __('property.add_to_favorites') }}">
                <i class="fas fa-heart"></i>
            </button>
        @endauth
    </div>

    <div class="card-body">
        <!-- Price -->
        <div class="property-price text-primary fw-bold fs-5 mb-2">
            {{ $property->formatted_price }}
            @if($property->type === 'rent')
                <small class="text-muted">/ {{ __('property.per_month') }}</small>
            @endif
        </div>

        <!-- Title -->
        <h5 class="card-title">
            <a href="{{ route('properties.show', ['locale' => app()->getLocale(), 'property' => $property->slug]) }}">
                {{ $property->title }}
            </a>
        </h5>

        <!-- Location -->
        <p class="text-muted mb-2">
            <i class="fas fa-map-marker-alt"></i>
            {{ $property->location?->name }}
        </p>

        <!-- Features -->
        <div class="property-features d-flex gap-3 text-muted">
            @if($property->bedrooms)
                <span><i class="fas fa-bed"></i> {{ $property->bedrooms }}</span>
            @endif
            @if($property->bathrooms)
                <span><i class="fas fa-bath"></i> {{ $property->bathrooms }}</span>
            @endif
            @if($property->area)
                <span><i class="fas fa-ruler-combined"></i> {{ $property->area }} {{ __('property.sqm') }}</span>
            @endif
        </div>
    </div>

    <div class="card-footer bg-transparent">
        <a href="{{ route('properties.show', ['locale' => app()->getLocale(), 'property' => $property->slug]) }}"
           class="btn btn-outline-primary btn-sm w-100">
            {{ __('property.view_details') }}
        </a>
    </div>
</div>
