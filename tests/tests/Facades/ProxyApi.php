<?php

use Illuminate\Config\Repository;
use OpenAI\Resources\Completions;
use OpenAI\Responses\Completions\CreateResponse;
use PHPUnit\Framework\ExpectationFailedException;
use vityakut\ProxyApi\Facades\ProxyApi;
use vityakut\ProxyApi\ServiceProvider;

it('resolves resources', function () {
    $app = app();

    $app->bind('config', fn () => new Repository([
        'proxyapi' => [
            'api_key' => 'test',
        ],
    ]));

    (new ServiceProvider($app))->register();

    ProxyApi::setFacadeApplication($app);

    $completions = ProxyApi::completions();

    expect($completions)->toBeInstanceOf(Completions::class);
});

test('fake returns the given response', function () {
    ProxyApi::fake([
        CreateResponse::fake([
            'choices' => [
                [
                    'text' => 'awesome!',
                ],
            ],
        ]),
    ]);

    $completion = ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    expect($completion['choices'][0]['text'])->toBe('awesome!');
});

test('fake throws an exception if there is no more given response', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);
})->expectExceptionMessage('No fake responses left');

test('append more fake responses', function () {
    ProxyApi::fake([
        CreateResponse::fake([
            'id' => 'cmpl-1',
        ]),
    ]);

    ProxyApi::addResponses([
        CreateResponse::fake([
            'id' => 'cmpl-2',
        ]),
    ]);

    $completion = ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    expect($completion)
        ->id->toBe('cmpl-1');

    $completion = ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    expect($completion)
        ->id->toBe('cmpl-2');
});

test('fake can assert a request was sent', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    ProxyApi::assertSent(Completions::class, function (string $method, array $parameters): bool {
        return $method === 'create' &&
            $parameters['model'] === 'gpt-3.5-turbo-instruct' &&
            $parameters['prompt'] === 'PHP is ';
    });
});

test('fake throws an exception if a request was not sent', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
    ]);

    ProxyApi::assertSent(Completions::class, function (string $method, array $parameters): bool {
        return $method === 'create' &&
            $parameters['model'] === 'gpt-3.5-turbo-instruct' &&
            $parameters['prompt'] === 'PHP is ';
    });
})->expectException(ExpectationFailedException::class);

test('fake can assert a request was sent on the resource', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    ProxyApi::completions()->assertSent(function (string $method, array $parameters): bool {
        return $method === 'create' &&
            $parameters['model'] === 'gpt-3.5-turbo-instruct' &&
            $parameters['prompt'] === 'PHP is ';
    });
});

test('fake can assert a request was sent n times', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
        CreateResponse::fake(),
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    ProxyApi::assertSent(Completions::class, 2);
});

test('fake throws an exception if a request was not sent n times', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
        CreateResponse::fake(),
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    ProxyApi::assertSent(Completions::class, 2);
})->expectException(ExpectationFailedException::class);

test('fake can assert a request was not sent', function () {
    ProxyApi::fake();

    ProxyApi::assertNotSent(Completions::class);
});

test('fake throws an exception if a unexpected request was sent', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    ProxyApi::assertNotSent(Completions::class);
})->expectException(ExpectationFailedException::class);

test('fake can assert a request was not sent on the resource', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
    ]);

    ProxyApi::completions()->assertNotSent();
});

test('fake can assert no request was sent', function () {
    ProxyApi::fake();

    ProxyApi::assertNothingSent();
});

test('fake throws an exception if any request was sent when non was expected', function () {
    ProxyApi::fake([
        CreateResponse::fake(),
    ]);

    ProxyApi::completions()->create([
        'model' => 'gpt-3.5-turbo-instruct',
        'prompt' => 'PHP is ',
    ]);

    ProxyApi::assertNothingSent();
})->expectException(ExpectationFailedException::class);
