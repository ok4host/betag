@extends('layouts.app')

@section('title', __('pages.terms_title'))

@section('content')
<!-- Page Header -->
<section class="page-header py-5 bg-light">
    <div class="container">
        <h1 class="fw-bold">{{ __('pages.terms_title') }}</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item">
                    <a href="{{ route('home', ['locale' => app()->getLocale()]) }}">{{ __('nav.home') }}</a>
                </li>
                <li class="breadcrumb-item active">{{ __('pages.terms_title') }}</li>
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

                        <h4 class="fw-bold">{{ __('pages.terms_intro_title') }}</h4>
                        <p class="text-muted">{{ __('pages.terms_intro_text') }}</p>

                        <h4 class="fw-bold mt-4">{{ __('pages.terms_use_title') }}</h4>
                        <p class="text-muted">{{ __('pages.terms_use_text') }}</p>

                        <h4 class="fw-bold mt-4">{{ __('pages.terms_account_title') }}</h4>
                        <p class="text-muted">{{ __('pages.terms_account_text') }}</p>

                        <h4 class="fw-bold mt-4">{{ __('pages.terms_content_title') }}</h4>
                        <p class="text-muted">{{ __('pages.terms_content_text') }}</p>

                        <h4 class="fw-bold mt-4">{{ __('pages.terms_liability_title') }}</h4>
                        <p class="text-muted">{{ __('pages.terms_liability_text') }}</p>

                        <h4 class="fw-bold mt-4">{{ __('pages.terms_changes_title') }}</h4>
                        <p class="text-muted">{{ __('pages.terms_changes_text') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
