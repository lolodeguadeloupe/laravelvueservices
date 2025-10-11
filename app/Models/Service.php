<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class Service extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'provider_id',
        'title',
        'slug',
        'description',
        'short_description',
        'price',
        'price_type',
        'duration',
        'location',
        'images',
        'requirements',
        'is_active',
        'is_featured',
        'rating',
        'reviews_count',
        'bookings_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration' => 'integer',
        'location' => 'array',
        'images' => 'array',
        'requirements' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'decimal:2',
        'reviews_count' => 'integer',
        'bookings_count' => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Service $service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(ServicePackage::class);
    }

    public function zones(): HasMany
    {
        return $this->hasMany(ServiceZone::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ServiceMedia::class);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, BookingRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeByProvider($query, $providerId)
    {
        return $query->where('provider_id', $providerId);
    }

    public function scopeInPriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        return $query;
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = number_format($this->price, 2);
        $suffix = match ($this->price_type) {
            'hourly' => '/h',
            'fixed' => '',
            default => '',
        };

        return "€{$price}{$suffix}";
    }

    public function getFirstImageAttribute(): ?string
    {
        return $this->images[0] ?? null;
    }
}
