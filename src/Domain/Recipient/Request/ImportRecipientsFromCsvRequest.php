<?php

declare(strict_types=1);

namespace Domain\Recipient\Request;

class ImportRecipientsFromCsvRequest
{
    public function __construct(
        public readonly string $filePath,
    ) {
    }
}
