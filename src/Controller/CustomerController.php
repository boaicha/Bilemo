<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomerController extends AbstractController
{
    #[Route('/api/customer/{id}', name: 'get_customers', methods:['get'])]
    public function index(ManagerRegistry $doctrine , int $id, UserInterface $authenticatedUser, Request $request, PaginatorInterface $paginator): JsonResponse
    {

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 2);


        if ($authenticatedUser->getId() !== $id) {
            $errorMessage = 'You do not have access to this customer.';

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_FORBIDDEN);
        }




        $users = $doctrine
            ->getRepository(User::class)
            ->findUsersByCustomer($id);



        if (!$users) {

            return $this->json('No customer found for id ' . $id, 404);
        }

        $pagination = $paginator->paginate(
            $users, // Sur quoi on pagine
            $page,          // Le numero de la page actuelle
            $limit          // limite des user à afficher
        );


        $userData = [];
        foreach ($pagination->getItems() as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            ];
        }


        return $this->json($userData);


    }

    #[Route('/api/customer/{idCustomer}/user/{idUser}', name: 'get_a_customer')]
    public function getAUserfromCustomerId(ManagerRegistry $doctrine , int $idCustomer, int $idUser, UserInterface $authenticatedUser): JsonResponse
    {

        if ($authenticatedUser->getId() !== $idCustomer) {
            $errorMessage = 'You do not have access to this customer.';

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_FORBIDDEN);
        }


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

    #[Route('/api/customer/{idCustomer}/deleteUser/{idUser}', name: 'delete_a_customer')]
    public function deleteUser(ManagerRegistry $doctrine , int $idCustomer, int $idUser, UserInterface $authenticatedUser): JsonResponse
    {
        if ($authenticatedUser->getId() !== $idCustomer) {
            $errorMessage = 'You do not have access to this customer.';

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_FORBIDDEN);
        }

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


    #[Route('/api/customer/{idCustomer}/create', name: 'add_a_customer', methods:['POST'])]
    public function addUser(ManagerRegistry $doctrine , int $idCustomer, Request $request,  UserInterface $authenticatedUser): JsonResponse
    {
        if ($authenticatedUser->getId() !== $idCustomer) {
            $errorMessage = 'You do not have access to this customer.';

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_FORBIDDEN);
        }

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
