<?php

declare(strict_types=1);

namespace App\Acme\Core;

use TheSeer\Tokenizer\Exception;
use App\Acme\Exeptions\MissingDatabaseCredentialsException;

/**
 * Класс конфигурации подключения к базе данных.
 *
 * Все параметры подключения должны быть явно заданы через переменные
 * окружения.
 */
final class Config
{
    private string $host;
    private string $port;
    private string $database;
    private string $username;
    private string $password;
    private string $charset;

    public function __construct(
        string $host,
        string $port,
        string $database,
        string $username,
        string $password,
        string $charset = 'utf8mb4'
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
    }

    /**
     * Создаёт конфигурацию из переменных окружения.
     *
     * @throws Exception Если хотя бы одна обязательная переменная не задана.
     */
    public static function fromEnv(): self
    {
        $requiredVars = [
            'DB_HOST',
            'DB_PORT',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
        ];

        $values = [];
        $missing = [];

        foreach ($requiredVars as $name) {
            $value = getenv($name);

            if ($value === false || $value === '') {
                $missing[] = $name;
                continue;
            }

            $values[$name] = $value;
        }

        if ($missing !== []) {
            throw new MissingDatabaseCredentialsException(
                'Database connection error: missing required environment variables: '
                . implode(', ', $missing),
            );
        }

        return new self(
            host: $values['DB_HOST'],
            port: $values['DB_PORT'],
            database: $values['DB_DATABASE'],
            username: $values['DB_USERNAME'],
            password: $values['DB_PASSWORD'],
            charset: 'utf8mb4',
        );
    }

    /**
     * Формирует DSN-строку для PDO.
     */
    public function getDsn(): string
    {
        return sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $this->getHost(),
            $this->getPort(),
            $this->getDatabase(),
            $this->getCharset(),
        );
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }
}
