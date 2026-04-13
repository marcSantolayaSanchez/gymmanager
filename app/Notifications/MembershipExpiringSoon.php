<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipExpiringSoon extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Client $client) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $daysLeft = now()->diffInDays($this->client->membership_expires_at);
        $planName = $this->client->membership?->name ?? 'tu membresía';

        return (new MailMessage)
            ->subject("⚠️ Tu membresía {$planName} vence en {$daysLeft} días")
            ->greeting("¡Hola, {$notifiable->name}!")
            ->line("Tu membresía **{$planName}** vence el **{$this->client->membership_expires_at->format('d/m/Y')}**.")
            ->line("Renueva ahora para no perder tu acceso al gimnasio.")
            ->action('Renovar membresía', url('/renovar'))
            ->line('Si ya renovaste, ignora este mensaje.')
            ->salutation('El equipo de GymManager');
    }
}
