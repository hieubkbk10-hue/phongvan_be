<?php

namespace App\Ship\Exceptions;

use App\Ship\Parents\Exceptions\Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ValidationFailedException extends Exception
{
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
    protected $message = 'Invalid Input.';

    public function __construct(?string $message = null, ?int $code = Response::HTTP_UNPROCESSABLE_ENTITY, ?Throwable $previous = null)
    {
        parent::__construct($message ?? $this->message, $code ?? $this->code, $previous);
    }
}
