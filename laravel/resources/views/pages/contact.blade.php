@extends('layouts.app')

@section('title', __('pages.contact_title'))
@section('description', __('pages.contact_description'))

@section('content')
<!-- Page Header -->
<section class="page-header py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">{{ __('pages.contact_title') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('nav.contact') }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Contact Content -->
<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-card card border-0 shadow">
                    <div class="card-body p-5">
                        <h3 class="fw-bold mb-4">{{ __('pages.send_message') }}</h3>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <form method="POST" action="{{ route('contact.submit', ['locale' => app()->getLocale()]) }}">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('form.name') }} *</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('form.email') }} *</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('form.phone') }} *</label>
                                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('form.subject') }}</label>
                                    <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">{{ __('form.message') }} *</label>
                                    <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                              rows="5" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane"></i> {{ __('form.send') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="contact-info">
                    <h3 class="fw-bold mb-4">{{ __('pages.contact_info') }}</h3>

                    <div class="info-item d-flex mb-4">
                        <div class="icon-box bg-primary-soft rounded-circle p-3 me-3">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ __('pages.address') }}</h6>
                            <p class="text-muted mb-0">{{ __('footer.address') }}</p>
                        </div>
                    </div>

                    <div class="info-item d-flex mb-4">
                        <div class="icon-box bg-primary-soft rounded-circle p-3 me-3">
                            <i class="fas fa-phone text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ __('pages.phone') }}</h6>
                            <p class="text-muted mb-0">
                                <a href="tel:+201234567890" class="text-muted">+20 123 456 7890</a>
                            </p>
                        </div>
                    </div>

                    <div class="info-item d-flex mb-4">
                        <div class="icon-box bg-primary-soft rounded-circle p-3 me-3">
                            <i class="fas fa-envelope text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ __('pages.email') }}</h6>
                            <p class="text-muted mb-0">
                                <a href="mailto:info@betag.com" class="text-muted">info@betag.com</a>
                            </p>
                        </div>
                    </div>

                    <div class="info-item d-flex mb-4">
                        <div class="icon-box bg-primary-soft rounded-circle p-3 me-3">
                            <i class="fas fa-clock text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">{{ __('pages.working_hours') }}</h6>
                            <p class="text-muted mb-0">{{ __('footer.working_hours') }}</p>
                        </div>
                    </div>

                    <!-- Social Links -->
                    <div class="social-links mt-5">
                        <h6 class="fw-bold mb-3">{{ __('pages.follow_us') }}</h6>
                        <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="contact-map mt-5">
            <div class="rounded-3 overflow-hidden shadow" style="height: 400px;">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3453.0977744985647!2d31.2357!3d30.0444!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzDCsDAyJzM5LjgiTiAzMcKwMTQnMDguNSJF!5e0!3m2!1sen!2seg!4v1234567890"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>
@endsection
