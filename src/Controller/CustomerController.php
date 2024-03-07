<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CustomerController extends AbstractController
{

    /**
     * Liste les users pour un customer spécifique
     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des users pour un customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * @OA\Response(
     *      response=401,
     *      description="JWT Token not found",
     * )
     * @OA\Response(
     *      response=403,
     *      description="Vous n'avez pas accès a ce customer.",
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Le page actuel d'affichage des users",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *      name="limit",
     *      in="query",
     *      description="Le nombre d'affichage des users",
     *      @OA\Schema(type="integer")
     *  )
     *
     * @OA\Tag(name="users")
     */
    #[Route('/api/customer/{id}', name: 'get_customers', methods:['get'])]
    public function index(ManagerRegistry $doctrine , int $id, UserInterface $authenticatedUser, Request $request, PaginatorInterface $paginator): JsonResponse
    {

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 2);


        if ($authenticatedUser->getId() !== $id) {
            $errorMessage = 'Vous n\'avez pas accès a ce customer.';

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_FORBIDDEN);
        }




        $users = $doctrine
            ->getRepository(User::class)
            ->findUsersByCustomer($id);



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

    /**
     * Retourne un user spécifique à partir d'un customer
     * @OA\Response(
     *     response=200,
     *     description="Retourne le user",
     *     @OA\JsonContent(
     *        @Model(type=User::class)
     *     )
     * )
     * @OA\Response(
     *      response=401,
     *      description="JWT Token not found",
     * )
     * @OA\Response(
     *       response=403,
     *       description="Vous n'avez pas accès a ce customer.",
     *  )
     * @OA\Response(
     *       response=404,
     *       description="Cette ressource n'existe pas.",
     *  )
     *
     * @OA\Tag(name="user")
     */
    #[Route('/api/customer/{idCustomer}/user/{idUser}', name: 'get_a_customer', methods:['get'])]
    public function getAUserfromCustomerId(ManagerRegistry $doctrine , int $idCustomer, int $idUser, UserInterface $authenticatedUser, UrlGeneratorInterface $urlGenerator): JsonResponse
    {

        if ($authenticatedUser->getId() !== $idCustomer) {
            $errorMessage = 'Vous n\'avez pas accès a ce customer.';

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_FORBIDDEN);
        }


        $user = $doctrine
            ->getRepository(User::class)
            ->findOneBy([
                'id' => $idUser,
                'customer' => $idCustomer,
            ]);

        if($user == null){
            $errorMessage = 'Cette ressource n\'existe pas.';
            return new JsonResponse(['error' => $errorMessage], Response::HTTP_NOT_FOUND);
        }

        $data =  [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'firstname' => $user->getEmail(),
            'links' => [
                'self' => [
                    'href' => $urlGenerator->generate('get_a_customer', ['idCustomer' => $idCustomer, 'idUser' => $idUser], UrlGeneratorInterface::ABSOLUTE_URL),
                    'type' => 'GET',
                ],
                'all' => [
                    'href' => $urlGenerator->generate('get_customers', ['id' => $idCustomer], UrlGeneratorInterface::ABSOLUTE_URL),
                    'type' => 'GET',
                ],
                'delete' => [
                    'href' => $urlGenerator->generate('delete_user', ['idCustomer' => $idCustomer, 'idUser' => $idUser], UrlGeneratorInterface::ABSOLUTE_URL),
                    'type' => 'DELETE',
                ],
                'add' => [
                    'href' => $urlGenerator->generate('add_user', ['idCustomer' => $idCustomer], UrlGeneratorInterface::ABSOLUTE_URL),
                    'type' => 'POST',
                ],
            ],
        ];

        return $this->json($data);

    }


    /**
     * Supprimer un user spécifique à partir d'un customer
     * @OA\Response(
     *     response=204,
     *     description="Supprimer le user",
     *     content: null
     * )
     * @OA\Response(
     *      response=401,
     *      description="JWT Token not found",
     * )
     * @OA\Response(
     *       response=403,
     *       description="Vous n'avez pas accès a ce customer.",
     *  )
     * @OA\Response(
     *        response=404,
     *        description="Cette ressource n'existe pas.",
     *   )
     * @OA\Tag(name="deleteUser")
     */
    #[Route('/api/customer/{idCustomer}/user/{idUser}', name: 'delete_user', methods:['DELETE'])]
    public function deleteUser(ManagerRegistry $doctrine , int $idCustomer, int $idUser, UserInterface $authenticatedUser): JsonResponse
    {
        if ($authenticatedUser->getId() !== $idCustomer) {
            $errorMessage = 'Vous n\'avez pas accès a ce customer.';

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_FORBIDDEN);
        }

        $entityManager = $doctrine->getManager();
        $user = $entityManager->getRepository(User::class)
            ->findOneBy([
                'id' => $idUser,
                'customer' => $idCustomer,
            ]);

        if($user == null){
            $errorMessage = 'Cette ressource n\'existe pas.';
            return new JsonResponse(['error' => $errorMessage], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($user);

        $entityManager->flush();


        return $this->json(['message' => 'Suppression exécutée avec succès'], 204);
    }


    /**
     * Creer un user à partir d'un customer spécifique
     * @OA\RequestBody (
     *      description="Ajout d'un user",
     *      @OA\JsonContent(
     *         @Model(type=User::class)
     *      )
     *  )
     * @OA\Response(
     *     response=201,
     *     description="Ajout du user avec succès"
     * )
     * @OA\Response(
     *      response=401,
     *      description="JWT Token not found",
     * )
     * @OA\Response(
     *       response=403,
     *       description="Vous n'avez pas accès a ce customer.",
     *  )
     * @OA\Tag(name="createUser")
     */
    #[Route('/api/customer/{idCustomer}', name: 'add_user', methods:['POST'])]
    public function addUser(ManagerRegistry $doctrine , int $idCustomer, Request $request,  UserInterface $authenticatedUser): JsonResponse
    {
        if ($authenticatedUser->getId() !== $idCustomer) {
            $errorMessage = 'Vous n\'avez pas accès a ce customer.';

            return new JsonResponse(['error' => $errorMessage], Response::HTTP_FORBIDDEN);
        }

        $entityManager = $doctrine->getManager();
        
        $user = new User();
        $content = $request->toArray();
        //dd($content);
        $user->setName($content['name']);
        $user->setEmail($content['email']);
        

        $customer = $entityManager->getRepository(Customer::class)
        ->findOneBy([
            'id' => $idCustomer
        ]);

        $user->setCustomer($customer);

        $entityManager->persist($user);
        $entityManager->flush();

        // Return a response, you can customize this based on your needs
        return $this->json(['message' => 'Ajout du user avec succès'], 201);
    }
}
