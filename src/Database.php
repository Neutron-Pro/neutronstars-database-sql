<?php

namespace NeutronStars\Database;

use PDO;
use PDOStatement;

class Database
{
    private PDO $pdo;

    /**
     * Database constructor.
     *
     * $data = [
     *   'url': '127.0.0.1',
     *   'port': 3306,
     *   'user': 'root',
     *   'password': '',
     *   'charset': 'utf8mb4',
     *   'fetchMode': 2,
     *   'errorMode': 1
     * ]
     *
     * @param string $dbname
     * @param array $data
     */
    public function __construct(string $dbname, array $data = [])
    {
        $this->connect($dbname, $this->fillDefaultData($data));
    }

    private function connect(string $dbname, array $data)
    {
        $this->pdo = new PDO('mysql:host='.$data['url'].';port='.$data['port'].';dbname='.$dbname, $data['user'], $data['password'], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$data['charset'],
            PDO::ATTR_DEFAULT_FETCH_MODE => $data['fetchMode'],
            PDO::ATTR_ERRMODE => $data['errorMode']
        ));
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    private function fillDefaultData(array $data = []): array
    {
        $data['url']       = $data['url']       ?? '127.0.0.1';
        $data['port']      = $data['port']      ?? 3306;
        $data['user']      = $data['user']      ?? 'root';
        $data['password']  = $data['password']  ?? '';
        $data['charset']   = $data['charset']   ?? 'utf8mb4';
        $data['fetchMode'] = $data['fetchMode'] ?? PDO::FETCH_ASSOC;
        $data['errorMode'] = $data['errorMode'] ?? PDO::ERRMODE_WARNING;
        return $data;
    }

    public function getLastInsertId(string $name = null): string
    {
        return $this->pdo->lastInsertId($name);
    }

    public function query(string $table): QueryExecutor
    {
        return new QueryExecutor($this, new QueryBuilder($table));
    }

    public function withQuery(QueryBuilder $queryBuilder, string $alias): QueryExecutor
    {
        return new QueryExecutor($this, QueryBuilder::create($queryBuilder, $alias));
    }

    /**
     * @param string $query
     * @param array $parameters
     * @return array|bool|Object|null
     */
    public function fetch(string $query, array $parameters = [])
    {
        return $this->execute($query, $parameters)->fetch();
    }

    /**
     * @param string $query
     * @param array $parameters
     * @return array[]|Object[]
     */
    public function fetchAll(string $query, array $parameters = []): array
    {
        return $this->execute($query, $parameters)->fetchAll();
    }

    public function fetchColumn(string $query, array $parameters = [])
    {
        return $this->execute($query, $parameters)->fetchColumn();
    }

    public function fetchObject(string $query, array $parameters = [])
    {
        return $this->execute($query, $parameters)->fetchObject();
    }

    public function execute(string $query, array $parameters = []): ?PDOStatement
    {
        $query = $this->pdo->prepare($query);
        if(!$query){
            return null;
        }
        $success = $query->execute($parameters);
        return $success ? $query : null;
    }

    public function getErrors(): array
    {
        return $this->pdo->errorInfo();
    }

    public function getErrorCode()
    {
        return $this->pdo->errorCode();
    }
}
