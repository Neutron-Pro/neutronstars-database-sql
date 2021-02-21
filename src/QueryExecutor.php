<?php

namespace NeutronStars\Database;


class QueryExecutor implements Query
{
    private Database $database;
    /**
     * @var QueryBuilder[]
     */
    private array $queryBuilders;
    private array $parameters = [];
    private array $unions = [];

    public function __construct(Database $database, QueryBuilder $queryBuilder)
    {
        $this->database = $database;
        $this->queryBuilders[] = $queryBuilder;
    }

    public function insertInto(string $columns, string ...$values): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->insertInto($columns, ...$values);
        return $this;
    }

    public function onDuplicateKeyUpdate(string $values): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->onDuplicateKeyUpdate($values);
        return $this;
    }

    public function update(string $values): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->update($values);
        return $this;
    }

    public function delete(): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->delete();
        return $this;
    }

    public function select(string ...$column): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->select(...$column);
        return $this;
    }

    public function join(string $table, string $condition = '',
                         string $join = self::LEFT_JOIN, bool $outer = false): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->join($table, $condition, $join, $outer);
        return $this;
    }

    public function joinQuery(QueryBuilder $builder, string $alias, string $condition = '',
                                  string $join = self::LEFT_JOIN, bool $outer = false): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->joinQuery($builder, $alias, $condition, $join, $outer);
        return $this;
    }

    public function where(string $where): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->where($where);
        return $this;
    }

    public function andWhere(string $where): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->andWhere($where);
        return $this;
    }

    public function whereIn(string $column, QueryBuilder $builder, bool $in = true): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->whereIn($column, $builder, $in);
        return $this;
    }

    public function andWhereIn(string $column, QueryBuilder $builder, bool $in = true): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->andWhereIn($column, $builder, $in);
        return $this;
    }

    public function groupBy(string $column): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->groupBy($column);
        return $this;
    }

    public function having(string $having): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->having($having);
        return $this;
    }

    public function andHaving(string $having): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->andHaving($having);
        return $this;
    }

    public function havingIn(string $column, QueryBuilder $builder, bool $in = true): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->havingIn($column, $builder, $in);
        return $this;
    }

    public function andHavingIn(string $column, QueryBuilder $builder, bool $in = true): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->andHavingIn($column, $builder, $in);
        return $this;
    }

    public function orderBy(string $column, string $order = self::ORDER_BY_ASC): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->orderBy($column, $order);
        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->queryBuilders[count($this->queryBuilders)-1]->limit($limit, $offset);
        return $this;
    }

    public function union(string $table, bool $all = false): self
    {
        $this->unions[] = ' UNION '.($all ? 'ALL ' : '');
        $this->queryBuilders[] = new QueryBuilder($table);
        return $this;
    }

    public function unionQuery(QueryBuilder $queryBuilder, string $alias, bool $all = false): self
    {
        $this->unions[] = ' UNION '.($all ? 'ALL ' : '');
        $this->queryBuilders[] = QueryBuilder::create($queryBuilder, $alias);
        return $this;
    }

    public function setParameters(array $parameters): self
    {
        $this->parameters += $parameters;
        return $this;
    }

    /**
     * @return array|bool|Object|null
     */
    public function getResult()
    {
        $result = $this->database->fetch($this->build(), $this->parameters);
        return $result ? $result : null;
    }

    /**
     * @return array[]|Object[]
     */
    public function getResults(): array
    {
        return $this->database->fetchAll($this->build(), $this->parameters);
    }

    public function execute(): bool
    {
        return $this->database->execute($this->build(), $this->parameters) !== null;
    }

    public function build(): string
    {
        $build = '';
        foreach ($this->queryBuilders as $index => $query) {
            if ($index > 0) {
                $build .= $this->unions[$index-1];
            }
            $build .= $query->build();
        }
        return $build;
    }
}
