<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Agency;
use App\DataFixtures\AgencyFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $faker = \Faker\Factory::create('fr_FR');
    $agencies = $manager->getRepository(Agency::class)->findAll();

    for ($i = 0; $i < 30; $i++) {
      $agency = (new Client())
        ->setSiren($faker->randomNumber(9, true))
        ->setName($faker->company())
        ->setEmail($faker->email())
        ->setLegalStatus($faker->randomElement(['SAS', 'SARL', 'SA', 'SNC', 'EI', 'EURL', 'SASU']))
        ->setSector($faker->randomElement(['Informatique', 'BTP', 'Industrie', 'Agriculture', 'Commerce', 'Services']))
        ->setAddress($faker->address())
        ->setPhoneNumber($faker->phoneNumber())
        ->setAgency($faker->randomElement($agencies));

      $manager->persist($agency);
    }

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [
      AgencyFixtures::class,
    ];
  }
}

