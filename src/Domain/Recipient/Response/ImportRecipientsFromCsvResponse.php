<?php

declare(strict_types=1);

namespace Domain\Recipient\Response;

class ImportRecipientsFromCsvResponse
{
    private bool $success;
    private string $message;
    private array $errors = [];

    public function __construct(bool $success, string $message, array $errors)
    {
        $this->success = $success;
        $this->message = $message;
        $this->errors = $errors;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
