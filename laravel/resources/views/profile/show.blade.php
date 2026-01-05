@extends('layouts.app')

@section('title', __('profile.title'))

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="profile-sidebar card">
                    <div class="card-body text-center">
                        <div class="profile-avatar mb-3">
                            <img src="{{ auth()->user()->avatar ?? asset('images/default-avatar.png') }}"
                                 alt="{{ auth()->user()->name }}"
                                 class="rounded-circle" width="100" height="100">
                        </div>
                        <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                        <p class="text-muted small mb-0">{{ auth()->user()->email }}</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}" class="text-dark">
                                <i class="fas fa-user me-2"></i> {{ __('profile.my_profile') }}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('my-properties.index', ['locale' => app()->getLocale()]) }}" class="text-dark">
                                <i class="fas fa-building me-2"></i> {{ __('profile.my_properties') }}
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="{{ route('favorites.index', ['locale' => app()->getLocale()]) }}" class="text-dark">
                                <i class="fas fa-heart me-2"></i> {{ __('profile.favorites') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <!-- Profile Info -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('profile.personal_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('auth.name') }}</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', auth()->user()->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('auth.email') }}</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email', auth()->user()->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('auth.phone') }}</label>
                                    <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone', auth()->user()->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ __('profile.save_changes') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('profile.change_password') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.password', ['locale' => app()->getLocale()]) }}">
                            @csrf
                            @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('profile.current_password') }}</label>
                                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('profile.new_password') }}</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('profile.confirm_password') }}</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-key"></i> {{ __('profile.update_password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
