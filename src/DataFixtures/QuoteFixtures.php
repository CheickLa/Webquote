<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Quote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class QuoteFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $faker = \Faker\Factory::create('fr_FR');

    $clients = $manager->getRepository(Client::class)->findAll();

    for ($i = 0; $i < 100; $i++) {
      $quote = new Quote();

      $client = $faker->randomElement($clients);
      $services = $client->getAgency()->getServices();

      $quote->setDate($faker->dateTimeBetween('-6 months'));
      $quote->setAmount($faker->randomFloat(2, 100, 1000));
      $quote->setClient($client);
      $quote->setService($faker->randomElement($services));
      $manager->persist($quote);
    }

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [
      ClientFixtures::class,
      ServiceFixtures::class,
    ];
  }
}
