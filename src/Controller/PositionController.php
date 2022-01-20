<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Position;
use App\Entity\PositionRole;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Repository\PositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PositionController extends AbstractController
{
    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/position/list', name: 'apiPositionList')]
    public function list(PositionRepository $positionRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $positions = $positionRepository->findBy(['company' => $user->getCompany()]);
        $total = count($positions);

        return $this->json([
            'success' => true,
            'total' => $total,
            'items' => $positions,
        ]);
    }

    #[IsGranted('ROLE_'.Role::EMPLOYEE_DELETE)]
    #[Route('/api/position/delete/{id<\d+>}', name: 'apiPositionDelete', methods: 'DELETE')]
    public function delete(Position $position, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        if ($user->getPosition()->getId() === $position->getId()) {
            return $this->json((new ArrayException('Вы не можете удалить свою роль', 202))->toArray());
        }

        $em->remove($position->getPositionRole());
        $em->remove($position);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }

    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/position/get/{id<\d+>}', name: 'apiPositionGet', methods: 'GET')]
    public function getPosition(Position $position): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        return $this->json([
            'success' => true,
            'item' => $position,
        ]);
    }

    #[IsGranted('ROLE_'.Role::EMPLOYEE_POST)]
    #[Route('/api/position/post', name: 'apiPositionPost')]
    public function post(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('Пользователь не найден', 202))->toArray());
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $position = new Position();
        $position->setCompany($user->getCompany());
        $position->setInCalendar($data['inCalendar']);
        $position->setTitle($data['title']);
        $em->persist($position);
        $positionRole = new PositionRole();
        $positionRole->setPosition($position);
        foreach ($data['roles'] as $role) {
            $positionRole->addRole($role['id']);
        }
        $em->persist($positionRole);
        $em->flush();

        return $this->json([
            'success' => true,
        ]);
    }
}
