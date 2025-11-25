<?php

return [
    'name'      => 'Sms',
    'kavenegar' => [
        'key'    => env('KAVENEGAR_API_KEY'),
        'sender' => env('KAVENEGAR_SENDER')
    ],

    'sms-ir' => [
        'key'      => env('SMS_IR_API_KEY'),
        'username' => env('SMS_IR_USERNAME'),
        'sender'   => env('SMS_IR_SENDER')
    ],

    'signal' => [
        'key'     => env('SIGNAL_API_KEY'),
        'baseUrl' => env('SIGNAL_BASE_URL'),
        'sender'  => env('SIGNAL_SENDER')
    ],

    'amoot' => [
        'key'     => env('AMOOT_API_KEY'),
        'baseUrl' => env('AMOOT_BASE_URL'),
        'sender'  => env('AMOOT_SENDER')
    ]
];
