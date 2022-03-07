<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContext;

class RegistrationValidator extends AbstractBaseValidator
{
    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $constraints = new Constraints\Collection([
            'email' => new Constraints\Email(),
            'phone' => new Constraints\Type('string'),
            'userName' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
            'position' => new Constraints\NotBlank(),
            'companyName' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
            'password' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string'),
                new Constraints\Callback(static function ($value, ExecutionContext $data) {
                    if ($data->getRoot()['repeatPassword'] !== $value) {
                       throw new \Exception('Пароль не соответствует, повторите пароль');
                    }
                })
            ],
            'repeatPassword' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
            'address' => [
                new Constraints\NotBlank(),
                new Constraints\Type('string')
            ],
        ], null, null, false, false);

        return $this->validate($data, $constraints);
    }
}