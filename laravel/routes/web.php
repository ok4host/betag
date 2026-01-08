<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CompoundController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Language Switcher
Route::get('language/{locale}', function ($locale) {
    if (in_array($locale, ['ar', 'en'])) {
        session()->put('locale', $locale);
        app()->setLocale($locale);

        // Get the previous URL and replace the locale
        $previousUrl = url()->previous();
        $parsedUrl = parse_url($previousUrl);
        $path = $parsedUrl['path'] ?? '/';

        // Replace the locale in the path
        $newPath = preg_replace('#^/(ar|en)#', '/' . $locale, $path);

        // If no locale was in the path, add it
        if ($newPath === $path && !preg_match('#^/(ar|en)#', $path)) {
            $newPath = '/' . $locale . $path;
        }

        return redirect($newPath);
    }
    return redirect()->back();
})->name('language.switch');

// Localized Routes Group
Route::group([
    'prefix' => '{locale?}',
    'where' => ['locale' => 'ar|en'],
    'middleware' => ['locale'],
], function () {

    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Properties
    Route::prefix('properties')->name('properties.')->group(function () {
        Route::get('/', [PropertyController::class, 'index'])->name('index');
        Route::get('/sale', [PropertyController::class, 'sale'])->name('sale');
        Route::get('/rent', [PropertyController::class, 'rent'])->name('rent');
        Route::get('/{slug}', [PropertyController::class, 'show'])->name('show');
    });

    // Compounds
    Route::prefix('compounds')->name('compounds.')->group(function () {
        Route::get('/', [CompoundController::class, 'index'])->name('index');
        Route::get('/{slug}', [CompoundController::class, 'show'])->name('show');
    });

    // Blog
    Route::prefix('blog')->name('blog.')->group(function () {
        Route::get('/', [BlogController::class, 'index'])->name('index');
        Route::get('/{slug}', [BlogController::class, 'show'])->name('show');
    });

    // Static Pages
    Route::get('/about', function () {
        return view('pages.about');
    })->name('about');

    Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

    Route::get('/privacy', function () {
        return view('pages.privacy');
    })->name('privacy');

    Route::get('/terms', function () {
        return view('pages.terms');
    })->name('terms');

    // Authentication Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [LoginController::class, 'login']);
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Profile
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
            Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        });

        // Favorites
        Route::prefix('favorites')->name('favorites.')->group(function () {
            Route::get('/', [FavoriteController::class, 'index'])->name('index');
            Route::post('/{property}/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
            Route::get('/count', [FavoriteController::class, 'count'])->name('count');
        });

        // My Properties
        Route::prefix('my-properties')->name('my-properties.')->group(function () {
            Route::get('/', [PropertyController::class, 'myProperties'])->name('index');
            Route::get('/create', [PropertyController::class, 'create'])->name('create');
            Route::post('/', [PropertyController::class, 'store'])->name('store');
            Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('edit');
            Route::put('/{property}', [PropertyController::class, 'update'])->name('update');
            Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('destroy');
        });
    });
});

// Redirect root to default locale
Route::get('/', function () {
    return redirect(app()->getLocale());
});
