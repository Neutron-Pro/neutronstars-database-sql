<?php


namespace NeutronStars\Database;

interface Query
{
    const ORDER_BY_ASC = 'ASC';
    const ORDER_BY_DESC = 'DESC';

    public function insertInto(string $columns, string ...$values): self;

    public function onDuplicateKeyUpdate(string $values): self;

    public function update(string $values): self;

    public function delete(): self;

    public function select(string ...$column): self;

    public function leftJoin(string $table, string $condition = ''): self;

    public function leftJoinQuery(QueryBuilder $builder, string $alias, string $condition = ''): self;

    public function where(string $where): self;

    public function groupBy(string $column): self;

    public function having(string $having): self;

    public function orderBy(string $column, string $order = self::ORDER_BY_ASC): self;

    public function limit(int $limit, int $offset = 0): self;


}
