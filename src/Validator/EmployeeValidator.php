<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints;

class EmployeeValidator extends AbstractBaseValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $constraints = new Constraints\Collection([
            'id' => new Constraints\Type('integer'),
            'position' => new Constraints\Collection([
                'id' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('integer')
                ],
                'title' => [
                    new Constraints\NotBlank(),
                    new Constraints\Type('string'),
                ]
            ], null, null, false),
            'name' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
            'phone' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
            'additionalPhone' => [
                new Constraints\Type('string')
            ],
            'email' => [
                new Constraints\NotBlank(),
                new Constraints\Email()
            ],
            'dateBrith' => new Constraints\Type('string'),
        ], null, null, false, false);

        return $this->validate($data, $constraints);
    }
}