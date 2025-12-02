<?php

namespace App\Notifications;

use App\Models\Contract;
use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Notifications\Notification;
use Modules\Sms\App\Channels\SignalSmsChannel;
use Modules\Sms\App\Traits\MessageFormaterTrait;

class ContractReadyNotification extends Notification
{
    use Queueable, MessageFormaterTrait;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly Contract $contract,
        private readonly Vendor $vendor
    ) {
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
        $contractUrl = url('/api/v1/customers/contracts/' . $this->contract->id);

        $message = $this->line("Your contract with \"{$this->vendor->name}\" is ready. Please read it in the link below:")
            ->line($contractUrl)
            ->cancel();

        return [
            'message' => $message,
            'to' => $notifiable->mobile,
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
            'message' => "Contract ready for application {$this->contract->application_id}",
            'contract_id' => $this->contract->id,
            'vendor_name' => $this->vendor->name,
            'created_at' => now(),
        ];
    }
}
