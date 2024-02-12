<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AgencyFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $faker = \Faker\Factory::create('fr_FR');

    $agency = (new Agency())
      ->setSiren('830256558')
      ->setName('AVANSEO');
    $manager->persist($agency);
    $this->addReference('agency', $agency);

    for ($i = 0; $i < 10; $i++) {
      $agency = (new Agency())
        ->setSiren(strval($faker->randomNumber(9, true)))
        ->setName($faker->company());
      $manager->persist($agency);
    }

    $manager->flush();
  }
}
