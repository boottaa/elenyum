<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints;

class OperationValidator extends AbstractBaseValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $constraints = new Constraints\Collection([
            'id' => new Constraints\Type('integer'),
            'price' => [
                new Constraints\Type('integer'),
                new Constraints\NotBlank(),
            ],
            'duration' => [
                new Constraints\Type('integer'),
                new Constraints\NotBlank(),
            ],
            'title' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ]
        ], null, null, false, false);

        return $this->validate($data, $constraints);
    }
}