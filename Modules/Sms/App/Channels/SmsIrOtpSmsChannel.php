<?php

namespace Modules\Sms\App\Channels;

use Exception;
use Http;
use Illuminate\Notifications\Notification;
use Log;

class SmsIrOtpSmsChannel
{

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('sms.sms-ir.key');
    }

    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toSMS($notifiable);

        $phoneNumber = $notifiable->routeNotificationForSms();

        try {
            $response = Http::asJson()->acceptJson()->withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'text/plain',
                'x-api-key'    => $this->apiKey,
            ])->post('https://api.sms.ir/v1/send/verify', [
                'mobile'     => $phoneNumber,
                'templateId' => $message['template'],
                'parameters' => [
                    [
                        'name'  => 'CODE',
                        'value' => $message['token'],
                    ]
                ],
            ]);

            dd($response->json());
        } catch (Exception $e) {
            Log::error('Kavenegar SMS Error: '.$e->getMessage());
        }
    }
}
