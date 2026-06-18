<?php

namespace App\Exceptions;

use Exception;

class ValidationFailedException extends Exception
{
    public function __construct(
        public readonly array $errors,
        string $message = 'La validation de l\'analyse IA a échoué.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
