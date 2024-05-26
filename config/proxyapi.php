<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Proxy API Key
    |--------------------------------------------------------------------------
    |
    | Here you may specify your Proxy API Key. This will be
    | used to authenticate with the Proxy API - you can find your API key
    | and on your dashboard, at https://proxyapi.ru/.
    */

    'api_key' => env('PROXYAPI_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | The timeout may be used to specify the maximum number of seconds to wait
    | for a response. By default, the client will time out after 30 seconds.
    */

    'request_timeout' => env('PROXYAP_REQUEST_TIMEOUT', 30),
];
