<?php

namespace App\Tests\Controller;

use App\Entity\Orders;
use App\Entity\Products;
use App\Controller\OrdersController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class OrdersControllerTest extends WebTestCase
{
    public function testGetOrderProducts()
    {
        $order = $this->createMock(Orders::class);

        $product1 = new Products();
        $product1->setName('Product 1');
        $product1->setPrice(100);

        $product2 = new Products();
        $product2->setName('Product 2');
        $product2->setPrice(200);

        // Use an ArrayCollection to mock the return value
        $products = new ArrayCollection([$product1, $product2]);

        $order->method('getProducts')->willReturn($products);

        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('serialize')
            ->with($products, 'json', ['groups' => 'product:read'])
            ->willReturn(json_encode([$product1, $product2]));

        $entityManager = $this->createMock(EntityManagerInterface::class);

        $controller = new OrdersController($entityManager, $serializer);
        $response = $controller->getOrderProducts($order);

        // Assert the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([$product1, $product2]),
            $response->getContent()
        );
    }
}
