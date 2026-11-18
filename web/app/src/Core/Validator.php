<?php

namespace App\Acme\Core;

abstract class Validator
{
    /** @var array<string, string> */
    protected array $errors = [];

    /** @return array<string, string> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }
}