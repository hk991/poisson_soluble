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

    public function findByInsee(string $insee): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT * FROM recipients WHERE insee = :insee',
            ['insee' => $insee]
        );

        $recipients = [];
        foreach ($rows as $row) {
            $recipients[] = new Recipient($row['insee'], $row['phone']);
        }

        return $recipients;
    }
}
