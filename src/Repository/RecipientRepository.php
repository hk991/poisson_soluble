<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Recipient;
use Doctrine\DBAL\Connection;

final class RecipientRepository
{
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    public function insert(Recipient $recipient): void
    {
        $this->connection->insert('recipients', [
            'insee' => $recipient->getInsee(),
            'phone' => $recipient->getPhone(),
        ]);
    }

    public function findByInsee(string $insee): ?Recipient
    {
        $data = $this->connection->fetchAssociative(
            'SELECT * FROM recipients WHERE insee = :insee',
            ['insee' => $insee]
        );

        return $data ? new Recipient($data['insee'], $data['phone']) : null;
    }
}
