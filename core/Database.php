<?php

namespace PHPFramework;

class Database
{
    protected \PDO $connection;
    protected \PDOStatement $stmt;

    public function __construct()
    {
        $dsn = "mysql:host=" . DB_SETTINGS['host'] . ";dbname=" . DB_SETTINGS['database'] . ";charset=" . DB_SETTINGS['charset'];
        try {
            $this->connection = new \PDO($dsn, DB_SETTINGS['username'], DB_SETTINGS['password'], DB_SETTINGS['options']);
        } catch (\PDOException $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] Ошибка подключения к БД: {$e->getMessage()}" . PHP_EOL, 3, ERROR_LOGS);
            abort('Ошибка подключения к БД', 500);
        }
        return $this;
    }

    /**
     * Выполняет запрос
     * @param string $query Строка запроса
     * @param array $params Параметры запроса
     * @return $this
     */
    public function query(string $query, array $params = []): self
    {
        $this->stmt = $this->connection->prepare($query);
        $this->stmt->execute($params);
        return $this;
    }

    public function get(): array
    {
        return $this->stmt->fetchAll();
    }

    public function getAssoc($key = 'id'): array
    {
        $data = [];
        while ($row = $this->stmt->fetch()) {
            $data[$row[$key]] = $row;
        }
        return $data;
    }

    public function getOne(): array
    {
        return $this->stmt->fetch();
    }

    public function getColumn(): mixed
    {
        return $this->stmt->fetchColumn();
    }

    public function findAll(string $table): array
    {
        $this->query("select * from $table");
        return $this->stmt->fetchAll();
    }

    public function findOne(string $table, mixed $value, string $key = 'id'): mixed
    {
        $this->query("select * from $table where $key = ? LIMIT 1", [$value]);
        return $this->stmt->fetch();
    }

    public function findOrFail(string $table, mixed $value, string $key = 'id')
    {
        $res = $this->findOne($table, $value, $key);
        if (!$res) {
            if ($_SERVER['HTTP_ACCEPT'] == 'application/json') {
                response()->json(['status' => 'error', 'answer' => 'Нет данных'], 404);
            }
            abort();
        }
        return $res;
    }

    public function count(string $table): int
    {
        return $this->query("select count(*) from $table")->getColumn();
    }

    public function getInsertId(): false|string
    {
        return $this->connection->lastInsertId();
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }

    public function commit(): bool
    {
        return $this->connection->commit();
    }

    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }
}