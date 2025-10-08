<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Get messages for a booking request
     */
    public function index(BookingRequest $booking)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur peut voir cette conversation
        if ($booking->client_id !== $user->id && $booking->provider_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette conversation.');
        }

        $messages = $booking->messages()
            ->with(['sender.profile', 'receiver.profile'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer les messages comme lus
        $booking->messages()
            ->where('receiver_id', $user->id)
            ->unread()
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages,
            'booking' => $booking->load(['service', 'client.profile', 'provider.profile']),
        ]);
    }

    /**
     * Send a new message
     */
    public function store(Request $request, BookingRequest $booking)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if ($booking->client_id !== $user->id && $booking->provider_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas autorisé à envoyer des messages dans cette conversation.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'in:text,image,document',
            'attachments' => 'nullable|array',
            'attachments.*' => 'string', // URLs vers les fichiers uploadés
        ]);

        // Déterminer le destinataire
        $receiverId = $booking->client_id === $user->id 
            ? $booking->provider_id 
            : $booking->client_id;

        $message = Message::create([
            'booking_request_id' => $booking->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'content' => $validated['content'],
            'type' => $validated['type'] ?? 'text',
            'attachments' => $validated['attachments'] ?? null,
        ]);

        $message->load(['sender.profile', 'receiver.profile']);

        // TODO: Envoyer notification en temps réel via Pusher/WebSocket
        // TODO: Envoyer notification email si le destinataire n'est pas en ligne

        return response()->json([
            'message' => $message,
            'success' => true,
        ], 201);
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message)
    {
        $user = Auth::user();

        if ($message->receiver_id !== $user->id) {
            abort(403, 'Vous ne pouvez pas marquer ce message comme lu.');
        }

        $message->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Get unread messages count for user
     */
    public function unreadCount()
    {
        $user = Auth::user();

        $count = Message::where('receiver_id', $user->id)
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }
}
