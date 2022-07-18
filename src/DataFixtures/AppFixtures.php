<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
// src/DataFixtures/AppFixtures.php
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    // ...
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('lhdev@lhdev.com')
            ->setfName('Dylan')
            ->setlName('LH')
            ->setAddress('Route de st-Etienne')
            ->setCity('Montrond')
            ->setzipCode(42210)
            ->setRoles(['ROLE_ADMIN']);

        $password = $this->hasher->hashPassword($user, '1234');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
