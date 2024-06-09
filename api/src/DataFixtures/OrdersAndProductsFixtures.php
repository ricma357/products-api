<?php

namespace App\DataFixtures;

use App\Entity\Orders;
use App\Entity\Products;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrdersAndProductsFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $user = new User();

//        Replace these values
//        As this is default created user
        $user->setUsername("test");
        $user->setPassword("test321!");
        $user->setRoles(["ROLE_USER"]);

        $manager->persist($user);

        $faker = Factory::create();

        $products = [];
        for ($i = 0; $i < 20; $i++) {

            $product = new Products();

            $product->setName($faker->word);
            $product->setPrice($faker->randomFloat(2, 10, 1000));
            $product->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()));

            $products[] = $product;
            $manager->persist($product);
        }

        for ($i = 0; $i < 5; $i++) {

            $order = new Orders();
            $order_amount = 0;

            $order->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeThisYear()));
            $order->setAmount($order_amount)
            ;
            // each order gets a random number of products (1 to 5 products)
            $numberOfProducts = $faker->numberBetween(1, 5);
            $randomProducts = $faker->randomElements($products, $numberOfProducts);

            foreach ($randomProducts as $product) {
                $order->addProduct($product);
                $order->setAmount($order_amount + $product->getPrice());
            }

            $manager->persist($order);
        }

        $manager->flush();
    }
}
