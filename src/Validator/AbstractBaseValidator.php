<?php

namespace App\Validator;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractBaseValidator
{
    public function __construct(
        protected ValidatorInterface $validator,
        private array $errors = []
    ) {
    }

    abstract public function isValid(array $data): bool;

    /**
     * @param array $data
     * @param mixed $constraints
     * @return bool
     */
    protected function validate(array $data, mixed $constraints): bool
    {
        $valid = $this->validator->validate($data, $constraints);

        if ($valid->count() > 0) {
            foreach ($valid as $item) {
                if ($item instanceof ConstraintViolation) {
                    $this->errors[] = $item;
                }
            }
        }

        return $valid->count() === 0;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}