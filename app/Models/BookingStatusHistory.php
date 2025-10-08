<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'booking_status_history';

    protected $fillable = [
        'booking_request_id',
        'old_status',
        'new_status',
        'changed_by',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public static function logStatusChange(
        BookingRequest $booking,
        string $oldStatus,
        string $newStatus,
        User $user,
        ?string $reason = null,
        ?array $metadata = null
    ): self {
        return self::create([
            'booking_request_id' => $booking->id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $user->id,
            'reason' => $reason,
            'metadata' => $metadata,
        ]);
    }
}
