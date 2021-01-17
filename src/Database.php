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
     *   'charset': 'utf8mb4'
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
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
        ));
    }

    private function fillDefaultData(array $data = []): array
    {
        if(!isset($data['url'])) { $data['url']   = '127.0.0.1'; }
        if(!isset($data['port'])) { $data['port'] = 3306; }
        if(!isset($data['user'])) { $data['user'] = 'root'; }
        if(!isset($data['password'])) { $data['password'] = ''; }
        if(!isset($data['charset'])) { $data['charset'] = 'utf8mb4'; }
        return $data;
    }

    public function query(string $table): QueryExecutor
    {
        return new QueryExecutor($this, new QueryBuilder($table));
    }

    public function withQuery(QueryBuilder $queryBuilder, string $alias): QueryExecutor
    {
        return new QueryExecutor($this, QueryBuilder::create($queryBuilder, $alias));
    }

    public function fetch(string $query, array $parameters = []): array
    {
        return $this->execute($query, $parameters)->fetch();
    }

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
}
