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
class TrackOrder extends Notification
{
    use Queueable;

    public $order;
    public $admin;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order, User $user, $statusOrder)
    {
        //
        $this->order = $order;
        $this->admin = $user;
        $this->status = $statusOrder;

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
        // $body = sprintf(
        //     'طلبيه رقم : %s',
        //     $this->order->id,
        // );
        // $url = sprintf(
        //     'https://tebusoft.netlify.app/courses/%s/lecture/%s',
        //     $this->lecture->course_id,
        //     $this->lecture->id
        // );
        $url = sprintf(
            'https://docank.mahmoudalbatran.com/orders'
        );
        return [
            'title' => $this->admin->name,
            'order_id' => $this->order->id,
            'from' => $this->admin,
            'body' => $this->status,
            'icon' => $this->admin->profile_photo_path,
            // 'url' => 'https://tebusoft.netlify.app/courses/{$this->lecture->course_id}/lecture/{$this->lecture->id}',
            'url' => $url,
        ];
    }

     public function toFcm($notifiable): FcmMessage
    {
        // $url = sprintf(
        //     'https://tebusoft.netlify.app/courses/%s/lecture/%s',
        //     $this->lecture->course_id,
        //     $this->lecture->id
        // );
        $url = sprintf(
            'https://docank.mahmoudalbatran.com/orders'
        );
        return (new FcmMessage(notification: new FcmNotification(
                title: $this->admin->name,
                body: $this->status,
                image: $this->admin->profile_photo_path
            )))
            ->data(['url' => $url, 'order_id' => (string) $this->order->id, 'icon' => (string) $this->admin->profile_photo_path])
            ->custom([
            'android' => [
                'priority' => 'high',
                'notification' => [
                    'title' => $this->admin->name,
                    'body'  => $this->status,
                    'image' => $this->admin->profile_photo_path,
                    'channel_id' => 'default',
                    'sound' => 'default',
                    'color' => '#0A0A0A',
                ],
            ],
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
                'payload' => [
                    'aps' => [
                        'alert' => [
                            'title' => $this->admin->name,
                            'body' => $this->status,
                        ],
                        'sound' => 'default',
                    ],
                ],
                'fcm_options' => [
                    'image' => $this->admin->profile_photo_path,
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
