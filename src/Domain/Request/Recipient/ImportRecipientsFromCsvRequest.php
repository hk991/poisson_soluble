<?php

declare(strict_types=1);

namespace Domain\Request\Recipient;

class ImportRecipientsFromCsvRequest
{
    public function __construct(
        public readonly string $filePath,
    ) {
    }
}
