<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewReaction extends Model
{
    protected $fillable = [
        'review_id',
        'user_id',
        'type',
        'reason',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isHelpful(): bool
    {
        return $this->type === 'helpful';
    }

    public function isNotHelpful(): bool
    {
        return $this->type === 'not_helpful';
    }

    public function isReport(): bool
    {
        return $this->type === 'report';
    }
}
