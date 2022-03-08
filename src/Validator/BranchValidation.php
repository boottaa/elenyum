<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContext;

class BranchValidation extends AbstractBaseValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $constraints = new Constraints\Collection([
            'id' => new Constraints\Type('integer'),
            'name' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
            'address' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
            'start' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
            'end' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ]
        ], null, null, false, false);

        return $this->validate($data, $constraints);
    }
}