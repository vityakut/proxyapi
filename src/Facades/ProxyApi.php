<?php

declare(strict_types=1);

namespace vityakut\ProxyApi\Facades;

use Illuminate\Support\Facades\Facade;
use OpenAI\Contracts\ResponseContract;
use vityakut\ProxyApi\Testing\ProxyApiFake;
use OpenAI\Responses\StreamResponse;

/**
 * @method static \OpenAI\Resources\Assistants assistants()
 * @method static \OpenAI\Resources\Audio audio()
 * @method static \OpenAI\Resources\Chat chat()
 * @method static \OpenAI\Resources\Completions completions()
 * @method static \OpenAI\Resources\Embeddings embeddings()
 * @method static \OpenAI\Resources\Edits edits()
 * @method static \OpenAI\Resources\Files files()
 * @method static \OpenAI\Resources\FineTunes fineTunes()
 * @method static \OpenAI\Resources\Images images()
 * @method static \OpenAI\Resources\Models models()
 * @method static \OpenAI\Resources\Moderations moderations()
 * @method static \OpenAI\Resources\Threads threads()
 */
class ProxyApi extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'proxyapi';
    }

    /**
     * @param  array<array-key, ResponseContract|StreamResponse|string>  $responses
     */
    public static function fake(array $responses = []): ProxyApiFake /** @phpstan-ignore-line */
    {
        $fake = new ProxyApiFake($responses);
        self::swap($fake);

        return $fake;
    }
}
