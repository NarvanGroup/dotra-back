<?php

namespace App\Notifications;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use Modules\Sms\App\Channels\SignalSmsChannel;
use Modules\Sms\App\Traits\MessageFormaterTrait;

class ConfirmationNotification extends Notification
{
    use Queueable, MessageFormaterTrait;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Customer $customer, private readonly int $otp)
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
        return [DatabaseChannel::class, SignalSmsChannel::class];
    }

    /**
     * Get the SMS representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        $message = $this->line("« شناس‌بان »")->line("مشتری گرامی")->line(" برای اعطای دسترسی به {$notifiable->name} جهت تطبیق اطلاعات کارت ملی شما از ثبت احوال لطفا کد $this->otp را در اختیار فروشنده قرار دهید ")->cancel();
        return [
            'message' => $message,
            'to'      => $this->customer->mobile
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

            'message' => "درخواست استعلام برای مشتری با کد ملی {$this->customer->national_id}",
            'created_at' => now()
        ];
    }
}
