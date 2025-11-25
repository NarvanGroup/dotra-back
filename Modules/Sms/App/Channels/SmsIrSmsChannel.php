<?php

namespace Modules\Sms\App\Channels;

use Exception;
use Http;
use Illuminate\Notifications\Notification;
use Log;

class SmsIrSmsChannel
{
    private string $apiKey;

    private string $sender;

    public function __construct()
    {
        $this->apiKey = config('sms.sms-ir.key');
        $this->username = config('sms.sms-ir.username');
        $this->sender = config('sms.sms-ir.sender');
    }

    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toSMS($notifiable);

        $phoneNumber = '09306057083';

        try {
            $response = Http::withHeaders([
                'Accept' => 'text/plain',
            ])->get('https://api.sms.ir/v1/send', [
                'username' => $this->username,
                'password' => $this->apiKey,
                'mobile'   => $phoneNumber,
                'line'     => $this->sender,
                'text'     => $message,
            ]);

            dd($response->json());
        } catch (Exception $e) {
            Log::error('Kavenegar SMS Error: '.$e->getMessage());
        }
    }
}
