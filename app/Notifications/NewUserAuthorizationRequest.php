<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;

class NewUserAuthorizationRequest extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newUser;

    public function __construct(User $user)
    {
        $this->newUser = $user;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $adminUrl = url('/admin/users');

        return (new MailMessage)
            ->subject('Nueva Solicitud de Autorización - Ferrechincha')
            ->view('emails.auth-request', [
                'user' => $this->newUser,
                'adminUrl' => $adminUrl
            ]);
    }
} 