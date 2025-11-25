<?php

namespace Modules\Sms\App\Channels;

use Exception;
use Http;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;
use Log;

class SignalOtpSmsChannel
{

    private string $apiKey;
    private $baseUrl;
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed|null
     */
    private mixed $sender;
    /**
     * @var \Illuminate\Config\Repository|\Illuminate\Foundation\Application|mixed|null
     */
    private mixed $base;

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
        $message = $notification->toSMS($notifiable);

        $phoneNumber = $notifiable->routeNotificationForSms();

        try {
            $response = $this->sendRequest('/pattern.json', 'POST', [
                "from"       => $this->sender,
                "pattern_id" => "610",
                "number"     => $phoneNumber,
                "params"     => [
                    "code" => $message['token']
                ]
            ]);
            dd($response->json());
        } catch (Exception $e) {
            dd($e);
            Log::error('Kavenegar SMS Error: '.$e->getMessage());
        }
    }
}
