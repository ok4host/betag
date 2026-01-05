<footer class="main-footer bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <!-- About -->
            <div class="col-lg-4 col-md-6 mb-4">
                <img src="{{ asset('images/logo-white.png') }}" alt="{{ __('site.name') }}" height="50" class="mb-3">
                <p class="text-muted">{{ __('footer.about_text') }}</p>
                <div class="social-links">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="mb-3">{{ __('footer.quick_links') }}</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="text-muted">{{ __('nav.home') }}</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('properties.index', ['locale' => app()->getLocale()]) }}" class="text-muted">{{ __('nav.properties') }}</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('compounds.index', ['locale' => app()->getLocale()]) }}" class="text-muted">{{ __('nav.compounds') }}</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="text-muted">{{ __('nav.blog') }}</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('about', ['locale' => app()->getLocale()]) }}" class="text-muted">{{ __('nav.about') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Property Types -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="mb-3">{{ __('footer.property_types') }}</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('properties.sale', ['locale' => app()->getLocale()]) }}" class="text-muted">{{ __('nav.for_sale') }}</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('properties.rent', ['locale' => app()->getLocale()]) }}" class="text-muted">{{ __('nav.for_rent') }}</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('properties.index', ['locale' => app()->getLocale(), 'category' => 'apartments']) }}" class="text-muted">{{ __('categories.apartments') }}</a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('properties.index', ['locale' => app()->getLocale(), 'category' => 'villas']) }}" class="text-muted">{{ __('categories.villas') }}</a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="mb-3">{{ __('footer.contact_us') }}</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        {{ __('footer.address') }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone text-primary me-2"></i>
                        <a href="tel:+201234567890" class="text-muted">+20 123 456 7890</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope text-primary me-2"></i>
                        <a href="mailto:info@betag.com" class="text-muted">info@betag.com</a>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-primary me-2"></i>
                        {{ __('footer.working_hours') }}
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <!-- Bottom Footer -->
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-muted">
                    &copy; {{ date('Y') }} {{ __('site.name') }}. {{ __('footer.rights_reserved') }}
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="{{ route('privacy', ['locale' => app()->getLocale()]) }}" class="text-muted me-3">{{ __('footer.privacy') }}</a>
                <a href="{{ route('terms', ['locale' => app()->getLocale()]) }}" class="text-muted">{{ __('footer.terms') }}</a>
            </div>
        </div>
    </div>
</footer>
