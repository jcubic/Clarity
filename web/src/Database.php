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

    public function findUserByEmail(string $email): ?int {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int) $id : null;
    }

    public function isUsernameTaken(string $username, ?string $exceptEmail = null): bool {
        if (!$this->pdo) {
            return false;
        }
        if ($exceptEmail) {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? AND email != ?');
            $stmt->execute([$username, $exceptEmail]);
        } else {
            $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
            $stmt->execute([$username]);
        }
        return (int) $stmt->fetchColumn() > 0;
    }

    public function createOrUpdateUser(string $email, string $username): int {
        if (!$this->pdo) {
            return 0;
        }
        $existing = $this->findUserByEmail($email);
        if ($existing !== null) {
            $stmt = $this->pdo->prepare('UPDATE users SET username = ? WHERE id = ?');
            $stmt->execute([$username, $existing]);
            return $existing;
        }
        $stmt = $this->pdo->prepare('INSERT INTO users (email, username) VALUES (?, ?)');
        $stmt->execute([$email, $username]);
        return (int) $this->pdo->lastInsertId();
    }

    public function createTheme(string $name, string $svgContent): int {
        if (!$this->pdo) {
            return 0;
        }
        $stmt = $this->pdo->prepare(
            'INSERT INTO themes (name, svg_content, status) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $svgContent, 'pending']);
        return (int) $this->pdo->lastInsertId();
    }

    public function publishTheme(int $themeId, int $userId): void {
        if (!$this->pdo) {
            return;
        }
        $stmt = $this->pdo->prepare('UPDATE themes SET user_id = ?, status = ? WHERE id = ?');
        $stmt->execute([$userId, 'published', $themeId]);
    }

    public function getThemeName(int $themeId): ?string {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare('SELECT name FROM themes WHERE id = ?');
        $stmt->execute([$themeId]);
        $name = $stmt->fetchColumn();
        return $name !== false ? (string) $name : null;
    }

    public function isThemeNameTaken(string $name): bool {
        if (!$this->pdo) {
            return false;
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM themes WHERE name = ? AND status = 'published'");
        $stmt->execute([$name]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function createMagicToken(string $token, string $email, string $username, int $themeId): void {
        if (!$this->pdo) {
            return;
        }
        $stmt = $this->pdo->prepare(
            'INSERT INTO magic_tokens (token, email, username, theme_id, expires_at) VALUES (?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE))'
        );
        $stmt->execute([$token, $email, $username, $themeId]);
    }

    /**
     * @return array{email: string, username: string, theme_id: int}|null
     */
    public function verifyMagicToken(string $token): ?array {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare(
            'SELECT email, username, theme_id FROM magic_tokens WHERE token = ? AND expires_at > NOW()'
        );
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $stmt = $this->pdo->prepare('DELETE FROM magic_tokens WHERE token = ?');
        $stmt->execute([$token]);
        return [
            'email' => $row['email'],
            'username' => $row['username'],
            'theme_id' => (int) $row['theme_id'],
        ];
    }

    /**
     * @return array<int, array{id: int, name: string, username: string|null, email: string|null, status: string, created_at: string}>
     */
    public function getAllThemes(): array {
        if (!$this->pdo) {
            return [];
        }
        return $this->pdo->query(
            'SELECT t.id, t.name, u.username, u.email, t.status, t.created_at
             FROM themes t LEFT JOIN users u ON t.user_id = u.id
             ORDER BY t.created_at DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    private function migrate(): void {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS counters (
            name VARCHAR(64) PRIMARY KEY,
            value BIGINT UNSIGNED NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $this->pdo->exec("INSERT IGNORE INTO counters (name, value) VALUES ('installs', 0)");

        $this->pdo->exec('CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            username VARCHAR(32) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $this->pdo->exec('CREATE TABLE IF NOT EXISTS themes (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED,
            name VARCHAR(32) NOT NULL,
            svg_content MEDIUMTEXT NOT NULL,
            status ENUM(\'pending\', \'published\') DEFAULT \'pending\',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $this->pdo->exec('CREATE TABLE IF NOT EXISTS magic_tokens (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            token VARCHAR(64) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL,
            username VARCHAR(32) NOT NULL,
            theme_id INT UNSIGNED NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    }
}
