<?php

declare(strict_types=1);

namespace App\Acme\Core;

/**
 * Унифицированный ответ API.
 *
 * Формат JSON:
 * {
 *   "success": bool,
 *   "errors": ["...", "..."],
 *   "response": { ... } | [ ... ]
 * }
 */
class Response
{
    private bool $success;
    /** @var string[] */
    private array $errors;
    /** @var array<string, mixed>|array<int, mixed> */
    private array $response;
    private int $statusCode;

    /**
     * @param string[] $errors
     * @param array<string, mixed>|array<int, mixed> $response
     */
    function __construct(
        bool $success,
        array $errors,
        array $response,
        int $statusCode
    ) {
        $this->success = $success;
        $this->errors = $errors;
        $this->response = $response;
        $this->statusCode = $statusCode;
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $response
     */
    public static function success(array $response = [], int $statusCode = 200): self
    {
        return new self(true, [], $response, $statusCode);
    }

    /**
     * @param string[] $errors
     * @param array<string, mixed>|array<int, mixed> $response
     */
    public static function error(array $errors, int $statusCode = 422, array $response = []): self
    {
        return new self(false, $errors, $response, $statusCode);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    /** @return string[] */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /** @return array<string, mixed>|array<int, mixed> */
    public function getResponse(): array
    {
        return $this->response;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            [
                'success'  => $this->success,
                'errors'   => $this->errors,
                'response' => $this->response,
            ],
            JSON_UNESCAPED_UNICODE,
        );
    }
}