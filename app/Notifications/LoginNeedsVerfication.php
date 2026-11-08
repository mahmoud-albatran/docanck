<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginNeedsVerfication extends Notification
{
    use Queueable;

    protected $randomCode = 0;
    /**
     * Create a new notification instance.
     */
    public function __construct($randomCode)
    {

        $this->randomCode = $randomCode;
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

    // $loginCode = rand(111111, 999999);
    //      $notifiable->update([
    //         'login_code' => $loginCode,
    //         'login_code_expires_at' => now()->addMinutes(5),
    //     ]);

        return (new MailMessage)
        ->subject('رمز التحقق')
        ->greeting('مرحبًا،')
        ->line('رمز التحقق الخاص بك هو:')
        ->line('🔐 ' . $this->randomCode)
        ->line('الرمز صالح لمدة 5 دقائق')
        ->salutation('مع التحية');
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
