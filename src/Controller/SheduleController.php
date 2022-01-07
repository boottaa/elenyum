<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Operation;
use App\Entity\Role;
use App\Entity\Shedule;
use App\Entity\SheduleOperation;
use App\Repository\SheduleRepository;
use App\Validator\SheduleValidator;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SheduleController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[IsGranted('ROLE_' . Role::SHEDULE_GET)]
    #[Route('/api/shedule/list', name: 'sheduleList')]
    public function list(Request $request, SheduleRepository $sheduleRepository): Response
    {
        $start = $request->query->getInt('start');
        $end = $request->query->getInt('end');
        if (empty($start) || empty($end)) {
            throw new Exception('Не верно переданы параметры даты начала и окончания');
        }

        $timeStart = DateTimeImmutable::createFromFormat('U', round($start / 1000))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );
        $timeEnd = DateTimeImmutable::createFromFormat('U', round($end / 1000))->setTimezone(
            new DateTimeZone('Europe/Moscow')
        );

        $result = $sheduleRepository->findByRange($timeStart, $timeEnd);

        return $this->json([
            'items' => $result,
            'total' => count($result),
        ]);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param SheduleValidator $validator
     * @param SheduleRepository $sheduleRepository
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \JsonException
     */
    #[IsGranted('ROLE_' . Role::SHEDULE_POST)]
    #[Route('/api/shedule/post', name: 'shedulePost', methods: ['POST'])]
    public function post(
        Request $request,
        EntityManagerInterface $em,
        SheduleValidator $validator,
        SheduleRepository $sheduleRepository
    ): Response {
        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if ($validator->isValid($data)) {
            $shedule = $data['id'] === null ? new Shedule() : $em->find(Shedule::class, $data['id']);
            $employee = $em->find(Employee::class, $data['resourceId']);

            if ($data['client']['id'] === null) {
                $client = new Client();
                $client->setStatus(Client::STATUS_NEW_CLIENT);
                $em->persist($client);
                $client->setPhone($data['client']['phone']);
            } else {
                $client = $em->find(Client::class, $data['client']['id']);
            }
            $client->setName($data['client']['name']);

            $shedule->setClient($client);
            $shedule->setEmployee($employee);
            if ($data['status'] !== false) {
                $shedule->setStatus($data['status']);

                if ($data['paymentType'] !== false) {
                    $shedule->setPaymentType($data['paymentType']);
                    $shedule->setPaymentCard($data['paymentCard']);
                    $shedule->setPaymentCash($data['paymentCash']);
                }
            }

            if ($data['status'] === false || $data['paymentType'] === false) {
                $shedule->setPaymentType(null);
                $shedule->setPaymentCard(null);
                $shedule->setPaymentCash(null);
            }

            $start = DateTimeImmutable::createFromFormat('U', strtotime($data['start']))->setTimezone(
                new DateTimeZone('Europe/Moscow')
            );
            $end = DateTimeImmutable::createFromFormat('U', strtotime($data['end']))->setTimezone(
                new DateTimeZone('Europe/Moscow')
            );
            foreach ($shedule->getSheduleOperations() as $sheduleOperation) {
                $em->remove($sheduleOperation);
            }
            foreach ($data['operations'] as $operation) {
                $sheduleOperation = new SheduleOperation();
                $operationEntity = $em->find(Operation::class, $operation['id']);
                $sheduleOperation->setOperation($operationEntity);
                $sheduleOperation->setCount($operation['count']);
                $sheduleOperation->setShedule($shedule);
                $em->persist($sheduleOperation);
            }

            $shedule->setStart($start);
            $shedule->setEnd($end);
            $em->persist($shedule);
            $em->flush();

            return $this->json([
                'success' => true,
                'item' => $sheduleRepository->findById($shedule->getId()),
            ]);
        }

        return $this->json([
            'success' => false,
            'errors' => $validator->getErrors(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[IsGranted('ROLE_' . Role::SHEDULE_DELETE)]
    #[Route('/api/shedule/remove/{id<\d+>}', name: 'sheduleRemove', methods: 'GET')]
    public function remove(Shedule $shedule, EntityManagerInterface $em): Response
    {
        foreach ($shedule->getSheduleOperations() as $sheduleOperation) {
            $em->remove($sheduleOperation);
        }

        $em->remove($shedule);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}
