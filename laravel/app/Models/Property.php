<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'location_id',
        'title',
        'slug',
        'description',
        'type',
        'price',
        'area',
        'bedrooms',
        'bathrooms',
        'floor',
        'address',
        'latitude',
        'longitude',
        'features',
        'featured_image',
        'gallery',
        'video_url',
        'owner_name',
        'owner_phone',
        'owner_whatsapp',
        'status',
        'is_featured',
        'views_count',
        'featured_until',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'features' => 'array',
        'gallery' => 'array',
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')
            ->withTimestamps();
    }

    // Accessors
    public function getImageUrlAttribute(): string
    {
        return $this->featured_image
            ? asset('storage/' . $this->featured_image)
            : asset('images/property-placeholder.jpg');
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = number_format($this->price);
        $currency = app()->getLocale() === 'ar' ? 'ج.م' : 'EGP';
        $suffix = $this->type === 'rent' ? (app()->getLocale() === 'ar' ? '/شهرياً' : '/month') : '';

        return "{$price} {$currency}{$suffix}";
    }

    public function getFormattedAreaAttribute(): string
    {
        $unit = app()->getLocale() === 'ar' ? 'م²' : 'sqm';
        return number_format($this->area) . ' ' . $unit;
    }

    public function getTypeTextAttribute(): string
    {
        $types = [
            'sale' => app()->getLocale() === 'ar' ? 'للبيع' : 'For Sale',
            'rent' => app()->getLocale() === 'ar' ? 'للإيجار' : 'For Rent',
        ];

        return $types[$this->type] ?? $this->type;
    }

    // Scopes
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)
            ->where(function ($q) {
                $q->whereNull('featured_until')
                    ->orWhere('featured_until', '>', now());
            });
    }

    public function scopeForSale(Builder $query): Builder
    {
        return $query->where('type', 'sale');
    }

    public function scopeForRent(Builder $query): Builder
    {
        return $query->where('type', 'rent');
    }

    public function scopeInLocation(Builder $query, $locationId): Builder
    {
        return $query->where('location_id', $locationId);
    }

    public function scopeInCategory(Builder $query, $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopePriceRange(Builder $query, $min = null, $max = null): Builder
    {
        if ($min) {
            $query->where('price', '>=', $min);
        }
        if ($max) {
            $query->where('price', '<=', $max);
        }

        return $query;
    }

    public function scopeAreaRange(Builder $query, $min = null, $max = null): Builder
    {
        if ($min) {
            $query->where('area', '>=', $min);
        }
        if ($max) {
            $query->where('area', '<=', $max);
        }

        return $query;
    }

    public function scopeWithBedrooms(Builder $query, $bedrooms): Builder
    {
        return $query->where('bedrooms', '>=', $bedrooms);
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }
}
