<?php

namespace App\DataFixtures;

use App\Entity\Invoice;
use App\Entity\Quote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $faker = \Faker\Factory::create('fr_FR');

    $quotes = $manager->getRepository(Quote::class)->findAll();

    // Create an invoice for only some quotes
    foreach ($quotes as $quote) {
      if ($faker->boolean(40)) {
        $invoice = new Invoice();
        $invoice->setQuote($quote);
        $invoice->setDate($faker->dateTimeBetween('now', '+6 months'));
        $invoice->setPaid($faker->boolean(50));
        $manager->persist($invoice);
      }
    }

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [
      QuoteFixtures::class,
    ];
  }
}
