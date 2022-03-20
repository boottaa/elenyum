<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\Signature;
use App\Repository\EmployeeRepository;
use App\Repository\SignatureRepository;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use SymfonyCasts\Bundle\VerifyEmail\Exception\ExpiredSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\InvalidSignatureException;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailSignatureComponents;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailService extends BaseAbstractService
{
    public function __construct(
        private EmployeeRepository $employeeRepository,
        private SignatureRepository $signatureRepository,
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $em,
    ) {
    }

    private const _NOREPLAY = 'noreply@elenyum.ru';

    /**
     * @param string $email
     * @return Employee|null
     */
    private function getUserByEmail(string $email): ?Employee
    {
        $employee = $this->employeeRepository->findOneBy(['email' => $email]);

        if ($employee instanceof Employee) {
            return $employee;
        }

        return null;
    }

    public function saveSignature(
        Employee $employee,
        string $routeName,
        VerifyEmailSignatureComponents $signatureComponents
    ): bool {
        $signature = new Signature();
        $signature->setEmployee($employee);
        $signature->setRouteName($routeName);

        //Дата до которого годна ссылка
        $date = new DateTimeImmutable();
        $date = $date->add(DateInterval::createFromDateString('1 day'))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );

        $signature->setExpiresAt($date);
        $url = $signatureComponents->getSignedUrl();
        $parseUrl = parse_url($url);
        parse_str($parseUrl['query'], $params);
        $signature->setToken($params['token']);
        $signature->setHash($params['signature']);

        $this->em->persist($signature);
        $this->em->flush();

        return true;
    }

    /**
     * @param Employee $employee
     * @return void
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function confirmationEmail(Employee $employee): void
    {
        $routeName = 'apiEmailVerify';
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $routeName,
            $employee->getId(),
            $employee->getEmail()
        );

        $template = (new TemplatedEmail())
            ->from(new Address(self::_NOREPLAY, 'Elenyum'))
            ->to($employee->getEmail())
            ->subject('Пожалуйста подтвердите ваш email')
            ->htmlTemplate('email/confirmation.html.twig');

        $context = $template->getContext();

        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $template->context($context);

        if ($this->saveSignature($employee, $routeName, $signatureComponents)) {
            $this->mailer->send($template);
        }
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    public function handleEmailConfirmation(Request $request): void
    {
        $signature = $this->signatureRepository->findOneBy(['token' => $request->get('token')]);
        if (
            $signature instanceof Signature &&
            $signature->getExpiresAt()->getTimestamp() > (new DateTimeImmutable())->getTimestamp()
        ) {
            $employee = $signature->getEmployee();
            if ($request->get('signature') === $signature->getHash()) {
                $employee->setStatus(Employee::STATUS_CONFIRMED);
                $this->em->remove($signature);
                $this->em->flush();
            } else {
                throw new InvalidSignatureException();
            }
        } else {
            throw new ExpiredSignatureException();
        }
    }
}