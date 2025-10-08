<?php

namespace App\Notifications;

use App\Models\BookingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public BookingRequest $booking,
        public string $newStatus,
        public ?string $message = null
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = $this->getMailSubject();
        $greeting = "Bonjour {$notifiable->name},";
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($this->message ?? $this->getDefaultMessage())
            ->action('Voir la réservation', route('bookings.show', $this->booking->uuid))
            ->line('Merci d\'utiliser notre plateforme !');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'booking_uuid' => $this->booking->uuid,
            'new_status' => $this->newStatus,
            'service_title' => $this->booking->service->title,
            'message' => $this->message ?? $this->getDefaultMessage(),
            'action_url' => route('bookings.show', $this->booking->uuid),
        ];
    }

    private function getMailSubject(): string
    {
        return match ($this->newStatus) {
            'quoted' => 'Nouveau devis reçu',
            'accepted' => 'Réservation acceptée',
            'rejected' => 'Réservation refusée',
            'in_progress' => 'Intervention en cours',
            'completed' => 'Intervention terminée',
            'cancelled' => 'Réservation annulée',
            default => 'Mise à jour de votre réservation',
        };
    }

    private function getDefaultMessage(): string
    {
        $serviceName = $this->booking->service->title;
        
        return match ($this->newStatus) {
            'quoted' => "Vous avez reçu un devis pour le service \"{$serviceName}\". Consultez les détails et acceptez si cela vous convient.",
            'accepted' => "Votre demande pour le service \"{$serviceName}\" a été acceptée par le prestataire !",
            'rejected' => "Votre demande pour le service \"{$serviceName}\" a été refusée. Vous pouvez contacter d'autres prestataires.",
            'in_progress' => "L'intervention pour le service \"{$serviceName}\" a commencé.",
            'completed' => "L'intervention pour le service \"{$serviceName}\" est terminée. N'oubliez pas de laisser un avis !",
            'cancelled' => "La réservation pour le service \"{$serviceName}\" a été annulée.",
            default => "Le statut de votre réservation pour \"{$serviceName}\" a été mis à jour.",
        };
    }
}
