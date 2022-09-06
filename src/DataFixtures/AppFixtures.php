<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));
        for ($i = 0; $i < 50; $i++) {
            $product = new Product();
            $product->setNom($faker->sentence(3));
            $product->setDesignation($faker->sentence());
            $product->setPrix(mt_rand(100, 1000));
            $product->setAvis($faker->paragraph());
            $product->setCodeart($faker->sentence(1));
            $product->setQte(mt_rand(1, 100));
            $product->setActif(mt_rand(0, 1));
            $product->setImage($faker->imageUrl(200, 200, true));


            $manager->persist($product);
        }

        $manager->flush();
    }
}
