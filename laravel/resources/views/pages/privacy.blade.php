@extends('layouts.app')

@section('title', __('pages.privacy_title'))

@section('content')
<!-- Page Header -->
<section class="page-header py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">{{ __('pages.privacy_title') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('pages.privacy_title') }}</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="content-card card border-0 shadow">
                    <div class="card-body p-5">
                        <p class="text-muted mb-4">{{ __('pages.last_updated') }}: {{ date('d/m/Y') }}</p>

                        <h4 class="fw-bold">{{ __('pages.privacy_intro_title') }}</h4>
                        <p class="text-muted">{{ __('pages.privacy_intro_text') }}</p>

                        <h4 class="fw-bold mt-4">{{ __('pages.privacy_collect_title') }}</h4>
                        <p class="text-muted">{{ __('pages.privacy_collect_text') }}</p>
                        <ul class="text-muted">
                            <li>{{ __('pages.privacy_collect_1') }}</li>
                            <li>{{ __('pages.privacy_collect_2') }}</li>
                            <li>{{ __('pages.privacy_collect_3') }}</li>
                        </ul>

                        <h4 class="fw-bold mt-4">{{ __('pages.privacy_use_title') }}</h4>
                        <p class="text-muted">{{ __('pages.privacy_use_text') }}</p>

                        <h4 class="fw-bold mt-4">{{ __('pages.privacy_security_title') }}</h4>
                        <p class="text-muted">{{ __('pages.privacy_security_text') }}</p>

                        <h4 class="fw-bold mt-4">{{ __('pages.privacy_contact_title') }}</h4>
                        <p class="text-muted">{{ __('pages.privacy_contact_text') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
