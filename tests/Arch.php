<?php

test('exceptions')
    ->expect('vityakut\ProxyApi\Exceptions')
    ->toUseNothing();

test('facades')
    ->expect('vityakut\ProxyApi\Facades\ProxyApi')
    ->toOnlyUse([
        'Illuminate\Support\Facades\Facade',
        'OpenAI\Contracts\ResponseContract',
        'vityakut\ProxyApi\Testing\ProxyApiFake',
        'OpenAI\Responses\StreamResponse',
    ]);

test('service providers')
    ->expect('vityakut\ProxyApi\ServiceProvider')
    ->toOnlyUse([
        'GuzzleHttp\Client',
        'Illuminate\Support\ServiceProvider',
        'vityakut\ProxyApi',
        'OpenAI',
        'Illuminate\Contracts\Support\DeferrableProvider',

        // helpers...
        'config',
        'config_path',
    ]);
