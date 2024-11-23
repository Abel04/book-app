<?php

namespace App\Infrastructure;

class DatabaseConnection
{
    private static ?self $instance = null;
    private \PDO $connection;

    private function __construct()
    {
        try {
            $dsn = 'sqlite:' . __DIR__ . '/../../database.db';

            $this->connection = new \PDO($dsn);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            Logger::getLogger()->error('Database connection error.', [
                'error' => $e->getMessage()
            ]);
            die('Database connection error: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection(): \PDO
    {
        return $this->connection;
    }
}
