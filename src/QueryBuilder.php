<?php

namespace NeutronStars\Database;

class QueryBuilder implements Query
{
    public static function create(QueryBuilder $builder, string $alias): QueryBuilder
    {
        return new self('('.$builder->build().') AS '.$alias);
    }

    private string $insertInto = '';
    private string $onDuplicateKeyUpdate = '';
    private string $update = '';
    private string $delete = '';
    private string $select = '';
    private string $leftJoin = '';
    private string $where = '';
    private string $groupBy = '';
    private string $having = '';
    private string $orderBy = '';
    private string $limit = '';
    private string $table;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function insertInto(string $columns, string ...$values): self
    {
        $this->insertInto = 'INSERT INTO '.$this->table.' ('.$columns.') VALUES ('.implode('),(', $values).')';
        return $this;
    }

    public function onDuplicateKeyUpdate(string $values): self
    {
        $this->onDuplicateKeyUpdate = ' ON DUPLICATE KEY UPDATE '.$values;
        return $this;
    }

    public function update(string $values): self
    {
        $this->update = 'UPDATE '.$this->table.' SET '.$values;
        return $this;
    }

    public function delete(): self
    {
        $this->delete = 'DELETE FROM '.$this->table;
        return $this;
    }

    public function select(string ...$column): self
    {
        $this->select = 'SELECT '.implode(',', $column).' FROM '.$this->table;
        return $this;
    }

    public function join(string $table, string $condition = '',
                         string $join = self::LEFT_JOIN, bool $outer = false): self
    {
        $this->leftJoin .= ' '.$join.($outer ? ' OUTER ' : '').' JOIN '
            .$table.(!empty($condition) ? ' ON '.$condition : '');
        return $this;
    }

    public function joinQuery(QueryBuilder $builder, string $alias, string $condition = '',
                              string $join = self::LEFT_JOIN, bool $outer = false): self
    {
        return $this->join('('.$builder->build().') AS '.$alias, $condition, $join, $outer);
    }

    public function where(string $where): self
    {
        $this->where = ' WHERE '.$where;
        return $this;
    }

    public function andWhere(string $where): self
    {
        $this->where .= ' AND '.$where;
        return $this;
    }

    public function whereIn(string $column, QueryBuilder $builder, bool $in = true): self
    {
        $this->where = ' WHERE '.$column.' '.(!$in ? 'NOT' : '').' IN ('.$builder->build().')';
        return $this;
    }

    public function andWhereIn(string $column, QueryBuilder $builder, bool $in = true): self
    {
        $this->where .= ' AND '.$column.' '.(!$in ? 'NOT' : '').' IN ('.$builder->build().')';
        return $this;
    }

    public function groupBy(string $columns): self
    {
        $this->groupBy = ' GROUP BY '.$columns;
        return $this;
    }

    public function having(string $having): self
    {
        $this->having = ' HAVING '.$having;
        return $this;
    }

    public function andHaving(string $having): self
    {
        $this->having .= ' AND '.$having;
        return $this;
    }

    public function havingIn(string $column, QueryBuilder $builder, bool $in = true): self
    {
        $this->having = ' HAVING '.$column.' '.(!$in ? 'NOT' : '').' IN ('.$builder->build().')';
        return $this;
    }

    public function andHavingIn(string $column, QueryBuilder $builder, bool $in = true): self
    {
        $this->having .= ' AND '.$column.' '.(!$in ? 'NOT' : '').' IN ('.$builder->build().')';
        return $this;
    }

    public function orderBy(string $column, string $order = self::ORDER_BY_ASC): self
    {
        if (empty($this->orderBy)) {
            $this->orderBy = ' ORDER BY '.$column.' '.$order;
        } else {
            $this->orderBy .= ', ' . $column . ' ' . $order;
        }
        return $this;
    }

    public function limit(int $limit, int $offset = 0): self
    {
        $this->limit = ' LIMIT '.$offset.','.$limit;
        return $this;
    }

    public function build(): string
    {
        $request = '';

        if(!empty($this->insertInto))
        {
            $request = $this->insertInto;
            if(!empty($this->onDuplicateKeyUpdate)){
                $request .= $this->onDuplicateKeyUpdate;
            }
            return $request;
        }

        if(!empty($this->select)) {
            $request .= $this->select;
        }elseif(!empty($this->update)){
            $request .= $this->update;
        }elseif(!empty($this->delete)) {
            $request .= $this->delete;
        }

        if(!empty($this->leftJoin)) {
            $request .= $this->leftJoin;
        }
        if(!empty($this->where)) {
            $request .= $this->where;
        }
        if(!empty($this->groupBy)) {
            $request .= $this->groupBy;
        }
        if(!empty($this->having)) {
            $request .= $this->having;
        }
        if(!empty($this->orderBy)) {
            $request .= $this->orderBy;
        }
        if(!empty($this->limit)) {
            $request .= $this->limit;
        }
        return $request;
    }
}
