<?php

declare(strict_types=1);

namespace App\tests;

use App\Entity\Recipient;
use PHPUnit\Framework\TestCase;

class RecipientTest extends TestCase
{
    public function testValidateInseeValid(): void
    {
        $recipient = new Recipient("75001", "0612345678");

        $this->assertSame(1, $recipient->validateInsee());
    }

    public function testValidateInseeInvalid(): void
    {
        $recipient = new Recipient("ABCDE", "0612345678");

        $this->assertSame(0, $recipient->validateInsee());
    }

    public function testValidateInseeTooShort(): void
    {
        $recipient = new Recipient("1234", "0612345678");

        $this->assertSame(0, $recipient->validateInsee());
    }

    public function testValidateInseeTooLong(): void
    {
        $recipient = new Recipient("123456", "0612345678");

        $this->assertSame(0, $recipient->validateInsee());
    }

    public function testValidatePhoneValid(): void
    {
        $recipient = new Recipient("75001", "0123456789");

        $this->assertSame(1, $recipient->validatePhone());
    }

    public function testValidatePhoneInvalid(): void
    {
        $recipient = new Recipient("75001", "12345ABCD");

        $this->assertSame(0, $recipient->validatePhone());
    }

    public function testValidatePhoneTooShort(): void
    {
        $recipient = new Recipient("75001", "1234567");

        $this->assertSame(0, $recipient->validatePhone());
    }

    public function testValidatePhoneTooLong(): void
    {
        $recipient = new Recipient("75001", "0123456789123");

        $this->assertSame(0, $recipient->validatePhone());
    }
}
