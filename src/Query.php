<?php


namespace NeutronStars\Database;

interface Query
{
    public const ORDER_BY_ASC = 'ASC';
    public const ORDER_BY_DESC = 'DESC';

    public const INNER_JOIN = 'INNER';
    public const CROSS_JOIN = 'CROSS';
    public const LEFT_JOIN = 'LEFT';
    public const RIGHT_JOIN = 'RIGHT';
    public const FULL_JOIN = 'FULL';
    public const NATURAL_JOIN = 'NATURAL';

    public function insertInto(string $columns, string ...$values): self;

    public function onDuplicateKeyUpdate(string $values): self;

    public function update(string $values): self;

    public function delete(): self;

    public function select(string ...$column): self;

    public function join(string $table, string $condition = '',
                         string $join = self::LEFT_JOIN, bool $outer = false): self;

    public function joinQuery(QueryBuilder $builder, string $alias, string $condition = '',
                              string $join = self::LEFT_JOIN, bool $outer = false): self;

    public function where(string $where): self;

    public function andWhere(string $where): self;

    public function whereIn(string $column, QueryBuilder $builder, bool $in = true): self;

    public function andWhereIn(string $column, QueryBuilder $builder, bool $in = true): self;

    public function groupBy(string $column): self;

    public function having(string $having): self;

    public function andHaving(string $having): self;

    public function havingIn(string $column, QueryBuilder $builder, bool $in = true): self;

    public function andHavingIn(string $column, QueryBuilder $builder, bool $in = true): self;

    public function orderBy(string $column, string $order = self::ORDER_BY_ASC): self;

    public function limit(int $limit, int $offset = 0): self;


}
