<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints;

class PositionValidator extends AbstractBaseValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $constraints = new Constraints\Collection([
            'id' => [
                new Constraints\Type('integer'),
            ],
            'inCalendar' => [
                new Constraints\Type('bool'),
            ],
            'title' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string'),
            ],
            'roles' => new Constraints\All([
                'constraints' => new Constraints\Collection([
                    'id' => [
                        new Constraints\NotBlank(),
                        new Constraints\Type('integer'),
                    ],
                    'title' => [
                        new Constraints\NotBlank(),
                        new Constraints\Type('string'),
                    ],
                    'description' => [
                        new Constraints\NotBlank(),
                        new Constraints\Type('string'),
                    ],
                ], null, null, false)
            ]),
            'operations' => [
                new Constraints\Type('array'),
            ],
        ], null, null, true, false);

        return $this->validate($data, $constraints);
    }
}