<?php

declare(strict_types=1);

namespace Domain\Recipient\Gateway;

use Domain\Recipient\Entity\Recipient;

interface RecipientRepositoryInterface
{
    public function insert(Recipient $recipient): void;

    public function findByInsee(string $insee): array;
}
