<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping\Column as DoctrineColumn;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class Table
{
    public function __construct(
        #[Id]
        #[DoctrineColumn('TABLE_NAME')]
        public readonly string $tableName,
        #[Id]
        #[DoctrineColumn('TABLE_SCHEMA')]
        public readonly string $tableSchema,
        #[DoctrineColumn('TABLE_COLLATION')]
        public readonly string $collation,
        #[DoctrineColumn('DATA_LENGTH')]
        public readonly int $dataSize,
        #[DoctrineColumn('INDEX_LENGTH')]
        public readonly int $indexSize,
        #[DoctrineColumn('AUTO_INCREMENT')]
        public readonly ?int $autoIncrementValue,
    ) {
    }

    public function getSize(): int
    {
        return $this->dataSize + $this->indexSize;
    }

    public function getSizeInMB(): string
    {
        return number_format($this->getSize() / 1024 / 1024, 2);
    }
}
