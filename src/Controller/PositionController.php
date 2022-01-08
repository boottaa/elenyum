<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Position;
use App\Entity\PositionRole;
use App\Entity\Role;
use App\Exception\ArrayException;
use App\Repository\PositionRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PositionController extends AbstractController
{
    #[IsGranted('ROLE_' . Role::EMPLOYEE_POST)]
    #[Route('/api/position/list', name: 'positionList')]
    public function index(PositionRepository $positionRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('User undefined', 202))->toArray());
        }

        $positions = $positionRepository->findBy(['company' => $user->getCompany()]);
        $total = count($positions);
        return $this->json([
            'total' => $total,
            'items' => $positions,
        ]);
    }

    #[IsGranted('ROLE_' . Role::EMPLOYEE_POST)]
    #[Route('/api/position/post', name: 'positionPost')]
    public function post(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Employee) {
            return $this->json((new ArrayException('User undefined', 202))->toArray());
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $position = new Position();
        $position->setCompany($user->getCompany());
        $position->setInCalendar($data['inCalendar']);
        $position->setTitle($data['title']);
        $em->persist($position);
        $positionRole = new PositionRole();
        $positionRole->setPosition($position);
        foreach ($data['roles'] as $roleId) {
            $positionRole->addRole($roleId);
        }

        return $this->json([
            'success' => true,
        ]);
    }
}
