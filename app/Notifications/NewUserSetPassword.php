<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserSetPassword extends Notification
{
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
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
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
            'new' => 1,
        ], false));
        

        return (new MailMessage)
            ->subject('Crie sua senha de acesso')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Você foi adicionado ao Dently. Clique no botão abaixo para criar sua senha:')
            ->action('Criar Senha', $url)
            ->line('Se você não esperava esse e-mail, apenas ignore.')
            ->salutation('Atenciosamente, Equipe Dently');
    }
}
