<?php

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use Modules\Sms\App\Traits\MessageFormaterTrait;

class FinalizationNotification extends Notification
{
    use Queueable, MessageFormaterTrait;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Customer $customer)
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
        return [DatabaseChannel::class];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "نهایی سازی استعلام برای مشتری با کد ملی {$this->customer->national_id}",
            'created_at' => now()
        ];
    }
}
