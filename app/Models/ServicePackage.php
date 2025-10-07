<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePackage extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'description',
        'price',
        'sessions_count',
        'validity_days',
        'discount_percentage',
        'is_active',
        'sort_order',
        'conditions',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'is_active' => 'boolean',
            'conditions' => 'array',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getDiscountedPriceAttribute(): float
    {
        if ($this->discount_percentage) {
            return $this->price * (1 - $this->discount_percentage / 100);
        }

        return $this->price;
    }

    public function getPricePerSessionAttribute(): float
    {
        return $this->discounted_price / $this->sessions_count;
    }

    public function isExpired(?\Carbon\Carbon $date = null): bool
    {
        if (! $this->validity_days) {
            return false;
        }

        $date = $date ?? now();
        $expirationDate = $this->created_at->addDays($this->validity_days);

        return $date->gt($expirationDate);
    }
}
