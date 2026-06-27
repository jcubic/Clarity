<?php

namespace Clarity;

use PDO;

class Database {
    private ?PDO $pdo;

    private function __construct(?PDO $pdo) {
        $this->pdo = $pdo;
    }

    public static function connect(
        string $host,
        string $name,
        string $username,
        string $password,
        string $port = '3306'
    ): self {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
        $db = new self($pdo);
        $db->migrate();
        return $db;
    }

    public static function null(): self {
        return new self(null);
    }

    public function isConnected(): bool {
        return $this->pdo !== null;
    }

    public function incrementCounter(string $name): void {
        if (!$this->pdo) {
            return;
        }
        $stmt = $this->pdo->prepare('UPDATE counters SET value = value + 1 WHERE name = ?');
        $stmt->execute([$name]);
    }

    public function getCounter(string $name): int {
        if (!$this->pdo) {
            return 0;
        }
        $stmt = $this->pdo->prepare('SELECT value FROM counters WHERE name = ?');
        $stmt->execute([$name]);
        return (int) ($stmt->fetchColumn() ?: 0);
    }

    /** @return array<string, int> */
    public function getAllCounters(): array {
        if (!$this->pdo) {
            return [];
        }
        return $this->pdo->query('SELECT name, value FROM counters')
            ->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    private function migrate(): void {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS counters (
            name VARCHAR(64) PRIMARY KEY,
            value BIGINT UNSIGNED NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->pdo->exec("INSERT IGNORE INTO counters (name, value) VALUES ('installs', 0)");
    }
}
