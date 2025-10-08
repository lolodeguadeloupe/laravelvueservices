<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'booking_request_id',
        'sender_id',
        'receiver_id',
        'content',
        'type',
        'attachments',
        'read_at',
        'is_system_message',
    ];

    protected $casts = [
        'attachments' => 'array',
        'read_at' => 'datetime',
        'is_system_message' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Message $message) {
            if (empty($message->uuid)) {
                $message->uuid = Str::uuid();
            }
        });
    }

    public function bookingRequest(): BelongsTo
    {
        return $this->belongsTo(BookingRequest::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function scopeForBooking($query, int $bookingId)
    {
        return $query->where('booking_request_id', $bookingId);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeUserMessages($query)
    {
        return $query->where('is_system_message', false);
    }

    public function scopeSystemMessages($query)
    {
        return $query->where('is_system_message', true);
    }

    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    public function isFromUser(User $user): bool
    {
        return $this->sender_id === $user->id;
    }

    public function hasAttachments(): bool
    {
        return !empty($this->attachments);
    }

    public static function createSystemMessage(
        BookingRequest $booking,
        string $content,
        ?array $metadata = null
    ): self {
        return self::create([
            'booking_request_id' => $booking->id,
            'sender_id' => $booking->provider_id,
            'receiver_id' => $booking->client_id,
            'content' => $content,
            'type' => 'system',
            'is_system_message' => true,
            'attachments' => $metadata,
        ]);
    }
}
