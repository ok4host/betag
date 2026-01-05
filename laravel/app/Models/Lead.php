<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'name',
        'email',
        'phone',
        'message',
        'source',
        'status',
        'notes',
    ];

    // Relationships
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }

    // Accessors
    public function getStatusTextAttribute(): string
    {
        $statuses = [
            'new' => app()->getLocale() === 'ar' ? 'جديد' : 'New',
            'contacted' => app()->getLocale() === 'ar' ? 'تم التواصل' : 'Contacted',
            'converted' => app()->getLocale() === 'ar' ? 'تم التحويل' : 'Converted',
            'lost' => app()->getLocale() === 'ar' ? 'خسارة' : 'Lost',
        ];

        return $statuses[$this->status] ?? $this->status;
    }
}
