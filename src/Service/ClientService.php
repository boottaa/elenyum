<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Employee;
use App\Exception\ArrayException;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;

class ClientService extends BaseAbstractService
{
    public function __construct(
        ClientRepository $repository,
        private EntityManagerInterface $em
    ) {
        $this->repository = $repository;
    }

    /**
     * @param Client $client
     * @param array $data
     * @return void
     */
    private function hydrate(Client $client, array $data): void
    {
        $client->setName($data['name']);
        $client->setDateBrith($data['dateBrith']);
        $client->setStatus($data['status']);
        $client->setEmail($data['email']);
        $client->setPhone($data['phone']);
        $client->setAdditionalPhone($data['additionalPhone']);
    }

    /**
     * @param array $data
     * @return Client
     * @throws ArrayException
     */
    public function put(array $data): Client
    {
        $clientData = $data['client'];
        $client = $this->repository->find($clientData['id']);
        if (!$client instanceof Client) {
            throw new ArrayException('Not defined '.Client::class, '422');
        }
        $this->hydrate($client, $clientData);
        $this->em->flush();

        return $client;
    }

    /**
     * @param array $data
     * @return Client
     * @throws ArrayException
     */
    public function post(array $data): Client
    {
        $user = $data['user'];
        $clientData = $data['client'];

        if (!$user instanceof Employee) {
            throw new ArrayException('Not defined '.Employee::class, '422');
        }

        $client = new Client();
        $client->setCompany($user->getCompany());
        $this->hydrate($client, $clientData);
        $this->em->flush();

        return $client;
    }

    /**
     * @param int $id
     * @return bool
     * @throws ArrayException
     */
    public function del(int $id): bool
    {
        $client = $this->repository->find($id);
        if (!$client instanceof Client) {
            throw new ArrayException('Not defined '.Client::class, '422');
        }

        $this->em->remove($client);
        $this->em->flush();

        return true;
    }
}