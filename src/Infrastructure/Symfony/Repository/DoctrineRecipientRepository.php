<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Repository;

use Doctrine\DBAL\Connection;
use Domain\Recipient\Entity\Recipient;
use Domain\Recipient\Gateway\RecipientRepositoryInterface;

final class DoctrineRecipientRepository implements RecipientRepositoryInterface
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
