<?php

namespace NeutronStars\DatabaseTest;

use NeutronStars\Database\Database;
use NeutronStars\Database\Query;
use NeutronStars\Database\QueryBuilder;
use NeutronStars\Database\QueryExecutor;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    public function testResults(): void
    {
        $database = $this->createMock(Database::class);
        $database->expects(self::once())->method('fetchAll')->willReturn([]);

        $this->assertSame([], (new QueryExecutor($database, new QueryBuilder('users')))->select('*')->getResults());
    }

    public function testResult(): void
    {
        $database = $this->createMock(Database::class);
        $database->expects(self::once())->method('fetch')->willReturn([]);

        $this->assertSame(
            [],
            (new QueryExecutor($database, new QueryBuilder('users')))
                ->select('*')
                ->where('id=:id')
                ->setParameters([
                    ':id' => 1
                ])
                ->getResult()
        );
    }

    public function testExecute(): void
    {
        $pdoStatement = $this->createMock(PDOStatement::class);
        $database = $this->createMock(Database::class);
        $database->expects(self::once())->method('execute')->willReturn($pdoStatement);
        $this->assertSame(
            true,
            (new QueryExecutor($database, new QueryBuilder('users')))
                ->delete()
                ->where('id=:id')
                ->setParameters([
                    ':id' => 1
                ])
                ->execute()
        );
    }

    public function testSelect(): void
    {
        $this->assertSame("SELECT * FROM users", (new QueryBuilder('users'))->select('*')->build());
    }

    public function testWhere(): void
    {
        $this->assertSame(
            "SELECT * FROM users WHERE id=:id",
            (new QueryBuilder('users'))
                ->select('*')
                ->where('id=:id')
                ->build()
        );
    }

    public function testLimit(): void
    {
        $this->assertSame(
            "SELECT * FROM users LIMIT 10,15",
            (new QueryBuilder('users'))
                ->select('*')
                ->limit(15, 10)
                ->build()
        );
    }

    public function testOrder(): void
    {
        $this->assertSame(
            "SELECT * FROM users ORDER BY id DESC",
            (new QueryBuilder('users'))
                ->select('*')
                ->orderBy('id', Query::ORDER_BY_DESC)
                ->build()
        );
    }

    public function testGroup(): void
    {
        $this->assertSame(
            "SELECT * FROM users GROUP BY id",
            (new QueryBuilder('users'))
                ->select('*')
                ->groupBy('id')
                ->build()
        );
    }

    public function testWhereOrder(): void
    {
        $this->assertSame(
            "SELECT * FROM users WHERE id=:id ORDER BY id ASC",
            (new QueryBuilder('users'))
                ->select('*')
                ->orderBy('id')
                ->where('id=:id')
                ->build()
        );
    }

    public function testWhereHavingOrder(): void
    {
        $this->assertSame(
            "SELECT * FROM users WHERE id=:id HAVING id=5 ORDER BY id ASC",
            (new QueryBuilder('users'))
                ->select('*')
                ->orderBy('id')
                ->where('id=:id')
                ->having('id=5')
                ->build()
        );
    }

    public function testLeftJoinWhere(): void
    {
        $this->assertSame(
            "SELECT * FROM users u LEFT JOIN profile p ON p.id=u.id WHERE u.id=:id",
            (new QueryBuilder('users u'))
                ->select('*')
                ->leftJoin('profile p', 'p.id=u.id')
                ->where('u.id=:id')
                ->build()
        );
    }

    public function testLeftMultiJoinWhere(): void
    {
        $this->assertSame(
            "SELECT * FROM users u LEFT JOIN profile p ON p.id=u.id LEFT JOIN roles r ON r.user=u.id WHERE u.id=:id",
            (new QueryBuilder('users u'))
                ->select('*')
                ->leftJoin('profile p', 'p.id=u.id')
                ->leftJoin('roles r', 'r.user=u.id')
                ->where('u.id=:id')
                ->build()
        );
    }

    public function testSubQueryFrom(): void
    {
        $subQuery = (new QueryBuilder('users'))->select('*');
        $this->assertSame(
            "SELECT * FROM (SELECT * FROM users) AS test",
            QueryBuilder::create($subQuery, 'test')
                ->select('*')
                ->build()
        );
    }

    public function testSubQueryJoin(): void
    {
        $subQuery = (new QueryBuilder('profiles'))->select('*');
        $this->assertSame(
            "SELECT * FROM users LEFT JOIN (SELECT * FROM profiles) AS test ON test.id=users.id",
            (new QueryBuilder('users'))
                ->select('*')
                ->leftJoinQuery($subQuery, 'test', 'test.id=users.id')
                ->build()
        );
    }

    public function testDelete(): void
    {
        $this->assertSame(
            "DELETE FROM users WHERE id=:id",
            (new QueryBuilder('users'))
                ->delete()
                ->where('id=:id')
                ->build()
        );
    }

    public function testUpdate(): void
    {
        $this->assertSame(
            "UPDATE users SET name=:name,email=:email WHERE id=:id",
            (new QueryBuilder('users'))
                ->update('name=:name,email=:email')
                ->where('id=:id')
                ->build()
        );
    }

    public function testInsertInto(): void
    {
        $this->assertSame(
            "INSERT INTO users (name, email) VALUES (:n1,:e1),(:n2,:e2),(:n3,:e3)",
            (new QueryBuilder('users'))
                ->insertInto('name, email', ':n1,:e1', ':n2,:e2', ':n3,:e3')
                ->build()
        );
    }

    public function testInsertIntoOnDuplicateKey(): void
    {
        $this->assertSame(
            "INSERT INTO users (name, email) VALUES (:n1,:e1) ON DUPLICATE KEY UPDATE name=:n1,email=:e1",
            (new QueryBuilder('users'))
                ->insertInto('name, email', ':n1,:e1')
                ->onDuplicateKeyUpdate('name=:n1,email=:e1')
                ->build()
        );
    }
}
