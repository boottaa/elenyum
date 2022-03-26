<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Signature;
use App\Exception\ArrayException;
use App\Repository\EmployeeRepository;
use App\Repository\SignatureRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;

class SecurityService extends BaseAbstractService
{
    public function __construct(
        private SignatureRepository $signatureRepository,
        private EmployeeRepository $employeeRepository,
        private EmailService $emailService,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $em
    ) {
    }

    /**
     * @param string $email
     * @return Employee|null
     */
    public function getUserByEmail(string $email): ?Employee
    {
        $employee = $this->employeeRepository->findOneBy(['email' => $email]);

        if ($employee instanceof Employee) {
            return $employee;
        }

        return null;
    }

    /**
     * @throws ArrayException
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function forgotPassword(string $email): Employee
    {
        $employee = $this->getUserByEmail($email);

        if (!$employee instanceof Employee) {
            throw new ArrayException('Not defined '.Employee::class, '422');
        }

        $this->emailService->forgotPasswordEmail($employee);

        return $employee;
    }

    /**
     * @param string $password
     * @param string $token
     * @param string $sign
     * @return void
     * @throws ExpiredSignatureException
     */
    public function recoveryPassword(string $password, string $token, string $sign): void
    {
        $signature = $this->signatureRepository->findOneBy(['token' => $token]);
        if (
            $signature instanceof Signature &&
            $sign === $signature->getHash() &&
            $signature->getExpiresAt()->getTimestamp() > (new DateTimeImmutable())->getTimestamp()
        ) {
            $employee = $signature->getEmployee();
            $this->changePassword($password, $employee);
            $this->em->remove($signature);
        } else {
            throw new ExpiredSignatureException();
        }
    }

    /**
     * @param string $password
     * @param Employee $employee
     * @return Employee
     */
    public function changePassword(string $password, Employee $employee): Employee
    {
        $employee->setPassword(
            $this->userPasswordHasher->hashPassword(
                $employee,
                $password
            )
        );
        $this->em->flush();

        return $employee;
    }
}