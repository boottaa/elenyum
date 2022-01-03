<?php

namespace App\Exception;

use Exception;

use Symfony\Component\ErrorHandler\Exception\FlattenException;

class ArrayException extends Exception
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
}