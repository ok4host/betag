<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'slug',
        'type',
        'parent_id',
        'description_ar',
        'description_en',
        'featured_image',
        'latitude',
        'longitude',
        'developer',
        'price_from',
        'price_to',
        'units_count',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'price_from' => 'decimal:2',
        'price_to' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    // Relationships
    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    // Accessors
    public function getNameAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'ar' ? $this->description_ar : $this->description_en;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->featured_image
            ? asset('storage/' . $this->featured_image)
            : asset('images/location-placeholder.jpg');
    }

    public function getFullNameAttribute(): string
    {
        $parts = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            $parts[] = $parent->name;
            $parent = $parent->parent;
        }

        return implode(', ', $parts);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeCompounds($query)
    {
        return $query->where('type', 'compound');
    }

    public function scopeCities($query)
    {
        return $query->where('type', 'city');
    }

    public function scopeAreas($query)
    {
        return $query->where('type', 'area');
    }
}
