<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Delivers the 6-digit signup OTP. Sent synchronously on purpose: without a
 * queue worker running, queued mail would sit unsent during development.
 */
class VerifyEmailOtp extends Notification
{
    use Queueable;

    public function __construct(public string $code)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Kode verifikasi email park.')
            ->view('emails.otp', [
                'code' => $this->code,
                'nama' => $notifiable->nama_lengkap,
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}