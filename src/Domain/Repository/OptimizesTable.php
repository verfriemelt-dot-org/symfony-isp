<?php namespace App\Domain\Repository;

use App\Domain\Entity\Table;

interface OptimizesTable {
    /**
     * @return array{Table: string, Op: string, Msg_type: string, Msg_text: string}
     */
    public function optimize(Table $table): array;
}
