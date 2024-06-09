<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Products;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/products')]
class ProductsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'products_all', methods: ['GET'])]
    public function getProducts(ProductsRepository $productsRepository): JsonResponse
    {
        $products = $productsRepository->findAll();
        $data = $this->serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'product_get', methods: ['GET'])]
    public function getProduct(Orders $product): JsonResponse
    {
        $data = $this->serializer->serialize($product, 'json', ['groups' => 'product:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/create', name: 'product_create', methods: ['POST'])]
    public function createProduct(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['price'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $product = new Products();
        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($product, 'json', ['groups' => 'product:read']);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}/update', name: 'product_update', methods: ['PUT'])]
    public function updateProduct(Request $request, Products $product): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['price'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $product->setName($data['name']);
        $product->setPrice($data['price']);
        $product->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        $data = $this->serializer->serialize($product, 'json', ['groups' => 'product:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function deleteProduct(Products $product): JsonResponse
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
