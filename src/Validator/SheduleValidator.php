<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints;

class SheduleValidator extends AbstractBaseValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $constraints = new Constraints\Collection([
            'id' => [
                new Constraints\Callback(function (?int $object) {
                    return $object ?? null;
                }),
                new Constraints\Type('integer'),
            ],
            'resourceId' => new Constraints\Type('integer'),
            'start' => new Constraints\NotBlank(),
            'end' => new Constraints\NotBlank(),
            'client' => new Constraints\Collection([
                'id' => new Constraints\Type('integer'),
                'name' => new Constraints\Type('string'),
                'phone' => new Constraints\Type('string'),
            ], null, null, true),
            'operations' => new Constraints\All(
                new Constraints\Collection([
                    'id' => new Constraints\Type('integer'),
                    'title' => new Constraints\Type('string'),
                    'price' => new Constraints\Type('integer'),
                    'duration' => new Constraints\Type('integer'),
                    'count' => new Constraints\Type('integer'),
                ], null, null, true, false),
            ),
        ], null, null, true, false);

        return $this->validate($data, $constraints);
    }
}