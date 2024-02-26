<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CustomerController extends AbstractController
{
    #[Route('/customer/{id}', name: 'get_customers', methods:['get'])]
    public function index(ManagerRegistry $doctrine , int $id): JsonResponse
    {
        $customer = $doctrine
            ->getRepository(Customer::class)
            ->find($id);

        if (!$customer) {

            return $this->json('No customer found for id ' . $id, 404);
        }


        $users = $customer->getUsers();

        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'username' => $user->getName(),
                // Add other properties as needed
            ];
        }

        $data =  [
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'firstname' => $customer->getFirstName(),
            'email' => $customer->getEmail(),
            'users' => $userData
        ];

        return $this->json($data);

        // retourn la liste des utilisateurs d'un client choisi par l'id

    }

    #[Route('/customer/{idCustomer}/user/{idUser}', name: 'get_a_customer')]
    public function getAUserfromCustomerId(ManagerRegistry $doctrine , int $idCustomer, int $idUser): JsonResponse
    {
        $user = $doctrine
            ->getRepository(User::class)
            ->findOneBy([
                'id' => $idUser,
                'customer' => $idCustomer,
            ]);

        $data =  [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'firstname' => $user->getEmail()
        ];

        return $this->json($data);

    }

    #[Route('/customer/{idCustomer}/deleteUser/{idUser}', name: 'delete_a_customer')]
    public function deleteUser(ManagerRegistry $doctrine , int $idCustomer, int $idUser): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)
            ->findOneBy([
                'id' => $idUser,
                'customer' => $idCustomer,
            ]);

        $entityManager->remove($user);

        $entityManager->flush();

        $users = $entityManager->getRepository(Customer::class)
            ->findOneBy([
                'id' => $idUser
            ]);

        return $this->json($users);
    }


    #[Route('/customer/{idCustomer}/create', name: 'add_a_customer', methods:['POST'])]
    public function addUser(ManagerRegistry $doctrine , int $idCustomer, Request $request): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        
        $user = new User();
        $content = $request->toArray();
        //dd($content);
        $user->setName($content['name']);
        $user->setEmail($content['email']);
        $user->setPassword($content['password']);
        

        $customer = $entityManager->getRepository(Customer::class)
        ->findOneBy([
            'id' => $idCustomer
        ]);

        $user->setCustomer($customer);

        $entityManager->persist($user);
        $entityManager->flush();

        // Return a response, you can customize this based on your needs
        return $this->json(['message' => 'User created successfully'], 201);
    }
}
