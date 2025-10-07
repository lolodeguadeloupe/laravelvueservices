<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'bio',
        'avatar',
        'address',
        'latitude',
        'longitude',
        'date_of_birth',
        'gender',
        'languages',
        'certifications',
        'portfolio',
        'is_verified',
        'verified_at',
        'rating',
        'reviews_count',
        'experience',
        'company_name',
        'documents',
    ];

    protected $casts = [
        'address' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'date_of_birth' => 'date',
        'languages' => 'array',
        'certifications' => 'array',
        'portfolio' => 'array',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'documents' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getFullAddressAttribute(): ?string
    {
        if (! $this->address) {
            return null;
        }

        $parts = array_filter([
            $this->address['street'] ?? null,
            $this->address['city'] ?? null,
            $this->address['postal_code'] ?? null,
            $this->address['country'] ?? null,
        ]);

        return implode(', ', $parts);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeInLocation($query, $latitude, $longitude, $radius = 10)
    {
        return $query->selectRaw('*, (
            6371 * acos(
                cos(radians(?)) * 
                cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + 
                sin(radians(?)) * 
                sin(radians(latitude))
            )
        ) AS distance', [$latitude, $longitude, $latitude])
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }
}
