<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Users;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class UsersFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;
    private SluggerInterface $slugger;
    public function __construct(
        UserPasswordHasherInterface $passwordEncoder,
        SluggerInterface $slugger)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }


    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail('luigi@vampa.com');
        $admin->setLastname('Vampa');
        $admin->setFirstname('Luigi');
        $admin->setAddress('11 rue Notre Dame');
        $admin->setZipcode('06400');
        $admin->setCity('Cannes');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'pass1234')
        );
        $admin->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

        for($usr = 1; $usr <= 5; $usr++)
        {
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setAddress($faker->streetAddress);
            $user->setZipcode(str_replace(' ','', $faker->postcode));
            $user->setCity($faker->city);
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user, 'pass1234')
            );
            $manager->persist($user);
        }


        $manager->flush();
    }
}
