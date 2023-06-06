<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CorreoPasswordActualizadaAdm extends Notification
{
    use Queueable;
    protected $password;
    protected $newEmail;

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
                    ->subject('Cuenta actualizada')
                    ->line('Reciba un cordial saludo de PAYMASTER SV.')
                    ->line('Su contraseña y correo han sido actualizados por la administración.')
                    ->line('Su nueva contraseña es: ' . $this->password)
                    ->line('Recibirá un correo para confirmar su cuenta. Por favor, verífiquela antes de acceder.')
                    ->line('Para más información, póngase en contacto con nosotros.')
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
