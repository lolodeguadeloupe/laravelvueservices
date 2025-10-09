<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ConversationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display conversations index
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Récupérer toutes les conversations de l'utilisateur
        $conversations = Conversation::where(function ($query) use ($user) {
                $query->where('participant_1_id', $user->id)
                      ->orWhere('participant_2_id', $user->id);
            })
            ->with([
                'participant1:id,name,email,avatar',
                'participant2:id,name,email,avatar',
                'lastMessage',
                'booking.service'
            ])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                // Calculer le nombre de messages non lus
                $conversation->unread_count = $conversation->messages()
                    ->where('user_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->count();
                
                // Ajouter les participants en tant que tableau
                $conversation->participants = [
                    $conversation->participant1,
                    $conversation->participant2
                ];
                
                return $conversation;
            });

        // Si une conversation spécifique est demandée
        $activeConversation = null;
        $messages = [];
        
        if ($request->conversation) {
            $activeConversation = $conversations->firstWhere('id', $request->conversation);
            
            if ($activeConversation) {
                $messages = $this->getConversationMessages($activeConversation->id);
                
                // Marquer les messages comme lus
                Message::where('conversation_id', $activeConversation->id)
                    ->where('user_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        return Inertia::render('Messages/Index', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'user' => $user
        ]);
    }

    /**
     * Get messages for a specific conversation
     */
    public function show(Conversation $conversation)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$conversation->hasParticipant($user->id)) {
            abort(403, 'Vous n\'avez pas accès à cette conversation.');
        }

        $messages = $this->getConversationMessages($conversation->id);
        
        // Marquer les messages comme lus
        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'messages' => $messages,
            'conversation' => $conversation->load(['participant1', 'participant2', 'booking.service'])
        ]);
    }

    /**
     * Send a new message
     */
    public function store(Request $request, Conversation $conversation)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur fait partie de la conversation
        if (!$conversation->hasParticipant($user->id)) {
            abort(403, 'Vous n\'avez pas accès à cette conversation.');
        }

        $validated = $request->validate([
            'content' => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        // Vérifier qu'il y a du contenu ou un attachement
        if (empty($validated['content']) && !$request->hasFile('attachment')) {
            return response()->json(['error' => 'Le message ne peut pas être vide.'], 422);
        }

        $messageData = [
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $validated['content'] ?? '',
        ];

        // Gérer l'attachement
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('messages/attachments', $fileName, 'public');
            
            $messageData['attachment_url'] = Storage::url($filePath);
            $messageData['attachment_name'] = $file->getClientOriginalName();
            $messageData['attachment_type'] = $file->getMimeType();
        }

        $message = Message::create($messageData);
        
        // Mettre à jour la conversation
        $conversation->touch();
        
        // Charger les relations pour la réponse
        $message->load('user:id,name,avatar');

        // TODO: Envoyer notification en temps réel via WebSocket
        // TODO: Envoyer notification push/email si nécessaire

        return response()->json([
            'message' => $message,
            'success' => true
        ], 201);
    }

    /**
     * Create or find conversation between two users
     */
    public function findOrCreate(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'participant_id' => 'required|exists:users,id',
            'booking_id' => 'nullable|exists:bookings,id'
        ]);

        $participantId = $validated['participant_id'];
        
        // Ne pas créer une conversation avec soi-même
        if ($participantId == $user->id) {
            return response()->json(['error' => 'Impossible de créer une conversation avec vous-même.'], 422);
        }

        // Rechercher une conversation existante
        $conversation = Conversation::where(function ($query) use ($user, $participantId) {
                $query->where('participant_1_id', $user->id)
                      ->where('participant_2_id', $participantId);
            })
            ->orWhere(function ($query) use ($user, $participantId) {
                $query->where('participant_1_id', $participantId)
                      ->where('participant_2_id', $user->id);
            })
            ->first();

        // Créer une nouvelle conversation si elle n'existe pas
        if (!$conversation) {
            $conversation = Conversation::create([
                'participant_1_id' => $user->id,
                'participant_2_id' => $participantId,
                'booking_id' => $validated['booking_id'] ?? null,
            ]);
        }

        return response()->json([
            'conversation' => $conversation->load(['participant1', 'participant2']),
            'redirect_url' => route('messages.conversation', $conversation->id)
        ]);
    }

    /**
     * Get unread messages count
     */
    public function unreadCount()
    {
        $user = Auth::user();
        
        $count = Message::whereHas('conversation', function ($query) use ($user) {
                $query->where('participant_1_id', $user->id)
                      ->orWhere('participant_2_id', $user->id);
            })
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get messages for a conversation
     */
    private function getConversationMessages($conversationId)
    {
        return Message::where('conversation_id', $conversationId)
            ->with('user:id,name,avatar')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Mark conversation messages as read
     */
    public function markAsRead(Conversation $conversation)
    {
        $user = Auth::user();
        
        if (!$conversation->hasParticipant($user->id)) {
            abort(403);
        }

        Message::where('conversation_id', $conversation->id)
            ->where('user_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}