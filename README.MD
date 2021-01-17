#Database Library For PHP

#Installation

```
composer require neutronstars/database-sql
```
```json
{
  "require": {
    "neutronstars/database-sql": ">=1.0.0"
  }
}
```
g
#Importation

```php
<?php
  require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
  use NeutronStars\Database\Database;
```

#Connection

```php
$options = [
  'url'      => '127.0.0.1',
  'port'     => 3306,
  'user'     => 'root',
  'password' => '',
  'charset'  => 'utf8mb4'
];
$database = new Database('database_name', $options);
```

SQL Request Samples
---

##SELECT

```sql
SELECT * FROM table;
```
```php
$results = $database->query('table')
                    ->select('*')
                    ->getResults();
```

---

```sql
SELECT name,email FROM table where id=4;
```
```php
$result = $database->query('table')
                   ->select('name', 'email')
                   ->where('id=:id')
                   ->setParameters([
                      ':id' => 4
                   ])
                   ->getResult();
```

---

```sql
SELECT * FROM table LIMIT 10 OFFSET 20;
SELECT * FROM table LIMIT 20,10;
```
```php
$results = $database->query('table')
                   ->select('*')
                   ->limit(10, 20)
                   ->getResults();
```

---

```sql
SELECT * FROM table ORDER BY points DESC;
```
```php
use NeutronStars\Database\Query;

$results = $database->query('table')
                   ->select('*')
                   ->orderBy('points', Query::ORDER_BY_DESC)
                   ->getResults();
```

---

```sql
SELECT * FROM table GROUP BY name;
```
```php
use NeutronStars\Database\Query;

$results = $database->query('table')
                   ->select('*')
                   ->groupBy('name')
                   ->getResults();
```

---

```sql
SELECT * FROM table1 t1 LEFT JOIN table2 t2 ON t1.id = t2.id;
```
```php
use NeutronStars\Database\Query;

$results = $database->query('table t1')
                   ->select('*')
                   ->leftJoin('table2 t2', 't1.id = t2.id')
                   ->getResults();
```

---

##DELETE
```sql
DELETE FROM table WHERE id=4;
```
```php
$database->query('table')
         ->delete()
         ->where('id=:id')
         ->setParameters([
            ':id' => 4
         ])
         ->execute();
```

---

##UPDATE
```sql
UPDATE table SET name='Doe' WHERE lastname='John';
```
```php
$database->query('table')
         ->update('name=:name')
         ->where('lastname=:lastname')
         ->setParameters([
             ':name'     => 'Doe',
             ':lastname' => 'John'
         ])
         ->execute();
```

---

##INSERT INTO
```sql
INSERT INTO table (name,lastname) VALUES (:name1,:lastname1), (:name2,:lastname2);
```
```php
$database->query('table')
         ->insertInto('name,lastname', ':name1,:lastname1', ':name2,:lastname2')
         ->setParameters([
            ':name1'     => 'Doe',
            ':lastname1' => 'John',
            ':name2'     => 'Doe',
            ':lastname2' => 'Jane'
         ])
         ->execute();
```

```sql
INSERT INTO table (name,lastname) VALUES (:name,:lastname) ON DUPLICATE KEY UPDATE name=:name, lastname=:lastname;
```
```php
$database->query('table')
         ->insertInto('name,lastname', ':name,:lastname')
         ->onDuplicateKeyUpdate('name=:name,lastname=:lastname')
         ->setParameters([
            ':name'     => 'Doe',
            ':lastname' => 'John'
         ])
         ->execute();
```

---

##SUB REQUEST

```sql
SELECT * FROM (SELECT * FROM table) AS test;
```
```php
use NeutronStars\Database\QueryBuilder;

$builder = (new QueryBuilder('table'))->select('*');
$results = $database->withQuery($builder, 'test')
                    ->getResults();
```

```sql
SELECT * FROM users u LEFT JOIN (SELECT * from test) AS t ON t.id=u.id WHERE u.points>5;
```
```php
use NeutronStars\Database\QueryBuilder;

$builder = (new QueryBuilder('test'))->select('*');
$results = $database->query('users u')
                    ->select('*')
                    ->leftJoinQuery($builder, 't', 't.id=u.id')
                    ->where('u.points>:points')
                    ->setParameters([
                       ':points'     => 5
                    ])
                    ->getResults();
```
