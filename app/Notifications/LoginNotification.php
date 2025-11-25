<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use Modules\Sms\App\Channels\SignalSmsChannel;
use Modules\Sms\App\Traits\MessageFormaterTrait;
use Morilog\Jalali\Jalalian;

class LoginNotification extends Notification
{
    use Queueable, MessageFormaterTrait;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(): array
    {
        return [DatabaseChannel::class, SignalSmsChannel::class];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(): array
    {
        $message = $this->line("« شناس‌بان »")->line("هشدار")->line("ورود به حساب شما در وب سایت شناس‌بان")->line(Jalalian::now()->format('Y-m-d H:i:s'))->line(request()->ip())->cancel();
        return [
            'message' => $message
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
            'message' => "ورود به سیستم",
            'created_at' => now()
        ];
    }
}
