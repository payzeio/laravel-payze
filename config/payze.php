<?php

return [

    /*
     * Enable/Disable database logging (used for debugging purposes)
     */
    'log' => (bool) env('PAYZE_LOG', env('APP_ENV') === 'local'),

    /*
     * Enable/Disable SSL verification in Guzzle
     */
    'verify_ssl' => (bool) env('PAYZE_VERIFY_SSL', true),

    /*
     * Success & Fail route names.
     * Update these if you have defined routes under name/namespace, for example "api.payze.succes"
     */
    'routes' => [
        'success' => 'payze.success',
        'fail' => 'payze.fail',
    ],

    /*
     * Success & Fail view names.
     * Set names of success and fail views. Setting null will redirect to "/" by default
     */
    'views' => [
        'success' => 'payze::success',
        'fail' => 'payze::fail',
    ],

    /*
     * Name on transactions table in database
     */
    'transactions_table' => 'payze_transactions',

    /*
     * Name of logs table in database
     */
    'logs_table' => 'payze_logs',

    /*
     * Name of card tokens table in database
     */
    'card_tokens_table' => 'payze_card_tokens',

    /*
     * API key for Payze
     */
    'api_key' => env('PAYZE_API_KEY'),

    /*
     * API secret for Payze
     */
    'api_secret' => env('PAYZE_API_SECRET'),

];
