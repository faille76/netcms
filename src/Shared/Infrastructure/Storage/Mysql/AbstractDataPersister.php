<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Storage\Mysql;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Query\QueryBuilder;
use Psr\Log\LoggerInterface;

abstract class AbstractDataPersister
{
    protected Connection $connection;
    protected LoggerInterface $logger;

    public function __construct(Connection $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    protected function save(QueryBuilder $qb): int
    {
        $response = $qb->execute();
        if (\is_int($response)) {
            return (int) $this->connection->lastInsertId();
        }

        throw new \RuntimeException('Got statement instead of integer during insertion.');
    }

    protected function getLastInsertId(): int
    {
        return (int) $this->connection->lastInsertId();
    }

    protected function insert(string $tableExpression, array $data, array $types = []): bool
    {
        try {
            $this->connection->insert($tableExpression, $data, $types);

            return true;
        } catch (DBALException $e) {
            $this->logger->error('Cannot insert on table "{table}".', [
                'table' => $tableExpression,
                'data' => $data,
                'e' => $e,
            ]);
        }

        return false;
    }

    protected function update(string $tableExpression, array $data, array $identifier, array $types = []): bool
    {
        try {
            $this->connection->update($tableExpression, $data, $identifier, $types);

            return true;
        } catch (DBALException $e) {
            $this->logger->error('Cannot update on table "{table}".', [
                'table' => $tableExpression,
                'data' => $data,
                'identifier' => $identifier,
                'e' => $e,
            ]);
        }

        return false;
    }

    protected function delete(string $tableExpression, array $identifier, array $types = []): bool
    {
        try {
            $this->connection->delete($tableExpression, $identifier, $types);

            return true;
        } catch (DBALException $e) {
            $this->logger->error('Cannot perform deletion on table "{table}".', [
                'table' => $tableExpression,
                'identifier' => $identifier,
                'e' => $e,
            ]);
        }

        return false;
    }

    protected function executeUpdate(string $query, array $params = [], array $types = []): bool
    {
        try {
            $this->connection->executeUpdate($query, $params, $types);

            return true;
        } catch (DBALException $e) {
            $this->logger->error('Cannot perform query "{query}".', [
                'query' => $query,
                'params' => $params,
                'e' => $e,
            ]);
        }

        return false;
    }
}
