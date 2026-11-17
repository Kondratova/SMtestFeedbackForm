<?php

declare(strict_types=1);

namespace App\Acme\Core;

use PDO;
use PDOException;
use App\Acme\Exeptions\MissingDatabaseCredentialsException;

final class Database
{
    private static ?PDO $instance = null;

    private function __construct()
    {
    }

    /**
     * @throws MissingDatabaseCredentialsException При ошибке не заполненных данных подключения бд.
     * @throws PDOException При ошибке конфигурации или установки соединения.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection(Config::fromEnv());
        }

        return self::$instance;
    }

    /**
     * Фабричный метод для создания PDO-соединения по объекту Config.
     *
     * @throws MissingDatabaseCredentialsException При ошибке не заполненных данных подключения бд.
     * @throws PDOException При ошибке подключения.
     */
    public static function createConnection(Config $config): PDO
    {
        try {
            return new PDO(
                $config->getDsn(),
                $config->getUsername(),
                $config->getPassword(),
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ],
            );
        } catch (MissingDatabaseCredentialsException $e) {
            throw new PDOException(
                'Database connection failed: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e,
            );
        } catch (PDOException $e) {
            throw new PDOException(
                'Database connection failed: ' . $e->getMessage(),
                (int) $e->getCode(),
                $e,
            );
        }
    }
}