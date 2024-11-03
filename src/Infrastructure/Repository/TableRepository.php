<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Table;
use App\Domain\Repository\FindsTable;
use App\Domain\Repository\OptimizesTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Table>
 */
class TableRepository extends ServiceEntityRepository implements FindsTable, OptimizesTable
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Table::class);
    }

    public function findByName(string $name): ?Table
    {
        $resultSetMappingBuilder = new ResultSetMappingBuilder($this->getEntityManager());
        $resultSetMappingBuilder->addRootEntityFromClassMetadata(Table::class, 't');
        $selectClause = $resultSetMappingBuilder->generateSelectClause();

        $dql = <<<SQL
                select {$selectClause}
                from information_schema.TABLES t
                where TABLE_SCHEMA = database() and TABLE_NAME = :name
                 ;
            SQL;

        $result = $this->getEntityManager()
            ->createNativeQuery($dql, $resultSetMappingBuilder)
            ->setParameter('name', $name)
            ->getSingleResult()
        ;

        \assert($result instanceof Table);

        return $result;
    }

    public function optimize(Table $table): array
    {
        return $this->getEntityManager()->getConnection()->executeQuery("OPTIMIZE TABLE {$table->tableName};")->fetchAllAssociative();
    }
}
