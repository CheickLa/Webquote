<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\DataFixtures\AgencyFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        $pwd = 'test';

        $user = (new User())
            ->setEmail('user@webquote.fr')
            ->setPassword($pwd)
            ->setRoles(['ROLE_USER'])
            ->setAgency($this->getReference('agency'))
        ;
        $manager->persist($user);
        $this->addReference('user', $user);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AgencyFixtures::class,
        ];
    }
}