<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    /**
     * Liste les produits

     * @OA\Response(
     *     response=200,
     *     description="Retourne la liste des produits",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Response(
     *      response=401,
     *      description="JWT Token not found",
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Le page actuel d'affichage des produits",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Parameter(
     *      name="limit",
     *      in="query",
     *      description="Le nombre d'affichage des produits",
     *      @OA\Schema(type="integer")
     *  )
     *
     * @OA\Tag(name="products")
     */
    #[Route('/api/product', name: 'app_product', methods: 'GET')]
    public function index(ManagerRegistry $doctrine, Request $request, PaginatorInterface $paginator): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 2);

        $products = $doctrine
            ->getRepository(Product::class)
            ->findAll();

        $pagination = $paginator->paginate(
            $products, // Sur quoi on pagine
            $page,          // Le numero de la page actuelle
            $limit          // limite des produits à afficher
        );

        $data = [];

        foreach ($pagination->getItems() as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),

            ];
        }

        return $this->json($data);
    }

    /**
     * Retourne un produit spécifique
     * @OA\Response(
     *     response=200,
     *     description="Retourne le produit",
     *     @OA\JsonContent(
     *        @Model(type=Product::class)
     *     )
     * )
     * @OA\Response(
     *      response=401,
     *      description="JWT Token not found",
     * )
     *
     * @OA\Response(
     *      response=404,
     *      description="Cette ressource n'existe pas.",
     * )
     *
     * @OA\Tag(name="product")
     */
    #[Route('/api/product/{id}', name: 'product_show', methods:['get'] )]
    public function show(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $product = $doctrine->getRepository(product::class)->find($id);

        if (!$product) {

            return $this->json('Cette ressource n\'existe pas');
        }

        $data =  [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
        ];

        return $this->json($data);
    }


}

