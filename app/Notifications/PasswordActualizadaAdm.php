<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordActualizadaAdm extends Notification
{
    use Queueable;
    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct($password)
    {
        //
        $this->password = $password;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('¡Hola!')
            ->subject('Su contraseña ha sido actualizada')
            ->line('Reciba un cordial saludo de PAYMASTER SV.')
            ->line('Su contraseña ha sido actualizada por la administración.')
            ->line('Nueva contraseña: ' . $this->password)
            ->line('Si no ha realizado esta actualización, póngase en contacto con nosotros.')
            ->from('paymaster@sv.com', 'PASYMASTER SV');

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
