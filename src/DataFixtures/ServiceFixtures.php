<?php

namespace App\DataFixtures;

use App\Entity\Service;
use App\Entity\ServiceCategory;
use App\DataFixtures\ServiceCategoryFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $faker = \Faker\Factory::create('fr_FR');

    $serviceCategories = $manager->getRepository(ServiceCategory::class)->findAll();

    for ($i = 0; $i < $faker->numberBetween(100, 200); $i++) {
      $agency = (new Service())
        ->setName($faker->sentence(3, true))
        ->setPrice($faker->randomFloat(2, 1, 1000))
        ->setServiceCategory($faker->randomElement($serviceCategories));
      $manager->persist($agency);
    }

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [
      ServiceCategoryFixtures::class,
    ];
  }
}
