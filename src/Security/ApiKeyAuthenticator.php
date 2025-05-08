<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ApiKeyAuthenticator
{
    public function __construct(
        private readonly string $apiKey,
    ) {
    }

    public function validate(Request $request): void
    {
        $providedKey = $request->headers->get('X-API-KEY');

        if ($this->apiKey !== $providedKey) {
            throw new UnauthorizedHttpException('', 'Unauthorized: invalid or missing API key');
        }
    }
}
