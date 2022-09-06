<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFullName($faker->name);
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setRoles(['ROLE_USER']);
            $user->setRoleName('client');
            $manager->persist($user);
        }

        $manager->flush();
    }
}
