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

    public function getUsernameByEmail(string $email): ?string {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare('SELECT username FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $username = $stmt->fetchColumn();
        return $username !== false ? (string) $username : null;
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

    public function createOrGetUser(string $email, string $username): int {
        if (!$this->pdo) {
            return 0;
        }
        $existing = $this->findUserByEmail($email);
        if ($existing !== null) {
            return $existing;
        }
        $stmt = $this->pdo->prepare('INSERT INTO users (email, username) VALUES (?, ?)');
        $stmt->execute([$email, $username]);
        return (int) $this->pdo->lastInsertId();
    }

    public function createTheme(string $name, string $description, string $version, string $svgContent): int {
        if (!$this->pdo) {
            return 0;
        }
        $stmt = $this->pdo->prepare(
            'INSERT INTO themes (name, description, version, svg_content, status) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $description, $version, $svgContent, 'pending']);
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

    public function getThemeOwnerEmail(string $name): ?string {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare(
            "SELECT u.email FROM themes t JOIN users u ON t.user_id = u.id WHERE t.name = ? AND t.status = 'published'"
        );
        $stmt->execute([$name]);
        $email = $stmt->fetchColumn();
        return $email !== false ? (string) $email : null;
    }

    /**
     * @return array{svg_content: string, description: string, version: string}|null
     */
    public function getThemeById(int $themeId): ?array {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare('SELECT svg_content, description, version FROM themes WHERE id = ?');
        $stmt->execute([$themeId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function deleteTheme(int $themeId): void {
        if (!$this->pdo) {
            return;
        }
        $stmt = $this->pdo->prepare('DELETE FROM themes WHERE id = ?');
        $stmt->execute([$themeId]);
    }

    public function replacePublishedTheme(string $name, int $userId, string $description, string $version, string $svgContent): void {
        if (!$this->pdo) {
            return;
        }
        $stmt = $this->pdo->prepare(
            "UPDATE themes SET description = ?, version = ?, svg_content = ?, created_at = CURRENT_TIMESTAMP WHERE name = ? AND user_id = ? AND status = 'published'"
        );
        $stmt->execute([$description, $version, $svgContent, $name, $userId]);
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

    public function getThemeSvg(string $username, string $themeName): ?string {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare(
            "SELECT t.svg_content FROM themes t
             JOIN users u ON t.user_id = u.id
             WHERE u.username = ? AND t.name = ? AND t.status = 'published'"
        );
        $stmt->execute([$username, $themeName]);
        $content = $stmt->fetchColumn();
        return $content !== false ? (string) $content : null;
    }

    /**
     * @return array<int, array{id: int, name: string, description: string, version: string, username: string|null, email: string|null, status: string, created_at: string}>
     */
    public function getAllThemes(): array {
        if (!$this->pdo) {
            return [];
        }
        return $this->pdo->query(
            'SELECT t.id, t.name, t.description, t.version, u.username, u.email, t.status, t.created_at
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
            description VARCHAR(200) NOT NULL DEFAULT \'\',
            version VARCHAR(16) NOT NULL DEFAULT \'v1.0\',
            svg_content MEDIUMTEXT NOT NULL,
            status ENUM(\'pending\', \'published\') DEFAULT \'pending\',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

        $columns = $this->pdo->query('SHOW COLUMNS FROM themes')->fetchAll(PDO::FETCH_COLUMN);
        if (!in_array('description', $columns, true)) {
            $this->pdo->exec("ALTER TABLE themes ADD COLUMN description VARCHAR(200) NOT NULL DEFAULT '' AFTER name");
        }
        if (!in_array('version', $columns, true)) {
            $this->pdo->exec("ALTER TABLE themes ADD COLUMN version VARCHAR(16) NOT NULL DEFAULT 'v1.0' AFTER description");
        }

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
