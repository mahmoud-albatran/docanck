<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;
class NewOrder extends Notification
{
    use Queueable;

    public $order;
    public $client;
    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, User $user)
    {
        $this->order = $order;
        $this->client = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', FcmChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }


    /**
        * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable)
    {
        // $notifiable->update([
        //     'order_id' => $this->order->id
        // ]);
        $body = sprintf(
            'طلبيه رقم : %s',
            $this->order->id,
        );
        // $url = sprintf(
        //     'https://tebusoft.netlify.app/courses/%s/lecture/%s',
        //     $this->lecture->course_id,
        //     $this->lecture->id
        // );
        $url = sprintf(
            'https://docank.mahmoudalbatran.com/orders'
        );
        return [
            'title' => 'طلب جديد',
            'order_id' => $this->order->id,
            'from' => $this->client,
            'body'     => "طلبية رقم : {$this->order->id}",
            'icon' => $this->client->profile_photo_path,
            // 'url' => 'https://tebusoft.netlify.app/courses/{$this->lecture->course_id}/lecture/{$this->lecture->id}',
            'url' => $url,
        ];
    }

     public function toFcm($notifiable): FcmMessage
    {

        $url = sprintf(
            'https://docank.mahmoudalbatran.com/orders'
        );
        return (new FcmMessage(notification: new FcmNotification(
                title: $this->client->name,
                body: 'طلب جديد',
                image: $this->client->profile_photo_path
            )))
            ->data(['url' => $url, 'order_id' => (string) $this->order->id, 'image' => (string) $this->client->profile_photo_path])
            ->custom([
                'android' => [
                    'notification' => [
                        'color' => '#0A0A0A',
                    ],
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
                'apns' => [
                    'fcm_options' => [
                        'analytics_label' => 'analytics',
                    ],
                ],
            ]);
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
