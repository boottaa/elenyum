<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class EmailController extends AbstractController
{
    private const _NOREPLAY = 'noreply@elenyum.ru';

    public function __construct(
        private MailerInterface $mailer,
        private EmailService $emailService,
    ) {
    }

    /**
     * https://elenyum.ru/api/email/test
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    #[Route('/api/email/test', name: 'aaaa')]
    public function index(): Response
    {

        try {
            $email = (new Email())
                ->from(self::_NOREPLAY)
                ->to('bootta@yandex.ru')
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Time for Symfony Mailer!')
                ->text('Sending emails is fun again!')
                ->html('<p>See Twig integration for better HTML integration!</p>');

            $this->mailer->send($email);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return $this->json([
            'success' => true,
        ]);
    }

    /**
     * @throws VerifyEmailExceptionInterface
     */
    #[Route('/api/email/verify', name: 'apiEmailVerify')]
    public function emailVerify(Request $request, EmailService $emailService): Response
    {
        try {
            $emailService->handleEmailConfirmation($request);
            return $this->redirectToRoute('login');
        } catch (\Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
