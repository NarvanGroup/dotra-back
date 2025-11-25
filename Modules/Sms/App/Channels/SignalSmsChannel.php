<?php

namespace Modules\Sms\App\Channels;

use Exception;
use Http;
use Illuminate\Notifications\Notification;
use Log;

class SignalSmsChannel
{
    private string $apiKey;

    private string $sender;

    public function __construct()
    {
        $this->apiKey = config('sms.signal.key');
        $this->sender = config('sms.signal.sender');
        $this->baseUrl = config('sms.signal.baseUrl');
    }

    /**
     * Centralized HTTP request handler.
     */
    protected function sendRequest(string $endpoint, string $method, array $params = [], array $headers = [])
    {
        $url = "{$this->baseUrl}/{$endpoint}";
        $http = Http::timeout(60)->retry(3)->withToken($this->apiKey)->withHeaders($headers);

        return match ($method) {
            'GET' => $http->get($url, $params),
            'POST' => $http->post($url, $params),
            default => throw new InvalidArgumentException("Unsupported HTTP method: {$method}"),
        };
    }

    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toSMS($notifiable)['message'] ?? $notification->toSMS($notifiable);
        $phoneNumber = $notification->toSMS($notifiable)['to'] ?? $notifiable->routeNotificationForSms();

        try {
            $response = $this->sendRequest('/send.json', 'POST', [
                "from"    => $this->sender,
                "message" => $message,
                "numbers" => [$phoneNumber],
            ]);
            dd($response->json());
        } catch (Exception $e) {
            dd($e);
            Log::error('Kavenegar SMS Error: '.$e->getMessage());
        }
    }
}
