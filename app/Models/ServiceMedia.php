<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ServiceMedia extends Model
{
    protected $fillable = [
        'service_id',
        'type',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'alt_text',
        'description',
        'is_primary',
        'is_public',
        'sort_order',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'is_public' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function getHumanFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isDocument(): bool
    {
        return $this->type === 'document';
    }

    public function getImageDimensions(): ?array
    {
        if (! $this->isImage() || ! isset($this->metadata['width'], $this->metadata['height'])) {
            return null;
        }

        return [
            'width' => $this->metadata['width'],
            'height' => $this->metadata['height'],
        ];
    }

    public function getVideoDuration(): ?int
    {
        if (! $this->isVideo() || ! isset($this->metadata['duration'])) {
            return null;
        }

        return $this->metadata['duration'];
    }
}
