<?php

namespace App\Exception;

use Exception;

use JsonSerializable;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ArrayException extends Exception implements JsonSerializable
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        $e = FlattenException::create($this);

        return [
            'success' => false,
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
//            'file' => $e->getFile(),
//            'line' => $e->getLine(),
//            'trace' => $e->getTrace(),
        ];
    }

    public function jsonSerialize(): array
    {
        $e = FlattenException::create($this);

        return [
            'success' => false,
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
        ];
    }
}