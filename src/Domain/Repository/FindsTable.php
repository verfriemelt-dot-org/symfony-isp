<?php namespace App\Domain\Repository;

use App\Domain\Entity\Table;

interface FindsTable {
    public function findByName(string $name): ?Table;
}
