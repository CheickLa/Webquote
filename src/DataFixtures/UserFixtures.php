<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\DataFixtures\AgencyFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher) {}

    public function load(ObjectManager $manager): void
    {
        $pwd = 'test';

        $user1 = (new User())
            ->setEmail('user@webquote.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setAgency($this->getReference('agency1'))
        ;
        $user1->setPassword($this->passwordHasher->hashPassword($user1, $pwd));
        $manager->persist($user1);
        $manager->flush();

        $user2 = (new User())
            ->setEmail('cheick@elogie-siemp.paris')
            ->setRoles(['ROLE_USER'])
            ->setAgency($this->getReference('agency2'))
        ;
        $user2->setPassword($this->passwordHasher->hashPassword($user2, $pwd));
        $manager->persist($user2);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AgencyFixtures::class,
        ];
    }
}
