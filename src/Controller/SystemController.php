<?php

namespace App\Controller;

use App\Entity\NewClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends AbstractController
{
    #[Route('/api/system/newClient', name: 'apiEmployeeList')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $newClient = new NewClient();
        $newClient->setPhone($content['phone']);

        $em->persist($newClient);
        $em->flush();

        return $this->json([
            'success' => true,
            'item' => $newClient
        ]);
    }
}
