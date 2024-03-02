<?php

namespace App\DataFixtures;

use App\Entity\Agency;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AgencyFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $agency1 = (new Agency())
      ->setSiren('830256558')
      ->setName('AVANSEO');
    $manager->persist($agency1);
    $this->addReference('agency1', $agency1);

    $agency2 = (new Agency())
      ->setSiren('552038200')
      ->setName('Elogie-Siemp');
    $manager->persist($agency2);
    $this->addReference('agency2', $agency2);

    $manager->flush();
  }
}
