<?php

namespace NeutronStars\Database;


class QueryExecutor implements Query
{
    private Database $database;
    private QueryBuilder $queryBuilder;
    private array $parameters = [];

    public function __construct(Database $database, QueryBuilder $queryBuilder)
    {
        $this->database = $database;
        $this->queryBuilder = $queryBuilder;
    }

    public function insertInto(string $columns, string ...$values): self
    {
        $this->queryBuilder->insertInto($columns, ...$values);
        return $this;
    }

    public function onDuplicateKeyUpdate(string $values): self
    {
        $this->queryBuilder->onDuplicateKeyUpdate($values);
        return $this;
    }

    public function update(string $values): self
    {
        $this->queryBuilder->update($values);
        return $this;
    }

    public function delete(): self
    {
        $this->queryBuilder->delete();
        return $this;
    }

    public function select(string ...$column): self
    {
        $this->queryBuilder->select(...$column);
        return $this;
    }

    public function leftJoin(string $table, string $condition = ''): self
    {
        $this->queryBuilder->leftJoin($table, $condition);
        return $this;
    }

    public function leftJoinQuery(QueryBuilder $builder, string $alias, string $condition = ''): self
    {
        $this->queryBuilder->leftJoinQuery($builder, $alias, $condition);
        return $this;
    }

    public function where(string $where): self
    {
        $this->queryBuilder->where($where);
        return $this;
    }

    public function groupBy(string $column): self
    {
        $this->queryBuilder->groupBy($column);
        return $this;
    }

    public function having(string $having): self
    {
        $this->queryBuilder->having($having);
        return $this;
    }

    public function orderBy(string $column, string $order = self::ORDER_BY_ASC): self
    {
        $this->queryBuilder->orderBy($column, $order);
        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->queryBuilder->limit($limit, $offset);
        return $this;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters += $parameters;
        return $this;
    }

    public function getResult(): ?array
    {
        return $this->database->fetch($this->queryBuilder->build(), $this->parameters);
    }

    public function getResults(): array
    {
        return $this->database->fetchAll($this->queryBuilder->build(), $this->parameters);
    }

    public function execute(): bool
    {
        return $this->database->execute($this->queryBuilder->build(), $this->parameters) !== null;
    }
}
