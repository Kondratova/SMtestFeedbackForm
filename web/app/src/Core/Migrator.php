<?php

namespace App\Acme\Core;

use PDO;

final class Migrator
{
    /**
     * Создаёт все необходимые таблицы, если их ещё нет.
     *
     * @throws \PDOException При ошибке выполнения SQL.
     */
    public static function run(PDO $db): void
    {
        $db->exec(self::createFeedbackTableSql());
    }

    private static function createFeedbackTableSql(): string
    {
        return <<<SQL
            CREATE TABLE IF NOT EXISTS feedback (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(255) NOT NULL,
                email VARCHAR(254) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB
              DEFAULT CHARSET=utf8mb4
              COLLATE=utf8mb4_unicode_ci
        SQL;
    }
}
