<?php

declare(strict_types=1);

namespace App\Acme\Core;

use DateTimeImmutable;
use Throwable;

final class Logger
{
    private static ?self $instance = null;

    private string $logFile;

    private function __construct(string $logFile)
    {
        $this->logFile = $logFile;
    }

    public static function getInstance(?string $logFile = null): self
    {
        if (self::$instance === null) {
            $logFile ??= dirname(__DIR__, 2) . '/var/log/app.log';
            self::$instance = new self($logFile);
        }

        return self::$instance;
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function exception(Throwable $e): void
    {
        $this->write('ERROR', get_class($e) . ': ' . $e->getMessage(), [
            'file'  => $e->getFile(),
            'line'  => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
    }

    private function write(string $level, string $message, array $context): void
    {
        $dir = dirname($this->logFile);
        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            error_log('Logger: cannot create log directory ' . $dir);
            return;
        }

        $timestamp = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $line = sprintf('[%s] %s: %s', $timestamp, $level, $message);

        if ($context !== []) {
            $line .= ' ' . json_encode(
                    $context,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                );
        }

        $line .= PHP_EOL;

        // @ — чтобы не падать, если директория только для чтения;
        // fallback на системный error_log.
        if (@file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX) === false) {
            error_log($line);
        }
    }
}