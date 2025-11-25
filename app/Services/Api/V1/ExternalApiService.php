<?php

namespace App\Services\Api\V1;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use SensitiveParameter;

class ExternalApiService
{
    public function getShahkar(#[SensitiveParameter] string $token, $mobile, string $nId): bool
    {
        return Http::withToken($token)->post('https://drapi.ir/rest/api/main/it_org/v1.0/istelamshahkar',
            [
                "nationalCode" => $nId,
                "mobileNumber" => $mobile
            ])->json("isMatched");
    }

    public function getCheque(#[SensitiveParameter] string $token, string $nId)
    {
        return Http::withToken($token)->post('https://drapi.ir/rest/api/main/bounced_Cheque_status/v1.0/bounced_cheque_status',
            [
                "nationalId" => $nId,
                "personType" => 1
            ]);
    }

    public function getFacilities(#[SensitiveParameter] string $token, string $trackId, string $otp, string $mobile, string $nId)
    {
        return Http::withToken($token)->get('https://drapi.ir/rest/api/main/facilityInquiry/v1.0/facilityinquiry',
            [
                "mobileNumber" => $mobile,
                "nationalCode" => $nId,
                "trackId"      => $trackId,
                "otp"          => $otp
            ]);
    }

    public function sendFacilitiesSms(string $mobile, string $nId, #[SensitiveParameter] mixed $token)
    {
        return Http::withToken($token)->get('https://drapi.ir/rest/api/main/facilityInquiry/v1.0/getotpsms',
            [
                "mobileNumber" => $mobile
            ])->json("trackId");
    }

    public function getToken()
    {
        return Http::post('https://drapi.ir/auth/http/token', [
            'username' => env('TLS_USERNAME'),
            'password' => env('TLS_PASSWORD'),
        ])->json("access_token");
    }

    public function getSimcart(string $mobile)
    {
        $url = 'https://simcart.com/api/v1/simcard-pricing/free-pricing/';

        $commonData = [
            'simcard'    => $mobile,
            'sim_status' => 'USED',
        ];

        $posData = [...$commonData, 'sim_type' => 'POS'];
        $preData = [...$commonData, 'sim_type' => 'PRE'];

        $responses = Http::pool(static fn(Pool $pool) => [
            $pool->as('pos')->asJson()->post($url, $posData),
            $pool->as('pre')->asJson()->post($url, $preData),
        ]);

        $posResult = $responses['pos']->successful() ? $responses['pos']->json('min_price') : ['error' => $responses['pos']->body()];
        $preResult = $responses['pre']->successful() ? $responses['pre']->json('min_price') : ['error' => $responses['pre']->body()];

        $candidates = [];

        if (!is_array($posResult) && $posResult != 0) {
            $candidates[] = $posResult;
        }

        if (!is_array($preResult) && $preResult != 0) {
            $candidates[] = $preResult;
        }

        $minNonZero = 100000;

        if (!empty($candidates)) {
            $minNonZero = min($candidates);
        }
        return $this->normalizeNumber($minNonZero);
    }

    public function normalizeNumber($number): int
    {
        $min = 100000;
        $max = 100000000;
        if ($number < $min) {
            $number = $min;
        }
        if ($number > $max) {
            $number = $max;
        }

        $newMin = 0;
        $newMax = 100;

        return floor($newMin + ($newMax - $newMin) * ($number - $min) / ($max - $min));
    }
}
