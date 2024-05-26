<?php

use Illuminate\Config\Repository;
use OpenAI\Client;
use OpenAI\Contracts\ClientContract;
use vityakut\ProxyApi\Exceptions\ApiKeyIsMissing;
use vityakut\ProxyApi\ServiceProvider;

it('binds the client on the container', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'proxyapi' => [
            'api_key' => 'test',
        ],
    ]));

    (new ServiceProvider($app))->register();

    expect($app->get(Client::class))->toBeInstanceOf(Client::class);
});

it('binds the client on the container as singleton', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'proxyapi' => [
            'api_key' => 'test',
        ],
    ]));

    (new ServiceProvider($app))->register();

    $client = $app->get(Client::class);

    expect($app->get(Client::class))->toBe($client);
});

it('requires an api key', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([]));

    (new ServiceProvider($app))->register();
})->throws(
    ApiKeyIsMissing::class,
    'The Proxy API Key is missing. Please publish the [proxyapi.php] configuration file and set the [api_key].',
);

it('provides', function () {
    $app = app();

    $provides = (new ServiceProvider($app))->provides();

    expect($provides)->toBe([
        Client::class,
        ClientContract::class,
        'proxyapi',
    ]);
});
