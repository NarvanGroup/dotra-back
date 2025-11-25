<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use Modules\Sms\App\Channels\SignalOtpSmsChannel;
use Modules\Sms\App\Traits\MessageFormaterTrait;

class OtpNotification extends Notification
{
    use Queueable, MessageFormaterTrait;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly int $otp)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [DatabaseChannel::class, SignalOtpSmsChannel::class];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        return [
            'token'    => $this->otp
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message'    => "درخواست رمز یکبار مصرف",
            'created_at' => now()
        ];
    }
}
