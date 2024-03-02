<?php

namespace App\DataFixtures;

use App\Entity\ServiceCategory;
use App\Entity\Agency;
use App\DataFixtures\AgencyFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ServiceCategoryFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $faker = \Faker\Factory::create('fr_FR');

    $agencies = $manager->getRepository(Agency::class)->findAll();

    $categories = array(
      "Conception de site web",
      "Développement back-end",
      "Optimisation SEO",
      "Sécurité web",
      "Maintenance et support",
      "Développement mobile",
      "Design graphique",
      "Analyse de données",
      "Formation en ligne",
      "Consultation en stratégie numérique"
    );

    for ($i = 0; $i < $faker->numberBetween(30, 50); $i++) {
      $agency = (new ServiceCategory())
        ->setName($categories[array_rand($categories)])
        ->setAgency($agencies[array_rand($agencies)]);
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
