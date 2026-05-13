<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $otpCode,
        private readonly string $purpose,
        private readonly int $expiresInMinutes = 10,
    ) {}

    /**
     * Create a new notification instance.
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
        $purposeLabel = str($this->purpose)->replace('_', ' ')->title();

        return (new MailMessage)
            ->subject($purposeLabel.' OTP Code')
            ->line('Use the code below to continue your '.$purposeLabel.' request:')
            ->line('OTP Code: '.$this->otpCode)
            ->line('This code expires in '.$this->expiresInMinutes.' minutes.')
            ->line('If you did not request this, please ignore this email.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'purpose' => $this->purpose,
        ];
    }
}
