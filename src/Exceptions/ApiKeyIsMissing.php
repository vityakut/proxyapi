<?php

declare(strict_types=1);

namespace vityakut\ProxyApi\Exceptions;

use InvalidArgumentException;

/**
 * @internal
 */
final class ApiKeyIsMissing extends InvalidArgumentException
{
    /**
     * Create a new exception instance.
     */
    public static function create(): self
    {
        return new self(
            'The Proxy API Key is missing. Please publish the [proxyapi.php] configuration file and set the [api_key].'
        );
    }
}
