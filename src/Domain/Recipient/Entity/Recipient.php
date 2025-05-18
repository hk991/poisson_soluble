<?php

declare(strict_types=1);

namespace Domain\Recipient\Entity;

class Recipient
{
    private string $insee;
    private string $phone;

    public function __construct(string $insee, string $phone)
    {
        $this->insee = $insee;
        $this->phone = $phone;
    }

    public function getInsee(): string
    {
        return $this->insee;
    }

    public function setInsee(string $insee): void
    {
        $this->insee = $insee;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function validateInsee(): bool
    {
        return preg_match('/^\d{5}$/', $this->insee) === 1;
    }

    public function validatePhone(): bool
    {
        return preg_match('/^\d{10}$/', $this->phone) === 1;
    }
}
