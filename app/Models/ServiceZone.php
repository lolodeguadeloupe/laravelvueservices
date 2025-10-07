<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceZone extends Model
{
    protected $fillable = [
        'service_id',
        'name',
        'description',
        'type',
        'latitude',
        'longitude',
        'radius_km',
        'postal_codes',
        'excluded_areas',
        'travel_cost_per_km',
        'min_travel_time',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'travel_cost_per_km' => 'decimal:2',
            'postal_codes' => 'array',
            'excluded_areas' => 'array',
            'is_active' => 'boolean',
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

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function isWithinZone(float $lat, float $lng): bool
    {
        switch ($this->type) {
            case 'radius':
                return $this->isWithinRadius($lat, $lng);
            case 'postal_codes':
                return $this->isWithinPostalCodes($lat, $lng);
            case 'custom':
                return $this->isWithinCustomArea($lat, $lng);
            default:
                return false;
        }
    }

    private function isWithinRadius(float $lat, float $lng): bool
    {
        if (! $this->latitude || ! $this->longitude || ! $this->radius_km) {
            return false;
        }

        $distance = $this->calculateDistance($lat, $lng, $this->latitude, $this->longitude);

        return $distance <= $this->radius_km;
    }

    private function isWithinPostalCodes(float $lat, float $lng): bool
    {
        // Cette méthode nécessiterait une API de géocodage inverse
        // pour convertir lat/lng en code postal
        return false;
    }

    private function isWithinCustomArea(float $lat, float $lng): bool
    {
        // Implémentation pour zones personnalisées
        return false;
    }

    private function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // Rayon de la Terre en km

        $lat1Rad = deg2rad($lat1);
        $lng1Rad = deg2rad($lng1);
        $lat2Rad = deg2rad($lat2);
        $lng2Rad = deg2rad($lng2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLng = $lng2Rad - $lng1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($deltaLng / 2) * sin($deltaLng / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function calculateTravelCost(float $distance): float
    {
        if (! $this->travel_cost_per_km) {
            return 0;
        }

        return $distance * $this->travel_cost_per_km;
    }
}
