<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Repository\OrdersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[Route('/api/orders')]
class OrdersController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'orders_all', methods: ['GET'])]
    public function getOrders(OrdersRepository $ordersRepository): JsonResponse
    {
        $orders = $ordersRepository->findAll();
        $data = $this->serializer->serialize($orders, 'json', ['groups' => 'order:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'orders_get', methods: ['GET'])]
    public function getOrder(Orders $order): JsonResponse
    {
        $data = $this->serializer->serialize($order, 'json', ['groups' => 'order:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}/products', name: 'orders_get', methods: ['GET'])]
    public function getOrderProducts(Orders $order): JsonResponse
    {
        $products = $order->getProducts();
        $data = $this->serializer->serialize($products, 'json', ['groups' => 'product:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/create', name: 'orders_create', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['amount'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $order = new Orders();
        $order->setAmount($data['amount']);
        $order->setCreatedAt(new \DateTimeImmutable());

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $data = $this->serializer->serialize($order, 'json', ['groups' => 'order:read']);

        return new JsonResponse($data, Response::HTTP_CREATED, [], true);
    }

    #[Route('/{id}/update', name: 'orders_update', methods: ['PUT'])]
    public function updateOrder(Request $request, Orders $order): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['amount'])) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $order->setAmount($data['amount']);
        $order->setUpdatedAt(new \DateTimeImmutable());

        $this->entityManager->flush();

        $data = $this->serializer->serialize($order, 'json', ['groups' => 'order:read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'orders_delete', methods: ['DELETE'])]
    public function deleteOrder(Orders $order): JsonResponse
    {
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
