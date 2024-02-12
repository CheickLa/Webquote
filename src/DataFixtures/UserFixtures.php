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

        $user = (new User())
            ->setEmail('user@webquote.fr')
            ->setRoles(['ROLE_USER'])
            ->setAgency($this->getReference('agency'))
        ;
        $user->setPassword($this->passwordHasher->hashPassword($user, $pwd));
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
