<?php

namespace Modules\Dbit\App\Services;

use App\Enums\InquiriesEnum;
use App\Enums\StatusEnum;
use App\Models\Card;
use App\Models\Customer;
use App\Models\Inquiry;
use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Throwable;

class DbitService
{
    private const DBIT_API_URL = 'https://dbit.ir/api/v1';
    private const RETRY_COUNT = 3;
    private const TIMEOUT = 60;
    private const API_KEY = 'DBIT_API_KEY';

    /**
     * Initialize request for customer verification.
     *
     * @param  Customer  $customer
     * @return bool
     */
    public static function initialization(Customer $customer): bool
    {
        $inquiry = self::logInquiry($customer->id, 'shahkar');

        $response = self::sendRequest('POST', '/shahkar', [
            'national_id' => $customer->national_id,
            'mobile'      => $customer->mobile
        ]);

        ($response['success'] ?? false) ? $inquiry->update(['status' => StatusEnum::SUCCESS]) : $inquiry->update(['status' => StatusEnum::FAILED]);

        return $response['data']['is_match'] ?? false;
    }

    public static function finalization(Customer $customer): Customer
    {
        $requests = self::buildFinalizationRequests($customer);

        // Initialize inquiries for logging
        $inquiries = [];
        foreach ($requests as $request) {
            $inquiries[$request['name']] = self::logInquiry($customer->id, $request['name']);
        }

        // Send requests in parallel
        $responses = Http::pool(static function (Pool $pool) use ($requests) {
            return array_map(static function ($request) use ($pool) {
                return self::sendApiRequest($pool, $request);
            }, $requests);
        });

        // Process responses and update inquiry statuses
        $results = [];
        foreach ($responses as $index => $response) {
            $request = $requests[$index];
            $inquiry = $inquiries[$request['name']];
            $name = $request['name'];
            try {
                if ($response->json('success')) {
                    $inquiry->update(['status' => StatusEnum::SUCCESS]);
                    self::updateCustomer($customer, $response, $name);
                } elseif ($response->json('message') == "اطلاعات هویتی یافت نشد") {
                    self::updateCustomer($customer, $response, $name);
                    $inquiry->update(['status' => StatusEnum::FAILED]);
                }
            } catch (Exception|Throwable) {
                $inquiry->update(['status' => StatusEnum::FATAL]);
            }

        }

        return $customer->fresh();
    }


    /**
     * Build the array of requests for finalization.
     *
     * @param  Customer  $customer
     * @return array
     */
    private static function buildFinalizationRequests(Customer $customer): array
    {
        $requests = [
            [
                'name'     => 'image',
                'url'      => '/idImage',
                'data' => ['national_id' => $customer->national_id, 'birthday' => $customer->birthdate],
                'jsonPath' => 'data.image',
            ],
            [
                'name'     => 'information',
                'url'      => '/idInformation',
                'data' => ['national_id' => $customer->national_id, 'birthday' => $customer->birthdate],
                'jsonPath' => 'data',
            ]
        ];

        if ($customer->card_number) {
            $requests[] = [
                'name'     => 'is_card_number_matched',
                'url'      => '/matchCardId',
                'data'     => [
                    'national_id' => $customer->national_id,
                    'birthday' => $customer->birthdate,
                    'card_number' => $customer->card_number
                ],
                'jsonPath' => 'data.is_match',
            ];
        }

        return $requests;
    }

    /**
     * Send an API request.
     *
     * @param  string  $method
     * @param  string  $endpoint
     * @param  array  $data
     * @return array
     */
    private static function sendRequest(string $method, string $endpoint, array $data): array
    {
        return Http::timeout(self::TIMEOUT)->retry(self::RETRY_COUNT)->asJson()->acceptJson()->withToken(env(self::API_KEY))->$method(self::DBIT_API_URL.$endpoint,
            $data)->json();
    }

    /**
     * Send a single API request within the pool.
     *
     * @param  Pool  $pool
     * @param  array  $request
     * @return PromiseInterface|Response
     * @throws ConnectionException
     */
    private static function sendApiRequest(Pool $pool, array $request)
    {
        return $pool->withToken(env(self::API_KEY))->retry(self::RETRY_COUNT, 100)->post(self::DBIT_API_URL.$request['url'], $request['data']);
    }


    /**
     * Log an inquiry for the request.
     *
     * @param  string|null  $customerId
     * @param  string  $type
     * @return Inquiry
     */
    private static function logInquiry(?string $customerId, string $type): Inquiry
    {
        $type = match ($type) {
            'shahkar' => InquiriesEnum::SHAHKAR->value,
            'image' => InquiriesEnum::IDIMAGE->value,
            'information' => InquiriesEnum::IDINFORMATION->value,
            'is_card_number_matched' => InquiriesEnum::MATCHCARDID->value
        };
        return Auth::user()->inquiries()->create([
            'customer_id' => $customerId ?? null,
            'type'        => $type,
            'status'      => StatusEnum::PENDING
        ]);
    }

    private static function updateCustomer(Customer $customer, Response $response, string $type)
    {
        $columns = match ($type) {
            'shahkar' => [
                'shahkar' => $response->json('data.is_match') ?? false,
            ],
            'image' => [
                'image' => $response->json('data.image') ?? false,
            ],
            'information' => [
                'id_information' => (bool) $response->json('data.first_name'),
                'first_name'            => $response->json('data.first_name'),
                'last_name'             => $response->json('data.last_name'),
                'father_name'           => $response->json('data.father_name'),
                'gender'                => $response->json('data.gender'),
                'live_status'           => $response->json('data.live_status'),
                'identification_number' => $response->json('data.identification_number'),
                'identification_serial' => $response->json('data.identification_serial'),
                'identification_series' => $response->json('data.identification_series'),
                'office_name'           => $response->json('data.office_name'),
            ],
            'is_card_number_matched' => [
                'is_matched_card_number' => $response->json('data.is_match') ?? false,
            ],
            default => throw new InvalidArgumentException("Invalid type: $type"),
        };

        $customer->update($columns);
    }

    public static function verifyCard(Card $card)
    {
        $card->update(['bank' => self::getCardBank($card->card_number)]);
        $inquiry = self::logInquiry(null, 'is_card_number_matched');
        try {
            $response = self::sendRequest('POST', '/matchCardId', [
                'national_id' => $card->national_id,
                'birthday'    => $card->birthdate,
                'card_number' => $card->card_number
            ]);
        } catch (Exception) {
            $inquiry->update(['status' => StatusEnum::FAILED]);
            return false;
        }


        ($response['success'] ?? false) ? $inquiry->update(['status' => StatusEnum::SUCCESS]) : $inquiry->update(['status' => StatusEnum::FAILED]);

        return $response['data']['is_match'] ?? false;
    }

    public static function getCardBank(int $cardNumber)
    {
        // Bank codes and names
        $banksCode = [
            '207177' => 'بانک توسعه صادرات ایران',
            '502229' => 'بانک پاسارگاد',
            '502806' => 'بانک شهر',
            '502908' => 'بانک توسعه تعاون',
            '502910' => 'بانک کارآفرین',
            '502938' => 'بانک دی',
            '505416' => 'بانک گردشگری',
            '505785' => 'بانک ایران زمین',
            '505801' => ' موسسه اعتباری کوثر',
            '589210' => 'بانک سپه',
            '589463' => ' بانک رفاه کارگران',
            '603769' => 'بانک صادرات ایران',
            '603770' => 'بانک کشاورزی',
            '603799' => 'بانک ملی ایران',
            '606373' => 'بانک قرض الحسنه مهر ایران',
            '610433' => 'بانک ملت',
            '621986' => 'بانک سامان',
            '622106' => 'بانک پارسیان',
            '627353' => 'بانک تجارت',
            '627381' => 'بانک انصار',
            '627412' => 'بانک اقتصاد نوین',
            '627488' => 'بانک کارآفرین',
            '627648' => 'بانک توسعه صادرات ایران',
            '627760' => 'پست بانک ایران',
            '627884' => 'بانک پارسیان',
            '627961' => 'بانک صنعت و معدن',
            '628023' => 'بانک مسکن',
            '628157' => 'موسسه اعتباری توسعه',
            '636214' => 'بانک تات',
            '636795' => 'بانک مرکزی',
            '636949' => 'بانک حکمت ایرانیان',
            '639194' => 'بانک پارسیان',
            '639217' => 'بانک کشاورزی',
            '639346' => 'بانک سینا',
            '639347' => 'بانک پاسارگاد',
            '639370' => 'بانک مهر اقتصاد',
            '639599' => 'بانک قوامین',
            '639607' => 'بانک سرمایه',
            '991975' => 'بانک ملت'
        ];

        return $banksCode[substr($cardNumber, 0, 6)] ?? null;
    }

}
