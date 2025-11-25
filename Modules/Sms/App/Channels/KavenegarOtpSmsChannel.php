<?php

namespace Modules\Sms\App\Channels;

use Exception;
use Http;
use Illuminate\Notifications\Notification;
use Log;

class KavenegarOtpSmsChannel
{

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('sms.kavenegar.key');
    }

    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toSMS($notifiable);

        $phoneNumber = $notifiable->routeNotificationForSms();

        try {
            Http::asJson()->acceptJson()->post("https://api.kavenegar.com/v1/{$this->apiKey}/verify/lookup.json?receptor={$phoneNumber}&token={$message['token']}&template={$message['template']}");
        } catch (Exception $e) {
            Log::error('Kavenegar SMS Error: '.$e->getMessage());
        }
    }
}
