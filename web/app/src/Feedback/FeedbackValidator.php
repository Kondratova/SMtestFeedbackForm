<?php

declare(strict_types=1);

namespace App\Acme\Feedback;

use App\Acme\Core\Validator;

final class FeedbackValidator extends Validator
{
    /**
     * @return string[]
     */
    public static function requiredFields(): array
    {
        return ['full_name', 'email', 'message'];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function validateForm(array $data): bool
    {
        $this->errors = [];

        foreach (self::requiredFields() as $field) {
            $value = is_string($data[$field] ?? null) ? trim($data[$field]) : '';

            if ($value === '') {
                $this->errors[$field] = 'Поле обязательно для заполнения.';
            }
        }

        // Формат email проверяем только если поле не пустое
        $email = trim((string) ($data['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Введите корректный адрес электронной почты.';
        }

        return $this->errors === [];
    }

    public function validateEmail(string $email): void
    {
        $this->errors = [];

        if ($email === '') {
            $this->errors['email'] = 'Поле обязательно для заполнения.';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'Введите корректный адрес электронной почты.';
        }
    }
}