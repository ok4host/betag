<header class="main-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('home', ['locale' => app()->getLocale()]) }}">
                <img src="{{ asset('images/logo.png') }}" alt="{{ __('site.name') }}" height="50">
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav {{ app()->getLocale() === 'ar' ? 'ms-auto' : 'me-auto' }}">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home', ['locale' => app()->getLocale()]) }}">
                            {{ __('nav.home') }}
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('properties.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                            {{ __('nav.properties') }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('properties.index', ['locale' => app()->getLocale()]) }}">
                                    {{ __('nav.all_properties') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('properties.sale', ['locale' => app()->getLocale()]) }}">
                                    {{ __('nav.for_sale') }}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('properties.rent', ['locale' => app()->getLocale()]) }}">
                                    {{ __('nav.for_rent') }}
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('compounds.*') ? 'active' : '' }}" href="{{ route('compounds.index', ['locale' => app()->getLocale()]) }}">
                            {{ __('nav.compounds') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}">
                            {{ __('nav.blog') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about', ['locale' => app()->getLocale()]) }}">
                            {{ __('nav.about') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.show', ['locale' => app()->getLocale()]) }}">
                            {{ __('nav.contact') }}
                        </a>
                    </li>
                </ul>

                <!-- Right Side -->
                <ul class="navbar-nav {{ app()->getLocale() === 'ar' ? 'me-auto' : 'ms-auto' }} align-items-center">
                    <!-- Theme Toggle -->
                    <li class="nav-item">
                        <button class="btn btn-link nav-link" onclick="toggleTheme()" title="{{ __('nav.toggle_theme') }}">
                            <i class="fas fa-moon dark-icon"></i>
                            <i class="fas fa-sun light-icon"></i>
                        </button>
                    </li>

                    <!-- Language Switcher -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-globe"></i>
                            {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() === 'ar' ? 'active' : '' }}" href="{{ route('language.switch', 'ar') }}">
                                    العربية
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                                    English
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Auth Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login', ['locale' => app()->getLocale()]) }}">
                                <i class="fas fa-sign-in-alt"></i> {{ __('nav.login') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm" href="{{ route('register', ['locale' => app()->getLocale()]) }}">
                                {{ __('nav.register') }}
                            </a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.show', ['locale' => app()->getLocale()]) }}">
                                        <i class="fas fa-user"></i> {{ __('nav.profile') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('my-properties.index', ['locale' => app()->getLocale()]) }}">
                                        <i class="fas fa-building"></i> {{ __('nav.my_properties') }}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('favorites.index', ['locale' => app()->getLocale()]) }}">
                                        <i class="fas fa-heart"></i> {{ __('nav.favorites') }}
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout', ['locale' => app()->getLocale()]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> {{ __('nav.logout') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest

                    <!-- Add Property Button -->
                    <li class="nav-item">
                        <a class="btn btn-gold btn-sm" href="{{ route('my-properties.create', ['locale' => app()->getLocale()]) }}">
                            <i class="fas fa-plus"></i> {{ __('nav.add_property') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
