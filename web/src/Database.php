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
        return new self($pdo);
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

    public function createTheme(string $name, string $description, string $version, string $svgContent, bool $isDark = true): int {
        if (!$this->pdo) {
            return 0;
        }
        $stmt = $this->pdo->prepare(
            'INSERT INTO themes (name, description, version, svg_content, is_dark, status) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $description, $version, $svgContent, $isDark ? 1 : 0, 'pending']);
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
     * @return array{svg_content: string, description: string, version: string, is_dark: int}|null
     */
    public function getThemeById(int $themeId): ?array {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare('SELECT svg_content, description, version, is_dark FROM themes WHERE id = ?');
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

    public function replacePublishedTheme(string $name, int $userId, string $description, string $version, string $svgContent, bool $isDark = true): void {
        if (!$this->pdo) {
            return;
        }
        $stmt = $this->pdo->prepare(
            "UPDATE themes SET description = ?, version = ?, svg_content = ?, is_dark = ? WHERE name = ? AND user_id = ? AND status = 'published'"
        );
        $stmt->execute([$description, $version, $svgContent, $isDark ? 1 : 0, $name, $userId]);
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
     * @return array{svg_content: string, updated_at: string}|null
     */
    public function getThemeSvgWithMeta(string $username, string $themeName): ?array {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare(
            "SELECT t.svg_content, t.updated_at FROM themes t
             JOIN users u ON t.user_id = u.id
             WHERE u.username = ? AND t.name = ? AND t.status = 'published'"
        );
        $stmt->execute([$username, $themeName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    /**
     * @return array<int, array{name: string, description: string, version: string, is_dark: int, view_count: int, download_count: int, like_count: int, username: string, created_at: string, updated_at: string}>
     */
    public function getPublishedThemes(): array {
        if (!$this->pdo) {
            return [];
        }
        return $this->pdo->query(
            "SELECT t.name, t.description, t.version, t.is_dark, t.view_count, t.download_count,
                    (SELECT COUNT(*) FROM likes l WHERE l.theme_id = t.id) AS like_count,
                    u.username, t.created_at, t.updated_at
             FROM themes t JOIN users u ON t.user_id = u.id
             WHERE t.status = 'published'
             ORDER BY t.updated_at DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array{id: int, name: string, description: string, version: string, is_dark: int, view_count: int, download_count: int, username: string, created_at: string, updated_at: string, user_created_at: string}|null
     */
    public function getThemeDetail(string $username, string $themeName): ?array {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare(
            "SELECT t.id, t.name, t.description, t.version, t.is_dark, t.view_count, t.download_count, t.created_at, t.updated_at,
                    u.username, u.created_at AS user_created_at
             FROM themes t
             JOIN users u ON t.user_id = u.id
             WHERE u.username = ? AND t.name = ? AND t.status = 'published'"
        );
        $stmt->execute([$username, $themeName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row !== false ? $row : null;
    }

    public function getUserThemeCount(string $username): int {
        if (!$this->pdo) {
            return 0;
        }
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM themes t
             JOIN users u ON t.user_id = u.id
             WHERE u.username = ? AND t.status = 'published'"
        );
        $stmt->execute([$username]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * @return array<int, array{id: int, name: string, description: string, version: string, username: string|null, email: string|null, status: string, created_at: string, updated_at: string}>
     */
    public function getAllThemes(): array {
        if (!$this->pdo) {
            return [];
        }
        return $this->pdo->query(
            'SELECT t.id, t.name, t.description, t.version, t.view_count, t.download_count,
                    (SELECT COUNT(*) FROM likes l WHERE l.theme_id = t.id) AS like_count,
                    u.username, u.email, t.status, t.created_at, t.updated_at
             FROM themes t LEFT JOIN users u ON t.user_id = u.id
             ORDER BY t.updated_at DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recordThemeView(int $themeId, string $ipHash): bool {
        if (!$this->pdo) {
            return false;
        }
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO theme_views (theme_id, ip_hash) VALUES (?, ?)');
        $stmt->execute([$themeId, $ipHash]);
        if ($stmt->rowCount() > 0) {
            $stmt = $this->pdo->prepare('UPDATE themes SET view_count = view_count + 1 WHERE id = ?');
            $stmt->execute([$themeId]);
            return true;
        }
        return false;
    }

    public function incrementDownloadCount(int $themeId): void {
        if (!$this->pdo) {
            return;
        }
        $stmt = $this->pdo->prepare('UPDATE themes SET download_count = download_count + 1 WHERE id = ?');
        $stmt->execute([$themeId]);
    }

    public function getThemeIdBySlug(string $username, string $name): ?int {
        if (!$this->pdo) {
            return null;
        }
        $stmt = $this->pdo->prepare(
            "SELECT t.id FROM themes t JOIN users u ON t.user_id = u.id WHERE u.username = ? AND t.name = ? AND t.status = 'published'"
        );
        $stmt->execute([$username, $name]);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int) $id : null;
    }

    public function addLike(int $themeId, string $ipHash): bool {
        if (!$this->pdo) {
            return false;
        }
        $stmt = $this->pdo->prepare('INSERT IGNORE INTO likes (theme_id, ip_hash) VALUES (?, ?)');
        $stmt->execute([$themeId, $ipHash]);
        return $stmt->rowCount() > 0;
    }

    public function getLikeCount(int $themeId): int {
        if (!$this->pdo) {
            return 0;
        }
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM likes WHERE theme_id = ?');
        $stmt->execute([$themeId]);
        return (int) $stmt->fetchColumn();
    }

    public function hasLiked(int $themeId, string $ipHash): bool {
        if (!$this->pdo) {
            return false;
        }
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM likes WHERE theme_id = ? AND ip_hash = ?');
        $stmt->execute([$themeId, $ipHash]);
        return (int) $stmt->fetchColumn() > 0;
    }

}
