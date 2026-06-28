#!/usr/bin/env python3

import os
import sys

try:
    import pymysql
except ImportError:
    print("pymysql not installed, run: pip install pymysql", file=sys.stderr)
    sys.exit(1)


MIGRATIONS = {
    1: [
        """CREATE TABLE IF NOT EXISTS counters (
            name VARCHAR(64) PRIMARY KEY,
            value BIGINT UNSIGNED NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",
        "INSERT IGNORE INTO counters (name, value) VALUES ('installs', 0)",
        """CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            username VARCHAR(32) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",
        """CREATE TABLE IF NOT EXISTS themes (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT UNSIGNED,
            name VARCHAR(32) NOT NULL,
            svg_content MEDIUMTEXT NOT NULL,
            status ENUM('pending', 'published') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",
        """CREATE TABLE IF NOT EXISTS magic_tokens (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            token VARCHAR(64) NOT NULL UNIQUE,
            email VARCHAR(255) NOT NULL,
            username VARCHAR(32) NOT NULL,
            theme_id INT UNSIGNED NOT NULL,
            expires_at TIMESTAMP NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4""",
    ],
    2: [
        "ALTER TABLE themes ADD COLUMN description VARCHAR(200) NOT NULL DEFAULT '' AFTER name",
        "ALTER TABLE themes ADD COLUMN version VARCHAR(16) NOT NULL DEFAULT 'v1.0' AFTER description",
    ],
    3: [
        "ALTER TABLE themes ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at",
    ],
}


def get_env(key):
    val = os.environ.get(key)
    if not val:
        print(f"Missing environment variable: {key}", file=sys.stderr)
        sys.exit(1)
    return val


def detect_current_version(cursor):
    """Bootstrap for databases that existed before the migrations table."""
    cursor.execute("SHOW TABLES")
    tables = [row[0] for row in cursor.fetchall()]

    if "themes" not in tables:
        return 0

    cursor.execute("SHOW COLUMNS FROM themes")
    columns = [row[0] for row in cursor.fetchall()]

    if "updated_at" in columns:
        return 3
    if "description" in columns:
        return 2
    return 1


def main():
    conn = pymysql.connect(
        host=get_env("DB_HOST"),
        port=int(os.environ.get("DB_PORT", "3306")),
        user=get_env("DB_USERNAME"),
        password=get_env("DB_PASSWORD"),
        database=get_env("DB_NAME"),
        charset="utf8mb4",
        autocommit=True,
    )
    cursor = conn.cursor()

    cursor.execute(
        """CREATE TABLE IF NOT EXISTS migrations (
            version INT UNSIGNED PRIMARY KEY
        ) ENGINE=InnoDB"""
    )

    cursor.execute("SELECT COALESCE(MAX(version), 0) FROM migrations")
    current = cursor.fetchone()[0]

    if current == 0:
        current = detect_current_version(cursor)
        for i in range(1, current + 1):
            cursor.execute("INSERT IGNORE INTO migrations (version) VALUES (%s)", (i,))

    applied = 0
    for version in sorted(MIGRATIONS.keys()):
        if version <= current:
            continue
        print(f"Applying migration {version}...")
        for sql in MIGRATIONS[version]:
            cursor.execute(sql)
        cursor.execute("INSERT INTO migrations (version) VALUES (%s)", (version,))
        applied += 1

    if applied:
        print(f"Applied {applied} migration(s). Now at version {max(MIGRATIONS.keys())}.")
    else:
        print(f"Already at version {current}. Nothing to do.")

    cursor.close()
    conn.close()


if __name__ == "__main__":
    main()
