<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Storage\Mysql;

use App\Shared\Domain\Exception\NotFoundException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractDataProvider
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * @return mixed
     */
    protected function fetchColumn(QueryBuilder $qb)
    {
        $stm = $qb->execute();
        if (!$stm instanceof Statement) {
            throw new \RuntimeException('Query execution did not returned a statement.');
        }

        $result = $stm->fetchColumn(0);
        if ($result === false) {
            throw new \RuntimeException('Cannot retrieve the column.');
        }

        return $result;
    }

    protected function fetchIntColumn(QueryBuilder $qb): int
    {
        return (int) $this->fetchColumn($qb);
    }

    /**
     * @return mixed[]
     */
    protected function fetchAll(QueryBuilder $qb)
    {
        $stm = $qb->execute();
        if (!$stm instanceof Statement) {
            throw new \RuntimeException('Query execution did not returned a statement.');
        }

        return $stm->fetchAll();
    }

    /**
     * @throws NotFoundException
     * @return mixed
     */
    protected function fetch(QueryBuilder $qb)
    {
        $stm = $qb->execute();
        if (!$stm instanceof Statement) {
            throw new \RuntimeException('Query execution did not returned a statement.');
        }

        $row = $stm->fetch();
        if (is_bool($row)) {
            throw new NotFoundException('Cannot retrieve result from query.');
        }

        return $row;
    }
}
