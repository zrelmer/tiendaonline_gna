<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordChangedNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔐 Contraseña actualizada - GNA Core')
            ->greeting('Hola ' . $notifiable->Usu_Nombre . ',')
            ->line('Tu contraseña ha sido cambiada correctamente.')
            ->line('Si realizaste este cambio, puedes ignorar este mensaje.')
            ->line('⚠️ Si NO fuiste tú, te recomendamos cambiar tu contraseña inmediatamente y contactar soporte.')
            ->action('Ir a mi cuenta', url('/login'))
            ->line('Gracias por confiar en GNA Core.');
    }
}
